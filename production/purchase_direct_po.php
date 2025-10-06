<?php
	$page_heading="New Purchase Order";
	include '../configuration.php';
	include 'header.php';
    $pi_nos_arr[] ='';
    if(isset($_GET['PO_ID']))
    {
        $PO_IDNew = base64_decode(@$_GET['PO_ID']);
        $rstpi = mysqli_query($connect,"select p.*,pp.*, co.countryName as countryName, s.stateName as stateName  from purchase_order p inner join purchase_order_payment pp on pp.purchase_order_id = p.po_id inner join country_master co on co.id = p.country_id inner join state_master s on s.id = p.state_id where p.po_id='$PO_IDNew'");
        $rwpi = mysqli_fetch_assoc($rstpi);
        extract($rwpi);
        $supplier_pi_date = date('Y-m-d',strtotime($supplier_pi_date));
        $po_date = date('Y-m-d',strtotime($po_date));
        $valid_upto = date('Y-m-d',strtotime($valid_upto));

        $rstprod = mysqli_query($connect,"select pi_no from purchase_order_details where purchase_order_id='$PO_IDNew'");
        $rwprod = mysqli_fetch_assoc($rstprod);
        $pi_nos = $rwprod['pi_no'];
        $pi_nos_arr = explode(",",$pi_nos); 
    }
    else{
        $po_date = date('Y-m-d');
    }
?>
<style>
   .form-control small-input {
      border-radius: 5px;
      font-size: 0.9rem; 
   }
   .form-check-input {
        transform: scale(1.2);
        margin-right: 8px;
    }
    .form-check-label {
        font-size: 0.75rem;
        color: #333;
    }
    .card-body{
        margin-top: -10px;
    }
    
