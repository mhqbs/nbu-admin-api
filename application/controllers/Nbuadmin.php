<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nbuadmin extends CI_Controller
{

    // public function __construct()
    // {
    //     parent::__construct();
    //     is_logged_in();
    // }
    public function postCURL($_url, $_param)
    {
        $data_string = json_encode($_param);
        $ch = curl_init($_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length:' . strlen($data_string)));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result, true);
        return $result;
    }

    public function index()
    {
        $data['title'] = 'Dashboard';

        $data['user'] = $this->db->get_where('nbu_user', ['hostname' =>
        $this->session->userdata('hostname')])->row_array();
        $user1 = $this->db->get_where('nbu_user', ['hostname' => $this->session->userdata('hostname')])->row_array();
        $hostname = $user1['hostname'];
        $port = $user1['port'];
        $basepath = "https://$hostname:$port/netbackup";
        $uri = "$basepath/login";
        $params = array(
            "userName" => $user1['username'],
            "password" => $user1['password'],
        );



        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('nbuadmin/index', $data);
        $this->load->view('templates/footer');
    }
}
