<?php
require_once(APPPATH . 'libraries'. DIRECTORY_SEPARATOR . 'REST_Controller.php');

defined('BASEPATH') OR exit('No direct script access allowed');

class Api_helper{

	public function __construct() { // override to auto-load model / libraries
		$this->ci = &get_instance();
	}
    
    public function index() {
        
    }
	
	public function token_get() {
		$csrf_token = $this->ci->security->get_csrf_token_name();
		$csrf_hash = $this->ci->security->get_csrf_hash();
		
		$token = ['csrf_token'=>$csrf_token,'csrf_hash'=>$csrf_hash];
		return $token;
	}
	
    
}
