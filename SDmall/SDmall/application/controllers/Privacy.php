<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privacy extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('session');
  }

  public function index()
  {
    $data['title'] = "Privacy Policy";
    $data['description'] = "";
    $data['navigation'] = "";

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('privacy');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
}

?>