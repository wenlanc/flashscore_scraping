<?php
/**
 * Created by PhpStorm.
 * User: DREAM
 * Date: 12/17/2020
 * Time: 7:36 PM
 */
?>
<?php

class League extends CI_Controller{


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
    $sports = $this->Admin_model->getSports();
    $countrys = $this->Admin_model->getCountrys();
    $data = array(
      'sports' => $sports,
      'countrys' => $countrys
    );
    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/league',$data);
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');

  }

  public function create(){
    if (isset($_POST['league_id']) && !empty($_POST['league_id'])) {

      $this->Admin_model->createLeague($_POST);
      $this->session->set_flashdata('alert_message', 'Created a league successfully!');
      redirect("admin/league/index");

    }
  }

  public function edit(){
    if (isset($_POST['league_id']) && !empty($_POST['league_id'])) {

      $this->Admin_model->updateLeague($_POST);
      $this->session->set_flashdata('alert_message', 'Updated a league successfully!');
      redirect("admin/league/index");

    }
  }

  public function ajaxList(){
    $page = 1;
    $perpage = 10;
    $length = 10;
    $start = 1;
    if (isset($_POST['pagination']))
    {
      $page = $_POST['pagination']['page'];
      $perpage =  $_POST['pagination']['perpage'];
      $length  = $perpage;
      $start = ($page - 1) * $perpage;
    }

    $field = "league_link";
    $sort = "asc";
    if (isset($_POST['sort']))
    {
      $field = $_POST['sort']["field"];
      $sort = $_POST['sort']['sort'];
    }

    $valid_columns = array(
        "league_name"=>'league.name',
        "league_link"=>'league.link',
    );

    $this->db->from("league");
    $this->db->select('league.*, sport.name as sport_name, country.name as country_name');
    $this->db->join('sport', 'sport.id = league.sport_id', 'left');
    $this->db->join('country', 'country.id = league.tournament_id', 'left');
    
    if(!isset($valid_columns[$field]))
    {
        $order = null;
    }
    else
    {
        $order = $valid_columns[$field];
    }
    if($order !=null)
    {
        $this->db->order_by($order, $sort);
    }
    if(isset($_POST['query']) && is_array($_POST['query']))
    {
      foreach($_POST['query'] as $key => $query)
      { 
        if($key == "generalSearch")
        {
          $x=0;
          if($_POST["query"][$key] != "")
          {
            $this->db->group_start();
            foreach($valid_columns as $sterm)
            {
              if($x==0)
              {
                  $this->db->like($sterm,$query);
              }
              else
              {
                  $this->db->or_like($sterm,$query);
              }
              $x++;
            }
            $this->db->group_end();
          }
          
        } else if($key == "sport_name") {
          $this->db->like('sport.name',$query);
        } else if($key == "country") {
          $this->db->like('country.name',$query);
        } else {
          $this->db->where($key,$query);
        }
      }        
    }
    
    $this->db->group_by('league.id');

    $countTotalProducts = $this->db->count_all_results('', false);
    $this->db->limit($length,$start);

    $products = $this->db->get();
    $logged_user_id = 0;
    if(is_array($this->session->userdata('logged_data'))){
        if(isset($this->session->userdata('logged_data')["userid"])){
            $logged_user_id = $this->session->userdata('logged_data')["userid"];
        }
    }
    
    $data = array();
    foreach($products->result() as $rows)
      {
          $data[]= array(
            "id" => $rows->id,
            "sport_id"=> $rows->sport_id,
            "sport_name" => $rows->sport_name,
            "tournament_id"=> $rows->tournament_id,
            "league_name"=> $rows->name,
            "country" => $rows->country_name,
            "league_link"=>$rows->link,
            "action" => '<a href="#" onclick=\'editLeague('.json_encode($rows).'); return false;\' data-id="'.$rows->id.'" class = "dayHead editTable kt-menu__link"><i class="la la-edit la-2x"></i>  </a><a href="'.base_url().'admin/league/delete/'.$rows->id.'" data-id="'.$rows->id.'" class = "dayHead editTable kt-menu__link"><i class="la la-trash la-2x"></i>  </a>', 
          );     
      }
      //$countTotalProducts = $this->Admin_model->countTotalProducts();
      $output = array(
          "meta" => array(
              "page"=> $page,
              "pages"=> $countTotalProducts / $perpage,
              "perpage"=> $perpage,
              "total"=> $countTotalProducts,
              "sort"=> "asc",
            ),
          "data" => $data
      );
      echo json_encode($output);
      exit();
  }  
  
  public function ajaxList_1(){
    $this->db->select('league.*, sport.name as sport_name,season.link as season_link');
    $this->db->join('sport', 'sport.id = league.sport_id', 'left');
    $this->db->join('season', 'season.league_id = league.id', 'left');
    $this->db->group_by('league.id');
    $leagues = $this->db->get('league');
    $data = array();
    foreach($leagues->result() as $rows)
    {
      $country_link = "";
      $season_link_arr = explode( "/", $rows->season_link);
      if(is_array($season_link_arr) && count($season_link_arr) > 3)
      {
        $country_link = $season_link_arr[3];
      }

        $data[]= array(
            "id" => $rows->id,
            "sport_id"=> $rows->sport_id,
            "sport_name" => $rows->sport_name,
            "tournament_id"=> $rows->tournament_id,
            "name"=> $rows->name,
            "country" => $country_link,
            "link"=>$rows->link,
            "action" => '<a href="#" onclick=\'editLeague('.json_encode($rows).'); return false;\' data-id="'.$rows->id.'" class = "dayHead editTable kt-menu__link"><i class="la la-edit la-2x"></i>  </a><a href="'.base_url().'admin/league/delete/'.$rows->id.'" data-id="'.$rows->id.'" class = "dayHead editTable kt-menu__link"><i class="la la-trash la-2x"></i>  </a>', 
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
    $this->db->where('id', $id);
    $this->db->delete('league');  
    $this->db->trans_complete();
    $this->session->set_flashdata('alert_message', 'Deleted a league successfully!');
    redirect("admin/league/index");
  }

}

?>