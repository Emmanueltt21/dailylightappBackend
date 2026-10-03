<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class Devotionals_model extends CI_Model{
    public $status = 'error';
    public $message = 'Something went wrong';
    public $data = [];
    public $date = "";

    function __construct(){
       parent::__construct();
	  }

    function getDevotional($date=""){
        $this->db->select('tbl_devotionals.*');
        $this->db->from('tbl_devotionals');
        $this->db->where('date', $date);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_total_views($id){
      //update total views on media
      $this->db->set('views_count', '`views_count`+ 1', false);
      $this->db->where('id' , $id);
      $this->db->update('tbl_devotionals');
      $this->status = 'ok';
    }

    function getTotalViews($id){
      $this->db->select('tbl_devotionals.views_count');
      $this->db->from('tbl_devotionals');
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
          $this->db->select('tbl_devotionals.content');
          $this->db->from('tbl_devotionals');
            $this->db->where('tbl_devotionals.id', $id);
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
      $this->db->order_by('date', 'desc');
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


   function adminDevotionalsListing($columnName,$columnSortOrder,$searchValue,$start, $length){
     $this->db->select('tbl_devotionals.*');
     $this->db->from('tbl_devotionals');
     if($searchValue!=""){
         $this->db->like('title', $searchValue);
         $this->db->or_like('content', $searchValue);
     }
     if($columnName!=""){
        $this->db->order_by($columnName, $columnSortOrder);
     }else{
       $this->db->order_by("date", "DESC");
     }
     $this->db->limit($length,$start);
     $query = $this->db->get();
     return $query->result();
   }

   public function get_total_devotionals($searchValue=""){
     if($searchValue==""){
       $query = $this->db->select("COUNT(*) as num")->get("tbl_devotionals");
     }else{
       $this->db->select("COUNT(*) as num");
       $this->db->from('tbl_devotionals');
       $this->db->join('tbl_rss_urls','tbl_rss_urls.id = tbl_devotionals.channel');
       $this->db->like('title', $searchValue);
       $this->db->or_like('content', $searchValue);
       $query = $this->db->get();
     }
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
  }

   function checkDevotionalExists($date, $id = 0)
   {
       $this->db->select("title");
       $this->db->from("tbl_devotionals");
       $this->db->where("date", $date);
       if($id != 0){
           $this->db->where("id !=", $id);
       }
       $query = $this->db->get();

       return $query->result();
   }


   function addNewDevotional($info)
   {
     $insert_id = 0;
     if(empty($this->checkDevotionalExists($info['date']))){
       $this->db->trans_start();
       $this->db->insert('tbl_devotionals', $info);
       $insert_id = $this->db->insert_id();
       $this->db->trans_complete();
       $this->status = 'ok';
       $this->message = 'Devotional added successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Devotional already added for this date '.$info['date'];
     }
     return $insert_id;
   }



   function addNewDevotionalOLDBK($info)
   {
     $insert_id = 0;
     if(empty($this->checkDevotionalExists($info['date']))){
         
         $date = $info['date'];
         $title = $info['title'];
         $bible_reading = $info['bible_reading'];
         $studies = $info['studies'];
         $confession = $info['confession'];
         $content = $info['content'];
         
         $french_content = $title .$bible_reading .$studies . $confession . $content ;
         $german_content = $title .$bible_reading .$studies . $confession . $content ;
         
         $french_version = $this->translate_content($french_content, 'FR');
         $german_version = $this->translate_content($german_content, 'DE');
         
         
         
         
         $infotrans = array(
									'date' => $date,
									'title' => $title,
									'author' => $author,
									'bible_reading' => $bible_reading,
									'studies' => $studies,
									'confession' => $confession,
									'content' => $content,
									'french_content' => $french_version,
									'german_content' => $german_version
									
								//	'french_content' => 'french_version',
					            //	'german_content' => 'german_version'
							);
         
         
         
       $this->db->trans_start();
       $this->db->insert('tbl_devotionals', $infotrans);
       $insert_id = $this->db->insert_id();
       $this->db->trans_complete();
       $this->status = 'ok';
       //$this->message = 'Devotional added successfully'.$translatedGerman_content. $translatedFrench_content;
       $this->message = 'Devotional added successfully' . $french_version . $german_version;
     }else{
       $this->status = 'error';
       $this->message = 'Devotional already added for this date '.$info['date'];
     }
     return $insert_id;
   }

 function editDevotional($info, $id){
     if(empty($this->checkDevotionalExists($info['date'],$id))){
       $this->db->where('id', $id);
       $this->db->update('tbl_devotionals', $info);
       $this->status = 'ok';
       $this->message = 'Devotional edited successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Date for this devotional already exists for another';
     }
   }
   

   function editDevotionalEDITEST($info, $id){
     if(empty($this->checkDevotionalExists($info['date'],$id))){
         
         $date = $info['date'];
         $title = $info['title'];
         $bible_reading = $info['bible_reading'];
         $studies = $info['studies'];
         $confession = $info['confession'];
         $content = $info['content'];
         
         $french_content = $title .$bible_reading .$studies . $confession . $content ;
         $german_content = $title .$bible_reading .$studies . $confession . $content ;
         
         $translatedFrench_content = $this->translate_content($french_content, 'FR');
         $translatedGerman_content = $this->translate_content($german_content, 'DE');
         
        
         
        
         	$info1 = array(
									'date' => $date,
									'title' => $title,
									'author' => $author,
									'bible_reading' => $bible_reading,
									'studies' => $studies,
									'confession' => $confession,
									'content' => $content,
									'french_content' => $translatedFrench_content,
					            	'german_content' => $translatedGerman_content
							);
							
							
         
       $this->db->where('id', $id);
       $this->db->update('tbl_devotionals', $info1);
       
     //  $testData = "This is a Test File in English";
     //  $translated_content = $this->translate_content($testData);
       
       
       
       
       $this->status = 'ok';
       $this->message = 'Devotional edited successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Date for this devotional already exists for another';
     }
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
            log_message('error', "DeepL single translate failed (HTTP {$http_code}): " . substr((string)$result, 0, 200));
        }
    }

    // Fallback to Google Translate if DeepL is unavailable or quota exceeded
    $fallback = $this->translate_via_google($content, $lang);
    if (!empty($fallback)) {
        return $fallback;
    }

    return false;
}

// Function to translate using Google Translate free API with multi-endpoint fallback
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

    // Try Endpoint 1: clients5.google.com
    $url1 = "https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl=auto&tl=" . urlencode($target) . "&q=" . urlencode($text);
    if (function_exists('curl_init')) {
        $ch = curl_init($url1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code == 200 && !empty($res)) {
            $arr = json_decode($res, true);
            if (isset($arr[0][0]) && !empty($arr[0][0])) {
                return $arr[0][0];
            }
        }

        // Try Endpoint 2: translate.googleapis.com
        $url2 = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . urlencode($target) . "&dt=t&q=" . urlencode($text);
        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        $res2 = curl_exec($ch2);
        $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        if ($code2 == 200 && !empty($res2)) {
            $arr2 = json_decode($res2, true);
            if (isset($arr2[0]) && is_array($arr2[0])) {
                $out = '';
                foreach ($arr2[0] as $segment) {
                    if (isset($segment[0])) {
                        $out .= $segment[0];
                    }
                }
                if (!empty($out)) {
                    return $out;
                }
            }
        } else {
            log_message('error', "Google translate fallback failed for {$lang_code} (HTTP {$code2})");
        }
    }

    return false;
}

