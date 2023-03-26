<?php
?>

<?php

class User extends CI_Controller{


  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('CISendEmail','session');
    $this->load->model(array('Admin_model','Auth_model'));
    $user = $this->session->userdata('logged_data'); // role_id = 1
    if( !isset($user) || !is_array($user) || $user["role_id"] != '1')
      redirect( base_url().'user/login');
  }

  public function index()
  {

    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/user');
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');
  }

  public function ajaxList()
  {
      $draw = intval($this->input->post("draw"));
      $start = intval($this->input->post("start"));
      $length = intval($this->input->post("length"));
      $order = $this->input->post("order");
      $search= $this->input->post("search");  
      $search = $search['value'];
      $col = 0;
      $dir = "";
      if(!empty($order))
      {
          foreach($order as $o)
          {
              $col = $o['column'];
              $dir= $o['dir'];
          }
      }

      if($dir != "asc" && $dir != "desc")
      {
          $dir = "desc";
      }

      $this->db->from('user');
      $this->db->select('user.*, user_privilege.privilege as user_role_name');
      $this->db->join('user_privilege', 'user_privilege.id = user.role_id', 'left');

      $valid_columns = array(
          0=>'name',
          1=>'email',
      );
      if(!isset($valid_columns[$col]))
      {
          $order = null;
      }
      else
      {
          $order = $valid_columns[$col];
      }
      if($order !=null)
      {
          $this->db->order_by($order, $dir);
      }
      
      if(!empty($search))
      {
          $x=0;
          foreach($valid_columns as $sterm)
          {
              if($x==0)
              {
                  $this->db->like($sterm,$search);
              }
              else
              {
                  $this->db->or_like($sterm,$search);
              }
              $x++;
          }                 
      }
      $this->db->limit($length,$start);

      $users = $this->db->get();
      $data = array();
      foreach($users->result() as $rows)
      {
          $data[]= array(
              'Username' => $rows->name,
              'Email' => $rows->email,
              'FirstName' => $rows->first_name,
			  'LastName' => $rows->last_name,
			  'CompanyName' => $rows->company_name,
			  'Country' => $rows->country,
			  'TownCity' => $rows->town_city,
			  'Address' => $rows->address,
			  'Zipcode' => $rows->zip_code,
			  'VATNumber' => $rows->VAT_number,
			  'RegisterDate' => $rows->register_date,
			  'LastLogin' => $rows->last_login,
			  'Defunct' => $rows->defunct,
              'Role' => $rows->user_role_name,
			  'Actions' => $rows->defunct?"":"<a href='".base_url()."admin/user/delete/".$rows->id."' class = 'dayHead editTable kt-menu__link'><i class='la la-trash la-2x'></i>  </a>"
          );     
      }
      $countTotalUsers = $this->Admin_model->countTotalUsers();
      $output = array(
          "draw" => $draw,
          "recordsTotal" => $countTotalUsers,
          "recordsFiltered" => $countTotalUsers,
          "data" => $data
      );
      echo json_encode($output);
      exit();
  }

  
  public function delete($id){
    
    //$this->db->trans_start();
    //$this->db->where('id', $id);
    //$this->db->delete('user');  
    //$this->db->trans_complete();
    //$this->session->set_flashdata('alert_message', 'Deleted a user successfully!');
    $this->Auth_model->updateUserDefunct($id);
    $this->session->set_flashdata('alert_message', 'Deactivated a user successfully!');
    $user = $this->Auth_model->getUserInfoFromID($id);
    if($user["role_id"] != 2 ){
        // Email sending...
        $data=array();
        $data["username"] = $user['name'];
        $data["signup_link"] = base_url()."user/signup";
        $mesg = $this->load->view('email/delete_account',$data,true);
        $this->cisendemail->send_email($data["username"], $user['email'], "excelsportdata.com", $mesg);
    }
    
    
    redirect("admin/user/index");
  }
  
}

?>