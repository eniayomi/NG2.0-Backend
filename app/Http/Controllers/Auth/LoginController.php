<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use Exception;
use App\User;

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
    //Social login
    public function redirect($provider)
    {   
        session()->put('state', request()->input('state'));
        return Socialite::driver($provider)->redirect();
    }
    protected function redirectTo()
    {
        return "/home";
    }
    public function handleCallback($provider)
    {
        try {
            session()->put('state', request()->input('state'));
            $user = Socialite::driver($provider)->user();
            $finduser = User::where('provider_id', $user->id)->first();
            //dd($user->id);
   
            if($finduser){
   
                Auth::login($finduser);
  
                return redirect('/home');
   
            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider_id'=> $user->id,
                    'provider'=> $provider,
                    'avatar'=> $user->avatar,
                    'password' => md5(rand(1,10000)),
                ]);
               # dd($newUser);
                Auth::login($newUser);
                return $provider;    
                //return redirect('/home');
            }
  
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
