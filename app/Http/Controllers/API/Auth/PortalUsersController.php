<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\Notifications\SMSController;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\InsurerIntermediary;
use App\Models\PRODUCT;
use App\Models\PRODUCTCONDITION;
use App\Models\User;
use App\TIRAClient\Scripts\Classes\Utils;
use App\TIRAClient\Scripts\Classes\TIRAClient;
use App\TIRAClient\Scripts\Classes\EsbClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;


class PortalUsersController extends Controller
{
    /** Roles a System Users admin can assign (older roles stay visible on users who still have them). */
    public const ROLES = [
        'ADMIN'             => 'Full access, manages users and settings',
        'Credit Officer'    => 'First review of loans (Waiting CO)',
        'Credit Admin'      => 'Credit administration approvals (Waiting CA)',
        'Credit Operation'  => 'Disbursement and takeover payments (Waiting COP)',
        'Branch Operations' => 'Sees loans of their own branch only',
        'Reports'           => 'Reports only',
    ];

    /** Same rule as the System Users menu */
    public static function canManageUsers($user = null): bool
    {
        $user = $user ?: Auth::user();
        return $user && ($user->role == 'ADMIN' || $user->email == 'cmselewa@azaniabank.co.tz');
    }

    public function get(Request $request)
    {
        if (substr(Route::getCurrentRoute()->uri, 3) != "security/users") {
            return User::orderBy('id', 'desc')->paginate(10);
        }
        abort_unless(self::canManageUsers(), 403);

        $q      = trim((string) $request->input('q'));
        $role   = $request->input('role');
        $status = $request->input('status');
        $users = User::query()
            ->when($q !== '', function ($w) use ($q) {
                $digits = ltrim(preg_replace('/\D/', '', $q), '0');
                $digits = str_starts_with($digits, '255') ? substr($digits, 3) : $digits;
                $w->where(function ($x) use ($q, $digits) {
                    $x->where('first_name', 'like', "%$q%")->orWhere('middle_name', 'like', "%$q%")->orWhere('last_name', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%")->orWhere('title', 'like', "%$q%")->orWhere('id_number', $q);
                    if ($digits !== '') { $x->orWhere('mobile', 'like', "%$digits%"); }
                });
            })
            ->when($role, fn ($w) => $w->where('role', $role))
            ->when($status, fn ($w) => $status === 'Reset' ? $w->where('password_resert', 'yes') : $w->where('status', $status))
            ->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $counts = [
            'total'    => User::count(),
            'Active'   => User::where('status', 'Active')->count(),
            'Inactive' => User::where('status', '<>', 'Active')->count(),
            'Reset'    => User::where('password_resert', 'yes')->count(),
        ];
        $roles = collect(array_keys(self::ROLES))->merge(User::select('role')->distinct()->pluck('role'))->filter()->unique()->values();
        $distinctBranchCodes = BRANCH::select('BRANCH_CODE', 'BRANCH_NAME')->distinct()->orderBy('BRANCH_CODE', 'ASC')->get();

        return view('admin.security.users', compact('users', 'distinctBranchCodes', 'counts', 'roles', 'q', 'role', 'status'));
    }

    /** Activate / deactivate a user (not yourself). */
    public function setStatus(Request $request)
    {
        abort_unless(self::canManageUsers(), 403);
        $request->merge(array_intersect_key((array) optional($request->route())->parameters(), ['id' => 1, 'status' => 1]));
        $request->validate(['id' => 'required|integer', 'status' => 'required|in:Active,Inactive']);
        $user = User::findOrFail($request->id);
        if ($user->id == Auth::id()) {
            return back()->withErrors(['You cannot change the status of your own account.']);
        }
        $user->status = $request->status;
        $user->save();
        $what = $request->status == 'Active' ? 'activated' : 'deactivated (cannot log in until activated)';
        return back()->with('success', '<b>'.e(strtoupper($user->first_name)).'</b> has been '.$what.'.');
    }

    /** Assign a role (and branch, needed for Branch Operations). */
    public function assignRole(Request $request)
    {
        abort_unless(self::canManageUsers(), 403);
        $request->validate(['id' => 'required|integer', 'role' => 'required|in:'.implode(',', array_keys(self::ROLES)), 'branch_id' => 'nullable|string|max:11']);
        $user = User::findOrFail($request->id);
        if ($user->id == Auth::id() && $request->role != $user->role) {
            return back()->withErrors(['You cannot change your own role. Ask another administrator.']);
        }
        if ($request->role == 'Branch Operations' && !$request->branch_id) {
            return back()->withErrors(['Choose the branch for a Branch Operations user.']);
        }
        $old = $user->role;
        $user->role = $request->role;
        if ($request->branch_id) {
            $user->branch_id = $request->branch_id;
        }
        $user->save();
        return back()->with('success', '<b>'.e(strtoupper($user->first_name)).'</b>: role changed from <b>'.e($old).'</b> to <b>'.e($user->role).'</b>'.($request->branch_id ? ', branch '.e($user->branch_id) : '').'.');
    }

    public function updateProdyctStatus(Request $request)
    {
        $endPoint='http://172.16.3.198:30002/api/v2/hmcis/'.$request->status.'/'.$request->id.'';
        $body='';
            $response = EsbClient::SendesbRequest($body,$endPoint);
            $myResponse = json_encode($response);
            $responseArray = json_decode($myResponse, true);
            if ($responseArray && isset($responseArray['status_code']) && $responseArray['status_code'] === 201) 
                {
                    return to_route('products-condtions',['id' => $request->id])->with('success', 'Successfully ');
                }
            else
                {
                    return to_route('products-condtions',['id' => $request->id])->with('success', 'Failed');
                }
    }

   

    public function profile()
    {
        return view('admin.security.profile');
    }

    public function change_password(Request $request)
    {
        $request->validate(['new_password' => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*#?&]/']],
            ['new_password.regex' => 'The password needs a lowercase letter, an uppercase letter, a number and a symbol (@$!%*#?&).',
             'new_password.confirmed' => 'The two passwords do not match.']);
        $User = User::find(Auth::user()->id);
        $User->password    = Hash::make($request->new_password);
        $User->is_first_time_pin = false;
        $User->password_resert = 'no';
        $User->save();
        Session::flush();
        Auth::logout();
        return redirect('/')->with('success', 'Successfully changed password, Login again');
    }

    public function resert_password(Request $request)
    {
        abort_unless(self::canManageUsers(), 403);
        $User = User::findOrFail($request->id);
        if ($User->id == Auth::id()) {
            return back()->withErrors(['Use My Profile to change your own password.']);
        }
        // Temporary password that meets the password rules; the user must change it at the next login
        $new_pass = 'Mcb@'.random_int(1000, 9999).chr(random_int(97, 122)).chr(random_int(65, 90));
        $User->password    = Hash::make($new_pass);
        $User->password_resert    = 'yes';
        $User->is_first_time_pin = false;
        $User->save();

        return redirect()->route('portal-users', $request->only('q', 'role', 'status', 'page'))
            ->with('success', 'Password reset for <b>'.e(strtoupper($User->first_name)).'</b>. Temporary password: <b class="tk-mono">'.$new_pass.'</b>. Give it to the user privately; they must change it when they log in.');
    }

    public function sendOTP($otp, $authenticatedUser = null)
    {
        $utils = new Utils();
        $user = $authenticatedUser == null ? Auth::user() : $authenticatedUser;

        $message       = "Dear ".ucfirst($user->first_name).", Use OTP ".$otp." to resert your password";
        $SMSController = new SMSController();
        try{
            //  $response = $SMSController->send("255".$user->mobile, $message);
            //$response = $SMSController->sendSandBox("255".$user->mobile, $message);
            $endPoint='sms';
            $body='{
                "msisdn":"255'.$user->mobile.'",
                "message":"'.$message.'",
            }
           ';
        // dd($body);
            $response = EsbClient::SendesbRequestbody($body,$endPoint); 
            
            $status = $response->status;
            $statusDescription = $response->statusDescription;
            if ($response->statusCode === 200) 
            {
                return array('responseCode' => 'SUCCESS', 'message' => 'SMS Submitted', 'response' => $message);
            }else
            {
                return array("responseCode" => "FAILED", "message" => $statusDescription );
            }
            
        }
        catch(Exception $e){
            return array("responseCode" => "FAILED", "message" => $e->getMessage());
        } 
    }

    public function search(Request $request)
    {
        if (substr(Route::getCurrentRoute()->uri, 3) == "security/users") {
            $q = $request->input('q', $request->input('cname', $request->input('phone_number', $request->input('id_number'))));
            return redirect()->route('portal-users', array_filter(['q' => $q, 'role' => $request->input('role'), 'status' => $request->input('status')]));
        }
        $users = User::where('mobile', $request->phone_number)->orWhere('id_number', $request->id_number)->orWhere('first_name', $request->cname)->orderBy('id','desc')->paginate(10);
        return response()->json(['responseCode'=> 'SUCCESS','message'=>'User Found Successfully', 'users' => $users]);
    }

    public function register(Request $request)
    {
        abort_unless(self::canManageUsers(), 403);
        $request->validate(['first_name' => ['required', 'min:3'], 'email' => ['required', 'email','unique:users'],'password' => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[@$!%*#?&]/'], 'mobile' => ['required', 'digits:9', 'unique:users'], 'role' => ['required', 'in:'.implode(',', array_keys(self::ROLES))]]);           
        $user = User::create(
            [
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'emp_id'        => 111,
                'branch_id'     => $request->branch_id,
                'title'         => $request->username,
                'first_name'    => $request->first_name,
                'middle_name'   => $request->first_name,
                'last_name'     => $request->first_name,
                'type'          => 1,
                'mobile'        => $request->mobile,
                'id_type'       => 1,
                'id_number'     => '111111',
                'dob'           => '2000-01-01',
                'role'          => $request->role,
                'company_id'    => 1,
                'created_by'    => intval(Auth::user()->id),
            ]);
            return redirect()->route('portal-users')->with('success', 'e-Mkopo User <b>'.strtoupper($request->first_name).' With user name ('.strtoupper($request->username).')</b> Successfully Registered  : ');
    }
    public function loginWeb(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'],'password' => ['required']]);
        if (!Auth::attempt($credentials))
        {
            return redirect('/')->with('error', 'Invalid username or Password');
        }
        else
        {
            $user = User::where('email', $request['email'])->first();
                if($user->status != 'Active')
                {
                    return redirect('/')->with('error', 'Sorry ! You have been deactivated.');
                }
                else
                {
                    if($user->password_resert === "yes")
                    {
                        return redirect()->intended(route('security-user-profile'));
                    }
                    else
                    {
                        return redirect()->intended(route('home'));
                    }

                }

            
            
        }
        
   }
    public function logout(Request $request)
    {
        try
        {
            if(substr(Route::getCurrentRoute()->uri, 3) == "logout")
            {
                Session::flush();
                Auth::logout();
                return redirect('/')->with('error', 'Successfully Logged out');
            }

            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'responseCode'  => 'SUCCESS',
                'message'       => 'Logged Out Successfully',
              ]);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message'       => $e->getMessage(),
                'responseCode'  => 'LOGGED_OUT_EXCEPTION'
              ]);
        }
    }

    public static function resolveIdType($id_type)
    {
        return ($id_type == 1 ? "NIDA" : (($id_type == 2 ? "Voters" : ($id_type == 3 ? "PASSPORT" : ($id_type == 4 ? "DRIVING LICENCE" : ($id_type == 5 ? "ZANID" : ($id_type == 6 ? "TIN" : ($id_type == 7 ? "INCORPORATION CERTIFICATE" : "N/A"))))) )));
    }

    public function howToUse(Request $request)
    {
        return view('guest.how-to-use');
    }

    public function index(Request $request)
    {
        return view('admin.authentication.login');
    }

}
