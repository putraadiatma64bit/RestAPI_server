<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Welcome extends REST_Controller 
{
    private $url_auth = "http://www.inspibook.com/wam/admin/auth/index";    
    private $key_auth = "f67d9380e2bdc74223c9f368d078e9be"; 

    private $username = "asdi";
    private $password = "~!@%^&*";

    private $key_client = "123";

    public function __construct()
    {        
        parent::__construct();   

        $data = $this->request_key(getallheaders(),$this->username,$this->password,$this->key_client);
        $auth = json_decode($data);
        if(($auth->auth_server != $this->key_auth)||($auth->auth_client != sha1($this->key_client))) exit;
    }
    //----------------------------------------- 
    private function postdata($url_auth,$key_server,$key_client)
    {
        $postdata = http_build_query(
            array(
                'key_server' => $key_server,
                'key_client' => $key_client                 
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);

        $result = file_get_contents($url_auth, false, $context);

        return $result;
    }
    
    private function request_key($data,$username,$password,$key_client)
    {       
        $auth = explode('-',$data['auth']);
        if(($auth[0] == $username)&&($auth[1] == $password))
        {
            return $this->postdata($this->url_auth,$auth[2],$key_client);
        }
    }
    //----------------------------------------- 
    public function index_get()
    {
        if ($this->uri->segment(3) == "auth")
        {            
           echo $this->get('id');
        }    
    }
    public function index_post()
    {
        if ($this->uri->segment(3) == "auth")
        {            
           	echo $this->post('username');
           	echo $this->post('password');           
        } 
        else
        if ($this->uri->segment(3) == "raw")
        {            
            $json = file_get_contents("php://input");   
            echo $json;              
        } 		  
    }
    public function index_put()
    {
        if ($this->uri->segment(3) == "auth")
        {            
           	echo $this->put('username');
            echo $this->put('password');         
        }
    }
    public function index_delete()
    {
        if ($this->uri->segment(3) == "auth")
        {                
           echo $this->delete('id');
    	}
    }
}
