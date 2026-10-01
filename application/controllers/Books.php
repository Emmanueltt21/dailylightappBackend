<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';
/*
* This class handles some of the requests from the android client app
*/
class Books extends BaseController {

	public function __construct()
    {
        parent::__construct();
	$this->load->model('books_model');
	
	
    }
    
    	function fetch(){
      // Datatables Variables
      	$this->load->model('books_model');

			$draw = intval($_POST['draw']);
			$start = intval($_POST['start']);
			$length = intval($_POST['length']);
			$columnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0; // Column index
			$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : ''; // Column name
			$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC'; // asc or desc
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

			$audios = $this->books_model->booksListing($columnName,$columnSortOrder,$searchValue,$start, $length); 
	    //	$audios = $this->books_model->bookListing($columnName,$columnSortOrder,$searchValue,$start, $length);
			$total_audios = $this->books_model->get_total_books();
		//	var_dump($total_audios); die;
			$dat = array();

			 $count = $start + 1;
        foreach($audios as $r) {
             $dat[] = array(
							    $count,//'.site_url()."stream?m=".$r->id.'
							    
							        '<a href="'.$r->url.'" >'.
							        '<img src="'.$r->thumbnail.'" style="height:50px; width:50px;"></img></a>',
									$r->b_title,
                                    $r->b_desc,
									'<a href="'.site_url().'editBook/'.$r->id.'" type="button" class="btn btn-primary btn-sm m-l-15 waves-effect" style="float: none;">'.
									'<i style="margin-bottom:5px;" class="material-icons list-icon" data-id="'.$r->id.'">create</i></a>'.
									'<button onclick="delete_item(event)" data-type="books" data-id="'.$r->id.'" type="button" class="btn btn-danger btn-sm m-l-15 waves-effect" style="float: none;">'.
									'<i style="margin-bottom:5px;"  class="material-icons list-icon" data-type="books" data-id="'.$r->id.'">delete</i></button>'
             );
						 $count++;
        }

        $output = array(
             "draw" => $draw,
               "recordsTotal" => $total_audios,
               "recordsFiltered" => $total_audios,
               "data" => $dat
          );
        echo json_encode($output);
    }
    
    
                
                //fetch trending books
		function getTrendingbooks(){
				$data = $this->get_data();     
				$results = [];
				$isLastPage = false;
				if(isset($data->media_type) && $data->media_type ="book"){
					//$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$type = $data->media_type;
                                        
					$page = 0; 
					if(isset($data->page)){
                                        $page = $data->page;
                                        }           
					$results = $this->books_model->getTrendingbooks($type,$page);  
					$total_items = $this->books_model->get_total_books();
					$isLastPage = (($page + 1) * 20) >= $total_items;
                                }else{
                                    
                                   echo json_encode(array("status" => "nok","message" =>"Type not supported"));
                                    
                                }

				echo json_encode(array("status" => "ok","books" => $results,"isLastPage" => $isLastPage));
		}
    
                
                   //fetch Books
                
                function update_books_total_views(){
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				if(isset($data->book_id) && $data->media_type ="book"){
					//$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$type = $data->media_type;
				     
					$results = $this->books_model->update_books_total_views($data->book_id);
                                }else{
                                    
                                   echo json_encode(array("status" => "nok","message" =>"Type not supported"));
                                    
                                }

				echo json_encode(array("status" => "ok","message" => "successfully updated"));
		}
                
		function booksListing(){
		    
				$data = $this->get_data();
				$results = [];
				$isLastPage = false;
				if(isset($data->media_type) && $data->media_type ="book"){
				    
					//$email = isset($data->email)?filter_var($data->email, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH):"null";
					$type = $data->media_type;
					$page = 0;
					if(isset($data->page)){
                         $page = $data->page;
                    }           
					$results = $this->books_model->booksListing_old($type,$page);
					$total_items = $this->books_model->get_total_books();
					$isLastPage = (($page + 1) * 20) >= $total_items;
					
                }else{
                                    
                    echo json_encode(array("status" => "nok","message" =>"Type not supported"));
                                    
                }

				echo json_encode(array("status" => "ok","books" => $results,"isLastPage" => $isLastPage));
		}
                
                
                   
