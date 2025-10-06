<?php include_once('header.php'); ?>

<?php
    $remark = $pi_no = $result_discription = $strcnt = "";
    if(isset($_GET['PI_No']))
    {
        $pi_no =base64_decode($_GET['PI_No']);

        $query ="SELECT pi_custom_number, supplier_lic_no, account_id, supplier_lic_date, pi_invoice_date, pi_valid_date,  grand_total FROM pro_forma_head  WHERE pi_no='$pi_no'";
         $result = $connect->query($query);
         if ($result->num_rows > 0) {
          if($row = $result->fetch_assoc()) {
            $pi_custom_number = $row['pi_custom_number'];
            $supplier_lic_no = $row['supplier_lic_no'];
            $account_id = $row['account_id'];
            $grand_total = round($row['grand_total']);
            $final_total = round($row['grand_total']);

            $supplier_lic_date=strtotime($row["supplier_lic_date"]);
            $supplier_lic_date=$row['supplier_lic_date']=date('d-m-Y', $supplier_lic_date); 

            $pi_invoice_date=strtotime($row["pi_invoice_date"]);
            $pi_invoice_date=$row['pi_invoice_date']=date('d-m-Y', $pi_invoice_date);  

            $pi_valid_date=strtotime($row["pi_valid_date"]);
            $pi_valid_date=$row['pi_valid_date']=date('d-m-Y', $pi_valid_date);

          }
        }
          
        $query1 ="SELECT SUM(paid_amount) as paid_amount1 FROM  pro_forma_receipt_payment WHERE pi_no='$pi_no'";
        $result1 = $connect->query($query1);
        if ($result1->num_rows > 0) {
            while($row1 = $result1->fetch_assoc()) {
              $paid_amount1 = $row1['paid_amount1'];
              if($paid_amount1 == ""){ $paid_amount1 = 0; }
              $remain_amount = $final_total-$paid_amount1;
            }
        }
        else{$remain_amount=$final_total;}
    }

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />

