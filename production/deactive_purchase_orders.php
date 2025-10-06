
<?php include_once('header.php'); ?>
<style>
  .btn-group{
    height: 37px;
  }
  #dtlRecord_filter{
    margin-right: -405px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordProForma"></div>     
            </div>
        </div>
      </div>
  </div>
</div>     
<?php
  include_once('footer.php');
?>
<script>
  $(document).ready(function(){
    ShowProFormaList();
   });
function ShowProFormaList()
{
    $.post("operation/purchase_order_operation.php",{
          Flag:"ShowDeactivePurchaseOrderList"
    },function(data,success){
          $("#divRecordProForma").html(data);
          $("#dtlRecord").DataTable({
          
          });
    });
}

</script>