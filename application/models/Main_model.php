<?php

class Main {
	public $id;
    public $object_key;
    public $object_value;
    public $created_datetime;
    public $updated_datetime;
}

class Main_model extends Base_model {
    private $tableName = 'sl_main';
    private $versioning_tableName = 'sl_versioning';
    
	public function __construct() {
		parent::__construct();
	}

    public function save($object) {

        if(!$this->check_existing($object->object_key)){
            return $this->insert($object);
        }

		$data = array(
            'object_key' => $object->object_key,
            'object_value' => $object->object_value,
            'updated_datetime' => date(MYSQL_DATETIME),
        );
            
        $this->db->where('object_key', $object->object_key);
        if ($this->db->update($this->tableName, $data)) {
            return TRUE;
        }
        
    }

    public function insert($data)
	{
		$data = array(
            'object_key' => $data->object_key,
            'object_value' => $data->object_value,
            'created_datetime' => date(MYSQL_DATETIME),
            'updated_datetime' => NULL,
        );

		$this->db->insert($this->tableName, $data);
		return $this->db->insert_id();
	}

    public function check_existing($object_key){
        $this->db->select('*')
                  ->from($this->tableName)
                  ->where('object_key', $object_key);
        $query = $this->db->get();

        if ($query->num_rows() > 0):
            return TRUE;
        endif;
        
        return FALSE;
    }


    public function get_object_by_key($object_key,$timestamp=NULL) {
        if(!empty($timestamp)){
            $this->db->select('*')
                  ->from($this->versioning_tableName)
                  ->where('object_key', $object_key)
                  ->where('created_datetime', $timestamp)
                  ->order_by('updated_datetime');
        }
        else{
            $this->db->select('*')
                  ->from($this->tableName)
                  ->where('object_key', $object_key)
                  ->order_by('updated_datetime');
        }
        
        $query = $this->db->get();

        if ($query->num_rows() > 0):
            $result = $query->result_object();
            return $result[0];
        endif;
        
        return FALSE;
    }

    public function get_all_objects(){
        $this->db->select('*')
                  ->from($this->versioning_tableName);

        $query = $this->db->get();

        $result_array = array();
        if ($query->num_rows() > 0):
            $results = $query->result_object();
            
            return $results;
        endif;

        return FALSE;
    }

}