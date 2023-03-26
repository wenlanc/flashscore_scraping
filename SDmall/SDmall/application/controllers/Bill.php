<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bill extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('session');
    $this->load->model(array('Public_model'));

    $user = $this->session->userdata('logged_data'); // role_id = 1
    if( !isset($user))
      redirect( base_url().'user/login');
  }
  
  public function index(){
    $data['title'] = 'MY ACCOUNT';
    $data['description'] = '';
    $data['navigation'] = 'My account&nbsp/&nbspInvoice';

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('invoice');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }  
  
  public function ajaxInvoiceList()
  {
    $logged_user_id = 0;
    if(is_array($this->session->userdata('logged_data'))){
        if(isset($this->session->userdata('logged_data')["userid"])){
            $logged_user_id = $this->session->userdata('logged_data')["userid"];
        }
    }

    $this->db->from('orders');
    $this->db->select('orders.*, user.first_name, user.last_name');
    $this->db->join('user', 'user.id = orders.user_id', 'left');
    $this->db->where('orders.user_id',$logged_user_id);
    $orders = $this->db->get();
    $data = array();
    foreach($orders->result() as $rows)
    {
        $data[]= array(
            'id'      => $rows->id,
            'invoice' => $rows->order_id,
            'amount' => "€".number_format($rows->paid_amount * 1,2),
            'status' => $rows->proccessed,
            'issued' => $rows->datetime,
            'due' => $rows->datetime,
            'pdf'   => '<span><a target="_blank" href = "'.base_url().'bill/generateInvoicePdf/'.$rows->id.'" ><span class="viewsample">View </span></a>&nbsp;&nbsp;&nbsp;<a target="_blank" href = "'.base_url().'bill/downloadInvoicePdf/'.$rows->id.'" ><span class="viewsample">Download </span></a></span>',
        );     
    }
    $output = array(
        "data" => $data
    );
    echo json_encode($output);
    exit();
  }

  function generateInvoicePdf($id)
  {

      $this->load->library('pdf');
      $order_data = $this->Public_model->getOrder($id);
      if(is_array($order_data) && count($order_data)>0){
        $html = $this->load->view('GeneratePdfView', [ "id" => $id, 'order_data' =>$order_data ], true);
        $this->pdf->createPDF($html, 'Invoice', false);
      }
  }
  function downloadInvoicePdf($id)
  {
    $this->load->library('pdf');
    $order_data = $this->Public_model->getOrder($id);
    if(is_array($order_data) && count($order_data)>0){
      $html = $this->load->view('GeneratePdfView', [ "id" => $id, 'order_data' =>$order_data ], true);
      $this->pdf->createPDF($html, 'Invoice');
    }
  }
  function bulkDownloadInvoicePdf()
  {
    if ( isset($_POST) &&  isset($_POST['order_ids'])) {
      $this->load->library('pdf');

      $order_ids = json_decode($_POST['order_ids']);
      foreach($order_ids as $id)
      {
        $order_data = $this->Public_model->getOrder($id);
        if(is_array($order_data) && count($order_data)>0){
          $html = $this->load->view('GeneratePdfView', ["id" => $id, 'order_data' =>$order_data], true);
          $this->pdf->createPDF($html, 'Invoice');
        }
      }
    }
  }  
}

?>