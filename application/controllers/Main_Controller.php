<?php
require_once(APPPATH . 'libraries'. DIRECTORY_SEPARATOR . 'REST_Controller.php');

defined('BASEPATH') OR exit('No direct script access allowed');

class Main_Controller extends REST_Controller {
	public $except = array();

	function __construct() {
        // Construct the parent class
        parent::__construct();
        
    }

    public function object_get($object_key = null)
    {
        // print_r(strtotime('2021-02-28 13:53:03'));exit();

        
        //load model
        $this->load->model('Main_model', 'mainModel');

        if(empty($object_key)){
            $this->response(array(
                "success" => FALSE,
                "message" => "Please pass in your object key and try again!"
            ));
            exit();
        }

        if($this->get()){
            $object_timestamp = !empty($this->get('timestamp')) ? date("Y-m-d H:m:s", $this->get('timestamp')) : null;
            // print_r($this->get('timestamp'));exit();
            // print_r($object_timestamp);exit();

            if($object_timestamp){
                if($object_timestamp){
                    $result = $this->mainModel->get_object_by_key($object_key,$object_timestamp);
                }
                else{
                    $this->response(array(
                        "success" => FALSE,
                        "message" => "Action failed. Please try again."
                    ));
                }
            }
            else{
                $result = $this->mainModel->get_object_by_key($object_key);
            }
            
        }
        else{
            if(!empty($object_key)){
                $result = $this->mainModel->get_object_by_key($object_key);
            }
            $this->response(array(
                "success" => FALSE,
                "message" => "Please try again."
            ));
        }


        if($result){
            $this->response(array(
                "success" => TRUE,
                "data" => $result,
                "message" => "Success!"
            ));
        }
        else{
            $this->response(array(
                "success" => FALSE,
                "message" => "Object not found. Please try again."
            ));

        }

    }


    public function object_post()
    {
        //load model
        $this->load->model('Main_model', 'mainModel');

        if ($this->post()){
            $post = $this->post();
            $json = (array)$post;
            
            foreach($json as $key => $val) {
                $new_object = new Main_model();
                $new_object->object_key = $key;
                $new_object->object_value = $val;
                $this->mainModel->save($new_object);
            }
            

        }
        else{
            $this->response(array(
				"success" => FALSE,
				"message" => "Please try again."
			));
        }

    }

    public function objects_get(){
        $this->load->model('Main_model', 'mainModel');
        $result = $this->mainModel->get_all_objects();

        if($result){
            $this->response(array(
                "success" => TRUE,
                "data" => $result,
                "message" => "Success!"
            ));
        }
        else{
            $this->response(array(
                "success" => FALSE,
                "message" => "No record found. Please try again later."
            ));
        }
        
    }



}
