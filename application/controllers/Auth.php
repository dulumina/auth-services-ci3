<?php 
        
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Auth extends RestController {

    protected $access_key='ACCESS_KEY';
    protected $refresh_key='REFRESH_KEY';
	function __construct()
    {
        parent::__construct();
    }

    public function login_post()
    {
        $data=array(
        );
        $this->response([
            'status' => TRUE,
            'access_token' => $this->access_token($this->input->post('userlogin')),
            'refresh_token' => $this->refresh_token($this->input->post('userlogin'))
        ], RestController::HTTP_OK); 
    }
        
    public function renew_token_post()
    {
        try {
            $decoded = JWT::decode($this->input->post('refresh_token'), $this->refresh_key, array('HS256'));
    
            $this->response( [
                'status' => true,
                'access_token' => $this->access_token($decoded->userlogin),
                'refresh_token' => $this->refresh_token($decoded->userlogin)
            ], 200 );
        } catch (Exception $e){
            if($e->getMessage() == "Expired token"){
                $this->response( [
                    'status' => false,
                    'message' => 'expired token'
                ], 401 );
            } else {
                $this->response( [
                    'status' => false,
                    'message' => 'Access denied',
                    'error' => $e->getMessage()
                ], 401 );
            }
        }
    }

    private function access_token($userlogin)
    {
        $payload = array(
            "userlogin" => $userlogin,
            "iat" => time(),
            "exp" => strtotime("+1 hour")
        );

        $jwt = JWT::encode($payload, $this->access_key);

        return $jwt;
    }

    private function refresh_token($userlogin)
    {
        $payload = array(
            "userlogin" => $userlogin,
            "iat" => time(),
            "exp" => strtotime("+7 days")
        );

        $jwt = JWT::encode($payload, $this->refresh_key);

        return $jwt;
    }
    
}


    /* End of file  api.php */
