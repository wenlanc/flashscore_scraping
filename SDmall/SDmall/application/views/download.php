<style>
.kt-datatable__pager-link--active{
  background: #009245!important;
}
</style>
  <div class = "kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="row mt-5 mb-5">
      <span class="col-lg-3 col-md-3 text-green ft-6 pl-3"><i class="flaticon2-download-2 text-normal"></i> &nbspMY DOWNLOADS</span>
      <div class="col-lg-2 col-md-2 form-group-sub kt-align-right">
          <label class="col-form-label" style="color: var(--forground_text_active) !important;">Filters</label>
        </div>
        <div class = "col-lg-2 col-md-2 form-group-sub m-0">
          <select class = "form-control kt-select2 selectpicker_home" name = "sportSearch" id = "sportSearch">
            <option value = "">--Sport--</option>
            <?php
              foreach($sports as $sport){
                echo "<option value = ".$sport["id"]." > ".$sport["name"]." </option>";
              }
            ?>
          </select>
        </div>
        <div class = "col-lg-2 col-md-2 form-group-sub m-0">
          <select class = "form-control kt-select2 selectpicker1 selectpicker_home" name = "countrySearch" id = "countrySearch">
            <option value = "">--Country--</option>
            <?php
              foreach($countrys as $country){
                echo "<option value = ".$country["id"]."  data-name = '".$country["link"]."'> ".$country["name"]." </option>";
              }
            ?>
          </select>
        </div>
        <div class = "col-lg-2 col-md-2 form-group-sub m-0 ">
          <select class = "form-control selectpicker1 selectpicker_home competitionSearchSelect" name = "competitionSearch" id = "competitionSearch">
            <option value = "">--League--</option>
            <?php
              foreach($leagues as $league){
                echo "<option value = ".$league["id"]."  data-name = '".$league["link"]."'> ".$league["name"]." </option>";
              }
            ?>
        </select>
        </div>
    </div>

    <?php if($this->session->flashdata('checkoutError')) {?>
      <div class="alert alert-info fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
        <div class="alert-text"><?php echo $this->session->flashdata('checkoutError');?></div>
        <div class="alert-close">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="la la-close"></i></span>
          </button>
        </div>
      </div>
      <?php } ?>

    <!-- begin:: Table -->
    <div class = "kt-datatable mt-5"></div>
    <!-- end:: Table -->
    <div class = "row mt-5 mb-5" style = "display:none;">
      <div class="col-md-5"></div>
      <div class="col-md-2 kt-align-center">
        <button class = "btn btn-loadmore">Lead More...</button>
      </div>
      <div class="col-md-5">
    </div>
  </div>

<!-- Modal -->
<div class="modal fade" id="sample_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Sample Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body" style = "overflow-x: scroll;" id = "sample_view_modal_content">
      </div>
    </div>
  </div>
</div>

  <script>
    var base_url = '<?php echo base_url();?>';
  </script>
  <script src = "<?=base_url()?>assets/js/index/account/download.js" type = "text/javascript"></script>
