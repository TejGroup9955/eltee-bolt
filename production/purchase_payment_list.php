
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -818px;
  }
  p{
    // margin-bottom: 1rem;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPurchasepayment"></div>     
            </div>
        </div>
      </div>
  </div>
</div>  

<div class="modal fade" id="mdlPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Payment Details</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <form id="frmPaymentEntryPo" method="POST">          
                <div class="modal-body">
                    <div class="row" id="divbasicpaymentdetails" style="    margin-top: -45px;">

                    </div>
                    <div class="row m-2">
                      <table class="table table-bordered">
                          <thead >
                              <tr>
                                  <th><i class="fas fa-calendar-alt"></i> Date</th>
                                  <th> Amount</th>
                                  <th> Status</th>
                                  <th> Document</th>
                              </tr>
                          </thead>
                          <tbody id="divloaddpreviouspaymentshort">
                              
                          </tbody>
                      </table>
                    </div>
                    <div class="row mt-3">
                      <div class="col-md-4">
                        <div class="mb-1">
                          <label class="form-label">Balance Amount</label>
                          <input type="hidden" id="txtpono" name="txtpono" readonly>
                          <input type="hidden" id="txtpocustomno" name="txtpocustomno" readonly>
                          <input type="text" id="totalAmount" name="totalAmount" class="form-control required" required readonly>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-1">
                          <label class="form-label">Enter Payment Amount</label>
                          <input type="number" id="paymentAmount" name="paymentAmount" class="form-control required" required placeholder="Enter amount">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-1">
                          <label class="form-label">Remaining Balance</label>
                          <input type="text" id="remainingBalance" name="remainingBalance" class="form-control required" required readonly>
                        </div>
                      </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSave">Request For Approval</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
  include_once('footer.php');
?>
<script>
  
    $(document).ready(function() {
        ShowPurchaseOrderPaymentList();
        $("#paymentAmount").on("input", function() {
            let totalAmount = $("#totalAmount").val();
            let enteredAmount = parseFloat($(this).val()) || 0;
            let remainingBalance = totalAmount - enteredAmount;
            if(remainingBalance<0) { $("#paymentAmount").val(totalAmount);  }
            $("#remainingBalance").val(remainingBalance >= 0 ? remainingBalance : 0);
        });

        $("#frmPaymentEntryPo").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this); // Use FormData instead of serialize
            formData.append("Flag", "SavePOPaymentDetails");
            if($("#paymentAmount").val()<=0)
            {
                alert("Please Enter Valid Amount")
            }
            else{
                $.ajax({
                    url:"operation/payment_receive_operation.php",
                    type: "POST", 
                    data: formData,
                    contentType: false, 
                    processData: false, 
                    success: function (response) {
                        //console.log(response);
                        if(response=="Added")
                        {
                            Swal.fire({
                                title: 'Success',
                                text: "Payment Entry Added Successfully",
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="purchase_payment_list.php";
                            }, 2000);
                        }
                        else
                        {
                            Swal.fire({
                                title: 'OOps',
                                text: "Connection Problem!!! Please try again....",
                                icon: 'warning',
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX Error: ", textStatus, errorThrown);
                        alert("An error occurred while submitting the data.");
                    }
                });
            }
        });
    });


    function ShowPurchaseOrderPaymentList()
    {
        $.post("operation/purchase_order_operation.php",{
              Flag:"ShowPurchaseOrderPaymentList"
        },function(data,success){
              $("#divRecordPurchasepayment").html(data);
              $("#dtlRecord").DataTable({
                dom: '<"d-flex justify-content-between"lfB>rtip', 
                buttons: [
                    {
                        // text: '<i class="fa fa-plus"></i> New Purchase Order',
                        // className: 'btn btn-primary btn-sm btn-round',
                        // action: function () {
                        //     window.location.href = 'purchase_direct_po.php';
                        // }
                    }
                ]
              });
        });
    }

    function ShowPurchaseOrderPaymentModal(po_id)
    {
        $.post("operation/purchase_order_operation.php",{
              Flag:"ShowPurchaseOrderPaymentModal",
              po_id:po_id
        },function(data,success){
              console.log(data);
              var res = JSON.parse(data);
              $("#txtpocustomno").val(res.po_custom_number);
              $("#totalAmount").val(res.CurrentRemainingAmt);
              $("#txtpono").val(res.po_id);
              $("#mdlPaymentModal").modal("toggle");
              $("#divbasicpaymentdetails").html(res.basicpodetails); 
              PurchasePreviousPaidPaymentDetails(po_id)
        });
    }
    function PurchasePreviousPaidPaymentDetails(po_id)
    {
        $.post("operation/purchase_order_operation.php",{
          Flag:"PurchasePreviousPaidPaymentDetails",
          po_id:po_id
        },function(data,success){
            $("#divloaddpreviouspaymentshort").html(data);
        });
    }

  //   function SendForApproval(po_id)
  // {
  //     Swal.fire({
  //       title: 'Are you sure?',
  //       text: "Do you Really Want To Send For Approval?",
  //       icon: 'question',
  //       showCancelButton: true,
  //       confirmButtonColor: '#3085d6',
  //       cancelButtonColor: '#d33',
  //       confirmButtonText: 'Yes, Submit!'
  //     }).then((result) => {
  //       if (result.isConfirmed) {
  //         $.post("operation/purchase_order_operation.php", {
  //           Flag: "SendForApproval",
  //           po_id: po_id,
  //         }, function(data, success) {
  //           if (data == "Approved") {
  //             Swal.fire(
  //               'Well Done!',
  //               'Purchase Order Sent For Approval Sucessfully.',
  //               'success'
  //             );
  //           } else {
  //             Swal.fire(
  //               'Error!',
  //               data,
  //               'error'
  //             );
  //           }
  //           ShowPurchaseOrderList();
  //         }); 
  //       }
  //     });
  // }
</script>