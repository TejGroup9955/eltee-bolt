<?php include_once('header.php'); ?>
<?php
    $remark = $id = $pi_no = $result_discription = $strcnt = "";$paid_amount1=0;$total_tax_amount =0;
    if(isset($_GET['id']))
    {
        $id = base64_decode($_GET['id']);

        $query = "SELECT * FROM `pro_forma_receipt_payment` WHERE `customer_receipt_id`='$id'";
        $rstclient = mysqli_query($connect,$query);
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            $pi_no = $rwclient['pi_no'];
            $grand_total = $rwclient['total_amount'];
            $paid_amount1 = $rwclient['paid_amount'];
            $remain_amount = $rwclient['remain_amount'];
        }

        $query1 = "SELECT sum(`tax_amount`) as tax_amount FROM `pro_forma_tax_payment` WHERE `customer_receipt_id`='$id'";
        $rstclient1 = mysqli_query($connect,$query1);
        while($rwclient1 = mysqli_fetch_assoc($rstclient1))
        {
            $total_tax_amount = $rwclient1['tax_amount'];
        }

        $query ="SELECT pi_custom_number, supplier_lic_no, account_id, supplier_lic_date, pi_invoice_date, pi_valid_date,  grand_total FROM pro_forma_head  WHERE pi_no='$pi_no'";
         $result = $connect->query($query);
         if ($result->num_rows > 0) {
          if($row = $result->fetch_assoc()) {
            $pi_custom_number = $row['pi_custom_number'];
            $account_id = $row['account_id'];
          }
        }
          
        
    }

?>
<style type="text/css">
    
    .mt-4{
        margin-top: 1.2rem !important;
    }
</style>
<script src="../vendors/jquery/dist/jquery.min.js"></script>

