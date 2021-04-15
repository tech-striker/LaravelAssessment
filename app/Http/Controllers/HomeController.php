<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        return view('home',['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
            $request->validate([
                'avtar'    =>  'required|image|mimes:jpeg,png,jpg',
                'name' => ['required', 'string', 'max:20'],
            ]);
    
            try {
                $path = "";
    
                if ($request->file('avtar')) {
                    $filenameWithExt = $request->file('avtar')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('avtar')->getClientOriginalExtension();
                    $mimeType = $request->file('avtar')->getClientMimeType();
                    $fileNameToStore = str_replace(" ", "-", $filename) . '_' . time() . '.' . $extension;
                    $path = $request->file('avtar')->storeAs('images', $fileNameToStore);
                }
    
                $userData = array(
                    'user_name' => $request->name,
                    //'email' => $data->email,
                    'avtar' => ($path) ? $path : NULL,
                );
    
                $saveUserData = User::updateOrCreate(
                    ['email' => $request->email],$userData
                );
                $user = Auth::user();
                return redirect('home')->with(['user' => $user])->with('flash_message', 'Updated successfully!.')->with('flash_type', 'alert-info');
            
        } catch (Exception $e) {
        }
    }
}
