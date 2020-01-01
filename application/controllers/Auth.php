<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('encryption');
    }
    public function index()
    {
        if ($this->session->userdata('username')) {
            redirect('user');
        }
        $this->form_validation->set_rules('hostname', 'Hostname', 'trim|required');
        $this->form_validation->set_rules('pass1', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'User Login';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            $this->_login();
        }
    }

    public function postCURL($_url, $_param)
    {
        $data_string = json_encode($_param);
        $ch = curl_init($_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result, true);
        return $result;
    }

    private function _login()
    {
        $host = $this->input->post('hostname');
        $psswd = $this->input->post('pass1');
        $user = $this->db->get_where('nbu_user', ['hostname' => $host])->row_array();
        $hostname = $user['hostname'];
        $port = $user['port'];
        $basepath = "https://$hostname:$port/netbackup";
        $uri = "$basepath/login";
        $params = array(
            "userName" => $user['username'],
            "password" => $user['password'],
        );

        if ($user) {
            # code... if user active
            if ($user['is_active'] == 1) {
                # code... cek password
                if ($psswd == $user['password']) {
                    # code...
                    $data = [
                        'hostname' => $user['hostname'],
                        'role_id' => $user['role_id']
                    ];

                    $this->session->set_userdata($data);
                    $this->db->set('last_login', 'current_login', false);
                    $this->db->where('hostname', $user['hostname']);
                    $result = $this->postCURL($uri, $params);

                    $data1 = [
                        'current_login' => time(),
                        'token' => $result['token']
                    ];
                    $this->db->update('nbu_user', $data1);

                    if ($user['role_id'] == 1) {
                        # code...
                        redirect('nbuadmin');
                    } else {
                        redirect('user');
                    }
                } else {
                    # code...
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" roles="alert">The password entered is invalid!</div>');
                    redirect('auth');
                }
            } else {
                # code... error active
                $this->session->set_flashdata('message', '<div class="alert alert-danger" roles="alert">This email has not been activated</div>');
                redirect('auth');
            }
        } else {
            # code...
            $this->session->set_flashdata('message', '<div class="alert alert-danger" roles="alert">The email entered is invalid</div>');
            redirect('auth');
        }
    }

    public function registration()
    {
        if ($this->session->userdata('username')) {
            redirect('user');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[nbu_user.username]', [
            'is_unique' => 'The username you entered is currently registered'
        ]);
        $this->form_validation->set_rules('hostname', 'Hostname', 'required|trim|is_unique[nbu_user.hostname]', [
            'is_unique' => 'The hostname you entered is currently registered'
        ]);
        $this->form_validation->set_rules('port', 'Port', 'required');
        $this->form_validation->set_rules('token', 'Token', 'required');
        $this->form_validation->set_rules('pass1', 'Password', 'required|trim|min_length[8]|matches[repass1]', [
            'matches' => 'Password does not match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('pass1', 'Password', 'required|trim|min_length[8]|matches[repass1]', [
            'matches' => 'Password does not match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('repass1', 'Password', 'required|trim|matches[pass1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                'username' => htmlspecialchars($this->input->post('username', true)),
                'password' => htmlspecialchars($this->input->post('pass1', true)),
                'token' => htmlspecialchars($this->input->post('token', true)),
                'hostname' => htmlspecialchars($this->input->post('hostname', true)),
                'port' => htmlspecialchars($this->input->post('port', true)),
                'role_id' => 1,
                'is_active' => 1,
                'date_created' => time(),
                'last_login' => time(),
                'current_login' => time()
            ];
            $this->db->insert('nbu_user', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" roles="alert"> Conratulation!, your account has been created. Please Login</div>');
            redirect('auth');
        }
    }
    public function logout()
    {
        # code...
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');
        $this->session->set_flashdata('message', '<div class="alert alert-success" roles="alert">You have been logout</div>');
        redirect('auth');
    }
    public function blocked()
    {
        # code...
        $data['title'] = '404';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('auth/blocked',);
        $this->load->view('templates/footer');
    }
}
