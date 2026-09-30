<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';
/*
* This class handles some of the requests from the android client app
*/
class Api extends BaseController {

	public function __construct()
    {
        parent::__construct();
				$this->check_headers();
				   $this->load->library([  'Paypal_lib']);
    }

		function test_email(){
			$this->sendMail("envisionaps@gmail.com","test email","Hello ");
		}

    //discover media
		function discover(){
			  $data = $this->get_data();
				$this->load->model('inbox_model');
				$this->load->model('livestreams_model');
				$this->load->model('radio_model');
				$this->load->model('events_model');
				$this->load->model('settings_model');
				$this->load->model('media_model');

				//$last_seen_event = isset($data->last_seen_event)?filter_var($data->last_seen_event, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):0;
				$last_seen_inbox = isset($data->last_seen_inbox)?filter_var($data->last_seen_inbox, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):0;
				$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";

				$livestreams = $this->livestreams_model->getLiveStreams();
				$radios = $this->radio_model->getRadio();
				$facebook_page = $this->settings_model->getFacebookPage();
				$youtube_page = $this->settings_model->getYoutubePage();
				$twitter_page = $this->settings_model->getTwitterPage();
				$instagram_page = $this->settings_model->getInstagramPage();
				$ads_interval = $this->settings_model->getAdvertsInterval();
				$events = $this->events_model->get_total_events(date("Y-m-d"));
				$inbox = $this->inbox_model->get_total_inbox($last_seen_inbox);

				$website_url = $this->settings_model->getWebsiteUrl();
				$image_one = $this->settings_model->getHomePageImage("image_one");
				$image_two = $this->settings_model->getHomePageImage("image_two");
				$image_three = $this->settings_model->getHomePageImage("image_three");
				$image_four = $this->settings_model->getHomePageImage("image_four");
				$image_five = $this->settings_model->getHomePageImage("image_five");
				$image_six = $this->settings_model->getHomePageImage("image_six");
				$image_seven = $this->settings_model->getHomePageImage("image_seven");
				$image_eight = $this->settings_model->getHomePageImage("image_eight");
				$slider_media = $this->media_model->fetchRandom($email);

				echo json_encode(array("status" => "ok"
				,"slider_media" => $slider_media
				,"livestream" => $livestreams
				,"facebook_page" => $facebook_page
				,"youtube_page" => $youtube_page
				,"twitter_page" => $twitter_page
				,"instagram_page" => $instagram_page
				,"ads_interval" => $ads_interval
				,"inbox" => $inbox
				,"website_url" => $website_url
				,"image_one" => $image_one
				,"image_two" => $image_two
				,"image_three" => $image_three
				,"image_four" => $image_four
				,"image_five" => $image_five
				,"image_six" => $image_six
				,"image_seven" => $image_seven
				,"image_eight" => $image_eight
				,"events" => $events
				,"radios" => $radios));
		}

		//categories listing
		function devotionals(){
			$data = $this->get_data();
		  $this->load->model('devotionals_model');
		  $date = isset($data->date)?filter_var($data->date, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):date("Y-m-d");
			$devotional = $this->devotionals_model->getDevotional(date('Y-m-d', strtotime($date)));
			if($devotional){
				echo json_encode(array("status" => "ok","devotional" => $devotional));
			}else{
				echo json_encode(array("status" => "error"));
			}
		}

		//fetch radios
		function fetch_radios(){
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				$page = 0;
				if(isset($data->page)){
					$page = $data->page;
				}
				$this->load->model('radio_model');
				$results = $this->radio_model->fetchRadio($page);
				$total_items = $this->radio_model->get_total_radio();
				$isLastPage = (($page + 1) * 20) >= $total_items;
				echo json_encode(array("status" => "ok","radios" => $results,"isLastPage" => $isLastPage));
		}

		//fetch albums
		function fetch_events(){
			   $data = $this->get_data();
			   $date = isset($data->date)?filter_var($data->date, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):date("Y-m-d");
				$this->load->model('events_model');
				$results = $this->events_model->fetchEvents(date('Y-m-d', strtotime($date)));
				echo json_encode(array("status" => "ok","events" => $results));
		}

