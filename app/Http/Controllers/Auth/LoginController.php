<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response3
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...

            return back()
                ->with('loginError', 'Licence');
            // $user = DB::table('users')
            //     ->where('users.email', $request->get('email'))->get();
            // $licence = DB::table('licenses')
            //     ->where('licenses.organization_id', $user->organization_id)->get();
            // var_dump($licence->expiration,date("Y-m-d"));
            
            // if ($licence->expiration >= date("Y-m-d")) {
                return redirect()->intended('dashboard');
            // } else {
                    // die();
            //     return back()->with('error', 'Licence has expired');
            // }

        }
    }

    function checklogin(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password'  => 'required|alphaNum|min:3'
        ]);
        $user_data = array(
            'email'  => $request->get('email'),
            'password' => $request->get('password')
        );
        if (Auth::attempt($user_data)) {
            return redirect('main/successlogin');
        } else {
            return back()->with('error', 'Wrong Login Details');
        }
    }

    function successlogin()
    {
     return view('successlogin');
    }

    public function logout()
    {
        Auth::logout(); // log the user out of our application
        return redirect('home');
    }
}
