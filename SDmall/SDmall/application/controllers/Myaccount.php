<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Myaccount extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('session');
    $this->load->model(array('Admin_model','Auth_model'));
    $user = $this->session->userdata('logged_data');
    if( ! isset($user) )
      redirect( base_url().'user/login');
  }

  public function _remap($page)
  {
    $user = $this->session->userdata('logged_data');
    if( ! isset($user) )
      redirect( base_url().'user/login');
    
    $title = array(
      'accountinfo' => 'Account Information',
      'accountaccess' => 'MY ACCOUNT',
      'billinfo' => 'Billing Information',
      'deleteaccount' => 'Delete account',
      'favourite' => 'MY ACCOUNT',
      'download' => 'MY ACCOUNT',
      'invoice' => 'Invoices'
    );
    $desc = array(
      'accountinfo' => '',   // 'Last Activity: 09/12/2020 - 2:30 pm'
      'accountaccess' => '',
      'billinfo' => '',
      'deleteaccount' => '',
      'favourite' => '',
      'download' => '',
      'invoice' => ''
    );
    $navigation = array(
      'accountinfo' => 'My account&nbsp/&nbspAccount information',
      'accountaccess' => 'My account&nbsp/&nbspAccount access',
      'billinfo' => 'My account&nbsp/&nbspBill information',
      'deleteaccount' => 'My account&nbsp/&nbspDelete account',
      'favourite' => 'My account&nbsp/&nbspFavorite',
      'download' => 'My account&nbsp/&nbspDownloads',
      'invoice' => 'My account&nbsp/&nbspInvoice'
    );
    $data['title'] = $title[$page];
    $data['description'] = $desc[$page];
    $data['navigation'] = $navigation[$page];

    $leagues = $this->Admin_model->getProductLeaguesWithTourID();
    $countrys = $this->Admin_model->getCountrysUsed();
    $sports = $this->Admin_model->getSportsUsed();
    $data["countrys"] = $countrys;
    $data["sports"] = $sports;
    $data["leagues"] = $leagues;
    
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    if($page == "accountinfo"){

      $countrys = $this->Admin_model->getCountrys();


      $content_data = array( 'countrys' => $countrys, 'user' => $this->Auth_model->getUserInfoFromID($user["userid"]));

      $this->load->view($page,  $content_data);

    } else {
      $this->load->view($page);
    }
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
  
  public function accountinfo(){
    $data['title'] = 'Account information';
    $data['description'] = '';
    $data['navigation'] = 'My account&nbsp/&nbspAccount information';

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $countrys = $this->Admin_model->getCountrys();
    $content_data = array( 'countrys' => $countrys, 'user' => $this->Auth_model->getUserInfoFromID($user["userid"]));
    $this->load->view('accountinfo',  $content_data);
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }

  public function billinfo(){
    $data['title'] = 'Billing Information';
    $data['description'] = '';
    $data['navigation'] = 'My account&nbsp/&nbspBill information';

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('billinfo');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
  
  public function accountaccess(){
    $data['title'] = 'MY ACCOUNT';
    $data['description'] = '';
    $data['navigation'] = 'My account&nbsp/&nbspAccount access';

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('accountaccess');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
}
?>