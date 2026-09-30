<?php

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

if (!function_exists('get_firebase_access_token')) {
    function get_firebase_access_token()
    {
        $credentialsPath = APPPATH . 'config/firebase_credentials.json'; // Ensure correct path

        if (!file_exists($credentialsPath)) {
            die("Error: Firebase credentials file not found at: " . $credentialsPath);
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        try {
            // Load the credentials
            $creds = new ServiceAccountCredentials($scopes, $credentialsPath);
            
            // Fetch the access token
            $accessToken = $creds->fetchAuthToken();
            
            return $accessToken['access_token'] ?? null;
        } catch (RequestException $e) {
            return null;
        }
    }
}