// Function to translate all devotional fields in parallel using Google Translate
public function translate_devotional_fields_via_google_parallel($title, $bible_reading, $content, $confession, $studies, array $target_langs) {
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

    $fields = [
        'title'         => $title,
        'bible_reading' => $bible_reading,
        'content'       => $content,
        'confession'    => $confession,
        'studies'       => $studies
    ];

    $results = [];

    if (!function_exists('curl_multi_init')) {
        foreach ($target_langs as $lang_key => $lang_code) {
            foreach ($fields as $field_key => $text) {
                $results[$lang_key . '_' . $field_key] = $this->translate_via_google($text, $lang_code) ?: $text;
            }
        }
        return $results;
    }

    $mh = curl_multi_init();
    $handles = [];

    foreach ($target_langs as $lang_key => $lang_code) {
        $target = isset($map[strtoupper($lang_code)]) ? $map[strtoupper($lang_code)] : strtolower($lang_code);
        foreach ($fields as $field_key => $text) {
            $res_key = $lang_key . '_' . $field_key;
            $results[$res_key] = $text; // default fallback

            if (empty($text) || trim($text) === '') {
                continue;
            }

            $url = "https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl=auto&tl=" . urlencode($target) . "&q=" . urlencode($text);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_multi_add_handle($mh, $ch);
            $handles[$res_key] = $ch;
        }
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh, 0.05);
    } while ($running > 0);

    foreach ($handles as $res_key => $ch) {
        $res = curl_multi_getcontent($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 200 && !empty($res)) {
            $arr = json_decode($res, true);
            if (isset($arr[0][0]) && !empty($arr[0][0])) {
                $results[$res_key] = $arr[0][0];
            }
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);

    return $results;
}

