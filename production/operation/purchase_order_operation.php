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
extract($_POST);

if($Flag=="SaveProductDetails")
{
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
            <td>$quantity</td>
            <td class='tdtotalamount'>$totakamountwithgst</td>
            <td style='display:none;'>$prostatus</td>
            <td style='display:none;'>$weightperpack</td>
            <td><button class='btn btn-info btn-sm btneditproduct' 
              onclick='EditProductDetails(
                            " . json_encode($product_id) . ", 
                            " . json_encode($product_name) . ", 
                            " . json_decode($packagingType) . ",
                            " . json_encode($weightperpack) . ", 
                            " . json_encode($quantity) . ", 
                            " . json_encode($rateperton) . ", 
                            " . json_encode($totakamountwithgst) . "
                ) '><i class='fa fa-pencil'></i></button>
            <button class='btn btn-danger btn-sm btnremoveproduct' id='removetrclick$product_id'><i class='fa fa-close'></i></button></td>
        </tr>";
}
if($Flag=="LoadProFormaProducts")
{
    if(isset($pi_no))
    {
        $productData = [];  // Array to store the aggregated data for each product.
        foreach($pi_no as $PINo)
        {
            $rstpro = mysqli_query($connect,"SELECT p.*,pp.uom_id, pp.product_name, u.u_name as uom, pp.status as prostatus 
                FROM pro_forma_head_details p 
                INNER JOIN product_master pp ON p.product_id = pp.product_id 
                LEFT JOIN uom_master u ON pp.uom_id = u.u_id 
                WHERE p.pi_no='$PINo'
                GROUP BY p.product_id");

            if(mysqli_num_rows($rstpro) > 0)
            {
                while($rwpro = mysqli_fetch_assoc($rstpro))
                {
                    $product_id = $rwpro['product_id'];
                    if(isset($productData[$product_id])) {

                        $no_of_bags_new = $rwpro['no_of_bags'] - $rwpro['po_used_qty'];
                        
                        $productData[$product_id]['total_weight'] += $rwpro['total_weight'];
                        // $productData[$product_id]['no_of_bags'] += $rwpro['no_of_bags'];
                        $productData[$product_id]['no_of_bags_new'] += $no_of_bags_new;
                        $productData[$product_id]['rate'] = 0;
                        $productData[$product_id]['total_amt'] = 0;
                    } else {
                        $productData[$product_id] = $rwpro;
                        $productData[$product_id]['no_of_bags_new'] = $rwpro['no_of_bags'] - $rwpro['po_used_qty'];
                        // $productData[$product_id]['total_weight'] = 0;
                        $productData[$product_id]['rate'] = 0;
                        $productData[$product_id]['total_amt'] = 0;
                    }
                }
            }
        }

        foreach($productData as $product) {
            echo "<tr>
                <td value='{$product['product_id']}'>{$product['product_name']}</td>
                <td>{$product['uom']}</td>
                <td>{$product['rateperton']}</td>
                <td>{$product['total_weight']}</td>
                <td>{$product['rate']}</td>
                <td style='display:none;'>0</td>
                <td>{$product['no_of_bags_new']}</td>
                <td class='tdtotalamount'>{$product['total_amt']}</td>
                <td style='display:none;'>{$product['prostatus']}</td>
                <td style='display:none;'>{$product['each_bag_weight']}</td>
                <td><button class='btn btn-info btn-sm btneditproduct' 
                        onclick='EditProductDetails(
                            " . json_encode($product['product_id']) . ", 
                            " . json_encode($product['product_name']) . ", 
                            " . json_decode($product['packaging_id']) . ",
                            " . json_encode($product['each_bag_weight']) . ", 
                            " . json_encode($product['no_of_bags_new']) . ", 
                            " . json_encode(0) . ", 
                            " . json_encode(0) . "
                        )'>
                        <i class='fa fa-pencil'></i>
                    </button>
                    <button class='btn btn-danger btn-sm btnremoveproduct' id='removetrclick{$product['product_id']}'>
                        <i class='fa fa-close'></i>
                    </button>
                </td>
            </tr>";
            echo "<script>$('#product_id option[value=\"{$product['product_id']}\"]').remove();</script>";
        }
    }
}

