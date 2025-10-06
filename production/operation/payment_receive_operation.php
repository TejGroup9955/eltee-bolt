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

if($Flag=="ShowPaymentList")
{
        echo '<table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                    <th>Paid Amount</th>
                    <th>Remaining Amount</th>
                </tr>
                </thead>
                <tbody>';
        $query = "select p.*,c.client_name,cm.currency_code from pro_forma_head p 
        inner join client_master c on c.client_id = p.account_id
        inner join country_master cm on cm.id=p.currency_id 
        where p.active_status='1' ";
        if($user_type_id=="1")
        {
            $query.=" AND p.branch_id='$branch_id' AND p.pro_forma_status IN ('1') ";
        }
        else
        {
            $query.=" and p.user_id='$user_id_session' AND p.branch_id='$branch_id' AND p.pro_forma_status IN ('1') ";
        }
        $query.=" order by p.pi_no desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            $pi_no_new = base64_encode($pi_no);
            $grand_total = ceil($rwclient['grand_total']);
            $paid_amount = 0;
            $query1 = $connect->query("SELECT SUM(paid_amount) AS paid_amt FROM pro_forma_receipt_payment WHERE pi_no=".$pi_no." ");
            $rowCount = $query1->num_rows;
            if($rowCount > 0){
                if($row1 = $query1->fetch_assoc()){
                    $paid_amount = $row1['paid_amt'];
                }
            }
            else{
                $paid_amount = "0";
            }
            if($paid_amount=="") { $paid_amount = 0; }
            $remaining_amt =  $grand_total - $paid_amount;
            if($paid_amount == $grand_total){
                $proFormabutton = "<a href='#' class='btn btn-success btn-round btn-sm' ><i class='fa fa-check'></i>&nbsp;Received</a>";
            }else{
                $proFormabutton = "<a href='payment_details.php?PI_No=".$pi_no_new."'  class='btn btn-info btn-round btn-sm' ><i class='fa fa-plus'></i>&nbsp;Receive</a>";
            }
            echo '<tr>
                <td>'.$srno.'</td>
                <td>'.$proFormabutton.'<a href="print-payment.php?pi_no='.$pi_no_new.'" target="_blank" class="btn btn-sm btn-round btn-secondary"><i class="fa fa-print"></i></a></td>
                <td>'.$pi_custom_number.'</td>
                <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
                <td>'.$client_name.'</td>
                <td>'.$grand_total.' '.$currency_code.'</td>
                <td>'.$paid_amount.' '.$currency_code.'</td>
                <td>'.$remaining_amt.' '.$currency_code.'</td>
            </tr>';
            $srno++;
        }        
       echo '</tbody></table>';
}

if($Flag=="SavePaymentDetails")
{
	if($pi_no!="")
    {       
            $error_count = $response = "";
            $error_product_name = "";
    		$query_res1 = mysqli_query($connect,"SELECT * FROM `pro_forma_head` WHERE `pi_no`='$pi_no'");
    		$query_res2 = mysqli_fetch_assoc($query_res1);
        	if(@$query_res2 > 0)
            {
                $sql1 = "INSERT INTO customer_information (payment_mode, voucher_number, voucher_date, received_by, cheque_number, cheque_date, cheque_bank_name, dd_number, dd_date, dd_bank_name, neft_date, utr_number_neft, bank_details_neft, rtgs_date, utr_number_rtgs, bank_details_rtgs, tt_date, tt_ref_number, bank_details_tt, lt_date, lt_ref_number, bank_details_lt) VALUES('$payment_mode', '$voucher_number', '$voucher_date', '$received_by', '$cheque_number', '$cheque_date', '$cheque_bank_name', '$dd_number', '$dd_date', '$dd_bank_name', '$neft_date', '$utr_number_neft', '$bank_details_neft', '$rtgs_date', '$utr_number_rtgs', '$bank_details_rtgs' , '$tt_date', '$tt_ref_number', '$bank_details_tt', '$lt_date', '$lt_ref_number', '$bank_details_lt')";
                $query_res1 = $connect->query($sql1);
                if($query_res1 > 0)
                {
                  $payment_method_id_new = mysqli_insert_id($connect);
                  $query1 ="INSERT INTO `pro_forma_receipt_payment`(`pi_no`, `total_amount`, `paid_amount`, `remain_amount`, `paid_date`, `payment_mode`) VALUES ('$pi_no','$grand_total','$paid_amount','$remain_amount','$paid_date','$payment_method_id_new')";
                  $query_res3 = $connect->query($query1);
                  if($query_res3 > 0)
                  {
                    $customer_receipt_id = mysqli_insert_id($connect);
                    uploadFiles('ProFormaPaymentTT','pro_forma_receipt_payment','payment_tt_copy','payment_tt_copy','customer_receipt_id',$customer_receipt_id, $connect);
                    $response = 1;
                  }
                  else { $error_count = 1; }
                }
                else { $error_count = 1; }
            }
            else {    $error_count = 1; }

            if($error_count == 1){
                $response = 2;
            }

            echo $response;
            die;
	}
}