// Function to translate all devotional fields into multiple languages in parallel
public function translate_devotional_fields($title, $bible_reading, $content, $confession, $studies, array $target_langs = []) {
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
        $results[$lang_key . '_bible_reading'] = $bible_reading;
        $results[$lang_key . '_content'] = $content;
        $results[$lang_key . '_confession'] = $confession;
        $results[$lang_key . '_studies'] = $studies;
    }

    if (empty($title) && empty($content)) {
        return $results;
    }

    $missing_langs = [];

    if (!empty($api_key) && function_exists('curl_multi_init')) {
        $mh = curl_multi_init();
        $curl_handles = [];

        foreach ($target_langs as $lang_key => $lang_code) {
            $payload = [
                "text" => [
                    (string)$title,
                    (string)$bible_reading,
                    (string)$content,
                    (string)$confession,
                    (string)$studies
                ],
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
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
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
                if (isset($resp['translations']) && count($resp['translations']) >= 5) {
                    $results[$lang_key . '_title'] = $resp['translations'][0]['text'];
                    $results[$lang_key . '_bible_reading'] = $resp['translations'][1]['text'];
                    $results[$lang_key . '_content'] = $resp['translations'][2]['text'];
                    $results[$lang_key . '_confession'] = $resp['translations'][3]['text'];
                    $results[$lang_key . '_studies'] = $resp['translations'][4]['text'];
                    $got_translation = true;
                }
            } else {
                log_message('error', "DeepL multi-translate failed for {$lang_key} (HTTP {$code}): " . substr((string)$res, 0, 200));
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if (!$got_translation) {
                $missing_langs[$lang_key] = $target_langs[$lang_key];
            }
        }
        curl_multi_close($mh);
    } else {
        $missing_langs = $target_langs;
    }

    // Parallel Google Translate fallback for all languages that failed on DeepL
    if (!empty($missing_langs)) {
        $google_translations = $this->translate_devotional_fields_via_google_parallel(
            $title, $bible_reading, $content, $confession, $studies, $missing_langs
        );
        foreach ($google_translations as $field_name => $trans_value) {
            if (!empty($trans_value)) {
                $results[$field_name] = $trans_value;
            }
        }
    }

    return $results;
}



   function editDevotionalBackUP($info, $id){
     if(empty($this->checkDevotionalExists($info['date'],$id))){
       $this->db->where('id', $id);
       $this->db->update('tbl_devotionals', $info);
       $this->status = 'ok';
       $this->message = 'Devotional edited successfully';
     }else{
       $this->status = 'error';
       $this->message = 'Date for this devotional already exists for another';
     }
   }


   function getDevotionalInfo($id)
   {
     $this->db->select('tbl_devotionals.*');
     $this->db->from('tbl_devotionals');
       $this->db->where('tbl_devotionals.id', $id);
       $query = $this->db->get();
       $row = $query->row();
       if(count((array)$row) > 0 && $row->thumbnail!="" && !$this->isValidURL($row->thumbnail)){
           
         $row->thumbnail = $this->base_url().$this->get_thumbnail_source($row->thumbnail);
        // $row->thumbnail = base_url()."uploads/thumbnails/".$row->thumbnail;
       }
       return $row;
   }


   function deleteDevotional($id){
       $this->db->where('id', $id);
       $this->db->delete('tbl_devotionals');
       $this->status = 'ok';
       $this->message = 'Devotional deleted successfully.';
   }


  function delete_old_articles()
  {
    $date = date("Y-m-d", strtotime('-7 day'));
    $this->db->where('dateInserted < ', $date);
    $this->db->delete('tbl_devotionals');
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
 
 	     
   private function get_thumbnail_source($thumbnail){
       if($this->isValidURL($thumbnail)){
         return $thumbnail;
       }
       return site_url()."uploads/thumbnails/".$thumbnail;
   }
   
    public function base_url(){
        if (strpos(site_url(), 'http://') === 0 || strpos(site_url(), 'https://') === 0) {
            return "";
        }
        return "http://".$_SERVER['HTTP_HOST'];
    }
	
}
