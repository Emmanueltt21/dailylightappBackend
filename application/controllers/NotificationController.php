<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';


class NotificationController extends CI_Controller {
    
     public function __construct(){
        parent::__construct();
				//$this->isLoggedIn();
				$this->load->model('Notifications_model');
				$this->load->library('PushNotification'); // Load the custom library
    }

    




 public function send_announcement() {
        // Basic usage
        $result = $this->pushnotification->sendNotification(
            'Important Announcement', 
            'This is an important message for all users.'
        );
        
        // Check if notification was sent successfully
        if ($result->success) {
            echo "Notification sent successfully!";
        } else {
            echo "Error sending notification: " . $result->error;
        }
    }
    
    public function send_with_data() {
        // Send with additional data
        $result = $this->pushnotification->sendNotification(
            'New Feature Available', 
            'Check out our new feature in the app!',
            'all_users_test',
            array(
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'feature_id' => '123',
                'type' => 'feature_announcement'
            )
        );
        
        // Output the result
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
    
    public function send_to_multiple_topics() {
        // Send to multiple topics
        $result = $this->pushnotification->sendMultipleTopics(
            'System Update', 
            'The system will be down for maintenance tonight.',
            array('admins', 'staff', 'premium_users')
        );
        
        // Output the result
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
    
    
    
}