<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"  autocomplete="off" id="frmpaymentpo">
                <input type="hidden" name="pi_no" value="<?php echo $pi_no; ?>" />
                <div class="row">
                    <div class="col-md-3" >
                        <label>PI Number</label>
                          <input type="text" readonly name="po_number" id="po_number" placeholder="Enter PI number" onkeypress="return isNumber(event)" value="<?php echo $pi_custom_number;?>" class="btn btn-danger btn_full " />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>PI Invoice Date</label>
                         <input type="text" readonly name="pi_invoice_date"  style="text-align: center;" placeholder="Enter PI NO " value="<?php echo $pi_invoice_date;?>" class="form-control " />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>Grand Total</label>
                        <input type="number" style="text-align: center;" readonly name="grand_total" id="grand_total" placeholder="Total Amount" class="form-control form_center"  value="<?= @$grand_total; ?>"    />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>Remain Amount</label>
                        <input type="text" style="text-align: center;" name="remain_amount" class="form-control form_center" readonly="" id="remain_amount" value="<?php echo $remain_amount; ?>" />
                        <input type="hidden"  id="remain_amount_temp" value="<?php echo $remain_amount; ?>" />
                    </div> 
                </div>
                
                <br>
                <div class="row" style="display:none">
                    <table class="table table-bordered table-striped jambo_table bulk_action" id="payment_details">
                        <thead>
                        <tr>
                            <th>Payment Mode</th>
                            <th>Advance %</th>
                            <th>Description</th>
                            <th>After %</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php  
                              $query02 ="SELECT * FROM `pro_forma_payment` WHERE `pi_no`='$pi_no'";
                              $result02 = $connect->query($query02);
                              if ($result02->num_rows > 0) {
                                $i=1;
                                while($row02 = $result02->fetch_array()) { 
                                    extract($row02);
                            ?> 
                            <tr id='rowCount1'>
                                <td style="width: 150px;">
                                    <select class="form-control small-input" id="payment_mode" class='form-control small-input' name='payment_mode[]' required>
                                        <option value="">Mode</option>
                                        <?php
                                        $cmd = "SELECT payment_mode_id, payment_mode_name FROM payment_mode";
                                        $result = $connect->query($cmd);
                                        if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            $payment_mode_name = $row['payment_mode_name'];
                                            $payment_mode_iddd = $row['payment_mode_id'];
                                            $selected='';
                                            if($payment_mode_id==$payment_mode_iddd)
                                            {
                                                $selected='selected';
                                            }
                                            echo "<option value='$payment_mode_iddd' $selected>$payment_mode_name</option>";
                                        }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td style="width: 100px;">
                                <input type='text' id='payment_per' oninput="percentage()" class='form-control small-input set_percen Negative' name='payment_per[]' placeholder="Enter %" value="<?= @$pay_percentage; ?>">

                                <input type='hidden' id='pay_in_advance'  class='form-control small-input set_percen' name='pay_in_advance[]' placeholder="Enter %" value="<?= @$pay_in_advance; ?>">

                                <br><strong id="pay_in_advance1" style="margin-top: -10px;"><?= @$pay_in_advance; ?></strong>
                                <strong class="right" id="pay_later_sym1" style="color: #777;" aria-hidden="true"></strong>
                                </td>
                                <td>
                                <select class="form-control small-input" id="payment_desc" class='form-control small-input' name='payment_desc[]' required>
                                    <option value="Before">Before</option>
                                    <?php
                                    $cmd = "SELECT payment_desc_id, payment_description FROM payment_description";
                                    $result = $connect->query($cmd);
                                    if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $payment_description = $row['payment_description'];
                                        $payment_desc_iddd = $row['payment_desc_id'];
                                        $selected='';
                                        if($payment_desc_id==$payment_desc_iddd)
                                        {
                                            $selected='selected';
                                        }
                                        echo "<option value='$payment_desc_iddd' $selected>$payment_description</option>";
                                    }
                                    }
                                    ?>
                                </select>
                                </td>
                                <td style="width: 100px;">
                                <input type='text' readonly id='after_payment_per' class='form-control small-input set_percen Negative' name='after_payment_per[]' placeholder="Enter %" value="<?= @$after_percentage; ?>" >
                                <input type='hidden' id='pay_later' oninput="" class='form-control small-input set_percen' name='pay_later[]' placeholder="Enter pay %" value="<?= @$pay_later; ?>">
                                <br><strong id="pay_later1" style="margin-top: -10px;"><?= @$pay_later; ?></strong>
                                <strong class="right" id="pay_later_sym2" style="color: #777;" aria-hidden="true"></strong>
                                </td>
                                <td>
                                <select class="form-control small-input" id="after_payment_desc" class='form-control small-input' name='after_payment_desc[]' >
                                    <option value="After">After</option>
                                    <?php
                                    $cmd = "SELECT payment_desc_id, payment_description FROM payment_description";
                                    $result = $connect->query($cmd);
                                    if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $payment_description = $row['payment_description'];
                                        $payment_desc_id = $row['payment_desc_id'];

                                        $selected='';
                                        if($after_payment_desc_id==$payment_desc_id)
                                        {
                                            $selected='selected';
                                        }
                                        echo "<option value='$payment_desc_id' $selected>$payment_description</option>";
                                    }
                                    }
                                    ?>
                                </select>
                                </td>
                                <td hidden style="width: 68px;"><input type='text' id='period' class='form-control small-input' name='period[]' placeholder='Days' ></td>
                            </tr>
                            <?php 
                               $i++;}

                             }?>
                        </tbody>
                        <tbody id="add_payment_list" style="background-color: #f5f5f5;">
                        </tbody>
                    </table>
                </div>
                <div class="row" style="line-height:45px">
                    <div class="col-md-3">
                        <label>Amount</label>
                    </div>
                    <div class="col-md-4" >
                        <input type="number" onchange="cal_remain();" placeholder="Please Enter Amount" value="" id="paid_amount" name="paid_amount"  class="form-control small-input"  required />
                    </div>
                     <div class="col-md-2">
                        <label>Upload TT Copy</label>
                    </div>
                    <div class="col-md-3" >
                        <input type="file" id="payment_tt_copy" name="payment_tt_copy"  class="dropify form-control small-input"  required />
                    </div>
                </div> 
                <div class="row" style="line-height:45px">
                    <div class="col-md-3" >
                        <label>Paid Date</label>
                    </div>
                    <div class="col-md-4" >
                        <input type="date" name="paid_date" id="paid_date" placeholder="Please Enter Payment Date" required class="form-control small-input"/>
                    </div> 
                </div>
                <div class="row" style="line-height:45px">
                    <div class="col-md-3" >
                        <label>Payment Mode</label>
                    </div>
                    <div class="col-md-4" >
                        <select id="payment_mode" required name="payment_mode" onchange="OnSelectionChange(this)" class="form-control">
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
                <div id="by_cash">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Voucher Number</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control Number payment by_cash" placeholder="Please Enter Voucher Number" value="<?php echo @$voucher_number; ?>" name="voucher_number" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Voucher Date</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" placeholder="Please Enter Voucher Date" value="<?php echo @$voucher_date_d; ?>" class="form-control payment by_cash" name="voucher_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Received By</label>
                        </div>
                        <div class="col-md-4">
                         <input type="text" placeholder="Please Enter Received By" value="<?php echo @$received_by; ?>" class="form-control payment by_cash" name="received_by" >
                         </div>
                    </div>
                </div>
                <div id="by_cheque">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Cheque Number</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control Number payment by_cheque" placeholder="Please Enter Cheque Number" value="<?php echo @$cheque_number; ?>" name="cheque_number" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Cheque Date</label>
                        </div>
                        <div class="col-md-4">
                           <input type="date" placeholder="Please Enter Cheque Date" value="<?php echo @$cheque_date_d; ?>" class="form-control payment by_cheque" name="cheque_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Name</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" placeholder="Please Enter Bank Name" value="<?php echo @$cheque_bank_name; ?>" class="form-control payment by_cheque" name="cheque_bank_name" >
                        </div>
                    </div>
                </div>
                <div id="by_dd">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>DD Number</label>
                        </div>
                        <div class="col-md-4">
                          <input type="text" class="form-control Number payment by_dd" placeholder="Please Enter DD Number" value="<?php echo @$dd_number; ?>" name="dd_number" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>DD Date</label>
                        </div>
                        <div class="col-md-4">
                          <input type="date" placeholder="Please Enter DD Date" value="<?php echo @$dd_date_d; ?>" class="form-control datepicker payment by_dd" name="dd_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Name</label>
                        </div>
                        <div class="col-md-4">
                          <input type="text" placeholder="Please Enter Bank Name" value="<?php echo @$dd_bank_name; ?>" class="form-control payment by_dd" name="dd_bank_name" >
                        </div>
                    </div>
                </div>
                <div id="by_neft">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>NEFT Date</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" placeholder="Please Enter NEFT Date" value="<?php echo @$neft_date_d; ?>" class="form-control datepicker payment by_neft" name="neft_date">
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>UTR Number</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control payment by_neft" placeholder="Please Enter UTR Number" value="<?php echo @$utr_number_neft; ?>" name="utr_number_neft" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Details</label>
                        </div>
                        <div class="col-md-4">
                          <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_neft; ?>" class="form-control payment by_neft" name="bank_details_neft" >
                        </div>
                    </div>
                </div>
                <div id="by_rtgs">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>RTGS Date</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" placeholder="Please Enter RTGS Date" value="<?php echo @$rtgs_date_d; ?>" class="form-control datepicker payment by_rtgs" name="rtgs_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>UTR Number</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control payment by_rtgs" placeholder="Please Enter UTR Number" value="<?php echo @$utr_number_rtgs; ?>" name="utr_number_rtgs" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Details</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control payment by_rtgs" name="bank_details_rtgs" >
                        </div>
                    </div>
                </div>
                <div id="by_tt">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>TT Date</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" placeholder="Please Enter TT Date" value="<?php echo @$tt_date_d; ?>" class="form-control payment by_tt" name="tt_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Ref. Number</label>
                        </div>
                        <div class="col-md-4">
                             <input type="text" class="form-control payment by_tt" placeholder="Please Enter Ref. Number" value="<?php echo @$ref_number; ?>" name="tt_ref_number" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Details</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control payment by_tt" name="bank_details_tt" >
                        </div>
                    </div>
                </div>
                <div id="by_lt">
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>LT Date</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" placeholder="Please Enter LT Date" value="<?php echo @$lt_date_d; ?>" class="form-control datepicker payment by_lt" name="lt_date" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Ref. Number</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control payment by_lt" placeholder="Please Enter Ref. Number" value="<?php echo @$ref_number; ?>" name="lt_ref_number" >
                        </div>
                    </div>
                    <div class="row" style="line-height:45px">
                        <div class="col-md-3">
                            <label>Bank Details</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" placeholder="Please Enter Bank Details" value="<?php echo @$bank_details_rtgs; ?>" class="form-control payment by_lt" name="bank_details_lt" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mt-4">
                        <input type="submit" class="btn btn-success" id="btnSave" value="Save">
                        <a href="payment_receive.php"><button type="button" class="btn btn-warning">Reset</button></a>
                        <a href="index.php"><button type="button" class="btn btn-secondary">Close</button></a>
                    </div>
                </div>
            </form>
        </div>
      </div>
  </div>
