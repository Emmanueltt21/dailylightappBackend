<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';

class News extends BaseController {

	public function __construct(){
        parent::__construct();
				//$this->isLoggedIn();
		$this->load->model('news_model');
			$this->load->library('PushNotification'); // Load the custom library
    }

		//rss links methods
   public function newsListing(){ 
       
       $this->isLoggedIn();
       $this->load->template('news/listing', []); // this will load the view file
   }
   
 
   
   	public	function getPrayer_request(){  
   	    
   	    	$data = $this->get_data();
			$results = [];
			$isLastPage = false; 
			if(isset($data->email) && $data->email != null ){
			    
				$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
				$page = 0; 
				if(isset($data->page)){  
	                   $page = $data->page;
	            }
				$results = $this->news_model->getPrayer_request($page,$email);
				$total_items = $this->news_model->get_total_request($email);
				$isLastPage = (($page + 1) * 20) >= $total_items;
		   }else{  
		       
		       		echo json_encode(array("status" => "nok","message" =>"invalid Email adress","isLastPage" => $isLastPage));
		       
		   }

		echo json_encode(array("status" => "ok","prayer_request" => $results,"isLastPage" => $isLastPage));
   	    
   	}
   	
   	public	function getPrayer_requestweb_old(){  
   	    
   	    	$data = $this->get_data();
			$results = [];
			$isLastPage = false; 
            $results = $this->news_model->getPrayer_requestweb();
	     	echo json_encode(array("status" => "ok","prayer_request" => $results,"isLastPage" => $isLastPage));
   	    
   	}
   	
   	
   	public function getPrayer_requestweb(){  
		$this->isLoggedIn();
        $data['request'] = $this->news_model->getPrayer_requestweb(); 
        $this->load->template('prayer_request/listing', $data); // this will load the view file
    }
    
    public function respondPrayerRequest($id = 0)
    {
        $this->isLoggedIn();
        $data['prayer_request'] = $this->news_model->getPrayerRequestInfo($id);
        if(count((array)$data['prayer_request']) == 0)
        {
            $this->session->set_flashdata('error', 'Prayer request not found');
            redirect('getPrayer_requestweb');
        }
        $this->load->template('prayer_request/respond', $data);
    }
    
    public function updatePrayerResponse()
    {
        $this->isLoggedIn();
        $id = $this->input->post('id');
        $response = $this->input->post('response');
        
        if(empty($response)) {
            $this->session->set_flashdata('error', 'Response cannot be empty');
            redirect('respondPrayerRequest/'.$id);
        }
        
        $info = array(
            'response' => $response,
            'utimo' => $this->session->userdata('name'), // admin name
            'dmo' => date('Y-m-d H:i:s') // response date
        );
        
        $this->news_model->updatePrayerRequest($info, $id);
        $this->session->set_flashdata('success', 'Response added successfully');
        redirect('getPrayer_requestweb');
    }
    
    public function deletePrayerRequest()
    {
        $this->isLoggedIn();
        $id = $this->input->post('id');
        
        if(empty($id)) {
            echo json_encode(array("status" => "nok", "message" => "Invalid request"));
            return;
        }
        
        $this->news_model->deletePrayerRequest($id);
        echo json_encode(array("status" => "ok", "message" => "Prayer request deleted successfully"));
    }
    
   
   
