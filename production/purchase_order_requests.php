
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -450px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPurchaseOrder"></div>     
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
    ShowRequestPurchaseOrderList();
   });

    function ShowRequestPurchaseOrderList()
    {
        $.post("operation/purchase_order_operation.php",{
              Flag:"ShowRequestPurchaseOrderList"
        },function(data,success){
              $("#divRecordPurchaseOrder").html(data);
              $("#dtlRecord").DataTable({
                
              });
        });
    }

    function ApprovePurchaseOrder(po_id, ApproveStatus)
    {
        if(ApproveStatus=="1")
        {
            var btntext = "Do You Want To Approve The Request?";
        }
        else
        {
            var btntext = "Do You Want To Reject The Request?";
        }
        Swal.fire({
            title: 'Are you sure?',
            text: btntext,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("operation/purchase_order_operation.php", {
                    Flag: "PurchaseOrderRequestAction",
                    po_id:po_id,
                    ApproveStatus: ApproveStatus,
                }, function(data, success) {
                    if (data == "Approved") {
                        Swal.fire(
                            'Well Done!',
                            'Purchase Order Approved Successfully',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            data,
                            'error'
                        );
                    }
            ShowRequestPurchaseOrderList();
                }); 
            }
        });
    }

</script>