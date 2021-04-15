<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmergencyList;
use App\SOS_alert;
use Edujugon\PushNotification\PushNotification;

class InviteController extends Controller
{
	public function userInvite(Request $request){
		$data = $request->contactDetails;
		$EmergencyListData = EmergencyList::where(['user_id' => $request->user()->id])->with('user')->get();
		$ids1 = array();
		$ids2 = array();

		//print_r($EmergencyListData);die("--");

		try {

			$users 		= User::get();
			$plucked 	= $users->pluck('phone_number')->toArray();
			$result = array();

			foreach ($data as $key => $value) {
				if(in_array( $value['phoneNumber'] , $plucked )){
					$value['registered'] = TRUE;
					
					foreach ($EmergencyListData as $key1 => $value1) {
						if ($value1->user != NULL) {
							if($value['phoneNumber'] == $value1->user->phone_number)
							{
								$value['emergencyContact'] = TRUE;
							}			
						}												
					}
					$result[] = $value;
				}else{
					$value['registered'] = FALSE;
					$result[] = $value;
				}
			}

			return response()->json(['status' => 'success', 'message' => 'registered users!.', 'contactDetails' => $result], 200);
		} catch (\Exception $e) {
			print_r($e);
			return response()->json(['error' => 'something went wrong.please try again!.'], 500);
		}
	}

	public function emergencyList(Request $request){
		$request->validate([
			'contact_id' => 'required',
		]);
		$senderUser = $request->user();
		
		$data = User::whereIn('phone_number', $request->contact_id)->get();
		$device_tokens = $data->pluck('device_token');
		
		$dtoken = $device_tokens;
		
		$params = array('name' => $senderUser['first_name'].' '.$senderUser['last_name'],'phone' => $senderUser['phone_number'],'email' => $senderUser['email'],'id' => $senderUser['id']);
		$message = 'Do you want to be an emergency contact for '.$senderUser["phone_number"].' ('.$senderUser["first_name"].' '.$senderUser['last_name'].')?';
		
		try {
			sendPushnotification($dtoken,'Notification From GBV APP',$message,$params,1);
			return response()->json(['status' => 'success', 'message' => 'added emergency contact request send!.'], 200);
		} catch (\Exception $e) {
			return response()->json(['message' => 'failed to send request to  user!.'], 409);
		}
	}

	public function acceptReject(Request $request){
		/*status 1 => accept and 2 => reject*/
		$acpt_req_user = $request->user();
		$device_token_sender_req = User::where('id',$request->contact_ids)->first();
		$message = $acpt_req_user->first_name.' '.$acpt_req_user->last_name.' added in your emergency contact!.';
		$data= array('user_id' => $request->contact_ids , 'contact_ids' => $acpt_req_user->id);
		$params = '';
		try {
			if ($request->status == 1) {
				$res = EmergencyList::create($data);
				send_Pushnotification($device_token_sender_req->device_token,'Notification From GBV APP',$message,$params,2); // 2 accept request
				return response()->json(['status' => 'success', 'message' => 'accept emergency contact request!.'], 200);
			}else{
				return response()->json(['status' => 'success', 'message' => 'reject emergency contact request!.'], 200);
			}
			
		} catch (\Exception $e) {
			return response()->json(['message' => 'failed to accept-reject request!.'], 409);
		}
	}

	public function sosAlert(Request $request){
		$request->validate([
			'audio' => 'required',
			'lat' => 'required',
			'long' => 'required',
		]);
		$mime_type = $request->audio->getClientMimeType();
		//print_r($mime_type);die("--------");
		$pushData = array();
		$message = 'I have a problem.please help.';
		try {

			if ($request->file('audio')) {
				$files = $request->file('audio');
				$filenameWithExt = $files->getClientOriginalName();
				$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
				$extension = $files->getClientOriginalExtension();
				$mimeType = $files->getClientMimeType();
				$fileNameToStore = str_replace(" ", "-", $filename) . '_' . time() . '.' . $extension;
				//$path = $files->storeAs('audio', $fileNameToStore);
				$path = $files->move(public_path().'/audio/',$fileNameToStore);

				$params = array('user_id' => $request->user()->id, 'lat' => $request->lat, 'long' => $request->long, 'file' => '/audio/'.$fileNameToStore);
			}elseif ($request->file('video')){
				$files = $request->file('video');
				$filenameWithExt = $files->getClientOriginalName();
				$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
				$extension = $files->getClientOriginalExtension();
				$mimeType = $files->getClientMimeType();
				$fileNameToStore = str_replace(" ", "-", $filename) . '_' . time() . '.' . $extension;
				//$path = $files->storeAs('audio', $fileNameToStore);
				$path = $files->move(public_path().'/video/',$fileNameToStore);

				$params = array('user_id' => $request->user()->id, 'lat' => $request->lat, 'long' => $request->long, 'file' => '/video/'.$fileNameToStore);
			}

			$res = SOS_alert::create($params);
			$pushData['name'] 	= $request->user()->first_name.' '.$request->user()->last_name;
			$pushData['email'] 	= $request->user()->email;
			$pushData['phone'] 	= $request->user()->phone_number;
			$pushData['id'] 	= $request->user()->id;
			$pushData['file'] 	= $params['file'];

			$res = EmergencyList::where(['user_id' => $pushData['id']])->with('user')->get();
			$contactsList  = array();

			foreach ($res as $key => $value) {
				$contactsList[] = $value->user->device_token;
				sendMultiple($value->user->device_token,'GBV APP',$message,$pushData,2);
			}
			
			//$dtoken = $contactsList;
			//sendMultiple($dtoken,'GBV APP',$message,$pushData,2);
			return response()->json(['status' => 'success', 'message' => 'alerts send successfully!.'], 200);
		} catch (\Exception $e) {
			print_r($e);die;
			return response()->json(['message' => 'failed to send alert!.'], 409);
		}
	}
}
