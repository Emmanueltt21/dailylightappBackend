<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';

class Payment extends BaseController {
    
    public $url = "http://pay.iwomitechnologies.com:8080/iwomipay_sandbox/";
    public $username = "iwomipay2021";
    public $password = "iwomipay@2020";
    public $failed_url = "";
    public $success_url = "";
    public $callback_url = "https://hupertech.com/ChurchApp/callback";
    public $message = "";
    public $status = "";
    
     
             
    
//    public $url = "http://pay.iwomitechnologies.com:8080/iwomipay_prodv1/";
//    public $username = "2605MOBIC2021";
//    public $password = "2021MOSA26BICEC";

	public function __construct()
    {
        parent::__construct();

	//$this->load->model('Transaction_model');
//	$this->load->model('Librairie_model');
        
    }
    
    
    
    public function authenticate()
	{
        
            $url = $this->url."authenticate";
            $data = array(
            'username' => $this->username,
            'password' => $this->password
            );
            
            $table_json = json_encode($data); 
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $table_json,
            CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
            ),
                
            ));
            
            $result = curl_exec($curl);  //var_dump($result);
            if (curl_errno($curl))
            print curl_error($curl);
            else
            curl_close($curl);
            $res =  (array)json_decode($result);   
            if(isset($res["status"]) && $res["status"] =="01"){
                return $res["token"];
            }else{
                
                return "NOK";
            }
        
        
	}

    public function request_payment($object=null)
	{
        
          // TEST PLATEFORME:
            $response = array();
            $momo_apikey = "5e016fb8-ef10-458c-80c4-442e94708eee";
            $momo_apisecret = "6ef3965f-ccb9-4f70-8864-1ca1de8e7c7d";
            $om_apikey = "36f4b65b-bbcf-4ca6-b881-1ba76b95ac39";
            $om_apisecret = "0cf40e83-e3eb-4bc1-9504-8d6cd3878343";
            $card_apikey = "5e8e482d-e299-4e40-86d6-41d1cf218466";
            $card_apisecret = "970bef1a-60d2-4925-b478-af0cbd4691ed";
            $paypal_apikey = "c4990995-29de-429a-8cdd-c5509953a0e6";
            $paypal_apisecret = "e64494b6-59a2-4579-b067-566ca140be63";
            
            $op_type =  $object["method"];
            $url = $this->url ."iwomipay";   //  var_dump($object);
            $token = $this->authenticate();
            
            if($token =="NOK" ){
                
                $response["status"] ="110";
                $response["message"] ="Invalid token";
                
                return $response;
                
            }
            
            $apiKey= "";
            $apiSecret = "";
            if($op_type == "om"){

               $apiKey= $om_apikey;
               $apiSecret = $om_apisecret;
            }

            if($op_type == "momo"){
                
                $apiKey= $momo_apikey;
                $apiSecret = $momo_apisecret;

            }
            if($op_type == "card"){
              
                $apiKey= $card_apikey;
                $apiSecret = $card_apisecret;
            }
            if($op_type == "paypal"){
                
                $apiKey= $paypal_apikey;
                $apiSecret = $paypal_apisecret;
            }
        //   var_dump($apiKey); var_dump($apiSecret);
           $AccountKey = base64_encode($apiKey.':'.$apiSecret);// ApiKey and Api  
           //var_dump($AccountKey);
            $header = array(
              'content-type: application/json',
              'accountKey:'.$AccountKey,
              'authorization: Bearer '. $token
             ); 
             $data = array(
                 
              'country' => $object["ctry"],
              'type' => $object["method"],
              'amount' => $object["amount"],
              'external_id' => $object["transaction_id"],
              'motif' => $object["reason"],
              'tel' => $object["phone"],
              'op_type' => "credit",  // for cashout debit for payout 
              'email' =>  $object["email"],
              'first_name' => $object["name"],
              'last_name' => $object["lname"],
              'address' => $object["address"],
              'city' => $object["town"],
          //    'failed_url' => $this->failed_url,
          //    'success_url' => $this->success_url,
                'callback_url' => $this->callback_url 
              );
              $table_json = json_encode($data);   
              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_SSL_VERIFYPEER => false,
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $table_json,
              CURLOPT_HTTPHEADER => $header,
              ));
              
                //var_dump($table_json);
              $result = curl_exec($curl);
              if (curl_errno($curl))
              print curl_error($curl);
              else
            
              curl_close($curl);
              return $result;
        
	}
    
    
    
    
    public function checkStatusIwomiPay($transactionID)
	{
            $internal_id = $transactionID;          
            $token = $this->authenticate();
            if($token =="NOK" ){
                $response["status"] ="110";
                $response["message"] ="Invalid token";
                return $response;
            }
            $url = $this->url."iwomipayStatus/".$internal_id;
            $header = array(
             'content-type: application/json',
             'authorization: Bearer '. $token
            );
            $curl = curl_init();
             curl_setopt_array($curl, array(
             CURLOPT_URL => $url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => "",
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_SSL_VERIFYPEER => false,
             CURLOPT_SSL_VERIFYHOST => 0,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "GET",
          //   CURLOPT_POSTFIELDS => $table_json,
             CURLOPT_HTTPHEADER => $header,
             ));
             $result = curl_exec($curl);
             if (curl_errno($curl))
             print curl_error($curl);
             else
             curl_close($curl);
             return $result;
          
        }

    public function paySubscription(){
        
		 $data = $this->get_data();
		 //var_dump($data); die;
		 if(!empty($data)){ 
                         
			 $reason = isset($data->reason)?filter_var($data->reason, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $method = isset($data->method)?filter_var($data->method, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $email  = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $name   = isset($data->name)?filter_var($data->name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $lname  = isset($data->name)?filter_var($data->name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $option = isset($data->option)?filter_var($data->option, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $amount = isset($data->amount)?filter_var($data->amount, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):0;
			 $pack_code   = isset($data->pack_code)?filter_var($data->pack_code, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $ctry   = isset($data->ctry)?filter_var($data->ctry, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $town   = isset($data->town)?filter_var($data->town, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $uname   = isset($data->uname)?filter_var($data->uname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $address   = isset($data->address)?filter_var($data->address, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $phone   = isset($data->phone)?filter_var($data->phone, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $book_ids   = isset($data->book_ids)?filter_var($data->book_ids, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
                         
			 $this->load->model('transaction_model');
			 $pay_ref['transaction_id'] = $this->generateGuid();
			 $pay_ref['internal_id'] = "";
			 $pay_ref['method'] = $method;
			 $pay_ref['amount'] = $amount;
			 $pay_ref['fees'] = "";
			 $pay_ref['reason'] = $reason;
			 $pay_ref['address'] = $address;
			 $pay_ref['dou'] = date('Y-m-d H:i:s');
			 $pay_ref['dmo'] = date('Y-m-d H:i:s');
			 $pay_ref['uti'] = $uname;
			 $pay_ref['utimo'] = $uname;
			 $pay_ref['pack_code'] = $pack_code;
			 $pay_ref['pack_name'] = "";
			 $pay_ref['book_id'] = $book_ids;
			 $pay_ref['email'] = $email;
			 $pay_ref['name'] = $name;
			 $pay_ref['lname'] = $lname;
			 $pay_ref['option'] = $option;
			 $pay_ref['ctry'] = $ctry;
			 $pay_ref['town'] = $town;
			 $pay_ref['phone'] = $phone;
			 $pay_ref['status'] = "1000";
             $this->transaction_model->recordTransactions($pay_ref);
             $return = $this->request_payment($pay_ref);
             $return = (array)json_decode($return);  
                        
             $pay_ref2['status'] = $return["status"] ;
             $pay_ref2['transaction_id'] = $pay_ref["transaction_id"] ;
             if(isset($return["internal_id"])){
                              $pay_ref2['internal_id'] = $return["internal_id"] ;
                         }else{
                             
                              $pay_ref2['internal_id'] = "" ;

                             
                         }
                         
                         $pay_ref2['message'] = $return["message"] ;
                         $return["transaction_id"] = $pay_ref["transaction_id"];
                         $this->transaction_model->updateTransactions2($pay_ref2, $pay_ref["transaction_id"]);
                         
                         if(isset($return["redirectUrl"])){
                             
                              $pay_ref2['redirectUrl'] = $return["redirectUrl"] ;
                              
                         }else{
                             
                              $pay_ref2['redirectUrl'] = "" ;
                         }
                         
                         echo json_encode($pay_ref2);
                         
			 exit;
	 }else{
                        echo json_encode(array("status" => "error","message" => "No data found for this transaction"));
	 }

 }
 
    function randomNumber($length){
        
     $numbers = range(0,9);
     shuffle($numbers);
     $digits ="";
     for($i = 0;$i < $length;$i++){
         $digits .= $numbers[$i];
     }
     return $digits;
     
    }
 
    public function generateGuid(){
        
        
       return "DAP_".date("YmdHis").$this->randomNumber(9);
    }
             
    public function checkStatus(){
        
		 $data = $this->get_data();
		 
		  $this->load->model('transaction_model');
		 //var_dump($data); die;
		 if(!empty($data)){
			 $transaction_id = isset($data->transaction_id)?filter_var($data->transaction_id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
                         
                         $ret = $this->transaction_model->verifyPaymentRefExists3($transaction_id); 
                         if($ret == true ){ 
                             
                           $result2 = $this->transaction_model->getTransaction($transaction_id);
                           if($result2->status != "1000" && $result2->status != "01" && ($result2->internal_id == null || $result2->internal_id == "") ){
                               
                               $result["status"] = $result2->status;
                               $result["message"]= $result2->message;
                               $result["transaction_id"] = $result2->transaction_id;
                               $result["internal_id"] = $result2->internal_id;
                               
                               echo json_encode($result);exit;
 
                            
                           }
                               
                            $result = $this->checkStatusIwomiPay($result2->internal_id); 
                            
                            $result = (array)json_decode($result);  
                            $pay_ref["status"] = $result["status"];
                            $pay_ref["message"] = $result["message"];    
                            
                            if(isset($result["internal_id"])){
                                
                                $pay_ref["internal_id"] = $result["internal_id"];    
                            }
                            
                            $this->transaction_model->updateTransactions2($pay_ref, $transaction_id);    

                            if($pay_ref["status"] === "01"){                                
                                 $result2 = $this->transaction_model->getTransaction($transaction_id);
                                 if($result2->option === "SUBSCRIPTION"){
                                     
                                     $this->updateSubscription($result2->uti, $result2->pack_code, $result2->transaction_id);
                                     
                                 }else{
                                     // METTRE A JOUR LA TABLE DES LIBRAIRIES SI CE N'EST PAS ENCORE LE CAS DONC IL YA UN CONTROLE A FAIRE; 
                                                                          
                                 }
                         
                            }
                            
                         }else{
                             echo json_encode(array("status" => "error","message" => "No data found for this transaction"));
                             exit;
                             
                         }                         
                         /// verifier que les elements de souscriptions ont été mis a jour pour ce code de transaction 
                         //// ainsi que les elements de librairies. 
                         echo json_encode($result); exit;			
		      
	 }else{
	     
		 echo json_encode(array("status" => "error","message" => "No data found for this transaction"));exit;
	 }

 }
      
    public function callback(){
            

		  if(null != file_get_contents('php://input')){
				  $data = (object) json_decode(file_get_contents('php://input'), TRUE);  
	       

		 if(!empty($data)){
                     
			 $external_id = isset($data->external_id)?filter_var($data->external_id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $internal_id = isset($data->internal_id)?filter_var($data->internal_id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $status = isset($data->status)?filter_var($data->status, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $message = isset($data->message)?filter_var($data->message, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
                         $this->load->model('transaction_model');
                         $ret = $this->transaction_model->verifyPaymentRefExists3($external_id);
                         if($ret == true ){
                             
                             
                            $ret = $this->transaction_model->getTransaction($external_id);
                            if($ret->status == "01"){
                                
                                 echo json_encode(array("status" => "01","message" => "Already Updated"));
                                 exit;
                                
                                
                            }

                             
                            $pay_ref['status'] = $status;
                            $pay_ref['message'] = $message;
                            $this->transaction_model->updateTransactions2($pay_ref, $external_id);
                                                          
                         }else{
                             
                             echo json_encode(array("status" => "100","message" => "No data found for this transaction"));
                             exit;
                             
                         }
                                                 
                         
                         //  mettre la souscription a jour expiration de la souscription en fonction du pack
                         // ajouter les elements de librairie de l'utilisateur. 
                         
                         // ensuite allez gerrer les librairies. 
                         
                         
                          if($status == "01"){
                         
                                
                                 $result = $this->transaction_model->getTransaction($external_id);
                                 
                                 if($result->option === "SUBSCRIPTION"){
                                     
                                     $this->updateSubscription($result->uti, $result->pack_code, $result->transaction_id);
                                     
                                 }else{
                                     
                                     // METTRE A JOUR LA TABLE DES LIBRAIRIES; 
                                                                          
                                 }
                         
                         
                         }

                         // revenir sur les save et updates books coté interface. 

                         
			 echo json_encode(array("status" => "01","message" => "successfully updated"));
			 exit;
	 }else{
		 echo json_encode(array("status" => "100","message" => "No data found for this transaction"));
	 }
	 
		  }else{
		      echo json_encode(array("status" => "100","message" => "No data found for this transaction update"));
		  }

 }
             
    public function getTransactionsList(){
		 
        $data = $this->get_data();
        if(!empty($data)){
             $email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
             $page = isset($data->page)?filter_var($data->page, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
             $this->load->model('transaction_model');
             $results = $this->transaction_model->transactionListingAndroidUser($email,$page);
             
	     echo json_encode(array("status" => "ok","transaction_list" => $results, "message" => "successfull"));
             exit;
             
        }else{
            
             echo json_encode(array("status" => "error","message" => "no param found"));
        }

 }
                   
    public function getPackList(){
        
        $data = $this->get_data();
        if(!empty($data)){
            
             $email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
             $this->load->model('transaction_model');
             $results = $this->transaction_model->packListing();
	     echo json_encode(array("status" => "ok","pack_list" => $results, "message" => "successfull"));
             exit;
             
        }else{
            
             echo json_encode(array("status" => "error","message" => "No param found"));
        }

 }
 
    function updateSubscription($email, $pack_code, $transaction_id){
      
            $today = strtotime('today');
            $this->load->model('account_model');
            $uus = $this->account_model->getUserInfo($email);
            if($uus->transaction_id == $transaction_id){
                return array("status" => "ok2","message" =>"Already subscribed");
            }
            $duration = $pack_code;
            $sub_expiry_date = 0;
            $sub_expiry_date = $this->get_expiry($duration);
            $details = array("sub_type"=>1,"subscribed"=>0,"subscribe_plan"=>$duration,"subscribe_expiry_date"=>$sub_expiry_date, "transaction_id"=>$transaction_id);
            $this->account_model->updateUserSubscription($details,$email);
            //$this->coupons_model->deleteCouponWithCode($code);
            return array("status" => "ok","period"=>$duration,"expiry_date"=>$sub_expiry_date,"message" =>"Subscription was successful");
            
    }
    
    function checSubscribtionM(){
         
        $data = $this->get_data();  
        if(!empty($data)){
            
            
            $email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
            
            $today = strtotime('today');
            $this->load->model('account_model');
            $uus = $this->account_model->getUserInfo($email);
            if(isset($uus->email)){
                
                
               // var_dump(strtotime('+1 year'));
                //var_dump(date("Y-m-d H:i:s", 1701523922));
                //var_dump(date("Y-m-d H:i:s", $today));
                if($uus->subscribed == 1 ){
                    echo json_encode( array("status" => "nok2","message" =>"No Subcription found"));exit;
                }

                if($uus->subscribed == 0  &&  (int)$uus->subscribe_expiry_date > $today ){
                    echo json_encode( array("status" => "ok","message" =>"this user has a subscription", "expired_date" =>date("Y-m-d H:i:s", (int)$uus->subscribe_expiry_date)));exit;
                }

                if($uus->subscribed == 0 &&  (int)$uus->subscribe_expiry_date < $today){
                    echo json_encode( array("status" => "nok3","message" =>"Subscription Expired", "expired_date" =>date("Y-m-d H:i:s", (int)$uus->subscribe_expiry_date)));exit;
                }
                
                 echo json_encode( array("status" => "nok4","message" =>"No subscription could be found"));exit;


            }
            echo json_encode(array("status" => "nok4","message" =>"No Account found"));    exit;    
        }
        
        echo json_encode(array("status" => "nok5","message" =>"No data sent")); exit;   

    }
    
    function checSubscribtion($email){
         
        $today = strtotime('today');
        $this->load->model('account_model');
        $uus = $this->account_model->getUserInfo($email);
        if(isset($uus->email)){
            
            if($uus->subscribed == 1 ){
                return array("status" => "nok2","message" =>"No Subcription found");
            }
            
           if($uus->subscribed == 0  &&  (int)$uus->subscribe_expiry_date > $today ){
                    
                return array("status" => "ok","message" =>"this user has subscription");
            }

            if($uus->subscribed == 0 &&  (int)$uus->subscribe_expiry_date < $today){
                
                return array("status" => "nok3","message" =>"Subscription Expired");
            }
        
        }
        return array("status" => "nok4","message" =>"No Account found");                       
    }

    public function get_expiry($duration){

        $expiry = 0;
        switch ($duration) {
         case 'one_week_sub':
                 $sum = strtotime('+1 week');
               //  $expiry = $sum * 1000;
                 break;
         case 'one_month_sub':
                 $sum = strtotime('+1 month');
                 //$expiry = $sum * 1000;
                 break;
         case '3_months_sub':
                 $sum = strtotime('+3 month');
                // $expiry = $sum * 1000;
                break;
        case '6_months_sub':
                        $sum = strtotime('+6 month');
                //        $expiry = $sum * 1000;
                 break;
         case '1_year_sub':
                 $sum = strtotime('+1 year');
               //  $expiry = $sum * 1000;
                 break;
        }
        //return $expiry;
        return $sum;
    }
    
    
		function getTransactionsList2(){
      // Datatables Variables
	    $this->load->model('transaction_model');
        $draw = intval($_POST['draw']);
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
				$columnIndex = $_POST['order'][0]['column']; // Column index
				$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
				$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
				$searchValue="";
				if(isset($_POST['search']['value'])){
					$searchValue = $_POST['search']['value']; // Search value
				}

				$columnName="";
				if(isset($_POST['columns'][$columnIndex]['data'])){
					$columnSortOrder = $_POST['columns'][$columnIndex]['data']; // Search value
				}

        $columnSortOrder = "ASC";
				if(isset($_POST['order'][0]['dir'])){
					$columnSortOrder = $_POST['order'][0]['dir']; // Search value
				}


        $feeds = $this->transaction_model->adminTransactionsListing($columnName,$columnSortOrder,$searchValue,$start, $length);
				$total_feeds = $this->transaction_model->get_total_transactions($searchValue);
        //var_dump($feeds); die;
        $dat = array();

				 $count = $start + 1;
        foreach($feeds as $r) {
					//var_dump($r); die;
          //$title = substr($r->title,0,10 );
          //$content = substr($r->content,0,50 );

             $dat[] = array(
							    $count,
									$r->name ." " . $r->lname,
									$r->transaction_id,
									$r->option,
									$r->pack_name,
									$r->book_id,
									$r->email,
									$r->status,
									$r->message,
									$r->ctry,
									$r->town,
									$r->internal_id,
									$r->dmo
									
             );
						 $count++;
        }

        $output = array(
             "draw" => $draw,
               "recordsTotal" => $total_feeds,
               "recordsFiltered" => $total_feeds,
               "data" => $dat
          );
        echo json_encode($output);
    }
    
    
    
    public function getTransactionsList3(){
	$this->isLoggedIn();
        //$data['book'] = $this->books_model->booksListing_old("", "NOK");
        $this->load->template('transactions/listing', []); // this will load the view file
    }
	
    
     
   

}