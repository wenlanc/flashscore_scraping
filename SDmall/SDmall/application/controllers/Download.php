<?php
/**
 * Created by PhpStorm.
 * User: DREAM
 * Date: 12/14/2020
 * Time: 7:55 PM
 */
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller {



  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url_helper');
    $this->load->library('session');
    $this->load->model(array('Admin_model',));
    $user = $this->session->userdata('logged_data'); // role_id = 1
    if( !isset($user))
      redirect( base_url().'user/login');
  }

  public function index()
  {
    $data['title'] = 'MY ACCOUNT';
    $data['description'] = '';
    $data['navigation'] = 'My account&nbsp/&nbspDownloads';

    $leagues = $this->Admin_model->getProductLeaguesWithTourID();
    $countrys = $this->Admin_model->getCountrysUsed();
    $sports = $this->Admin_model->getSportsUsed();
    $data1["countrys"] = $countrys;
    $data1["sports"] = $sports;
    $data1["leagues"] = $leagues;
    
    $this->load->view('common/header_html');
    $this->load->view('common/header');
    $this->load->view('common/sub_header', $data);
    $this->load->view('download',$data1);
    $this->load->view('common/footer');
    $this->load->view('common/footer_html');
  }

  public function ajaxDownloadList(){

    $logged_user_id = 0;
    if(is_array($this->session->userdata('logged_data'))){
        if(isset($this->session->userdata('logged_data')["userid"])){
            $logged_user_id = $this->session->userdata('logged_data')["userid"];
        }
    }
    
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

    $field = "Season";
    $sort = "asc";
    if (isset($_POST['sort']))
    {
      $field = $_POST['sort']["field"];
      $sort = $_POST['sort']['sort'];
    }

    $valid_columns = array(
        "Sport"=>'sport.name',
        "Country"=>'country.name',
        "Competition"=>'season.name',
        "Season"=>'season.name',
        "Price"=>'season.name',
    );
        
    $this->db->from('order_product');
    $this->db->select('product.*, country.name as country_name, league.name as league_name,  sport.name as sport_name, season.name as season_name');
    $this->db->join('product', 'product.id = order_product.product_id', 'left');
    $this->db->join('country', 'country.id = product.country_id', 'left');
    $this->db->join('league', 'league.id = product.league_id', 'left');
    $this->db->join('sport', 'sport.id = product.category_id', 'left');
    $this->db->join('season', 'season.link = product.season_link', 'left');
    $this->db->join('orders', 'order_product.order_id = orders.id', 'left');
    $this->db->where('order_product.user_id',$logged_user_id);
    $this->db->where('orders.proccessed',1);
    
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
          
        } else if($key == "league_p_id") {
          $this->db->like('league.id',$query);
        } else {
          $this->db->where($key,$query);
        }
        
      }        
    }
    $this->db->group_by('product.id');
    $countTotalProducts = $this->db->count_all_results('', false);
    $this->db->limit($length,$start);
    
    $products = $this->db->get();
      $data = array();
      foreach($products->result() as $rows)
      {

          $data[]= array(
              'Sport' => $rows->sport_name,
              'Country' => $rows->country_name,
              'Competition' => $rows->league_name,
              'Season' => $rows->season_name,
              'GamePlayed' => $rows->game_played,
              'MatchStats' => $rows->match_stats,
              'Price' => "€".$rows->price,
              'LastUpdate' => $rows->last_update,
              'Action'   => '<span style="display:flex;align-items: center;"><a href="javascript:manageShoppingCart(\'add\', '. $rows->id .',\'' .base_url().'checkout'.'\');" ><span class="viewsample">Update</span><i class="la la-lg m-0 la-shopping-cart" style="color: var(--custom_green);"></i></a>&nbsp;&nbsp;&nbsp;<a href= "'.base_url().'download/download/'.$rows->id.'" class="btn btn-md btn-addtocart"> Download&nbsp<i class="flaticon2-download-2"></i></a>',

              'ViewSample' => '<a href= "'.base_url().'download/sample_download/'.$rows->id.'" ><span class="viewsample">View Sample</span></a>',
              'Download'   => '<a href= "'.base_url().'download/download/'.$rows->id.'" class="btn btn-md btn-addtocart"> Download&nbsp<i class="flaticon2-download-2"></i></a>'
               // <a href = "#" class="btn btn-md btn-addtocart"> Download&nbsp<i class="flaticon2-download-2"></i></a>     
            );     
      }

    
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

  public function download($id){
    $this->load->helper('download');
    $logged_user_id = 0;
    if(is_array($this->session->userdata('logged_data'))){
        if(isset($this->session->userdata('logged_data')["userid"])){
            $logged_user_id = $this->session->userdata('logged_data')["userid"];
        }
    }
    $this->db->from('order_product');
    $this->db->select('product.*');
    $this->db->join('product', 'product.id = order_product.product_id', 'left');
    $this->db->join('orders', 'order_product.order_id = orders.id', 'left');
    $this->db->where('order_product.user_id',$logged_user_id);
    $this->db->where('orders.proccessed',1);
    $this->db->where('product.id',$id);
    $products = $this->db->get()->result_array();
    if(count($products)>0)
    { 
      $fileinfo = $this->Admin_model->getFilePath($id); 
      if($fileinfo != "" && file_exists(APPPATH."products".DIRECTORY_SEPARATOR.$logged_user_id.DIRECTORY_SEPARATOR.$fileinfo))
        force_download(APPPATH."products".DIRECTORY_SEPARATOR.$logged_user_id.DIRECTORY_SEPARATOR.$fileinfo, NULL);
    } else {
      redirect($_SERVER['HTTP_REFERER']);
    }
  }


  public function sample_download($id){
    $this->load->helper('download');
    $fileinfo = $this->Admin_model->getSampleFilePath($id);
    if(!empty($fileinfo)){
      force_download(APPPATH.$fileinfo, NULL);
    } else {
      redirect($_SERVER['HTTP_REFERER']);
    }
    
  }

  
}

?>