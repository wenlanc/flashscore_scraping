
<div class = "kt-container kt-grid__item">
  <div class="row mt-5 mb-5">
    <span class="text-green ft-6 pl-3"><i class="flaticon-file-2 text-normal"></i> &nbspMY INVOICES</span>
  </div>
  <!-- begin:: Table -->
  <div class = "kt-datatable mt-5"></div>
  <!-- end:: Table -->
  <div class = "row mt-5 mb-5">
    <div class="col-md-12 kt-align-right">
      <form method = "POST" action = "<?php echo base_url();?>invoice/bulkDownloadInvoicePdf" id = "downloadInvoiceForm" ><button type = "submit" class = "btn btn-loadmore">Download<i class="flaticon2-download-2"></i></button></form>
    </div>
  </div>
</div>

<script>
  var base_url = "<?php echo base_url();?>";
</script>
<script src = "<?=base_url()?>assets/js/index/account/invoice.js" type = "text/javascript"></script>