if($Flag=="SavePurchaseOrder")
{
    if($hiddenPOID=="")
    {
        $i=0;
        $j=0;
        $error_product_name = "";$purchase_order_status = 0;
        if($user_type_id == 1){
        	$purchase_order_status = 1;
        }

        $po_custom_number = 0;
        $destination_port_name = $port_of_loading_name = "";
        $cmd_1 = "SELECT po_custom_number FROM purchase_order WHERE year_id = '$financial_year' AND comp_id = '$comp_id' ORDER BY po_id DESC LIMIT 1 ";
        $result_1 = $connect->query($cmd_1);
        if ($result_1->num_rows > 0)
        {
            if($row_1 = $result_1->fetch_assoc())
            {
                $last_po_number = $row_1['po_custom_number'];
                $prefix = preg_replace('/\d+$/', '', $last_po_number); // "PONO"
                $number = preg_replace('/\D/', '', $last_po_number);   // "0051"
                $new_number = str_pad((int)$number + 1, strlen($number), '0', STR_PAD_LEFT); // "0052"
                $po_custom_number = $prefix . $new_number;
            }
        }
        else
        {
            $po_custom_number="PONO0001";
        }
        // echo $po_custom_number;
        // exit;
        $sql = "INSERT INTO `purchase_order`(po_custom_number, supplier_id, supplier_pi_no, supplier_pi_date, 
        po_date,valid_upto, currency_id, state_id,country_id,total_amt,discount,grand_total,remark,user_id,comp_id, branch_id,purchase_order_status,po_type,year_id) 
        VALUES('$po_custom_number','$account_id','$supplier_pi_no','$supplier_pi_date','$po_date','$valid_upto','$currency_id','$state_id','$country_id','$totalAmtAll',$DiscountAmt,'$grand_total','$remark','$user_id_session', '$comp_id', '$branch_id','$purchase_order_status','$hiddenPOType','$financial_year') ";
        $query_res1 = $connect->query($sql);
        $po_no_new = mysqli_insert_id($connect);


        if($query_res1 > 0)
        {
            $todate = date('Y-m-d');
            if(isset($_POST['tableData'])){
                $tableDataNew = json_decode($_POST['tableData'], true);
                foreach ($tableDataNew as $contact) {
                    $product_id = $contact['product_id'];
                    $update_qty = $contact['quantity'];
                    $rate = $contact['rate'];
                    $totalamt = $contact['totalamt'];
                    $gst_amount = $contact['gst_amount'];
                    $totalweight = $contact['totalweight'];
                    $rateperton = $contact['rateperton'];
                    $weightperpack = $contact['weightperpack'];
                    $quantity = $update_qty;
                    $pi_no_arr = [];
                    if (isset($ProformaId)) {
                        $total_exist_qty = 0;  
                        $records = [];

                        foreach ($ProformaId as $PINo) {
                            $rstpi = mysqli_query($connect, "
                                SELECT pro_forma_head_details_id, no_of_bags 
                                FROM pro_forma_head_details
                                WHERE pi_no = '$PINo' AND product_id = '$product_id'
                            ");
            
                            while ($row = mysqli_fetch_assoc($rstpi)) {
                                $records[] = $row; 
                                $total_exist_qty += $row['no_of_bags'];
                            }
                            $pi_no_arr[] = $PINo;
                        }
                        if ($update_qty > $total_exist_qty) {
                            echo "⚠️ New qty ($update_qty) exceeds the total existing qty ($total_exist_qty) for product_id $product_id. Update aborted.<br>";
                            continue;  // Skip this product_id
                        }
                        $remaining_qty = $update_qty;
                        foreach ($records as $record) {
                            $pro_forma_head_details_id = $record['pro_forma_head_details_id'];
                            $exist_qty = $record['no_of_bags'];
                            if ($remaining_qty >= $exist_qty) {
                                $po_used_qty = $exist_qty;
                                $po_total_qty = $update_qty;
                                $remaining_qty -= $exist_qty;
                            } else {
                                $po_used_qty = $exist_qty - $remaining_qty;
                                $po_total_qty = $update_qty;
                                $remaining_qty = 0;
                            }
                            mysqli_query($connect, "
                                UPDATE pro_forma_head_details 
                                SET po_total_qty = '$po_total_qty', 
                                    po_used_qty = '$po_used_qty' 
                                WHERE pro_forma_head_details_id = '$pro_forma_head_details_id'
                            ");
                            if ($remaining_qty <= 0) {
                                break;  // Stop when no remaining qty
                            }
                        }
                    }
                    $pi_nos = implode(",",$pi_no_arr);
                    $query =" INSERT INTO `purchase_order_details`(purchase_order_id,
                    product_id, each_bag_weight, no_of_bags, total_weight, rate,rateperton,packaging_type,
                    gst,total_amt,packaging_id,pi_no) 
                    VALUES('$po_no_new','$product_id','$weightperpack','$quantity','$totalweight','$rate',
                    '$rateperton', '$packagingTypeName','$gst_amount','$totalamt','$packaging_id','$pi_nos')";
                    $query_res2 = $connect->query($query);       
                    $i++;
                }
            }

            if(isset($_POST['termData'])){
                $termDataNew = json_decode($_POST['termData'], true);
                foreach ($termDataNew as $termrecord) {
                    $payquery = "INSERT INTO `purchase_order_termscondition_detail`(`purchase_order_id`, `term_id`, `title`, `discription`) VALUES('$po_no_new','$termrecord[0]','$termrecord[1]','$termrecord[2]')";
                    $query_result2 = $connect->query($payquery);     
                    
                }
            }
            if(isset($_POST['shipData'])){
                $shipDataNew = json_decode($_POST['shipData'], true);
                foreach ($shipDataNew as $record) {
                    $shipquery1 =" INSERT INTO `purchase_order_shipment_detail`(po_no,
                    shipment_document_name,shipment_document_id) 
                    VALUES('$po_no_new','$record[0]','$record[1]')";
                    $query_result1 = $connect->query($shipquery1);       
                }
            }

            if(@$query_res2 > 0)
            {
                foreach($payment_per as $pay1) 
                {
                    if($payment_desc[$j]=="Before"){ $payment_desc[$j]=0; }
                    if($after_payment_desc[$j]=="After"){ $after_payment_desc[$j]=0; }
                    $query1 ="INSERT INTO `purchase_order_payment`(purchase_order_id,pay_percentage,pay_in_advance,payment_desc_id, after_percentage,pay_later,after_payment_desc_id,payment_mode_id, time_period) VALUES ('$po_no_new','$pay1','$pay_in_advance[$j]','$payment_desc[$j]','$after_payment_per[$j]','$pay_later[$j]','$after_payment_desc[$j]','$payment_mode[$j]','$period[$j]')";
                    $query_res3 = $connect->query($query1);
                    $j++;
                }
                if($query_res3 > 0 && $error_product_name == "")
                {
                    mysqli_commit($connect);
                
                    echo 1;
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
        $pi_nos_arr = explode(",",$hiddenPOID);
        $sql = "UPDATE `purchase_order` SET supplier_id = '$account_id', supplier_pi_no = '$supplier_pi_no', supplier_pi_date = '$supplier_pi_date',po_date = '$po_date',valid_upto = '$valid_upto', currency_id = '$currency_id', state_id = '$state_id',country_id = '$country_id',total_amt = '$totalAmtAll',discount = '$DiscountAmt',grand_total = '$grand_total',remark = '$remark' WHERE po_id = '$hiddenPOID'";
        $query_res1 = $connect->query($sql);
        if($query_res1 > 0)
        {
            $todate = date('Y-m-d');
            if(isset($_POST['tableData'])){
                $rstdel = mysqli_query($connect,"delete from purchase_order_details where purchase_order_id = '$hiddenPOID'");
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
                    $update_qty = $quantity;
                    if (isset($ProformaId)) {
                        $total_exist_qty = 0;  
                        $records = [];

                        foreach ($ProformaId as $PINo) {
                            $rstpi = mysqli_query($connect, "
                                SELECT pro_forma_head_details_id, no_of_bags 
                                FROM pro_forma_head_details
                                WHERE pi_no = '$PINo' AND product_id = '$product_id'
                            ");
            
                            while ($row = mysqli_fetch_assoc($rstpi)) {
                                $records[] = $row; 
                                $total_exist_qty += $row['no_of_bags'];
                            }
                            if(in_array($PINo,$pi_nos_arr)){}
                            else{
                                $pi_nos_arr[] = $PINo;
                            }
                            
                        }
                        if ($update_qty > $total_exist_qty) {
                            echo "⚠️ New qty ($update_qty) exceeds the total existing qty ($total_exist_qty) for product_id $product_id. Update aborted.<br>";
                            continue;  // Skip this product_id
                        }
                        $remaining_qty = $update_qty;
                        foreach ($records as $record) {
                            $pro_forma_head_details_id = $record['pro_forma_head_details_id'];
                            $exist_qty = $record['no_of_bags'];
                            if ($remaining_qty >= $exist_qty) {
                                $po_used_qty = $exist_qty;
                                $po_total_qty = $update_qty;
                                $remaining_qty -= $exist_qty;
                            } else {
                                $po_used_qty = $exist_qty - $remaining_qty;
                                $po_total_qty = $update_qty;
                                $remaining_qty = 0;
                            }
                            mysqli_query($connect, "
                                UPDATE pro_forma_head_details 
                                SET po_total_qty = '$po_total_qty', 
                                    po_used_qty = '$po_used_qty' 
                                WHERE pro_forma_head_details_id = '$pro_forma_head_details_id'
                            ");
                            if ($remaining_qty <= 0) {
                                break;  // Stop when no remaining qty
                            }
                        }
                    }
                    $pi_nos = implode(",",$pi_nos_arr);
                    $query =" INSERT INTO `purchase_order_details`(purchase_order_id,
                    product_id, each_bag_weight, no_of_bags, total_weight, rate,rateperton,packaging_type,
                    gst,total_amt,packaging_id,pi_no) VALUES('$hiddenPOID','$product_id','$weightperpack','$quantity','$totalweight','$rate','$rateperton', '$packagingTypeName','$gst_amount','$totalamt','$packaging_id','$pi_nos')";
                    $query_res2 = $connect->query($query);       
                    $i++;
                }

            }

            if(isset($_POST['termData'])){
                $rstdel = mysqli_query($connect,"delete from purchase_order_termscondition_detail where purchase_order_id = '$hiddenPOID'");
                $termDataNew = json_decode($_POST['termData'], true);
                foreach ($termDataNew as $termrecord) {
                    $payquery = "INSERT INTO `purchase_order_termscondition_detail`(`purchase_order_id`, `term_id`, `title`, `discription`) VALUES('$hiddenPOID','$termrecord[0]','$termrecord[1]','$termrecord[2]')";
                    $query_res2 = $connect->query($payquery);     
                    
                }
            }
            if(isset($_POST['shipData'])){
                $rstdel = mysqli_query($connect,"delete from purchase_order_shipment_detail where po_no = '$hiddenPOID'");
                $shipDataNew = json_decode($_POST['shipData'], true);
                foreach ($shipDataNew as $record) {
                    $shipquery1 =" INSERT INTO `purchase_order_shipment_detail`(po_no,
                    shipment_document_name,shipment_document_id) 
                    VALUES('$hiddenPOID','$record[0]','$record[1]')";
                    $query_result1 = $connect->query($shipquery1);       
                }
            }
            if(@$query_res2 > 0)
            {
                foreach($payment_per as $pay1) 
                {
                    if($payment_desc[$j]=="Before"){ $payment_desc[$j]=0; }
                    if($after_payment_desc[$j]=="After"){ $after_payment_desc[$j]=0; }
                    $query1 = "UPDATE `purchase_order_payment` SET pay_percentage = '$pay1',pay_in_advance = '$pay_in_advance[$j]',payment_desc_id = '$payment_desc[$j]', after_percentage = '$after_payment_per[$j]',pay_later = '$pay_later[$j]',after_payment_desc_id = '$after_payment_desc[$j]',payment_mode_id = '$payment_mode[$j]', time_period = '$period[$j]' WHERE purchase_order_id = '$hiddenPOID'";

                    $query_res3 = $connect->query($query1);
                    $j++;
                }
                if($query_res3 > 0 && $error_product_name == "")
                {
                    mysqli_commit($connect);
                
                    echo 2;
                }
                else {   $error_count = 1; }
            }
            else {    $error_count = 1; }

        }
        else{
            $error_count = 1;
        }
    }
    
    if(@$error_count == 1)
    {
        echo "Connection Problem!!! Please try again..";
    }

}

if($Flag=="ShowPurchaseOrderList")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>';
                
                if($user_type_id==1){
                    echo '<th>Cancel PO</th>
                          <th>Link to PI</th>';
                }
                  
                   echo '<th>PO No</th>
                    <th>PO Date</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Grand Total</th>';
                    if($user_type_id == 1){
		            	echo '<th>User Name</th>';
		            }
        echo   '</tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,c.client_mob, u.user_name,cm.currency_code,pp.po_no from purchase_order p 
        left join purchase_order_receipt_payment pp on pp.po_no = p.po_id 
        inner join client_master c on c.client_id = p.supplier_id
        inner join country_master cm on cm.id=p.currency_id 
        inner join user_master u on u.user_id = p.user_id
        where p.active_status='1' and p.po_cancel_status !=1 ";
        if($user_type_id=="1")
        {
            $query.=" AND p.branch_id='$branch_id' AND p.purchase_order_status IN ('-1','1')";

        }else{
        	$query.=" AND p.user_id='$user_id_session' AND p.branch_id='$branch_id' AND p.purchase_order_status IN ('-1','1','0','2')";
        }
        
        
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            $SendMailAndWhatsappbtn='';
            $POCancelbtn ='';
            if($user_type_id=="1")
            {
                if($purchase_order_status=="1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                    if($po_type == "Direct PO")
                    {
                        $proFormabutton .= "<a href='purchase_entry.php?PO_ID=".base64_encode($po_id)."'  class='btn btn-warning btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                    }else if($po_type == "Using ProForma"){
                        $proFormabutton .= "<a href='purchase_direct_po.php?PO_ID=".base64_encode($po_id)."' class='btn btn-warning btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                    }
                    
                     $SendMailAndWhatsappbtn = "<button class='btn btn-sm btn-round btn-info' onclick='sendpurchasemail($po_id, $supplier_id, \"".$po_custom_number."\",\"".$po_date."\")'><i class='fa fa-envelope'></i></button>
                    <button class='btn btn-sm btn-round btn-success' onclick='sendpurchaseWhatsApp(\"".$client_name."\", \"".$client_mob."\", \"".$po_custom_number."\",\"".$po_date."\")'><i class='fa fa-whatsapp'></i></button>";
                }
                else if($purchase_order_status=="-1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Pending</button>";
                }

                
                if($po_refund_status=="1")
                {
                    $POCancelbtn='<button class="btn btn-sm btn-round btn-success">Refunded</button>';
                }else if($po_cancel_status=="1")
                {
                    $POCancelbtn='<button class="btn btn-sm btn-round btn-primary" onclick="RefundPO('.$po_id.', \''.$po_custom_number.'\');">Refund</button>';
                }else if($po_no==$po_id){
                    $POCancelbtn='<button class="btn btn-sm btn-round btn-danger" onclick="CancelPO('.$po_id.');">Cancel PO</button>';
                }
            }
            else
            {
                if($purchase_order_status=="1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                     $SendMailAndWhatsappbtn = "<button class='btn btn-sm btn-round btn-info' onclick='sendpurchasemail($po_id, $supplier_id, \"".$po_custom_number."\",\"".$po_date."\")'><i class='fa fa-envelope'></i></button>
                    <button class='btn btn-sm btn-round btn-success' onclick='sendpurchaseWhatsApp(\"".$client_name."\", \"".$client_mob."\", \"".$po_custom_number."\",\"".$po_date."\")'><i class='fa fa-whatsapp'></i></button>";
                    
                }
                else if($purchase_order_status=="0")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-info' onclick='SendForApproval($po_id)'>Send For Approval</button><a href='purchase_direct_po.php?PO_ID=".base64_encode($po_id)."'  class='btn btn-warning btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
                }
                else if($purchase_order_status=="-1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-secondary'>Waiting For Approval</button>";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-danger'>Rejected</button>";
                }
            }

            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$proFormabutton.'<a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-secondary"><i class="fa fa-print"></i></a>'.$SendMailAndWhatsappbtn.'</td>';

            if($user_type_id==1)
            {
                echo '<td style="text-align:center">'.$POCancelbtn.'</td>';
                if($po_type == "Direct PO")
                {
                    if($LinkToPI_Status == 1){
                        echo "<td style='text-align:center'><a href='javascript:void(0);' class='btn btn-success btn-round btn-sm' title='PI Linked'><i class='fa fa-check' style='color:white'></i></a></td>";
                    }else{
                        echo "<td style='text-align:center'><a href='javascript:void(0);' onclick='openLinkToPIModal($po_id)'class='btn btn-info btn-round btn-sm'><i class='fa fa-link' style='color:white'></i></a></td>";
                    }
                }else{
                    echo '<td></td>';
                }
            }
            
            echo '<td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>';
            if($user_type_id == 1){
            	echo '<td>'.$user_name.'</td>';
            }

            echo '</tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="showPoDetailsForShipment")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PO No</th>
                    <th>PO Date</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Container Allocation</th>
                  </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,u.user_name from purchase_order p 
        inner join client_master c on c.client_id = p.supplier_id
        inner join user_master u on u.user_id = p.user_id
        where p.active_status='1' AND p.branch_id='$branch_id' and p.purchase_order_status='1' ";
        
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            if($shipment_document_status=="1")
            {
                $shipdocbtn = '<button data-toggle="tooltip" title="Edit Shipment Documents" class="btn btn-sm btn-round btn-warning" onclick="UpdateShipmentDoc('.$po_id.',\''.$client_name.'\');"><i class="fa fa-pencil"></i></button>';
                $AddShipmentDetails = '<a href="Add-Shipment-Details.php?po_id='.base64_encode($po_id).'"><button class="btn btn-sm btn-round btn-primary" data-toggle="tooltip" title="Add Shipment Details"><i class="fa fa-plus"></i></button></a>';
            }
            else{
                $shipdocbtn = '<button class="btn btn-sm btn-round btn-success" data-toggle="tooltip" title="Upload Shipment Documents" onclick="LoadDocumentModule('.$po_id.',\''.$client_name.'\');"><i class="fa fa-plus"></i></button>';
                $AddShipmentDetails ='';
            }

            $containerallocationbtn ="";
            if($shipment_status=="1")
            {
                $AddShipmentDetails ='<a href="print_po_shipment.php?po_no='.base64_encode($po_id).'" target="_blank" class="btn btn-round btn-sm btn-secondary"><i class="fa fa-print"></i> Shipment Added</a>';
                $containerallocationbtn = "<a href='container_allocation.php?po_id=".base64_encode($po_id)."'><button class='btn btn-sm btn-round btn-info'><i class='fa fa-plus'></i> Allocate Container</button></a>";
            }

            $LinkPIBtn ='';
            if($container_allocation_status=="1")
            {
                $containerallocationbtn = "<button class='btn btn-sm btn-round btn-secondary'> Allocated</button>";

                $rstpilink = mysqli_query($connect,"select purchase_shipment_detail_id from purchase_shipment_details where purchase_id='$po_id' and linking_status='0' and pro_forma_no='0' ");
                if(mysqli_num_rows($rstpilink)==0)
                {
                    $LinkPIBtn = "<button class='btn btn-sm btn-round btn-info' type='button'>PI Linked</button>";
                }
                else{
                    $LinkPIBtn = "<a href='#' class='btn btn-sm btn-round btn-primary' onclick='LoadPILInkModule($po_id,\"".$client_name."\");'> Link PI</a>";
                }
            }

            echo '<tr>
            <td>'.$srno.'</td>
            <td><a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>'.$shipdocbtn.''.$AddShipmentDetails.'</td>';
            
            echo '<td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$containerallocationbtn.' '.$LinkPIBtn.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowPurchaseOrderPaymentList")
{
    echo ' <table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PO No</th>
                    <th>PO Date</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Grand Total</th>';
                    if($user_type_id == 1){
		            	echo '<th>User Name</th>';
		            }
        echo   '</tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,u.user_name,cm.currency_code from purchase_order p 
        inner join client_master c on c.client_id = p.supplier_id
        inner join country_master cm on cm.id=p.currency_id 
        inner join user_master u on u.user_id = p.user_id
        where p.active_status='1' and p.po_cancel_status!='1' ";
        if($user_type_id=="1")
        {
            $query.=" AND p.branch_id='$branch_id' AND p.purchase_order_status = '1'";

        }else{
        	$query.=" AND p.user_id='$user_id_session' AND p.branch_id='$branch_id' AND p.purchase_order_status = '1' ";
        }
        
        
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            $rstpaidsum = mysqli_query($connect,"select SUM(paid_amount) as PreviousPaidAmount from purchase_order_receipt_payment where po_no='$po_id' AND approval_status <> 0");
            $rwpaidsum = mysqli_fetch_assoc($rstpaidsum);
            $PreviousPaidAmount = $rwpaidsum['PreviousPaidAmount']??0;
            $CurrentRemainingAmt = $grand_total-$PreviousPaidAmount;
            if($CurrentRemainingAmt <= 0 ){
                $btnpayment ='<a href="#" class="btn btn-sm btn-round btn-success">Received</a>&nbsp;<a href="print_purchase_payment.php?po_no='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-secondary"><i class="fa fa-print"></i></a>';
            }else{
                $btnpayment ='<a href="#" class="btn btn-sm btn-round btn-info" onclick="ShowPurchaseOrderPaymentModal('.$po_id.')"><i class="fa fa-plus"></i></a>&nbsp;<a href="print_purchase_payment.php?po_no='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-secondary"><i class="fa fa-print"></i></a>';
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$btnpayment.'</td>
            <td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>';
            if($user_type_id == 1){
            	echo '<td>'.$user_name.'</td>';
            }

            echo '</tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}

if($Flag=="ShowPurchaseOrderPaymentModal")
{
    $data = array();
    $rstpurchase = mysqli_query($connect,"select p.po_custom_number,p.supplier_pi_date,p.grand_total,c.client_name,
            u.user_name,pp.* from purchase_order p inner join client_master c on c.client_id = p.supplier_id
            left join purchase_order_payment pp on pp.purchase_order_id = p.po_id inner join user_master u on u.user_id = p.user_id where p.po_id='$po_id'");
    $rwpurchase = mysqli_fetch_assoc($rstpurchase);
    extract($rwpurchase);
    $data['po_custom_number'] = $po_custom_number;
    $data['grand_total'] = $grand_total;
    $data['po_id'] = $po_id;

    $rstpaidsum = mysqli_query($connect,"select SUM(paid_amount) as PreviousPaidAmount from purchase_order_receipt_payment where po_no='$po_id' AND approval_status <> 0");
    $rwpaidsum = mysqli_fetch_assoc($rstpaidsum);
    $PreviousPaidAmount = $rwpaidsum['PreviousPaidAmount']??0;
    $data['CurrentRemainingAmt'] = $rwpurchase['grand_total']-$PreviousPaidAmount;
    $data['basicpodetails'] =   '<div class="row">
                            <div class="col-md-4 mb-1 mt-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-warning mb-1">PO No:</h6>
                                    <p class="fw-bold">'.$po_custom_number.'</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1 mt-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-danger mb-1">PO Date:</h6>
                                    <p class="fw-bold">'.date("d M Y",strtotime($supplier_pi_date)).'</p>
                                </div>
                            </div>
                    
                            <div class="col-md-4 mb-1 mt-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Total Amount:</h6>
                                    <p class="fw-bold">₹'.number_format($grand_total).'</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1 mt-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-secondary mb-1">Advance Payment:</h6>
                                    <p class="fw-bold">₹'.number_format(@$pay_in_advance) .'('. @$pay_percentage.'%)</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-1 mt-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Next Payment</h6>
                                    <p class="fw-bold">₹'.number_format(@$pay_later) .'('. @$after_percentage.'%)</p>
                                </div>
                            </div>
                        </div>';

    echo json_encode($data);
}

if($Flag=="PurchasePreviousPaidPaymentDetails")
{
    $po_id = $_POST['po_id'];
    $rstpaidamt = mysqli_query($connect,"select * from purchase_order_receipt_payment p 
    where p.po_no='$po_id'");
    if(mysqli_num_rows($rstpaidamt)>0)
    {
        while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
        {
            extract($rwpaidamt);
            echo "<tr>
                <td>".date('d M Y',strtotime($paid_date))."</td>
                <td>₹ ".number_format($paid_amount)."</td>";
            if($approval_status=="1")
            {
                echo '<td><button type="button" class="btn btn-sm btn-round btn-success">Approved</button></td>';
            }
            else if($approval_status=="0")
            {
                echo '<td><button type="button" class="btn btn-sm btn-round btn-danger">Rejected</button></td>';
            }else{
                echo '<td><button type="button" class="btn btn-sm btn-round btn-warning">Pending</button></td>';
            }
            if($TransactionProof!="")
            {
                echo "<td><a href='production/$TransactionProof' target='_blank' class='text-primary'><i class='fas fa-file-alt'></i> View</a></td>";
            }
            else{echo "<td>-</td>"; }
            echo "</tr>";
        }
    }
}
if($Flag=="ShowRequestPurchaseOrderList")
{
    echo ' <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PO No</th>
                    <th>PO Date</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,u.user_name from purchase_order p 
        inner join client_master c on c.client_id = p.supplier_id
        inner join user_master u on u.user_id = p.user_id
        where p.active_status='1'";
        if($user_type_id!="1")
        {
            $query.=" AND p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        }
        if($user_type_id=="1")
        {
            $query.=" AND p.branch_id='$branch_id' AND  p.purchase_order_status ='-1' ";
        }
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            if($user_type_id=="1")
            {
                if($purchase_order_status=="1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success' onclick='ApprovePurchaseOrder($po_id,1)'>Approve</button>
                    <button class='btn btn-sm btn-round btn-danger' onclick='ApprovePurchaseOrder($po_id,2)'>Reject</button>";
                }
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$proFormabutton.'<a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a></td>
            <td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.'</td>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowDeactivePurchaseOrderList")
{
    echo ' <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PO No</th>
                    <th>PO Date</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,u.user_name,cm.currency_code from purchase_order p 
        inner join client_master c on c.client_id = p.supplier_id
        inner join user_master u on u.user_id = p.user_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1' and p.purchase_order_status ='2' and p.po_cancel_status ='0' ";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        }else{
            $query.=" and p.branch_id='$branch_id'";
        }
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.'<a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a></td>
            <td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="ShowCancelPurchaseOrderList")
{
    echo ' <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PO No</th>
                    <th>PO Date</th>
                    <th>Cancel Reason</th>
                    <th>Supplier PI No</th>
                    <th>Supplier PI Date</th>
                    <th>Supplier Name</th>
                    <th>Grand Total</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,u.user_name,cm.currency_code from purchase_order p 
        inner join client_master c on c.client_id = p.supplier_id
        inner join user_master u on u.user_id = p.user_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1' and p.po_cancel_status ='1'";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id'";
        }else{
            $query.=" and p.branch_id='$branch_id'";
        }
        $query.=" order by p.po_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            
            $POCancelbtn='';
            if($user_type_id==1)
            {
                if($po_refund_status=="1")
                {
                    $POCancelbtn='<button class="btn btn-sm btn-round btn-success">Refunded</button>';
                }else if($po_cancel_status=="1")
                {
                    $POCancelbtn='<button class="btn btn-sm btn-round btn-primary" onclick="RefundPO('.$po_id.', \''.$po_custom_number.'\');">Refund</button>';
                }else{
                    $POCancelbtn='';
                }
            }

            $viewrefunddetails = '';
            if($po_refund_amount > 0)
            {
                $viewrefunddetails ="<button class='btn btn-sm btn-round btn-secondary' onclick='LoadRefundDetailsview($po_id, \"".$po_custom_number."\");'><i class='fa fa-eye'></i></button>";
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.'<a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" 
                    data-placement="top" title="Purchase Order" target="_blank" 
                    class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i>
                </a>
                <a href="print_purchase_payment.php?po_no='.base64_encode($po_id).'" target="_blank" 
                    data-placement="top" title="Purchase Payment" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i>
                </a>'.$POCancelbtn.' '.$viewrefunddetails.'
            </td>
            <td>'.$po_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($po_date)).'</td>
            <td>'.$po_cancel_remark.'</td>
            <td>'.$supplier_pi_no.'</td>
            <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.' '.$currency_code.'</td>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
if($Flag=="SendForApproval")
{
    $po_id = $_POST['po_id'];
    $rstpro = mysqli_query($connect,"UPDATE purchase_order SET purchase_order_status='-1' WHERE po_id='$po_id'");
    if($rstpro)
    {
        echo 'Approved';
    }
    else
    {
        echo "Unable To Send For Approval";
    }
}
if($Flag=="PurchaseOrderRequestAction")
{
    if($ApproveStatus=="1")
    {
        $purchase_order_status='1';
        $active_status='1';
    }
    else
    {
        $purchase_order_status='2';
        $active_status='1';
    }
    $rstpro = mysqli_query($connect,"UPDATE purchase_order SET purchase_order_status='$purchase_order_status',active_status='$active_status' WHERE po_id='$po_id'");
    if($rstpro)
    {
        echo 'Approved';
    }
    else
    {
        echo "Unable To Update";
    }
}
// if($Flag=="CancelPO")
// {
//     $po_id = $_POST['po_id'];
//     $remark = $_POST['remark'];
//     $rstcancel = mysqli_query($connect,"update purchase_order set po_cancel_status='1',po_cancel_remark ='$remark' where po_id='$po_id'");
//     if($rstcancel)
//     {
//         $rst = mysqli_query($connect,"select po.pi_no,pr.pro_forma_head_details_id,pr.no_of_bags,pr.po_total_qty,pr.po_used_qty from purchase_order_details po
//             join pro_forma_head_details pr on pr.pi_no=po.pi_no AND pr.product_id=po.product_id
//             where po.purchase_order_id = '$po_id' AND po.po_type = 'Using ProForma'");
//         if ($rst && mysqli_num_rows($rst) > 0) 
//         {
//             while($rwpo = mysqli_fetch_assoc($rst))
//             {   
//                 $new_po_total_qty = 0;
//                 $new_po_used_qty = 0;
                
//                 $pro_forma_head_details_id = $rwpo['pro_forma_head_details_id'];
//                 $no_of_bags = (int)$rwpo['no_of_bags'];
//                 $po_total_qty = (int)$rwpo['po_total_qty'];
//                 $po_used_qty = (int)$rwpo['po_used_qty'];

//                 $new_po_total_qty = $po_total_qty - $no_of_bags;
//                 $new_po_used_qty = $po_used_qty - $no_of_bags;
                
//                 $new_po_total_qty = max(0, $new_po_total_qty);
//                 $new_po_used_qty = max(0, $new_po_used_qty);

//                 $rstqty = mysqli_query($connect,"update pro_forma_head_details set po_total_qty='$new_po_total_qty',po_used_qty ='$new_po_used_qty' where pro_forma_head_details_id='$pro_forma_head_details_id'");
//             }
//         }
//         echo "Success";
//     }
//     else{
//         echo "Unable to Cancel PO";
//     }
// }
if($Flag=="CancelPO")
{
    $po_id = $_POST['po_id'];
    $remark = $_POST['remark'];
    $rstcancel = mysqli_query($connect,"update purchase_order set po_cancel_status='1',po_cancel_remark ='$remark' where po_id='$po_id'");
    if($rstcancel)
    {
        $rst = mysqli_query($connect,"select po.pi_no as proforma_id,po_main.po_type,po_main.LinkToPI_Status,
        po_main.LinkToPI from purchase_order_details po
            JOIN purchase_order po_main ON po_main.po_id = po.purchase_order_id
            where po.purchase_order_id = '$po_id'");
        if ($rst && mysqli_num_rows($rst) > 0) 
        {
            while($rwpo = mysqli_fetch_assoc($rst))
            {   
                $po_type = $rwpo['po_type'];
                if($po_type == "Using ProForma"){
                    $new_po_total_qty = 0;
                    $new_po_used_qty = 0;
                
                    $pro_forma_head_details_id = $rwpo['pro_forma_head_details_id'];
                    $no_of_bags = (int)$rwpo['no_of_bags'];
                    $po_total_qty = (int)$rwpo['po_total_qty'];
                    $po_used_qty = (int)$rwpo['po_used_qty'];
                
                    $new_po_total_qty = $po_total_qty - $no_of_bags;
                    $new_po_used_qty = $po_used_qty - $no_of_bags;
                
                    $new_po_total_qty = max(0, $new_po_total_qty);
                    $new_po_used_qty = max(0, $new_po_used_qty);

                    $rstqty = mysqli_query($connect,"update pro_forma_head_details set po_total_qty='$new_po_total_qty',po_used_qty ='$new_po_used_qty' where pro_forma_head_details_id='$pro_forma_head_details_id'");
                }else if($po_type == "Direct PO"){
                    $LinkToPI = $rwpo['LinkToPI'];
                    $LinkToPI_Status = $rwpo['LinkToPI_Status'];
                    if($LinkToPI_Status == 1 && !empty($LinkToPI))
                    {
                        $linkedProformas = json_decode($LinkToPI, true);

                        if (!empty($linkedProformas) && is_array($linkedProformas)) {
                            foreach ($linkedProformas as $pf) {

                                $proforma_id = $pf['proforma_id'];
                                $use_qty     = (int)$pf['use_qty'];
                                $pfqry = mysqli_query($connect, "SELECT no_of_bags,po_total_qty, po_used_qty FROM pro_forma_head_details WHERE pro_forma_head_details_id = '$proforma_id'");

                                if ($pfqry && mysqli_num_rows($pfqry) > 0) {
                                    $pfrow = mysqli_fetch_assoc($pfqry);
                                    $no_of_bags = (int)$pfrow['no_of_bags'];
                                    $po_total_qty = (int)$pfrow['po_total_qty'];
                                    $po_used_qty = (int)$pfrow['po_used_qty'];
                                    $new_po_total_qty = $po_total_qty - $no_of_bags;
                                    $new_po_used_qty = $po_used_qty - $no_of_bags;
                                
                                    $updated_po_total_qty = max(0, $new_po_total_qty - $use_qty);
                                    $updated_po_used_qty  = max(0, $new_po_used_qty - $use_qty);

                                    $sql = mysqli_query($connect, "UPDATE pro_forma_head_details SET po_total_qty = '$updated_po_total_qty', po_used_qty = '$updated_po_used_qty' WHERE pro_forma_head_details_id = '$proforma_id'");
                                }
                            }
                            $rstupdate = mysqli_query($connect,"update purchase_order_details set pi_no='0', pi_product_qty='' where  purchase_order_id='$po_id'");
                        }
                    }
                }
            }
            echo "Success";
        }
        
    }
    else{
        echo "Unable to Cancel PO";
    }
}
if($Flag == "LoadRefundDetails")
{
    $po_id = $_POST['po_id'];
    $data = array();
    $rstpurchase = mysqli_query($connect,"select p.po_custom_number,p.supplier_pi_date,p.grand_total,
    c.client_name,u.user_name,pp.*,cm.currency_code,p.po_refund_amount from purchase_order p 
    inner join client_master c on c.client_id = p.supplier_id
    left join purchase_order_payment pp on pp.purchase_order_id = p.po_id 
    inner join country_master cm on cm.id=p.currency_id 
    inner join user_master u on u.user_id = p.user_id where p.po_id='$po_id'");
    $rwpurchase = mysqli_fetch_assoc($rstpurchase);
    extract($rwpurchase);

    $rstpaidamt = mysqli_query($connect,"select pp.paid_amount,pp.paid_date,p.grand_total as total_amount,p.po_custom_number,cm.currency_code,p.po_id from 
    purchase_order_receipt_payment pp
    inner join purchase_order p on p.po_id=pp.po_no 
    inner join client_master c on c.client_id = p.supplier_id
    inner join country_master cm on cm.id=p.currency_id 
    where p.po_id = '$po_id' and pp.approval_status=1
    order by pp.customer_receipt_id desc");
    $sumpaidAmt = 0;
    while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
    {
        $sumpaidAmt+=$rwpaidamt['paid_amount'];
    }

    $data['po_custom_number'] = $po_custom_number;
    $data['grand_total'] = $grand_total;
    $data['po_id'] = $po_id;
    $po_paid_amount = 0;
    $data['basicpodetails'] =   '<div class="row" style="margin-top:-40px">
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-warning mb-1">PO No/Date:</h6>
                                    <p class="fw-bold">'.$po_custom_number.' / '.date("d M Y",strtotime($supplier_pi_date)).'</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-danger mb-1">PO Amount:</h6>
                                    <p class="fw-bold">'.number_format($grand_total).' '.$currency_code.'</p>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Paid Amount:</h6>
                                    <p class="fw-bold">'.number_format($sumpaidAmt).' '.$currency_code.'</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Total Refund Amount:</h6>
                                    <p class="fw-bold">'.number_format($po_refund_amount).' '.$currency_code.'</p>
                                </div>
                            </div>
                        </div>';
                $data['popaymentdetails'] = '<div class="row"> <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th> Sr No.</th>
                        <th> Total Amount</th>
                        <th> Paid Amount</th>
                        <th> Paid Date</th>
                    </tr>
                </thead>
                <tbody>';

                mysqli_data_seek($rstpaidamt,0);
                if(mysqli_num_rows($rstpaidamt)>0)
                {
                    $sr = 1;
                    while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
                    {
                        extract($rwpaidamt);
                        $data['popaymentdetails'] .= "<tr>
                            <td>".$sr."</td>
                            <td>".number_format($total_amount)." ".$currency_code."</td>
                            <td>".number_format($paid_amount)." ".$currency_code."</td>
                            <td>".date('d/m/Y',strtotime($paid_date))."</td>";
                        $data['popaymentdetails'] .= "</tr>";
                        $sr++;
                        $po_paid_amount = $po_paid_amount + $paid_amount;
                    }
                } 
    $data['popaymentdetails'] .= '</tbody>
    </table></div>';

    $data['popaidamount'] = $po_paid_amount - $po_refund_amount;
    $data['pocurrencycode'] = $currency_code;
    echo json_encode($data);
}
if($Flag=="SaveRefundPO")
{
    $po_id = $_POST['po_id'];
    $refund_amount = (float)$_POST['refund_amount'];
    $paid_date = date('Y-m-d');
    $rstpurchase = mysqli_query($connect,"insert into purchase_order_refund(po_id,refund_amt,refund_by)
    values('$po_id','$refund_amount','$user_id_session')");
    if($rstpurchase)
    {
        $rstpurchase = mysqli_query($connect,"select po_refund_amount from purchase_order where po_id='$po_id' ");
        $rwpurchase = mysqli_fetch_assoc($rstpurchase);
        $popreviousrefund = $rwpurchase['po_refund_amount'] +$refund_amount;
        $porefund_status ='';
        if((float)$POTotalAmount == (float)$popreviousrefund)
        {
            $porefund_status  = ", po_refund_status='1'";
        }
        $rstpro = mysqli_query($connect,"UPDATE purchase_order SET 
        po_refund_amount= po_refund_amount+$refund_amount $porefund_status WHERE po_id='$po_id'");
        if($rstpro)
        {
            echo "Success";
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    }else{
        echo "Unable to add refund";
    }
}

if ($Flag == "LoadPODetails") 
{
    $po_id = $_POST['po_id'];
    $data = array();
    $data['basicPIdetails'] = '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Sr.No</th>
                <th>Products</th>
                <th>Quantity</th>
                <th>Pro Forma</th>
                <th>Total Quantity</th>
                <th>Use Quantity</th>
            </tr>
        </thead>
        <tbody>';

    $rstcat = mysqli_query($connect, "
        SELECT ph.pi_custom_number,
               po.product_id,
               pr.product_id AS prd_id,
               po.no_of_bags AS qty,
               pr.pi_no,
               pr.pro_forma_head_details_id,
               pr.no_of_bags,
               pr.po_total_qty,
               pr.po_used_qty,
               p.product_name,
               pp.po_custom_number
        FROM purchase_order_details po
        JOIN pro_forma_head_details pr ON pr.product_id = po.product_id 
        JOIN pro_forma_head ph ON ph.pi_no = pr.pi_no
        JOIN product_master p ON p.product_id = pr.product_id
        JOIN purchase_order pp ON pp.po_id = po.purchase_order_id
        WHERE po.purchase_order_id = '$po_id' 
          AND ph.active_status = '1'
        ORDER BY po.product_id, pr.pi_no
    ");

    $srno = 1;
    $products = [];
    $po_custom_number = "";
    // Group Pro Formas by product
    while ($rwcat = mysqli_fetch_assoc($rstcat)) {
        $po_custom_number = $rwcat['po_custom_number'];
        $products[$rwcat['product_id']][] = $rwcat;
    }

    foreach ($products as $product_id => $rows) {
        $firstRow = $rows[0];
        // print_r($firstRow);
        // Build dropdown
        $ProductIdForSelect = $firstRow['product_id'];
        $selectHtml = "<select class='proforma-select form-control' id='proformaproduct_$ProductIdForSelect'>";
        $selectHtml .= "<option value=''>Select</option>";
        foreach ($rows as $r) {
            $total_remain_qty = $r['no_of_bags'] - $r['po_used_qty'];
            if($total_remain_qty > 0){
                $selectHtml .= "<option value='{$r['pro_forma_head_details_id']}' data-qty='{$total_remain_qty}'>{$r['pi_custom_number']}</option>";
            }
        }
        $selectHtml .= "</select>";

        $data['basicPIdetails'] .= "<tr>
            <td>{$srno}</td>
            <td style='display:none;' class='product_id'>{$ProductIdForSelect}</td>
            <td>{$firstRow['product_name']}</td>
            <td class='actual-qty'>{$firstRow['qty']}</td>
            <td>{$selectHtml}</td>
            <td class='qty-val'>0</td>
            <td><input type='text' class='form-control use-qty' value=''></td>
        </tr>";
        $srno++;
    }

    $data['basicPIdetails'] .= "</tbody></table>";
    $data['heading'] = "Linking To PI ( PO No. : ".$po_custom_number." )";
    echo json_encode($data);
}

if ($Flag == "SaveLinkedPI") {
    // print_r($_POST);
    // exit;
    $po_id = mysqli_real_escape_string($connect, $_POST['po_id']);
    
    //$linkData  = mysqli_real_escape_string($connect, $_POST['linkData']);

    $linkDataRaw = $_POST['linkData'];

    $linkArray = json_decode($linkDataRaw, true);

    if (!is_array($linkArray)) {
        echo "Error: Invalid link data format";
        exit;
    }

    $linkDataEscaped = mysqli_real_escape_string($connect, $linkDataRaw);


    $res = mysqli_query($connect, "UPDATE `purchase_order` SET `LinkToPI`= '$linkDataEscaped',`LinkToPI_Status`= 1 WHERE `po_id`='$po_id'");
    if ($res) {

        $sql = mysqli_query($connect, "INSERT INTO PO_link_log (po_id, link_data) VALUES ('$po_id', '$linkDataEscaped')");

        foreach ($linkArray as $item) 
        {
            $proforma_detail_id = intval($item['proforma_detail_id']); 
            $product_id = intval($item['product_id']); 
            $useqty      = floatval($item['use_qty']);
            $actual_qty   = floatval($item['actual_qty']);
            $pi_available_qty    = floatval($item['pi_available_qty']);

            $rstupdatepurchase = mysqli_query($connect,"update purchase_order_details set pi_no='$proforma_detail_id',
            pi_product_qty='$pi_available_qty' where purchase_order_id='$po_id' and product_id='$product_id' ");

            $result = mysqli_query($connect,"SELECT `no_of_bags`, `po_total_qty`, `po_used_qty` FROM pro_forma_head_details WHERE `pro_forma_head_details_id` = '$proforma_detail_id'");

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                $no_of_bags   = floatval($row['no_of_bags']);
                $total_qty    = floatval($row['po_total_qty']);
                $used_qty_old = floatval($row['po_used_qty']);

                $remaining_before = $no_of_bags - $used_qty_old;

                if ($useqty > $remaining_before) {
                    echo "Error: Trying to use more than available for ID $proforma_detail_id";
                    continue;
                }

                $new_used_qty   = $used_qty_old + $useqty;
                $remaining_after = $total_qty + $actual_qty;

                $sql1 = mysqli_query($connect,"UPDATE pro_forma_head_details SET po_used_qty = '$new_used_qty', po_total_qty = '$remaining_after' WHERE pro_forma_head_details_id = '$proforma_detail_id'");
            }
        }

        echo "Success";

    } else {
        echo "Error: " . mysqli_error($connect);
    }
    exit;
}

if($Flag=="LoadRefundDetailsview")
{
    $po_id = $_POST['po_id'];
    $rstpurchase = mysqli_query($connect,"select p.po_custom_number,p.supplier_pi_date,p.grand_total,
    c.client_name,u.user_name,pp.*,cm.currency_code,p.po_refund_amount from purchase_order p 
    inner join client_master c on c.client_id = p.supplier_id
    left join purchase_order_payment pp on pp.purchase_order_id = p.po_id 
    inner join country_master cm on cm.id=p.currency_id 
    inner join user_master u on u.user_id = p.user_id where p.po_id='$po_id'");
    $rwpurchase = mysqli_fetch_assoc($rstpurchase);
    extract($rwpurchase);

    $rstpaidamt = mysqli_query($connect,"select pp.paid_amount from 
    purchase_order_receipt_payment pp
    inner join purchase_order p on p.po_id=pp.po_no 
    where p.po_id = '$po_id' and pp.approval_status=1");
     $sumpaidAmt = 0;
    while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
    {
        $sumpaidAmt+=$rwpaidamt['paid_amount'];
    }

    $po_paid_amount = 0;
    echo  '<div class="row" style="margin-top:-40px">
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-warning mb-1">PO No/Date:</h6>
                                    <p class="fw-bold">'.$po_custom_number.' / '.date("d M Y",strtotime($supplier_pi_date)).'</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-danger mb-1">PO Amount:</h6>
                                    <p class="fw-bold">'.number_format($grand_total).' '.$currency_code.'</p>
                                </div>
                            </div>
                    
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Paid Amount:</h6>
                                    <p class="fw-bold">'.number_format($sumpaidAmt).' '.$currency_code.'</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-1 bg-light rounded shadow-sm">
                                    <h6 class="text-info mb-1">Total Refund Amount:</h6>
                                    <p class="fw-bold">'.number_format($po_refund_amount).' '.$currency_code.'</p>
                                </div>
                            </div>
                        </div>';
                echo '<div class="row mt-3"> <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th> Sr No.</th>
                        <th> Amount</th>
                        <th> Refund By</th>
                        <th> Refund On</th>
                    </tr>
                </thead>
                <tbody>';

                $rstrefund = mysqli_query($connect,"select pf.*, um.user_name from purchase_order_refund pf 
                inner join user_master um on um.user_id= pf.refund_by where pf.po_id='$po_id' order by pf.refund_id desc");
                if(mysqli_num_rows($rstrefund)>0)
                {
                    $sr = 1;
                    while($rwrefund = mysqli_fetch_assoc($rstrefund))
                    {
                        extract($rwrefund);
                        echo  "<tr>
                            <td>".$sr."</td>
                            <td>".$refund_amt." ".$currency_code."</td>
                            <td>".$user_name."</td>
                            <td>".date('d/m/Y',strtotime($refund_on))."</td>";
                       echo "</tr>";
                        $sr++;
                    }
                } 
    echo '</tbody>
    </table></div>';

}
?>