<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");


require_once APPPATH . 'helpers/jwt_helper.php';

class DispatcherController extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->model('Dispatcher_model');
        $this->load->library('form_validation');
    }

    private function verify_token() {
        $headers = $this->input->request_headers();
        if (!isset($headers['Authorization'])) {
            $this->output->set_status_header(401);
            echo json_encode(array('status' => false, 'message' => 'Unauthorized access'));
            exit();
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded_token = validateToken($token);
        
        if ($decoded_token === null) {
            $this->output->set_status_header(401);
            echo json_encode(array('status' => false, 'message' => 'Unauthorized access'));
            exit();
        }

        return $decoded_token;
    }

    public function index($id = NULL) {
        try{
        $this->verify_token();
        $data = $this->Dispatcher_model->get_dispatchers($id);
        echo json_encode(array('status' => true, 'data' => $data));
        }catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal Server Error','error' => $e));
        }
    }

    public function create() {
        try{
        $admin_data = $this->verify_token();
        // print_r($admin_data);

        $postData = json_decode(file_get_contents('php://input'), true);

        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[dispatcher.email]');
        $this->form_validation->set_rules('phone', 'Phone', 'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(array('status' => false, 'message' => $errors));
        } else {
            $data = array(
                'name' => $postData['name'],
                'email' => $postData['email'],
                'phone' => $postData['phone'],
                'created_by' => $admin_data['email'],
                'updated_by' => $admin_data['email']
            );

            if ($this->Dispatcher_model->create_dispatcher($data)) {
                echo json_encode(array('status' => true, 'message' => 'Dispatcher created successfully'));
            } else {
                $this->output->set_status_header(500);
                echo json_encode(array('status' => false, 'message' => 'Failed to create dispatcher'));
            }
        }
    }catch (Exception $e) {
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal Server Error','error' => $e));
    }
    }

    public function update($id) {
        try{
        $admin_data = $this->verify_token();

        $postData = json_decode(file_get_contents('php://input'), true);

        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(array('status' => false, 'message' => $errors));
        } else {
            $data = array(
                'name' => $postData['name'],
                'email' => $postData['email'],
                'phone' => $postData['phone'],
                'updated_by' => $admin_data['email']
            );
            $update = $this->Dispatcher_model->update_dispatcher($id, $data);
            // print_r($update);
            if ($update) {
                echo json_encode(array('status' => true, 'message' => 'Dispatcher updated successfully','update' => $update));
            } else {
                $this->output->set_status_header(500);
                echo json_encode(array('status' => false, 'message' => 'Failed to update dispatcher'));
            }
        }
    } catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal Server Error','error' => $e));
    }
    }

    public function delete($id) {
        try{
        $this->verify_token();

        if ($this->Dispatcher_model->delete_dispatcher($id)) {
            echo json_encode(array('status' => true, 'message' => 'Dispatcher deleted successfully'));
        } else {
            $this->output->set_status_header(500);
            echo json_encode(array('status' => false, 'message' => 'Failed to delete dispatcher'));
        }
    } catch(Exception $e){
        $this->output->set_status_header(500);
        echo json_encode(array('status' => false, 'message' => 'Internal Server Error','error' => $e));
    }
    }
}
