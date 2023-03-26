<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

error_reporting(-1);
		ini_set('display_errors', 1);


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Product extends CI_Controller{


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
    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/product/list');
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');
  }

  public function create()
  {
    // POST request
    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
      if(isset($_POST['season_id']) && !empty($_POST['season_id']))
      {
        if($_POST["season_id"] == '0'){
            if( isset($_POST['league_id']) ){  // && !empty($_POST['league_id'])

              if( $_POST["league_id"] == "0" ) {
                  // All leagues - All seasons
                  $leagues = $this->Admin_model->getLeagues($_POST['category_id'],$_POST['country_link']);
                  foreach($leagues as $league){
                    $_POST["league_id"] = $league["id"];
                    $seasons = $this->Admin_model->getSeasons($_POST['country_link'], $league["id"]);
                    foreach($seasons as $season){
                      $_POST["season_id"] = $season["link"];
                      $_POST["country_id1"] = $this->getCountryIDFromSeason( $season["link"]);
                      $_POST["season_from"] = $this->getFromSeason( $season["link"]);
                      $_POST["season_to"] = $this->getToSeason( $season["link"]);
                      $this->createProductOne( $_POST );
                    }
                  }
              } else {
                  // Selected league - all seasons
                  $seasons = $this->Admin_model->getSeasons($_POST['country_link'], $_POST["league_id"]);
                  foreach($seasons as $season){
                    $_POST["season_id"] = $season["link"];
                    $_POST["country_id1"] = $this->getCountryIDFromSeason( $season["link"]);
                    $_POST["season_from"] = $this->getFromSeason( $season["link"]);
                    $_POST["season_to"] = $this->getToSeason( $season["link"]);
                    $this->createProductOne( $_POST );
                  }
              }
            } else {
                // exeption 
            }
        } else {
            // Selected season
            $this->createProductOne( $_POST );
        }

      } else {  
        if( isset($_POST['league_id'])){   // && !empty($_POST['league_id'])
          if( $_POST["league_id"] == "0" ) { 
              // All leagues - All seasons

              $leagues = $this->Admin_model->getLeagues($_POST['category_id'],$_POST['country_link']);
              foreach($leagues as $league){
                $_POST["league_id"] = $league["id"];
                $seasons = $this->Admin_model->getSeasons($_POST['country_link'], $league["id"]);
                foreach($seasons as $season){
                  $_POST["season_id"] = $season["link"];
                  $_POST["country_id1"] = $this->getCountryIDFromSeason( $season["link"]);
                  $_POST["season_from"] = $this->getFromSeason( $season["link"]);
                  $_POST["season_to"] = $this->getToSeason( $season["link"]);
                  $this->createProductOne( $_POST );
                }
              }
          } else {
              // Selected league - all seasons
              $seasons = $this->Admin_model->getSeasons($_POST['country_link'], $_POST["league_id"]);
              foreach($seasons as $season){
                $_POST["season_id"] = $season["link"];
                $_POST["country_id1"] = $this->getCountryIDFromSeason( $season["link"]);
                $_POST["season_from"] = $this->getFromSeason( $season["link"]);
                $_POST["season_to"] = $this->getToSeason( $season["link"]);
                $this->createProductOne( $_POST );
              }
          } 
        } else {  
            // exeption 
        }
      }
      redirect("admin/product/index");
    } 

    // Get request
    $leagues = $this->Admin_model->getLeagues();
    $countrys = $this->Admin_model->getCountrys();
    $sports = $this->Admin_model->getSports();
    $seasons = $this->Admin_model->getSeasons();
    $last_update = $this->Admin_model->getLastUpdate();
    $data = array(
      'countrys' => $countrys,
      'sports' => $sports,
      'last_update' => $last_update,
    );
    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/product/create', $data);
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');
  }

  function getCountryIDFromSeason($season_name){
    $pieces = explode("/", $season_name);
    if(is_array($pieces) && count($pieces) > 3 ) {
      $country_name = $pieces[2];
      $query = $this->db->select("*")->where('link' , $country_name)->get("country");
      $result = $query->row();
      if(isset($result)) return $result->id;
    } 
    return "";
  }

  function getFromSeason($season_name){
    $year = false;
    $season_from = "";
    $season_to = "";
    if(preg_match_all("/\d{4}/", $season_name, $match)) {
      $year = $match[0];
      if(count($year) > 1)
      {
        $season_from = $year[0];
        $season_to = $year[1];
      }
      else if( count($year) == 1) {
        $season_from = $year[0];
      }
    }
    return $season_from;
  }

  function getToSeason($season_name){
    $year = false;
    $season_from = "";
    $season_to = "";
    if(preg_match_all("/\d{4}/", $season_name, $match)) {
      $year = $match[0];
      if(count($year) > 1)
      {
        $season_from = $year[0];
        $season_to = $year[1];
      }
      else if( count($year) == 1) {
        $season_from = $year[0];
      }
    }
    return $season_to;
  }

  function createProductOne($post)
  { 
    $sport_link = $this->Admin_model->getSports($post['category_id'])[0]["link"];
    $table_name = $sport_link . "_statistics";
    $season_link = $post['season_id']; 
    //$this->db->from($table_name);
    $this->db->select($table_name.'.*, match.*');
    $this->db->join($table_name, $table_name.'.match_id = match.id', 'left');
    $this->db->like('match.season_link',$season_link);
    $this->db->order_by('datetime','ASC');
    $matchs = $this->db->get('match'); 
	
    $field_array = $this->db->list_fields($table_name); 
    
    // Error on linux    
    // $field_array = $this->db->query("select * from ".$table_name)->list_fields();

    $field_array_mand = array_diff($field_array, array('id' , 'match_id')); 
    $match_result = $matchs->result();
    if(count($match_result) < 1)
    {
      $this->session->set_flashdata('alert_message', 'Failed!. No data exist.');
    }
    else {
      // Csv file creating
      //$filename = "products/product".(new DateTime())->format('YmdHisv').".csv";
      //$filename = "products/excel_sport_data_".str_replace("/","_",trim(trim($season_link,"/"))).".csv";
      //$fp = fopen(APPPATH.$filename, 'w');
      //fputcsv($fp, $field_array_mand);

      $file_result = $this->createExcel($season_link, $match_result, $field_array_mand, $post['description']);
      $post["match_stats"] = count($field_array_mand);
      $post['sample_view'] = html_entity_decode($file_result['sample_data'],ENT_QUOTES);
      $post['file_path'] = $file_result["filename"];
      $post['sample_file_path'] = $file_result["sample_filename"];

      $post["game_played"] = count($match_result);
      $this->Admin_model->createProduct($post);
      $this->session->set_flashdata('alert_message', 'Created a product successfully!');
    }
  }

  private function _number_alphabet( $num, $code = "" ){
      //$alphabet = range('A', 'Z');
      //return $alphabet[$n]; // returns D
      $alphabets = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

    $division = floor($num / 26);
    $remainder = $num % 26; 

    if($remainder == 0)
    {
        $division = $division - 1;
        $code .= 'z';
    }
    else
        $code .= $alphabets[$remainder];

    if($division > 26)
        return number_to_alpha($division, $code);   
    else
        $code .= $alphabets[$division];     

    return strrev($code);
  }

  public function edit($id = 0)
  {
    // POST request
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {

      $sport_link = $this->Admin_model->getSports($_POST['category_id1'])[0]["link"];
      $table_name = $sport_link . "_statistics";
      $season_link = $_POST['season_id1'];

      //$this->db->from($table_name);
      $this->db->select($table_name.'.*, match.*');
      $this->db->join($table_name, $table_name.'.match_id = match.id', 'left');
      $this->db->like('match.season_link',$season_link);
      $this->db->order_by('datetime','ASC');
      $matchs = $this->db->get('match');
      //$field_array = $this->db->query("select * from ".$table_name)->list_fields();
      $field_array = $this->db->list_fields($table_name);
      $field_array_mand = array_diff($field_array, array('id' , 'match_id'));
      $match_result = $matchs->result();
      if(count($match_result) < 1)
      {
        $this->session->set_flashdata('alert_message', 'Failed!. No data exist.');
      }
      else {
        $file_result = $this->createExcel($season_link, $match_result, $field_array_mand, $_POST['description']);

        $_POST['sample_view'] = html_entity_decode($file_result['sample_data'],ENT_QUOTES);
        $_POST['file_path'] = $file_result["filename"];
        $_POST['sample_file_path'] = $file_result["sample_filename"];
        $_POST["match_stats"] = count($field_array_mand);
        $_POST["game_played"] = count($match_result);
        $this->Admin_model->updateProduct($_POST);
        $this->session->set_flashdata('alert_message', 'Updated a product successfully!');
        redirect("admin/product/index");
      }
    } 

    // Get request
    $countrys = $this->Admin_model->getCountrys();
    $sports = $this->Admin_model->getSports();
    $last_update = $this->Admin_model->getLastUpdate();
    $product = $this->Admin_model->getProduct($id);
    $data = array(
      'countrys' => $countrys,
      'sports' => $sports,
      'last_update' => $last_update,
      'product' => $product
    );
    $this->load->view('admin/common/header_html');
    $this->load->view('admin/common/header');
    $this->load->view('admin/product/edit', $data);
    $this->load->view('admin/common/footer');
    $this->load->view('admin/common/footer_html');
  }

  public function bulkUpdate(){
    $last_update = $this->Admin_model->getLastUpdate();
    $this->db->from("product");
    $this->db->where('season_from',0)->or_where("season_from", date("Y") )->or_where("season_to",date("Y"));
    $products = $this->db->get();
    foreach($products->result() as $product)
    {  
      $sport_link = $this->Admin_model->getSports($product->category_id)[0]["link"];
      $table_name = $sport_link . "_statistics";
      $season_link = $product->season_link;

      $this->db->from('match');
      $this->db->select($table_name.'.*, match.*');
      $this->db->join($table_name, $table_name.'.match_id = match.id', 'left');
      $this->db->like('match.season_link',$season_link);
      $this->db->order_by('datetime','ASC');
      $matchs = $this->db->get();
      //$field_array = $this->db->query("select * from ".$table_name)->list_fields();
      $field_array = $this->db->list_fields($table_name);
      $field_array_mand = array_diff($field_array, array('id' , 'match_id'));
      $match_result = $matchs->result();
      if(count($match_result) < 1)
      {
        $this->session->set_flashdata('alert_message', 'Failed!. No data exist.');
      }
      else {
        
        $file_result = $this->createExcel($season_link, $match_result, $field_array_mand, $product->description);

        if (!$this->db->update('product',array(
          'file_path' => $file_result["filename"],
          'sample_file_path' => $file_result["sample_filename"],
          'sample_view' => html_entity_decode($file_result['sample_data'],ENT_QUOTES),
          'last_update' => $last_update,
          'updated_at' => date('Y-m-d H:i:s'),
          "match_stats" => count($field_array_mand),
          "game_played" => count($match_result)
        ), 'id = '.$product->id )) {
                return -1;        
          }

        $this->session->set_flashdata('alert_message', 'Updated a product successfully!');
      }
    }

    redirect("admin/product/index");
  }

  public function createExcel($season_link, $match_result, $field_array_mand, $description ){

    // xlsx with color style 
    $file_extension = 'xlsx';
    $filename = "products/excel_sport_data_".str_replace("/","_",trim(trim($season_link,"/"))).".$file_extension";
    $sample_filename = "products/excel_sport_data_".str_replace("/","_",trim(trim($season_link,"/")))."_sample.$file_extension";
    
    $styleArray = [
      'font' => [
          'bold' => false,
          'color' => [
              'argb' => 'FFFFFF',
          ]
      ],
      'alignment' => [
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
      ],
      'borders' => [
          'top' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          ],
      ],
      'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'color' => [
              'argb' => '6bac3f',
          ]
      ],
  ];

  $row_style = array(
      'borders' => array(
          'top' => array('style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 
              'color' => array('rgb' => '6bac3f')
          ),
          'bottom' => array('style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 
              'color' => array('rgb' => '6bac3f')
          )
      ),
      'alignment' => [
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
      ],
  );

  $total_rows = count($match_result);
  $total_columns = count($field_array_mand);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    for($i = 0; $i < $total_columns; $i++){
        if($i == 0){
            $spreadsheet->setActiveSheetIndex(0)->getColumnDimension($this->_number_alphabet($i+1))->setWidth(60);
        }else{
            $spreadsheet->setActiveSheetIndex(0)->getColumnDimension($this->_number_alphabet($i+1))->setWidth(37);
        }
    }

    $spreadsheet->getActiveSheet()->getStyle('A1:'.$this->_number_alphabet($total_columns).'1')->applyFromArray($styleArray);
    $spreadsheet->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(34.5);

    for($i = 1;$i <= $total_rows+1; $i++){        
        $spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$i.':'.$this->_number_alphabet($total_columns).$i)->applyFromArray($row_style);
        $spreadsheet->setActiveSheetIndex(0)->getRowDimension($i+1)->setRowHeight(32);

    }

    $k = 1;
    foreach($field_array_mand as $field){
      $sheet->setCellValue($this->_number_alphabet($k).'1', $field);
      $k++;
    }

    $spreadsheet_sample = new Spreadsheet();
    $sheet_sample = $spreadsheet_sample->getActiveSheet();

    for($i = 0; $i < $total_columns; $i++){
        if($i == 0){
            $spreadsheet_sample->setActiveSheetIndex(0)->getColumnDimension($this->_number_alphabet($i+1))->setWidth(60);
        }else{
            $spreadsheet_sample->setActiveSheetIndex(0)->getColumnDimension($this->_number_alphabet($i+1))->setWidth(37);
        }
    }

    $spreadsheet_sample->getActiveSheet()->getStyle('A1:'.$this->_number_alphabet($total_columns).'1')->applyFromArray($styleArray);
    $spreadsheet_sample->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(34.5);

    for($i = 1;$i <= $total_rows+1; $i++){        
        $spreadsheet_sample->setActiveSheetIndex(0)->getStyle('A'.$i.':'.$this->_number_alphabet($total_columns).$i)->applyFromArray($row_style);
        $spreadsheet_sample->setActiveSheetIndex(0)->getRowDimension($i+1)->setRowHeight(32);
    }

    $k = 1;
    foreach($field_array_mand as $field){
      $sheet_sample->setCellValue($this->_number_alphabet($k).'1', $field);
      $k++;
    }

    $rows = 2;

    $data = '<table class="table table-striped- table-bordered table-hover table-checkable dataTable" style = ""> <thead> <tr>';
    foreach($field_array_mand as $field){
          $data .= '<th>'.$field . "</th>";
    }
    $data .= "</tr> </thead> <tbody>";
    
    foreach($match_result as $item)
    { 
      $data .= "<tr>";
      
      //$line = array();
      // foreach($field_array_mand as $field){
      //     if($rows < 3 )
      //       $data .= '<td>'.$item->$field . "</td>";
      //     $line[] = $item->$field;
      // }
      $k = 1;
      foreach($field_array_mand as $field){

        if ($rows%2) {
          $spreadsheet->getActiveSheet()->getStyle($this->_number_alphabet($k).$rows)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e2efda'); //e2efda
          $spreadsheet_sample->getActiveSheet()->getStyle($this->_number_alphabet($k).$rows)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('e2efda'); //e2efda
        }

        if( $rows < 12 ){
          $data .= '<td>'. $item->$field . "</td>";
          $sheet_sample->setCellValue($this->_number_alphabet($k).$rows, $item->$field);
        }

        $sheet->setCellValue($this->_number_alphabet($k).$rows, $item->$field);
        $k++;
      }
      //fputcsv($fp, $line);
      $data .= "</tr>";
      $rows+=1;
    }
    if ($rows > 12){
      $rows = 12;
    }
    $spreadsheet_sample->getActiveSheet()->getStyle("A".$rows)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f1e5bc'); //e2efda
    $sheet_sample->setCellValue("A".$rows, $description);
    
    $data .= " </tbody></table><p>".$description."</p>";

    $writer = new Xlsx($spreadsheet);
    $writer->save(APPPATH.$filename);

    $writer_sample = new Xlsx($spreadsheet_sample);
    $writer_sample->save(APPPATH.$sample_filename);

    return array("filename" => $filename, "sample_filename" => $sample_filename, "sample_data" => $data);
  }

  public function ajaxLeagueList()
  {
    $sport_id = $this->input->post("sport_id");
    $country_link = $this->input->post("country_link");
    echo json_encode($this->Admin_model->getLeagues($sport_id,$country_link));
    exit();
  }
    
  public function ajaxSeasonList()
  {
    $country_id = $this->input->post("country_link");
    $league_id = $this->input->post("league_id");
    echo json_encode($this->Admin_model->getSeasons($country_id, $league_id ));
    exit();
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

      $this->db->from('product');
      $this->db->select('product.*, country.name as country_name, league.name as league_name,  sport.name as sport_name, season.name as season_name');
      $this->db->join('country', 'country.id = product.country_id', 'left');
      $this->db->join('league', 'league.id = product.league_id', 'left');
      $this->db->join('sport', 'sport.id = product.category_id', 'left');
      $this->db->join('season', 'season.link = product.season_link', 'left');

      $valid_columns = array(
          0=>'sport.name',
          1=>'country.name',
          2=>'season.name',
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
              'MatchStats' => $rows->match_stats,
              'GamePlayed' => $rows->game_played,
              'Price' => $rows->price,
              'LastUpdate' => $rows->last_update,
              'Actions' => '<a href= "'.base_url().'admin/product/sample_download/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Sample Data"><i class="la la-print"></i></a><a href= "'.base_url().'admin/product/download/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Download"><i class="fa flaticon2-download-2"></i></a> <a href="'.base_url().'admin/product/edit/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="la la-edit"></i></a><a href="'.base_url().'admin/product/delete/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="la la-trash"></i></a>'
              //'Actions' => '<a href= "#" onclick="viewSampleData('.$rows->id.');" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Sample Data"><i class="la la-print"></i></a><a href= "'.base_url().'admin/product/download/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Download"><i class="fa flaticon2-download-2"></i></a> <a href="'.base_url().'admin/product/edit/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="la la-edit"></i></a><a href="'.base_url().'admin/product/delete/'.$rows->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><i class="la la-trash"></i></a>'
          );     
      }
      //$countTotalProducts = $this->Admin_model->countTotalProducts();
      $output = array(
          "draw" => $draw,
          "recordsTotal" => $countTotalProducts,
          "recordsFiltered" => $countTotalProducts,
          "data" => $data
      );
      echo json_encode($output);
      exit();
  }
  public function ajaxViewSample()
  {
    $product_id = intval($this->input->post("product_id"));
    echo json_encode($this->Admin_model->getViewSample($product_id));
    exit();
  }
  public function download($id){
    $this->load->helper('download');
    $fileinfo = $this->Admin_model->getFilePath($id);
    if(!empty($fileinfo)){
      force_download(APPPATH.$fileinfo, NULL);
    } else {
      redirect("admin/product/index");
    }
    
  }

  public function sample_download($id){
    $this->load->helper('download');
    $fileinfo = $this->Admin_model->getSampleFilePath($id);
    if(!empty($fileinfo)){
      force_download(APPPATH.$fileinfo, NULL);
    } else {
      redirect("admin/product/index");
    }
    
  }

  public function delete($id){
    $this->db->trans_start();
    $this->db->where('id', $id);
    $this->db->delete('product');  
    $this->db->trans_complete();
    $this->session->set_flashdata('alert_message', 'Deleted a product successfully!');
    redirect("admin/product/index");
  }
}

?>