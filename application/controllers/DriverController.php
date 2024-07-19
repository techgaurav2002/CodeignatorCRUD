<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

require_once APPPATH . 'helpers/jwt_helper.php';

class DriverController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model( 'Driver_model' );
        $this->load->library( 'form_validation' );
    }

    private function verify_token() {
        $headers = $this->input->request_headers();
        if ( !isset( $headers[ 'Authorization' ] ) ) {
            $this->output->set_status_header( 401 );
            echo json_encode( array( 'status' => false, 'message' => 'Unauthorized access' ) );
            exit();
        }

        $token = str_replace( 'Bearer ', '', $headers[ 'Authorization' ] );
        $decoded_token = validateToken( $token );

        if ( $decoded_token === null ) {
            $this->output->set_status_header( 401 );
            echo json_encode( array( 'status' => false, 'message' => 'Unauthorized access' ) );
            exit();
        }

        return $decoded_token;
    }

    public function create() {
        try{
        $admin_data = $this->verify_token();
        $postData = json_decode( file_get_contents( 'php://input' ), true );
        $this->form_validation->set_data( $postData );

        $this->form_validation->set_rules( 'name', 'Name', 'required' );
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[dispatcher.email]');
        $this->form_validation->set_rules( 'phone', 'Phone', 'required' );
        $this->form_validation->set_rules('license_number', 'License Number', 'required|is_unique[driver.license_number]');
        $this->form_validation->set_rules( 'address', 'Address', 'required' );
        $this->form_validation->set_rules( 'dispatcher_id', 'Dispatcher ID', 'required|integer' );

        if ( $this->form_validation->run() === FALSE ) {
            $this->output->set_status_header( 400 );
            echo json_encode( array( 'status' => false, 'message' => $this->form_validation->error_array() ) );
        } else {
            $data = array(
                'name' => $postData['name'],
                'email' => $postData['email'],
                'phone' => $postData['phone'],
                'license_number' => $postData['license_number'],
                'address' => $postData['address'],
                'dispatcher_id' => $postData['dispatcher_id'],
                'created_by' => $admin_data['email'],
                'updated_by' => $admin_data['email']
            );
            $driver_Id = $this->Driver_model->create_driver( $data );
            if ( $driver_Id) {
                echo json_encode( array( 'status' => true, 'message' => 'Driver created successfully','Driver_Id' => $driver_Id ) );
            } else {
                $this->output->set_status_header( 500 );
                echo json_encode( array( 'status' => false, 'message' => 'Failed to create driver' ) );
            }
        }
    } catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }
    }

    public function index() {
        try{
        $this->verify_token();

        $drivers = $this->Driver_model->get_all_drivers();
        echo json_encode( array( 'status' => true, 'data' => $drivers ) );
        }catch(Exception $e){
            $this->output->set_status_header( 500 );
            echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
        }

    }

    public function show( $id ) {
        try{
        $this->verify_token();
        $driver = $this->Driver_model->get_driver_by_id( $id );
        if ( $driver ) {
            echo json_encode( array( 'status' => true, 'data' => $driver ) );
        } else {
            $this->output->set_status_header( 404 );
            echo json_encode( array( 'status' => false, 'message' => 'Driver not found' ) );
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }

    }

    public function update( $id ) {
        try{
        $admin_data = $this->verify_token();
        $putData = json_decode( file_get_contents( 'php://input' ), true );
        $this->form_validation->set_data( $putData );

        $this->form_validation->set_rules( 'name', 'Name', 'required' );
        $this->form_validation->set_rules( 'email', 'Email', 'required|valid_email' );
        $this->form_validation->set_rules( 'phone', 'Phone', 'required' );
        $this->form_validation->set_rules( 'license_number', 'License Number', 'required' );
        $this->form_validation->set_rules( 'address', 'Address', 'required' );
        $this->form_validation->set_rules( 'dispatcher_id', 'Dispatcher ID', 'required|integer' );

        if ( $this->form_validation->run() === FALSE ) {
            $this->output->set_status_header( 400 );
            echo json_encode( array( 'status' => false, 'message' => $this->form_validation->error_array() ) );
        } else {
            $data = array(
                'name' => $putData['name'],
                'email' => $putData['email'],
                'phone' => $putData['phone'],
                'license_number' => $putData['license_number'],
                'address' => $putData['address'],
                'dispatcher_id' => $putData['dispatcher_id'],
                'updated_by' => $admin_data['email']
            );
            if ( $this->Driver_model->update_driver( $id, $data ) ) {
                echo json_encode( array( 'status' => true, 'message' => 'Driver updated successfully' ) );
            } else {
                $this->output->set_status_header( 500 );
                echo json_encode( array( 'status' => false, 'message' => 'Failed to update driver' ) );
            }
        }
    }catch(Exception $e){
        $this->output->set_status_header( 500 );
        echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
    }

    }

    public function delete( $id ) {
        try{
        $this->verify_token();
            if ( $this->Driver_model->delete_driver( $id ) ) {
                echo json_encode( array( 'status' => true, 'message' => 'Driver deleted successfully' ) );
            } else {
                $this->output->set_status_header( 500 );
                echo json_encode( array( 'status' => false, 'message' => 'Failed to delete driver' ) );
            }
        }catch(Exception $e){
            $this->output->set_status_header( 500 );
            echo json_encode( array( 'status' => false, 'message' => 'Something went wrong' ) );
        }
    }
}
