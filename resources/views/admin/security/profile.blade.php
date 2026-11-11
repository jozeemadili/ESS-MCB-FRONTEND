@extends('layouts.admin.master')
@php
  $u = Auth::user();
  $name = trim(preg_replace('/\s+/', ' ', strtoupper($u->first_name)));
  $parts = preg_split('/\s+/', $name);
  $ini = substr($parts[0] ?? '', 0, 1).substr(count($parts) > 1 ? end($parts) : '', 0, 1);
  $branchName = null;
  if ($u->branch_id) {
      $branchName = optional(App\Models\BRANCH::where('BRANCH_CODE', $u->branch_id)->first())->BRANCH_NAME;
      if (!$branchName) {
          $legacy = App\Http\Controllers\API\Auth\CustomersController::getBranchName($u->branch_id);
          $branchName = stripos((string) $legacy, 'not found') === false ? $legacy : null;
      }
      $branchName = $branchName ? strtoupper($branchName) : null;
  }
  $mustChange = $u->password_resert === 'yes';
  $active = $u->status == 'Active';
@endphp
@section('title', 'My Profile')
@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>My Profile</h3>
    @endslot
    @slot('breadcrumb_action_buttons')
    @endslot
    <li class="breadcrumb-item">Security</li>
    <li class="breadcrumb-item active">My Profile</li>
  @endcomponent

