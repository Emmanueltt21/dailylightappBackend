<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\FirebaseException;

/**
 * PushNotification Library
 * 
 * A CodeIgniter library for sending push notifications directly via
 * Firebase Cloud Messaging (FCM HTTP v1) using Google Service Account credentials.
 * Configured with full APNs support for iOS and Android high priority.
 */
class PushNotification {
    
    protected $CI;
    protected $messaging = null;
    protected $default_topic = 'all_users';
    protected $init_error = null;
    
    /**
     * Constructor
     * 
     * @param array $config Configuration parameters
     */
    public function __construct($config = array()) {
        $this->CI = get_instance();
        
        // Ensure Kreait Firebase SDK is loaded
        if (!class_exists('Kreait\Firebase\Factory')) {
            if (file_exists(FCPATH . 'vendor_newVendor8.3/autoload.php')) {
                require_once FCPATH . 'vendor_newVendor8.3/autoload.php';
            } elseif (file_exists(FCPATH . 'vendor/autoload.php')) {
                require_once FCPATH . 'vendor/autoload.php';
            } elseif (file_exists(APPPATH . 'vendor/autoload.php')) {
                require_once APPPATH . 'vendor/autoload.php';
            }
        }
        
        // Override defaults with custom config
        if (!empty($config) && is_array($config)) {
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
        
        if (!class_exists('Kreait\Firebase\Factory')) {
            $this->init_error = 'Firebase SDK autoload failed: vendor/autoload.php not found. Run "composer install" on server.';
            log_message('error', 'PushNotification: ' . $this->init_error);
            return;
        }
        
        // Initialize Firebase Messaging
        try {
            $credentialsFile = APPPATH . 'config/firebase_credentials.json';
            if (!file_exists($credentialsFile) && file_exists(FCPATH . 'firebase_credentials.json')) {
                $credentialsFile = FCPATH . 'firebase_credentials.json';
            }
            if (file_exists($credentialsFile)) {
                $firebase = (new Factory)->withServiceAccount($credentialsFile);
                $this->messaging = $firebase->createMessaging();
            } else {
                $this->init_error = 'Firebase credentials file missing at ' . $credentialsFile . '. Please upload firebase_credentials.json to application/config/ on Hostinger.';
                log_message('error', 'PushNotification: ' . $this->init_error);
            }
        } catch (\Throwable $e) {
            $this->init_error = 'Firebase Init Error: ' . $e->getMessage();
            log_message('error', 'PushNotification initialization error: ' . $e->getMessage());
        }
    }
    
    /**
     * Build message payload array for FCM HTTP v1 with full Android and iOS APNs support
     * 
     * @param string $targetType 'topic' or 'token'
     * @param string $targetValue Topic name or device token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data key-value pairs
     * @return array Complete CloudMessage configuration array
     */
    protected function _buildMessageArray($targetType, $targetValue, $title, $body, $data = array()) {
        $cleanBody = strip_tags($body);
        
        // Stringify all data values for FCM v1 requirement
        $dataPayload = array(
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'title' => (string) $title,
            'body' => (string) $cleanBody,
        );
        
        if (!empty($data) && is_array($data)) {
            foreach ($data as $k => $v) {
                $dataPayload[(string)$k] = is_scalar($v) ? (string) $v : json_encode($v);
            }
        }
        
        $messageArray = array(
            $targetType => $targetValue,
            'notification' => array(
                'title' => (string) $title,
                'body' => (string) $cleanBody,
            ),
            'data' => $dataPayload,
            // Android high-priority configuration
            'android' => array(
                'priority' => 'high',
                'notification' => array(
                    'sound' => 'default',
                    'default_sound' => true,
                    'default_vibrate_timings' => true,
                ),
            ),
            // iOS APNs configuration (critical for iOS lock screen, sound, and background wake)
            'apns' => array(
                'headers' => array(
                    'apns-priority' => '10',
                    'apns-push-type' => 'alert',
                    'apns-topic' => 'com.lighthouseglobal.yourdailylight',
                ),
                'payload' => array(
                    'aps' => array(
                        'alert' => array(
                            'title' => (string) $title,
                            'body' => (string) $cleanBody,
                        ),
                        'sound' => 'default',
                        'badge' => 1,
                        'content-available' => 1,
                    ),
                ),
            ),
        );
        
        return $messageArray;
    }
    
    /**
     * Send a notification directly to FCM topic (with iOS APNs & Android support)
     * 
     * @param string $title The notification title
     * @param string $body The notification body
     * @param string $topic The topic to send to (optional, defaults to 'all_users')
     * @param array $data Additional data to send (optional)
     * @return object Standard response object { success: bool, message_id?: string, error?: string }
     */
    public function sendNotification($title, $body, $topic = null, $data = array()) {
        if (empty($title) || empty($body)) {
            return (object) array(
                'success' => false,
                'error' => 'Title and body are required'
            );
        }
        
        if ($this->messaging === null) {
            return (object) array(
                'success' => false,
                'error' => $this->init_error ?: 'Firebase Messaging could not be initialized'
            );
        }
        
        if (empty($topic)) {
            $topic = $this->default_topic;
        }
        
        try {
            $messageArray = $this->_buildMessageArray('topic', $topic, $title, $body, $data);
            $message = CloudMessage::fromArray($messageArray);
            
            $response = $this->messaging->send($message);
            $messageId = is_array($response) && isset($response['name']) ? $response['name'] : (is_string($response) ? $response : 'sent');
            
            return (object) array(
                'success' => true,
                'message_id' => $messageId,
                'topic' => $topic
            );
        } catch (FirebaseException $e) {
            log_message('error', 'PushNotification FCM send error: ' . $e->getMessage());
            return (object) array(
                'success' => false,
                'error' => $e->getMessage()
            );
        } catch (\Throwable $e) {
            log_message('error', 'PushNotification general error: ' . $e->getMessage());
            return (object) array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }
    
    /**
     * Send notification to a specific device FCM token (with iOS APNs & Android support)
     * 
     * @param string $token Device FCM registration token
     * @param string $title The notification title
     * @param string $body The notification body
     * @param array $data Additional data (optional)
     * @return object Standard response object
     */
    public function sendToToken($token, $title, $body, $data = array()) {
        if (empty($token) || empty($title) || empty($body)) {
            return (object) array(
                'success' => false,
                'error' => 'Token, title and body are required'
            );
        }
        
        if ($this->messaging === null) {
            return (object) array(
                'success' => false,
                'error' => $this->init_error ?: 'Firebase Messaging could not be initialized'
            );
        }
        
        try {
            $messageArray = $this->_buildMessageArray('token', $token, $title, $body, $data);
            $message = CloudMessage::fromArray($messageArray);
            
            $response = $this->messaging->send($message);
            $messageId = is_array($response) && isset($response['name']) ? $response['name'] : (is_string($response) ? $response : 'sent');
            
            return (object) array(
                'success' => true,
                'message_id' => $messageId
            );
        } catch (\Throwable $e) {
            log_message('error', 'PushNotification sendToToken error: ' . $e->getMessage());
            return (object) array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }
    
    /**
     * Send notification to multiple topics
     * 
     * @param string $title The notification title
     * @param string $body The notification body
     * @param array $topics Array of topics to send to
     * @param array $data Additional data to send (optional)
     * @return array Responses for each topic
     */
    public function sendMultipleTopics($title, $body, $topics = array(), $data = array()) {
        if (empty($topics) || !is_array($topics)) {
            return array(
                'success' => false,
                'error' => 'Topics must be provided as an array'
            );
        }
        
        $responses = array();
        foreach ($topics as $topic) {
            $responses[$topic] = $this->sendNotification($title, $body, $topic, $data);
        }
        
        return $responses;
    }
    
    /**
     * Set the default topic
     * 
     * @param string $topic The default topic
     * @return void
     */
    public function setDefaultTopic($topic) {
        $this->default_topic = $topic;
    }
}