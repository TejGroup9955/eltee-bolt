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
        <td><button class='btn btn-danger btn-sm btnremoveproduct'><i class='fa fa-close'></i></button></td>
    </tr>";
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
    $i=0;
    $j=0;
    $pi_custom_number = 0;
    $destination_port_name = $port_of_loading_name = "";
    $cmd_1 = "SELECT pi_custom_number FROM pro_forma_head WHERE year_id = '$financial_year' AND comp_id = '$comp_id' ORDER BY pi_no DESC LIMIT 1 ";
    $result_1 = $connect->query($cmd_1);
    if ($result_1->num_rows > 0)
    {
        if($row_1 = $result_1->fetch_assoc())
        {
            $piArr = explode("/", $row_1['pi_custom_number']);	
            $pi_custom_number = $piArr[1];
        }
    }
    else
    {
        $pi_custom_number=0;
    }
    $pi_custom_number =  intval($pi_custom_number) + 1;
        
    if($pi_custom_number < 10)
    {
        $pi_custom_number = '00'.$pi_custom_number;
    }
    else if($pi_custom_number < 100 )
    {
        $pi_custom_number = '0'.$pi_custom_number;
    }
    else
    {
        $pi_custom_number = $pi_custom_number;
    }
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
    $year_name_session  ="2024-25";
    $year_prev = substr($year_name_session, 0,2);
    $year_after = substr($year_name_session,5);  
    $pi_custom_number = $year_prev.'-'.$year_after.'/'.$pi_custom_number;
    $error_product_name = "";
    $sql = "INSERT INTO `pro_forma_head`(pi_custom_number, pi_invoice_date, account_id, 
    country_id,currency_id, remark, total_amount,grand_total,user_id, comp_id, branch_id, year_id, pro_forma_status,DiscountAmt,bank_detail_id,time_of_shipment, country_of_origin, country_of_supply, port_of_loading, destination_port, port_of_loading_name,destination_port_name,part_shipment, trans_shipment, insurance, marking) 
    VALUES('$pi_custom_number',
    '$pi_invoice_date', '$account_id', 
    '$country_id','$currency_id','$remark', '$totalAmtAll',
    '$grand_total', '$user_id_session', '$comp_id', '$branch_id', '$financial_year', 
     '$pro_forma_status','$DiscountAmt','$bank_account_no','$time_of_shipment','$country_of_origin','$country_of_supply','$port_of_loading','$destination_port','$port_of_loading_name','$destination_port_name','$part_shipment','$trans_shipment','$insurance','$marking') ";
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
                $query =" INSERT INTO `pro_forma_head_details`(pi_no,
                 product_id, total_weight, rate, total_amt, gst,packaging_type,each_bag_weight,
                 rateperton,packaging_id,no_of_bags) 
                VALUES('$pi_no_new','$product_id','$totalweight','$rate','$totalamt','$gst_amount',
                '$packagingTypeName', '$weightperpack','$rateperton','$packaging_id','$quantity')";
                $query_res2 = $connect->query($query);       
                $i++;
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

    if(@$error_count == 1)
    {
        echo 'Connection Problem!!! Please try again..';
    }

}

if($Flag=="ShowProFormaList")
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
        $query = "select p.*,c.client_name from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        where p.active_status='1' ";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' ";
        }
        if($user_type_id=="1")
        {
            $query.=" and p.pro_forma_status IN ('1') ";
        }
        else
        {
            $query.=" and p.pro_forma_status IN ('-1','1','0') ";
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
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>
                    <a href='pro-forma-invoice.php?PI_No=".base64_encode($pi_no)."'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-pencil'></i></a>";
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
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-success'>Approved</button>";
                }
                else if($pro_forma_status=="-1")
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-info' onclick='SendForApproval($pi_no)'>Send For Approval</button>";
                }
                else
                {
                    $proFormabutton = "<button class='btn btn-sm btn-round btn-secondary'>Waiting For Approval</button>";
                }
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$proFormabutton.'<a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.'</td>
            </tr>';
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
        $query = "select p.*,c.client_name from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        where p.active_status='1'";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' ";
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
            <td>'.$proFormabutton.'<a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.'</td>
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
        $query = "select p.*,c.client_name from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        where p.active_status='0' and p.pro_forma_status ='0'";
        if($user_type_id!="1")
        {
            $query.=" and p.user_id='$user_id_session' ";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            
            echo '<tr>
            <td>'.$srno.'</td>
            <td><a href="print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank"><i class="fa fa-print"></i></a></td>
            <td>'.$pi_custom_number.'</td>
            <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
            <td>'.$client_name.'</td>
            <td>'.$grand_total.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
?>