<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

require_once APPPATH . 'helpers/jwt_helper.php';
class TrailerController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Trailer_model');
        $this->load->library( 'form_validation' );
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

    public function create() {
        try{
        $admin_data = $this->verify_token();

        $postData = json_decode(file_get_contents('php://input'), true);
        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('model', 'Model', 'required');
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required|is_unique[trailer.license_plate]');
        $this->form_validation->set_rules('vin', 'VIN', 'required|is_unique[trailer.vin]');
        $this->form_validation->set_rules('manufacturer', 'Manufacturer', 'required');
        $this->form_validation->set_rules('year', 'Year', 'required');
        $this->form_validation->set_rules('truck_id', 'Truck ID', 'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(['status' => false, 'message' => $errors]);
        } else {
            $postData['created_by'] = $admin_data['email'];
            $postData['updated_by'] = $admin_data['email'];
            if ($this->Trailer_model->create_trailer($postData)) {
                echo json_encode(['status' => true, 'message' => 'Trailer created successfully']);
            } else {
                $this->output->set_status_header(500);
                echo json_encode(['status' => false, 'message' => 'Internal server error']);
            }
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }
    }

    public function get($trailer_id = Null) {
        try{
        $this->verify_token();

        $trailer = $this->Trailer_model->get_trailer($trailer_id);
        if ($trailer) {
            echo json_encode(['status' => true, 'data' => $trailer]);
        } else {
            $this->output->set_status_header(404);
            echo json_encode(['status' => false, 'message' => 'Trailer not found']);
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }
    }

    public function update($trailer_id) {
        try{
        $admin_data = $this->verify_token();

        $postData = json_decode(file_get_contents('php://input'), true);
        $this->form_validation->set_data($postData);

        $this->form_validation->set_rules('model', 'Model', 'required');
        $this->form_validation->set_rules('license_plate', 'License Plate', 'required');
        $this->form_validation->set_rules('vin', 'VIN', 'required');
        $this->form_validation->set_rules('manufacturer', 'Manufacturer', 'required');
        $this->form_validation->set_rules('year', 'Year', 'required');
        $this->form_validation->set_rules('truck_id', 'Truck ID', 'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->output->set_status_header(400);
            echo json_encode(['status' => false, 'message' => $errors]);
        } else {
            $postData['updated_by'] = $admin_data['email'];
            if ($this->Trailer_model->update_trailer($trailer_id, $postData)) {
                echo json_encode(['status' => true, 'message' => 'Trailer updated successfully']);
            } else {
                $this->output->set_status_header(500);
                echo json_encode(['status' => false, 'message' => 'Internal server error']);
            }
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }
    }

    public function delete($trailer_id) {
        try{
        $this->verify_token();

        if ($this->Trailer_model->delete_trailer($trailer_id)) {
            echo json_encode(['status' => true, 'message' => 'Trailer deleted successfully']);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['status' => false, 'message' => 'Internal server error']);
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }
    }
}
