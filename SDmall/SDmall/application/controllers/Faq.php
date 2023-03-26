<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('session','CISendEmail');
  }

  public function index()
  {
    $data['title'] = "FREQUENTLY ASKED QUESTIONS";
    $data['description'] = "";
    $data['navigation'] = "";
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('faq');
    $this->load->view('common/footer_html');
    $this->load->view('common/footer');
  }
  public function ask(){

    $sender_name = $this->input->post('username'); 
    $subject = $this->input->post('subject'); 
    $message = $this->input->post('message'); 
    $to_email = $this->input->post('email');
    
    if($this->cisendemail->send_email($sender_name, $to_email, $subject, $message)){
       //Success email Sent
       $successMessage = "Email sent successfully!";
        $this->session->set_flashdata('emailError', $successMessage);
        redirect("faq/index");
    }else{
       //Email Failed To Send
       //echo $this->email->print_debugger();
       $successMessage = "Email not sent with some reasons!";
        $this->session->set_flashdata('emailError', $successMessage);
        redirect("faq/index");
    }
  }
}