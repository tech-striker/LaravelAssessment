<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Model\Invitation;
use Session;
use Notification;
use App\Notifications\InviteMail;
use App\Notifications\SendOTP;
use Redirect;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function registerForm(Request $request)
    {
        return view('auth.register',['token' => $request->input('token')]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(Request $request)
    {
        $request->validate([
            //'avtar'    =>  'required|image|mimes:jpeg,png,jpg',
            'name' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $data = Invitation::where('invitation_token' , $request->input('usertoken'))->first();
            $path = "";

            $userData = array(
                'user_name' => $request->name,
                'email' => $data->email,
                //'avtar' => ($path) ? $path : NULL,
                'password' => Hash::make($request->password),
                'otp' => $this->generaterandomotp(),
                'email_verified' => 0
            );
            
            Notification::route('mail', $userData['email'])->notify(new SendOTP($userData['otp']));

            $saveUserData = User::updateOrCreate(
                ['email' => $userData['email']],$userData
            );

            return view('auth.verify',['userData' => $userData['email']]);
        } catch (Exception $e) {
            
        }
    }

    public function generaterandomotp()
    {
        return mt_rand(100000, 999999);
    }

    protected function verifyOTP(Request $request)
    {
        try {
            $verify = User::where(['email' => $request->email , 'otp' => $request->verification_code])->first();
            $token = Invitation::where(['email' => $request->email ])->first();
            if ($verify) {
                $verify->otp = NULL;
                $verify->email_verified = 1;
                $verify->registered_at = now();
                $verify->save();

                return redirect('login')->with('flash_message', 'Register successfully!.')->with('flash_type', 'alert-info');
            } else {
                return redirect('register-form?token='.$token->invitation_token.'')->with('flash_message', 'Your OTP does not matched!.')->with('flash_type', 'alert-danger');
            }
            
        } catch (Exception $e) {
        }
    }
}
