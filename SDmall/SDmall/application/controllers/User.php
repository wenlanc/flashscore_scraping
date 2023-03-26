<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
  private $registerErrors = array();
  private $user_id;
  private $num_rows = 5;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('CISendEmail');
    $this->load->model(array('Auth_model',));  

  }

  public function _remap($method, $params = array())
  {
    $method = $method;

    if (method_exists($this, $method))
    {
      return call_user_func_array(array($this, $method), $params);
    }
  }

  public function login()
  {
    if (isset($_POST) && isset($_POST['username'])) {
      $checkUser = $this->Auth_model->checkUserExsists($_POST);
      if ( is_array($checkUser) ) {
        $remember_me = false;
        if (isset($_POST['rememberme'])) {
            $remember_me = true;
        }
        $this->setLoginSession($checkUser, $remember_me);
        redirect(base_url());
        return ;
      } else {
        $this->session->set_flashdata('userError', 'Username or password is incorrect!');
      }
    }
//    $head['title'] = lang('user_login');
//    $head['description'] = lang('user_login');
//    $head['keywords'] = str_replace(" ", ",", $head['title']);
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('login');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');

  }

  public function setLoginSession($user, $remember_me)
    {
        if ($remember_me == true) {
            set_cookie('logged_user', $user["username"], time()+ (10 * 365 * 24 * 60 * 60));
            set_cookie('logged_data', $_POST["password"], time()+ (10 * 365 * 24 * 60 * 60));
        } else {
            set_cookie('logged_user', '');
            set_cookie('logged_data', '');

            delete_cookie("logged_user");
            delete_cookie("logged_data");

        }
        $_SESSION['logged_user'] = $user["username"];
        $this->Auth_model->updateLastLogin($user["email"]);
        $this->session->set_userdata('logged_data', $user);
    }

  public function signup()
  {
    if ( isset($_POST) &&  isset($_POST['username'])) {
      $result = $this->registerUser();
      if (!$result) {
        $this->session->set_flashdata('userError', $this->registerErrors);
      } else {
        $checkUser = $this->Auth_model->checkUserExsists($_POST);
        if(is_array($checkUser)){
            $this->setLoginSession($checkUser, false);
            
            // Email sending...
            $data=array();
            $data["username"] = $_POST['username'];
            $data["base_link"] = base_url();
            $data["faq_link"] = base_url()."faq";
            $mesg = $this->load->view('email/singup',$data,true);
            // or
            //$mesg = $this->load->view('template/email','',true);
            $this->cisendemail->send_email($_POST['username'], $_POST['email'], "excelsportdata.com", $mesg);
            redirect(base_url().'myaccount/accountinfo');
        }
      }
    }
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('signup');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
  private function registerUser()
  {
      $errors = array();
      if (mb_strlen(trim($_POST['password'])) == 0) {
          $errors[] = 'Please enter password';
      }
      if (mb_strlen(trim($_POST['re_password'])) == 0) {
          $errors[] = 'Please repeat password';
      }
      if ($_POST['password'] != $_POST['re_password']) {
          $errors[] = 'Passwords dont match';
      }
      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $errors[] = 'Invalid email';
      }
      $count_emails = $this->Auth_model->countUsersWithEmail($_POST['email']);
      if ($count_emails > 0) {
          $errors[] = 'Email is taken';
      }
      if (!empty($errors)) {
          $this->registerErrors = $errors;
          return false;
      }
      $this->Auth_model->registerUser($_POST);
      return true;
  }
  public function forget() {

    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $user = $this->Auth_model->getUserInfoFromEmail($_POST['email']);
        if ($user != null) {
            //$myDomain = $this->config->item('base_url');
            //$newPass = $this->Auth_model->updateUserPassword($_POST['email']);
            
            // Email sending...
            $data=array();
            $data["username"] = $user['name'];
            $data["user_email"] = $_POST['email'];
            $data["reset_link"] = base_url()."user/reset_pass?email=".$_POST['email'];
            $mesg = $this->load->view('email/forgot_pass',$data,true);
            // or
            //$mesg = $this->load->view('template/email','',true);
            $this->cisendemail->send_email($data["username"], $_POST['email'], "excelsportdata.com", $mesg);
            
            $this->session->set_flashdata('userError', 'New password sent.');
            redirect( base_url().'user/login');
        }
    }

    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('forget');
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }
  
  public function reset_pass() {

    if (isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        $user = $this->Auth_model->getUserInfoFromEmail($_GET['email']);
        if ($user != null) {
            $newPass = $this->Auth_model->updateUserPassword($_GET['email']);
            
            //$this->sendmail->sendTo($_POST['email'], 'Admin', 'New password for ' . $myDomain, 'Hello, your new password is ' . $newPass);
            
            // Email sending...
            $data=array();
            $data["username"] = $user['name'];
            $data["user_email"] = $_GET['email'];
            $data["new_password"] = $newPass;
            $data["login_link"] = base_url()."user/login";
            $mesg = $this->load->view('email/confirm_pass',$data,true);
            // or
            //$mesg = $this->load->view('template/email','',true);
            $this->cisendemail->send_email($data["username"], $data["user_email"], "excelsportdata.com", $mesg);
            
            $this->session->set_flashdata('userError', 'New password sent.');
            redirect( base_url().'user/login');
        }
    }
  }

  // public function myaccount($page = 0)
  // {
  //   if (isset($_POST['update'])) {
  //     $_POST['id'] = $_SESSION['logged_user'];
  //     $count_emails = $this->Public_model->countPublicUsersWithEmail($_POST['email'], $_POST['id']);
  //     if ($count_emails == 0) {
  //       $this->Public_model->updateProfile($_POST);
  //     }
  //     redirect(LANG_URL . '/myaccount');
  //   }
  //   $head = array();
  //   $data = array();
  //   $head['title'] = lang('my_acc');
  //   $head['description'] = lang('my_acc');
  //   $head['keywords'] = str_replace(" ", ",", $head['title']);
  //   $data['userInfo'] = $this->Public_model->getUserProfileInfo($_SESSION['logged_user']);
  //   $rowscount = $this->Public_model->getUserOrdersHistoryCount($_SESSION['logged_user']);
  //   $data['orders_history'] = $this->Public_model->getUserOrdersHistory($_SESSION['logged_user'], $this->num_rows, $page);
  //   $data['links_pagination'] = pagination('myaccount', $rowscount, $this->num_rows, 2);
  //   $this->render('user', $head, $data);
  // }

  public function logout()
  {
//    session_unset();
//    var_dump($_SESSION);
    unset($_SESSION['logged_user']);
    $this->session->unset_userdata('logged_data');
    redirect(base_url());
  }
  public function updatepass(){
    if (isset($_POST)) {
      $_POST['username'] = $_SESSION["logged_user"];
      $checkUser = $this->Auth_model->checkUserExsists($_POST);
      if( is_array($checkUser) ) {
        $this->Auth_model->updateUserNewPassword( $checkUser['email'] , $_POST["newpassword"]);
        $this->session->set_flashdata('userError', 'Successfully password updated.');

      } else {
        $this->session->set_flashdata('userError', 'Failed to change password.');

      }
      redirect(base_url()."myaccount/accountaccess");
    }
  }

  public function submitaccountinfo(){

    if (isset($_POST)) {
      $loggin_user_id = 0;
      if( is_array($this->session->userdata("logged_data")) ){
        $loggin_user_id = $this->session->userdata("logged_data")["userid"];
      }
      if( $this->Auth_model->updateUserAccountInfo($loggin_user_id,$_POST) ) {
        $this->session->set_flashdata('userError', 'Successfully updated.');
      } else {
        $this->session->set_flashdata('userError', 'Failed to update.');
      }
      redirect(base_url()."myaccount/accountinfo");
    }
  }

}
