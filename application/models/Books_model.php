<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class Books_model extends CI_Model{
    public $status = 'error';
    public $message = 'Error processing requested operation.';
    public $user = "";

    function __construct(){
       parent::__construct();
	  }
	  
    public function base_url(){
        if (strpos(site_url(), 'http://') === 0 || strpos(site_url(), 'https://') === 0) {
            return "";
        }
        return "http://".$_SERVER['HTTP_HOST'];
    }

    public function get_total_books(){ 
      $query = $this->db->select("COUNT(*) as num")->get("tbl_books");
      $result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
   }

   public function getTrendingbooks($media_type, $page){ 
     $this->db->select('tbl_books.*,tbl_book_cat.id as category_id,tbl_book_cat.cat_name as category, tbl_book_cat.thumbnail as cat_thumbnail');
     $this->db->from('tbl_books');
     $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
     $this->db->where('views_count >',0); //update from zero to minimum amount for a media to trend
     $this->db->order_by('views_count','desc');
     if($page!=0){
         $this->db->limit(20,$page * 20);
     }else{
       $this->db->limit(20);
     }
       $query = $this->db->get();
       $result = $query->result(); 
       foreach ($result as $res) {
         $res->thumbnail = $this->base_url().$this->get_thumbnail_source($res->thumbnail);
         $res->url = $this->base_url().$this->get_media_source($res->url);
         $res->cat_thumbnail = $this->base_url().$this->get_thumbnail_source($res->cat_thumbnail);
       }
       
      
       return $result;
    }

   public function update_books_total_views($id){
     //update total views on media
     $this->db->set('views_count', '`views_count`+ 1', false);
     $this->db->where('id' , $id);
     $this->db->update('tbl_books');
     $this->status = 'ok';
   }

   function booksListing_old($type, $page){ 
        $this->db->select('tbl_books.*,tbl_book_cat.id as category_id,tbl_book_cat.cat_name as category, tbl_book_cat.thumbnail as cat_thumbnail');
        $this->db->from('tbl_books');
        $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
        $this->db->order_by('dou','DESC');
        
        if($page != "NOK"){
        if($page!=0){
            $this->db->limit(20,$page * 20);
        }else{
          $this->db->limit(20);
        }
        }
        $query = $this->db->get();
        $result = $query->result(); 
        foreach ($result as $res) {
          $res->thumbnail = $this->base_url().$this->get_thumbnail_source($res->thumbnail);
          $res->url = $this->base_url().$this->get_media_source($res->url);
        }
        
         
        return $result;
   }


function booksListing($columnName,$columnSortOrder,$searchValue,$start, $length){
        $this->db->select('tbl_books.*,tbl_book_cat.id as category_id,tbl_book_cat.cat_name as category, tbl_book_cat.thumbnail as cat_thumbnail');
        $this->db->from('tbl_books');
        $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
        $this->db->where('b_type','book');
        if($searchValue!=""){
            $this->db->like('b_tile', $searchValue);
            $this->db->or_like('b_desc', $searchValue);
        }
        if($columnName!=""){
           $this->db->order_by($columnName, $columnSortOrder);
        }
        $this->db->limit($length,$start);

        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $res) {
            
         if(!$this->isValidURL($res->thumbnail)){

            $res->thumbnail = $this->base_url().$this->get_thumbnail_source($res->thumbnail);
          
         }
         
          if(!$this->isValidURL($res->url)){

             $res->url = $this->base_url().$this->get_media_source($res->url);
          
          }
        }
        return $result;
    
}
      
      
      
      
   private function get_thumbnail_source($thumbnail){
       if($this->isValidURL($thumbnail)){
         return $thumbnail;
       }
       return site_url()."uploads/thumbnails/".$thumbnail;
   }

   private function get_media_source($source){
    return site_url()."uploads/pdf/".$source;
   }

   function isValidURL($url){
      return filter_var($url, FILTER_VALIDATE_URL);
   }


   function checkNameExists($name, $id = 0)
   {
       //echo $name . " and ". $group;
       $this->db->select("b_title");
       $this->db->from("tbl_books");
       $this->db->where("b_title", $name);
       if($id != 0){
           $this->db->where("id !=", $id);
       }
       $query = $this->db->get();
       //var_dump($query->result()); die;
       return $query->result();
   }


   function addNewBook($info)
   {
     if(empty($this->checkNameExists($info['b_title']))){
       $this->db->trans_start();
       $this->db->insert('tbl_books', $info);
       $this->db->trans_complete();
       $this->status = 'ok';
       $this->message = 'Ebook added successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Ebook already exists';
     }
   }


   function editBook($info, $id){   
       
     if(empty($this->checkNameExists($info['b_title'],$id))){
       $this->db->where('id', $id);
       $this->db->update('tbl_books', $info);
       $this->status = 'ok';
       $this->message = 'Ebook edited successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Ebook already exists with this title';
     }
   }


   function getbookInfo($id)
   {
     $this->db->select('tbl_books.*,tbl_book_cat.id as category_id,tbl_book_cat.cat_name as category');
     $this->db->from('tbl_books');
     $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
       $this->db->where('tbl_books.id', $id);
       $query = $this->db->get();
       $row = $query->row();
       if(count((array)$row) > 0){
        if(!$this->isValidURL($row->thumbnail)){
           $row->thumbnail = $this->base_url().$this->get_thumbnail_source($row->thumbnail);
        }
        
        if(!$this->isValidURL($row->url)){
         $row->url = $this->base_url().$this->get_media_source($row->url);
        }
          // $row->_thumbnail = "";
        // }else{
          //  $row->_thumbnail = $this->get_thumbnail_source($row->thumbnail);
        //    $row->thumbnail = "";
        // }
       }
       $this->update_books_total_views($id);
       return $row;
   }


   function deleteBook($id){
       $this->db->where('id', $id);
       $this->db->delete('tbl_books');
        $this->status = 'ok';
        $this->message = 'Ebook deleted successfully.';
   }

   public function fetch_Books($page = 0){
       $this->db->select('tbl_books.*,tbl_categories.id as category_id,tbl_categories.name as category');
       $this->db->from('tbl_books');
       $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
       $this->db->order_by('dou','desc');
       if($page!=0){
           $this->db->limit(20,$page * 20);
       }else{
         $this->db->limit(20);
       }
       $query = $this->db->get();
       $result = $query->result();
       foreach ($result as $res) {
           
         if(!$this->isValidURL($row->thumbnail)){
           $row->thumbnail = $this->base_url().$this->get_thumbnail_source($row->thumbnail);
        }
        
        if(!$this->isValidURL($row->url)){
         $row->url = $this->base_url().$this->get_media_source($row->url);
        }
      //   $res->thumbnail = $this->get_thumbnail_source($res->thumbnail);
      //   $res->url = $this->get_media_source($res->url);
       }
       return $result;
   }

   public function fetch_categories_books($category,$page = 0,$email="null",$sub=0){
     $this->db->select('tbl_books.*,tbl_book_cat.id as category_id,tbl_book_cat.cat_name as category');
     $this->db->from('tbl_books');
     $this->db->join('tbl_book_cat','tbl_book_cat.id=tbl_books.cat_id');
       $this->db->where('category',$category);
//       if($sub!=0){
//         $this->db->where('sub_category',$sub);
//       }
       $this->db->order_by('dou','desc');

       if($page!=0){
           $this->db->limit(20,$page * 20);
       }else{
         $this->db->limit(20);
       }

       $query = $this->db->get();
       $result = $query->result();
       foreach ($result as $res) {
           
         $res->thumbnail = $this->get_thumbnail_source($res->thumbnail);
         $res->url = $this->get_media_source($res->url);
       }
       return $result;
   }

   public function total_categories_books($id,$sub=0){
     $this->db->select("COUNT(*) as num");
     $this->db->where('cat_id',$id);
//     if($sub!=0){
//       $this->db->where('sub_category',$sub);
//     }
     $query = $this->db->get("tbl_books");
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
    }
}
