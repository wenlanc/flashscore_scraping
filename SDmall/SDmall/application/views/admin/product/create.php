<link href="<?=base_url()?>assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

<?php if($this->session->flashdata('alert_message')) {?>
<div class="alert alert-info fade show" role="alert">
  <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
  <div class="alert-text"><?php echo $this->session->flashdata('alert_message');?></div>
  <div class="alert-close">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true"><i class="la la-close"></i></span>
    </button>
  </div>
</div>
<?php } ?>

<div class="kt-portlet__head kt-portlet__head--lg">
  <div class="kt-portlet__head-label">
    <span class="kt-portlet__head-icon">
      <i class="kt-font-success flaticon2-plus-1"></i>
    </span>
    <h3 class="kt-portlet__head-title dayHead textBold">
      New Product
    </h3>
  </div>
</div>

<!--begin: Datatable -->
<div class = "kt-container mt-4">
  <!--begin::Form-->
  <form class="kt-form kt-form--fit kt-form--label-right" method = "POST" action = "<?=base_url()?>admin/product/create" id = "frmProductCreate">
    
    <div class="kt-alert m-alert--icon alert alert-danger kt-hidden" role="alert" id="kt_form_1_msg" style="display: block;">
      <div class="kt-alert__icon">
        <i class="la la-warning"></i>
      </div>
      <div class="kt-alert__text">
        Oh snap! Change a few things up and try submitting again.
      </div>
      <div class="kt-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
      </div>
    </div>

    <div class="kt-portlet__body">

      <div class="form-group row">
        <label class="col-form-label col-lg-3 col-sm-12">Sport:</label>
        <div class=" col-lg-4 col-md-9 col-sm-12">
          <select class="form-control kt-select2" id="category_id" name="category_id">
            <option></option>
            <?php
              foreach($sports as $sport){
                echo "<option value = ".$sport["id"]." > ".$sport["name"]." </option>";
              }
            ?>
          </select>
        </div>
      </div>
       <div class="form-group row">
        <label class="col-form-label col-lg-3 col-sm-12">Location:</label>
        <div class=" col-lg-4 col-md-9 col-sm-12">
          <select class="form-control kt-select2" id="country_id" name="country_id">
              <option></option>
              <option value = "-1">No Selected</option>
            <?php
              foreach($countrys as $country){
                echo "<option value = ".$country["id"]."  data-name = '".$country["link"]."'> ".$country["name"]." </option>";
              }
            ?>
          </select>
          
        </div>
      </div>
      <input type = "hidden" name = "country_id1" id = "country_id1" value = "" >
      <input type = "hidden" name = "country_link" id = "country_link" value = "" >
      <div class="form-group row">
        <label class="col-form-label col-lg-3 col-sm-12" id = "leagueLabel">Competition:</label>
        <div class=" col-lg-4 col-md-9 col-sm-12">
          <select class="form-control kt-select2" id="league_id" name="league_id">
            <option></option>
          </select>
        </div>
      </div>
     
      <div class="form-group row">
        <label class="col-form-label col-lg-3 col-sm-12">Season:</label>
        <div class=" col-lg-4 col-md-9 col-sm-12">
          <select class="form-control kt-select2" id="season_id" name="season_id">
              <option></option>
          </select>
        </div>
      </div>

      <div class="form-group row" style = "display:none;">
        <label class="col-form-label col-lg-3 col-sm-12"></label>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <input class="form-control" type="number" value="" placeholder = "From" id="season_from" name = "season_from">
        </div> &nbsp;
        <div class="col-lg-2 col-md-4 col-sm-6">
          <input class="form-control" type="number" value="" placeholder = "To"  id="season_to" name = "season_to">
        </div>
      </div>
     
      <div class="form-group row">
        <label class="col-lg-3 col-form-label">Price:</label>
        <div class="col-lg-4 col-md-9 col-sm-12 input-group">
          <div class="input-group-prepend"><span class="input-group-text" id="basic-addon2">&euro;</span></div>
          <input type="number" class="form-control" placeholder="0.99" name = "price" id = "price">
        </div>
      </div>

      <div class="form-group row">
        <label class="col-lg-3 col-form-label">Description:</label>
        <div class="col-lg-4 col-md-9 col-sm-12 input-group">
          
          <textarea class="form-control"  name = "description" id = "description"></textarea>
        </div>
      </div>
        
      <div class="kt-portlet__foot">
        <div class="kt-form__actions">
          <div class="row">
            <div class="col-lg-3">
            </div>
            <div class="col-lg-4 col-md-9 col-sm-12">
              <button type="submit" id = "btnCreateProduct" class="btn btn-success">Submit</button>
              <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </from>
</div>
<!--end: Datatable -->
<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";
</script>
<script src="<?=base_url()?>assets/js/admin/productcreate.js" type="text/javascript"></script>


