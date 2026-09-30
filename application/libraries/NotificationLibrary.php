<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\FirebaseException;

class NotificationLibrary {
    private $messaging;

    public function __construct() {
        require 'vendor/autoload.php'; // Load Firebase SDK

        $firebase = (new Factory)
            ->withServiceAccount(APPPATH . 'config/firebase_credentials.json');

        $this->messaging = $firebase->createMessaging();
    }


public function sendNotificationToAllUser($title, $bodyContent) {
    
    if(empty($title) || empty($bodyContent)) {
            return "Error: Title or body content is empty!";
        }
        
     $contentData = (strlen($bodyContent) > 35) ? substr($bodyContent, 0, 35) . '...' : $bodyContent;
     
    try {
        $message = CloudMessage::fromArray([
             // 'topic' => 'all_users', //Real
               'topic' => 'all_users_test',
            'notification' => [
                'title' => $title,
                'body' => $contentData,
               // 'body' => (strlen($content) > 35) ? substr($content, 0, 35) . '...' : $content, // Limit body length
            ],
            'data' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'id' => '1',
                'status' => 'done',
                'title' => $title,
                'body' => $bodyContent, // Send the full body in data payload
            ],
        ]);

        return $this->messaging->send($message);
    } catch (FirebaseException $e) {
        return 'Error: ' . $e->getMessage();
    }
}
    
    
    public function sendNotificationToAllUserXXXX($title, $bodyContent) {
    
        if(empty($title) || empty($bodyContent)) {
            return "Error: Title or body content is empty!";
        }
        
          // $this-> $response =  ($strlen($bodyContent) > 35) ? substr($bodyContent, 0, 35) . '...' : $bodyContent;
                  $response = (strlen($bodyContent) > 35) ? substr($bodyContent, 0, 35) . '...' : $bodyContent;

          
            return   $response;
            
}


    
    public function sendNotificationToAllUserOLD() {
        try {
            $message = CloudMessage::fromArray([
               // 'topic' => 'all_users', //Real
               'topic' => 'all_users_test',
                'notification' => [
                    'title' => 'Global Announcement!',
                    'body' => 'This is a test notification for all users.',
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'id' => '1',
                    'status' => 'done',
                ],
            ]);

            return $this->messaging->send($message);
        } catch (FirebaseException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}