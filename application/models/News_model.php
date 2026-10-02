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
     if(!isset($info['date']) || empty($this->checkNewsExists($info['date'],$id))){
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
   
   


// Function to translate single content using DeepL API with Google Translate fallback
public function translate_content($content, $lang) {
    if (empty($content) || trim($content) === '') {
        return $content;
    }

    $api_key = $this->config->item('deepl_api_key');
    if (empty($api_key) && defined('DEEPL_API_KEY')) {
        $api_key = DEEPL_API_KEY;
    }
    if (empty($api_key)) {
        $api_key = getenv('DEEPL_API_KEY');
    }
    $api_key = trim((string)$api_key);

    $is_free_key = (substr($api_key, -3) === ':fx');
    $url = $is_free_key ? 'https://api-free.deepl.com/v2/translate' : 'https://api.deepl.com/v2/translate';

    $data = array(
        "text" => [$content],
        "target_lang" => $lang,
        "tag_handling" => "html"
    );

    // DeepL only supports formality for specific languages (e.g. DE, FR, IT, ES, PT, RU). HI and ZH return HTTP 400 if formality is sent.
    $supports_formality = in_array(strtoupper(explode('-', $lang)[0]), ['DE', 'FR', 'IT', 'ES', 'PT', 'RU', 'NL', 'PL', 'JA']);
    if ($supports_formality) {
        $data["formality"] = "less";
    }

    if (!empty($api_key) && function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: DeepL-Auth-Key " . $api_key
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result !== false && $http_code == 200) {
            $response = json_decode($result, true);
            if (isset($response['translations'][0]['text'])) {
                return $response['translations'][0]['text'];
            }
        } else {
            log_message('error', "DeepL single translate news failed (HTTP {$http_code}): " . substr((string)$result, 0, 200));
        }
    } elseif (!empty($api_key)) {
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n" .
                             "Authorization: DeepL-Auth-Key " . $api_key . "\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'timeout' => 20
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        );
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result !== false) {
            $response = json_decode($result, true);
            if (isset($response['translations'][0]['text'])) {
                return $response['translations'][0]['text'];
            }
        }
    }

    // Fallback to Google Translate if DeepL is unavailable or quota exceeded
    $fallback = $this->translate_via_google($content, $lang);
    if (!empty($fallback)) {
        return $fallback;
    }

    return false;
}

// Function to translate using Google Translate free API as a reliable fallback
public function translate_via_google($text, $lang_code) {
    if (empty($text) || trim($text) === '') {
        return $text;
    }

    $map = [
        'FR'      => 'fr',
        'DE'      => 'de',
        'IT'      => 'it',
        'ES'      => 'es',
        'HI'      => 'hi',
        'RU'      => 'ru',
        'PT'      => 'pt',
        'PT-BR'   => 'pt',
        'ZH'      => 'zh-CN',
        'ZH-HANS' => 'zh-CN',
    ];

    $target = isset($map[strtoupper($lang_code)]) ? $map[strtoupper($lang_code)] : strtolower($lang_code);
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . urlencode($target) . "&dt=t&q=" . urlencode($text);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code == 200 && !empty($res)) {
            $arr = json_decode($res, true);
            if (isset($arr[0]) && is_array($arr[0])) {
                $out = '';
                foreach ($arr[0] as $segment) {
                    if (isset($segment[0])) {
                        $out .= $segment[0];
                    }
                }
                if (!empty($out)) {
                    return $out;
                }
            }
        } else {
            log_message('error', "Google translate fallback failed for {$lang_code} (HTTP {$code})");
        }
    }

    return false;
}

// Function to translate news title and content into multiple languages in parallel
public function translate_news_fields($title, $content, array $target_langs = []) {
    if (empty($target_langs)) {
        $target_langs = [
            'french'     => 'FR',
            'german'     => 'DE',
            'italian'    => 'IT',
            'spanish'    => 'ES',
            'hindi'      => 'HI',
            'russian'    => 'RU',
            'portuguese' => 'PT',
            'mandarin'   => 'ZH'
        ];
    }

    $api_key = $this->config->item('deepl_api_key');
    if (empty($api_key) && defined('DEEPL_API_KEY')) {
        $api_key = DEEPL_API_KEY;
    }
    if (empty($api_key)) {
        $api_key = getenv('DEEPL_API_KEY');
    }
    $api_key = trim((string)$api_key);

    $is_free_key = (substr($api_key, -3) === ':fx');
    $url = $is_free_key ? 'https://api-free.deepl.com/v2/translate' : 'https://api.deepl.com/v2/translate';

    $results = [];
    foreach ($target_langs as $lang_key => $lang_code) {
        $results[$lang_key . '_title'] = $title;
        $results[$lang_key . '_content'] = $content;
    }

    if (empty($title) && empty($content)) {
        return $results;
    }

    if (!empty($api_key) && function_exists('curl_multi_init')) {
        $mh = curl_multi_init();
        $curl_handles = [];

        foreach ($target_langs as $lang_key => $lang_code) {
            $payload = [
                "text" => [(string)$title, (string)$content],
                "target_lang" => $lang_code,
                "tag_handling" => "html"
            ];

            $supports_formality = in_array(strtoupper(explode('-', $lang_code)[0]), ['DE', 'FR', 'IT', 'ES', 'PT', 'RU', 'NL', 'PL', 'JA']);
            if ($supports_formality) {
                $payload["formality"] = "less";
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: DeepL-Auth-Key " . $api_key
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_multi_add_handle($mh, $ch);
            $curl_handles[$lang_key] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh, 0.1);
        } while ($running > 0);

        foreach ($curl_handles as $lang_key => $ch) {
            $res = curl_multi_getcontent($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $got_translation = false;

            if ($code == 200 && !empty($res)) {
                $resp = json_decode($res, true);
                if (isset($resp['translations'][0]['text']) && !empty($resp['translations'][0]['text'])) {
                    $results[$lang_key . '_title'] = $resp['translations'][0]['text'];
                    $got_translation = true;
                }
                if (isset($resp['translations'][1]['text']) && !empty($resp['translations'][1]['text'])) {
                    $results[$lang_key . '_content'] = $resp['translations'][1]['text'];
                }
            } else {
                log_message('error', "DeepL multi-translate news failed for {$lang_key} (HTTP {$code}): " . substr((string)$res, 0, 200));
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            // If DeepL was not successful (e.g. quota exceeded code 456), fallback to Google Translate
            if (!$got_translation) {
                $lang_code = $target_langs[$lang_key];
                $fallback_title = $this->translate_via_google($title, $lang_code);
                if (!empty($fallback_title)) {
                    $results[$lang_key . '_title'] = $fallback_title;
                }
                $fallback_content = $this->translate_via_google($content, $lang_code);
                if (!empty($fallback_content)) {
                    $results[$lang_key . '_content'] = $fallback_content;
                }
            }
        }
        curl_multi_close($mh);
    } else {
        // Fallback to sequential translation if curl_multi is not available
        foreach ($target_langs as $lang_key => $lang_code) {
            $tr_title = $this->translate_content($title, $lang_code);
            if ($tr_title !== false && !empty($tr_title)) {
                $results[$lang_key . '_title'] = $tr_title;
            }
            $tr_content = $this->translate_content($content, $lang_code);
            if ($tr_content !== false && !empty($tr_content)) {
                $results[$lang_key . '_content'] = $tr_content;
            }
        }
    }

    return $results;
}

}




