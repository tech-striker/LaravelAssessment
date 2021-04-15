 <?php
use Edujugon\PushNotification\PushNotification;
use App\Models\Push_notification;
use App\Models\User;
    /*function send_notification_FCM($notification_id, $title, $message, $id,$type) {
 
        $accesstoken = env('FCM_KEY');
     
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $post_data = '{
          "to" : "' . $notification_id . '",
          "data" : {
            "body" : "",
            "title" : "' . $title . '",
            "type" : "' . $type . '",
            "id" : "' . $id . '",
            "message" : "' . $message . '",
          },
          "notification" : {
            "body" : "' . $message . '",
            "title" : "' . $title . '",
            "type" : "' . $type . '",
            "id" : "' . $id . '",
            "message" : "' . $message . '",
            "icon" : "new",
            "sound" : "default"
          },
        }';
        //print_r($post_data);die;
     
        $crl = curl_init();
     
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: key='.$accesstoken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
     
        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
     
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
     
        $rest = curl_exec($crl);
     
        if ($rest === false) {
          $result_noti = 0;
        } else {
          $result_noti = 1;
        }
        //curl_close($crl);
        //print_r($result_noti);die;
        //return $result_noti;
        return $rest;
    }*/

    /*function sendTo($registration_id, $title, $message, $id,$type) {
 
        $accesstoken = env('FCM_KEY');
     
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $post_data = '{
            "to" : "' . $registration_id . '",
            "data" : {
                "body" : "",
                "title" : "' . $title . '",
                "type" : "' . $type . '",
                "id" : "' . $id . '",
                "message" : "' . $message . '",
            },
            "notification" : {
                "body" : "' . $message . '",
                "title" : "' . $title . '",
                "type" : "' . $type . '",
                "id" : "' . $id . '",
                "message" : "' . $message . '",
                "icon" : "new",
                "sound" : "default"
            },
        }';
        //print_r($post_data);die;
     
        $crl = curl_init();
     
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: key='.$accesstoken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
     
        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
     
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
     
        $rest = curl_exec($crl);
     
        if ($rest === false) {
          $result_noti = 0;
        } else {
          $result_noti = 1;
        }
        return $rest;
    }*/

    function sendMultiple($device_token, $title, $message, $data ,$notificationType) {
        $push = new PushNotification('fcm');
        $push->setConfig([
            'priority' => 'high',
            'time_to_live' => 5,
            'dry_run' => false
        ]);
        
        try {
            if($data){
                $notificationData = [
                    'name'      =>  $data['name'],
                    'phone'     =>  $data['phone'],
                    'email'     =>  $data['email'],
                    'id'        =>  $data['id'],
                    'file'      =>  $data['file'],
                ];

                /*$save_notification_data = array('reciver_id' => $data['service_provide_id'],'message' => $message , 'notification_data' => json_encode($notificationData));
                $save_notification = Push_notification::create($save_notification_data);*/
            }else{
                $notificationData = '';
               /* $data = User::where('device_token',$device_token)->first();
                $save_notification_data = array('reciver_id' => $data->id ,'message' => $message , 'notification_data' => NULL);
                $save_notification = Push_notification::create($save_notification_data);*/
            }
        
            $dd = $device_token;
            $extraNotificationData = [
                'title'                 => $title,
                'body'                  => $message,
                'sound'                 => 'default',
                'badge'                 => 1,
                'message'               => $notificationData,
                'notificationType'      => $notificationType,
            ];

            $push->setMessage([
                'notification' => [
                    'title' => $title,
                    'body'  => $message,
                    'sound' => 'default'
                ],
                'data' =>  $extraNotificationData
            ])
            ->setApiKey(env('FCM_KEY'))
            ->setDevicesToken($dd)
            ->send();
        } catch (Exception $e) {
            print_r($e);
        }
    }



    function sendPushnotification($device_token, $title, $message, $data ,$notificationType)
    {
        $push = new PushNotification('fcm');
        $push->setConfig([
            'priority' => 'high',
            'time_to_live' => 5,
            'dry_run' => false
        ]);
        
        try {
            if($data){
                $notificationData = [
                    'name'      =>  $data['name'],
                    'phone'     =>  $data['phone'],
                    'email'     =>  $data['email'],
                    'id'        =>  $data['id'],
                ];

                /*$save_notification_data = array('reciver_id' => $data['service_provide_id'],'message' => $message , 'notification_data' => json_encode($notificationData));
                $save_notification = Push_notification::create($save_notification_data);*/
            }else{
                $notificationData = '';
               /* $data = User::where('device_token',$device_token)->first();
                $save_notification_data = array('reciver_id' => $data->id ,'message' => $message , 'notification_data' => NULL);
                $save_notification = Push_notification::create($save_notification_data);*/
            }
        
            $dd = $device_token->toArray();
            $extraNotificationData = [
                'title'                 => $title,
                'body'                  => $message,
                'sound'                 => 'default',
                'badge'                 => 1,
                'message'               => $notificationData,
                'notificationType'      => $notificationType,
            ];

            $push->setMessage([
                'notification' => [
                    'title' => $title,
                    'body'  => $message,
                    'sound' => 'default'
                ],
                'data' =>  $extraNotificationData
            ])
            ->setApiKey(env('FCM_KEY'))
            ->setDevicesToken($dd)
            ->send();
        } catch (Exception $e) {
        }
    }


    function send_Pushnotification($device_token, $title, $message, $data ,$notificationType)
    {
        $push = new PushNotification('fcm');
        $push->setConfig([
            'priority' => 'high',
            'time_to_live' => 5,
            'dry_run' => false
        ]);
        
        try {
            if($data){
                $notificationData = [
                    'name'      =>  $data['name'],
                    'phone'     =>  $data['phone'],
                    'email'     =>  $data['email'],
                ];

                /*$save_notification_data = array('reciver_id' => $data['service_provide_id'],'message' => $message , 'notification_data' => json_encode($notificationData));
                $save_notification = Push_notification::create($save_notification_data);*/
            }else{
                $notificationData = '';
               /* $data = User::where('device_token',$device_token)->first();
                $save_notification_data = array('reciver_id' => $data->id ,'message' => $message , 'notification_data' => NULL);
                $save_notification = Push_notification::create($save_notification_data);*/
            }
            
            $dd = $device_token;
            $extraNotificationData = [
                'title'                 => $title,
                'body'                  => $message,
                'sound'                 => 'default',
                'badge'                 => 1,
                'message'               => "notificationData",
                'notificationType'      => $notificationType,
            ];

            $push->setMessage([
                'notification' => [
                    'title' => $title,
                    'body'  => $message,
                    'sound' => 'default'
                ],
                'data' =>  $extraNotificationData
            ])
            ->setApiKey(env('FCM_KEY'))
            ->setDevicesToken($dd)
            ->send();
        } catch (Exception $e) {
            
        }
    }