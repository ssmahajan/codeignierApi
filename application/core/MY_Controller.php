<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once APPPATH . 'libraries/jwt/vendor/autoload.php';
/**
 * Class MY_Controller
 */
class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
}

class MYAPI_Controller extends MY_Controller
{

    const SECRET = 'd87da62c4fdw193ecddeq6ac043qwd5a56542bf2c7eaf';
    const SECRET_REFRESH = 'd87da6d2c4fd193eopqwjfiwqpefui0q3fdduixefpq9e090re0f90f9ew0feafekfheihfiqf0e0efu0fejoejfkeqjfhkeh';
    private $_jwt;
    public $error_details=array();
    /**
     * constructer
     */
    public function __construct()
    {
        //error_reporting(0);
        //ini_set("display_errors", 0);
        parent::__construct();
        if(ENVIRONMENT!='production'){
            header('Access-Control-Allow-Origin: *');//will change on production mode
        }else{
            header('Access-Control-Allow-Origin: '.base_url());//will change on production mode
        }
        $this->load->model('user_model');
        if ($this->router->method != 'login' && $this->router->method != 'refreshToken' && $this->router->method != 'verifyEmail' && $this->router->method != 'register') {
            $headers = $this->input->request_headers();
            $authToken = '';
            $refreshToken = '';
            foreach ($headers as $name => $value) {
                if (strtolower($name) == 'x-auth') {
                    $authToken = $value;
                }
            }
            $this->_jwt = $this->authCheck($authToken);

            if (!$this->_jwt) {
                header('Content-Type: application/json');
                http_response_code(401);
                die(json_encode(array('success' => false, 'code' => 'auth_failed',
                    'message' => 'Your login has expired, please try logging in again.',
                    'error_details'=>$this->error_details)));
            } else {
                //$user_id = $this->_jwt->user_id;
                // if(!empty($user_id)){
                /*$result = $this->user_model->checkSubscriptionPlan($user_id);
                if($result){
                    //print_r($result);
                    // exit;
                    if($result['purchased']==1){
                        echo json_encode(['success'=>1,'subscription'=>1,'status'=>'purchased']);
                        return true;
                    }elseif($result['purchased']==0 && $result['message']=='free'){
                        echo json_encode(['success'=>1,'subscription'=>0,'status'=>'free_trial']);
                        return true;
                    }elseif ($result['purchased']==0 && $result['message']=='expired') {
                        echo json_encode(['success'=>0,'subscription'=>0,'status'=>'expired']);
                        return false;
                    }
                    //echo json_encode($result);
                }*/

                // }
            }
        }
    }

    /**
     * Authorization authcheck()
     * @param jwt token
     * @return
     */
    private function authCheck($jwt) {
        $clientToken = $jwt;
        if (strpos($clientToken, 'Bearer ') !== false) {
            $clientToken = str_replace('Bearer ', '', $clientToken);
            $decode = '';
            try {
                $decode = \Firebase\JWT\JWT::decode($clientToken, self::SECRET, array('HS256'));
                $decode = $decode->user_data;
                //print_r($decode); die;
            } catch (Exception $e) {
                $this->error_details[]=$e->getMessage();
                //echo json_encode(array('success' => false, 'message' => 'Invalid token.'));
                return false;
            }
            return $decode;
        }
    }
}