                public function get_book_details(){
                    
                    $data = $this->get_data();
                    if(!empty($data)){
                          $id = isset($data->id)?$data->id:0;
                          $results = $this->books_model->getbookInfo($id);
                          echo json_encode(array("status" => "ok","books" => $results));
                     }else{
                          echo json_encode(array("status" => "nok","message" => "An error occured during the process"));
                     }
                     
	       }
                
                
            

    public function index(){
	$this->isLoggedIn();
        $data['book'] = $this->books_model->booksListing_old("", "NOK");
        $this->load->template('books/listing', $data); // this will load the view file
    }
       public function newBooks()
    {
	    $this->isLoggedIn();
        $this->load->model('books_cat_model');
	    $data['categories'] = $this->books_cat_model->categoriesListing(); // var_dump($data);exit;
        $this->load->template('books/new',$data); // this will load the view file
    }

    public function editBook($id=0)
    {
     	$this->isLoggedIn();
        $data['book'] = $this->books_model->getbookInfo($id);
        if(count((array)$data['book'])==0)
        {
            redirect('booksListing');
        } 
        $this->load->model('books_cat_model');
        $data['categories'] = $this->books_cat_model->categoriesListing();   //var_dump($data );
        $this->load->template('books/edit', $data); // this will load the view file
    }
    
     public function savenewBook(){
     
            	$this->isLoggedIn();
			 $data = $this->get_data();
			 if(isset($data) && isset($data->b_title)){
				 //var_dump($data); die;

				 $media_type = 0;
				 if(isset($data->b_type)){
						$media_type = $data->b_type;
				 }
				 $title = $data->b_title;
				 $category = 0;
				 if(isset($data->cat_id)){
						$category = $data->cat_id;
				 }
                                 
                                 $amount= 0;
				 if(isset($data->amount)){
						$amount = $data->amount;
				 }
				// $subcategory = 0;
//				if(isset($data->cat_id)){
//					 $subcategory = $data->subcategory;
//				}

				 $description = "";
				 if(isset($data->b_desc)){
						$b_desc = $data->b_desc;
				 }
				 
				 $book_url = "";
                if(isset($data->book_url)){
                  $book_url = $data->book_url;
                    }
				 //$duration = 0;
				if(isset($data->b_name)){
					 $b_name = $data->b_name;
				}
				 $is_free = 0;
				 if(isset($data->is_free)){
						$is_free = $data->is_free;
				 }
				 $can_download = 0;
				 if(isset($data->can_download)){
						$can_download = $data->can_download;
				 }
//				 $can_preview = 1;
//				 if(isset($data->can_preview)){
//						$can_preview = $data->can_preview;
//				 }
//				 $preview_duration = 0;
//				 if(isset($data->preview_duration)){
//						$preview_duration = $data->preview_duration;
//				 }


				 $notify = false;
				 if(isset($data->notify)){
						$notify = $data->notify;
				 }
//          $_duration = new Duration;
					$info = array(
						'cat_id' => $category,
						'b_title' => $title,
						'b_name' => $b_name,
						'b_desc'=> $b_desc,
						'book_url'=> $book_url,
						'is_free'=> $is_free,
						'amount'=> $amount,
						'can_download'=> $can_download,
                                                'uti' =>  $this->session->userdata ( 'userId' ),
						'utimo' =>  $this->session->userdata ( 'userId' ),
						'dou' =>  date('y-m-d h:i:s') ,
						'dmo' =>  date('y-m-d h:i:s'),
						//'can_preview'=> $can_preview,
						//'preview_duration' => $preview_duration,
						//'sub_category' => $subcategory,
					       // 'duration' => $_duration->toSeconds($duration) * 1000,
						'b_type' => 'book'
					);

					if($media_type==0){
	 				 //upload image file
	 					$thumb_upload = $this->upload_thumbnail();
	 					//upload video file
	 					$audio_upload = $this->upload_book();

	 					//if there are any error, display to user
	 					if($audio_upload[0]=='error' || $thumb_upload[0]=='error'){
	 						 $msg = $audio_upload[0]=='error'?"Book upload error: ".$audio_upload[1]:"";
	 						 $msg .= $thumb_upload[0]=='error'?"\nThumbnail upload error: ".$thumb_upload[1]:"";
	 					         echo json_encode(array("status" => "error","msg" => $msg));
	 						exit;
	 					}

	 					$info['thumbnail'] = $thumb_upload[1];
	 					$info['url'] = $audio_upload[1];
	 			 }else{
	 				$info['thumbnail'] = $data->thumbnail;
                                        $info['url'] = $data->url;
	 			 }

				 $id = $this->books_model->addNewBook($info);
                                 
				 if($notify){
                                     
					 $this->load->model('settings_model');
					 $server_key = $this->settings_model->getFcmServerKey();
					 $this->load->model('fcm_model');
					 $title = "Tap to read this new Book :  ".$title;
					 $this->load->model('media_model');
					 $media = $this->media_model->fetchPlayableMedia($id);
					 $this->fcm_model->newMediaNotification($server_key,$title,$media);
				 }
		 }
		 echo json_encode(array("status" => $this->books_model->status,"msg" => $this->books_model->message));
    }


