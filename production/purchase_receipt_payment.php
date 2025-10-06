<?php 
include_once('header.php'); 
?>
<style>
    p{
        padding:3px;
        margin-bottom:0px;
    }
    td{
        font-size: 12px;
    }
    #dtlRecord_filter{
        margin-right: -518px;
    }
    .btn-sm{
        font-size: 0.525rem;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h6 class="text-left text-dark mb-1"><i class="fas money-check-alt"></i> Payment Details</h6>
                        <div class="table-responsive" id="divRecordPurchasepaymentreceiptList"></div>     
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdlPaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Payment Transaction Details</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <hr>
                    <form id="frmPaymentTransactionPo" method="POST">          
                        <div class="modal-body">
                            <input type="hidden" id="customer_receipt_id" name="customer_receipt_id" readonly>
                                  
                                    <div class="row">                 
                                    <div class="col-md-6" >
                                            <div class="mb-1">
                                                <label>Payment Mode</label>                 
                                                <select id="payment_mode" required="" name="payment_mode" onchange="OnSelectionChange(this)" class="form-control">
                                                <option value="">Please Select </option>
                                                <option>By Cash</option>
                                                <option>By Cheque</option>
                                                <option>By DD</option>
                                                <option>By NEFT</option>
                                                <option>By RTGS</option>
                                                <option>By TT</option>
                                                <option>By LT</option>
                                                <option>None</option>
                                                </select>
                                            </div>
                                    </div> 
                                   </div>
                                    <div id="by_cash">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>Voucher Number</label>
                                                <input type="text" class="form-control Number by_cash" placeholder="Please Enter Voucher Number"  name="voucher_number" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Voucher Date</label>
                                                <input type="date" placeholder="Please Enter Voucher Date"  class="form-control by_cash" name="voucher_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Received By</label>
                                                <input type="text" placeholder="Please Enter Received By" class="form-control by_cash" name="received_by" >
                                            </div>
                                        </div>
                                    </div>
                                                               
                                    <div id="by_cheque">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>Cheque Number</label>
                                                <input type="text" class="form-control Number by_cheque" placeholder="Please Enter Cheque Number" name="cheque_number" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Cheque Date</label>
                                                <input type="date" placeholder="Please Enter Cheque Date"  class="form-control by_cheque" name="cheque_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Name</label>
                                                <input type="text" placeholder="Please Enter Bank Name"  class="form-control by_cheque" name="cheque_bank_name" >
                                            </div>
                                        </div>
                                    </div>
                                    <div id="by_dd">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>DD Number</label>
                                                <input type="text" class="form-control Number by_dd" placeholder="Please Enter DD Number"  name="dd_number" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>DD Date</label>
                                                <input type="date" placeholder="Please Enter DD Date"  class="form-control datepicker by_dd" name="dd_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Name</label>
                                                <input type="text" placeholder="Please Enter Bank Name"  class="form-control by_dd" name="dd_bank_name" >
                                            </div>
                                        </div>
                                    </div>
                                    <div id="by_neft">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>NEFT Date</label>
                                                <input type="date" placeholder="Please Enter NEFT Date"  class="form-control datepicker by_neft" name="neft_date">
                                            </div>
                                            <div class="col-md-4">
                                                <label>UTR Number</label>
                                                <input type="text" class="form-control by_neft" placeholder="Please Enter UTR Number" name="utr_number_neft" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Details</label>
                                                <input type="text" placeholder="Please Enter Bank Details"  class="form-control by_neft" name="bank_details_neft" >
                                            </div>
                                        </div>
                                    </div>
                                    <div id="by_rtgs">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>RTGS Date</label>
                                                <input type="date" placeholder="Please Enter RTGS Date" class="form-control datepicker by_rtgs" name="rtgs_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>UTR Number</label>
                                                <input type="text" class="form-control by_rtgs" placeholder="Please Enter UTR Number" name="utr_number_rtgs" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Details</label>
                                                <input type="text" placeholder="Please Enter Bank Details" class="form-control by_rtgs" name="bank_details_rtgs" >
                                            </div>
                                        </div>
                                    </div>
                                    <div id="by_tt">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>TT Date</label>
                                                <input type="date" placeholder="Please Enter TT Date" class="form-control by_tt" name="tt_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Ref. Number</label>
                                                <input type="text" class="form-control by_tt" placeholder="Please Enter Ref. Number" name="tt_ref_number" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Details</label>
                                                <input type="text" placeholder="Please Enter Bank Details" class="form-control by_tt" name="bank_details_tt" >
                                            </div>
                                        </div>
                                    </div>
                                    <div id="by_lt">
                                        <div class="row" style="line-height:25px">
                                            <div class="col-md-4">
                                                <label>LT Date</label>
                                                <input type="date" placeholder="Please Enter LT Date"  class="form-control datepicker by_lt" name="lt_date" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Ref. Number</label>
                                                <input type="text" class="form-control by_lt" placeholder="Please Enter Ref. Number" name="lt_ref_number" >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bank Details</label>
                                                <input type="text" placeholder="Please Enter Bank Details"  class="form-control by_lt" name="bank_details_lt" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="line-height:25px">
                                         <div class="col-md-12" >
                                            <label class="form-label">Upload TT Proof</label>
                                            <input type="file" id="txtTransactionProof" name="txtTransactionProof" class="form-control dropify required" required data-height="100">
                                        </div>
                                        
                                    </div>
                                    
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdlPaymentModal_TT" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">TT Details</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <hr>
                    <form id="frmPaymentTransactionPoTT" method="POST">          
                        <div class="modal-body">
                            <input type="hidden" id="customer_receipt_id_tt" name="customer_receipt_id_tt" readonly>
                            <div class="row">
                                <div class="col-md-12" >
                                    <label class="form-label">Upload TT File</label>
                                    <input type="file" id="txtTransactionTTProof" name="txtTransactionTTProof" class="form-control dropify required" required data-height="100">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include_once('footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" ></script>

<script type="text/javascript">
    
    $(document).ready(function() {
          $(".dropify").dropify();
          ShowPaymentReceiptList();
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none'); 
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');

          $("#frmPaymentTransactionPo").submit(function(e){
                e.preventDefault();
                var formData = new FormData(this); // Use FormData instead of serialize
                formData.append("Flag", "SavePaymentTransactionPO");
                var form = event.target;
                var fileInput = form.querySelector('input[name="txtTransactionProof"]'); 
                if (fileInput && fileInput.files[0]) {
                    formData.append("TransactionProof", fileInput.files[0]);
                }
                if($("#payment_mode").val() == "")
                {
                    alert("Please Select Payment Mode")
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
                                    text: "Payment Transaction Details Added Successfully",
                                    icon: 'success',
                                });
                                setTimeout(() => {
                                    window.location.href="purchase_receipt_payment.php";
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

          $("#frmPaymentTransactionPoTT").submit(function(e){
                e.preventDefault();
                var formData = new FormData(this); // Use FormData instead of serialize
                formData.append("Flag", "SaveTransactionPO_TTFile");
                var form = event.target;
                var fileInput = form.querySelector('input[name="txtTransactionTTProof"]'); 
                if (fileInput && fileInput.files[0]) {
                    formData.append("TransactionTTFile", fileInput.files[0]);
                }
                
                    $.ajax({
                        url:"operation/payment_receive_operation.php",
                        type: "POST", 
                        data: formData,
                        contentType: false, 
                        processData: false, 
                        success: function (response) {
                            console.log(response);
                            if(response=="Added")
                            {
                                Swal.fire({
                                    title: 'Success',
                                    text: "TT File Uploaded Successfully",
                                    icon: 'success',
                                });
                                setTimeout(() => {
                                    window.location.href="purchase_receipt_payment.php";
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
                
         });
    });

    function ShowPaymentReceiptList()
    {
        $.post("operation/payment_receive_operation.php",{
              Flag:"ShowPaymentReceiptList"
        },function(data,success){
              $("#divRecordPurchasepaymentreceiptList").html(data);
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

    function ApprovePurchaseOrderPayment(id,status){

        if(confirm("Are You Sure To Approve/Reject ?") == true){
            $.post("operation/payment_receive_operation.php",{
                  Flag:"ApprovePurchaseOrderPayment",
                  customer_receipt_id:id,
                  status:status
            },function(data,success){
                  console.log(data);
                  var res = JSON.parse(data);
                  if(res.error==0)
                  {
                                Swal.fire({
                                    title: 'Success',
                                    text: res.msg,
                                    icon: 'success',
                                });
                                setTimeout(() => {
                                    window.location.href="purchase_receipt_payment.php";
                                }, 2000);
                   }
                   else
                   {
                                Swal.fire({
                                    title: 'OOps',
                                    text: res.msg,
                                    icon: 'warning',
                                });
                   }
            });
        }
    }

    function PurchaseOrderTransactionStatus(id, status){

        if(confirm("Are You Sure ?") == true){
            $.post("operation/payment_receive_operation.php",{
                  Flag:"PurchaseOrderTransactionStatus",
                  customer_receipt_id:id,
                  status:status
            },function(data,success){
                  if(data == "Updated")
                  {
                        Swal.fire({
                            title: 'Success',
                            text: "Payment Status Updated Successfully",
                            icon: 'success',
                        });
                        setTimeout(() => {
                            window.location.href="purchase_receipt_payment.php";
                        }, 2000);
                   }
                   else
                    {
                        Swal.fire({
                            title: 'OOps',
                            text: "Something Went Wrong",
                            icon: 'warning',
                        });
                   }
            });
        }
    }

    function OnSelectionChange (select) {
        var selectedOption = select.options[select.selectedIndex].value;
        if(selectedOption == "" || selectedOption == "None")
        {
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none'); 
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');
       
        }
        if(selectedOption == "By Cash")
        {
          $('#by_cash').css('display' , 'block');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');

        }
        if(selectedOption == "By Cheque")
        {
          $('#by_cheque').css('display' , 'block');
          $('#by_cash').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');

        }
        if(selectedOption == "By DD")
        {
          $('#by_dd').css('display' , 'block');
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');

        }
        if(selectedOption == "By NEFT")
        {
          $('#by_neft').css('display' , 'block');
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_rtgs').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');

        
        }
        if(selectedOption == "By RTGS")
        {
          $('#by_rtgs').css('display' , 'block');
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'none');
          
        }
        if(selectedOption == "By TT")
        {
          $('#by_rtgs').css('display' , 'none');
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_tt').css('display' , 'block');
          $('#by_lt').css('display' , 'none');
         
        }
        if(selectedOption == "By LT")
        {
          $('#by_rtgs').css('display' , 'none');
          $('#by_cash').css('display' , 'none');
          $('#by_cheque').css('display' , 'none');
          $('#by_dd').css('display' , 'none');
          $('#by_neft').css('display' , 'none');
          $('#by_tt').css('display' , 'none');
          $('#by_lt').css('display' , 'block');
        }
  }
    function PurchaseOrderTransaction(id){
        $("#customer_receipt_id").val(id);
        $("#mdlPaymentModal").modal("toggle");
    }

    function PurchaseOrderTransactionTTFile(id){
        $("#customer_receipt_id_tt").val(id);
        $("#mdlPaymentModal_TT").modal("toggle");
    }

    
</script>