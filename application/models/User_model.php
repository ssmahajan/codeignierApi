<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public $usersTable = 'users';
    public function __construct()
    {
        parent::__construct();
    }
    public function loginCheck($email,$password){
        $result = $this->db->select('*')->from($this->usersTable)
            ->where(['email'=>$email,'password'=>$password])
            ->get()->row();

        if(isset($result) && count((array)$result) > 0){
            $update_data['last_login_at'] = date('Y-m-d h:i:s');
            $update_data['login_count'] = date('Y-m-d h:i:s');
            $this->db->where('user_id',$result->user_id);
            $this->db->update($this->usersTable,$update_data);
        }/*else{
            $insert_data['google_id'] = $google_id;
            $insert_data['created_at'] = date('Y-m-d h:i:s');
            $insert_data['verified_email'] = (int)0;
            $this->db->insert($this->usersTable,$insert_data);
            $result = $insert_data;
        }*/
        return $result;
        /*if($this->db->insert_id()||$this->db->affected_rows()){
            return $this->db->insert_id()||$this->db->affected_rows();
        }*/
    }
    public function checkEmail($email)
    {
        $result = $this->db->select('*')->from($this->usersTable)
            ->where(['email' => $email])
            ->get()->row();
        return $result;
    }
    public function getUsers($user_id='')
    {
         $this->db->select('*')->from($this->usersTable);
            if($user_id!='') {
                $this->db->where(['user_id' => $user_id]);
            }
        $result=$this->db->get()->result_array();
        return $result;
    }
    public function insertData($table,$data){
        return $this->db->insert($table,$data);
    }
    public function updateData($table,$data,$id){
        return $this->db->update($table,$data,$id);
    }
}