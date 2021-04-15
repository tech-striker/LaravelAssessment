<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Invitation;
use App\User;
use App\Mail\InviteCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Notification;
//use Illuminate\Notifications\Notifiable;
use App\Notifications\InviteMail;
use Illuminate\Support\Facades\URL;

class ApiController extends Controller
{
	public function store(Request $request)
	{
		$request->validate([
			'email' => 'required|email'
		]);

		try {
			$exist = Invitation::where(['email' => $request->email])->first();
		
			if ($exist) {
				return response()->json(['status' => 'success', 'message' => 'already requested!.'], 200);
			}
			$invitation = new Invitation($request->all());
			$invitation->generateInvitationToken();
			$invitation->save();

			$url = url('/')."/register-form?token=".$invitation->invitation_token;
			
			Notification::route('mail', $request->input('email'))->notify(new InviteMail($url));

			return response()->json(['status' => 'success', 'message' => 'invitation send successfully!.'], 200);
		} catch (Exception $e) {
			return response()->json(['status' => 'success', 'message' => 'failed to send invitation'], 200);
		}
		
	}
}
