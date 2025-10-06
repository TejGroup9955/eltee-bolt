<?php
include_once("../../configuration.php");
$Flag = $_POST['Flag'];
session_start();
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$dept_id = @$_SESSION['dept_id'];
$branch_id = @$_SESSION['branch_id'];
$UserNameSession = @$_SESSION['user_name'];
$financial_year = @$_SESSION['financial_year'];
// foreach ($_POST as $key => $value) {
//     $_POST[$key] = sanitizeInput($value, $connect);
// }
extract($_POST);

if($Flag=="SaveProductDetails")
{
    // if($gst==""){ $gst=0;}
    $gst=0;
    $gst_amount = ($total_amount * $gst)/100;
    $totakamountwithgst = $total_amount+$gst_amount;
    $ratesingle = $rateperton/1000;
    $totalWeight = $weightperpack*$quantity;
    echo "<tr>
            <td value='$product_id'>$product_name</td>
            <td>$uom</td>
            <td>$rateperton</td>
            <td>$totalWeight</td>
            <td>$ratesingle</td>
            <td style='display:none;'>0</td>
            <td class='tdtotalamount'>$totakamountwithgst</td>
            <td style='display:none;'>$prostatus</td>
            <td style='display:none;'>$weightperpack</td>
            <td style='display:none;'>$quantity</td>
            <td><button class='btn btn-danger btn-sm btnremoveproduct'><i class='fa fa-close'></i></button></td>";

        // $rstproductdescription = mysqli_query($connect,"select * from product_description where product_id='$product_id' ");
        // if(mysqli_num_rows($rstproductdescription)>0)
        // {
        //     while($rwpro = mysqli_fetch_assoc($rstproductdescription))
        //     {
        //         $descId = $rwpro['product_description_id'];
        //         $descText = $rwpro['product_description'];

        //         echo "
        //             <div class='form-check'>
        //                 <input class='form-check-input checkprodspecification'
        //                 style='margin-top: 1px;'
        //                 type='checkbox'
        //                 name='product_desc_".$product_id."[]'
        //                 data-product-id='$product_id'
        //                 value='$descId'>
        //                 <label class='form-check-label' for='desc_$descId'>
        //                     $descText
        //                 </label>
        //             </div>
        //         ";
        //     }
        // }
    echo "</tr>";
}
if($Flag=="showuom")
{
    $temp=array();
    $product_id = $_POST['product_id'];
    $rstpro = mysqli_query($connect,"select p.status, u.u_name, p.rate,p.gst_precent from product_master p
    inner join uom_master u on u.u_id = p.uom_id where p.product_id='$product_id'");
    $rwpro = mysqli_fetch_assoc($rstpro);
    $uom = $rwpro['u_name'];
    $rate = $rwpro['rate'];
    $gst_precent = $rwpro['gst_precent'];
    $product_status = $rwpro['status'];
    $temp['uom']= $uom;
    $temp['rate']= $rate;
    $temp['gst_precent']= $gst_precent;
    $temp['product_status']= $product_status;
    echo json_encode($temp);
}
if($Flag=="loadPackagingType")
{
    $product_id = $_POST['product_id'];
    $rstpack = mysqli_query($connect,"select packing_type from product_master where product_id='$product_id'");
    $rwpack = mysqli_fetch_assoc($rstpack);
    $packing_type = $rwpack['packing_type'];
    $Package = explode(',',$packing_type);
    $PackNames ="<option value=''>Select Option</option>";
    foreach($Package as $Packs)
    {
        if($Packs!='')
        {
            $rstpack = mysqli_query($connect,"select * from packaging_type where id='$Packs'");
            $rwpack = mysqli_fetch_assoc($rstpack);
            $PackNames .= "<option value='$Packs'>".$rwpack['packaging_type']."</option>";
        }
    }
    echo $PackNames;
}
if($Flag=="SaveProForma")
{
    // print_r($_POST);
    // exit();
    if($hiddenPiNo=="")
    {
        $i=0;
        $j=0;
        $pi_custom_number = 0;
        $destination_port_name = $port_of_loading_name = "";
        $cmd_1 = "SELECT pi_custom_number FROM pro_forma_head 
                WHERE year_id = '$financial_year' AND comp_id = '$comp_id' 
                ORDER BY pi_no DESC LIMIT 1";
        $result_1 = $connect->query($cmd_1);
        $pi_custom_number = 50;
        if ($result_1->num_rows > 0) {
            if ($row_1 = $result_1->fetch_assoc()) {
                $piArr = explode("/", $row_1['pi_custom_number']); // ["ED-0050", "2025"]
                $prefixPart = $piArr[0]; // "ED-0050"
                $prefixArr = explode("-", $prefixPart); // ["ED", "0050"]
                $pi_custom_number = intval($prefixArr[1]) + 1; // Increment number
            }
        }
        $pi_number_padded = str_pad($pi_custom_number, 4, "0", STR_PAD_LEFT);
        $cmd_year = "SELECT year_name FROM financial_year WHERE year_id = '$financial_year' LIMIT 1";
        $res_year = $connect->query($cmd_year);
        $year_part = "0000"; // fallback
        if ($res_year->num_rows > 0) {
            $row_year = $res_year->fetch_assoc();
            $fyParts = explode("-", $row_year['year_name']);
            if (count($fyParts) === 2) {
                $year_part = $fyParts[0]; 
            }
        }
        $pi_custom_number = "ED-$pi_number_padded/$year_part";
        // echo $final_custom_number;
        // exit;

        if($user_type_id=="1")
        {
            $pro_forma_status = "1";
        }
        else
        {
            $pro_forma_status = "-1";
        }
        if($port_of_loading != ''){
            $portres = mysqli_query($connect,"select port_name from port_master where status='Active' AND port_master_id='$port_of_loading'");
            $rwport = mysqli_fetch_assoc($portres);
            $port_of_loading_name = $rwport['port_name'];
        }
        if($destination_port != ''){
            $portres1 = mysqli_query($connect,"select port_name from port_master where status='Active' AND port_master_id='$destination_port'");
            $rwport1 = mysqli_fetch_assoc($portres1);
            $destination_port_name = $rwport1['port_name'];
        }
      
        $error_product_name = "";
        $sql = "INSERT INTO `pro_forma_head`(pi_custom_number, pi_invoice_date, account_id, 
        country_id,currency_id, remark, total_amount,grand_total,user_id, comp_id, branch_id, year_id, pro_forma_status,DiscountAmt,bank_detail_id,time_of_shipment, country_of_origin, country_of_supply, port_of_loading, destination_port, port_of_loading_name,destination_port_name,part_shipment, trans_shipment, insurance, marking,port_id,country_name,incoterms_id) 
        VALUES('$pi_custom_number',
        '$pi_invoice_date', '$account_id', 
        '$country_id','$currency_id','$remark', '$totalAmtAll',
        '$grand_total', '$user_id_session', '$comp_id', '$branch_id', '$financial_year', 
        '$pro_forma_status','$DiscountAmt','$bank_account_no','$time_of_shipment','$country_of_origin','$country_of_supply','$port_of_loading','$destination_port','$port_of_loading_name','$destination_port_name','$part_shipment','$trans_shipment','$insurance','$marking','$port','$country_name','$incoterms') ";
        $query_res1 = $connect->query($sql);
        $pi_no_new = mysqli_insert_id($connect);
        if($query_res1 > 0)
        {
            $todate = date('Y-m-d');
            if(isset($_POST['tableData'])){
                $tableDataNew = json_decode($_POST['tableData'], true);
                foreach ($tableDataNew as $contact) {
                    $product_id = $contact['product_id'];
                    $quantity = $contact['quantity'];
                    $rate = $contact['rate'];
                    $totalamt = $contact['totalamt'];
                    $gst_amount = $contact['gst_amount'];
                    $totalweight = $contact['totalweight'];
                    $rateperton = $contact['rateperton'];
                    $weightperpack = $contact['weightperpack'];
                    // $descriptions = $contact['descriptions'];

                    $query =" INSERT INTO `pro_forma_head_details`(pi_no,
                    product_id, total_weight, rate, total_amt, gst,packaging_type,each_bag_weight,
                    rateperton,packaging_id,no_of_bags) 
                    VALUES('$pi_no_new','$product_id','$totalweight','$rate','$totalamt','$gst_amount',
                    '$packagingTypeName', '$weightperpack','$rateperton','$packaging_id','$quantity')";
                    $query_res2 = $connect->query($query); 
                    
                    // foreach($descriptions as $prodesc)
                    // {
                    //     $rstdesc = mysqli_query($connect,"INSERT INTO `proforma_product_description`
                    //     (`description_id`, `po_no`, `product_id`) 
                    //     VALUES ('$prodesc','$pi_no_new','$product_id')");
                    // }
                    $i++;
                }
            }

            if(isset($_POST['shipData'])){
                $shipDataNew = json_decode($_POST['shipData'], true);
                foreach ($shipDataNew as $record) {
                    $shipquery1 =" INSERT INTO `pro_forma_head_shipment_detail`(pi_no,
                    shipment_document_name,shipment_document_id) 
                    VALUES('$pi_no_new','$record[0]','$record[1]')";
                    $query_result1 = $connect->query($shipquery1);       
                }
            }

            if(isset($_POST['termData'])){
                $termDataNew = json_decode($_POST['termData'], true);
                foreach ($termDataNew as $termrecord) {
                    $query2 =" INSERT INTO `pro_forma_head_termcondition_detail`(pi_no,
                    terms_id,title,discription) 
                    VALUES('$pi_no_new','$termrecord[0]','$termrecord[1]','$termrecord[2]')";
                    $query_result2 = $connect->query($query2);       
                }
            }

            if(@$query_res2 > 0)
            {
                foreach ($payment_per as $payment_per1) 
                {
                    if($payment_desc[$j]=="Before"){ $payment_desc[$j]=0; }
                    if($after_payment_desc[$j]=="After"){ $after_payment_desc[$j]=0; }
                    $query1 ="INSERT INTO `pro_forma_payment`(pi_no,`pay_percentage`,`pay_in_advance`,
                    `payment_desc_id`, `after_percentage`,`pay_later`, `after_payment_desc_id`,
                    `payment_mode_id`, `time_period`) VALUES ('$pi_no_new','$payment_per1',
                    '$pay_in_advance[$j]','$payment_desc[$j]','$after_payment_per[$j]','$pay_later[$j]',
                    '$after_payment_desc[$j]','$payment_mode[$j]','$period[$j]')";
                    $query_res3 = $connect->query($query1);
                    $j++;
                }
                if($query_res3 > 0 && $error_product_name == "")
                {
                    mysqli_commit($connect);
                
                    echo 'Pro-Forma Invoice generated successfully';
                }
                else {   $error_count = 1; }
            }
            else {    $error_count = 1; }
        }
        else {    $error_count = 1; }
    }
    else
    {
        $i=0;
        $j=0;
        $error_product_name = "";
        $destination_port_name = $port_of_loading_name = "";
        if($port_of_loading != ''){
            $portres = mysqli_query($connect,"select port_name from port_master where status='Active' AND port_master_id='$port_of_loading'");
            $rwport = mysqli_fetch_assoc($portres);
            $port_of_loading_name = $rwport['port_name'];
        }
        if($destination_port != ''){
            $portres1 = mysqli_query($connect,"select port_name from port_master where status='Active' AND port_master_id='$destination_port'");
            $rwport1 = mysqli_fetch_assoc($portres1);
            $destination_port_name = $rwport1['port_name'];
        }

        $sql = "UPDATE `pro_forma_head` 
        SET  account_id = '$account_id', 
            country_id = '$country_id', 
            currency_id = '$currency_id', 
            remark = '$remark', 
            total_amount = '$totalAmtAll', 
            grand_total = '$grand_total', 
            DiscountAmt = '$DiscountAmt', 
            bank_detail_id = '$bank_account_no', 
            time_of_shipment = '$time_of_shipment', 
            country_of_origin = '$country_of_origin', 
            country_of_supply = '$country_of_supply', 
            port_of_loading = '$port_of_loading', 
            destination_port = '$destination_port', 
            port_of_loading_name = '$port_of_loading_name', 
            destination_port_name = '$destination_port_name', 
            part_shipment = '$part_shipment', 
            trans_shipment = '$trans_shipment', 
            insurance = '$insurance', 
            marking = '$marking', 
            port_id = '$port', 
            country_name = '$country_name', 
            incoterms_id = '$incoterms' 
        WHERE pi_no = '$hiddenPiNo'";

        $query_res1 = $connect->query($sql);
        if($query_res1 > 0)
        {
            $todate = date('Y-m-d');
            if(isset($_POST['tableData'])){
                $rstdel = mysqli_query($connect,"delete from pro_forma_head_details where pi_no = '$hiddenPiNo'");
                // $rstdel = mysqli_query($connect,"delete from proforma_product_description where po_no = '$hiddenPiNo'");
                $tableDataNew = json_decode($_POST['tableData'], true);
                foreach ($tableDataNew as $contact) {
                    $product_id = $contact['product_id'];
                    $quantity = $contact['quantity'];
                    $rate = $contact['rate'];
                    $totalamt = $contact['totalamt'];
                    $gst_amount = $contact['gst_amount'];
                    $totalweight = $contact['totalweight'];
                    $rateperton = $contact['rateperton'];
                    $weightperpack = $contact['weightperpack'];
                    // $descriptions = $contact['descriptions'];

                    $query =" INSERT INTO `pro_forma_head_details`(pi_no,
                    product_id, total_weight, rate, total_amt, gst,packaging_type,each_bag_weight,
                    rateperton,no_of_bags) 
                    VALUES('$hiddenPiNo','$product_id','$totalweight','$rate','$totalamt','$gst_amount',
                    '$packagingTypeName', '$weightperpack','$rateperton','$quantity')";
                    $query_res2 = $connect->query($query);
                    
                    // foreach($descriptions as $prodesc)
                    // {   
                    //     $rstdesc = mysqli_query($connect,"INSERT INTO `proforma_product_description`
                    //     (`description_id`, `po_no`, `product_id`) 
                    //     VALUES ('$prodesc','$hiddenPiNo','$product_id')");
                    // }
                    $i++;
                }
            }

            if(isset($_POST['shipData'])){
                $rstdel = mysqli_query($connect,"delete from pro_forma_head_shipment_detail where pi_no = '$hiddenPiNo'");
                $shipDataNew = json_decode($_POST['shipData'], true);
                foreach ($shipDataNew as $record) {
                    $shipquery1 =" INSERT INTO `pro_forma_head_shipment_detail`(pi_no,
                    shipment_document_name,shipment_document_id) 
                    VALUES('$hiddenPiNo','$record[0]','$record[1]')";
                    $query_result1 = $connect->query($shipquery1);       
                }
            }

            if(isset($_POST['termData'])){
                $rstdel = mysqli_query($connect,"delete from pro_forma_head_termcondition_detail where pi_no = '$hiddenPiNo'");
                $termDataNew = json_decode($_POST['termData'], true);
                foreach ($termDataNew as $termrecord) {
                    $query2 =" INSERT INTO `pro_forma_head_termcondition_detail`(pi_no,
                    terms_id,title,discription) 
                    VALUES('$hiddenPiNo','$termrecord[0]','$termrecord[1]','$termrecord[2]')";
                    $query_result2 = $connect->query($query2);       
                }
            }

            if(@$query_res2 > 0)
            {
                foreach ($payment_per as $payment_per1) 
                {
                    if($payment_desc[$j]=="Before"){ $payment_desc[$j]=0; }
                    if($after_payment_desc[$j]=="After"){ $after_payment_desc[$j]=0; }
                    $query1 ="update `pro_forma_payment` SET
                    `pay_percentage`='$payment_per1',
                    `pay_in_advance`='$pay_in_advance[$j]',
                    `payment_desc_id`='$payment_desc[$j]',
                    `after_percentage`='$after_payment_per[$j]',
                    `pay_later`='$pay_later[$j]',
                    `after_payment_desc_id`='$after_payment_desc[$j]',
                    `payment_mode_id`='$payment_mode[$j]', 
                    `time_period` ='$period[$j]' where pi_no = '$hiddenPiNo' ";
                    $query_res3 = $connect->query($query1);
                    $j++;
                }
                if($query_res3 > 0 && $error_product_name == "")
                {
                    mysqli_commit($connect);
                
                    echo 'Pro-Forma Invoice Updated successfully';
                }
                else {   $error_count = 1; }
            }
            else {    $error_count = 1; }
        }
        else {    $error_count = 1; }
    }
    if(@$error_count == 1)
    {
        echo 'Connection Problem!!! Please try again..';
    }

}