		//categories listing
		function categories(){
			  $data = $this->get_data();
				$this->load->model('categories_model');
				$categories = $this->categories_model->categoriesListing();
				echo json_encode(array("status" => "ok","categories" => $categories));
		}

		//fetch audios/videos
		function fetch_media(){
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				if(isset($data->media_type)){
					$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$type = $data->media_type;
					$page = 0;
					if(isset($data->page)){
	          $page = $data->page;
	        }

					$this->load->model('media_model');
					$results = $this->media_model->fetch_media($type,$page,$email);
					$total_items = $this->media_model->get_total_media($type);
					$isLastPage = (($page + 1) * 20) >= $total_items;
				}

				echo json_encode(array("status" => "ok","media" => $results,"isLastPage" => $isLastPage));
		}

		//fetch audios/videos
		function fetch_hymns(){
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				$query = isset($data->query)?filter_var($data->query, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
				$page = 0;
				if(isset($data->page)){
					$page = $data->page;
				}

				$this->load->model('hymns_model');
				$results = $this->hymns_model->fetch_hymns($page,$query);
				$total_items = $this->hymns_model->get_total_hymns($query);
				$isLastPage = (($page + 1) * 20) >= $total_items;

				echo json_encode(array("status" => "ok","hymns" => $results,"isLastPage" => $isLastPage));
		}

		//fetch inbox
		function fetch_inbox(){
			$data = $this->get_data();
			$results = [];
			$isLastPage = false;
			$page = 0;
			if(isset($data->page)){
				$page = $data->page;
			}
			$this->load->model('inbox_model');
			$results = $this->inbox_model->fetchInbox($page);
			$total_items = $this->inbox_model->get_total_inbox();
			$isLastPage = (($page + 1) * 20) >= $total_items;
			echo json_encode(array("status" => "ok","isLastPage" => $isLastPage,"inbox" => $results));
		}

		//fetch categories audios/videos
		function fetch_categories_media(){
				$data = $this->get_data();
				$results = [];
				$subcategories = [];
				$isLastPage = false;
				if(isset($data->category)){
					$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$category = $data->category;
					$version = isset($data->version)?$data->version:"v1";
					$page = 0;
					if(isset($data->page)){
	          $page = $data->page;
	        }
					$sub = 0;
					if(isset($data->sub)){
	          $sub = $data->sub;
	        }
					$media_type = "all";
					if(isset($data->media_type)){
	          $media_type = $data->media_type;
	        }
					$this->load->model('media_model');
					$results = $this->media_model->fetch_categories_media($category,$page,$email,$sub,$media_type);
					$total_items = $this->media_model->total_categories_media($category,$sub,$media_type);
					$isLastPage = (($page + 1) * 20) >= $total_items;

					if($page==0){
						$this->load->model('categories_model');
						$subcategories = $this->categories_model->subcategoriesListing($category);
					}
				}
				echo json_encode(array("status" => "ok","subcategories" => $subcategories,"isLastPage" => $isLastPage,"media" => $results));
		}


		//fetch categories audios/videos
		function getTrendingMedia(){
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
				$version = isset($data->version)?$data->version:"v1";
				$page = 0;
				if(isset($data->page)){
					$page = $data->page;
				}

				$this->load->model('media_model');
				$results = $this->media_model->getTrendingMedia($page,$email,"",$version);
				$total_items = $this->media_model->total_trending_media($version);
				$isLastPage = (($page + 1) * 20) >= $total_items;

				echo json_encode(array("status" => "ok","isLastPage" => $isLastPage,"media" => $results));
		}

		//process user like or unlike media
				public function update_media_total_views(){
					$data = $this->get_data();
					$this->load->model('media_model');
					if(!empty($data)){
						  $media = isset($data->media)?filter_var($data->media, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";

						  if($media !=""){
								 $this->media_model->update_media_total_views($media);
						  }
					 }
					 echo json_encode(array("status" => $this->media_model->status));
				}

				//process user like or unlike media
						public function update_ebooks_articles_views(){
							$data = $this->get_data();
							$id = isset($data->id)?filter_var($data->id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
							$type = isset($data->type)?filter_var($data->type, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
							if($type=="ebooks"){
								 	$this->load->model('ebooks_model');
								 $this->ebooks_model->update_ebooks_total_views($id);
							}else if($type=="articles"){
								 	$this->load->model('articles_model');
								 $this->articles_model->update_articles_total_views($id);
							}
							 echo json_encode(array("status" => "ok"));
						}

//process user like or unlike media
		public function likeunlikemedia(){
			$data = $this->get_data();
			$this->load->model('media_model');
			if(!empty($data)){
				  $email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
				  $media = isset($data->media)?filter_var($data->media, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
					$action = isset($data->action)?filter_var($data->action, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";

				  if($email!="" && $media !=""){
						 $this->media_model->likeunlikemedia($media,$email,$action);
				  }
			 }
			 echo json_encode(array("status" => $this->media_model->status,"message" => $this->media_model->message));
		}

//get total likes and comments for a media
		public function getmediatotallikesandcommentsviews(){
			$data = $this->get_data();
			$this->load->model('media_model');
			$total_likes = 0;
			$total_comments = 0;
			if(!empty($data)){
				  $media = isset($data->media)?filter_var($data->media, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";

				  if($media !=""){
						 $total_comments = $this->media_model->get_total_comments($media);
						 $total_likes = $this->media_model->getMediaTotalLikes($media);
						 $total_views = $this->media_model->getMediaTotalViews($media);
				  }
			 }
			 echo json_encode(array("status" => 'ok'
			 ,"total_likes" => $total_likes
			 ,"total_comments" => $total_comments
		   ,"total_views" => $total_views));
		}

    //search audios/videos
		function search(){
				$data = $this->get_data();
				$result = [];
				if(isset($data->query)){
					$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$query = $data->query;

					$offset = 0;
					if(isset($data->offset)){
	          $offset = $data->offset;
	        }
					$this->load->model('search_model');
					$result = $this->search_model->searchListing($query,$offset,$email);
				}
				echo json_encode(array("status" => "ok","search" => $result));
		}

		//download media
		function download(){
			$this->load->model('download_model');
			if(isset($_GET['m'])){
				$this->download_model->load($_GET['m']);
			}else{
				echo "invalid url";
			}
		}

	//store user fcm token
	function storeFcmToken(){
			$data = $this->get_data();
			$this->load->model('fcm_model');
			if(isset($data->token) && $data->token!=""){
				$token = $data->token;
				$version = isset($data->version)?$data->version:"v1";
				$data = array("token"=>$token,"app_version"=>$version);
			  $this->fcm_model->storeUserFcmToken($data);
			}
			echo json_encode(array("status" => $this->fcm_model->status
			,"msg" => $this->fcm_model->message));
	}

	//store user fcm token
	function updateFcmToken(){
			$data = $this->get_data();
			$this->load->model('fcm_model');
			if(isset($data->token) && $data->token!=""){
				$token = $data->token;
				$version = isset($data->token)?$data->token:"v1";
			  $this->fcm_model->updateUserFcmToken($token,$version);
			}
			echo json_encode(array("status" => $this->fcm_model->status
			,"msg" => $this->fcm_model->message));
	}

	function send_feedback(){
		$data = $this->get_data();
			if(!empty($data)){
				  $name = isset($data->name)?filter_var($data->name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
					$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
					$phone = isset($data->phone)?filter_var($data->phone, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
				    $message = isset($data->message)?filter_var($data->message, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";

					//check for empty or invalid fields
					$_name_error = $name==""?"Name is empty!":"";
					$_email_error = $this->validateEmail($email)==TRUE?"":"Email Address Is not valid!";
					$_password_error = $message==""?"Message is empty!":"";
					if($_name_error !="" || $_email_error !="" || $_password_error != ""){
						 $this->response("error",$_name_error."\n".$_email_error."\n".$_password_error);
                         exit;
					}
					$subject = "App Feedback";
						 $htmlContent = '<p>From '.$name.',</p>';
				         $htmlContent = '<p>Email '.$email.',</p>';
				         $phone = '<p>From '.$phone.',</p>';
						 $htmlContent .= '<br><br>';
						 $htmlContent .= '<p>'.$message.'</p>';
						 $this->sendMail($email,$subject,$htmlContent);
			 }
			 $this->response("ok","Thank your feedback, We will attend to it shortly");
	}

	public function get_article_content(){
		$data = $this->get_data();
		if(!empty($data)){
				$id = isset($data->id)?$data->id:0;
				if($data->type == "inbox"){
					$this->load->model('inbox_model');
					$content = $this->inbox_model->getArticleContent($id);
				}else{
					$this->load->model('events_model');
					$content = $this->events_model->getArticleContent($id);
				}
				echo json_encode(array("content" => $content));
		 }else{
			 echo json_encode(array("content" => ""));
		 }
	}

	public function saveDonation(){
		 $data = $this->get_data();
		 //var_dump($data); die;
		 if(!empty($data)){
			 $reason = isset($data->reason)?filter_var($data->reason, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $method = isset($data->method)?filter_var($data->method, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $name = isset($data->name)?filter_var($data->name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
			 $amount = isset($data->amount)?filter_var($data->amount, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):0;
			 $reference = isset($data->reference)?filter_var($data->reference, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";

			 $this->load->model('donations_model');
			 $pay_ref['email'] = $email;
			 $pay_ref['name'] = $name;
			 $pay_ref['reason'] = $reason;
			 $pay_ref['reference'] = $reference;
			 $pay_ref['amount'] = $amount;
			 $pay_ref['method'] = $method;
				$this->donations_model->recordDonation($pay_ref);


			 echo json_encode(array("status" => $this->donations_model->status,"message" => $this->donations_model->message));
			 exit;
	 }else{
		 echo json_encode(array("status" => "error","message" => "No data found for this transaction"));
	 }

 }
 
 
  public function get_paypal_linkv1()
    {
        /*   test
            user_id : 2
            order_id : 1
            amount : 150
        */
        $data = $this->get_data();
             $user_id = isset($data->user_id)?filter_var($data->user_id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
             $order_id = isset($data->order_id)?filter_var($data->order_id, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
              $amount = isset($data->amount)?filter_var($data->amount, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
              $optype = isset($data->optype)?filter_var($data->optype, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"";
       
            $this->response['error'] = false;
                $this->response['message'] = 'Order created for wallet!';
                 $this->response['order_id'] = $user_id;
                 $this->response['amount'] = $amount;
                  $this->response['order_id'] = base_url() . 'api/app_payment_status1';
                $this->response['data'] = 'api/paypal_transaction_webviewv1?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount. '&optype=' . $optype;
                
                 $this->response['url_pay'] = base_url('api/paypal_transaction_webviewv1?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount. '&optype=' . $optype);
          
        print_r(json_encode($this->response));
    }
    
      //paypal_transaction_webview()
    public function paypal_transaction_webviewv1()
    {
        /*
            user_id : 2
            order_id : 1
        */

        header("Content-Type: html");
        
          $user_id = $_GET['user_id'];
        $order_id = $_GET['order_id'];
        $amount = $_GET['amount'];
        $optype = $_GET['optype'];
         
       $data['user'] = $user_id;
            $data['payment_type'] = "paypal";
            // Set variables for paypal form
            $returnURL ='https://app.yourdailylight.org/dailylight/api/app_payment_status1';
            $cancelURL = 'https://app.yourdailylight.org/dailylight/api/app_payment_status1';
            $notifyURL =  'https://app.yourdailylight.org/dailylight/api/ipnx';
            $txn_id = time() . "-" . rand();
            // Get current user ID from the session
            $userID = $user_id;
            $order_id = $order_id;
            $payeremail = "jacksoncman19@gmail.com";

            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', 'Online shopping');
            $this->paypal_lib->add_field('custom', $userID . '|' . $payeremail);
            $this->paypal_lib->add_field('item_number', $order_id);
            $this->paypal_lib->add_field('amount', $amount);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
    }
    
    
     
   public function app_payment_status1()
    {
        $payment_status = $this->input->post('payment_status');
        $paypalInfo = $this->input->post();
        
        $body_data =  $this->input->post('data');
        
      //  $payment_status =  $data->data->payment_status;
        
        $payer_email =  $paypalInfo->data->payer_email;
        $payer_id =  $paypalInfo->data->payer_id;
        $txn_id =  $paypalInfo->data->txn_id;
        
                 $appstatusurl = "https://app.yourdailylight.org/dailylight/app_payment_status.php?" . 'payment_status=' . $payment_status . '&payer_email=' . $payer_email . '&payer_id=' . $payer_id . '&txn_id=' . $txn_id;

        
        
        if (!empty($paypalInfo) && isset($payment_status) && strtolower($payment_status) == "completed") {
            
            $response['error'] = false;
            $response['message'] = "Payment Completed Successfully";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($payment_status) && strtolower($payment_status) == "authorized") {
            $response['error'] = false;
            $response['message'] = "Your payment is has been Authorized successfully. We will capture your transaction within 30 minutes, once we process your order. After successful capture coins wil be credited automatically.";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($payment_status) && strtolower($payment_status) == "Pending") {
            $response['error'] = false;
            $response['message'] = "Your payment is pending and is under process. We will notify you once the status is updated.";
            $response['data'] = $paypalInfo;
        } else {

            $response['error'] = true;
            $response['message'] = "Payment Cancelled / Declined ";
            $response['data'] = (isset($_GET)) ? $this->input->post() : "";
        }
        
        header('Location: '. $appstatusurl); 
        exit();
        //print_r(json_encode($response));
    }

 public function ipnx()
    {
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
        if (!empty($paypalInfo)) {
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

            // Check whether the transaction is valid
            if ($ipnCheck) {

                $order_id = $paypalInfo["item_number"];
                /* if its not numeric then it is for the wallet recharge */
                
                //echo paypalInfo;
                 echo "This is a Test Payments ";
                
                if (
                    $paypalInfo["payment_status"] == 'Completed' 
                ) {
                    
                    $amount = $paypalInfo["mc_gross"];
                    /* IPN for user wallet recharge */
                    $data['transaction_type'] = "wallet";
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['type'] = "credit";
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $amount;
                    $data['status'] = "success";
                   
                    /* IPN for normal Order  */
                    // Insert the transaction data in the database
                    $userData = explode('|', $paypalInfo['custom']);

                    $data['transaction_type'] = 'Transaction';
                    $data['user_id'] = $userData[0];
                    $data['payer_email']  = $userData[1];
                    $data['order_id'] = $paypalInfo["item_number"];
                    $data['type'] = 'paypal';
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $paypalInfo["mc_gross"];
                    $data['currency_code'] = $paypalInfo["mc_currency"];
                    $data['status'] = 'success';
                    $data['message'] = 'Payment Verified';
                    
                    
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
			 $pay_ref['status'] = "01";
             $this->transaction_model->recordTransactions($pay_ref);
             
                }
                      else {
                        /* if transaction wasn't completed successfully then cancel the order and transaction */
                        $data['transaction_type'] = 'Transaction';
                        $data['user_id'] = $userData[0];
                        $data['payer_email']  = $userData[1];
                        $data['order_id'] = $paypalInfo["item_number"];
                        $data['type'] = 'paypal';
                        $data['txn_id'] = $paypalInfo["txn_id"];
                        $data['amount'] = $paypalInfo["mc_gross"];
                        $data['currency_code'] = $paypalInfo["mc_currency"];
                        $data['status'] = $paypalInfo["payment_status"];
                        $data['message'] = 'Payment could not be completed due to one or more reasons!';
                        
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
             
             
                    }
                }
            }
        }
    

 public function iniPayment()
    {
        /*   test
            user_id : 2
            order_id : 1
            amount : 150
        */

            $user_id = $_POST['user_id'];
            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
        
            if (!is_numeric($order_id)) {
                $this->response['error'] = false;
                $this->response['message'] = 'Transaction created Successfully';
                $this->response['data'] = "Response object";
                print_r(json_encode($this->response));
                return false;
            }else {
            $this->response['error'] = true;
            $this->response['message'] = 'Unable to Create Request ';
            $this->response['data'] = "Response Object";
            }
           
        
        print_r(json_encode($this->response));
    }
    
    public function transtatusUpdate(){
        
        $user_id = $_GET['user_id'];
        $order_id = $_GET['order_id'];
        $amount = $_GET['amount'];
    
         $paypalInfo = $this->input->get();
        if (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "completed") {
            $response['error'] = false;
            $response['message'] = "Payment Completed Successfully";
            $response['data'] = $paypalInfo;
        }  else {
            $response['error'] = true;
            $response['message'] = "Payment Failed ";
            $response['data'] = (isset($_GET)) ? $this->input->get() : "";
        }
        
        print_r(json_encode($response));
        
    }
    
    
 
     public function get_paypal_link()
    {
        /*   test
            user_id : 2
            order_id : 1
            amount : 150
        */

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
        } else {
            $user_id = $_POST['user_id'];
            $order_id = $_POST['order_id'];
            $amount = $_POST['amount'];
            if (!is_numeric($order_id)) {
                $this->response['error'] = false;
                $this->response['message'] = 'Order created for wallet!';
                $this->response['data'] = base_url('api/paypal_transaction_webview?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount);
                print_r(json_encode($this->response));
                return false;
            }
            $this->response['error'] = false;
            $this->response['message'] = 'Order Detail Founded !';
            $this->response['data'] = base_url('api/paypal_transaction_webview?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount);

            /*
            $orderData = fetch_details(['id' => $order_id, 'user_id' => $user_id], 'orders');

            if (empty($orderData)) {
                $this->response['error'] = true;
                $this->response['message'] = 'No Order Detail Founded!';
                $this->response['data'] = array();
            } else {
                $this->response['error'] = false;
                $this->response['message'] = 'Order Detail Founded !';
                $this->response['data'] = base_url('app/v1/api/paypal_transaction_webview?' . 'user_id=' . $user_id . '&order_id=' . $order_id . '&amount=' . $amount);
            }
            */
        }
        print_r(json_encode($this->response));
    }

    //paypal_transaction_webview()
    public function paypal_transaction_webview()
    {
        /*
            user_id : 2
            order_id : 1
        */

        header("Content-Type: html");

        $this->form_validation->set_data($_GET);

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('order_id', 'Order ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|xss_clean');
        if (!$this->form_validation->run()) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }

        $user_id = $_GET['user_id'];
        $order_id = $_GET['order_id'];
        $amount = $_GET['amount'];

        $q = $this->db->where('id', $user_id)->get('users')->result_array();
        if (empty($q) && !isset($q)) {
            echo "user error update";
            return false;
        }

        $order_res = $this->db->where('id', $order_id)->get('orders')->result_array();
        if (!empty($order_res)) {

            $data['user'] = $q[0];
            $data['order'] = $order_res[0];
            $data['payment_type'] = "paypal";
            // Set variables for paypal form
                       $returnURL ='https://app.yourdailylight.org/dailylight/api/app_payment_status';
            $cancelURL = 'https://app.yourdailylight.org/dailylight/api/app_payment_status';
            $notifyURL =  'https://app.yourdailylight.org/dailylight/api/ipn';
            $txn_id = time() . "-" . rand();
            // Get current user ID from the session
            $userID = $data['user']['id'];
            $order_id = $data['order']['id'];
            $payeremail = $data['user']['email'];
            // $userID = $data['user']->id;
            // Add fields to paypal form
            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', 'Test');
            $this->paypal_lib->add_field('custom', $userID . '|' . $payeremail);
            $this->paypal_lib->add_field('item_number', $order_id);
            $this->paypal_lib->add_field('amount', $amount);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
        } else {
            $data['user'] = $q[0];
            $data['payment_type'] = "paypal";
            // Set variables for paypal form
            $returnURL ='https://app.yourdailylight.org/dailylight/api/app_payment_status';
            $cancelURL = 'https://app.yourdailylight.org/dailylight/api/app_payment_status';
            $notifyURL =  'https://app.yourdailylight.org/dailylight/api/ipn';
            $txn_id = time() . "-" . rand();
            // Get current user ID from the session
            $userID = $data['user']['id'];
            $order_id = $order_id;
            $payeremail = $data['user']['email'];

            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', 'Online shopping');
            $this->paypal_lib->add_field('custom', $userID . '|' . $payeremail);
            $this->paypal_lib->add_field('item_number', $order_id);
            $this->paypal_lib->add_field('amount', $amount);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
        }
    }

    public function app_payment_status()
    {
        $paypalInfo = $this->input->get();

        if (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "completed") {
            $response['error'] = false;
            $response['message'] = "Payment Completed Successfully";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "authorized") {
            $response['error'] = false;
            $response['message'] = "Your payment is has been Authorized successfully. We will capture your transaction within 30 minutes, once we process your order. After successful capture coins wil be credited automatically.";
            $response['data'] = $paypalInfo;
        } elseif (!empty($paypalInfo) && isset($_GET['st']) && strtolower($_GET['st']) == "Pending") {
            $response['error'] = false;
            $response['message'] = "Your payment is pending and is under process. We will notify you once the status is updated.";
            $response['data'] = $paypalInfo;
        } else {
            $response['error'] = true;
            $response['message'] = "Payment Cancelled / Declined ";
            $response['data'] = (isset($_GET)) ? $this->input->get() : "";
        }
        print_r(json_encode($response));
    }

    public function ipn()
    {
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
        if (!empty($paypalInfo)) {
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

            // Check whether the transaction is valid
            if ($ipnCheck) {

                $order_id = $paypalInfo["item_number"];
                /* if its not numeric then it is for the wallet recharge */
                
                echo paypalInfo;
                
                if (
                    $paypalInfo["payment_status"] == 'Completed' 
                ) {
                    
                    $amount = $paypalInfo["mc_gross"];
                    /* IPN for user wallet recharge */
                    $data['transaction_type'] = "wallet";
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['type'] = "credit";
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $amount;
                    $data['status'] = "success";
                   
                    /* IPN for normal Order  */
                    // Insert the transaction data in the database
                    $userData = explode('|', $paypalInfo['custom']);

                    $data['transaction_type'] = 'Transaction';
                    $data['user_id'] = $userData[0];
                    $data['payer_email']  = $userData[1];
                    $data['order_id'] = $paypalInfo["item_number"];
                    $data['type'] = 'paypal';
                    $data['txn_id'] = $paypalInfo["txn_id"];
                    $data['amount'] = $paypalInfo["mc_gross"];
                    $data['currency_code'] = $paypalInfo["mc_currency"];
                    $data['status'] = 'success';
                    $data['message'] = 'Payment Verified';
                    
                    
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
			 $pay_ref['status'] = "01";
             $this->transaction_model->recordTransactions($pay_ref);
             
                }
                      else {
                        /* if transaction wasn't completed successfully then cancel the order and transaction */
                        $data['transaction_type'] = 'Transaction';
                        $data['user_id'] = $userData[0];
                        $data['payer_email']  = $userData[1];
                        $data['order_id'] = $paypalInfo["item_number"];
                        $data['type'] = 'paypal';
                        $data['txn_id'] = $paypalInfo["txn_id"];
                        $data['amount'] = $paypalInfo["mc_gross"];
                        $data['currency_code'] = $paypalInfo["mc_currency"];
                        $data['status'] = $paypalInfo["payment_status"];
                        $data['message'] = 'Payment could not be completed due to one or more reasons!';
                        
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
             
             
                    }
                }
            }
        }
    


    public function generateGuid(){
        
        
       return "DAP_".date("YmdHis").$this->randomNumber(9);
    }
     




}
