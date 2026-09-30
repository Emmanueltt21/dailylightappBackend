<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class Transaction_model extends CI_Model{
    public $status = 'error';
    public $message = 'Error processing requested operation.';
    public $user = "";

    function __construct(){
       parent::__construct();
	  }

    function transactionListingFilter($columnName,$columnSortOrder,$searchValue,$start, $length){
      $this->db->select('tbl_transactions.*');
      $this->db->from('tbl_transactions');
      if($searchValue!=""){
        $this->db->like('internal_id', $searchValue);
        $this->db->or_like('dou', $searchValue);
        $this->db->or_like('uti', $searchValue);
        $this->db->or_like('utimo', $searchValue);
      }
      if($columnName!=""){
         $this->db->order_by($columnName, $columnSortOrder);
      }
      $this->db->limit($length,$start);
      $query = $this->db->get();
      return $query->result();
    }
    
    
   function transactionListing(){
      $this->db->select('tbl_transactions.*');
      $this->db->from('tbl_transactions');
      $columnName ="dou";
      $columnSortOrder ="desc";
      if($columnName!=""){
         $this->db->order_by($columnName, $columnSortOrder);
      }
      //$this->db->limit($length,$start);
      $query = $this->db->get();  
      return $query->result();
    }

 


  public function recordTransactions($ref){
   if($this->verifyPaymentRefExists($ref['uti'],$ref['transaction_id']) == FALSE){
       
     $this->db->trans_start();
     $this->db->insert('tbl_transactions', $ref);
     $this->db->trans_complete();
     $this->status = "ok";
     $this->message = "Donation was done successfully";
     
   }else{
       
     $this->status = "error";
     $this->message = "Cannot record the transaction made at the moment";
     
   }
  }


  function verifyPaymentRefExists($email,$ref)
  {
      $this->db->select('tbl_transactions.id');
      $this->db->from('tbl_transactions');
      $this->db->where('uti',$email);
      $this->db->where('transaction_id',$ref);
      $query = $this->db->get();
      if(count((array)$query->row())>0){
        return TRUE;
      }
      return FALSE;
  }


 public function verifyPaymentRefExists3($ref)
  {
      $this->db->select('tbl_transactions.*');
      $this->db->from('tbl_transactions');
     // $this->db->where('uti',$email);
      $this->db->where('transaction_id',$ref);
     // $this->db->where('status',"1000");
      $query = $this->db->get();  
      if(count((array)$query->row())>0){ 
        return TRUE;
      }
      return FALSE;
  }
  
  
   public function getTransaction($ref)
  {
      $this->db->select('tbl_transactions.*');
      $this->db->from('tbl_transactions');
     // $this->db->where('uti',$email);
      $this->db->where('transaction_id',$ref);
      //$this->db->where('status',"1000");
      $query = $this->db->get();  
      return $query->row();
      
  }

  function updateTransactions($data,$id){
      $this->db->where('id', $id);
      $this->db->update('tbl_transactions', $data);
      $this->status = 'ok';
      $this->message = 'Transaction updated successfully';
  }
  
  
   function updateTransactions2($data,$id){
      $this->db->where('transaction_id', $id);
      $this->db->update('tbl_transactions', $data);
      $this->status = 'ok';
      $this->message = 'Transaction updated successfully';
  }
  
  
       
   function transactionListingAndroidUser($email,$page){
      $this->db->select('tbl_transactions.*');
      $this->db->from('tbl_transactions');
      $columnName ="dou";
      $columnSortOrder ="desc";
      if($columnName!=""){
         $this->db->order_by($columnName, $columnSortOrder);
      }
      $this->db->or_like('email', $email); 
      if($page!=0){
           $this->db->limit(20,$page * 20);
      }else{
          $this->db->limit(20);
      }
      $query = $this->db->get();
      return $query->result();
    }
    
    
    function packListing(){ 
        
      $this->db->select('tbl_pack.*');
      $this->db->from('tbl_pack');
      $columnName ="dou";
      $columnSortOrder ="desc";
      if($columnName!=""){
         $this->db->order_by($columnName, $columnSortOrder);
      }
      //$this->db->limit($length,$start);
      $query = $this->db->get();
      return $query->result();
      
    }
    
    
    
   function adminTransactionsListing($columnName,$columnSortOrder,$searchValue,$start, $length){
     $this->db->select('tbl_transactions.*');
     $this->db->from('tbl_transactions');
     if($searchValue!=""){
         $this->db->like('transaction_id', $searchValue);
         $this->db->like('internal_id', $searchValue);
         $this->db->or_like('option', $searchValue);
         $this->db->or_like('email', $searchValue);
         $this->db->or_like('pack_name', $searchValue);
         $this->db->or_like('status', $searchValue);
         $this->db->or_like('phone', $searchValue);
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

   public function get_total_transactions($searchValue=""){
     if($searchValue==""){
       $query = $this->db->select("COUNT(*) as num")->get("tbl_transactions");
     }else{
       $this->db->select("COUNT(*) as num");
       $this->db->from('tbl_transactions');
   //    $this->db->join('tbl_rss_urls','tbl_rss_urls.id = tbl_transactions.channel');
       $this->db->like('transaction_id', $searchValue);
	   $this->db->like('internal_id', $searchValue);
       $this->db->or_like('option', $searchValue);
       $this->db->or_like('email', $searchValue);
       $this->db->or_like('pack_name', $searchValue);
       $this->db->or_like('status', $searchValue);
       $this->db->or_like('phone', $searchValue);
       $query = $this->db->get();
     }
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
  }

 

}
