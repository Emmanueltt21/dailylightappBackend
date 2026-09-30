<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');
require_once APPPATH . '/libraries/BaseController.php';

class Books_cat extends BaseController {

	public function __construct()
    {
        parent::__construct();
        $this->isLoggedIn();
        $this->load->model('books_cat_model');
    }

    public function index(){
        $data['categories'] = $this->books_cat_model->categoriesListing();
        $this->load->template('categories/listing_book', $data); // this will load the view file
    }

    public function loadcategories(){
        $categories = $this->books_cat_model->categoriesListing();
        echo json_encode(array("status" => "ok","categories" => $categories));
    }

     public function newCategory()
    {
        $this->load->template('categories/new_book', []); // this will load the view file
    }

    public function editCategory($id=0)
    {
        $data['category'] = $this->books_cat_model->getCategoryInfo($id);
        if(count((array)$data['category'])==0)
        {
            redirect('categoriesListing');
        }
        $this->load->template('categories/edit_book', $data); // this will load the view file
    }

    function savenewcategory()
    {
            //var_dump($_POST); die;
            $this->load->library('session');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('cat_name','Category Name','trim|required|max_length[128]|xss_clean');

            if($this->form_validation->run() == FALSE)
            {
                redirect('newCategory');
            } 
            if(empty($_FILES['thumbnail']['name'])){
							$this->session->set_flashdata('error', "Thumbnail is empty");
							redirect('newCategory2');
						}
            else
            {
							$upload = $this->upload_thumbnail();
							if($upload[0]=='ok'){
								$name = $this->input->post('cat_name');
								$cat_desc = $this->input->post('cat_desc');
                                                                $userid = $this->session->userdata ( 'userId' );
								$info = array(
										'cat_name' => $name,
										'cat_desc' => $cat_desc,
										'media_count' => "0",
										'dou' => date("Y-m-d h:i:s") ,
										'dmo' => date("Y-m-d h:i:s") ,
										'uti' => $userid ,
										'dele' => "0" ,
										'utimo' => $userid ,
										'thumbnail' => $upload[1]
								);
                                //var_dump($info); die;
								$this->books_cat_model->addNewCategory($info);
								if($this->books_cat_model->status == "ok")
								{
                                                                    $this->session->set_flashdata('success', $this->books_cat_model->message);
								}
								else
								{
                                                                    $this->session->set_flashdata('error', $this->books_cat_model->message);
								}
							}else{
								$this->session->set_flashdata('error', $upload[1]);
							}
              redirect('newCategory2');
            }

    }


    function editCategoryData()
    {
			//var_dump($_FILES); die;
			$this->load->library('session');
			$this->load->library('form_validation');
      $id = $this->input->post('id');
			$this->form_validation->set_rules('cat_name','Category Name','trim|required|max_length[128]|xss_clean');    

			if($this->form_validation->run() == FALSE)
			{
					redirect('editCategory2/'.$id);
			} else
			{

					$name = $this->input->post('cat_name');
                                        $cat_desc = $this->input->post('cat_desc');
                                        $userid = $this->session->userdata ( 'userId' );
                                        $info = array(
                                                        'cat_name' => $name,
                                                        'cat_desc' => $cat_desc,
                                                        'media_count' => "0",
                                                        'dou' => date("Y-m-d h:i:s") ,
                                                        'dmo' => date("Y-m-d h:i:s") ,
                                                        'uti' => $userid ,
                                                        'dele' => "0" ,
                                                        'utimo' => $userid 
                                        );

					if(!empty($_FILES['thumbnail']['name'])){
						$upload = $this->upload_thumbnail();

						if($upload[0]=='ok'){
                                                     $info['thumbnail'] = $upload[1];
						}else{
							$this->session->set_flashdata('error', $upload[1]);
							redirect('editCategory2/'.$id);
							return;
						}
					}
					
				//	var_dump($info);exit;

					$this->books_cat_model->editCategory($info,$id);
					if($this->books_cat_model->status == "ok")
					{
							$this->session->set_flashdata('success', $this->books_cat_model->message);
					}
					else
					{
							$this->session->set_flashdata('error', $this->books_cat_model->message);
					}
					redirect('editCategory2/'.$id);

			}
    }


    function deleteCategory($id=0)
    {
      $this->load->library('session');
      $this->books_cat_model->deleteCategory($id);
      if($this->books_cat_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->books_cat_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->books_cat_model->message);
      }
      redirect('categoriesListing2');
    }

		//sub category methods
		public function subcategoryListing(){
        $data['categories'] = $this->Books_cat_model->subcategoriesListing();
        $this->load->template('subcategories/listing', $data); // this will load the view file
    }

		public function loadsubcategories(){
			$id = 0;
			if(isset($_GET['id'])){
				$id = $_GET['id'];
			}
			$categories = $this->Books_cat_model->subcategoriesListing($id);
			echo json_encode(array("status" => "ok","subcategories" => $categories));
		}

		public function newSubCategory()
    {
			  $data['categories'] = $this->Books_cat_model->categoriesListing();
        $this->load->template('subcategories/new', $data); // this will load the view file
    }

    public function editSubCategory($id=0)
    {
        $data['category'] = $this->categories_model->getSubCategoryInfo($id);
        if(count((array)$data['category'])==0)
        {
            redirect('subcategoryListing');
        }
				$data['categories'] = $this->categories_model->categoriesListing();
        $this->load->template('subcategories/edit', $data); // this will load the view file
    }

    function savenewsubcategory()
    {
            //var_dump($_FILES); die;
            $this->load->library('session');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('name','Sub Category Name','trim|required|xss_clean');

            if($this->form_validation->run() == FALSE)
            {
                redirect('newSubCategory');
            }
            else
            {
							$name = $this->input->post('name');
							$category_id = $this->input->post('category_id');
							$info = array(
									'name' => $name,
									'category_id' => $category_id
							);

							$this->categories_model->addNewSubCategory($info);
							if($this->categories_model->status == "ok")
							{
									$this->session->set_flashdata('success', $this->categories_model->message);
							}
							else
							{
									$this->session->set_flashdata('error', $this->categories_model->message);
							}
              redirect('newSubCategory');
            }

    }


    function editSubCategoryData()
    {
			//var_dump($_FILES); die;
			$this->load->library('session');
			$this->load->library('form_validation');
                        $id = $this->input->post('id');
			$this->form_validation->set_rules('cat_name','Sub Category Name','trim|required|xss_clean');

			if($this->form_validation->run() == FALSE)
			{
					redirect('editSubCategory/'.$id);
			} else
			{

					$name = $this->input->post('name');
					$category_id = $this->input->post('category_id');
					$info = array(
							'name' => $name,
							'category_id' => $category_id
					);

					$this->categories_model->editSubCategory($info,$id);
					if($this->categories_model->status == "ok")
					{
							$this->session->set_flashdata('success', $this->categories_model->message);
					}
					else
					{
							$this->session->set_flashdata('error', $this->categories_model->message);
					}
					redirect('editSubCategory/'.$id);

			}
    }


    function deleteSubCategory($id=0)
    {
      $this->load->library('session');
      $this->categories_model->deleteSubCategory($id);
      if($this->categories_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->categories_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->categories_model->message);
      }
      redirect('subcategoryListing');
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
