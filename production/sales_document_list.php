
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordProFormaDocumentListSalesLogin"></div>     
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
          Flag:"ShowProFormaListDocumentListSalesLogin"
    },function(data,success){
          $("#divRecordProFormaDocumentListSalesLogin").html(data);
          $("#dtlRecord").DataTable({
            // dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
            // buttons: [
            //     {
            //         text: '<i class="fa fa-plus"></i> New Pro-Forma Invoice',
            //         className: 'btn btn-primary btn-sm btn-round',
            //         action: function () {
            //             window.location.href = 'Pro-Forma-Invoice.php';
            //         }
            //     }
            // ]
          });
    });
}
function CheckdocumentsStatusUpdate(pi_no)
{
  var c = confirm("Do You really want to update document status as Updated ?");
  if(c==true)
  {
      $.post("operation/pro_forma_operation.php",{
        Flag:"UpdateDocumentCheckStatus",
        pi_no : pi_no
      },function(data,success){
        if(data=="Updated")
        {
          swal.fire(
            'Success',
            'Document Status Updated',
            'success'
          );
          ShowProFormaList();
        }
        else{
          swal.fire(
            'oops',
            'Unable To Update Document Status',
            'error'
          );
          ShowProFormaList();
        }
      });   
  }
}
function UpdateTelexStatus(pi_no)
{
    var c = confirm("Do You really want to update Telex Release Status ?");
    if(c==true)
    {
        $.post("operation/pro_forma_operation.php",{
          Flag:"UpdateTelexStatus",
          pi_no : pi_no
        },function(data,success){
          if(data=="Updated")
          {
            swal.fire(
              'Success',
              'Telex Released Successfully',
              'success'
            );
            ShowProFormaList();
          }
          else{
            swal.fire(
              'oops',
              'Unable To Update Telex Release Status',
              'error'
            );
            ShowProFormaList();
          }
        });   
    }
}
</script>