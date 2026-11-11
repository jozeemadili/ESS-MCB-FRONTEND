@extends('layouts.admin.master')
@php
  use App\Http\Controllers\API\Auth\PortalUsersController as PUC;
  $branchNames = collect($distinctBranchCodes)->mapWithKeys(fn ($b) => [strtoupper($b->BRANCH_CODE) => strtoupper($b->BRANCH_NAME)]);
  $roleColor = ['ADMIN' => '#c62828', 'Credit Officer' => '#0d47a1', 'Credit Admin' => '#6d28d9', 'Credit Operation' => '#137a43', 'Branch Operations' => '#b45309', 'Reports' => '#475569'];
  $filtered = $q || $role || $status;
@endphp
@section('title', 'System Users')
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>System Users</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
      <li><button class="btn" style="background:#0d47a1;border-color:#0d47a1;color:#fff;font-weight:600;border-radius:10px" data-bs-toggle="modal" data-bs-target="#newModal"><i class="fa fa-user-plus"></i> New User</button></li>
    @endslot
    <li class="breadcrumb-item">Security</li>
    <li class="breadcrumb-item active">System Users</li>
  @endcomponent

@include('admin.loan_take_over._style')
<style>
  .us-person{display:flex;align-items:center;gap:10px;}
  .us-avatar{flex:0 0 36px;height:36px;border-radius:50%;color:#fff;font-weight:700;font-size:12.5px;display:flex;align-items:center;justify-content:center;}
  .us-role{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:color-mix(in srgb,var(--c) 12%,#fff);color:var(--c);}
  .us-you{font-size:10px;font-weight:700;background:#e8effb;color:#0d47a1;border-radius:10px;padding:1px 7px;margin-left:6px;}
  .us-actions{display:flex;gap:6px;justify-content:flex-end;flex-wrap:nowrap;}
  .us-nw{white-space:nowrap;}
  .us-role{white-space:nowrap;}
  .us-actions .btn{border-radius:8px;font-weight:600;font-size:11.5px;padding:4px 8px;white-space:nowrap;}
  .us-filter{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;padding:14px 18px;border-bottom:1px solid var(--line);align-items:end;}
  .us-filter label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--mut);font-weight:700;margin-bottom:4px;display:block;}
  .us-filter .form-control,.us-filter .form-select{border:1px solid var(--line);border-radius:10px;font-size:13px;height:38px;}
  .us-filter .btn{height:38px;border-radius:10px;font-weight:600;font-size:13px;}
  .us-rolepick{display:grid;gap:8px;}
  .us-rolepick label{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--line);border-radius:10px;padding:10px 12px;cursor:pointer;margin:0;}
  .us-rolepick label:hover{border-color:#0d47a1;}
  .us-rolepick input{margin-top:3px;}
  .us-rolepick b{display:block;font-size:13px;} .us-rolepick small{color:var(--mut);}
  .us-temp{background:#fff4e5;border:1px solid #f6d7a7;border-radius:12px;padding:12px 16px;margin-bottom:14px;color:#7c4a03;}
  @media (max-width:767px){.us-filter{grid-template-columns:1fr;}}
</style>

<div class="container-fluid tk">
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger outline alert-dismissible fade show" role="alert"><i class="icon-info-alt txt-danger"></i> {{ $error }}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button></div>
  @endforeach
  @if($message = Session::get('success'))
    <div class="us-temp alert-dismissible fade show" role="alert"><i class="fa fa-check-circle"></i> {!! $message !!}
      <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" style="float:right"></button></div>
  @endif

  <div class="tk-kpis">
    <a class="tk-kpi {{ !$status ? 'on' : '' }}" href="{{ route('portal-users', array_filter(['q' => $q, 'role' => $role])) }}"><span>All users</span><b>{{ $counts['total'] }}</b></a>
    <a class="tk-kpi {{ $status == 'Active' ? 'on' : '' }}" style="--a:#137a43" href="{{ route('portal-users', array_filter(['q' => $q, 'role' => $role, 'status' => 'Active'])) }}"><span>Active</span><b>{{ $counts['Active'] }}</b></a>
    <a class="tk-kpi {{ $status == 'Inactive' ? 'on' : '' }}" style="--a:#c62828" href="{{ route('portal-users', array_filter(['q' => $q, 'role' => $role, 'status' => 'Inactive'])) }}"><span>Deactivated</span><b>{{ $counts['Inactive'] }}</b></a>
    <a class="tk-kpi {{ $status == 'Reset' ? 'on' : '' }}" style="--a:#f28c28" href="{{ route('portal-users', array_filter(['q' => $q, 'role' => $role, 'status' => 'Reset'])) }}"><span>Must change password</span><b>{{ $counts['Reset'] }}</b></a>
  </div>

  <div class="tk-card">
    <form method="get" action="{{ route('portal-users') }}" class="us-filter">
      <div><label for="usQ">Search</label><input class="form-control" id="usQ" name="q" value="{{ $q }}" placeholder="Name, email, username or phone"></div>
      <div><label for="usRole">Role</label>
        <select class="form-select" id="usRole" name="role"><option value="">All roles</option>
          @foreach($roles as $r)<option value="{{ $r }}" @selected($role == $r)>{{ $r }}</option>@endforeach
        </select></div>
      <div><label for="usStatus">Status</label>
        <select class="form-select" id="usStatus" name="status"><option value="">All</option>
          <option value="Active" @selected($status == 'Active')>Active</option>
          <option value="Inactive" @selected($status == 'Inactive')>Deactivated</option>
          <option value="Reset" @selected($status == 'Reset')>Must change password</option>
        </select></div>
      <div class="d-flex gap-2"><button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
        @if($filtered)<a class="btn btn-light" href="{{ route('portal-users') }}">Reset</a>@endif</div>
    </form>

    <div class="tk-bar"><div><b>Users</b><span class="tk-count">{{ $users->total() }} {{ $filtered ? 'matching' : 'in total' }}</span></div></div>
    <div class="tk-scroll">
      <table class="tk-table">
        <thead><tr><th>#</th><th>User</th><th>Phone</th><th>Role</th><th>Branch</th><th>Status</th><th>Registered</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
        @forelse($users as $user)
          @php
            $me = $user->id == Auth::id();
            $name = trim(preg_replace('/\s+/', ' ', strtoupper($user->first_name)));
            $parts = preg_split('/\s+/', $name);
            $ini = substr($parts[0] ?? '', 0, 1).substr(count($parts) > 1 ? end($parts) : '', 0, 1);
            $c = $roleColor[$user->role] ?? '#55606e';
            $active = $user->status == 'Active';
            $branch = $user->branch_id ? strtoupper($user->branch_id) : null;
          @endphp
          <tr>
            <td class="tk-sub" style="margin:0">{{ $users->firstItem() + $loop->index }}</td>
            <td>
              <div class="us-person">
                <span class="us-avatar" style="background:{{ $c }}">{{ $ini ?: '?' }}</span>
                <div><b>{{ $name }}</b>@if($me)<span class="us-you">YOU</span>@endif
                  <span class="tk-sub">{{ $user->email }}@if($user->title && strcasecmp($user->title, $user->email) != 0) · {{ $user->title }}@endif</span></div>
              </div>
            </td>
            <td class="tk-mono us-nw">+255 {{ $user->mobile }}</td>
            <td><span class="us-role" style="--c:{{ $c }}">{{ $user->role }}</span></td>
            <td>@if($branch){{ $branchNames[$branch] ?? '' }}<span class="tk-sub">{{ $branch }}</span>@else<span class="tk-sub">—</span>@endif</td>
            <td>
              <span class="tk-pill {{ $active ? 'ok' : 'bad' }}">{{ $active ? 'Active' : ($user->status ?: 'Inactive') }}</span>
              @if($user->password_resert == 'yes')<span class="tk-note">Must change password</span>@endif
            </td>
            <td class="us-nw">{{ optional($user->created_at)->format('d M Y') }}<span class="tk-sub">{{ optional($user->created_at)->diffForHumans() }}</span></td>
            <td>
              <div class="us-actions">
                @if($me)
                  <a class="btn btn-outline-secondary" href="{{ route('security-user-profile') }}"><i class="fa fa-user"></i> My profile</a>
                @else
                  <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#roleModal"
                          data-id="{{ $user->id }}" data-name="{{ $name }}" data-role="{{ $user->role }}" data-branch="{{ $branch }}"><i class="fa fa-id-badge"></i> Role</button>
                  <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetModal" data-id="{{ $user->id }}" data-name="{{ $name }}"><i class="fa fa-key"></i> Reset</button>
                  @if($active)
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="{{ $user->id }}" data-name="{{ $name }}" data-status="Inactive"><i class="fa fa-ban"></i> Deactivate</button>
                  @else
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="{{ $user->id }}" data-name="{{ $name }}" data-status="Active"><i class="fa fa-check"></i> Activate</button>
                  @endif
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="tk-empty"><i class="fa fa-users fa-2x d-block mb-2"></i>{{ $filtered ? 'No users match your search.' : 'No users yet.' }}</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="tk-pager">
      <span>{{ $users->total() ? 'Showing '.$users->firstItem().'–'.$users->lastItem().' of '.$users->total() : 'No records' }}</span>
      {{ $users->links() }}
    </div>
  </div>
</div>

{{-- Assign role --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <form method="post" action="{{ route('security-user-role') }}" onsubmit="return tkBusy(this, 'Saving...')">
      @csrf
      <input type="hidden" name="id">
      <div class="modal-header"><h5 class="modal-title">Assign role · <span data-name></span></h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body">
        <div class="us-rolepick">
          @foreach(PUC::ROLES as $r => $desc)
            <label><input type="radio" name="role" value="{{ $r }}" required onchange="usBranch(this.form)"><span><b>{{ $r }}</b><small>{{ $desc }}</small></span></label>
          @endforeach
        </div>
        <div class="mt-3">
          <label class="form-label" for="roleBranch">Branch <small class="text-muted">(required for Branch Operations)</small></label>
          <select class="form-select" name="branch_id" id="roleBranch">
            <option value="">Keep current branch</option>
            @foreach ($distinctBranchCodes as $b)<option value="{{ strtoupper($b->BRANCH_CODE) }}">{{ strtoupper($b->BRANCH_NAME) }} ({{ strtoupper($b->BRANCH_CODE) }})</option>@endforeach
          </select>
        </div>
        <p class="tk-sub mt-2">The new role applies the next time the user opens a page.</p>
      </div>
      <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save role</button></div>
    </form>
  </div></div>
</div>

{{-- Reset password --}}
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <form method="post" action="{{ route('security-user-reset') }}" onsubmit="return tkBusy(this, 'Resetting...')">
      @csrf
      <input type="hidden" name="id">
      @foreach(['q' => $q, 'role' => $role, 'status' => $status, 'page' => request('page')] as $k => $v)@if($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif @endforeach
      <div class="modal-header"><h5 class="modal-title">Reset password</h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body">
        <p>Reset the password of <b data-name></b>?</p>
        <p class="tk-sub">A temporary password is created and shown to you once. The user must change it at their next login; their current password stops working.</p>
      </div>
      <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-warning" type="submit">Reset password</button></div>
    </form>
  </div></div>
</div>

{{-- Activate / deactivate --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <form method="post" action="{{ route('security-user-status') }}" onsubmit="return tkBusy(this, 'Saving...')">
      @csrf
      <input type="hidden" name="id"><input type="hidden" name="status">
      <div class="modal-header"><h5 class="modal-title" data-title></h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body"><p data-text></p></div>
      <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn" type="submit" data-btn></button></div>
    </form>
  </div></div>
</div>

{{-- New user --}}
<div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="post" action="{{ route('portal-users-add') }}" onsubmit="return tkBusy(this, 'Registering...')">
      @csrf
      <div class="modal-header"><h5 class="modal-title">New system user</h5><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control" name="first_name" value="{{ old('first_name') }}" required minlength="3"></div>
          <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" value="{{ old('username') }}" required></div>
          <div class="col-md-6"><label class="form-label">Email address</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="col-md-6"><label class="form-label">Phone number</label>
            <div class="input-group"><span class="input-group-text">+255</span><input class="form-control" name="mobile" value="{{ old('mobile') }}" required pattern="[0-9]{9}" maxlength="9" placeholder="7XXXXXXXX"></div></div>
          <div class="col-md-6"><label class="form-label">Role</label>
            <select class="form-select" name="role" required><option value="">Choose role</option>
              @foreach(PUC::ROLES as $r => $desc)<option value="{{ $r }}" @selected(old('role') == $r)>{{ $r }} — {{ $desc }}</option>@endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">Branch</label>
            <select class="form-select" name="branch_id" required><option value="">Choose branch</option>
              @foreach ($distinctBranchCodes as $b)<option value="{{ strtoupper($b->BRANCH_CODE) }}" @selected(old('branch_id') == strtoupper($b->BRANCH_CODE))>{{ strtoupper($b->BRANCH_NAME) }} ({{ strtoupper($b->BRANCH_CODE) }})</option>@endforeach
            </select></div>
          <div class="col-md-6"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required minlength="8" autocomplete="new-password"></div>
          <div class="col-md-6"><label class="form-label">Retype password</label><input class="form-control" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div>
          <div class="col-12 tk-sub">At least 8 characters with a lowercase letter, an uppercase letter and a symbol (@$!%*#?&).</div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Register user</button></div>
    </form>
  </div></div>
</div>

@push('scripts')
<script>
  function usBranch(form) {
    var r = form.querySelector('input[name=role]:checked');
    form.branch_id.required = !!r && r.value === 'Branch Operations';
  }
  function usFill(id, fn) {
    var m = document.getElementById(id);
    m.addEventListener('show.bs.modal', function (e) {
      var d = e.relatedTarget.dataset, f = m.querySelector('form');
      f.id.value = d.id;
      m.querySelectorAll('[data-name]').forEach(function (el) { el.textContent = d.name; });
      if (fn) { fn(f, d, m); }
    });
  }
  usFill('roleModal', function (f, d) {
    f.querySelectorAll('input[name=role]').forEach(function (r) { r.checked = r.value === d.role; });
    f.branch_id.value = '';
    usBranch(f);
  });
  usFill('resetModal');
  usFill('statusModal', function (f, d, m) {
    var on = d.status === 'Active';
    f.status.value = d.status;
    m.querySelector('[data-title]').textContent = on ? 'Activate user' : 'Deactivate user';
    m.querySelector('[data-text]').innerHTML = on
      ? 'Activate <b></b>? They will be able to log in again.'
      : 'Deactivate <b></b>? They will not be able to log in until an administrator activates them again.';
    m.querySelector('[data-text] b').textContent = d.name;
    var b = m.querySelector('[data-btn]');
    b.className = 'btn ' + (on ? 'btn-success' : 'btn-danger');
    b.textContent = on ? 'Activate' : 'Deactivate';
  });
  @if($errors->any() && old('email'))
    try { new bootstrap.Modal(document.getElementById('newModal')).show(); } catch (e) {}
  @endif
</script>
@endpush
@endsection