if($Flag=="SavePOPaymentDetails")
{
    if($txtpono!="")
    {     
        // print_r($_POST);
        // exit();  
        $query_res1 = mysqli_query($connect,"SELECT * FROM `purchase_order` WHERE `po_id`='$txtpono' and po_custom_number='$txtpocustomno'");
        $query_res2 = mysqli_fetch_assoc($query_res1);
        if(@$query_res2 > 0)
        {
                $paid_date = date('Y-m-d');
                $query1 ="INSERT INTO `purchase_order_receipt_payment`(`po_no`, `total_amount`, `paid_amount`, 
                `remain_amount`, `paid_date`) VALUES ('$txtpono','$totalAmount','$paymentAmount',
                '$remainingBalance','$paid_date')";
                $payment_receipt_id = mysqli_insert_id($connect);
                $query_res3 = $connect->query($query1);
                if($query_res3 > 0){
                    echo "Added";
                }
                else { echo "Unable To Add Payment Details"; }
        }
        else { echo "Purchase Details Not Found"; }
    }
}

if($Flag == "ApprovePurchaseOrderPayment")
{
    $data = array();
    $customer_receipt_id = $_POST['customer_receipt_id'];
    $status = $_POST['status'];
    $rstpro = mysqli_query($connect,"UPDATE purchase_order_receipt_payment SET approval_status='$status' WHERE customer_receipt_id='$customer_receipt_id'");
    if($rstpro)
    {
        if($status == 1){
            $data['msg'] = "Approved Successfully";
            $data['error'] = 0;
        }else{
            $data['msg'] = "Rejected Successfully";
            $data['error'] = 0;
        }
    }
    else
    {
        $data['msg'] = "Unable To Send For Approval";
        $data['error'] = 1;
    }

    echo json_encode($data);

}

if($Flag=="SavePaymentTransactionPO")
{
    if($customer_receipt_id!="")
    {     
        // print_r($_POST);
        // exit();  
        $query_res1 = mysqli_query($connect,"SELECT * FROM `purchase_order_receipt_payment` WHERE `customer_receipt_id`='$customer_receipt_id'");
        $query_res2 = mysqli_fetch_assoc($query_res1);
        if(@$query_res2 > 0)
        {
            $sql1 = "INSERT INTO customer_information (payment_mode, voucher_number, voucher_date, received_by, cheque_number, cheque_date, cheque_bank_name, dd_number, dd_date, dd_bank_name, neft_date, utr_number_neft, bank_details_neft, rtgs_date, utr_number_rtgs, bank_details_rtgs, tt_date, tt_ref_number, bank_details_tt, lt_date, lt_ref_number, bank_details_lt) VALUES('$payment_mode', '$voucher_number', '$voucher_date', '$received_by', '$cheque_number', '$cheque_date', '$cheque_bank_name', '$dd_number', '$dd_date', '$dd_bank_name', '$neft_date', '$utr_number_neft', '$bank_details_neft', '$rtgs_date', '$utr_number_rtgs', '$bank_details_rtgs' , '$tt_date', '$tt_ref_number', '$bank_details_tt', '$lt_date', '$lt_ref_number', '$bank_details_lt')";
            $query_res1 = $connect->query($sql1);
            $payment_method_id_new = mysqli_insert_id($connect);
            if($query_res1 > 0)
            {
                $query1 ="UPDATE `purchase_order_receipt_payment` SET `payment_mode`='$payment_method_id_new',payment_status='-1' WHERE `customer_receipt_id`='$customer_receipt_id'";
                $query_res3 = $connect->query($query1);
                uploadFiles('POPaymentProof','purchase_order_receipt_payment','TransactionProof','txtTransactionProof','customer_receipt_id',$customer_receipt_id, $connect);
                echo "Added";
            }
            else { echo "Unable To Add Payment Details"; }
        }
        else { echo "Purchase Details Not Found"; }
    }
}

if($Flag=="PurchaseOrderTransactionStatus")
{
    if($customer_receipt_id!="")
    {   
        $query_res1 = mysqli_query($connect,"SELECT * FROM `purchase_order_receipt_payment` WHERE `customer_receipt_id`='$customer_receipt_id'");
        $query_res2 = mysqli_fetch_assoc($query_res1);
        if(@$query_res2 > 0)
        {
            $query1 ="UPDATE `purchase_order_receipt_payment` SET `payment_status`='$status' WHERE `customer_receipt_id`='$customer_receipt_id'";
            $query_res3 = $connect->query($query1);
            echo "Updated";
        }
        else { echo "Purchase Details Not Found"; }
    }
}


