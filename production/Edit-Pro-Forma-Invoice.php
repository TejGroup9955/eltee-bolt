<?php
	$page_heading="New Purchase Order";
	include '../configuration.php';
	include 'header.php';
    $remark = $PI_NoNew = $result_discription = $strcnt = "";
    if(isset($_GET['PI_No']))
    {
        $PI_NoNew = base64_decode(@$_GET['PI_No']);
        $EditMode = base64_decode(@$_GET['EditMode']);
        $rstpi = mysqli_query($connect,"select p.*,pp.*,b.bank_name,bb.account_no,co.countryName as 
        CountryOfOrigin,cs.countryName as CountryOfShipment,pm.port_name,cc.countryName,
        inn.incoterms_fullform,inn.incoterms
        from pro_forma_head p
        inner join pro_forma_payment pp on pp.pi_no=p.pi_no
        inner join incoterms_master inn on inn.incoterms_id= p.incoterms_id
        inner join bank_details bb on bb.id=p.bank_detail_id
        inner join bank_master b on b.bank_id=bb.bank_id
        inner join country_master co on co.id=p.country_of_origin
        inner join country_master cs on cs.id=p.country_of_supply
        left join port_master pm on pm.port_master_id = p.port_id
        left join country_master cc on cc.id=pm.country_id
        where p.pi_no='$PI_NoNew'");
        $rwpi = mysqli_fetch_assoc($rstpi);
        extract($rwpi);
         echo "
         <script src='../vendors/jquery/dist/jquery.min.js'></script>
         <script>
            $(document).ready(function(){
                    $('input, select, textarea, button').prop('disabled', true);
                    $('#btnSave').prop('disabled', false); // Save button
                    $('#btnClose').prop('disabled', false); // Close button
                    $('.divproduct').prop('disabled', false);
            });
        </script>";
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
        <?php echo @$alert; ?>
        <form method="POST" enctype="multipart/form-data"  autocomplete="off" id="frmpaymentpo">
            <div class="card shadow-sm rounded">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0" style='color:white'>Generate Pro-Forma Invoice</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Select Quotation -->
                        <div class="col-md-2">
                            <label class="form-label">Customer Name</label>
                            <input type='hidden' class="form-control small-input" id="hiddenPiNo" name="hiddenPiNo" value="<?= $PI_NoNew; ?>">
                            <select name="account_id" id="account_id" class='form-control small-input form-select' required onchange="LoadClientDetails();loadClientCountry();">
                                <?php
                                echo "<option value=''>Select Client</option>";
                                $rstsales = mysqli_query($connect,"select client_id, client_name from client_master where client_status NOT IN('Raw','Delete','Not Interested') AND LeadType = 'Lead' AND user_id = $user_id AND branch_id = $branch_id" );
                                if(mysqli_num_rows($rstsales)>0) {
                                    while($rwsales = mysqli_fetch_assoc($rstsales)) {
                                        $client_id = $rwsales['client_id'];
                                        $client_name = $rwsales['client_name'];
                                        $selected='';
                                        if($account_id==$client_id)
                                        {
                                            $selected='selected';
                                            echo '<script>
                                                    $(document).ready(function(){
                                                    LoadClientDetails();
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
                        <div class="col-md-2">
                            <label>PI Date</label>
                            <div data-toggle="tooltip" data-placement="right" title="" data-original-title="Select PI Date">
                                <input type="date" readonly="" name="pi_invoice_date" id="pi_invoice_date" value="<?php echo date('Y-m-d'); ?>" placeholder="PI Date" class="form-control small-input center " required="">
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <label>PI Valid Upto</label>
                            <div data-toggle="tooltip" data-placement="right" title="" data-original-title="Select valid upto date">
                                <input type="text" name="pi_valid_date" id="pi_valid_date" placeholder="Valid upto date" value="<?php echo date('d-m-Y'); ?>" class="form-control small-input center has_data">
                            </div>
                        </div> -->
                        <div class="col-md-2" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select State">
                            <label>Country</label>
                            <select required="" data-live-search="true" name="country_id" id="country_id" class="form-control small-input selectpicker1 has_data">
                                <option value=''>Select Country</option>
                            </select>
                        </div>
                        <div class="col-md-2" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select State">
                            <label>Select Currency</label>
                            <select required="" data-live-search="true" name="currency_id" id="currency_id" onchange="LoadCurrentBankDetails();" class="form-control small-input has_data">
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
                        <div class="col-md-4">
                            <label>Select Bank Details</label>
                            <select required="" data-live-search="true" name="bank_account_no" id="bank_account_no" class="form-control small-input form-select has_data">
                                    <?php
                                        if($PI_NoNew!="")
                                        {
                                            echo "<option value='$bank_detail_id'>$bank_name/$account_no</option>";
                                        }
                                    ?>
                            </select>
                        </div>
                        <input type="hidden" name="company" id="company" value="<?= $comp_id; ?>">
                        <!-- <div class="col-md-4  p-2 rounded" style="margin-right: 0px;margin-left: 0px;">
                            <label class="mb-1 fw-bold">Kind Attention: <span id='txtkindAttention' class="text-primary"></span> / <span id='txtmobileNo' class="text-primary"></span></label>
                            <br>
                            <label class="mb-1 fw-bold">Address: <span id='txtAddress' class="text-primary"></span></label>
                        </div> -->
                    </div>
                    <div class="row mb-2 mt-2">
                        <div class="col-md-2">
                            <label for="time_of_shipment" class="form-label">Time of Shipment</label>
                            <input type="text" class='form-control small-input' id="time_of_shipment" name="time_of_shipment" placeholder="Time Of Shipment" value="<?= @$time_of_shipment; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for = 'country_of_origin' class = 'form-label'> Country of Origin </label>
                            <select id='country_of_origin' name='country_of_origin' class='form-control small-select ' required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for = 'country_of_supply' class = 'form-label'> Country of Supply </label>
                            <select id='country_of_supply' name='country_of_supply' class='form-control small-input ' required>
                                
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for = 'port_of_loading' class = 'form-label'> Port of Loading </label>
                            <select id='port_of_loading' name='port_of_loading' class='form-control small-input ' required>
                                <?php
                                    if(isset($PI_NoNew))
                                    {
                                        echo "<option value='$port_of_loading'>$port_of_loading_name</option>";
                                        $rstdes = mysqli_query($connect,"select * from port_master where status='Active' and port_master_id!='$port_of_loading' ");
                                        while($rwdes = mysqli_fetch_assoc($rstdes))
                                        {
                                            $port_master_id = $rwdes['port_master_id'];
                                            $port_name = $rwdes['port_name'];
                                            echo "<option value='$port_master_id'>$port_name</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="destination_port" class="form-label">Destination Port</label>
                            <select id='destination_port' name='destination_port' class='form-control small-input ' required>
                                <?php
                                    if(isset($PI_NoNew))
                                    {
                                        echo "<option value='$destination_port'>$destination_port_name</option>";
                                        $rstdes = mysqli_query($connect,"select * from port_master where status='Active' and port_master_id!='$destination_port' ");
                                        while($rwdes = mysqli_fetch_assoc($rstdes))
                                        {
                                            $port_master_id = $rwdes['port_master_id'];
                                            $port_name = $rwdes['port_name'];
                                            echo "<option value='$port_master_id'>$port_name</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>                
                        <div class="col-md-2">
                            <label for="part_shipment" class="form-label">Part Shipment</label>
                            <!-- <input type="text" class='form-control small-input' id="part_shipment" name="part_shipment"> -->
                            <select id='part_shipment' name='part_shipment' class='form-control small-input ' required>
                                <?php
                                    if($PI_NoNew!="")
                                    {
                                        echo "<option value='$part_shipment'>$part_shipment</option>";
                                    }
                                ?>
                                <option value="">Select</option>
                                <option value="Allowed">Allowed</option>
                                <option value="Not Allowed">Not Allowed</option>
                            </select>
                        </div> 
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <label for="trans_shipment" class="form-label">Trans Shipment</label>
                            <!-- <input type="text" class='form-control small-input' id="trans_shipment" name="trans_shipment" > -->
                            <select id='trans_shipment' name='trans_shipment' class='form-control small-input ' required>
                                <?php
                                    if($PI_NoNew!="")
                                    {
                                        echo "<option value='$trans_shipment'>$trans_shipment</option>";
                                    }
                                ?>
                                <option value="">Select</option>
                                <option value="Allowed">Allowed</option>
                                <option value="Not Allowed">Not Allowed</option>
                            </select>
                        </div> 
                        <div class="col-md-2">
                            <label for="insurance" class="form-label">Insurance</label>
                            <!-- <input type="text" class='form-control small-input' id="insurance" name="insurance" > -->
                            <select id='insurance' name='insurance' class='form-control small-input ' required>
                                <?php
                                    if($PI_NoNew!="")
                                    {
                                        echo "<option value='$insurance'>$insurance</option>";
                                    }
                                ?>                   
                                <option value="">Select</option>
                                <option value="On sellers account">On sellers account</option>
                                <option value="On buyers account">On buyers account</option>
                            </select>
                        </div> 
                        <div class="col-md-2">
                            <label for="marking" class="form-label">Marking</label>
                            <!-- <input type="text" class='form-control small-input' id="marking" name="marking" > -->
                            <select id='marking' name='marking' class='form-control small-input ' required>
                                <?php
                                    if($PI_NoNew!="")
                                    {
                                        echo "<option value='$marking'>$marking</option>";
                                    }
                                ?> 
                                <option value="">Select</option>
                                <option value="English neutral marking">English neutral marking</option>
                                <option value="Manufacturers Bag">Manufacturers Bag</option>
                                <option value="Buyers Bag">Buyers Bag</option>
                            </select>
                        </div> 
                        <div class="col-md-2">
                            <label for="incoterms" class="form-label">Incoterms</label>
                            <select id='incoterms' name='incoterms' class='form-control small-input ' required>
                            
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="port" class="form-label">Port</label>
                            <select id='port' name='port' class='form-control small-input ' onchange="getCountryName()" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="country_name" class="form-label">Country Name</label>
                            <input type="text" readonly id="country_name" name="country_name" class='form-control small-input' value="<?= @$countryName ;?>">
                        </div>
                        
                        <!-- <div class="col-md-2">
                            <label for="packing" class="form-label">Packing</label>
                            <input type="text" class='form-control small-input' id="packing" name="packing" >
                        </div>  -->
                    </div>
                    <!-- Combined Info Section -->
                    
                    
                    <!-- Payment Details -->
                    <!-- <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="txtpaymentterm" class="form-label">Select Payment Terms</label>
                            <select name="txtpaymentterm" id="txtpaymentterm" class='form-control small-input form-select' required onchange="loadpaymenttermsdetails();">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="txtpaymentcode" class="form-label">Payment Code</label>
                            <input type="text" readonly class='form-control small-input bg-light' id="txtpaymentcode" name="txtpaymentcode">
                        </div>
                        <div class="col-md-3">
                            <label for="txtpaymentamt" class="form-label">Payment Amount</label>
                            <input type="text" readonly class='form-control small-input bg-light' id="txtpaymentamt" name="txtpaymentamt">
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <button type="submit" class="btn btn-gradient btn-lg px-2 shadow-lg" id="btnSavePI" style="background: linear-gradient(to right, #4facfe, #00f2fe); color: white; font-weight: bold; border: none; border-radius: 5px; transition: transform 0.2s;">
                                Generate Pro-Forma
                            </button>
                        </div>
                    </div> -->
                    <div class="row" style="border:1px solid black;margin: 1px;border: 1px solid #8a8d93; padding: 5px;">
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
                            <input type="number" name="quantity" id="quantity" placeholder="Qty" class="form-control small-input" oninput="calculatetotalamount();" />
                        </div> 
                        <div class="col-md-2">
                            <label>Rate/Ton</label>
                            <input type="number"  name="rate" id="rate" placeholder="Rate" class="form-control small-input"  oninput="calculatetotalamount();" />
                        </div> 
                        
                        <div class="col-md-2" >
                            <label>Total Amount</label>
                            <input type="number" readonly name="total_amount" id="total_amount" placeholder="Total Amount" class="form-control small-input"  />
                        </div> 
                        <div class="col-md-1 mt-4" >
                            <label></label>
                            <button type="button" class="btn btn-primary btn-sm" onclick="SaveProductDetails();" ><i class="fa fa-plus"></i></button>
                        </div>  
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
                                        <td>Total Amt.</td>
                                        <td>Action</td>
                                        <!-- <td>Specification</td> -->
                                        </tr>
                                    </thead>
                                    <tbody id="divpurchaseproducts">
                                        <?php
                                            if(isset($_GET['PI_No'])!="")
                                            {
                                                $rstpro = mysqli_query($connect,"select p.*, pp.product_name,pp.status,u.u_name from pro_forma_head_details p 
                                                inner join product_master pp on pp.product_id= p.product_id 
                                                inner join uom_master u on u.u_id=pp.uom_id
                                                where p.pi_no='$PI_NoNew'");
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

                                                        $rstproductdescription = mysqli_query($connect,"select * from product_description where product_id='$product_id' ");
                                                        
                                                        echo "<tr>
                                                                <td value='$product_id'>$product_name</td>
                                                                <td>$u_name</td>
                                                                <td><input type='number' class='form-control divproduct'  id='txtproductratePerton$product_id' oninput='CalculateProductEditRates(".$product_id.")' value='$rateperton'></td>
                                                                <td><input type='number' class='form-control divproduct' id='txtTotalWeight$product_id' oninput='CalculateProductEditRates(".$product_id.")' value='$total_weight'></td>
                                                                <td id='txtproductrate$product_id'>$rate</td>
                                                                <td style='display:none;'>0</td>
                                                                <td class='tdtotalamount' id='txtproducttotalamt$product_id'>$total_amt</td>
                                                                <td style='display:none;'>$status</td>
                                                                <td style='display:none;' id='txteach_bag_weight$product_id'>$each_bag_weight</td>
                                                                <td style='display:none;' id='txtproductnoofbags$product_id' >$no_of_bags</td>
                                                                <td><button class='btn btn-danger btn-sm btnremoveproduct'><i class='fa fa-close'></i></button></td>";
                                                        // <td>
                                                        // $rstproductdescription = mysqli_query($connect,"select * from product_description where product_id='$product_id' ");
                                                        // if(mysqli_num_rows($rstproductdescription)>0)
                                                        // {
                                                        //     while($rwpro = mysqli_fetch_assoc($rstproductdescription))
                                                        //     {
                                                        //         $descId = $rwpro['product_description_id'];
                                                        //         $descText = $rwpro['product_description'];
                                                        //         $checked ='';
                                                        //         $rstprodesc = mysqli_query($connect,"select proforma_product_description_id from proforma_product_description where product_id='$product_id' and po_no='$PI_NoNew' and description_id='$descId' ");
                                                        //         if(mysqli_num_rows($rstprodesc)>0)
                                                        //         {
                                                        //             $checked ="checked";
                                                        //         }
                                                        //         echo "
                                                        //             <div class='form-check'>
                                                        //                 <input class='form-check-input checkprodspecification'
                                                        //                 style='margin-top: 1px;'
                                                        //                 type='checkbox'
                                                        //                 name='product_desc_".$product_id."[]'
                                                        //                 data-product-id='$product_id'
                                                        //                 value='$descId' $checked>
                                                        //                 <label class='form-check-label' for='desc_$descId'>
                                                        //                     $descText
                                                        //                 </label>
                                                        //             </div>
                                                        //         ";
                                                        //     }
                                                        // }
                                                        // </td>
                                                        echo "</tr>";
                                                    }
                                                }
                                            }
                                        ?>
                                    </tbody>
                            </table>
                        </div> 
                        <div class="col-md-3" >
                            <label>Total Amount</label>
                            <input type="number" readonly name="totalAmtAll" id="totalAmtAll" placeholder="Total Amount" class="form-control small-input"  value="<?= @$total_amount; ?>"/>
                        </div> 
                        <div class="col-md-3" >
                            <label>Enter Discount (use %)</label>
                            <input type="text" name="DiscountAmt" id="DiscountAmt" placeholder="Total Amount" class="form-control small-input"  oninput="calculateDiscount();" value="<?= @$DiscountAmt; ?>"/>
                        </div> 
                        <div class="col-md-3" >
                            <label>Grand Total</label>
                            <input type="number" readonly name="grand_total" id="grand_total" placeholder="Total Amount" class="form-control small-input"  value="<?= @$grand_total; ?>"    />
                        </div> 
                    </div>
                    <br>
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
                                            $checkQuery = "SELECT terms_id, discription FROM pro_forma_head_termcondition_detail WHERE pi_no = '$PI_NoNew'";
                                            $checkResult = $connect->query($checkQuery);
                                            while ($row = $checkResult->fetch_assoc()) {
                                                $checkedTerms[] = $row['terms_id'];
                                                $checkedDescriptions[$row['terms_id']] = addslashes($row['discription']);
                                            }

                                            $cmd = "SELECT * FROM terms_conditions WHERE comp_id = '$comp_id' AND status = 1";
                                            $result = $connect->query($cmd);
                                            if ($result->num_rows > 0) {
                                                $i = 1;
                                                $script = "";
                                            
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

                            <!-- Shipment Document Section -->
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
                                            $checkQuery = "SELECT shipment_document_id FROM pro_forma_head_shipment_detail WHERE pi_no = '$PI_NoNew'";
                                            $checkResult = $connect->query($checkQuery);
                                            while ($rowdd = $checkResult->fetch_assoc()) {
                                                $checkedTerms[] = $rowdd['shipment_document_id'];
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
                        </div>
                    <textarea name="remark" id="remark" rows="3" class="form-control small-input mt-3" placeholder="Enter Remark" spellcheck="true"><?= $remark; ?></textarea>
                    <div class="col-md-4 mt-4">
                        <input type="submit" class="btn btn-success" id="btnSave" value="Save">
                        <a href="product_master.php"><button type="button" class="btn btn-warning">Reset</button></a>
                        <a href="index.php"><button type="button" class="btn btn-secondary" id="btnClose">Close</button></a>
                    </div>
                </div>
            </div>
	    </form>
    </div>

<div class="modal fade" id="LoadProductSpecificationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="ProdSpecificationHead">Add Product Specification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="row mt-3" id="divLoadProductSpecification"></div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>


	$(document).ready(function()
	{
        loadCountryList();
        loadPortList();
        loadIncotermsList();
        //ShowTermsConditions();
        //ShowShipmentDocument();
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
            // if(productstatus!=0)
            // {
                $("#product_id").append($('<option>', {
                    value: productId,
                    text: productName
                }));
            // }
            calculateDiscount();
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
	});
	
	function loadpaymenttermsdetails()
	{
		var selectedOption = $('#txtpaymentterm option:selected');
        var amount = selectedOption.attr('amount-attr');
        var code = selectedOption.attr('code-attr');
		$("#txtpaymentcode").val(code);
		$("#txtpaymentamt").val(amount);
	}
	$(document).ready(function(){
		$("#frmpaymentpo").submit(function(e){
			e.preventDefault();
            var formData = new FormData(this); // Use FormData instead of serialize
            formData.append("Flag", "SaveProFormaOnlyProductUpdate");
            formData.append("PI_NoNew", <?= $PI_NoNew; ?>);
            
            let tableData = [];
            $("#tblproductdetails tbody tr").each(function () {
                const row = $(this);
                const productId = row.find("td:eq(0)").attr("value");
                // const selectedDescriptions = [];
                // row.find("input.checkprodspecification:checked").each(function () {
                //     selectedDescriptions.push($(this).val());
                // });
                const rowData = {
                    product_id: productId,
                    ProductName: row.find("td:eq(0)").text().trim(),
                    uom: row.find("td:eq(1)").text().trim(),
                    rateperton: row.find("td:eq(2) input").val().trim(),
                    totalweight: row.find("td:eq(3) input").val().trim(),
                    rate: row.find("td:eq(4)").text().trim(),
                    gst_amount: row.find("td:eq(5)").text().trim(),
                    totalamt: row.find("td:eq(6)").text().trim(),
                    weightperpack: row.find("td:eq(8)").text().trim(),
                    quantity: row.find("td:eq(9)").text().trim(),
                    // descriptions: selectedDescriptions 
                };

                tableData.push(rowData);
            });

            if(tableData.length=="0" || tableData=="[]")
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
                formData.append("tableData", JSON.stringify(tableData));
            
                $.ajax({
                    url:"operation/pro_forma_operation.php",
                    type: "POST", 
                    data: formData,
                    contentType: false, 
                    processData: false, 
                    success: function (response) {
                        var res = JSON.parse(response);
                        if(res.Status=="Success")
                        {
                            Swal.fire({
                                title: 'Success',
                                text: res.Message,
                                icon: 'success',
                            });
                            setTimeout(() => {
                                window.location.href="pro-forma.php";
                            }, 2000);
                        }
                        else
                        {
                            Swal.fire({
                                title: 'OOps',
                                text: res.Message,
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
    function loadClientCountry()
    {
        $.post("../component.php",{
            Flag:"loadClientCountry",
            account_id : $("#account_id option:selected").val()
        },function(data,success){
            $("#country_id").html(data);
        });
    }
    function loadCountryList()
    {
        $.post("../component.php",{
            Flag:"LoadCountry",
        },function(data,success){
            <?php if($PI_NoNew!=""){ ?>        
                $("#country_of_supply").html("<option value='<?= $country_of_supply; ?>'><?= $CountryOfShipment ?></option>"); 
                $("#country_of_origin").html("<option value='<?= $country_of_origin; ?>'><?= $CountryOfOrigin ?></option>"); 
            <?php }else{ ?>        
                $("#country_of_origin").html(data);
                $("#country_of_supply").html(data);
            <?php } ?>
        });
    }
    function loadPortList()
    {
        $.post("../component.php",{
            Flag:"LoadPort",
        },function(data,success){
            <?php if($PI_NoNew!=""){ ?>        
                // $("#port_of_loading").html("<option value='<?= $port_of_loading ?>'><?= $port_of_loading_name ?></option>"); 
                // $("#destination_port").html("<option value='<?= $destination_port ?>'><?= $destination_port_name ?></option>"); 
                $("#port").html("<option value='<?= $port_id; ?>'><?= $port_name ?></option>"); 
            <?php }else{ ?>        
                $("#port_of_loading").html(data);
                $("#destination_port").html(data);
                $("#port").html(data);
            <?php } ?>
        });
    }
    function loadIncotermsList()
    {
        $.post("../component.php",{
            Flag:"loadIncoterms",
        },function(data,success){
            <?php if($PI_NoNew!=""){ ?>        
                $("#incoterms").html("<option value='<?= $incoterms_id ?>'><?= $incoterms ?> - <?= $incoterms_fullform ?></option>"); 
            <?php }else{ ?>        
                $("#incoterms").html(data);
            <?php } ?>
        });
    }
    function getCountryName(){

        var port_id = $("#port option:selected").val();
        if(port_id!="")
        {
            $.post("../component.php",{
                Flag:"LoadCountryDetails",
                port_id:port_id
            },function(data,success){
                var res = JSON.parse(data);
                $("#country_name").val(res.countryName);
                
            });
        }
        else
        {
            $("#country_name").val('');
        }
    }
    function LoadClientDetails()
    {
        var account_id = $("#account_id option:selected").val();
        if(account_id!="")
        {
            $.post("../component.php",{
                Flag:"LoadClientDetails",
                account_id:account_id
            },function(data,success){
                var res = JSON.parse(data);
                $("#txtkindAttention").html(res.kind_attention);
                $("#txtmobileNo").html(res.client_mob);
                $("#txtAddress").html(res.client_add);
            });
        }
        else
        {
            $("#txtkindAttention").html('');
            $("#txtmobileNo").html('');
            $("#txtAddress").html('');
        }
    }
    function SaveProductDetails()
    {
        var product_id = $("#product_id option:selected").val();
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
        else
        {
            $.post("operation/pro_forma_operation.php",{
            Flag:"SaveProductDetails",
            product_id:product_id,
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
                $("#btnproudctdata").html("Select Product");
                $("#product_id option[value='" + product_id + "']").remove();
            })
        }
    }
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
                // $("#rate").val(res.rate);
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
    function calculatetotalamount()
    {
        var weightperpack = $("#weightperpack").val();
        var rate = $("#rate").val();
        var quantity = $("#quantity").val();
        var TotalWeight = weightperpack*quantity;
        var RatePerKg = rate/1000;
        // alert(TotalWeight);
        // alert(RatePerKg);
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
        // else if(totalamount<=0)
        // {
        //     alert("Please Enter Valid Amount");
        //     $("#total_amount").focus();
        // }
        else
        {
            $("#total_amount").val(totalamount);
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
    function LoadCurrentBankDetails(){
        $.post("../component.php",{
            Flag:"LoadCurrencyBankDetails",
            currency_id : $("#currency_id option:selected").val()
        },function(data,success){
            $("#bank_account_no").html(data);
        });
    }
    // function showProdDescModal(product_id)
    // {
    //     $("#LoadProductSpecificationModal").modal("toggle");
    //     $("#divLoadProductSpecification").html(data);
    // }
    function CalculateProductEditRates(ProductId)
    {
        var productratePerton = $("#txtproductratePerton"+ProductId).val();
        var TotalWeight = $("#txtTotalWeight"+ProductId).val();
        var each_bag_weight = $("#txteach_bag_weight"+ProductId).html();
        var PINo = "<?= $PI_NoNew ?>";
        
        $.post('operation/pro_forma_operation.php',{
            Flag:"LoadProductQtyUsedCalculationDetails",
            PINo:PINo,
            productratePerton:productratePerton,
            TotalWeight:TotalWeight,
            ProductId:ProductId
        },function(data,success)
        {
            var res = JSON.parse(data);
            if(res.Status=="Success")
            {   
                var totalRate = productratePerton/1000;
                var TOtalAmt = totalRate*TotalWeight;
                var TotalNoOFBags = TotalWeight/each_bag_weight;
                $("#txtproductrate"+ProductId).html(totalRate);
                $("#txtproducttotalamt"+ProductId).html(TOtalAmt);
                $("#txtproductnoofbags"+ProductId).html(TotalNoOFBags);
            }else{
                Swal.fire({
                    title: 'Oops',
                    text: res.Message,
                    icon: 'warning',
                });
                var TotalWeightNew = 0;
                var totalRate = productratePerton/1000;
                var TOtalAmt = totalRate*TotalWeightNew;
                var TotalNoOFBags = TotalWeightNew/each_bag_weight;
                $("#txtproductrate"+ProductId).html(totalRate);
                $("#txtproducttotalamt"+ProductId).html(TOtalAmt);
                $("#txtproductnoofbags"+ProductId).html(TotalNoOFBags);
            }
        });  
    }
</script>