<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"  autocomplete="off" id="frmpaymentpo">
                <input type="hidden" name="customer_receipt_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="pi_no" value="<?php echo $pi_no; ?>" />
                <div class="row">
                    <div class="col-md-3" >
                        <label>PI Number</label>
                          <input type="text" readonly name="pi_number" id="pi_number" placeholder="Enter PI number" onkeypress="return isNumber(event)" value="<?php echo $pi_custom_number;?>" class="btn btn-danger btn_full " />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>Grand Total</label>
                        <input type="number" style="text-align: center;" readonly name="grand_total" id="grand_total" placeholder="Total Amount" class="form-control form_center"  value="<?= @$grand_total; ?>"    />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>Paid Amount</label>
                        <input type="number" style="text-align: center;" readonly name="paid_amount" id="paid_amount" placeholder="Total Amount" class="form-control form_center"  value="<?= @$paid_amount1; ?>"    />
                    </div>
                    <div class="col-md-3" style="text-align: center;">
                        <label>Remain Amount</label>
                        <input type="text" style="text-align: center;" name="remain_amount" class="form-control form_center" readonly="" id="remain_amount" value="<?php echo $remain_amount; ?>" />
                        <input type="hidden"  id="remain_amount_temp" value="<?php echo $remain_amount; ?>" />
                    </div> 
                </div>
                <br>
                <div class="row" style="border:1px solid black;margin: 1px;border: 1px solid #8a8d93; padding: 5px;">
                    <?php if($total_tax_amount != $paid_amount1){ ?>
                    <div class="col-md-3">
                        <label id="btnproudctdata">Tax Payment</label>
                        <select class="form-control small-input form-select select2cls" id="tax_id" name="tax_id">
                            <option value="">Select</option>
                            <?php
                                    $rstcat = mysqli_query($connect,"select * from payment_tax");
                                    while($rwcat = mysqli_fetch_assoc($rstcat))
                                    {
                                    $tax_id = $rwcat['tax_id'];
                                    $tax_name = $rwcat['tax_name'];
                                    echo "<option value='$tax_id'>$tax_name</option>";
                                    }
                            ?>
                        </select>
                    </div> 
                    <div class="col-md-1" >
                        <label id="taxamountlbl">Amount</label>
                        <input type="number" name="taxamount" id="taxamount" class="form-control" oninput="validity.valid||(value='');cal_remain(this.value)">
                    </div>  
                    <input type="hidden" name="totalAmtAll" id="totalAmtAll" placeholder="Total Amount" class="form-control small-input"/>
                    <div class="col-md-1 mt-4" >
                        <label></label>
                        <button type="button" class="btn btn-primary btn-sm" onclick="SavepaymentDetails();" ><i class="fa fa-plus"></i></button>
                    </div>  
                    <?php } ?>
                    <div class="col-md-12"  style="margin-top: 15px;">
                    <table class="table table-bordered" id="tbltaxdetails">
                            <thead>
                                <tr>
                                    <td>Tax Name</td>
                                    <td>Amount</td>
                                    <?php if($total_tax_amount != $paid_amount1){ ?>
                                    <td>Action</td>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="divtaxdetails">
                                <?php
                                    if(isset($_GET['id'])!="")
                                    {
                                        $rstpro = mysqli_query($connect,"SELECT * FROM `pro_forma_tax_payment` WHERE `customer_receipt_id`='$id'");
                                        if(mysqli_num_rows($rstpro)>0)
                                        {   
                                            $total_amount = 0;
                                            while($rwpro = mysqli_fetch_assoc($rstpro))
                                            {
                                                extract($rwpro);
                                                
                                                $total_amount = $tax_amount+$total_amount;
                                                echo '
                                                <script>
                                                $(document).ready(function(){
                                                        $("#tax_id option[value='.$tax_id.']").remove();
                                                        $("#totalAmtAll").val('.$total_amount.');
                                                });</script>';
                                                echo "<tr>
                                                        <td value='$tax_id'>$tax_name</td>
                                                        <td>$tax_amount</td>
                                                        <td style='display:none;' class='tdtotalamount'>$total_amount</td>";
                                                if($total_tax_amount != $paid_amount1){
                                                echo "<td><button class='btn btn-danger btn-sm btnremovetax'><i class='fa fa-close'></i></button></td>";
                                                }
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                    </table>
                </div> 
                </div>
                <div class="row">
                    <div class="col-md-4 mt-4">
                         <?php if($total_tax_amount != $paid_amount1){ ?>
                        <input type="submit" class="btn btn-success" id="btnSave" value="Save">
                        <a href="customer_payment.php"><button type="button" class="btn btn-warning">Reset</button></a>
                        <?php } ?>
                        <a href="customer_payment.php"><button type="button" class="btn btn-secondary">Close</button></a>
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
<script type="text/javascript">
    
    function cal_remain(amount)
    {
        var totalAmtAll = document.getElementById("totalAmtAll").value;
        var paid_amt = document.getElementById("paid_amount").value;  
        var total = 0;
        
        if(totalAmtAll=="")
        {
            totalAmtAll = 0;
        }

        total = parseFloat(totalAmtAll)+parseFloat(amount);
        //alert(total+" == "+paid_amt);
        if(parseFloat(total) > paid_amt)  
        {
          alert("Paid Amount should greater than Tax Amount...")
          document.getElementById("taxamount").value = '';
          $("#taxamount").focus();
        }
        
    }

    function SavepaymentDetails(){

        var tax_id = $("#tax_id option:selected").val();
        var tax_name = $("#tax_id option:selected").text();
        var amount = $("#taxamount").val();
        var totalAmtAll = $("#totalAmtAll").val();
        var paid_amt = document.getElementById("paid_amount").value;  

        if(totalAmtAll=="")
        {
            totalAmtAll = 0;
        }
        var total = 0;
        total = parseFloat(totalAmtAll)+parseFloat(amount);
        //alert(total+" == "+paid_amt);
        if(parseFloat(total) > paid_amt)  
        {
          alert("Paid Amount should greater than Tax Amount...");
          document.getElementById("taxamount").value = '';
          $("#taxamount").focus();
        }

        if(tax_id=="")
        {
            alert("Please Select Tax");
            $("#tax_id").focus();
        }
        else if(amount=="")
        {
            alert("Please Add Amount");
            $("#taxamount").focus();
        }
        else
        {
            $.post("operation/payment_operation.php",{
            Flag:"SavePaymentDetails",
            tax_id:tax_id,
            tax_name:tax_name,
            amount:amount,
            total_amount:totalAmtAll,
            },function(data,success)
            {
                $("#divtaxdetails").append(data);
                var totalamt = 0;
                $(".tdamount").each(function() {
                    totalamt += parseFloat($(this).text());
                });
                $("#totalAmtAll").val(totalamt);
                $("#taxamount").val('');
                $("#tax_id option[value='" + tax_id + "']").remove();
            })
        }
    }

    $(document).ready(function(){

        $(document).on('click', '.btnremovetax', function() {
            var row = $(this).closest('tr');
            var tax_id = row.find('td').eq(0).attr('value');
            var tax_name = row.find('td').eq(0).text();
            row.remove();
            var totalamt = 0;
            $(".tdamount").each(function() {
                totalamt += parseFloat($(this).text());
            });
            $("#totalAmtAll").val(totalamt);
            $("#tax_id").append($('<option>', {
                value: tax_id,
                text: tax_name
            }));
        });

        $("#frmpaymentpo").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this); // Use FormData instead of serialize
            formData.append("Flag", "SaveTaxPayment");
            var paid_amt = document.getElementById("paid_amount").value; 
            var total = 0; 
            var tableData = [];
            $("#tbltaxdetails tbody tr").each(function() {
                var row = $(this);
                var rowData = {
                    tax_id: row.find("td:eq(0)").attr("value"), 
                    tax_name: row.find("td:eq(0)").text(),
                    tax_amount: row.find("td:eq(1)").text(),
                };
                total = total + parseInt(row.find("td:eq(1)").text());
                tableData.push(rowData);
            });
            if(tableData.length=="0" || tableData=="[]")
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Please Add At least One Tax Payable Amount",
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        $("#tax_id").focus();
                    }
                });
            }
            else if(total != paid_amt || total > paid_amt || total < paid_amt){
                Swal.fire({
                    icon: 'info',
                    title: 'Payment',
                    text: "Please Add Payment in appropriate manner",
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        $("#tax_id").focus();
                    }
                });
            }
            else
            {
                formData.append("tableData", JSON.stringify(tableData));
                $.ajax({
                    url:"operation/payment_operation.php",
                    type: "POST", 
                    data: formData,
                    contentType: false, 
                    processData: false, 
                    success: function (response) {
                        console.log(response);
                        if(response==1)
                        {
                            Swal.fire({
                                title: 'Success',
                                text: "Tax Amount Added successfully",
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="customer_payment.php";
                            }, 2000);
                        }
                        else if(response==2)
                        {
                            Swal.fire({
                                title: 'Success',
                                text: "Tax Amount Updated successfully",
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="customer_payment.php";
                            }, 2000);
                        }
                        else
                        {
                            Swal.fire({
                                title: 'OOps',
                                text: "Unable to save data...",
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

        $("#taxamount").on("input", function() {
            this.value = this.value.replace(/^[0]*/, "");
        });
    });
</script>