<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }
    public function postCURL($_url, $_param)
    {
        $data_string = json_encode($_param);
        $ch = curl_init($_url);
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
    public function getCURL($_url, $_token)
    {
        $token = $_token;
        $pagination = array(
            'page' =>
            array(
                'limit' => '100',
            ),
        );
        $data_string = json_encode($pagination);
        $query = urlencode("page[limit]=100");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url . $query);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization:' . $token));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        $result = json_decode($result, true);
        curl_close($ch);
        return $result;
    }
    public function index()
    {
        $data['title'] = 'Dashboard';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        $data['title'] = 'Role Management';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $data['role'] = $this->db->get('user_role')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role', $data);
        $this->load->view('templates/footer');
    }
    public function roleaccess($role_id)
    {
        $data['title'] = 'Edit Role Access';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $data['role'] = $this->db->get_where('user_role', ['id' => $role_id])->row_array();

        $this->db->where('id !=', 1);

        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/roleaccess', $data);
        $this->load->view('templates/footer');
    }
    public function changeaccess()
    {
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');

        $data = [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ];

        $result = $this->db->get_where('user_access_menu', $data);

        if ($result->num_rows() < 1) {
            # code...
            $this->db->insert('user_access_menu', $data);
        } else {
            $this->db->delete('user_access_menu', $data);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success" roles="alert">Access Changed!</div>');
    }

    public function jobs()
    {
        $data['title'] = 'Activity Monitor';
        $data['user'] = $this->db->get_where('nbu_user', ['hostname' =>
        $this->session->userdata('hostname')])->row_array();
        $user = $this->db->get_where('nbu_user', ['hostname' => $this->session->userdata('hostname')])->row_array();
        $data['role'] = $this->db->get('user_role')->result_array();
        $token = $user['token'];
        $hostname = $user['hostname'];
        $port = $user['port'];
        $basepath = "https://$hostname:$port/netbackup";
        $uri = "$basepath/admin/jobs";
        $data['result'] = $this->getCURL($uri, $token);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        var_dump($data['result']);
        // $this->load->view('admin/jobs', $data);
        $this->load->view('templates/footer');
    }
}
