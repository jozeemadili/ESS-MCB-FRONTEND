@php use App\Http\Controllers\API\Auth\CustomersController as CC; @endphp
<div class="tk">
  @if($message = session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px">{!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endif
  @if($message = session('success'))
    <div class="alert alert-light border alert-dismissible fade show" role="alert" style="border-radius:12px">{!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endif
  <style>
    .br-tools{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;padding:14px 18px;border-bottom:1px solid var(--line);align-items:end;}
    .br-tools label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--mut);font-weight:700;margin-bottom:4px;display:block;}
    .br-tools .form-control,.br-tools .form-select{border:1px solid var(--line);border-radius:10px;font-size:13px;height:38px;}
    .br-tools .btn{height:38px;border-radius:10px;font-weight:600;font-size:13px;}
    .br-branches{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px;margin-bottom:14px;}
    .br-branch{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;cursor:pointer;text-align:left;width:100%;}
    .br-branch.on{box-shadow:0 0 0 2px var(--b) inset;}
    .br-branch b{font-size:14.5px;display:block;} .br-branch small{color:var(--mut);}
    .br-bar{height:6px;border-radius:6px;background:#eef1f6;margin-top:10px;overflow:hidden;} .br-bar i{display:block;height:100%;background:#137a43;}
    .br-code{display:inline-block;background:#f1f5f9;color:#334155;border-radius:6px;padding:1px 7px;font-size:11px;font-weight:700;font-family:SFMono-Regular,Menlo,Consolas,monospace;}
    .br-actions{display:flex;justify-content:flex-end;gap:6px;}
    .br-actions .btn{border-radius:8px;font-weight:600;font-size:12px;padding:4px 10px;white-space:nowrap;}
    .br-pick{max-height:320px;overflow:auto;border:1px solid var(--line);border-radius:10px;padding:8px 10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:4px 10px;}
    .br-pick label{font-size:12.5px;display:flex;gap:6px;align-items:center;margin:0;padding:3px 0;}
    .br-pick label.done{color:var(--mut);}
    .tk-pager nav{margin:0;}
    .br-issues{margin:14px 18px 4px;background:#fff8e6;border:1px solid #f6d7a7;color:#7c4a03;border-radius:12px;padding:12px 16px;font-size:13px;}
    .br-issue{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:8px 0;border-top:1px solid #f3e2bf;}
    .br-issue:first-of-type{margin-top:8px;}
    .br-issue .btn{border-radius:8px;font-weight:600;white-space:nowrap;}
    .br-edit{display:flex;gap:6px;flex-wrap:wrap;align-items:center;} .br-s2-wrap{min-width:280px;display:inline-block;}
    .tk .select2-container--default .select2-selection--single,.tk .select2-container--default .select2-selection--multiple{border:1px solid var(--line);border-radius:10px;min-height:38px;}
    .tk .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:36px;}
    .tk .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px;}
    .tk .select2-container--default .select2-selection--multiple .select2-selection__choice{background:#e8effb;border:0;color:#0d47a1;border-radius:6px;font-size:12.5px;}
    .select2-container--open .select2-dropdown{z-index:2000;}
    .tk [wire\:loading].tk-spin{display:none;}
    .select2-results__option[aria-disabled=true]{color:#9ca3af;}
    @media (max-width:767px){.br-tools{grid-template-columns:1fr;}}
  </style>

  <div class="tk-kpis">
    <div class="tk-kpi"><span>Branches</span><b>{{ $counts['branches'] }}</b></div>
    <div class="tk-kpi" style="--a:#137a43"><span>Districts served</span><b>{{ $counts['districts'] }}</b></div>
    <div class="tk-kpi" style="--a:#0d47a1"><span>Active mappings</span><b>{{ $counts['active'] }}</b></div>
    <div class="tk-kpi" style="--a:#c62828"><span>Inactive mappings</span><b>{{ $counts['inactive'] }}</b></div>
    <div class="tk-kpi" style="--a:#f28c28"><span>Districts not served</span><b>{{ $counts['unmapped'] }}</b></div>
  </div>

  {{-- One card per branch: click to filter --}}
  <div class="br-branches">
    @foreach($branches as $b)
      <button type="button" class="br-branch {{ $branch === (string) $b->BRANCH_CODE ? 'on' : '' }}" wire:click="$set('branch', '{{ $branch === (string) $b->BRANCH_CODE ? '' : $b->BRANCH_CODE }}')">
        <b>{{ strtoupper($b->BRANCH_NAME) }}</b>
        <small><span class="br-code">{{ $b->BRANCH_CODE }}</span> · {{ (int) $b->active }} active of {{ $b->total }} districts</small>
        <div class="br-bar"><i style="width:{{ $b->total ? round($b->active / $b->total * 100) : 0 }}%"></i></div>
      </button>
    @endforeach
  </div>

  <div class="tk-card">
    <div class="br-tools">
      <div><label for="brSearch">Search</label><input id="brSearch" class="form-control" wire:model.debounce.400ms="search" placeholder="District, district code or branch"></div>
      <div><label for="brBranch">Branch</label>
        <select id="brBranch" class="form-select" wire:model="branch"><option value="">All branches</option>
          @foreach($branches as $b)<option value="{{ $b->BRANCH_CODE }}">{{ strtoupper($b->BRANCH_NAME) }} ({{ $b->BRANCH_CODE }})</option>@endforeach
        </select></div>
      <div><label for="brStatus">Status</label>
        <select id="brStatus" class="form-select" wire:model="status"><option value="">All</option><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div>
      <div class="d-flex gap-2">
        @if($search !== '' || $branch !== '' || $status !== '')<button class="btn btn-light" type="button" wire:click="clearFilters">Clear</button>@endif
        @if($canManage)
          <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#brAdd"><i class="fa fa-plus"></i> Add districts</button>
        @endif
      </div>
    </div>

    @if($issues['duplicates'] || $issues['unknown'])
      <div class="br-issues">
        <b><i class="fa fa-exclamation-triangle"></i> Check these active mappings before publishing</b>
        @foreach($issues['duplicates'] as $d)
          <div class="br-issue">
            <span><b>{{ strtoupper($d->BRANCH_NAME) }}</b> {{ $d->BRANCH_CODE }} is mapped to <b>{{ CC::getDistrictName($d->DISTRICT_CODE) }}</b> ({{ $d->DISTRICT_CODE }}) more than once. ESS gets it once.</span>
            @if($canManage)
              <button class="btn btn-sm btn-outline-danger" type="button" wire:click="deleteDuplicate({{ $d->ID }})" wire:loading.attr="disabled"
                onclick="confirm('Delete the extra {{ addslashes(strtoupper($d->BRANCH_NAME)) }} – {{ addslashes(CC::getDistrictName($d->DISTRICT_CODE)) }} mapping? The first one stays.') || event.stopImmediatePropagation()"><i class="fa fa-trash"></i> Delete duplicate</button>
            @endif
          </div>
        @endforeach
        @foreach($issues['unknown'] as $d)
          <div class="br-issue">
            <span><b>{{ strtoupper($d->BRANCH_NAME) }}</b> {{ $d->BRANCH_CODE }} uses district code <b>{{ $d->DISTRICT_CODE ?: '(blank)' }}</b>, which is not in the ESS district list.</span>
            @if($canManage)
              @if($editId === (int) $d->ID)
                <span class="br-edit">
                  <span class="br-s2-wrap" wire:ignore wire:key="edit-{{ $editId }}">
                    <select class="br-s2" data-field="editDistrict" data-placeholder="Search district name or code">
                      <option value=""></option>
                      @foreach($districts as $code => $name)<option value="{{ $code }}">{{ $name }} ({{ $code }})</option>@endforeach
                    </select>
                  </span>
                  <button class="btn btn-sm btn-primary" type="button" wire:click="saveDistrict" wire:loading.attr="disabled">Save</button>
                  <button class="btn btn-sm btn-light" type="button" wire:click="cancelEdit">Cancel</button>
                  @error('editDistrict')<small class="text-danger d-block w-100">{{ $message }}</small>@enderror
                </span>
              @else
                <button class="btn btn-sm btn-outline-primary" type="button" wire:click="startEdit({{ $d->ID }})"><i class="fa fa-pencil"></i> Edit district</button>
              @endif
            @endif
          </div>
        @endforeach
      </div>
    @endif

    <div class="tk-bar">
      <div><b>District mappings</b><span class="tk-count">{{ $rows->total() }} {{ ($search !== '' || $branch !== '' || $status !== '') ? 'matching' : 'in total' }}</span>
        <span class="tk-count" wire:loading><span class="spinner-border spinner-border-sm"></span> Loading…</span></div>
      @if($canManage)
        <button class="btn btn-success tk-btn" type="button" data-bs-toggle="modal" data-bs-target="#brPublish"><i class="fa fa-cloud-upload"></i> Publish to ESS</button>
      @endif
    </div>
    <div class="tk-scroll">
      <table class="tk-table">
        <thead><tr><th>#</th><th>Branch</th><th>ESS district</th><th>Status</th>@if($canManage)<th style="text-align:right">Action</th>@endif</tr></thead>
        <tbody>
        @forelse($rows as $r)
          @php $active = $r->STATUS === 'Active'; @endphp
          <tr wire:key="br-{{ $r->ID }}">
            <td class="tk-sub" style="margin:0">{{ $rows->firstItem() + $loop->index }}</td>
            <td><b>{{ strtoupper($r->BRANCH_NAME) }}</b> <span class="br-code">{{ $r->BRANCH_CODE }}</span></td>
            <td>{{ CC::getDistrictName($r->DISTRICT_CODE) }} <span class="tk-sub">{{ $r->DISTRICT_CODE ?: 'No district' }}</span></td>
            <td><span class="tk-pill {{ $active ? 'ok' : 'bad' }}">{{ $r->STATUS ?: 'Unknown' }}</span>
              @if(isset($issues['duplicates'][$r->ID]))<span class="tk-pill wait" title="Same branch and district is already active">Duplicate</span>@endif
              @if(isset($issues['unknown'][$r->ID]))<span class="tk-pill wait" title="District code not in the ESS district list">Unknown district</span>@endif</td>
            @if($canManage)
            <td><div class="br-actions">
              @if(isset($issues['duplicates'][$r->ID]))
                <button class="btn btn-outline-danger" type="button" wire:click="deleteDuplicate({{ $r->ID }})" wire:loading.attr="disabled" onclick="confirm('Delete this extra mapping? The first one stays.') || event.stopImmediatePropagation()"><i class="fa fa-trash"></i> Delete</button>
              @endif
              @if(isset($issues['unknown'][$r->ID]))
                <button class="btn btn-outline-primary" type="button" wire:click="startEdit({{ $r->ID }})"><i class="fa fa-pencil"></i> Edit district</button>
              @endif
              @if($active)
                <button class="btn btn-outline-danger" type="button" wire:click="setStatus({{ $r->ID }}, 'Inactive')" wire:loading.attr="disabled" onclick="confirm('Stop {{ addslashes(strtoupper($r->BRANCH_NAME)) }} from serving {{ addslashes(CC::getDistrictName($r->DISTRICT_CODE)) }}?') || event.stopImmediatePropagation()"><i class="fa fa-ban"></i> Deactivate</button>
              @else
                <button class="btn btn-outline-success" type="button" wire:click="setStatus({{ $r->ID }}, 'Active')" wire:loading.attr="disabled"><i class="fa fa-check"></i> Activate</button>
              @endif
            </div></td>
            @endif
          </tr>
        @empty
          <tr><td colspan="5" class="tk-empty"><i class="fa fa-map-o fa-2x d-block mb-2"></i>No district mappings match your search.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="tk-pager">
      <span>{{ $rows->total() ? 'Showing '.$rows->firstItem().'–'.$rows->lastItem().' of '.$rows->total() : 'No records' }}</span>
      {{ $rows->links() }}
    </div>
  </div>

  @if($canManage)
  {{-- Add districts to a branch --}}
  <div class="modal fade" id="brAdd" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <form wire:submit.prevent="addMapping">
        <div class="modal-header"><h5 class="modal-title">Add districts to a branch</h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body">
          <label class="form-label" for="brNewBranch">Branch</label>
          <select id="brNewBranch" class="form-select mb-1" wire:model="newBranch">
            <option value="">Choose branch</option>
            @foreach($branches as $b)<option value="{{ $b->BRANCH_CODE }}">{{ strtoupper($b->BRANCH_NAME) }} ({{ $b->BRANCH_CODE }})</option>@endforeach
          </select>
          @error('newBranch')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
          <label class="form-label mt-3">ESS districts this branch serves <small class="text-muted">(type to search; districts it already serves are greyed out)</small></label>
          <div wire:ignore wire:key="pick-{{ $newBranch === '' ? 'none' : $newBranch }}-{{ $pickVersion }}">
            <select class="br-s2" multiple data-field="newDistricts" data-placeholder="{{ $newBranch === '' ? 'Choose the branch first' : 'Search district name or code' }}" @disabled($newBranch === '')>
              @foreach($districts as $code => $dname)
                <option value="{{ $code }}" @disabled(in_array($code, $mappedForNew))>{{ $dname }} ({{ $code }}){{ in_array($code, $mappedForNew) ? ' – already served' : '' }}</option>
              @endforeach
            </select>
          </div>
          @if($newBranch !== '')<div class="tk-sub mt-1">{{ count($mappedForNew) }} of {{ count($districts) }} districts already served by this branch.</div>@endif
          @error('newDistricts')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          <p class="tk-sub mt-2">New mappings are saved as Active. Use <b>Publish to ESS</b> afterwards so employees see the change.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="addMapping">
            <span wire:loading wire:target="addMapping" class="tk-spin"></span> Save districts</button>
        </div>
      </form>
    </div></div>
  </div>

  {{-- Publish --}}
  <div class="modal fade" id="brPublish" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Publish branches to ESS</h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body">
        <p>Send the active branch and district list ({{ $counts['active'] }} mappings) to ESS Utumishi?</p>
        <p class="tk-sub">Employees choose their nearest MCB branch from this list when they apply.</p>
        @if($issues['duplicates'] || $issues['unknown'])
          <div class="alert alert-warning py-2 mb-0" style="font-size:12.5px">
            @if($issues['duplicates']){{ count($issues['duplicates']) }} duplicate {{ \Illuminate\Support\Str::plural('mapping', count($issues['duplicates'])) }} will be sent once. @endif
            @if($issues['unknown']){{ count($issues['unknown']) }} {{ \Illuminate\Support\Str::plural('mapping', count($issues['unknown'])) }} with a district code ESS may not know will be sent as is. @endif
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success" type="button" wire:click="publishBranch" wire:loading.attr="disabled" wire:target="publishBranch">
          <span wire:loading wire:target="publishBranch" class="tk-spin"></span> Publish</button>
      </div>
    </div></div>
  </div>

  <script>
    window.addEventListener('branch-mapping-saved', function () {
      var m = document.getElementById('brAdd');
      try { bootstrap.Modal.getOrCreateInstance(m).hide(); } catch (e) {}
    });
  </script>
  @endif
</div>
