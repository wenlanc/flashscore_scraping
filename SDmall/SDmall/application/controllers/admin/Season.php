<?php
?>
<?php

class Season extends CI_Controller{


  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->model(array('Admin_model',));
    $user = $this->session->userdata('logged_data'); // role_id = 1
    if( !isset($user) || !is_array($user) || $user["role_id"] != '1')
      redirect( base_url().'user/login');
  }

  public function index()
  {
    $leagues = $this->Admin_model->getLeagues();
    $sports = $this->Admin_model->getSports();
    $countrys = $this->Admin_model->getCountrys();
    $data = array(
      'leagues' => $leagues,
      'sports' => $sports,
      'countrys' => $countrys
    );
    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/season',$data);
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');

  }

  public function create(){
    if (isset($_POST['season_link']) && !empty($_POST['season_link'])) {

      $this->Admin_model->createSeason($_POST);
      $this->session->set_flashdata('alert_message', 'Created a season successfully!');
      redirect("admin/season/index");

    }
  }

  public function edit(){
    if (isset($_POST['season_link']) && !empty($_POST['season_link'])) {

      $this->Admin_model->updateSeason($_POST);
      $this->session->set_flashdata('alert_message', 'Updated a season successfully!');
      redirect("admin/season/index");

    }
  }

  public function ajaxList(){
    $this->db->select('season.*, league.name as league_name, league.id as league_id');
    $this->db->join('league', 'league.id = season.league_id', 'left');
    $seasons = $this->db->get('season');
    $data = array();
    foreach($seasons->result() as $rows)
    {
        $data[]= array(
            "season_name"=> $rows->name,
            "season_link" => $rows->link,
            "league_name"=> $rows->league_name,
            "league_id"=> $rows->league_id,
            "action" => '<a href="#" onclick=\'editSeason('.json_encode($rows).'); return false;\' class = "dayHead editTable kt-menu__link"><i class="la la-edit la-2x"></i></a><a href="'.base_url().'admin/season/delete/'.str_replace("/","_",$rows->link).'" data-id="'.$rows->link.'" class = "dayHead editTable kt-menu__link"><i class="la la-trash la-2x"></i></a>', 
        );     
    }
    $output = array(
        "data" => $data
    );
    echo json_encode($output);
    exit();

  }

  public function delete($id){
    $this->db->trans_start();
    $this->db->where('link', str_replace("_","/",$id));
    $this->db->delete('season');  
    $this->db->trans_complete();
    $this->session->set_flashdata('alert_message', 'Deleted a season successfully!');
    redirect("admin/season/index");
  }

}

?>