   	public	function addPrayer_request(){ 
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				
		   	if(!isset($data->content) ||  !isset($data->email)|| !isset($data->author) || !isset($data->title)){
				   	echo json_encode(array("status" => "nok","message" => "Something went wrong. Please contact the administrators")); exit;
				}
				
				if(!isset($data->email) ||  $data->email==null){
				   	echo json_encode(array("status" => "nok","message" => "please fill your mail adresse "));
                    exit;
				}
				
		    	if(!isset($data->title) ||  $data->title==null){
				   	echo json_encode(array("status" => "nok","message" => "please fill the Object of the message"));
                    exit;
				}
				
				if(!isset($data->author) ||  $data->author==null){
				   	echo json_encode(array("status" => "nok","message" => "please fill your name in the form"));  exit;

				}
				
		    	if(!isset($data->content) ||  $data->content==null || $data->content==''){
				   	echo json_encode(array("status" => "nok","message" => "please fill your name in the form")); exit;
				}
				
				$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
				$title = isset($data->title)?filter_var($data->title, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
				$author = isset($data->author)?filter_var($data->author, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
				$content = isset($data->content)?filter_var($data->content, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";

				
					$info = array(
									'title' => $title,
									'author' => $author,
									'uti' => $email,
									'response' => "" ,
									'r_author' =>  "",
									'content' => $content,
									'dou' =>  date('y-m-d h:i:s') ,
									'dmo' =>  date('y-m-d h:i:s') 
							);
							
			    $results = $this->news_model->addPrayer_request($info);
				echo json_encode(array("status" => "ok","message" => "successfully added"));
		}
   
   //fetch news/news
    	public	function fetch_news(){ 
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
					$results = $this->news_model->fetch_news($type,$page,$email);
					$total_items = $this->news_model->get_total_news($type);
					$isLastPage = (($page + 1) * 20) >= $total_items;
				}

				echo json_encode(array("status" => "ok","news" => $results,"isLastPage" => $isLastPage));
		}
                
                
              
                   
                public function get_news_content(){
                    
                    $data = $this->get_data();
                    $results=[];
                    if(!empty($data)){
                                    $id = isset($data->id)?$data->id:0;
                                    if($data->media_type == "news"){
                                        
                                      $results = $this->news_model->getNewsInfo($id);
                                    }else{
                                        $content ="";
                                    }
                                    
                                    echo json_encode(array("status" => "ok","books" => $results));

                                    //echo json_encode(array("content" => $content));
                     }else{
                                    echo json_encode(array("status" => "ok","mesage" => "Incorrect Data"));
                     }
	       }
                
                

     function getNews(){ 
      // Datatables Variables

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


        $feeds = $this->news_model->adminNewsListing($columnName,$columnSortOrder,$searchValue,$start, $length);
				$total_feeds = $this->news_model->get_total_news($searchValue);
        //var_dump($feeds); die;
        $dat = array();

				 $count = $start + 1;
        foreach($feeds as $r) {
					//var_dump($r); die;
          //$title = substr($r->title,0,10 );
          //$content = substr($r->content,0,50 );

             $dat[] = array(
							    $count,
									$r->dmo,
									$r->author,
							     	$r->title,

									'<div class="btn-group btn-group-sm" style="float: none;">'.
									'<a href="'.site_url().'editNews/'.$r->id.'" type="button" class="tabledit-edit-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="margin-bottom:5px;" class="material-icons list-icon" data-id="'.$r->id.'">create</i></a>'.
									'<button onclick="delete_item(event)" data-type="news" data-id="'.$r->id.'" type="button" class="tabledit-delete-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="color:red;margin-bottom:5px;"  class="material-icons list-icon" data-type="news" data-id="'.$r->id.'">delete</i></button>'.
									'</div>'
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


		public function newNews(){
		  $this->isLoggedIn();
		  $data['author']= $this->session->userdata("fullname");
        $this->load->template('news/new', $data); // this will load the view file
    }

    public function editNews($id=0)
    {
        
        $this->isLoggedIn();

        $data['news'] = $this->news_model->getNewsInfo($id);    // var_dump($data);exit;
        if(count((array)$data['news'])==0)
        {
            redirect('newsListing');
        }
        $this->load->template('news/edit', $data); // this will load the view file
    }



    function saveNewNews()
    {

            $this->load->library('session');
            $this->load->library('form_validation');

          //  $this->form_validation->set_rules('date','News Date','trim|required');
						$this->form_validation->set_rules('title','News Title','trim|required');
						$this->form_validation->set_rules('content','News Content','trim|required');

            if($this->form_validation->run() == FALSE)
            {
			    $this->session->set_flashdata('error', "Some fields were left empty");
                redirect('newNews');
            }else {

						//	$date = $this->input->post('date');
							$date = date('d-m-y h:i:s');
							$title = $this->input->post('title');
							$author =$this->input->post('author');
							//$bible_reading = $this->input->post('bible_reading');
							$content = $this->input->post('content');
							//$studies =$this->input->post('studies');
							//$confession =$this->input->post('confession');
							
						$french_content =  $content ;
                        $german_content = $content ;
         
                         $translatedFrench_content = $this->news_model->translate_content($french_content, 'FR');
                          $translatedGerman_content = $this->news_model->translate_content($german_content, 'DE');
                          
                          
                           $translatedGerman_title = $this->news_model->translate_content($title, 'DE');
                           $translatedFrench_title = $this->news_model->translate_content($title, 'FR');
        

							$info = array(
									'date' => $date,
									'title' => $title,
									'german_title' => $translatedGerman_title,
									'french_title' => $translatedFrench_title,
									'author' => $author,
									'uti' =>  $this->session->userdata ( 'userId' ),
									'utimo' =>  $this->session->userdata ( 'userId' ),
									//'bible_reading' => $bible_reading,
									//'studies' => $studies,
									//'confession' => $confession,
									'content' => $content,
									'french_content' => $translatedFrench_content,
									'german_content' => $translatedGerman_content,
									'dou' =>  date('y-m-d h:i:s') ,
									'dmo' =>  date('y-m-d h:i:s') ,
									'init_date' =>  date('y-m-d h:i:s') 
							);

							if(!empty($_FILES['thumbnail']['name'])){
								$upload = $this->upload_thumbnail();
								if($upload[0]=='ok'){
									$info['thumbnail'] =  $upload[1];
								}
							}

              $this->news_model->addNewNews($info);
						}

							if($this->news_model->status == "ok")
							{
							    
							     /*$result = $this->pushnotification->sendNotification(
                                                        $title, 
                                                        "Detials in App ..."
                                                    );
                                                    
                           if (isset($result->success) && $result->success) {
                                    $this->session->set_flashdata('success', $this->news_model->message . ' Notification sent successfully.');
                                } else {
                                    $this->session->set_flashdata('error', $this->news_model->message . ' (Note: Notification delivery failed)');
                                }*/
                                $this->session->set_flashdata('success', $this->news_model->message . ' News created successfully.');
									
							}
							else
							{
									$this->session->set_flashdata('error', $this->news_model->message);
							}
                redirect('newNews');

    }



    function editNewsData()
    {
			//var_dump($_FILES); die;
			$this->load->library('session');
			$this->load->library('form_validation');
            $id = $this->input->post('id');

		//	$this->form_validation->set_rules('date','News Date','trim|required');
			$this->form_validation->set_rules('title','News Title','trim|required');
			$this->form_validation->set_rules('content','News Content','trim|required');

			if($this->form_validation->run() == FALSE)
			{
					$this->session->set_flashdata('error', "Some fields were left empty");
					redirect('editNews/'.$id);
			}else {

			//	$date = $this->input->post('date');
		//		$date =  date('d-m-y h:i:s');
				$title = $this->input->post('title');
				$author =$this->input->post('author');
		//		$bible_reading = $this->input->post('bible_reading');
				$content = $this->input->post('content');
	//			$studies =$this->input->post('studies');
	//			$confession =$this->input->post('confession');
	
            	$french_content =$this->input->post('french_content');
				$german_content =$this->input->post('german_content');
				
				$french_title = $this->input->post('french_title');
				$german_title = $this->input->post('german_title');

				$info = array(
			//			'date' => $date,
						'title' => $title,
						'french_title' => $french_title,
						'german_title' => $german_title,
						'author' => $author,
						'utimo' =>  $this->session->userdata ( 'userId' ),
				     	'dmo' =>  date('y-m-d h:i:s') ,

	//					'bible_reading' => $bible_reading,
	//					'studies' => $studies,
	//					'confession' => $confession,
						'content' => $content,
						'french_content' => $french_content,
						'german_content' => $german_content
				);

				if(!empty($_FILES['thumbnail']['name'])){
					$upload = $this->upload_thumbnail();
					if($upload[0]=='ok'){
						$info['thumbnail'] =  $upload[1];
					}
				}

				$this->news_model->editNews($info,$id);
			}

				if($this->news_model->status == "ok")
				{
						/* $result = $this->pushnotification->sendNotification(
                                                        $title, 
                                                        "Detials in App ..."
                                                    );
                                                    
                           if (isset($result->success) && $result->success) {
                                    $this->session->set_flashdata('success', $this->news_model->message . ' Notification sent successfully.');
                                } else {
                                    $this->session->set_flashdata('error', $this->news_model->message . ' (Note: Notification delivery failed)');
                                }*/
                                  $this->session->set_flashdata('success', $this->news_model->message . ' News created successfully.');
				}
				else
				{
						$this->session->set_flashdata('error', $this->news_model->message);
				}

			redirect('editNews/'.$id);
    }

 

    function deleteNews($id=0)
    {
      $this->load->library('session');
      $this->news_model->deleteNews($id);
      if($this->news_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->news_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->news_model->message);
      }
      redirect('newsListing');
    }

		public function upload_thumbnail(){
			$path = $_FILES['thumbnail']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_name = time().".".$ext;

			$config['file_name'] = $new_name;
			$config['upload_path']          = './uploads/thumbnails';
			$config['max_size']             = 10000;
			$config['allowed_types']        = 'jpg|png|jpeg|PNG';
			$config['overwrite'] = TRUE; //overwrite thumbnail


			//var_dump($config);

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('thumbnail'))
			{
					//$error = array('error' => $this->upload->display_errors());
					return ['error',strip_tags($this->upload->display_errors())];
			}
			else{
					$image_data = $this->upload->data();
					return ['ok',$new_name];
			}
		}
                
                
                
                
                
		

}
