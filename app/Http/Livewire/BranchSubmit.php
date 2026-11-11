<?php

namespace App\Http\Livewire;

use App\Http\Controllers\API\Auth\CustomersController;
use App\Http\Controllers\API\Auth\PortalUsersController;
use App\Models\BRANCH;
use App\TIRAClient\Scripts\Classes\EsbClient;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Branches (Security & Settings > Branches): which MCB branch serves which ESS Utumishi district.
 * Each row of BRANCH maps one branch to one district; active mappings are published to ESS.
 */
class BranchSubmit extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['search' => ['except' => ''], 'branch' => ['except' => ''], 'status' => ['except' => '']];

    public $search = '';
    public $branch = '';
    public $status = '';

    // Add mapping form
    public $newBranch = '';
    public $newDistricts = [];

    public function updating($name)
    {
        if (in_array($name, ['search', 'branch', 'status'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'branch', 'status']);
        $this->resetPage();
    }

    private function canManage(): bool
    {
        return PortalUsersController::canManageUsers();
    }

    public function setStatus($id, $status)
    {
        abort_unless($this->canManage(), 403);
        if (!in_array($status, ['Active', 'Inactive'])) {
            return;
        }
        $row = BRANCH::where('ID', $id)->first();
        if ($row) {
            BRANCH::where('ID', $id)->update(['STATUS' => $status]);
            $district = CustomersController::getDistrictName($row->DISTRICT_CODE);
            session()->flash('success', '<b>'.e(strtoupper($row->BRANCH_NAME)).'</b> – '.e($district).' is now <b>'.($status == 'Active' ? 'active' : 'inactive').'</b>. Publish to ESS to send the change.');
        }
    }

    public function addMapping()
    {
        abort_unless($this->canManage(), 403);
        $this->validate(['newBranch' => 'required', 'newDistricts' => 'required|array|min:1'],
            ['newDistricts.required' => 'Choose at least one district.', 'newBranch.required' => 'Choose the branch.']);

        $branch = BRANCH::where('BRANCH_CODE', $this->newBranch)->first();
        if (!$branch) {
            $this->addError('newBranch', 'Unknown branch.');
            return;
        }
        $districts = CustomersController::districts();
        $added = [];
        $skipped = [];
        foreach (array_unique($this->newDistricts) as $code) {
            if (!isset($districts[$code])) {
                continue;
            }
            $exists = BRANCH::where('BRANCH_CODE', $branch->BRANCH_CODE)->where('DISTRICT_CODE', $code)->first();
            if ($exists) {
                if ($exists->STATUS != 'Active') {
                    BRANCH::where('ID', $exists->ID)->update(['STATUS' => 'Active']);
                    $added[] = $districts[$code].' (re-activated)';
                } else {
                    $skipped[] = $districts[$code];
                }
                continue;
            }
            BRANCH::create(['BRANCH_CODE' => $branch->BRANCH_CODE, 'BRANCH_NAME' => $branch->BRANCH_NAME, 'DISTRICT_CODE' => $code, 'STATUS' => 'Active']);
            $added[] = $districts[$code];
        }
        $this->reset(['newDistricts']);
        $this->dispatchBrowserEvent('branch-mapping-saved');
        session()->flash('success', '<b>'.e(strtoupper($branch->BRANCH_NAME)).'</b>: '
            .($added ? 'added '.e(implode(', ', $added)).'. ' : '')
            .($skipped ? 'Already mapped: '.e(implode(', ', $skipped)).'. ' : '')
            .($added ? 'Publish to ESS to send the change.' : ''));
    }

    public function publishBranch()
    {
        abort_unless($this->canManage(), 403);
        $response = EsbClient::SendesbRequesturl('branches/');
        $ok = isset($response->statusCode) && $response->statusCode === 200;
        $desc = $response->statusDescription ?? 'No response from the ESS gateway';
        return to_route('branches-list')->with('success', ($ok ? '<b>Published to ESS</b>. ' : '<b>Publishing to ESS failed</b>. ').e($desc));
    }

    public function render()
    {
        $districts = CustomersController::districts();
        $search = trim($this->search);
        $matchingDistricts = $search === '' ? [] : array_keys(array_filter($districts, fn ($n) => stripos($n, $search) !== false));

        $rows = BRANCH::query()
            ->when($this->branch !== '', fn ($q) => $q->where('BRANCH_CODE', $this->branch))
            ->when($this->status !== '', fn ($q) => $q->where('STATUS', $this->status))
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search, $matchingDistricts) {
                $w->where('BRANCH_NAME', 'like', "%$search%")->orWhere('BRANCH_CODE', $search)->orWhere('DISTRICT_CODE', $search);
                if ($matchingDistricts) {
                    $w->orWhereIn('DISTRICT_CODE', $matchingDistricts);
                }
            }))
            ->orderBy('BRANCH_CODE')->orderBy('STATUS')->orderBy('ID')
            ->paginate(20);

        $branches = BRANCH::selectRaw("BRANCH_CODE, MAX(BRANCH_NAME) AS BRANCH_NAME, SUM(STATUS = 'Active') AS active, COUNT(*) AS total")
            ->groupBy('BRANCH_CODE')->orderBy('BRANCH_CODE')->get();

        $counts = [
            'branches'  => $branches->count(),
            'districts' => BRANCH::where('STATUS', 'Active')->distinct()->count('DISTRICT_CODE'),
            'active'    => BRANCH::where('STATUS', 'Active')->count(),
            'inactive'  => BRANCH::where('STATUS', '<>', 'Active')->count(),
            'unmapped'  => count(array_diff(array_keys($districts), BRANCH::where('STATUS', 'Active')->pluck('DISTRICT_CODE')->all())),
        ];

        $mappedForNew = $this->newBranch === '' ? [] : BRANCH::where('BRANCH_CODE', $this->newBranch)->where('STATUS', 'Active')->pluck('DISTRICT_CODE')->all();

        return view('livewire.branch-submit', [
            'rows'      => $rows,
            'branches'  => $branches,
            'districts' => $districts,
            'counts'    => $counts,
            'canManage' => $this->canManage(),
            'mappedForNew' => $mappedForNew,
        ]);
    }
}
