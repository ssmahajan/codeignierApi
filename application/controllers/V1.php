<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class V1 extends MYAPI_Controller {

//echo  $generatedKey = sha1(mt_rand(10000,99999).time().'JWREJKJTuIUI734847387483fhJHjdghjwet548fskghjdgh');
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
//        $this->load->view('welcome_message');
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(array('success' => true, 'message' => 'Welcome.'));
        return false;
    }
    /**
     * refreshToken()
     * @return refresh token
     */
    public function refreshToken()
    {
        header('Content-Type: application/json');

        $headers = $this->input->request_headers();
        $authToken = '';
        $refreshToken = '';
//        echo "<pre>";
//        print_r($headers);die;
        foreach ($headers as $name => $value) {
            if (strtolower($name) == 'x-auth') {
                $authToken = $value;
            } else if (strtolower($name) == 'x-refresh') {
                $refreshToken = $value;
            }
        }

        if (empty($authToken) || empty($refreshToken)) {
            http_response_code(401);
            echo json_encode(array('success' => false, 'message' => 'Invalid token.'));
            return;
        }

        if (strpos($authToken, 'Bearer ') !== false) {
            $clientToken = str_replace('Bearer ', '', $authToken);
        } else {
            http_response_code(401);
            echo json_encode(array('success' => false, 'message' => 'Invalid token.'));
            return;
        }

        if (strpos($refreshToken, 'Bearer ') !== false) {
            $clientRefresh = str_replace('Bearer ', '', $refreshToken);
        } else {
            http_response_code(401);
            echo json_encode(array('success' => false, 'message' => 'Invalid token.'));
            return;
        }

       // try {



            try {
                $decode = \Firebase\JWT\JWT::decode($clientToken, self::SECRET, array('HS256'));
                $refreshDecode = \Firebase\JWT\JWT::decode($clientRefresh, self::SECRET_REFRESH, array('HS256'));
//                echo "<pre>";print_r($refreshDecode);die;
                if ($refreshDecode->jwt != $clientToken) {
                    $newToken = \Firebase\JWT\JWT::encode(
                        array(
                            'user_data' => (array)$refreshDecode->user_data,
                            "created" => time(),
                            "exp" => time() + (60 * 60),
                        )
                        , self::SECRET);
                    $newRefresh = $newToken = \Firebase\JWT\JWT::encode(
                        array(
                            'jwt' => $newToken,
                            'user_data' => (array)$refreshDecode->user_data,
                            "created" => time(),
                            "exp" => time() + (60 * 60 * 1.5),
                        )
                        , self::SECRET);

                    http_response_code(200);
                    echo json_encode(
                        array(
                            'success' => true,
                            'jwt' => $newToken,
                            'refresh' => $refreshToken,
                            'email' => $refreshDecode->user_data->email,
                            'user_id' => $refreshDecode->user_data->user_id,
                            'refresh_token_expire' => false
                        ));
                    return;
                } else {
                    http_response_code(401);
                    echo json_encode(array('success' => false, 'message' => 'Invalid token.', 'refresh_token_expire' => true));
                    return;
                }

            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(array('success' => false, 'message' => 'Invalid token.'.$e->getMessage(), 'refresh_token_expire' => true));
                return;
            }

        http_response_code(401);
        echo json_encode(array('success' => false, 'message' => 'Invalid token.'));
        return;

    }
    /**
     * login() function for login with googleid
     *
     */
    public function login(){
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == 'POST'){
            $email = $this->input->post('email') ? trim($this->input->post('email')) :'';
            $password = $this->input->post('password') ? trim($this->input->post('password')) :'';
            $arrayMessages=[];
            if ($email=='') {
                $arrayMessages[]= 'You must enter a email.';
            }else{
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $arrayMessages[]= "  $email is not a valid email address";
                }
            }
            if ($password=='') {
                $arrayMessages[]= 'You must enter a password.';
            }
            if (count($arrayMessages)) {
                echo json_encode(array('success' => false, 'message' => $arrayMessages));
                return;
            }

            /*$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            //$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
            if ($this->form_validation->run() == FALSE){
                $this->load->view('users/login');
            }else{

            }*/
            $result = $this->user_model->loginCheck($email,$password);

            if($result) {
                $jwt = \Firebase\JWT\JWT::encode(
                    array(
                        'user_data' => (array)$result,
                        "created" => time(),
                        "exp" => time() + (60 * 60),
                    )
                    , self::SECRET);

                $refreshJwt = \Firebase\JWT\JWT::encode(
                    array(
                        'jwt' => $jwt,
                        'user_data' => (array)$result,
                        "created" => time(),
                        "exp" => time() + (60 * 60 * 1.5),
                    )
                    , self::SECRET_REFRESH);
                http_response_code(200);
                echo json_encode(
                    array(
                        'success' => true,
                        'jwt' => $jwt,
                        'refresh' => $refreshJwt,
                       'idLevel' => $result->idLevel,
                        'email' => $result->email,
                        'user_data' => $result,
                    ));
            }else{
                http_response_code(200);
                echo json_encode(array('success' => false, 'message' => 'Record Not found or invalid Login Details'));
                return;
            }
        }else{
            http_response_code(405);
            echo json_encode(array('success' => false, 'message' => 'Method Not Allowed'));
            return;
        }
    }

    public function verifyEmail(){
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->post('email') ? trim($this->input->post('email')) :'';
            $arrayMessages=[];
            if ($email=='') {
                $arrayMessages[]= 'You must enter a email.';
            }else{
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $arrayMessages[]= "  $email is not a valid email address";
                }
            }
            if (count($arrayMessages)) {
                echo json_encode(array('success' => false, 'message' => $arrayMessages));
                return;
            }
            $result=$this->user_model->checkEmail($email);