<style>
  .pf{--b:#0d47a1;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .pf-card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:18px;overflow:hidden;}
  .pf-hero{background:linear-gradient(120deg,#0d47a1 0%,#1565c0 60%,#f28c28 160%);color:#fff;padding:26px 26px 22px;display:flex;align-items:center;gap:18px;flex-wrap:wrap;}
  .pf-avatar{width:76px;height:76px;border-radius:50%;background:rgba(255,255,255,.18);border:3px solid rgba(255,255,255,.55);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;letter-spacing:.02em;}
  .pf-hero h4{margin:0;color:#fff;font-weight:800;font-size:22px;}
  .pf-hero .pf-mail{opacity:.85;font-size:13px;margin-top:2px;}
  .pf-tags{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;}
  .pf-tag{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:3px 11px;font-size:11.5px;font-weight:700;}
  .pf-tag.ok{background:#e6f6ee;color:#137a43;border-color:#e6f6ee;} .pf-tag.bad{background:#fdecec;color:#c62828;border-color:#fdecec;}
  .pf-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));}
  .pf-item{padding:16px 22px;border-top:1px solid var(--line);border-right:1px solid var(--line);}
  .pf-item span{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .pf-item b{display:block;font-size:14.5px;margin-top:4px;word-break:break-word;}
  .pf-item small{color:var(--mut);}
  .pf-head{padding:16px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px;}
  .pf-head i{width:34px;height:34px;border-radius:10px;background:#e8effb;color:var(--b);display:flex;align-items:center;justify-content:center;}
  .pf-head b{font-size:15px;} .pf-head small{display:block;color:var(--mut);font-weight:400;}
  .pf-body{padding:18px 22px;}
  .pf-body label{font-size:12px;font-weight:700;color:#475569;margin-bottom:5px;}
  .pf-body .form-control{border:1px solid var(--line);border-radius:10px;height:42px;font-size:13.5px;}
  .pf-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:none;color:var(--mut);}
  .pf-rules{list-style:none;padding:0;margin:12px 0 16px;display:grid;grid-template-columns:1fr 1fr;gap:6px 10px;font-size:12.5px;color:var(--mut);}
  .pf-rules li:before{content:"\2022";display:inline-block;width:16px;font-weight:800;}
  .pf-rules li.ok{color:#137a43;} .pf-rules li.ok:before{content:"\2713";}
  .pf-alert{background:#fff4e5;border:1px solid #f6d7a7;color:#7c4a03;border-radius:12px;padding:12px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start;}
  .pf-btn{background:var(--b);border-color:var(--b);color:#fff;border-radius:10px;font-weight:700;height:42px;width:100%;}
  .pf-btn:disabled{opacity:.55;}
  @media (max-width:575px){.pf-rules{grid-template-columns:1fr;}}
</style>

<div class="container-fluid pf">
  @if($mustChange)
    <div class="pf-alert"><i class="fa fa-exclamation-triangle mt-1"></i>
      <div><b>Your password was reset by an administrator.</b> Set a new password below to continue using e-Mkopo.</div></div>
  @endif

  <div class="row">
    <div class="col-xl-8">
      <div class="pf-card">
        <div class="pf-hero">
          <div class="pf-avatar">{{ $ini ?: '?' }}</div>
          <div>
            <h4>{{ $name }}</h4>
            <div class="pf-mail">{{ strtolower($u->email) }}</div>
            <div class="pf-tags">
              <span class="pf-tag">{{ $u->role }}</span>
              <span class="pf-tag {{ $active ? 'ok' : 'bad' }}">{{ $active ? 'Active' : strtoupper((string) $u->status) }}</span>
              @if($branchName || $u->branch_id)<span class="pf-tag">{{ $branchName ?: 'Branch' }} ({{ $u->branch_id }})</span>@endif
            </div>
          </div>
        </div>
        <div class="pf-grid">
          <div class="pf-item"><span>Full name</span><b>{{ $name }}</b></div>
          <div class="pf-item"><span>Username</span><b>{{ $u->title ? strtoupper($u->title) : '—' }}</b></div>
          <div class="pf-item"><span>Email address</span><b>{{ strtolower($u->email) }}</b></div>
          <div class="pf-item"><span>Phone number</span><b>+255 {{ $u->mobile }}</b></div>
          <div class="pf-item"><span>Bank</span><b>{{ strtoupper(optional($u->company)->name ?? 'Mwalimu Commercial Bank') }}</b></div>
          <div class="pf-item"><span>Branch</span><b>{{ $branchName ?: '—' }}</b><small>{{ $u->branch_id ? 'Code '.$u->branch_id : '' }}</small></div>
          <div class="pf-item"><span>System role</span><b>{{ $u->role }}</b></div>
          <div class="pf-item"><span>Registered</span><b>{{ optional($u->created_at)->format('d M Y, H:i') }}</b><small>{{ optional($u->created_at)->diffForHumans() }}</small></div>
          <div class="pf-item"><span>Last profile update</span><b>{{ $u->updated_at ? \Carbon\Carbon::parse($u->updated_at)->format('d M Y, H:i') : '—' }}</b><small>{{ $u->updated_at ? \Carbon\Carbon::parse($u->updated_at)->diffForHumans() : '' }}</small></div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="pf-card">
        <div class="pf-head"><i class="fa fa-lock"></i><div><b>Change password</b><small>You will be logged out and asked to log in again.</small></div></div>
        <div class="pf-body">
          @foreach ($errors->all() as $error)
            <div class="alert alert-danger outline py-2" role="alert"><i class="icon-info-alt txt-danger"></i> {{ $error }}</div>
          @endforeach
          <form action="{{ route('security-user-change_password') }}" method="post" id="pfForm" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf
            <div class="mb-3">
              <label for="pfNew">New password</label>
              <div class="position-relative">
                <input class="form-control" type="password" id="pfNew" name="new_password" required autocomplete="new-password" placeholder="Enter new password">
                <button class="pf-eye" type="button" onclick="pfEye('pfNew', this)" aria-label="Show password"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <div class="mb-1">
              <label for="pfConfirm">Confirm new password</label>
              <div class="position-relative">
                <input class="form-control" type="password" id="pfConfirm" name="new_password_confirmation" required autocomplete="new-password" placeholder="Type it again">
                <button class="pf-eye" type="button" onclick="pfEye('pfConfirm', this)" aria-label="Show password"><i class="fa fa-eye"></i></button>
              </div>
            </div>
            <ul class="pf-rules" id="pfRules">
              <li data-r="len">At least 8 characters</li>
              <li data-r="low">A lowercase letter</li>
              <li data-r="up">An uppercase letter</li>
              <li data-r="num">A number</li>
              <li data-r="sym">A symbol (@$!%*#?&amp;)</li>
              <li data-r="same">Both passwords match</li>
            </ul>
            <button class="btn pf-btn" type="submit" id="pfSubmit" disabled>Change password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function pfEye(id, btn) {
    var i = document.getElementById(id), show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
  }
  (function () {
    var n = document.getElementById('pfNew'), c = document.getElementById('pfConfirm');
    function check() {
      var v = n.value, r = {len: v.length >= 8, low: /[a-z]/.test(v), up: /[A-Z]/.test(v), num: /[0-9]/.test(v), sym: /[@$!%*#?&]/.test(v), same: v !== '' && v === c.value};
      var all = true;
      document.querySelectorAll('#pfRules li').forEach(function (li) { var ok = r[li.dataset.r]; li.classList.toggle('ok', ok); all = all && ok; });
      document.getElementById('pfSubmit').disabled = !all;
    }
    n.addEventListener('input', check); c.addEventListener('input', check);
  })();
</script>
@endpush
@endsection