     public function editBookData(){
         
         	$this->isLoggedIn();
			$data = $this->get_data();
			if(!isset($data) || !isset($data->b_title)){
				echo json_encode(array("status" => $this->books_model->status,"msg" => $this->books_model->message));
				exit;
		   }

                  $id = isset($data->id)?$data->id:0;
                  
                 // var_dump($data);exit;
                 $media_type = 0;
				 if(isset($data->b_type)){
						$media_type = $data->b_type;
				 }
				 $title = $data->b_title;
				 $category = 0;
				 if(isset($data->cat_id)){
						$category = $data->cat_id;
				 }
				// $subcategory = 0;
//				if(isset($data->cat_id)){
//					 $subcategory = $data->subcategory;
//				}

				 $description = "";
				 if(isset($data->b_desc)){
						$description = $data->b_desc;
				 }
				 
				
				 
				 //$duration = 0;
				if(isset($data->b_name)){
					 $name = $data->b_name;
				}
				 $is_free = 0;
				 if(isset($data->is_free)){
						$is_free = $data->is_free;
				 }
				 $can_download = 0;
				 if(isset($data->can_download)){
						$can_download = $data->can_download;
				 }
//				 $can_preview = 1;
//				 if(isset($data->can_preview)){
//						$can_preview = $data->can_preview;
//				 }
//				 $preview_duration = 0;
//				 if(isset($data->preview_duration)){
//						$preview_duration = $data->preview_duration;
//				 }
	             $amount =0;
                 if(isset($data->amount)){
						$amount = $data->amount;
				 }
				 
				  $book_url = "";
                if(isset($data->book_url)){
                  $book_url = $data->book_url;
                    }


				 $info = array(
						'cat_id' => $category,
						'b_title' => $title,
						'b_name' => $title,
						'b_desc'=> $description,
						'book_url'=> $book_url,
						'is_free'=> $is_free,
						'amount'=> $amount,
						'can_download'=> $can_download,
						'utimo' =>  $this->session->userdata ( 'userId' ),
						'dmo' =>  date('y-m-d h:i:s'),
						//'can_preview'=> $can_preview,
						//'preview_duration' => $preview_duration,
						//'sub_category' => $subcategory,
					       // 'duration' => $_duration->toSeconds($duration) * 1000,
						'b_type' => 'book'
					);

 				$thumbnail_link = "";
 				 if(isset($data->thumbnail_link)){
 						$thumbnail_link = $data->thumbnail_link;
 				 }
				 $original_thumb = "";
				 if(isset($data->newthumbnail)){
						$original_thumb = $data->newthumbnail;
				 }
				 $media_link = "";
				 if(isset($data->media_link)){
						$media_link = $data->media_link;
				 }
				 $original_video = "";
				 if(isset($data->original_video)){
						$original_video = $data->original_video;
				 }

			/*	 if($thumbnail_link != $original_thumb){
					 $info['thumbnail '] = $data->thumbnail_link;
				 }*/

			/*	 if($original_video != $media_link){
	 				 $info['url'] = $data->media_link;
				 } */
				 
				
				        //var_dump($_FILES);
				        
	 					if(isset($_FILES['thumbnail']) && isset($_FILES['thumbnail']['name'])){
	 			    	$thumb_upload = $this->upload_thumbnail();  
	 					//upload video file
	 					
	 					if($thumb_upload[0] == 'ok'){
	 					$info['thumbnail'] = $thumb_upload[1];
	 					}
	 					
	 					}
	 					
	 					if(isset($_FILES['book']) && isset($_FILES['book']['name'])){
	 					
	 					$audio_upload = $this->upload_book();

	 					
	 					if($audio_upload[0] == 'ok'){
	 					    
	 					    $info['url'] = $audio_upload[1];
	 					}

	 					}
 /*var_dump($thumb_upload);
 var_dump($audio_upload);
 var_dump($info);exit;*/
				
				
				
				
				 
				 
				 
				 
				 
				/* if(isset($data->newthumbnail)){
	 				 //upload image file
	 					$thumb_upload = $this->upload_thumbnail();  
	 					//upload video file
	 					$info['thumbnail'] = $thumb_upload[1];

                        if(isset($data->newurl)){
	 					
	 					$audio_upload = $this->upload_book();

	 					//if there are any error, display to user
	 					if($audio_upload[0]=='error' || $thumb_upload[0]=='error'){
	 						 $msg = $audio_upload[0]=='error'?"Book upload error: ".$audio_upload[1]:"";
	 						 $msg .= $thumb_upload[0]=='error'?"\nThumbnail upload error: ".$thumb_upload[1]:"";
	 					         echo json_encode(array("status" => "error","msg" => $msg));
	 						exit;
	 					}

	 					$info['url'] = $audio_upload[1];
	 					
                        }
	 		//	 }else{
	 		//		$info['thumbnail'] = $data->thumbnail;
            //         $info['url'] = $data->url;
	 		//	 }
				 
				 */
				 
				 
				 
				 
				 
				 
 				 $this->books_model->editBook($info, $id);

				 //we send a fcm broadcast to users to automatically edit medias on their device
				 //$this->load->model('settings_model');
				// $server_key = $this->settings_model->getFcmServerKey();
				// $this->load->model('media_model');
				// $media = $this->media_model->fetchPlayableMedia($id);
				// $this->load->model('fcm_model');
				// $this->fcm_model->editMediaNotification($server_key,$media);

		    echo json_encode(array("status" => $this->books_model->status,"msg" => $this->books_model->message));
    }

    
    
    
    
    


