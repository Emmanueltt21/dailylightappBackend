<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';

class Devotionals extends BaseController {

	public function __construct(){
        parent::__construct();
				$this->isLoggedIn();
				$this->load->model('devotionals_model');
    }

		//rss links methods
		public function devotionalsListing(){
        $this->load->template('devotionals/listing', []); // this will load the view file
    }

		function getDevotionals(){
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


        $feeds = $this->devotionals_model->adminDevotionalsListing($columnName,$columnSortOrder,$searchValue,$start, $length);
				$total_feeds = $this->devotionals_model->get_total_devotionals($searchValue);
        //var_dump($feeds); die;
        $dat = array();

				 $count = $start + 1;
        foreach($feeds as $r) {
					//var_dump($r); die;
          //$title = substr($r->title,0,10 );
          //$content = substr($r->content,0,50 );

             $dat[] = array(
							    $count,
									$r->date,
									$r->title,
									'<div class="btn-group btn-group-sm" style="float: none;">'.
									'<a href="'.site_url().'editDevotional/'.$r->id.'" type="button" class="tabledit-edit-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="margin-bottom:5px;" class="material-icons list-icon" data-id="'.$r->id.'">create</i></a>'.
									'<button onclick="delete_item(event)" data-type="devotionals" data-id="'.$r->id.'" type="button" class="tabledit-delete-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="color:red;margin-bottom:5px;"  class="material-icons list-icon" data-type="devotionals" data-id="'.$r->id.'">delete</i></button>'.
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


		public function newDevotional(){
        $this->load->template('devotionals/new', []); // this will load the view file
    }

    public function editDevotional($id=0)
    {

        $data['devotional'] = $this->devotionals_model->getDevotionalInfo($id);
        if(count((array)$data['devotional'])==0)
        {
            redirect('devotionalsListing');
        }
        $this->load->template('devotionals/edit', $data); // this will load the view file
    }



    function saveNewDevotional()
    {

            $this->load->library('session');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('date','Devotional Date','trim|required');
						$this->form_validation->set_rules('title','Devotional Title','trim|required');
						$this->form_validation->set_rules('content','Devotional Content','trim|required');

            if($this->form_validation->run() == FALSE)
            {
							  $this->session->set_flashdata('error', "Some fields were left empty");
                redirect('newDevotional');
            }else {

							$date = $this->input->post('date');
							$title = $this->input->post('title');
							$author =$this->input->post('author');
							$bible_reading = $this->input->post('bible_reading');
							$content = $this->input->post('content');
							$studies =$this->input->post('studies');
							$confession =$this->input->post('confession');

							// Translate all 5 fields into all 8 languages:
							// French, German, Italian, Spanish, Hindi, Russian, Portuguese, Mandarin
							$translations = $this->devotionals_model->translate_devotional_fields($title, $bible_reading, $content, $confession, $studies);

							$info = array(
									'date' => $date,
									'title' => $title,
									'author' => $author,
									'bible_reading' => $bible_reading,
									'studies' => $studies,
									'confession' => $confession,
									'content' => $content,

									// French
									'french_title' => $translations['french_title'],
									'french_bible_reading' => $translations['french_bible_reading'],
									'french_content' => $translations['french_content'],
									'french_confession' => $translations['french_confession'],
									'french_studies' => $translations['french_studies'],

									// German
									'german_title' => $translations['german_title'],
									'german_bible_reading' => $translations['german_bible_reading'],
									'german_content' => $translations['german_content'],
									'german_confession' => $translations['german_confession'],
									'german_studies' => $translations['german_studies'],

									// Italian
									'italian_title' => $translations['italian_title'],
									'italian_bible_reading' => $translations['italian_bible_reading'],
									'italian_content' => $translations['italian_content'],
									'italian_confession' => $translations['italian_confession'],
									'italian_studies' => $translations['italian_studies'],

									// Spanish
									'spanish_title' => $translations['spanish_title'],
									'spanish_bible_reading' => $translations['spanish_bible_reading'],
									'spanish_content' => $translations['spanish_content'],
									'spanish_confession' => $translations['spanish_confession'],
									'spanish_studies' => $translations['spanish_studies'],

									// Hindi
									'hindi_title' => $translations['hindi_title'],
									'hindi_bible_reading' => $translations['hindi_bible_reading'],
									'hindi_content' => $translations['hindi_content'],
									'hindi_confession' => $translations['hindi_confession'],
									'hindi_studies' => $translations['hindi_studies'],

									// Russian
									'russian_title' => $translations['russian_title'],
									'russian_bible_reading' => $translations['russian_bible_reading'],
									'russian_content' => $translations['russian_content'],
									'russian_confession' => $translations['russian_confession'],
									'russian_studies' => $translations['russian_studies'],

									// Portuguese
									'portuguese_title' => $translations['portuguese_title'],
									'portuguese_bible_reading' => $translations['portuguese_bible_reading'],
									'portuguese_content' => $translations['portuguese_content'],
									'portuguese_confession' => $translations['portuguese_confession'],
									'portuguese_studies' => $translations['portuguese_studies'],

									// Mandarin
									'mandarin_title' => $translations['mandarin_title'],
									'mandarin_bible_reading' => $translations['mandarin_bible_reading'],
									'mandarin_content' => $translations['mandarin_content'],
									'mandarin_confession' => $translations['mandarin_confession'],
									'mandarin_studies' => $translations['mandarin_studies'],
							);

							if(!empty($_FILES['thumbnail']['name'])){
								$upload = $this->upload_thumbnail();
								if($upload[0]=='ok'){
									$info['thumbnail'] =  $upload[1];
								}
							}

              $this->devotionals_model->addNewDevotional($info);
              
						}

							if($this->devotionals_model->status == "ok")
							{
									$this->session->set_flashdata('success', $this->devotionals_model->message);
							}
							else
							{
									$this->session->set_flashdata('error', $this->devotionals_model->message);
							}
                redirect('newDevotional');

    }



    function editDevotionalData()
    {
			//var_dump($_FILES); die;
			$this->load->library('session');
			$this->load->library('form_validation');
            $id = $this->input->post('id');

			$this->form_validation->set_rules('date','Devotional Date','trim|required');
			$this->form_validation->set_rules('title','Devotional Title','trim|required');
			$this->form_validation->set_rules('content','Devotional Content','trim|required');

			if($this->form_validation->run() == FALSE)
			{
					$this->session->set_flashdata('error', "Some fields were left empty");
					redirect('editDevotional/'.$id);
			}else {

				$date = $this->input->post('date');
				$title = $this->input->post('title');
				$author =$this->input->post('author');
				$bible_reading = $this->input->post('bible_reading');
				$content = $this->input->post('content');
				$studies =$this->input->post('studies');
				$confession =$this->input->post('confession');
				$french_content =$this->input->post('french_content');
				$german_content =$this->input->post('german_content');
				
				$french_title =$this->input->post('french_title');
				$german_title =$this->input->post('german_title');

				// Languages to auto-translate: Italian, Spanish, Hindi, Russian, Portuguese, Mandarin
				// Also translate French and German if left empty on the edit page
				$langs_to_translate = [
					'italian'    => 'IT',
					'spanish'    => 'ES',
					'hindi'      => 'HI',
					'russian'    => 'RU',
					'portuguese' => 'PT',
					'mandarin'   => 'ZH'
				];
				if (empty($french_title) || empty($french_content)) {
					$langs_to_translate['french'] = 'FR';
				}
				if (empty($german_title) || empty($german_content)) {
					$langs_to_translate['german'] = 'DE';
				}

				$translations = $this->devotionals_model->translate_devotional_fields($title, $bible_reading, $content, $confession, $studies, $langs_to_translate);

				$info = array(
						'date' => $date,
						'title' => $title,
						'author' => $author,
						'bible_reading' => $bible_reading,
						'studies' => $studies,
						'confession' => $confession,
						'content' => $content,

						// French
						'french_title' => !empty($french_title) ? $french_title : (isset($translations['french_title']) ? $translations['french_title'] : $title),
						'french_bible_reading' => isset($translations['french_bible_reading']) ? $translations['french_bible_reading'] : $bible_reading,
						'french_content' => !empty($french_content) ? $french_content : (isset($translations['french_content']) ? $translations['french_content'] : $content),
						'french_confession' => isset($translations['french_confession']) ? $translations['french_confession'] : $confession,
						'french_studies' => isset($translations['french_studies']) ? $translations['french_studies'] : $studies,

						// German
						'german_title' => !empty($german_title) ? $german_title : (isset($translations['german_title']) ? $translations['german_title'] : $title),
						'german_bible_reading' => isset($translations['german_bible_reading']) ? $translations['german_bible_reading'] : $bible_reading,
						'german_content' => !empty($german_content) ? $german_content : (isset($translations['german_content']) ? $translations['german_content'] : $content),
						'german_confession' => isset($translations['german_confession']) ? $translations['german_confession'] : $confession,
						'german_studies' => isset($translations['german_studies']) ? $translations['german_studies'] : $studies,

						// Italian
						'italian_title' => $translations['italian_title'],
						'italian_bible_reading' => $translations['italian_bible_reading'],
						'italian_content' => $translations['italian_content'],
						'italian_confession' => $translations['italian_confession'],
						'italian_studies' => $translations['italian_studies'],

						// Spanish
						'spanish_title' => $translations['spanish_title'],
						'spanish_bible_reading' => $translations['spanish_bible_reading'],
						'spanish_content' => $translations['spanish_content'],
						'spanish_confession' => $translations['spanish_confession'],
						'spanish_studies' => $translations['spanish_studies'],

						// Hindi
						'hindi_title' => $translations['hindi_title'],
						'hindi_bible_reading' => $translations['hindi_bible_reading'],
						'hindi_content' => $translations['hindi_content'],
						'hindi_confession' => $translations['hindi_confession'],
						'hindi_studies' => $translations['hindi_studies'],

						// Russian
						'russian_title' => $translations['russian_title'],
						'russian_bible_reading' => $translations['russian_bible_reading'],
						'russian_content' => $translations['russian_content'],
						'russian_confession' => $translations['russian_confession'],
						'russian_studies' => $translations['russian_studies'],

						// Portuguese
						'portuguese_title' => $translations['portuguese_title'],
						'portuguese_bible_reading' => $translations['portuguese_bible_reading'],
						'portuguese_content' => $translations['portuguese_content'],
						'portuguese_confession' => $translations['portuguese_confession'],
						'portuguese_studies' => $translations['portuguese_studies'],

						// Mandarin
						'mandarin_title' => $translations['mandarin_title'],
						'mandarin_bible_reading' => $translations['mandarin_bible_reading'],
						'mandarin_content' => $translations['mandarin_content'],
						'mandarin_confession' => $translations['mandarin_confession'],
						'mandarin_studies' => $translations['mandarin_studies'],
				);

				if(!empty($_FILES['thumbnail']['name'])){
					$upload = $this->upload_thumbnail();
					if($upload[0]=='ok'){
						$info['thumbnail'] =  $upload[1];
					}
				}

				$this->devotionals_model->editDevotional($info,$id);
			}

				if($this->devotionals_model->status == "ok")
				{
						$this->session->set_flashdata('success', $this->devotionals_model->message);
				}
				else
				{
						$this->session->set_flashdata('error', $this->devotionals_model->message);
				}

			redirect('editDevotional/'.$id);
    }


    function deleteDevotional($id=0)
    {
      $this->load->library('session');
      $this->devotionals_model->deleteDevotional($id);
      if($this->devotionals_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->devotionals_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->devotionals_model->message);
      }
      redirect('devotionalsListing');
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