if($Flag=="ShowProFormaList")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,c.client_mob, cm.currency_code from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1' ";
        // if($user_type_id!="1")
        // {
        //     $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        // }
        if($user_type_id=="1")
        {
            $query.=" AND p.branch_id='$branch_id' AND p.pro_forma_status IN ('1') ";
        }
        else
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id' AND p.pro_forma_status IN ('-1','1','0') ";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            if($user_type_id=="1")
            {
                $SendMailAndWhatsappbtn='';
                if($pro_forma_status=="1")
                {
                    // <a href='Pro-Forma-Invoice.php?PI_No=".base64_encode($pi_no)."'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-pencil'></i></a>
                    $EditDeactiveBtn='';
                    $rstproproduct = mysqli_query($connect,"SELECT purchase_order_id FROM purchase_order_details WHERE FIND_IN_SET('$pi_no', pi_no) > 0");
                    if(mysqli_num_rows($rstproproduct)>0)
                    {
                        // $EditDeactiveBtn = "<a href='Pro-Forma-Invoice.php?PI_No=".base64_encode($pi_no)."&EditMode=IsProductEdit'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                    }
                    else{
                        $EditDeactiveBtn = "<button class='btn btn-sm btn-round btn-danger' onclick='ApproveProForma($pi_no,0)'>Deactive Proforma</button>";
                    }
                    $SendMailAndWhatsappbtn = "<button class='btn btn-sm btn-round btn-info' onclick='sendproformamail($pi_no, $account_id, \"".$pi_custom_number."\",\"".$pi_invoice_date."\")'><i class='fa fa-envelope'></i></button>
                    <button class='btn btn-sm btn-round btn-success' onclick='sendproformawhastapp(\"".$client_name."\", \"".$client_mob."\",  \"".$pi_custom_number."\",\"".$pi_invoice_date."\")'><i class='fa fa-whatsapp'></i></button>";
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>$EditDeactiveBtn";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Pending</button>";
                }
            }
            else
            {
                if($pro_forma_status=="1")
                {   
                    $EditDeactiveBtn='';
                    $rstproproduct = mysqli_query($connect,"SELECT purchase_order_id FROM purchase_order_details WHERE FIND_IN_SET('$pi_no', pi_no) > 0");
                    if(mysqli_num_rows($rstproproduct)>0)
                    {
                        $EditDeactiveBtn = "<a href='Edit-Pro-Forma-Invoice.php?PI_No=".base64_encode($pi_no)."&EditMode=" . base64_encode('IsProductEdit') . "'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                    }
                
                     $SendMailAndWhatsappbtn = "<button class='btn btn-sm btn-round btn-info' onclick='sendproformamail($pi_no, $account_id, \"".$pi_custom_number."\",\"".$pi_invoice_date."\")'><i class='fa fa-envelope'></i></button>
                    <button class='btn btn-sm btn-round btn-success' onclick='sendproformawhastapp(\"".$client_name."\", \"".$client_mob."\", \"".$pi_custom_number."\",\"".$pi_invoice_date."\")'><i class='fa fa-whatsapp'></i></button>";
                    

                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>$EditDeactiveBtn";
                }
                else if($pro_forma_status=="-1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-info' onclick='SendForApproval($pi_no)'>Send For Approval</button><a href='Pro-Forma-Invoice.php?PI_No=".base64_encode($pi_no)."'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-secondary'>Waiting For Approval</button>";
                }
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$proFormabutton.'<a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-sm btn-round btn-secondary"><i class="fa fa-print"></i></a>'.$SendMailAndWhatsappbtn.'</td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowProFormaListDocumentListSalesLogin")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Documents</th>
                    <th>Check Status</th>
                    <th>Telex Status</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        where p.active_status='1' and p.pro_forma_status='1' and p.branch_id='$branch_id' and logistic_send_for_approval_status='1' ";

        if($user_type_id!="1")
        {
            $query.= " and p.user_id='$user_id_session' ";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            echo '<tr>
            <td>'.$srno.'</td>
            <td><a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>';
            echo '<td>';
            $rstdocuments = mysqli_query($connect,"select * from proforma_sales_documents where proforma_id='$pi_no' ");
            $isfound = false;
            if(mysqli_num_rows($rstdocuments)>0)
            {
                $isfound=true;
                while($rwdocument = mysqli_fetch_assoc($rstdocuments))
                {
                    $document_path = $rwdocument['document_path'];
                    $document_name = $rwdocument['document_name'];
                    echo $document_name." <a href='production/".$document_path."' target='_blank'><button type='button' class='btn btn-info btn-sm btn-round'><i class='fa fa-eye'></i></button></a>";
                }
            }
            echo '</td>';
            
            if($isfound==true)
            {   
                if($user_type_id=="71" && $sales_approve_document_status=="0")
                {
                    echo "<td><button type='button' class='btn btn-info btn-round btn-sm' onclick='CheckdocumentsStatusUpdate(".$pi_no.")'> Mark As Checked</button></td>";
                }
                else if($sales_approve_document_status=="1")
                {
                    echo "<td><button type='button' class='btn btn-success btn-round btn-sm'><i class='fa fa-check'></i>Checked</button></td>";
                }
                else {
                    echo "<td><button type='button' class='btn btn-warning btn-round btn-sm'>Pending</button></td>";
                }
                
            }
            else{
                 echo "<td></td>";
            }

            if($sales_approve_document_status=="1")
            {
                if($telex_release_status=="1")
                {
                    echo "<td><button type='button' class='btn btn-success btn-round btn-sm'> Telex Released</button></td>";
                }
                else if($telex_release_status=="0" && $user_type_id=="1")
                {
                    echo "<td><button type='button' class='btn btn-info btn-round btn-sm' onclick='UpdateTelexStatus(".$pi_no.")'><i class='fa fa-check'></i> Telex Release</button></td>";
                }
                else{
                    echo "<td><button type='button' class='btn btn-warning btn-round btn-sm'>Telex Pending</button></td>";
                }
            }else{
                 echo "<td></td>";
            }
            echo '</tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowProFormaListForSalesDocument")
{
                        // <th style="width: 35px;"> <input type="checkbox" id="AllCheckBox" /></th>
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Action</th>
                    <th>Tax Invoice</th>
                     <th>Packaging List</th>
                    <th>COA</th>
                    <th>COO</th>
                    <th>BRC</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                    <th>Send For Checking</th>
                    <th>Mail </th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,cm.currency_code from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1' and p.pro_forma_status='1' and p.branch_id='$branch_id' ";

        $query .= " order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            if($Sales_document_status=="1")
            {
                $salesdocument = '<button data-toggle="tooltip" title="Edit Shipment Documents" class="btn btn-sm btn-round btn-warning" onclick="UpdateShipmentDoc('.$pi_no.',\''.$client_name.'\');"><i class="fa fa-pencil"></i></button>';
            }
            else{
                $salesdocument = '<button class="btn btn-sm btn-round btn-success" data-toggle="tooltip" title="Upload Sales Documents" onclick="LoadDocumentModule('.$pi_no.',\''.$client_name.'\');"><i class="fa fa-plus"></i></button>';
            }

            $disabled="disabled";
            if($TaxInvoicePrintStatus=="1")
            {
                 $disabled="";
                $TaxInvoicePrintBtn = '<a href="print_tax_invoice.php?PI_No='.base64_encode($pi_no).'" target="_blank" data-toggle="tooltip" title="Tax Invoice Print" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
            }else{
                $TaxInvoicePrintBtn = '<button class="btn btn-sm btn-round btn-secondary" data-toggle="tooltip" title="Generate Tax Invoice" onclick="LoadTaxInvoiceModal('.$pi_no.',\''.$client_name.'\');"><i class="fa fa-plus"></i></button>';
            }

            if($COAPrintStatus=="1")
            {   
                $COAPrintBtn = '<a href="print_coa.php?PI_No='.base64_encode($pi_no).'" target="_blank" data-toggle="tooltip" title="COA Print" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
            }else{
                $COAPrintBtn = '<button class="btn btn-sm btn-round btn-secondary" data-toggle="tooltip" title="Generate COA Print" onclick="GenerateCOAPrint('.$pi_no.',\''.$client_name.'\');" ><i class="fa fa-plus"></i></button>';
            }

            if($COOPrintStatus=="1")
            {   
                $COOPrintBtn = '<a href="print_coo.php?PI_No='.base64_encode($pi_no).'" target="_blank" ata-toggle="tooltip" title="COO Print"  class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
            }else{
                $COOPrintBtn = '<button class="btn btn-sm btn-round btn-secondary" data-toggle="tooltip" title="Generate COO Print" onclick="GeneratePrints('.$pi_no.',\''.$client_name.'\', \'coo\');" '.$disabled.'><i class="fa fa-plus"></i></button>';
            }

             if($PKLPrintStatus=="1")
            {   
                $PKLPrintBtn = '<a href="print_pkl.php?PI_No='.base64_encode($pi_no).'" target="_blank" data-toggle="tooltip" title="Packaging List Print" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
            }else{
                $PKLPrintBtn = '<button class="btn btn-sm btn-round btn-secondary" data-toggle="tooltip" title="Generate Packaging List" onclick="GeneratePrints('.$pi_no.',\''.$client_name.'\', \'pkl\');" '.$disabled.'><i class="fa fa-plus"></i></button>';
            }

            if($BRCPrintStatus=="1")
            {
                $BRCPrintBtn = '<a href="print_brc.php?PI_No='.base64_encode($pi_no).'" target="_blank" data-toggle="tooltip" title="Batch Release Certificate Print" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
            }else{
                $BRCPrintBtn = '<button class="btn btn-sm btn-round btn-secondary" data-toggle="tooltip" title="Generate Batch Release Certificate" onclick="GeneratePrints('.$pi_no.',\''.$client_name.'\', \'brc\');" '.$disabled.'><i class="fa fa-plus"></i></button>';
            }
            
            // <td> <input type="checkbox" name="check[]" class="Alltdcheckbox" value="'.$pi_no.'"/></td>
            echo '<tr>
            <td><a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-sm btn-round btn-info">
            <i class="fa fa-print"></i></a>'.$salesdocument.'</td>
            <td>'.$TaxInvoicePrintBtn.'</td>
            <td>'.$PKLPrintBtn.'</td>
            <td>'.$COAPrintBtn.'</td>
            <td>'.$COOPrintBtn.'</td>
            <td>'.$BRCPrintBtn.'</td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>';
            if($logistic_send_for_approval_status=="1" && $sales_approve_document_status=="1")
            {
                echo "<td><button type='button' class='btn btn-sm btn-round btn-success'> Checking Done</button></td>";
            }
            else if($logistic_send_for_approval_status=="1" && $sales_approve_document_status=="0")
            {
                echo "<td><button type='button' class='btn btn-sm btn-round btn-warning'> Checking Pending</button></td>";
            }
            else if($Sales_document_status=="1" && $COAPrintStatus == "1" && $COOPrintStatus == "1" && $PKLPrintStatus == "1" && $logistic_send_for_approval_status=="0")
            {
                echo "<td><button type='button' class='btn btn-sm btn-round btn-info' onclick='SendForSalesChecking(".$pi_no.")'> Send For Checking</button></td>";
            }
            else{
                echo "<td>-</td>";
            }

            if($sales_approve_document_status=="1")
            {
                echo "<td><button type='button' class='btn btn-sm btn-round btn-info' onclick='SendSalesDocumentMail(".$pi_no.");'> Send Mail</button></td>";
            }
            else{
                echo "<td></td>";
            }
            echo '</tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowProFormaRequests")
{
    echo ' <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,cm.currency_code from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1'";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        }
        if($user_type_id=="1")
        {
            $query.=" and p.pro_forma_status ='0' ";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            if($user_type_id=="1")
            {
                if($pro_forma_status=="1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                }
                else if($pro_forma_status=="-1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success' onclick='ApproveProForma($pi_no,1)'>Approve</button>
                    <button class='btn btn-sm btn-round btn-danger' onclick='ApproveProForma($pi_no,0)'>Reject</button>";
                }
            }
            
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$proFormabutton.'<a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-info btn-round btn-sm"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}

if($Flag=="SendForApproval")
{
    $pi_no = $_POST['pi_no'];
    $rstpro = mysqli_query($connect,"update pro_forma_head set pro_forma_status='0' where pi_no='$pi_no'");
    if($rstpro)
    {
        echo 'Approved';
    }
    else
    {
        echo "Unable To Send For Approval";
    }
}
if($Flag=="ProFormaRequestAction")
{
    if($ApproveStatus=="1")
    {
        $pro_forma_status='1';
        $active_status='1';
    }
    else
    {
        $pro_forma_status='0';
        $active_status='0';
    }
    $rstpro = mysqli_query($connect,"update pro_forma_head set pro_forma_status='$pro_forma_status',active_status='$active_status' where pi_no='$pi_no'");
    if($rstpro)
    {
        echo 'Approved';
    }
    else
    {
        echo "Unable To Update";
    }
}
if($Flag=="ShowDeactiveProforma")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,cm.currency_code from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='0' and p.pro_forma_status ='0'";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            
            echo '<tr>
            <td>'.$srno.'</td>
            <td><a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
// if($Flag=="ShowTermsConditions"){
//     $cmd = "SELECT * FROM terms_conditions WHERE comp_id = '$comp_id' AND status = 1";
//     $result = $connect->query($cmd);
//     if($result->num_rows > 0) {
//         $i = 1;
//         while ($row = $result->fetch_assoc()) { 
//             echo '<div class="checkbox">
//                         <label>'.$i.'. &nbsp;<input type="checkbox" class="termcheckboxes" id="termchk_'.$i.'" value="'.$row['title'].'"> '.$row['title'].'</label>
//                         &emsp;&emsp;&emsp;<label id="termdiv_'.$i.'" style="display:show">'.$row['discription'].'</label> 
//                     </div>';
//                     $i++;
//         }
//     }

// }
// if($Flag=="ShowShipmentDocument"){
//     $cmd = "SELECT * FROM shipment_document WHERE status = 'Active'";
//     $result = $connect->query($cmd);
//     if($result->num_rows > 0) {
//         $i = 1;
//         while ($row = $result->fetch_assoc()) { 
//             echo '<div class="checkbox">
//                     <label>'.$i.'. &nbsp;<input type="checkbox" class="shipcheckboxes" value="'.$row['shipment_document_name'].'"> '.$row['shipment_document_name'].'</label>
//                   </div>';
//                   $i++;
//         }
//     }

// }


if($Flag=="SaveSalesDocuments")
{   
    $shipmentPINo = $_POST['shipmentPINo'];
    $document_names = $_POST['document_names'];
    $document_files = $_FILES['document_files'];

    $isuploaded = false;
    if(!empty($document_names))
    {
        mysqli_query($connect, "DELETE FROM proforma_sales_documents WHERE proforma_id='$shipmentPINo'");
        for ($i = 0; $i < count($document_names); $i++) {
            $docName = mysqli_real_escape_string($connect, $document_names[$i]);
            $fileName = $document_files['name'][$i];
            $tmpName = $document_files['tmp_name'][$i];

            if (!empty($fileName) && is_uploaded_file($tmpName)) {
                $uploadDir = '../ProformaSalesDocs/SalesDocument'.$shipmentPINo.'/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // create directory if it doesn't exist
                }
                $newFileName = uniqid() . '_' . basename($fileName);
                $targetPath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    mysqli_query($connect, "INSERT INTO proforma_sales_documents(proforma_id, document_name, document_path) 
                        VALUES('$shipmentPINo', '$docName', '$targetPath')");

                    mysqli_query($connect,"update pro_forma_head set Sales_document_status='1' where pi_no='$shipmentPINo' ");
                    $isuploaded = true;
                }
            }
        }
    }
    if($isuploaded==true)
    {
        echo "Inserted";
    }
    else{
        echo "Unable to upload shipment documents";
    }
}
if($Flag=="UpdateSalesDocument")
{
    $uploadedDocs = [];
    $rstdoc = mysqli_query($connect, "SELECT * FROM proforma_sales_documents WHERE proforma_id='$pi_no'");
    if (mysqli_num_rows($rstdoc) > 0) {
        while ($rwdoc = mysqli_fetch_assoc($rstdoc)) {
            $uploadedDocs[] = [
                'document_name' => $rwdoc['document_name'],
                'document_path' => $rwdoc['document_path']
            ];
        }
    }
    echo json_encode($uploadedDocs);
    exit;
}
if($Flag=="loadPIDocumentList")
{
    $pi_no = $_POST['pi_no'];
    echo "<option value=''>Select Document</option>";
    $rstdoc = mysqli_query($connect,"select * from pro_forma_head_shipment_detail where pi_no='$pi_no' ");
    while($rwdoc = mysqli_fetch_assoc($rstdoc))
    {
        $shipment_document_name = $rwdoc['shipment_document_name'];
        echo "<option value='$shipment_document_name'>$shipment_document_name</option>";
    }
}
if($Flag=="UpdateDocumentCheckStatus")
{
    $pi_no = $_POST['pi_no'];
    $rstpro = mysqli_query($connect,"update pro_forma_head set sales_approve_document_status ='1' where pi_no='$pi_no' ");
    if($rstpro)
    {
        echo "Updated";
    }
    else{
        echo "Unable to Update the status";
    }
}
if($Flag=="UpdateTelexStatus")
{
    $pi_no = $_POST['pi_no'];
    $rstpro = mysqli_query($connect,"update pro_forma_head set telex_release_status ='1' where pi_no='$pi_no' ");
    if($rstpro)
    {
        echo "Updated";
    }
    else{
        echo "Unable to Update the Telex Release Status";
    }
}
if($Flag=="SendForSalesChecking")
{
    $pi_no = $_POST['pi_no'];
    $rstpro = mysqli_query($connect,"update pro_forma_head set logistic_send_for_approval_status ='1' where pi_no='$pi_no' ");
    if($rstpro)
    {
        echo "Success";
    }
    else{
        echo "Unable to Send";
    }
}
if($Flag=="LoadCOAProductDetails")
{
    $data = array();
    $pi_no = $_POST['pi_no'];
    $IsSpecificationFlag = 0;
    $data['coaproductdetails'] = '<table class="table table-bordered" id="tblproductdetails">
                <thead>
                    <tr>
                    <td>Product Name</td>
                    <td>UOM</td>
                    <td>Rate/Ton</td>
                    <td>Total Weight</td>
                    
                    <td>Specification</td>
                    </tr>
                </thead>
                <tbody id="divpurchaseproducts">';
    
            $rstpro = mysqli_query($connect,"select p.*, pp.product_name,pp.status,u.u_name from pro_forma_head_details p 
            inner join product_master pp on pp.product_id= p.product_id 
            inner join uom_master u on u.u_id=pp.uom_id
            where p.pi_no='$pi_no'");
            if(mysqli_num_rows($rstpro)>0)
            {
                while($rwpro = mysqli_fetch_assoc($rstpro))
                {
                    extract($rwpro);
                    $rstproductdescription = mysqli_query($connect,"select * from product_description where product_id='$product_id' ");
                    
                    $data['coaproductdetails'] .= "<tr>
                            <td value='$product_id'>$product_name</td>
                            <td>$u_name</td>
                            <td>$rateperton</td>
                            <td>$total_weight</td><td>";
                        
                    $rstproductdescription = mysqli_query($connect,"select product_description_id,product_description,value,product_range from product_description where product_id='$product_id' ");
                    if(mysqli_num_rows($rstproductdescription)>0)
                    {
                        while($rwpro = mysqli_fetch_assoc($rstproductdescription))
                        {
                            $descId = $rwpro['product_description_id'];
                            $descText = $rwpro['product_description'];
                            $stdValue = $rwpro['value'];
                            $product_range = $rwpro['product_range'];
                            $checked ='';
                            // $rstprodesc = mysqli_query($connect,"select proforma_product_description_id from proforma_product_description where product_id='$product_id' and po_no='$pi_no' and description_id='$descId' ");
                            // if(mysqli_num_rows($rstprodesc)>0)
                            // {
                            //     $checked ="checked";
                            // }
                          $data['coaproductdetails'] .= "
                            <div class='form-check mb-3'>
                                <input class='form-check-input checkprodspecification'
                                    style='margin-top: 1px;'
                                    type='checkbox'
                                    id='desc_$descId'
                                    name='product_desc_".$product_id."[]'
                                    data-product-id='$product_id'
                                    data-std-value='$stdValue'
                                    value='$descId' $checked>
                                <label class='form-check-label' for='desc_$descId' >
                                    $descText
                                </label>

                                <div class='spec-inputs mt-2' id='spec_inputs_$descId' style='display: none;'>
                                    <input type='text' class='form-control mb-2' readonly placeholder='Standard Value' id='descSTDValue_$descId' value='$stdValue $product_range'>
                                    <input type='text' class='form-control' placeholder='Enter Result' id='descResultValue_$descId'>
                                </div>
                            </div>";
                            $IsSpecificationFlag++;
                        }
                    }
                    $data['coaproductdetails'] .= "</td></tr>";
                }
            }
                                    
        $data['coaproductdetails'] .= "</tbody>
        </table>";

        $data['IsSpecificationFlag'] = $IsSpecificationFlag;
        echo json_encode($data);
}
if ($Flag == "SaveCOAProductUpdate") {
    $tableDataNew = json_decode($_POST['tableData'], true); // decode JSON into array
    $pi_no_new = $_POST['COAProductPiNo'];
    if (is_array($tableDataNew)) {
        $hasError = false;
        $errorMessages = [];

        foreach ($tableDataNew as $contact) {
            $product_id = $contact['product_id'];
            $descriptions = $contact['descriptions'] ?? [];
            $selectedDescriptionsStdValue = $contact['selectedDescriptionsStdValue'] ?? [];
            $selectedDescriptionsValue = $contact['selectedDescriptionsValue'] ?? [];

            for ($i = 0; $i < count($descriptions); $i++) {
                $descId = $descriptions[$i];
                $stdValue = $selectedDescriptionsStdValue[$i] ?? '';
                $range = $selectedDescriptionsValue[$i] ?? '';
                $stdValueNew = explode(' ', $stdValue);
                $stdNumber = floatval($stdValueNew[0]);

                if ($range === "") {
                    $hasError = true;
                    $errorMessages[] = "❌ Error: Blank Result value not allowed";
                }
                // elseif ($range > $stdNumber) {
                //     $hasError = true;
                //     $errorMessages[] = "⚠️ Warning: Range value ($range) exceeds standard value ($stdNumber) .";
                // }
            }
        }

        if (!$hasError) {
            foreach ($tableDataNew as $contact) {
                $product_id = $contact['product_id'];
                $descriptions = $contact['descriptions'] ?? [];
                $selectedDescriptionsStdValue = $contact['selectedDescriptionsStdValue'] ?? [];
                $selectedDescriptionsValue = $contact['selectedDescriptionsValue'] ?? [];

                for ($i = 0; $i < count($descriptions); $i++) {
                    $descId = $descriptions[$i];
                    $stdValue = $selectedDescriptionsStdValue[$i] ?? '';
                    $range = $selectedDescriptionsValue[$i] ?? '';
                    $stdValueNew = explode(' ', $stdValue);

                    $rstcheckquery = mysqli_query($connect,"select proforma_product_description_id from proforma_product_description
                    where description_id='$descId' and po_no='$pi_no_new' and product_id='$product_id'");
                    if(mysqli_num_rows($rstcheckquery)==0)
                    {
                        $query = "
                            INSERT INTO `proforma_product_description`
                            (`description_id`, `po_no`, `product_id`, percentage, product_range) 
                            VALUES ('$descId', '$pi_no_new', '$product_id', '$stdValueNew[0]', '$range')
                        ";
                    }else{
                        $rwcheckquery = mysqli_fetch_assoc($rstcheckquery);
                        $proforma_product_description_id = $rwcheckquery['proforma_product_description_id'];
                        $query = "
                            update `proforma_product_description`
                            set percentage='$stdValueNew[0]', product_range='$range'
                            where proforma_product_description_id='$proforma_product_description_id'
                        ";
                    }
                    $rstdesc = mysqli_query($connect, $query);
                }
            }
            echo "Updated";
            $date = date('Y-m-d');
            $rstproupdate = mysqli_query($connect,"update pro_forma_head set COAPrintStatus='1',COAPrintDate='$date' where pi_no='$pi_no_new' ");
        } else {
            foreach ($errorMessages as $msg) {
                echo $msg ;
            }
        }

    }
}

if($Flag=="SaveProFormaOnlyProductUpdate")
{
    $isAllValid = true;
    $errorMessage = '';
    $tableDataNew = [];

    if (isset($_POST['tableData'])) {
        $tableDataNew = json_decode($_POST['tableData'], true);
        foreach ($tableDataNew as $contact) {
            $product_id = $contact['product_id'];
            $quantity = $contact['quantity'];
            $totalweight = $contact['totalweight'];
            $weightperpack = $contact['weightperpack'];
            $ProductName = $contact['ProductName'];

            // Fetch proforma record
            $rstproforma = mysqli_query($connect, "SELECT each_bag_weight, no_of_bags, total_weight, rate, rateperton, po_used_qty 
                FROM pro_forma_head_details 
                WHERE pi_no='$PI_NoNew' AND product_id='$product_id'");

            if (mysqli_num_rows($rstproforma) > 0) {
                $rwproforma = mysqli_fetch_assoc($rstproforma);
                extract($rwproforma);

               
                $PoRemaing   = $no_of_bags - $po_used_qty;
                if($PoRemaing==0 || $PoRemaing=="0")
                {
                    $PoRemaing = $po_used_qty;
                }
                $PoRemaingTotaWirght = $PoRemaing * $each_bag_weight;
                $WeigthPerTon = $totalweight/$each_bag_weight;
                if($WeigthPerTon < $PoRemaing)
                {
                    $isAllValid = false;
                    $errorMessage = "You have already used the total weight: " . $PoRemaingTotaWirght . " MT for Product : $ProductName.";
                    break; // Stop at the first invalid entry
                }
            }
        }
        if ($isAllValid) {
            foreach ($tableDataNew as $contact) {
                $product_id = $contact['product_id'];
                $quantity = $contact['quantity'];
                $rate = $contact['rate'];
                $totalamt = $contact['totalamt'];
                $gst_amount = $contact['gst_amount'];
                $totalweight = $contact['totalweight'];
                $rateperton = $contact['rateperton'];

                $query = "UPDATE `pro_forma_head_details` SET 
                            total_weight='$totalweight', 
                            rate='$rate',
                            total_amt='$totalamt',
                            gst='$gst_amount',
                            rateperton='$rateperton',
                            no_of_bags='$quantity'
                        WHERE product_id='$product_id' AND pi_no='$PI_NoNew'";

                $query_res2 = $connect->query($query);
                if (!$query_res2) {
                    $isAllValid = false;
                    $errorMessage = "Failed to update Product ID: $product_id";
                    break;
                }
            }
        }

        if ($isAllValid) {
            $temp['Status'] = "Success";
            $temp['Message'] = "All products updated successfully.";
        } else {
            $temp['Status'] = "Fail";
            $temp['Message'] = $errorMessage;
        }

        echo json_encode($temp);
    }

}
if($Flag=="LoadProductQtyUsedCalculationDetails")
{
    $temp = array();
    $rstproforma = mysqli_query($connect,"select each_bag_weight,no_of_bags,total_weight,rate,rateperton,po_used_qty from pro_forma_head_details
    where pi_no='$PINo' and product_id='$ProductId' ");
    if(mysqli_num_rows($rstproforma)>0)
    {
        $rwproforma = mysqli_fetch_assoc($rstproforma);
        extract($rwproforma);
        $PoRemaing   = $no_of_bags - $po_used_qty;
        if($PoRemaing==0 || $PoRemaing=="0")
        {
            $PoRemaing = $po_used_qty;
        }
        $PoRemaingTotaWirght = $PoRemaing * $each_bag_weight;
        $WeigthPerTon = $TotalWeight/$each_bag_weight;
        if($WeigthPerTon < $PoRemaing)
        {
            $temp['Status'] = "Fail";
            $temp['Message'] =  "You have already used the total weight: " .$PoRemaingTotaWirght." MT.";
        }
        else{
            $temp['Status'] = "Success";
        }
    }
    echo json_encode($temp);
}
if($Flag=="GenerateTaxInvoice")
{
    $pi_no = $_POST['pi_no'];
    $bl_number = $_POST['bl_number'];
    $vessel_name = $_POST['vessel_name'];
    $rstpi = mysqli_query($connect,"select grand_total from pro_forma_head where pi_no='$pi_no' ");
    if(mysqli_num_rows($rstpi)>0)
    {
        $rwpi = mysqli_fetch_assoc($rstpi);
        $grand_total = $rwpi['grand_total'];

        $cmd_1 = "SELECT invoice_number FROM tax_invoice_head 
                WHERE year_id = '$financial_year' AND comp_id = '$comp_id' 
                ORDER BY tax_invoice_id DESC LIMIT 1";
        $result_1 = $connect->query($cmd_1);
        $invoice_number = 1;

        if ($result_1->num_rows > 0) {
            if ($row_1 = $result_1->fetch_assoc()) {
                $piArr = explode("/", $row_1['invoice_number']); 
                $prefixPart = $piArr[2]; 
                $runningNo = substr($prefixPart, 2); // Extract number part after year (e.g., "2501" → "01")
                $invoice_number = intval($runningNo) + 1;
            }
        }

        $cmd_year = "SELECT year_name FROM financial_year WHERE year_id = '$financial_year' LIMIT 1";
        $res_year = $connect->query($cmd_year);
        $year_part = "00"; // fallback
        if ($res_year->num_rows > 0) {
            $row_year = $res_year->fetch_assoc();
            $fyParts = explode("-", $row_year['year_name']);
            if (count($fyParts) === 2) {
                $year_part = substr($fyParts[0], -2);
            }
        }
        $inv_number_padded = str_pad($invoice_number, 2, "0", STR_PAD_LEFT);
        $invoice_number = "ED/EXP/{$year_part}{$inv_number_padded}";

        $rsttaxinvoice = mysqli_query($connect,"INSERT INTO `tax_invoice_head`(`pi_no`, `invoice_number`, 
        `invoice_date`, `invoice_amt`, `generated_by`, `generated_on`,year_id,comp_id,bl_no,vessal_name) 
        VALUES('$pi_no','$invoice_number',CURDATE(), '$grand_total','$user_id_session',NOW(),'$financial_year',
        '$comp_id','$bl_number','$vessel_name' )");

        if($rsttaxinvoice)
        {
            $date = date('Y-m-d');
            $rstproupdate = mysqli_query($connect,"update pro_forma_head set TaxInvoicePrintStatus='1',TaxInvoicePrintDate='$date' where pi_no='$pi_no' ");
            echo "Success";
        }else{
            echo "Unable to generate tax Invoice";
        }

    }
}
?>