    function deleteBook($id=0)
    {  
	 $this->isLoggedIn();
      $this->load->library('session');
      $book = $this->books_model->getbookInfo($id);
			if(count((array)$book)>0)
			{
				@unlink('./uploads/pdf/'.$book->thumbnail);
				@unlink('./uploads/thumbnails/'.$book->url);
			}
      $this->books_model->deleteBook($id);
      if($this->books_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->books_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->books_model->message);
      }
      redirect('booksListing');
    }


    public function upload_book(){
        
      $path = $_FILES['book']['name'];
      $file_name = "book_".time().".".pathinfo($path, PATHINFO_EXTENSION);
      $config['upload_path'] = './uploads/pdf';
      $config['file_name'] = $file_name;
      //$config['max_size']             = 10000;
      $config['allowed_types']        = 'pdf|epub';
      $config['overwrite'] = FALSE; //overwrite file

      //var_dump($config);
      $this->load->library('upload');
      $this->upload->initialize($config);

      if ( ! $this->upload->do_upload('book')){
          //$error = array('error' => $this->upload->display_errors());
          return ['error',strip_tags($this->upload->display_errors())];
      }else{
          $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
	   return ['ok',$file_name];
      }
    }

    function upload_thumbnail(){
        
			$config['upload_path']          = './uploads/thumbnails';
			//$config['max_size']             = 10000;
			$config['allowed_types']        = 'jpeg|jpg|png|JPEG|PNG';
			$config['overwrite'] = TRUE; //overwrite file
			$this->load->library('upload');
                        $this->upload->initialize($config);
			if ( ! $this->upload->do_upload('thumbnail'))
                        {
					//$error = array('error' => $this->upload->display_errors());
			     return ['error',strip_tags($this->upload->display_errors())];
			}else{
			     $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
			     $file_name = $upload_data['file_name'];
		             return ['ok',$file_name];
			}
                        
                        
                        
		}

}
