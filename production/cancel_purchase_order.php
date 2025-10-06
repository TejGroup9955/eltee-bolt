
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

<div class="modal fade" id="LoadRefundPOModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="RefundPOModalHead"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <div id="POBasicDetailsContent"> 
                           
                </div>
                <br>
                <div id="RefundDetailsContent"> 
                           
                </div>
                <br>
                <div class="row">
                    <form id="frmRefundPO" enctype="multipart/form-data" >
                        <div class="col-md-4" style="max-width: 100% !important;">
                            <input type="hidden" class="form-control" id="RefundPOId" name="RefundPOId">
                            <input type="hidden" class="form-control" id="POTotalAmount" name="POTotalAmount">
                            <input type="hidden" class="form-control" id="POPaidAmount" name="POPaidAmount">
                            <input type="hidden" class="form-control" id="pocurrencycode" name="pocurrencycode">
                            <label for="refund_amount">Refund Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="refund_amount" name="refund_amount" required min="0">
                        </div>
                        <div class="col-md-4">
                            <br><button type="submit" class="btn btn-success btn-sm">Add Refund</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>    

<div class="modal fade" id="LoadRefundDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="RefundDetailsModalHead"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <div id="DivLoadRefundDetails"> 
                           
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

        $('#frmRefundPO').submit(function(e) {
            e.preventDefault();

            let refundAmount = parseFloat($('#refund_amount').val());
            let paidAmount = parseFloat($('#POPaidAmount').val());
            let pocurrencycode = $('#pocurrencycode').val();

            if (isNaN(refundAmount) || refundAmount <= 0) {
                Swal.fire('Invalid Input', 'Please enter a valid refund amount.', 'warning');
                return;
            }

            if (refundAmount > paidAmount) {
                Swal.fire('Invalid Refund', 'Refund amount cannot be greater than '+paidAmount+' '+pocurrencycode, 'error');
                return;
            }

            Swal.fire({
            title: 'Are you sure?',
            text: "Do you Really Want To Refund Amount?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit!'
            }).then((result) => {
                if (result.isConfirmed) {

                var formData = {
                    Flag: 'SaveRefundPO',
                    po_id: $('#RefundPOId').val(),
                    refund_amount: $('#refund_amount').val(),
                    POTotalAmount: $('#POTotalAmount').val()
                };

                $.post("operation/purchase_order_operation.php", formData, function(response) {
                    if (response.trim() === "Success") {
                        Swal.fire(
                            'Well Done!',
                            'Refund saved successfully.',
                                'success'
                        );
                        //alert("Refund saved successfully.");
                        $('#RefundPOId').val('');
                        $('#POTotalAmount').val('');
                        $('#POPaidAmount').val('');
                        $('#pocurrencycode').val('');
                        $('#refund_amount').val('');
                        $('#LoadRefundPOModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert("Error: " + response);
                    }
                });


                }
            });
        });
   });
function ShowProFormaList()
{
    $.post("operation/purchase_order_operation.php",{
          Flag:"ShowCancelPurchaseOrderList"
    },function(data,success){
          $("#divRecordProForma").html(data);
          $("#dtlRecord").DataTable().destroy();
          $("#dtlRecord").DataTable();
    });
}

  function RefundPO(po_id, po_custom_number)
  {
      $("#LoadRefundPOModal").modal("toggle");
      $("#RefundPOModalHead").html("Refund PO : "+po_custom_number);
      $("#RefundPOId").val(po_id);
      $.post("operation/purchase_order_operation.php",{
        Flag:"LoadRefundDetails",
        po_id:po_id
      }, function(data) {
        //console.log(data);
        var res = JSON.parse(data);
        $("#POTotalAmount").val(res.grand_total);
        $("#POPaidAmount").val(res.popaidamount);
        $("#pocurrencycode").val(res.pocurrencycode);
        $("#POBasicDetailsContent").html(res.basicpodetails);
        $("#RefundDetailsContent").html(res.popaymentdetails);
      });
  } 
  function LoadRefundDetailsview(po_id,po_custom_number)
  {
        $.post("operation/purchase_order_operation.php",{
            Flag:"LoadRefundDetailsview",
            po_id :po_id
        },function(data,success){
            $("#LoadRefundDetailModal").modal("toggle");
            $("#RefundDetailsModalHead").html("Refunded PO Details : "+po_custom_number);
            $("#DivLoadRefundDetails").html(data);
        });
  }
</script>