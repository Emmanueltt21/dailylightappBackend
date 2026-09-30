<?php
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

class FirebaseLib {
    private $messaging;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(APPPATH . 'config/firebase_credentials.json');

        $this->messaging = $firebase->createMessaging();
    }

    public function getMessaging()
    {
        return $this->messaging;
    }
}
?>
