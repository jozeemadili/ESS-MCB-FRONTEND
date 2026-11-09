@extends('admin.authentication.master')

@section('title')Login
 | {{Config('custom.constants.solution.name')}}
@endsection

@push('css')
@endpush

@section('content')
    <style>
        :root {
            --mcb-blue: #0d47a1;
            --mcb-blue-dark: #082c66;
            --mcb-orange: #f28c28;
            --mcb-ink: #1f2933;
            --mcb-muted: #6b7785;
            --mcb-line: #dfe5ec;
        }
        .mcb-login { min-height: 100vh; display: flex; background: #f4f7fb; font-family: 'Rubik', 'Roboto', sans-serif; }

        /* Brand panel */
        .mcb-brand {
            flex: 1 1 50%; position: relative; overflow: hidden; color: #fff;
            background: linear-gradient(145deg, var(--mcb-blue) 0%, var(--mcb-blue-dark) 100%);
            display: flex; flex-direction: column; justify-content: space-between; padding: 56px 64px;
        }
        .mcb-brand::before, .mcb-brand::after {
            content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        }
        .mcb-brand::before { width: 520px; height: 520px; right: -180px; top: -160px; background: rgba(255,255,255,0.06); }
        .mcb-brand::after { width: 360px; height: 360px; left: -120px; bottom: -140px; background: rgba(242,140,40,0.18); }
        .mcb-brand-logo { position: relative; z-index: 1; display: inline-flex; align-self: flex-start; background: #fff; border-radius: 14px; padding: 10px 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.18); }
        .mcb-brand-logo img { height: 64px; width: auto; }
        .mcb-brand-body { position: relative; z-index: 1; max-width: 460px; }
        .mcb-brand-body h1 { color: #fff; font-size: 34px; font-weight: 600; line-height: 1.25; margin-bottom: 14px; }
        .mcb-brand-body h1 span { color: var(--mcb-orange); }
        .mcb-brand-body p { color: rgba(255,255,255,0.82); font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        .mcb-feature { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; color: rgba(255,255,255,0.92); font-size: 14px; }
        .mcb-feature i { width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;
                         background: rgba(255,255,255,0.12); color: var(--mcb-orange); font-size: 18px; flex: 0 0 38px; }
        .mcb-brand-foot { position: relative; z-index: 1; font-size: 12px; color: rgba(255,255,255,0.6); }

        /* Form panel */
        .mcb-form-side { flex: 1 1 50%; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
        .mcb-card { width: 100%; max-width: 420px; background: #fff; border-radius: 18px; padding: 40px 36px;
                    box-shadow: 0 20px 50px rgba(13,71,161,0.10); border: 1px solid #eef2f7; }
        .mcb-card .mcb-mobile-logo { display: none; text-align: center; margin-bottom: 20px; }
        .mcb-card .mcb-mobile-logo img { height: 72px; }
        .mcb-greeting { color: var(--mcb-orange); font-weight: 500; font-size: 14px; letter-spacing: 0.3px; margin-bottom: 6px; }
        .mcb-card h2 { color: var(--mcb-ink); font-size: 24px; font-weight: 600; margin-bottom: 6px; }
        .mcb-card .mcb-sub { color: var(--mcb-muted); font-size: 14px; margin-bottom: 28px; }

        .mcb-field { margin-bottom: 18px; }
        .mcb-field label { display: block; font-size: 13px; font-weight: 500; color: var(--mcb-ink); margin-bottom: 7px; }
        .mcb-input { position: relative; display: flex; align-items: center; }
        .mcb-input > i.mcb-lead { position: absolute; left: 14px; color: #9aa6b2; font-size: 16px; pointer-events: none; }
        .mcb-input input {
            width: 100%; height: 48px; border: 1.5px solid var(--mcb-line); border-radius: 10px; padding: 0 46px 0 42px;
            font-size: 14px; color: var(--mcb-ink); background: #fbfcfe; transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .mcb-input input::placeholder { color: #a8b3bf; }
        .mcb-input input:focus { outline: none; border-color: var(--mcb-blue); background: #fff; box-shadow: 0 0 0 4px rgba(13,71,161,0.10); }
        .mcb-input.is-invalid input { border-color: #e5484d; }
        .mcb-toggle {
            position: absolute; right: 8px; height: 34px; width: 34px; border: 0; border-radius: 8px; background: transparent;
            color: #7b8794; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 17px;
        }
        .mcb-toggle:hover, .mcb-toggle:focus-visible { background: #eef3fb; color: var(--mcb-blue); outline: none; }
        .mcb-error { color: #e5484d; font-size: 12px; margin-top: 6px; display: block; }
        .mcb-caps { display: none; color: #b25e09; font-size: 12px; margin-top: 6px; }
        .mcb-caps i { margin-right: 4px; }

        .mcb-row { display: flex; justify-content: flex-end; margin: -4px 0 22px; }
        .mcb-row a { font-size: 13px; color: var(--mcb-blue); font-weight: 500; }
        .mcb-row a:hover { color: var(--mcb-orange); }

        .mcb-submit {
            width: 100%; height: 50px; border: 0; border-radius: 10px; color: #fff; font-weight: 600; font-size: 15px; letter-spacing: 0.3px;
            background: linear-gradient(90deg, var(--mcb-blue) 0%, #1565c0 100%); box-shadow: 0 10px 22px rgba(13,71,161,0.25);
            display: inline-flex; align-items: center; justify-content: center; gap: 10px; transition: transform .1s, box-shadow .15s, opacity .15s;
        }
        .mcb-submit:hover { box-shadow: 0 12px 26px rgba(13,71,161,0.32); }
        .mcb-submit:active { transform: translateY(1px); }
        .mcb-submit[disabled] { opacity: 0.8; cursor: progress; }
        .mcb-spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%;
                       display: none; animation: mcb-spin .7s linear infinite; }
        .mcb-submit.loading .mcb-spinner { display: inline-block; }
        @keyframes mcb-spin { to { transform: rotate(360deg); } }

        .mcb-card .alert { margin-top: 18px; border-radius: 10px; font-size: 13px; }
        .mcb-secure { display: flex; align-items: center; justify-content: center; gap: 6px; color: var(--mcb-muted); font-size: 12px; margin-top: 22px; }
        .mcb-secure i { color: #2e9e6a; }
        .mcb-env { text-align: center; margin-top: 10px; font-size: 12px; }
        .mcb-env a { color: var(--mcb-muted); }

        @media (max-width: 991.98px) {
            .mcb-brand { display: none; }
            .mcb-card .mcb-mobile-logo { display: block; }
            .mcb-form-side { padding: 24px 16px; }
            .mcb-card { padding: 32px 22px; }
        }
    </style>

    <section class="mcb-login">
        <!-- Brand panel -->
        <div class="mcb-brand">
            <div class="mcb-brand-logo">
                <img src="{{ asset('assets/images/logo/mwalimu2.png') }}" alt="MCB Bank" />
            </div>
            <div class="mcb-brand-body">
                <h1>e-Mkopo <span>Back Office</span></h1>
                <p>Manage employee loans from ESS Utumishi: approvals, disbursement, restructuring, takeover and liquidation in one place.</p>
                <div class="mcb-feature"><i class="icofont icofont-money-bag"></i> New loans, approvals and disbursement</div>
                <div class="mcb-feature"><i class="icofont icofont-refresh"></i> Loan restructuring and takeover</div>
                <div class="mcb-feature"><i class="icofont icofont-chart-bar-graph"></i> Reports and Excel exports</div>
            </div>
            <div class="mcb-brand-foot">&copy; {{ date('Y') }} Mwalimu Commercial Bank PLC &middot; Better Together</div>
        </div>

        <!-- Form panel -->
        <div class="mcb-form-side">
            <div class="mcb-card">
                <div class="mcb-mobile-logo">
                    <img src="{{ asset('assets/images/logo/mwalimu2.png') }}" alt="MCB Bank" />
                </div>
                <div class="mcb-greeting">{{ App\TIRAClient\Scripts\Classes\Utils::timeGreeting() }} !</div>
                <h2>Sign in to your account</h2>
                <div class="mcb-sub">Use your e-Mkopo username and password.</div>

                <form method="post" action="/portal/auth" id="mcbLoginForm" novalidate>
                    @csrf
                    <div class="mcb-field">
                        <label for="mcbUsername">Username</label>
                        <div class="mcb-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                            <i class="icon-email mcb-lead"></i>
                            <input id="mcbUsername" type="text" name="email" value="{{ old('email') }}" required autofocus
                                   autocomplete="username" placeholder="Enter your username" />
                        </div>
                        @if($errors->has('email'))
                            <span class="mcb-error">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="mcb-field">
                        <label for="mcbPassword">Password</label>
                        <div class="mcb-input {{ $errors->has('password') ? 'is-invalid' : '' }}">
                            <i class="icon-lock mcb-lead"></i>
                            <input id="mcbPassword" type="password" name="password" required
                                   autocomplete="current-password" placeholder="Enter your password" />
                            <button type="button" class="mcb-toggle" id="mcbTogglePassword"
                                    aria-label="Show password" aria-pressed="false" title="Show password">
                                <i class="icofont icofont-eye"></i>
                            </button>
                        </div>
                        <span class="mcb-caps" id="mcbCapsWarning"><i class="icofont icofont-warning-alt"></i>Caps Lock is on</span>
                        @if($errors->has('password'))
                            <span class="mcb-error">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="mcb-row">
                        <a href="{{ route('forget-password') }}">Forgot password?</a>
                    </div>

                    <button class="mcb-submit" type="submit" id="mcbSubmit">
                        <span class="mcb-spinner"></span>
                        <span class="mcb-submit-text">Sign in</span>
                    </button>

                    @if($message = Session::get('error'))
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
                            {{ $message }}
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($message = Session::get('success'))
                        <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-success"></i>
                            {{ $message }}
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </form>

                <div class="mcb-secure"><i class="icofont icofont-lock"></i> Secure staff access &middot; activity is logged</div>
                <div class="mcb-env"><a href="#" target="_blank">UAT test Take over &amp; Restucturing. Mwalimu Cormmecial Bank PLC</a></div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        (function () {
            var password = document.getElementById('mcbPassword');
            var toggle = document.getElementById('mcbTogglePassword');
            var caps = document.getElementById('mcbCapsWarning');
            var form = document.getElementById('mcbLoginForm');
            var submit = document.getElementById('mcbSubmit');

            // Show / hide password
            toggle.addEventListener('click', function () {
                var show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                toggle.setAttribute('title', show ? 'Hide password' : 'Show password');
                toggle.querySelector('i').className = show ? 'icofont icofont-eye-blocked' : 'icofont icofont-eye';
                password.focus();
            });

            // Caps Lock warning
            function checkCaps(event) {
                if (event.getModifierState) {
                    caps.style.display = event.getModifierState('CapsLock') ? 'block' : 'none';
                }
            }
            password.addEventListener('keyup', checkCaps);
            password.addEventListener('keydown', checkCaps);
            password.addEventListener('blur', function () { caps.style.display = 'none'; });

            // Loading state; hide the password again before sending
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }
                password.type = 'password';
                submit.disabled = true;
                submit.classList.add('loading');
                submit.querySelector('.mcb-submit-text').textContent = 'Signing in...';
            });
        })();
    </script>
    @endpush

@endsection