if($Flag=="SaveTransactionPO_TTFile")
{
    if($customer_receipt_id_tt!="")
    {   
        $query_res1 = mysqli_query($connect,"SELECT * FROM `purchase_order_receipt_payment` WHERE `customer_receipt_id`='$customer_receipt_id_tt'");
        $query_res2 = mysqli_fetch_assoc($query_res1);
        if(@$query_res2 > 0)
        {
            $today = date('Y-m-d H:i:s');
            uploadFiles('POPaymentProof','purchase_order_receipt_payment','TTdoc_file','txtTransactionTTProof','customer_receipt_id',$customer_receipt_id_tt, $connect);
            $query1 ="UPDATE `purchase_order_receipt_payment` SET `TTUploaded_on`='$today' WHERE `customer_receipt_id`='$customer_receipt_id_tt'";
            $query_res3 = $connect->query($query1);
            echo "Added";
        }
        else { echo "Purchase Details Not Found"; }
    }
}

if($Flag=="SavePaymentEntryPO")
{
	if($txtpono!="")
    {     
        // print_r($_POST);
        // exit();  
        $query_res1 = mysqli_query($connect,"SELECT * FROM `purchase_order` WHERE `po_id`='$txtpono' and supplier_pi_no='$txtpocustomno'");
        $query_res2 = mysqli_fetch_assoc($query_res1);
        if(@$query_res2 > 0)
        {
            $sql1 = "INSERT INTO customer_information (payment_mode, voucher_number, voucher_date, received_by, cheque_number, cheque_date, cheque_bank_name, dd_number, dd_date, dd_bank_name, neft_date, utr_number_neft, bank_details_neft, rtgs_date, utr_number_rtgs, bank_details_rtgs, tt_date, tt_ref_number, bank_details_tt, lt_date, lt_ref_number, bank_details_lt) VALUES('$payment_mode', '$voucher_number', '$voucher_date', '$received_by', '$cheque_number', '$cheque_date', '$cheque_bank_name', '$dd_number', '$dd_date', '$dd_bank_name', '$neft_date', '$utr_number_neft', '$bank_details_neft', '$rtgs_date', '$utr_number_rtgs', '$bank_details_rtgs' , '$tt_date', '$tt_ref_number', '$bank_details_tt', '$lt_date', '$lt_ref_number', '$bank_details_lt')";
            $query_res1 = $connect->query($sql1);
            $payment_method_id_new = mysqli_insert_id($connect);
            if($query_res1 > 0)
            {
                $paid_date = date('Y-m-d');
                $query1 ="INSERT INTO `purchase_order_receipt_payment`(`po_no`, `total_amount`, `paid_amount`, 
                `remain_amount`, `paid_date`, `payment_mode`) VALUES ('$txtpono','$totalAmount','$paymentAmount',
                '$remainingBalance','$paid_date','$payment_method_id_new')";
                $payment_receipt_id = mysqli_insert_id($connect);
                $query_res3 = $connect->query($query1);
                uploadFiles('POPaymentProof','purchase_order_receipt_payment','TransactionProof','txtTransactionProof','payment_mode',$payment_receipt_id, $connect);
                echo "Added";
            }
            else { echo "Unable To Add Payment Details"; }
        }
        else { echo "Purchase Details Not Found"; }
	}
}

