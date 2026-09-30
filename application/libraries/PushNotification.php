<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PushNotification Library
 * 
 * A CodeIgniter library for sending push notifications via Firebase Cloud Messaging
 * using the custom notification API.
 */
class PushNotification {
    
    protected $CI;
    protected $api_url;
    protected $default_topic;
    
    /**
     * Constructor
     * 
     * @param array $config Configuration parameters
     */
    public function __construct($config = array()) {
        $this->CI =& get_instance();
        
        // Default configuration
        $this->api_url = 'https://pushsdk.vercel.app/sendNotification';
        //$this->default_topic = 'all_users';
         $this->default_topic = 'all_users';
        
        // Override defaults with custom config
        if (!empty($config)) {
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }
    
    /**
     * Send a notification
     * 
     * @param string $title The notification title
     * @param string $body The notification body
     * @param string $topic The topic to send to (optional)
     * @param array $data Additional data to send (optional)
     * @return object Response from the notification API
     */
    public function sendNotification($title, $body, $topic = null, $data = array()) {
        if (empty($title) || empty($body)) {
            return (object) array(
                'success' => false,
                'error' => 'Title and body are required'
            );
        }
        
        // Use default topic if none provided
        if (empty($topic)) {
            $topic = $this->default_topic;
        }
        
        // Prepare the notification payload
        $payload = array(
            'topic' => $topic,
            'title' => $title,
            'body' => $body
        );
        
        // Add additional data if provided
        if (!empty($data) && is_array($data)) {
            $payload['data'] = $data;
        }
        
        // Send the notification using cURL
        return $this->_sendCurl($payload);
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
     * Send the cURL request to the notification API
     * 
     * @param array $payload The notification payload
     * @return object Response from the API
     */
    private function _sendCurl($payload) {
        // Initialize cURL
        $ch = curl_init($this->api_url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($payload))
        ));
        
        // Execute cURL request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        // Close cURL connection
        curl_close($ch);
        
        // Handle response
        if ($response === false) {
            return (object) array(
                'success' => false,
                'error' => 'cURL Error: ' . $curl_error
            );
        }
        
        // Decode JSON response
        $result = json_decode($response);
        
        // If not a valid JSON response
        if ($result === null) {
            return (object) array(
                'success' => false,
                'error' => 'Invalid API response',
                'http_code' => $http_code,
                'raw_response' => $response
            );
        }
        
        return $result;
    }
    
    /**
     * Set the API URL
     * 
     * @param string $url The API URL
     * @return void
     */
    public function setApiUrl($url) {
        $this->api_url = $url;
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