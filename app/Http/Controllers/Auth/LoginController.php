<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = "/sample";
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function username()
    {
        return 'name';
    }
    /**
     * ログイン時、api_token生成
     * 
     * @param Request $request
     * @param [type] $user
     * @return void
     */
    protected function authenticated(Request $request, $user)
    {
        $token = str_random(60);
        $user->update(['api_token' => $token]);
    }

    public function showLoginForm()
    {
        $js_file = "login.js";
        $message = "status";
        return view('auth.login', compact('js_file', 'message'));
    }


    /**
     * ログアウト時、api_token初期化
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        // api_tokenをnullにする
        $user = $request->user();
        $user->update(['api_token' => null]);

        $this->guard()->logout();

        $request->session()->flush();
        $request->session()->regenerate();

        // return redirect('/');
        return redirect('/sample');
    }
}
