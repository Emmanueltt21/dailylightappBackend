<?php
class Notifications_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Load database
    }

    public function adminDevotionalsListing($columnName, $columnSortOrder, $searchValue, $start, $length) {
        $this->db->select('*');
        $this->db->from('notifications'); // Assuming 'notifications' is your table name

        if(!empty($searchValue)) {
            $this->db->like('title', $searchValue);
        }

        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($length, $start);

        return $this->db->get()->result();
    }

    public function get_total_devotionals($searchValue) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('notifications');

        if(!empty($searchValue)) {
            $this->db->like('title', $searchValue);
        }

        $query = $this->db->get();
        return $query->row()->count;
    }

    public function addNewNotification($data) {
        $this->db->insert('notifications', $data);
        return $this->db->insert_id();
    }

    public function getNotificationInfo($id) {
        return $this->db->get_where('notifications', array('id' => $id))->row();
    }

    public function editNotification($data, $id) {
        $this->db->where('id', $id);
        return $this->db->update('notifications', $data);
    }

    public function deleteNotification($id) {
        return $this->db->delete('notifications', array('id' => $id));
    }
}
