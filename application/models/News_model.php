<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class News_model extends CI_Model{
    public $status = 'error';
    public $message = 'Something went wrong';
    public $data = [];
    public $date = "";

    function __construct(){
       parent::__construct();
	  }

	  
    public function base_url(){
        if (strpos(site_url(), 'http://') === 0 || strpos(site_url(), 'https://') === 0) {
            return "";
        }
        return "http://".$_SERVER['HTTP_HOST'];
    }
    function getNews($date=""){
        $this->db->select('tbl_news.*');
        $this->db->from('tbl_news');
        $this->db->where('date', $date);
        $query = $this->db->get();
        return $query->row();
    }
    
    
    
    
     function getPrayer_request($page, $email=""){
         
        $this->db->select('tbl_prayer_request.*');
        $this->db->from('tbl_prayer_request');
        if($email!="NOK"){
          $this->db->where('uti', $email);
        }
        $this->db->order_by("dou", "DESC");
        if($page!=0){
             $this->db->limit(20,$page * 20);
        }else{
           $this->db->limit(20);
        }
        $query = $this->db->get();
           
        return $query->result();
    }
    
    
     function getPrayer_requestweb(){
         
        $this->db->select('tbl_prayer_request.*');
        $this->db->from('tbl_prayer_request');
        $this->db->order_by("dou", "DESC");
        $query = $this->db->get();
        return $query->result();
    }
    
    
    
    public function get_total_request($email){
      $query = $this->db->select("COUNT(*) as num")->where("uti", $email)->get("tbl_prayer_request");
      $result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
   }

    public function update_total_views($id){
      //update total views on media
      $this->db->set('views_count', '`views_count`+ 1', false);
      $this->db->where('id' , $id);
      $this->db->update('tbl_news');
      $this->status = 'ok';
    }

    function getTotalViews($id){
      $this->db->select('tbl_news.views_count');
      $this->db->from('tbl_news');
      $this->db->where('id', $id);
      $query = $this->db->get();
      $row = $query->row();
      return $row->views_count;
    }


    function getArticleData($id)
    {  
      $this->db->select('tbl_devotionals.*,interests.id as interest_id,interests.name as interest');
      $this->db->from('tbl_devotionals');
      $this->db->join('interests','interests.id=tbl_devotionals.interest');
        $this->db->where('tbl_devotionals.id', $id);
        $query = $this->db->get();
        $row = $query->row();
        if(count((array)$row)>0){
          $row->timeStamp = strtotime($row->date);
          $row->comments_count = $this->get_total_comments($row->id);
          $row->likes_count = $this->get_total_likes($row->id);
          $row->thumbnail = $this->get_media_source($row->thumbnail);
          $row->timeStamp = strtotime($row->date);
          $row->date = date("D M j G:i:s T Y", $row->timeStamp);
          $row->title = preg_replace('/\s+/S', " ", $row->title);
          if($row->feed_type == "article"){
            $row->content = "";
          }
          $row->video_source = $this->get_video_source($row->video_source,$row->video_type);
        }
        //echo $row->source; die;
        return $row;
    }


        function getArticleContent($id)
        {
          $this->db->select('tbl_news.content');
          $this->db->from('tbl_news');
            $this->db->where('tbl_news.id', $id);
            $query = $this->db->get();
            $row = $query->row();
            if($row){
              return $row->content;
            }
            return "";
        }

   function feedsListing($data = []){

     $this->db->select('tbl_devotionals.*,interests.id as interest_id,interests.name as interest');
     $this->db->from('tbl_devotionals');
     $this->db->join('interests','interests.id=tbl_devotionals.interest');

     if(isset($data->interests)){
        $this->db->where('tbl_devotionals.interest ', $data->interests);
     }

     if(isset($data->date)){
       $this->db->where('tbl_devotionals.dateInserted < ', $data->date);
     }
      $this->db->order_by('dmo', 'desc');
      if(isset($data->offset)){
        $this->db->limit(20,$data->offset + 1);
      }else{
        $this->db->limit(20,0);
      }
      $query = $this->db->get();


       //var_dump($query); die;
       $result = $query->result();
       foreach ($result as $res) {
         $res->thumbnail = $this->get_media_source($res->thumbnail);
         $res->timeStamp = strtotime($res->date);
         $res->comments_count = $this->get_total_comments($res->id);
         $res->likes_count = $this->get_total_likes($res->id);
         $res->date = date("D M j G:i:s T Y", $res->timeStamp);
         $res->title = preg_replace('/\s+/S', " ", $res->title);
         if($res->feed_type == "article"){
           $res->content = $this->character_limiter(strip_tags($res->content),200);
         }
         $res->video_source = $this->get_video_source($res->video_source,$res->video_type);
       }

       $this->data = $result;
       if(count((array)$result)>0){
         $this->date = $result[0]->dateInserted;
       }
   }

   public function get_total_comments($id){
     $query = $this->db->select("COUNT(*) as num")->where('post_id',$id)->where('deleted',1)->get("tbl_comments");
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
  }

  public function get_total_likes($id){
    $query = $this->db->select("COUNT(*) as num")->where('post_id',$id)->get("tbl_likes");
    $result = $query->row();
    if(isset($result)) return $result->num;
    return 0;
 }


   function trendingFeedsListing($data = []){

     $this->db->select('tbl_devotionals.*,interests.id as interest_id,interests.name as interest');
     $this->db->from('tbl_devotionals');
     $this->db->join('interests','interests.id=tbl_devotionals.interest');

     if(isset($data->interests)){
        $this->db->where('tbl_devotionals.interest ', $data->interests);
     }

     if(isset($data->date)){
       $this->db->where('tbl_devotionals.dateInserted < ', $data->date);
     }
     $this->db->where('views_count >',0); //update from zero to minimum amount for a media to trend
     $this->db->order_by('views_count','desc');
      if(isset($data->offset)){
        $this->db->limit(20,$data->offset + 1);
      }else{
        $this->db->limit(20,0);
      }
      $query = $this->db->get();


       //var_dump($query); die;
       $result = $query->result();
       foreach ($result as $res) {
         $res->comments_count = $this->get_total_comments($res->id);
         $res->likes_count = $this->get_total_likes($res->id);
         $res->thumbnail = $this->get_media_source($res->thumbnail);
         $res->timeStamp = strtotime($res->date);
         $res->date = date("D M j G:i:s T Y", $res->timeStamp);
         $res->title = preg_replace('/\s+/S', " ", $res->title);
         if($res->feed_type == "article"){
           $res->content = $this->character_limiter(strip_tags($res->content),200);
         }
         $res->video_source = $this->get_video_source($res->video_source,$res->video_type);
       }

       $this->data = $result;
       if(count((array)$result)>0){
         $this->date = $result[0]->dateInserted;
       }
   }


function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
    if (strlen($str) < $n)
    {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n)
    {
        return $str;
    }

    $out = "";
    foreach (explode(' ', trim($str)) as $val)
    {
        $out .= $val.' ';

        if (strlen($out) >= $n)
        {
            $out = trim($out);
            return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
        }
    }
 }


   function adminNewsListing($columnName,$columnSortOrder,$searchValue,$start, $length){
     $this->db->select('tbl_news.*');
     $this->db->from('tbl_news');
     if($searchValue!=""){
         $this->db->like('title', $searchValue);
         $this->db->or_like('content', $searchValue);
     }
     if($columnName!=""){
        $this->db->order_by($columnName, $columnSortOrder);
     }else{
       $this->db->order_by("dmo", "DESC");
     }
     $this->db->limit($length,$start);
     $query = $this->db->get();
     return $query->result();
   }
   
   
   public function get_total_news($type,$version="v1"){
      $this->db->select("COUNT(*) as num");
      $this->db->where('type',$type);
      $query = $this->db->get("tbl_news");
      $result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
   }

