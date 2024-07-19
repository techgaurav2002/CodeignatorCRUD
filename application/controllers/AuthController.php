<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->library('form_validation');

    }
    public function index(){
        echo "OK";
    }
    public function register()
    {
        try {
            $postData = json_decode(file_get_contents('php://input'), true);
            print_r($postData);
            if (!isset($postData['name']) || !isset($postData['email']) || !isset($postData['password']) || !isset($postData['phone'])) {
                throw new Exception('Missing required fields');
            }
    
            $this->form_validation->set_data($postData);
    
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('phone', 'Phone', 'required');
    
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                $status_code = 400;
                $this->output->set_status_header($status_code);
                echo json_encode(array('status' => false, 'status_code' => $status_code, 'message' => $errors));
            } else {
                // Manually check if email already exists
                if ($this->Admin_model->email_exists($postData['email'])) {
                    $status_code = 409; // Conflict status code
                    $this->output->set_status_header($status_code);
                    echo json_encode(array('status' => false, 'status_code' => $status_code, 'message' => 'User already exists with this email'));
                    return;
                }
    
                $data = array(
                    'name' => $postData['name'],
                    'email' => $postData['email'],
                    'password' => md5($postData['password']),
                    'phone' => $postData['phone']
                );
    
                if ($this->Admin_model->register($data)) {
                    $status_code = 200;
                    echo json_encode(array('status' => true, 'status_code' => $status_code, 'message' => 'Admin registered successfully'));
                } else {
                    throw new Exception('Failed to register admin');
                }
            }
        } catch (Exception $e) {
            $status_code = 500;
            $this->output->set_status_header($status_code);
            echo json_encode(array('status' => false, 'status_code' => $status_code, 'message' => 'Internal server error'));
        }
    }
    

    public function login()
    {
        try {
            $postData = json_decode(file_get_contents('php://input'), true);
    
            $this->form_validation->set_data($postData);
    
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');
    
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                $this->output->set_status_header(400);
                echo json_encode(array('status' => false, 'message' => $errors));
            } else {
                $email = $postData['email'];
                $password = md5($postData['password']);
    
                $admin = $this->Admin_model->login($email, $password);
    
                if ($admin) {
                    // Generate JWT token
                    $this->load->helper('jwt');
                    $status_code = 200;
                    $token = generateToken(array('email' => $email, 'id' => $admin->admin_id));
    
                    echo json_encode(array('status' => true, 'status_code' => $status_code, 'message' => 'success', 'token' => $token));
                } else {
                    $this->output->set_status_header(401);
                    $status_code = 401;
                    echo json_encode(array('status' => false, 'status_code' => $status_code, 'message' => 'Invalid email or password'));
                }
            }
        } catch (Exception $e) {
            $this->output->set_status_header(500);
            echo json_encode(array('status' => false, 'message' => 'Internal server error'));
        }
    }
    

    
}
?>
