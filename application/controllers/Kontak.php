<?php
class Kontak extends CI_Controller
{

    var $API = "";

    function __construct()
    {
        parent::__construct();
        $this->API = "http://192.168.0.105/netbackup";
        $this->load->library('session');
        $this->load->library('curl');
        $this->load->helper('form');
        $this->load->helper('url');
    }

    // menampilkan data kontak
    function index()
    {
        $data['nbuapi'] = json_decode($this->curl->simple_post($this->API . '/login'));
        $this->load->view('', $data);
    }
}
