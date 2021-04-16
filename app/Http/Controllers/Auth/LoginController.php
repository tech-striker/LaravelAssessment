<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|between:8,15',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors(), 'message' => 'Validation failed.'], 422);
        }

        $data = User::where('email',$request->email)->first();

        if($data->email_verified == 1){
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return redirect('login')->with('flash_message', 'You dont have registered properly!.')->with('flash_type', 'alert-info'); 
            }
            return redirect('home')->with(['user' => $data]); 
        }else {
            return redirect('login')->with('flash_message', 'You dont have registered properly!.')->with('flash_type', 'alert-info'); 
        }
    }
}
