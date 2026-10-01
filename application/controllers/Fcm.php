<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');
require_once APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations
 */
class Fcm extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedIn();
    }


    public function index(){
        $this->load->template('notification', []); // this will load the view file
    }

    function sendNotification()
    {
            $this->load->library('session');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('title','Title','trim|required|max_length[128]|xss_clean');
            $this->form_validation->set_rules('message','Message','trim|required|xss_clean');

            if($this->form_validation->run() == FALSE)
            {
                redirect('notifications');
            }
            else
            {
                $title = $this->input->post('title');
                $msg = $this->input->post('message');
                $this->load->library('PushNotification');
                $result = $this->pushnotification->sendNotification($title, $msg);
                if(isset($result->success) && $result->success)
                {
                    $this->session->set_flashdata('success', 'Notification sent successfully to app users.');
                }
                else
                {
                    $error = isset($result->error) ? $result->error : 'Failed to send notification';
                    $this->session->set_flashdata('error', $error);
                }

                redirect('notifications');
            }

    }

    //store user fcm token
        function storeFcmToken(){
            $data = $this->get_data();
            $this->load->model('fcm_model');
            if(isset($data->token) && $data->token!="")){
              $token = $data->token;
              $uuid = $data->uuid;
              $version = $data->location;
              $token = array("token"=>$token,"uuid"=>$uuid,"location"=>$location);
              $this->fcm_model->storeUserFcmToken($token);
            }
            echo json_encode(array("status" => $this->fcm_model->status
            ,"msg" => $this->fcm_model->message));
        }
}

?>
