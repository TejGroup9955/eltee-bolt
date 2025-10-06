
<?php include_once('header.php'); ?>
<style>
  .btn-group{
    height: 37px;
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
    $.post("operation/pro_forma_operation.php",{
          Flag:"ShowProFormaRequests"
    },function(data,success){
          $("#divRecordProForma").html(data);
          $("#dtlRecord").DataTable({           
          });
    });
}
function ApproveProForma(pi_no, ApproveStatus)
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
            $.post("operation/pro_forma_operation.php", {
                Flag: "ProFormaRequestAction",
                pi_no:pi_no,
                ApproveStatus: ApproveStatus,
            }, function(data, success) {
                if (data == "Approved") {
                    Swal.fire(
                        'Well Done!',
                        'Pro-Forma Status Updated.',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'Error!',
                        data,
                        'error'
                    );
                }
        ShowProFormaList();
            });	
        }
    });
}
</script>