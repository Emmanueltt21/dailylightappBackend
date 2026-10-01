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
        if (!class_exists('Kreait\Firebase\Factory')) {
            if (file_exists(FCPATH . 'vendor_newVendor8.3/autoload.php')) {
                require_once FCPATH . 'vendor_newVendor8.3/autoload.php';
            } elseif (file_exists(FCPATH . 'vendor/autoload.php')) {
                require_once FCPATH . 'vendor/autoload.php';
            }
        }

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
                'topic' => 'all_users',
                'notification' => [
                    'title' => $title,
                    'body' => $contentData,
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'id' => '1',
                    'status' => 'done',
                    'title' => (string) $title,
                    'body' => (string) $bodyContent,
                ],
            ]);

            return $this->messaging->send($message);
        } catch (FirebaseException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}