if($Flag=="ShowPaymentReceiptList")
{
    // dt-responsive nowrap
    echo '<table class="display table table-striped table-bordered " cellspacing="0" width="100%" id="dtlRecord">
                            <thead >
                                <tr>
                                    <th> Sr No.</th>
                                    <th> PO No</th>
                                    <th> PI No</th>
                                    <th> Supplier Name</th>
                                    <th> Total Amount</th>
                                    <th> Paid Amount</th>
                                    <th> Remaining Amount</th>
                                    <th> Paid Date</th>
                                    <th width="100px"> Approval Status</th>
                                    <th> Transaction Status</th>
                                    <th width="80px"> TT File</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $rstpaidamt = mysqli_query($connect,"select pp.*,p.po_custom_number,p.grand_total,
                            cm.currency_code,p.po_id,c.client_name,p.LinkToPI from 
                            purchase_order_receipt_payment pp
                            inner join purchase_order p on p.po_id=pp.po_no 
                            inner join client_master c on c.client_id = p.supplier_id
                            inner join country_master cm on cm.id=p.currency_id 
                            where p.active_status='1' and p.po_cancel_status!='1'
                            order by pp.customer_receipt_id desc");
                                    if(mysqli_num_rows($rstpaidamt)>0)
                                    {
                                       $sr = 1;
                                       while($rwpaidamt = mysqli_fetch_assoc($rstpaidamt))
                                       {
                                            extract($rwpaidamt);
                                            $proforma_ids = '';
                                            $data = json_decode($LinkToPI,true);
                                            if(!empty($data)){
                                                $proforma_id = array_column($data, 'proforma_id');
                                                $proforma_ids = implode(",",$proforma_id);
                                            }
                                            $po_print_btn = '<a href="print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" class="btn btn-sm btn-round btn-info"><i class="fa fa-print"></i></a>';
                                            echo "<tr>
                                                <td>".$sr."</td>
                                                <td>".$po_custom_number." " .$po_print_btn."</td>
                                                <td>".$proforma_ids."</td>
                                                <td>".$client_name."</td>
                                                <td>".number_format($grand_total)." ".$currency_code."</td>
                                                <td>".number_format($paid_amount)." ".$currency_code."</td>
                                                <td>".number_format($total_amount - $paid_amount)." ".$currency_code."</td>
                                                <td>".date('d/m/Y',strtotime($paid_date))."</td>";
                                                if($approval_status == -1){
                                                    if($user_type_id == 1){
                                                    echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-success" onclick="ApprovePurchaseOrderPayment('.$customer_receipt_id.',1)">&nbsp;Approve</a>&nbsp;<a href="#" class="btn btn-sm btn-round btn-danger" onclick="ApprovePurchaseOrderPayment('.$customer_receipt_id.',0)">&nbsp;Reject</a></td>';
                                                    }else{
                                                        echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-danger">&nbsp;Pending For Approval</a></td>';
                                                    }
                                                    echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-warning">&nbsp;Pending</a></td>
                                                    <td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-secondary">&nbsp;Not Uploaded Yet</a></td>';
                                                }else if($approval_status == 1){
                                                    echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-success"><i class="fa fa-check"></i>&nbsp;Approved</a></td>';

                                                    if($user_type_id != 1){
                                                        if($payment_status==0)
                                                        {
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-info" onclick="PurchaseOrderTransaction('.$customer_receipt_id.',1)"><i class="fa fa-plus"></i></a></td>
                                                             <td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-secondary">&nbsp;Not Uploaded Yet</a></td>';
                                                        }else if($payment_status=="-1"){
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-secondary">Waiting For Approval</a></td>
                                                            <td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }
                                                        else if($payment_status=="-2"){
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-danger">Rejected</a></td>
                                                            <td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }
                                                        else{
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-success">&nbsp;Completed</a></td>
                                                            <td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }
                                                    }else{
                                                        if($payment_status==0)
                                                        {
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-warning">&nbsp;Pending</a></td>
                                                             <td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-secondary">&nbsp;Not Uploaded Yet</a></td>';
                                                        }else if($payment_status=="-1"){
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-success" onclick="PurchaseOrderTransactionStatus('.$customer_receipt_id.',1)">&nbsp;Approve</a>
                                                            &nbsp;<a href="#" class="btn btn-sm btn-round btn-danger" onclick="PurchaseOrderTransactionStatus('.$customer_receipt_id.',-2)">&nbsp;Reject</a>
                                                            </td><td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }else if($payment_status=="-2"){
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-danger">Rejected</a></td>
                                                            </td><td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }else{
                                                            echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-success">&nbsp;Completed</a></td>
                                                            <td style="text-align:center"><a href="production/'.$TransactionProof.'" target="_blank" class="btn btn-sm btn-round btn-success">&nbsp;<i class="fa fa-eye"></i> </a></td>';
                                                        }
                                                    }
                                                }
                                                else if($approval_status == 0){
                                                    echo '<td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-danger"><i class="fa fa-times"></i>&nbsp;Rejected</a></td>
                                                    <td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-warning">&nbsp;Pending</a></td>
                                                    <td style="text-align:center"><a href="#" class="btn btn-sm btn-round btn-secondary">&nbsp;Not Uploaded Yet</a></td>';
                                                }

                                            echo "</tr>";
                                            $sr++;
                                       }
                                    }
                                echo '</tbody>
                        </table>';
}

function uploadFiles($FolderName, $tablename, $fieldName, $value, $CompareField, $CompareId, $connect) {
    if (isset($_FILES[$value]) && $_FILES[$value]['error'] == 0) {
        $file_name = $_FILES[$value]['name'];
        $file_tmp = $_FILES[$value]['tmp_name'];
        $upload_dir = "../$FolderName/"; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_file_name = uniqid('BD_', true) . '.' . $file_ext;             
        $target_file = $upload_dir . $unique_file_name;

       // $target_file = $upload_dir . basename($file_name);
        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = mysqli_query($connect,"UPDATE $tablename SET $fieldName='$target_file' WHERE $CompareField='$CompareId'");
        } else {
            echo "Error uploading file: " . $file_name;
        }
    }
}




























































?>