//   public function get_total_news($searchValue=""){
//     if($searchValue==""){
//       $query = $this->db->select("COUNT(*) as num")->get("tbl_news");
//     }else{
//       $this->db->select("COUNT(*) as num");
//       $this->db->from('tbl_news');
//       $this->db->join('tbl_rss_urls','tbl_rss_urls.id = tbl_news.channel');
//       $this->db->like('title', $searchValue);
//       $this->db->or_like('content', $searchValue);
//       $query = $this->db->get();
//     }
//     $result = $query->row();
//     if(isset($result)) return $result->num;
//     return 0;
//  }

   function checkNewsExists($date, $id = 0)
   {
       $this->db->select("title");
       $this->db->from("tbl_news");
       $this->db->where("date", $date);
       if($id != 0){
           $this->db->where("id !=", $id);
       }
       $query = $this->db->get();

       return $query->result();
   }


   function addNewNews($info)
   {
     $insert_id = 0;
     if(empty($this->checkNewsExists($info['date']))){
       $this->db->trans_start();
       $this->db->insert('tbl_news', $info);
       $insert_id = $this->db->insert_id();
       $this->db->trans_complete();
       $this->status = 'ok';
       $this->message = 'News added successfully';
     }else{
       $this->status = 'error';
       $this->message = 'News already added for this date '.$info['date'];
     }
     return $insert_id;
   }


   function editNews($info, $id){
     if(empty($this->checkNewsExists($info['date'],$id))){
       $this->db->where('id', $id);
       $this->db->update('tbl_news', $info);
       $this->status = 'ok';
       $this->message = 'News edited successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Date for this news already exists for another';
     }
   }


   function getNewsInfo($id)
   {
     $this->db->select('tbl_news.*');
     $this->db->from('tbl_news');
       $this->db->where('tbl_news.id', $id);
       $query = $this->db->get();
       $row = $query->row();
       if(count((array)$row) > 0 && $row->thumbnail!=""){
         $row->thumbnail =  base_url()."uploads/thumbnails/".$row->thumbnail;
       }
       return $row;
   }  


   function deleteNews($id){
       $this->db->where('id', $id);
       $this->db->delete('tbl_news');
       $this->status = 'ok';
       $this->message = 'Devotional deleted successfully.';
   }


  function delete_old_articles()
  {
    $date = date("Y-m-d", strtotime('-7 day'));
    $this->db->where('dateInserted < ', $date);
    $this->db->delete('tbl_news');
  }

  private function get_video_source($source,$type){
      if($source==""){
        return "";
      }
      if($type!="mp4_video"){
        return $source;
      }
      if($this->isValidURL($source)){
        return $source;
      }
      return site_url()."uploads/videos/".$source;
  }

  private function get_media_source($source){
      if($this->isValidURL($source)){
        return $source;
      }
      return site_url()."uploads/thumbnails/".$source;
  }

  function isValidURL($url){
     return filter_var($url, FILTER_VALIDATE_URL);
 }
 
 
  public function fetch_news($type,$page = 0,$email="null"){ 
       $this->db->select('tbl_news.*');
  //     $this->db->select('tbl_news.*,tbl_categories.id as category_id,tbl_categories.name as category');
       $this->db->from('tbl_news');
     //  $this->db->join('tbl_categories','tbl_categories.id=tbl_media.category');
         $this->db->where('type',$type);

         $this->db->order_by('dmo','desc');

         if($page!=0){
             $this->db->limit(20,$page * 20);
         }else{
           $this->db->limit(20);
         }
          
         $query = $this->db->get();
         $result = $query->result();
         foreach ($result as $res) {
      //     $res->thumbnails = base_url()."uploads/thumbnails/".$res->thumbnails;
           $res->thumbnail =  $this->base_url().$this->get_media_source($res->thumbnail);

//           $res->stream = base_url().$this->get_media_source($res->type,$res->video_type,$res->source);
//           $res->download = base_url().$this->get_media_source($res->type,$res->video_type,$res->source);
//           $res->comments_count = $this->get_total_comments($res->id);
//           $res->user_liked = $this->checkIfUserLikedMedia($res->id,$email);
         }
         return $result;
     }
     
     
      
   function addPrayer_request($info)
   {
     $insert_id = 0;
     //if(empty($this->checkNewsExists($info['author']))){
       $this->db->trans_start();
       $this->db->insert('tbl_prayer_request', $info);
       $insert_id = $this->db->insert_id();
       $this->db->trans_complete();
       $this->status = 'ok';
       $this->message = 'request added successfully';
//     }else{
//       $this->status = 'error';
//       $this->message = 'News already added for this date '.$info['date'];
//     }
        return $insert_id;
   }
   
   function getPrayerRequestInfo($id)
   {
     $this->db->select('tbl_prayer_request.*');
     $this->db->from('tbl_prayer_request');
     $this->db->where('tbl_prayer_request.id', $id);
     $query = $this->db->get();
     return $query->row();
   }
   
   function updatePrayerRequest($info, $id)
   {
     $this->db->where('id', $id);
     $this->db->update('tbl_prayer_request', $info);
     $this->status = 'ok';
     $this->message = 'Prayer request updated successfully';
   }
   
   function deletePrayerRequest($id)
   {
     $this->db->where('id', $id);
     $this->db->delete('tbl_prayer_request');
     $this->status = 'ok';
     $this->message = 'Prayer request deleted successfully';
   }
   
   


// Function to translate content using DeepL API
public function translate_content($content, $lang) {

    $api_key = 'f192525c-93cd-aceb-1154-a8bdd645c9b2';
    $url = 'https://api.deepl.com/v2/translate';

    $data = array(
        "text" => [$content],
        "target_lang" => $lang,
        "formality" => "less"
    );

    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/json\r\n" .
                         "Authorization: DeepL-Auth-Key " . $api_key . "\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return false;
    }

    $response = json_decode($result, true);

    if(isset($response['translations'][0]['text'])){
        return $response['translations'][0]['text'];
    }

    return false;
}

}