//            echo "<pre>";print_r($this->_jwt);die;
            if($result){
                http_response_code(200);
                echo json_encode(array('success' => true, 'message' => 'Email verified'));
                return;
            }else{
                http_response_code(200);
                echo json_encode(array('success' => false, 'message' => 'Email Not Found'));
                return;
            }

        }else{
            http_response_code(405);
            echo json_encode(array('success' => false, 'message' => 'Method Not Allowed'));
            return;
        }
    }

    public function register(){
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == 'PUT'){

            $firstName=$this->input->post('first_name') ? trim($this->input->post('first_name')) :'';
            $lastName=$this->input->post('last_name') ? trim($this->input->post('last_name')) :'';
            $contactNo=$this->input->post('contact_no') ? trim($this->input->post('contact_no')) :'';
            $email = $this->input->post('email') ? trim($this->input->post('email')) :'';
            $password = $this->input->post('password') ? trim($this->input->post('password')) :'';
            $idLevel = $this->input->post('id_level') ? trim($this->input->post('id_level')) :'';
            $userId = $this->input->post('user_id') ? trim($this->input->post('user_id')) :'';
            $arrayMessages=[];

            if ($firstName=='') {
                $arrayMessages[]= 'You must enter a first name.';
            }
            if ($lastName=='') {
                $arrayMessages[]= 'You must enter a last name.';
            }


            if ($email=='') {
                $arrayMessages[]= 'You must enter a email.';
            }else{
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $arrayMessages[]= "  $email is not a valid email address";
                }
            }
            if ($password=='') {
                $arrayMessages[]= 'You must enter a password.';
            }

            if ($contactNo=='') {
                $arrayMessages[]= 'You must enter a contact number.';
            }
            if ($idLevel=='') {
                $arrayMessages[]= 'You must enter a user id level.';
            }
            if (count($arrayMessages)) {
                echo json_encode(array('success' => false, 'message' => $arrayMessages));
                return;
            }

            $userArray=array("first_name"=>$firstName,"last_name"=>$lastName,"email"=>$email,
                "contact_no"=>$contactNo,"idLevel"=>$idLevel,"status"=>1);
            $checkEmail=$this->user_model->checkEmail($email);


/*print_r($checkEmail);
            $userEmail='';
            if($userId){
                $getUser=$this->user_model->getUsers($userId);
                $userEmail=$getUser[0]["email"];


            }

            if((($userEmail==$email)OR(empty($checkEmail)))AND(!empty($checkEmail))){

                if($userId){
                    $id=array("user_id"=>$userId);
                    $resultResponse=$this->user_model->updateData("users",$userArray,$id);
                    $message="Update";

                }else{
                    $userArray["password"]=$password;
                    $resultResponse=$this->user_model->insertData("users",$userArray);
                    $message="Registration";
                }



                if($resultResponse){
                    http_response_code(200);
                    echo json_encode(array('success' => true, 'message' => 'User $message Successfully'));
                    return;
                }else{
                    http_response_code(200);
                    echo json_encode(array('success' => false, 'message' => 'User Record not Insert '));
                    return;
                }

            }else{
                http_response_code(200);
                echo json_encode(array('success' => false, 'message' => 'Email Already Exists'));
                return;
            }*/


            if($userId){

                $getUser=$this->user_model->getUsers($userId);

                if(($getUser[0]["email"]==$email) OR (empty($checkEmail))){

                    $id=array("user_id"=>$userId);
                    $resultResponse=$this->user_model->updateData("users",$userArray,$id);

                    if($resultResponse){
                        http_response_code(200);
                        echo json_encode(array('success' => true, 'message' => 'User Update Successfully'));
                        return;
                    }else{
                        http_response_code(200);
                        echo json_encode(array('success' => false, 'message' => 'User Record not Update '));
                        return;
                    }

                }else{
                    http_response_code(200);
                    echo json_encode(array('success' => false, 'message' => 'Email Already Exists'));
                    return;
                }
            }else{

                if(!$checkEmail){

                    $userArray["password"]=$password;
                    $resultResponse=$this->user_model->insertData("users",$userArray);
                    if($resultResponse){
                        http_response_code(200);
                        echo json_encode(array('success' => true, 'message' => 'User Registration Successfully'));
                        return;
                    }else{
                        http_response_code(200);
                        echo json_encode(array('success' => false, 'message' => 'User Record not Insert '));
                        return;
                    }


                }else{
                    http_response_code(200);
                    echo json_encode(array('success' => false, 'message' => 'Email Already Exists'));
                    return;
                }
            }

        }else{
            http_response_code(405);
            echo json_encode(array('success' => false, 'message' => 'Method Not Allowed'));
            return;
        }
    }
    public function getUsers($user_id=''){
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $arrayMessages=[];
            $result=$this->user_model->getUsers($user_id);
//            echo "<pre>";print_r($result);die;
            if($result){
                http_response_code(200);
                echo json_encode(array('success' => true, 'users' => $result));
                return;
            }else{
                http_response_code(200);
                echo json_encode(array('success' => false, 'message' => 'Users Not Found'));
                return;
            }

        }else{
            http_response_code(405);
            echo json_encode(array('success' => false, 'message' => 'Method Not Allowed'));
            return;
        }
    }
}