</style>
<script src="../vendors/jquery/dist/jquery.min.js"></script>
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <form method="POST" enctype="multipart/form-data"  autocomplete="off" id="frmpaymentpo">
        <div class="card shadow-sm rounded">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0" style='color:white'>Generate Purchase Order</h6>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <!-- Select Quotation -->
                <div class="col-md-3">
                    <label class="form-label">Supplier Name</label>
                    <input type='hidden' class="form-control small-input" id="hiddenPOID" name="hiddenPOID" value="<?= @$PO_IDNew; ?>">
                    <input type='hidden' class="form-control small-input" id="hiddenPOType" name="hiddenPOType" value="Using ProForma">
                    <input type='hidden' class="form-control small-input" id="hiddenProFormaId" name="hiddenProFormaId" value="<?= @$pi_nos; ?>">
                    <select name="account_id" id="account_id" class='form-control small-input form-select' required onchange="loadClientCountry();loadClientState();">
                        <?php
                        echo "<option value=''>Select Supplier</option>";
                        $sqlquery = "select client_id, client_name from client_master where client_status NOT IN('Raw','Delete','Not Interested') AND branch_id = $branch_id AND LeadType = 'Supplier'" ;
                        if($user_type_id!="1")
                        {
                            $sqlquery .=" AND user_id = $user_id";
                        }
                        $rstsales = mysqli_query($connect,$sqlquery);
                        if(mysqli_num_rows($rstsales)>0) {
                            while($rwsales = mysqli_fetch_assoc($rstsales)) {
                                $client_id = $rwsales['client_id'];
                                $client_name = $rwsales['client_name'];
                                $selected='';
                                if($supplier_id==$client_id)
                                {
                                    $selected='selected';
                                    echo '<script>
                                            $(document).ready(function(){
                                            loadClientState();
                                            loadClientCountry();
                                            });
                                        </script>';
                                }
                                echo "<option value='$client_id' $selected>$client_name</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Supplier PI Number</label>
                    <div data-toggle="tooltip" data-placement="right" title="">
                        <input type="text" name="supplier_pi_no" id="supplier_pi_no"  value="<?= @$supplier_pi_no; ?>" placeholder="Supplier PI Number" class="form-control small-input center " required="">
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Supplier PI Date</label>
                    <div data-toggle="tooltip" data-placement="right" title="" data-original-title="Select PI Date">
                        <input type="date" name="supplier_pi_date" id="supplier_pi_date" value="<?php echo @$supplier_pi_date; ?>" placeholder="Supplier PI Date" class="form-control small-input center " required="">
                    </div>
                </div>
                <div class="col-md-3">
                    <label>PO Date</label>
                    <div data-toggle="tooltip" data-placement="right" title="" data-original-title="Select PO Date">
                        <input type="date" name="po_date" id="po_date" value="<?php echo @$po_date; ?>" placeholder="PO Date" class="form-control small-input center " required="">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <label>Valid Upto</label>
                    <div data-toggle="tooltip" data-placement="right" title="" data-original-title="Select Date">
                        <input type="date" name="valid_upto" id="valid_upto" value="<?php echo @$valid_upto; ?>" placeholder="valid upto" class="form-control small-input center " required="">
                    </div>
                </div>
                <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select Currency">
                    <label>Select Currency</label>
                    <select required="" data-live-search="true" name="currency_id" id="currency_id" class="form-control small-input has_data">
                        <option value=''>Select Currency</option>
                        <?php
                            $rstcur = mysqli_query($connect,"select id,currency_code from country_master where currency_code!=''");
                            while($rwcur = mysqli_fetch_assoc($rstcur))
                            {
                                $id = $rwcur['id'];
                                $currency_code = $rwcur['currency_code'];
                                $selected='';
                                if($currency_id==$id)
                                {
                                    $selected='selected';
                                }
                                echo "<option value='$id' $selected>$currency_code</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select Country">
                    <label>Country</label>
                    <select required="" data-live-search="true" name="country_id" id="country_id" class="form-control small-input selectpicker1 has_data">
                        <option value=''>Select Country</option>
                    </select>
                </div>
                <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select State">
                    <label>State</label>
                    <select required="" data-live-search="true" name="state_id" id="state_id" class="form-control small-input selectpicker1 has_data">
                        <option value=''>Select State</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3" data-toggle="tooltip" >
                    <label>Select Pro-Forma</label>
                    <select id="ProformaId" name="ProformaId[]" class="form-control js-example-basic-multiple required" multiple="multiple" class="form-control small-input has_data" >
                        <option value=''>Select Pro-Forma</option>
                        <?php
                            $rstProForma = mysqli_query($connect,"SELECT DISTINCT p.pi_no, 
                            p.pi_custom_number, c.client_name FROM pro_forma_head p
                            INNER JOIN client_master c ON p.account_id = c.client_id
                            INNER JOIN pro_forma_head_details d ON p.pi_no = d.pi_no
                            WHERE p.pro_forma_status = '1' 
                            AND p.active_status = '1'
                            and p.branch_id = '$branch_id'
                            AND EXISTS (
                                SELECT 1
                                FROM pro_forma_head_details sub
                                WHERE sub.pi_no = p.pi_no
                                AND sub.po_used_qty < sub.no_of_bags
                            ) ");
                            if(mysqli_num_rows($rstProForma)>0)
                            {
                                $piselected = "";
                                while($rwproforma = mysqli_fetch_assoc($rstProForma))
                                {
                                    $pi_no = $rwproforma['pi_no'];
                                    $pi_custom_number = $rwproforma['pi_custom_number'];
                                    $client_name = $rwproforma['client_name'];
                                    if(in_array($pi_no,$pi_nos_arr)){
                                        echo "<option value='$pi_no' selected>$client_name($pi_custom_number)</option>";
                                        echo "<script>
                                            $(document).ready(function(){
                                                setTimeout(()=>{
                                                    disabledselect('$pi_no','$client_name','$pi_custom_number');
                                                }, 3000);
                                            });
                                        </script>";
                                    }else{
                                        echo "<option value='$pi_no'>$client_name($pi_custom_number)</option>";
                                    }
                                }
                            }
                            
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mt-3">
                    <button type="button" class="btn btn-info btn-sm" id="BtnProformaProductsLoad" onclick="LoadProFormaProducts();">Load Products</button>
                </div>
            </div>
            <div class="row mt-3" id="DivProductAdd">
                <div class="col-md-3">
                    <label id="btnproudctdata">Select product </label>
                    <input type="hidden" readonly name="prostatus" id="prostatus" placeholder="Enter UOM" class="form-control small-input" />
                    <input type="hidden" readonly name="uom" id="uom" placeholder="Enter UOM" class="form-control small-input" />
                    <input type="hidden" readonly name="gst" id="gst" placeholder="Enter GST" class="form-control small-input" />
                    <select class="form-control small-input form-select select2cls" id="product_id" name="product_id" onchange="loaduom();loadPackagingType();">
                        <option value="">Select Product</option>
                        <?php
                                $rstcat = mysqli_query($connect,"select * from product_master where status='Active'");
                                while($rwcat = mysqli_fetch_assoc($rstcat))
                                {
                                $product_id = $rwcat['product_id'];
                                $product_name = $rwcat['product_name'];
                                echo "<option value='$product_id'>$product_name</option>";
                                }
                        ?>
                    </select>
                </div> 
                <div class="col-md-2" >
                    <label>Packaging Type</label>
                    <select name="packagingType" id="packagingType" class="form-control small-input" onchange="LoaddPackName();"></select>
                </div>
                <div class="col-md-1" >
                    <label id="PackLabel">Weight/Pack</label>
                    <input name="weightperpack" id="weightperpack" class="form-control small-input" oninput="calculatetotalamount();">
                </div>  
                
                <div class="col-md-1" >
                    <label>Quantity</label>
                    <input type="number" name="quantity" id="quantity" placeholder="Qty" class="form-control small-input" oninput="calculatetotalamount();checkquantity();" />
                    <input type="hidden" name="totquantity" id="totquantity" />
                </div> 
                <div class="col-md-2">
                    <label>Rate/Ton</label>
                    <input type="number"  name="rate" id="rate" placeholder="Rate" class="form-control small-input"  oninput="calculatetotalamount();" />
                </div> 
                
                <div class="col-md-2" >
                    <label>Total Amount</label>
                    <input type="number" readonly name="total_amount" id="total_amount" placeholder="Total Amount" class="form-control small-input"  />
                </div> 
                <div class="col-md-1 mt-3">
                    <label></label>
                    <button type="button" class="btn btn-primary btn-sm" onclick="SaveProductDetails();" ><i class="fa fa-plus"></i></button>
                </div>  
            </div>  
            <div class="row mt-1" >
                <div class="col-md-12"  style="margin-top: 15px;">
                    <table class="table table-bordered" id="tblproductdetails">
                            <thead>
                                <tr>
                                <td>Product Name</td>
                                <td>UOM</td>
                                <td>Rate/Ton</td>
                                <td>Total Weight</td>
                                <td>Rate</td>
                                <td hidden>GST Amount</td>
                                <td>Qty.</td>
                                <td>Total Amt.</td>
                                
                                <td>Action</td>
                                </tr>
                            </thead>
                            <tbody id="divpurchaseproducts">
                                <?php
                                    if(isset($_GET['PO_ID'])!="")
                                    {
                                        $rstpro = mysqli_query($connect,"select p.*,pp.uom_id, pp.product_name,pp.status,u.u_name from purchase_order_details p 
                                        inner join product_master pp on pp.product_id= p.product_id
                                        inner join uom_master u on u.u_id=pp.uom_id
                                        where p.purchase_order_id='$PO_IDNew'");
                                        if(mysqli_num_rows($rstpro)>0)
                                        {
                                            while($rwpro = mysqli_fetch_assoc($rstpro))
                                            {
                                                extract($rwpro);

                                                echo '
                                                <script>
                                                $(document).ready(function(){
                                                        $("#product_id option[value='.$product_id.']").remove();
                                                });</script>';
                                                echo "<tr>
                                                        <td value='$product_id'>$product_name</td>
                                                        <td>$u_name</td>
                                                        <td>$rateperton</td>
                                                        <td>$total_weight</td>
                                                        <td>$rate</td>
                                                        <td style='display:none;'>0</td>
                                                        <td>$no_of_bags</td>
                                                        <td class='tdtotalamount'>$total_amt</td>
                                                        <td style='display:none;'>$status</td>
                                                        <td style='display:none;'>$each_bag_weight</td>
                                                        <td>
                                                        <button type='button' class='btn btn-info btn-sm btneditproduct' 
                                                            onclick='EditProductDetails(
                                                                " . json_encode($product_id) . ", 
                                                                " . json_encode($product_name) . ", 
                                                                " . json_decode($packaging_id) . ",
                                                                " . json_encode($each_bag_weight) . ", 
                                                                " . json_encode($no_of_bags) . ", 
                                                                " . json_encode($rateperton) . ", 
                                                                " . json_encode($total_amt) . "
                                                            )'>
                                                            <i class='fa fa-pencil'></i>
                                                        </button>
                                                        <button type='button' class='btn btn-danger btn-sm btnremoveproduct' id='removetrclick$product_id'><i class='fa fa-close'></i></button></td>
                                                </tr>";
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                    </table>
                </div> 
                <div class="col-md-3" >
                    <label>Total Amount</label>
                    <input type="number" readonly name="totalAmtAll" id="totalAmtAll" placeholder="Total Amount" class="form-control small-input"  value="<?= @$total_amt; ?>"/>
                </div> 
                <div class="col-md-3" >
                    <label>Enter Discount (use %)</label>
                    <input type="text" name="DiscountAmt" id="DiscountAmt" placeholder="Total Amount" class="form-control small-input"  oninput="calculateDiscount();" value="<?= @$discount; ?>"/>
                </div> 
                <div class="col-md-3" >
                    <label>Grand Total</label>
                    <input type="number" readonly name="grand_total" id="grand_total" placeholder="Total Amount" class="form-control small-input"  value="<?= @$grand_total; ?>"    />
                </div> 
            </div>
            <table class="table table-bordered table-striped jambo_table bulk_action
            mt-3" id="payment_details">
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
                            <!-- <option value="Before">Before</option> -->
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
                            <!-- <option value="After">After</option> -->
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
                </tbody>
                <tbody id="add_payment_list" style="background-color: #f5f5f5;">
                </tbody>
            </table>
            <div class="row">
                    <!-- Terms & Conditions Section -->
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Terms & Conditions</h6>
                            </div>
                            <div class="card-body">
                                <label class="form-check-label fw-bold" style="margin-left: 8px;">
                                    <input type="checkbox" name="checktermall" id="checktermall" class="form-check-input" value="all"> Select All
                                </label>
                                <div id="termdiv">
                                    <?php 
                                    $checkedTerms = [];
                                    $checkedDescriptions = [];
                                    if(isset($_GET['PO_ID'])!="")
                                    {
                                        $checkQuery = "SELECT term_id, discription FROM purchase_order_termscondition_detail WHERE purchase_order_id = '$PO_IDNew'";
                                        $checkResult = $connect->query($checkQuery);
                                        while ($row = $checkResult->fetch_assoc()) {
                                            $checkedTerms[] = $row['term_id'];
                                            $checkedDescriptions[$row['term_id']] = addslashes($row['discription']);
                                        }
                                    }
                                    $cmd = "SELECT * FROM terms_conditions WHERE comp_id = '$comp_id' AND status = 1";
                                    $result = $connect->query($cmd);
                                    if ($result->num_rows > 0) {
                                        $i = 1;
                                        $script = "";
                                        $strcnt = 0;
                                        while ($row = $result->fetch_assoc()) { 
                                            $isChecked = in_array($row['terms_id'], $checkedTerms) ? "checked" : "";
                                            $termId = $row['terms_id'];
                                            if ($isChecked == "checked" && isset($checkedDescriptions[$termId])) {
                                                $discriptionData = $checkedDescriptions[$termId];
                                                $script .= "$('#termdiv_$termId').css('display','inline'); $('#termdiv_$termId').html('$discriptionData');\n";
                                            }
                    
                                            if(strpos($row['discription'], "@@@") !== false){
                                                $strarr = explode(" ", $row['discription']);
                                                $resarr = [];
                                                foreach ($strarr as $value) {
                                                    if($value == "@@@"){
                                                        $replacestr = substr_replace($value,"<input type='text' class='specialcharint' id='specialcharinput_".$strcnt."' style='display:none'><span class='specialinput' id='specialchar_".$strcnt."'>_ _ _ _</span>",0);
                                                        array_push($resarr,$replacestr);
                                                        $strcnt++;
                                                    }else{
                                                        array_push($resarr,$value);
                                                    }
                                                }
                                                $result_discription = implode(" ",$resarr);
                                                echo '<div class="form-check p-1 px-2 rounded">
                                                        <input type="checkbox" class="form-check-input termcheckboxes" id="termchk_'.$row['terms_id'].'" value="'.$row['title'].'" '.$isChecked.'>
                                                        <label class="form-check-label fw-semibold" for="termchk_' . $row['terms_id'] . '">' . $i . '. ' . $row['title'] . '</label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="termdiv_'.$row['terms_id'].'" style="display:none" class="mt-1 text-muted">'.$result_discription.'</label> 
                                                    </div>';
                                            }else{
                                            
                                                echo '<div class="form-check p-1 px-2 rounded">
                                                        <input type="checkbox" class="form-check-input termcheckboxes" id="termchk_'.$row['terms_id'].'" value="'.$row['title'].'" '.$isChecked.'>
                                                        <label class="form-check-label fw-semibold" for="termchk_' . $row['terms_id'] . '">' . $i . '. ' . $row['title'] . '</label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="termdiv_'.$row['terms_id'].'" style="display:none" class="mt-1 text-muted">'.$row['discription'].'</label> 
                                                    </div>';
                                            }
                                            if (!empty($script)) {
                                                echo "<script>
                                                    $(document).ready(function() {
                                                        $script
                                                    });
                                                </script>";
                                            }
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Shipment Documents</h6>
                            </div>
                            <div class="card-body">
                                <label class="form-check-label fw-bold" style="margin-left: 8px;">
                                    <input type="checkbox" name="checkshipall" id="checkshipall" class="form-check-input"> Select All
                                </label>
                                <div id="shipmentdiv">
                                    <?php
                                    $checkedTerms = [];
                                    if(isset($_GET['PO_ID'])!="")
                                    {
                                        $checkQuery = "SELECT shipment_document_id FROM purchase_order_shipment_detail WHERE po_no = '$PO_IDNew'";
                                        $checkResult = $connect->query($checkQuery);
                                        while ($rowdd = $checkResult->fetch_assoc()) {
                                            $checkedTerms[] = $rowdd['shipment_document_id'];
                                        }
                                    }

                                    $cmd = "SELECT * FROM shipment_document WHERE status = 'Active'";
                                    $result = $connect->query($cmd);
                                    if ($result->num_rows > 0) {
                                        $i = 1;
                                        while ($row = $result->fetch_assoc()) { 
                                            $isChecked = in_array($row['shipment_document_id'], $checkedTerms) ? "checked" : "";
                                            echo '<div class="form-check p-1 px-2 rounded ">
                                                    <input type="checkbox" class="form-check-input shipcheckboxes" id="shipchk_' . $row['shipment_document_id'] . '" value="' . $row['shipment_document_name'] . '" data-attr="' . $row['shipment_document_id'] . '" ' . $isChecked . '>
                                                    <label class="form-check-label fw-semibold" for="shipchk_' . $row['shipment_document_id'] . '">' . $i . '. ' . $row['shipment_document_name'] . '</label>
                                                </div>';
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <textarea name="remark" id="remark" rows="3" class="form-control small-input mt-3" placeholder="Enter Remark" spellcheck="true"><?= @$remark; ?></textarea>
                    </div>
            </div>

            <div class="col-md-4 mt-4">
                <input type="submit" class="btn btn-success" id="btnSave" value="Save">
                <a href="purchase_orders.php"><button type="button" class="btn btn-warning">Reset</button></a>
                <a href="index.php"><button type="button" class="btn btn-secondary">Close</button></a>
            </div>
        </div>
    </div>


	</form>
</div>
<?php include_once('footer.php'); ?>

<script>

     function disabledselect(a,b,c){
        var name = b+"("+c+")";
        $("li.select2-selection__choice[title='"+name+"']").find("button").attr("disabled","disabled");
     }


    $(document).ready(function()
    {
        $("#DivProductAdd").hide();
        $('.js-example-basic-multiple').select2({
            placeholder:"Select Pro-Forma"
        });
	    $(document).on('click', '.btnremoveproduct', function() {
            var row = $(this).closest('tr');
            var productId = row.find('td').eq(0).attr('value');
            var productName = row.find('td').eq(0).text();
            // var productstatus = row.find('td').eq(7).text();
            row.remove();
            var totalamt = 0;
            $(".tdtotalamount").each(function() {
                totalamt += parseFloat($(this).text());
            });
            $("#totalAmtAll").val(totalamt);
            if ($("#product_id option[value='" + productId + "']").length === 0) {
                $("#product_id").append($('<option>', {
                    value: productId,
                    text: productName
                }));
            }
            calculateDiscount();
            percentage();
        });

        $("#checktermall").click(function(){
            //console.log("clicked");
            var checked = $(this).prop('checked');
            $('#termdiv').find('input:checkbox').prop('checked', checked);
            $(".termcheckboxes:checkbox").each(function () {
                var input = $(this).attr('id').split('_');
                var id = input[1];
                if(this.checked){
                    $("#termdiv_"+id).show();
                }else{
                    $("#termdiv_"+id).hide();
                }
            });
        });
        $("#checkshipall").click(function(){
            //console.log("clicked");
            var checked = $(this).prop('checked');
            $('#shipmentdiv').find('input:checkbox').prop('checked', checked);
        });
       $(".termcheckboxes:checkbox").change(function(){
            var input = $(this).attr('id').split('_');
            var id = input[1];
            if(this.checked){
                $("#termdiv_"+id).show();
            }else{
                $("#termdiv_"+id).hide();
            }
        }); 
        
        $(".specialinput").on('click',function(){
            //alert(this.id);
            var input = $(this).attr('id').split('_');
            var id = input[1];
            $("#specialcharinput_"+id).show();
            $("#specialchar_"+id).hide();
        });

        $('.specialcharint').on('blur', function() {
            var input = $(this).attr('id').split('_');
            var id = input[1];
            $('#specialchar_'+id).text($(this).val()).show();
            $(this).hide();
        });

        $("#frmpaymentpo").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this); // Use FormData instead of serialize
            formData.append("Flag", "SavePurchaseOrder");
            formData.append("packagingTypeName", $("#packagingType option:selected").text());
            formData.append("packaging_id", $("#packagingType option:selected").val());

            var termData = [];
            $(".termcheckboxes:checkbox:checked").each(function() {
                var termarr = [];
                var input = $(this).attr('id').split('_');
                var id = input[1];
                if(this.checked){
                    termarr.push(id); //term id
                    termarr.push($(this).val()); // title
                    termarr.push($("#termdiv_"+id).text()); //discription
                }

                termData.push(termarr);
            });
            var shipData = [];
            $(".shipcheckboxes:checkbox:checked").each(function() {
                var ShipArr = [];
                ShipArr.push($(this).val());
                ShipArr.push($(this).data('attr'));
                shipData.push(ShipArr);
            });
            var tableData = [];
            var invalidRate = false;
            $("#tblproductdetails tbody tr").each(function() {
                var row = $(this);
                var rowData = {
                    product_id: row.find("td:eq(0)").attr("value"), 
                    uom: row.find("td:eq(1)").text(),
                    rateperton: row.find("td:eq(2)").text(),
                    totalweight: row.find("td:eq(3)").text(),
                    rate: row.find("td:eq(4)").text(),
                    gst_amount: row.find("td:eq(5)").text(),
                    quantity: row.find("td:eq(6)").text(),
                    totalamt: row.find("td:eq(7)").text(),
                    weightperpack: row.find("td:eq(9)").text(),
                };
                var totalWeight = parseFloat(row.find("td:eq(3)").text());
                var rate = parseFloat(row.find("td:eq(4)").text());
                if (totalWeight === 0 || rate === 0) {
                    invalidRate = true;
                    return false; // Exit the loop immediately
                }
                tableData.push(rowData);
            });
            if (invalidRate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Please update valid product rates.",
                })
               return;
            }
            else if(shipData.length=="0" || shipData=="[]")
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Please Select At least One Shipment Document",
                });
            }
            else if(tableData.length=="0" || tableData=="[]")
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Please Add At least One Product",
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        $("#product_id").focus();
                    }
                });
            }
            else
            {
                showSpinner();
                formData.append("tableData", JSON.stringify(tableData));
                formData.append("termData", JSON.stringify(termData));
                formData.append("shipData", JSON.stringify(shipData));
                $.ajax({
                    url:"operation/purchase_order_operation.php",
                    type: "POST", 
                    data: formData,
                    contentType: false, 
                    processData: false, 
                    success: function (response) {
                        hideSpinner();
                        console.log(response);
                        if(response==1)
                        {
                            Swal.fire({
                                title: 'Success',
                                text: "Purchase Order Generated Successfully",
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="purchase_orders.php";
                            }, 2000);
                        }
                        else if(response==2)
                        {
                            Swal.fire({
                                title: 'Success',
                                text: "Purchase Order Updated Successfully",
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="purchase_orders.php";
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
    
    function loaduom()
    {
        if($("#product_id").val()==""){
            $("#btnproudctdata").html("Select Products");
            $("#uom").val('');
            $("#rate").val('');
            $("#gst").val('');
            $("#prostatus").val('');
        }
        else
        {
            $.post("operation/pro_forma_operation.php",{
                Flag:"showuom",
                product_id:$("#product_id").val()
            },function(data,success)
            {
                var res = JSON.parse(data);
                $("#uom").val(res.uom);
                $("#gst").val(res.gst_precent);
                $("#prostatus").val(res.product_status);
                if($("#product_id").val()!=""){
                    $("#btnproudctdata").html("Select Products (UOM : "+res.uom+")");
                }
            });
        }
    }

    function loadPackagingType()
    {
        if($("#product_id").val()==""){
            $("#packagingType").html('');
        }
        else
        {
            $.post("operation/pro_forma_operation.php",{
                Flag:"loadPackagingType",
                product_id:$("#product_id").val()
            },function(data,success)
            {
                $("#packagingType").html(data);
            });
        }
    }
	
    function loadClientCountry()
    {
        $.post("../component.php",{
            Flag:"loadClientCountry",
            account_id : $("#account_id option:selected").val()
        },function(data,success){
            $("#country_id").html(data);
        });
    }
    
    function loadClientState()
    {
        $.post("../component.php",{
            Flag:"loadClientState",
            account_id : $("#account_id option:selected").val()
        },function(data,success){
            $("#state_id").html(data);
        });
    }

    function LoaddPackName()
    {
        var packagingType = $("#packagingType option:selected").text();
        if(packagingType!="Select Option")
        {   
            $("#PackLabel").html("Weight/"+packagingType);
        }
        else{
            $("#PackLabel").html("Weight/Pack");
        }
    }

    function calculatetotalamount()
    {
        var weightperpack = $("#weightperpack").val();
        var rate = $("#rate").val();
        var quantity = $("#quantity").val();
        var TotalWeight = weightperpack*quantity;
        var RatePerKg = rate/1000;
        var totalamount = TotalWeight * RatePerKg;
        if(quantity < 0){
            alert("Please Enter Valid Quantity");
            $("#quantity").val('');
            $("#quantity").focus();
        }
        else if(rate < 0)
        {
            alert("Please Enter Valid Rate");
            $("#rate").val('');
            $("#rate").focus();
        }
        else
        {
            $("#total_amount").val(totalamount.toFixed(2));
        }
    }

    function checkquantity(){
        var quantity = $("#quantity").val();
        var totquantity = $("#totquantity").val();
        if(quantity > totquantity){
            alert("The requested quantity is not available for this product...Please enter valid quantity ( Available Quantity : "+totquantity+" ) ");
            $("#quantity").val('');
            $("#quantity").focus();
        }else
        {
            $("#quantity").val(quantity);
        }
    }

    function SaveProductDetails()
    {
        var product_id = $("#product_id option:selected").val();
        var packagingType = $("#packagingType option:selected").val();
        var product_name = $("#product_id option:selected").text();
        var quantity = $("#quantity").val();
        if(product_id=="")
        {
            alert("Please Select Product");
            $("#product_id").focus();
        }
        else if(quantity=="")
        {
            alert("Please Add Quantity");
            $("#quantity").focus();
        }
        else if($("#total_amount").val()<=0)
        {
            alert("Please Add Valid Product Amount");
            $("#total_amount").focus();
        }
        else
        {
            $.post("operation/purchase_order_operation.php",{
            Flag:"SaveProductDetails",
            product_id:product_id,
            packagingType:packagingType,
            product_name:product_name,
            quantity:quantity,
            uom:$("#uom").val(),
            rateperton:$("#rate").val(),
            gst:$("#gst").val(),
            prostatus:$("#prostatus").val(),
            total_amount:$("#total_amount").val(),
            weightperpack : $("#weightperpack").val()
            },function(data,success)
            {
                $("#divpurchaseproducts").append(data);
                var totalamt = 0;
                $(".tdtotalamount").each(function() {
                    totalamt += parseFloat($(this).text());
                });
                $("#totalAmtAll").val(totalamt);
                $("#DiscountAmt").val(0);
                $("#grand_total").val(totalamt);

                $("#uom").val('');
                $("#gst").val('');
                $("#prostatus").val('');
                $("#rate").val('');
                $("#product_id").focus();
                $("#product_id").val('');
                $("#total_amount").val('');
                $("#weightperpack").val('');
                $("#quantity").val('');
                $("#totquantity").val('');
                $("#btnproudctdata").html("Select Product");
                $("#product_id").prop('disabled',false);
                $("#product_id option[value='" + product_id + "']").remove();
            });
        }
    }

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
    function LoadProFormaProducts()
    {
        var pi_no = $("#ProformaId").val();
        
        $.post("operation/purchase_order_operation.php",{
        Flag:"LoadProFormaProducts",
        pi_no:pi_no,
        },function(data,success)
        {
            $("#ProformaId").prop('readonly',true);
            $("#BtnProformaProductsLoad").prop('disabled',true);
            $("#divpurchaseproducts").html('');
            $("#divpurchaseproducts").append(data);
            var totalamt = 0;
            $(".tdtotalamount").each(function() {
                totalamt += parseFloat($(this).text());
            });
            $("#totalAmtAll").val(totalamt);
            $("#DiscountAmt").val(0);
            $("#grand_total").val(totalamt);
            $("#btnproudctdata").html("Select Product");
        });
    }
    function EditProductDetails(ProductId,productname, packaging_id, Weightperpack,Quantity,ratePerTon, TotalAmt)
    {
        $("#DivProductAdd").show();
        if ($("#product_id option[value='" + ProductId + "']").length === 0) {
            $("#product_id").append("<option value='" + ProductId + "'>" + productname + "</option>");
        }
        $("#product_id").prop('disabled',true);
        $("#product_id").val(ProductId).change();
        $("#weightperpack").val(Weightperpack);
        $("#quantity").val(Quantity);
        $("#totquantity").val(Quantity);
        $("#rate").val(ratePerTon);
        $("#total_amount").val(TotalAmt.toFixed(2));
        setTimeout(() => {
            $("#packagingType").val(packaging_id).change();
        }, 500);
        $("#removetrclick"+ProductId).click();
    }

</script>