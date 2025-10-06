
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -150px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPaymentReceive"></div>     
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
    ShowPaymentList();
   });

    function ShowPaymentList()
    {
        $.post("operation/payment_receive_operation.php",{
              Flag:"ShowPaymentList"
        },function(data,success){
              $("#divRecordPaymentReceive").html(data);
              $("#dtlRecord").DataTable({
                dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
                buttons: [
                    {
                        
                    }
                ]
              });
        });
    }

   
</script>