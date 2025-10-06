<?php 
include_once('header.php'); 
$po_id = base64_decode($_GET['po_id']);

$rstpurchase = mysqli_query($connect,"select p.supplier_pi_no,p.supplier_pi_date,p.grand_total,c.client_name,
u.user_name,pp.* from purchase_order p 
inner join client_master c on c.client_id = p.supplier_id
left join purchase_order_payment pp on pp.purchase_order_id = p.po_id 
inner join user_master u on u.user_id = p.user_id where p.po_id='$po_id' ");
$rwpurchase = mysqli_fetch_assoc($rstpurchase);
extract($rwpurchase);

$rstpaidsum = mysqli_query($connect,"select SUM(paid_amount) as PreviousPaidAmount from purchase_order_receipt_payment where po_no='$po_id'");
$rwpaidsum = mysqli_fetch_assoc($rstpaidsum);
$PreviousPaidAmount = $rwpaidsum['PreviousPaidAmount']??0;
$CurrentRemainingAmt = $rwpurchase['grand_total']-$PreviousPaidAmount;


?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />
<style>
    p{
        padding:3px;
        margin-bottom:0px;
    }
</style>
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card shadow-lg p-0">
                    <div class="card-body">
                        <!-- <h5 class="text-left text-dark mb-4"><i class="fas fa-user-tie"></i> Supplier & Client Info</h5> -->
                        <div class="mb-3">
                            <h3 class="text-primary fw-bold"><i class="fas fa-user me-2"></i> <?= $client_name; ?> </h3>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-2 mb-1">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-warning mb-1">PO No:</h6>
                                    <p class="fw-bold"><?= $supplier_pi_no; ?></p>
                                </div>
                            </div>
                            <div class="col-md-2 mb-1">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-danger mb-1">PO Date:</h6>
                                    <p class="fw-bold"><?= date('d M Y',strtotime($supplier_pi_date)); ?></p>
                                </div>
                            </div>
                    
                            <div class="col-md-2 mb-1">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Total Amount:</h6>
                                    <p class="fw-bold">₹<?= number_format($grand_total); ?></p>
                                </div>
                            </div>
                            <div class="col-md-2 mb-1">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-secondary mb-1">Advance Payment:</h6>
                                    <p class="fw-bold">₹<?= number_format(@$pay_in_advance) ."(". @$pay_percentage."%)" ?></p>
                                </div>
                            </div>
                            <div class="col-md-2 mb-1">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Next Payment</h6>
                                    <p class="fw-bold">₹<?= number_format(@$pay_later) ."(". @$after_percentage."%)" ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h6 class="text-left text-dark mb-1"><i class="fa-solid fa-pen-to-square"></i> Payment Entry</h6>
                        <hr>
                        <form id="frmPaymentEntryPo">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <label class="form-label">Balance Amount</label>
                                        <input type="hidden" id="txtpono" name="txtpono" value="<?= $po_id ?>" readonly>
                                        <input type="hidden" id="txtpocustomno" name="txtpocustomno" value="<?= $supplier_pi_no ?>" readonly>
                                        <input type="text" id="totalAmount" name="totalAmount" class="form-control required" required value="<?= $CurrentRemainingAmt ?>" readonly>
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
                            <div class="row">                 
                                <div class="col-md-12" >
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
                                        <input type="text" class="form-control Number by_cash" placeholder="Please Enter Voucher Number" value="<?php echo @$voucher_number; ?>" name="voucher_number" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Voucher Date</label>
                                        <input type="date" placeholder="Please Enter Voucher Date" value="<?php echo @$voucher_date_d; ?>" class="form-control by_cash" name="voucher_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Received By</label>
                                        <input type="text" placeholder="Please Enter Received By" value="<?php echo @$received_by; ?>" class="form-control by_cash" name="received_by" >
                                    </div>
                                </div>
                            </div>
                                                       
                            <div id="by_cheque">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>Cheque Number</label>
                                        <input type="text" class="form-control Number by_cheque" placeholder="Please Enter Cheque Number" value="<?php echo @$cheque_number; ?>" name="cheque_number" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Cheque Date</label>
                                        <input type="date" placeholder="Please Enter Cheque Date" value="<?php echo @$cheque_date_d; ?>" class="form-control by_cheque" name="cheque_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Name</label>
                                        <input type="text" placeholder="Please Enter Bank Name" value="<?php echo @$cheque_bank_name; ?>" class="form-control by_cheque" name="cheque_bank_name" >
                                    </div>
                                </div>
                            </div>
                            <div id="by_dd">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>DD Number</label>
                                        <input type="text" class="form-control Number by_dd" placeholder="Please Enter DD Number" value="<?php echo @$dd_number; ?>" name="dd_number" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>DD Date</label>
                                        <input type="text" placeholder="Please Enter DD Date" value="<?php echo @$dd_date_d; ?>" class="form-control datepicker by_dd" name="dd_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Name</label>
                                        <input type="text" placeholder="Please Enter Bank Name" value="<?php echo @$dd_bank_name; ?>" class="form-control by_dd" name="dd_bank_name" >
                                    </div>
                                </div>
                            </div>
                            <div id="by_neft">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>NEFT Date</label>
                                        <input type="text" placeholder="Please Enter NEFT Date" value="<?php echo @$neft_date_d; ?>" class="form-control datepicker by_neft" name="neft_date">
                                    </div>
                                    <div class="col-md-4">
                                        <label>UTR Number</label>
                                        <input type="text" class="form-control by_neft" placeholder="Please Enter UTR Number" value="<?php echo @$utr_number_neft; ?>" name="utr_number_neft" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Details</label>
                                        <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_neft; ?>" class="form-control by_neft" name="bank_details_neft" >
                                    </div>
                                </div>
                            </div>
                            <div id="by_rtgs">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>RTGS Date</label>
                                        <input type="text" placeholder="Please Enter RTGS Date" value="<?php echo @$rtgs_date_d; ?>" class="form-control datepicker by_rtgs" name="rtgs_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>UTR Number</label>
                                        <input type="text" class="form-control by_rtgs" placeholder="Please Enter UTR Number" value="<?php echo @$utr_number_rtgs; ?>" name="utr_number_rtgs" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Details</label>
                                        <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control by_rtgs" name="bank_details_rtgs" >
                                    </div>
                                </div>
                            </div>
                            <div id="by_tt">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>TT Date</label>
                                        <input type="date" placeholder="Please Enter TT Date" value="<?php echo @$tt_date_d; ?>" class="form-control by_tt" name="tt_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Ref. Number</label>
                                        <input type="text" class="form-control by_tt" placeholder="Please Enter Ref. Number" value="<?php echo @$ref_number; ?>" name="tt_ref_number" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Details</label>
                                        <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control by_tt" name="bank_details_tt" >
                                    </div>
                                </div>
                            </div>
                            <div id="by_lt">
                                <div class="row" style="line-height:25px">
                                    <div class="col-md-4">
                                        <label>LT Date</label>
                                        <input type="text" placeholder="Please Enter LT Date" value="<?php echo @$lt_date_d; ?>" class="form-control datepicker by_lt" name="lt_date" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Ref. Number</label>
                                        <input type="text" class="form-control by_lt" placeholder="Please Enter Ref. Number" value="<?php echo @$ref_number; ?>" name="lt_ref_number" >
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bank Details</label>
                                        <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control by_lt" name="bank_details_lt" >
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-1 mt-2">
                                <label class="form-label">Upload Payment Proof</label>
                                <input type="file" id="txtTransactionProof" name="txtTransactionProof" class="form-control dropify required" required data-height="100">
                            </div>
                            <div class="d-flex justify-content-start mt-2">
                                <input type="submit" class="btn btn-success" id="savePayment" value="Save"> 
                                <button type="reset" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h6 class="text-left text-dark mb-1"><i class="fas money-check-alt"></i> Payment Details</h6>
                        <table class="table table-bordered">
                            <thead >
                                <tr>
                                    <th><i class="fas fa-calendar-alt"></i> Date</th>
                                    <th> Payment Mode</th>
                                    <th> Transaction Id</th>
                                    <th> Amount</th>
                                    <th><i class="fas fa-paperclip"></i> Document</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $rstpaidamt = mysqli_query($connect,"select * from purchase_order_receipt_payment p 
                                    inner join customer_information c on c.payment_method_id = p.payment_mode
                                    where p.po_no='$po_id'");
                                    if(mysqli_num_rows($rstpaidamt)>0)
                                    {
                                       while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
                                       {
                                            extract($rwpaidamt);
                                            echo "<tr>
                                                <td>".date('d M Y',strtotime($paid_date))."</td>
                                                <td>$payment_mode</td>
                                                <td>#9656565</td>
                                                <td>₹ ".number_format($paid_amount)."</td>";
                                            if($TransactionProof!="")
                                            {
                                                echo "<td><a href='production/$TransactionProof' target='_blank' class='text-primary'><i class='fas fa-file-alt'></i> View</a></td>";
                                            }
                                            else{echo "<td>-</td>"; }
                                            echo "</tr>";
                                       }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" ></script>
<script>
     $('#by_cash').css('display' , 'none');
    $('#by_cheque').css('display' , 'none');
    $('#by_dd').css('display' , 'none');
    $('#by_neft').css('display' , 'none');
    $('#by_rtgs').css('display' , 'none'); 
    $('#by_tt').css('display' , 'none');
    $('#by_lt').css('display' , 'none');
    $('#by_cash').css('display' , 'none');
    $('#by_cheque').css('display' , 'none');
    $('#by_dd').css('display' , 'none');
    $('#by_neft').css('display' , 'none');
    $('#by_rtgs').css('display' , 'none'); 
    $('#by_tt').css('display' , 'none');
    $('#by_lt').css('display' , 'none');
    $(document).ready(function() {
        $(".dropify").dropify();
        $("#paymentAmount").on("input", function() {
            let totalAmount = $("#totalAmount").val();
            let enteredAmount = parseFloat($(this).val()) || 0;
            let remainingBalance = totalAmount - enteredAmount;
            if(remainingBalance<0) { $("#paymentAmount").val(totalAmount);  }
            $("#remainingBalance").val(remainingBalance >= 0 ? remainingBalance : 0);
        });
    });
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
    $(document).ready(function(){
        $("#frmPaymentEntryPo").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this); // Use FormData instead of serialize
            formData.append("Flag", "SavePaymentEntryPO");
            var form = event.target;
            var fileInput = form.querySelector('input[name="txtTransactionProof"]'); 
            if (fileInput && fileInput.files[0]) {
                formData.append("TransactionProof", fileInput.files[0]);
            }
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

</script>