</div>     
<?php
  include_once('footer.php');
?>     
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" ></script>

<script type="text/javascript">
    $(document).ready(function(){
        // $(".dropify").dropify();
    });
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
          
    function calculateDiscount() {
        var discountInput = $("#DiscountAmt").val().trim();
        var totalAmtAll = parseFloat($("#totalAmtAll").val()) || 0;
        var grandTotal = totalAmtAll; 
        var discountAmt = 0;

        if (discountInput.includes("%")) {
            var discountPercentage = parseFloat(discountInput.replace("%", "")) || 0;
            discountAmt = (totalAmtAll * discountPercentage) / 100;
        } else {
            discountAmt = parseFloat(discountInput) || 0;
        }
        if (discountAmt > totalAmtAll) {
            discountAmt = totalAmtAll;
        }
        grandTotal = totalAmtAll - discountAmt;
        $("#grand_total").val(grandTotal.toFixed(2));
    }

    function percentage()
    {
        var total_amt = document.getElementById("grand_total").value;  
        var pay_percentage = document.getElementById("payment_per").value; 
        if(parseFloat(pay_percentage) > 100)
        {
            alert('Please enter valid percentage!!!');
            $('#payment_per').val("");
            $('#payment_per').focus();  
        }
        else
        {
            var left_percetage = 100 - parseFloat(pay_percentage);
            var pay_amt = (parseFloat(total_amt)/100) * parseFloat(pay_percentage);
            var remain_amt = (parseFloat(total_amt)/100) * parseFloat(left_percetage);
            pay_amt = pay_amt.toFixed(2);

            document.getElementById("pay_in_advance").value = pay_amt;
            document.getElementById("pay_in_advance1").innerHTML = pay_amt;
            left_percetage = left_percetage.toFixed(2);
            remain_amt = remain_amt.toFixed(2);

            document.getElementById("after_payment_per").value = left_percetage;
            document.getElementById("pay_later").value = remain_amt;
            document.getElementById("pay_later1").innerHTML = remain_amt;
        }
    }

    function OnSelectionChange (select) {
        var selectedOption = select.options[select.selectedIndex].value;
          $('.by_cash').val('');
          $('.by_cheque').val('');
          $('.by_dd').val('');
          $('.by_neft').val('');
          $('.by_rtgs').val(''); 
          $('.by_tt').val('');
          $('.by_lt').val('');
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

  function cal_remain()
  {
    var remain_amt = document.getElementById("remain_amount").value;
    var remain_amt1 = document.getElementById("remain_amount_temp").value;
    var paid_amt = document.getElementById("paid_amount").value;  
    
    document.getElementById("remain_amount").value = parseFloat(remain_amt1) - parseFloat(paid_amt);
    if(paid_amt > parseFloat(remain_amt1))  
    {
      alert("Your Paid Amount is Greater Then Remaining Amount...")
      document.getElementById("paid_amount").value = remain_amt1;
      document.getElementById("remain_amount").value = 0;
    }
    if(paid_amt=="")
    {
      document.getElementById("remain_amount").value = remain_amt1;
    }
  }

    $(document).ready(function(){
        $("#frmpaymentpo").submit(function(e){
            e.preventDefault();
            var payment_mode = $("#payment_mode").val();
            if(payment_mode == ""){
                alert("Please Select Payment Mode");
            }
            else{
                
                var formData = new FormData(this); // Use FormData instead of serialize
                formData.append("Flag", "SavePaymentDetails");
                var form = event.target;
                var fileInput = form.querySelector('input[name="payment_tt_copy"]'); 
                if (fileInput && fileInput.files[0]) {
                    formData.append("payment_tt_copy", fileInput.files[0]);
                }
                $.ajax({
                        url:"operation/payment_receive_operation.php",
                        type: "POST", 
                        data: formData,
                        contentType: false, 
                        processData: false, 
                        success: function (response) {
                            //console.log(response);
                            if(response==1)
                            {
                                Swal.fire({
                                    title: 'Success',
                                    text: "Payment Received Successfully",
                                    icon: 'success',
                                });
                                setTimeout(() => {
                                    window.location.href="payment_receive.php";
                                }, 2000);
                            }
                            else if(response==2)
                            {
                                Swal.fire({
                                    title: 'Success',
                                    text: "Connection Problem!!! Please try again...",
                                    icon: 'success',
                                });
                                setTimeout(() => {
                                    window.location.href="payment_receive.php";
                                }, 2000);
                            }
                            else
                            {
                                Swal.fire({
                                    title: 'OOps',
                                    text: "Unable to generate Purchase Order...",
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