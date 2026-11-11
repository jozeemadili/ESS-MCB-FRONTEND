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

    // Fix an unknown district code
    public $editId = null;
    public $editDistrict = '';

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
        if ($row && $status == 'Active' && BRANCH::where('BRANCH_CODE', $row->BRANCH_CODE)->where('DISTRICT_CODE', $row->DISTRICT_CODE)
                ->where('STATUS', 'Active')->where('ID', '<>', $row->ID)->exists()) {
            session()->flash('error', '<b>'.e(strtoupper($row->BRANCH_NAME)).'</b> already serves '.e(CustomersController::getDistrictName($row->DISTRICT_CODE)).'; this copy stays inactive.');
            return;
        }
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

    /**
     * Sends the active mappings to ESS (FSP_BRANCHES) through the gateway. The gateway replies statusCode 200 only when
     * ESS answers 8000; otherwise statusDescription says why (ESS code, no reply, nothing to send).
     */
    public function publishBranch()
    {
        abort_unless($this->canManage(), 403);
        if (!BRANCH::where('STATUS', 'Active')->exists()) {
            return to_route('branches-list')->with('error', '<b>Not published</b>. There are no active mappings to send to ESS.');
        }
        try {
            $response = EsbClient::SendesbRequesturl('branches/');
            $ok = (int) ($response->statusCode ?? 0) === 200;
            $desc = $response->statusDescription ?? 'No response from the ESS gateway';
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $body = $e->hasResponse() ? json_decode((string) $e->getResponse()->getBody()) : null;
            $ok = false;
            $desc = $body->statusDescription ?? 'The ESS gateway could not be reached';
            \Log::warning('Branch publish failed: '.$e->getMessage());
        } catch (\Throwable $e) {
            $ok = false;
            $desc = 'The ESS gateway could not be reached';
            \Log::warning('Branch publish failed: '.$e->getMessage());
        }
        return $ok
            ? to_route('branches-list')->with('success', '<b>Published to ESS</b>. '.e($desc))
            : to_route('branches-list')->with('error', '<b>Publishing to ESS failed</b>. '.e($desc));
    }

    /** Deletes an extra copy of an active branch/district mapping (the first copy stays). */
    public function deleteDuplicate($id)
    {
        abort_unless($this->canManage(), 403);
        $dup = $this->issues(CustomersController::districts())['duplicates'][$id] ?? null;
        if (!$dup) {
            session()->flash('error', 'That mapping is no longer a duplicate, so it was not deleted.');
            return;
        }
        BRANCH::where('ID', $dup->ID)->delete();
        \Log::info('Branch mapping '.$dup->ID.' deleted as duplicate of '.$dup->BRANCH_CODE.'/'.$dup->DISTRICT_CODE.' by user '.auth()->id());
        session()->flash('success', 'Duplicate removed: <b>'.e(strtoupper($dup->BRANCH_NAME)).'</b> – '.e(CustomersController::getDistrictName($dup->DISTRICT_CODE))
            .' is now mapped once. Publish to ESS to send the change.');
    }

    public function startEdit($id)
    {
        abort_unless($this->canManage(), 403);
        $this->editId = (int) $id;
        $this->editDistrict = '';
        $this->resetErrorBag('editDistrict');
    }

    public function cancelEdit()
    {
        $this->reset(['editId', 'editDistrict']);
        $this->resetErrorBag('editDistrict');
    }

    /** Replaces an unknown district code with one from the ESS district list. */
    public function saveDistrict()
    {
        abort_unless($this->canManage(), 403);
        $this->resetErrorBag('editDistrict');
        $districts = CustomersController::districts();
        $row = BRANCH::where('ID', $this->editId)->first();
        if (!$row) {
            $this->cancelEdit();
            return;
        }
        if (!isset($districts[$this->editDistrict])) {
            $this->addError('editDistrict', 'Choose a district from the list.');
            return;
        }
        if (BRANCH::where('BRANCH_CODE', $row->BRANCH_CODE)->where('DISTRICT_CODE', $this->editDistrict)->where('STATUS', 'Active')
                ->where('ID', '<>', $row->ID)->exists()) {
            $this->addError('editDistrict', strtoupper($row->BRANCH_NAME).' already serves '.$districts[$this->editDistrict].'. Delete or deactivate this row instead.');
            return;
        }
        $old = $row->DISTRICT_CODE;
        BRANCH::where('ID', $row->ID)->update(['DISTRICT_CODE' => $this->editDistrict]);
        \Log::info('Branch mapping '.$row->ID.' district changed '.$old.' -> '.$this->editDistrict.' by user '.auth()->id());
        session()->flash('success', '<b>'.e(strtoupper($row->BRANCH_NAME)).'</b>: district <b>'.e($old ?: '(blank)').'</b> changed to <b>'
            .e($districts[$this->editDistrict]).'</b> ('.e($this->editDistrict).'). Publish to ESS to send the change.');
        $this->cancelEdit();
    }

    /** Active rows the gateway will not send as they are: unknown district codes, and extra copies of a branch/district. */
    private function issues(array $districts): array
    {
        $active = BRANCH::where('STATUS', 'Active')->orderBy('ID')->get(['ID', 'BRANCH_CODE', 'BRANCH_NAME', 'DISTRICT_CODE']);
        $seen = [];
        $duplicates = [];
        $unknown = [];
        foreach ($active as $r) {
            $key = trim((string) $r->BRANCH_CODE).'|'.trim((string) $r->DISTRICT_CODE);
            if (isset($seen[$key])) {
                $duplicates[$r->ID] = $r;
            } else {
                $seen[$key] = true;
            }
            if (!isset($districts[trim((string) $r->DISTRICT_CODE)])) {
                $unknown[$r->ID] = $r;
            }
        }
        return ['duplicates' => $duplicates, 'unknown' => $unknown];
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

        $issues = $this->issues($districts);

        return view('livewire.branch-submit', [
            'issues'    => $issues,
            'rows'      => $rows,
            'branches'  => $branches,
            'districts' => $districts,
            'counts'    => $counts,
            'canManage' => $this->canManage(),
            'mappedForNew' => $mappedForNew,
        ]);
    }
}
