<?php

include '../configuration.php';
// include 'numconvert.php';

$pi_no = base64_decode($_GET['pi_no']);
$grand_total = 0; $tax_amt = 0; $sbc_amt = 0; $kkc_amt = 0;
$account_id = "";
$cmd ="SELECT pi_custom_number, supplier_lic_no, account_id, supplier_lic_date, pi_invoice_date, pi_valid_date, grand_total, comp_id FROM pro_forma_head WHERE pi_no='$pi_no'";
$result = $connect->query($cmd);
if ($result->num_rows > 0) {
	if($row = $result->fetch_assoc()) {
		$pi_custom_number = $row['pi_custom_number'];
		$supplier_lic_no = $row['supplier_lic_no'];
		$account_id = $row['account_id'];
		$grand_total = round($row['grand_total']);

		//$supplier_lic_date=strtotime($row["supplier_lic_date"]);
		//$supplier_lic_date=$row['supplier_lic_date']=date('d-m-Y', $supplier_lic_date); 

		if($row["supplier_lic_date"] == "0000-00-00"){
			$supplier_lic_date = "-";
		}else{
			$supplier_lic_date=date('d-m-Y', strtotime($row["supplier_lic_date"]));
		}

		//$pi_invoice_date=strtotime($row["pi_invoice_date"]);
		//$pi_invoice_date=$row['pi_invoice_date']=date('d-m-Y', $pi_invoice_date);  
		
		if($row["pi_invoice_date"] == "0000-00-00"){
			$pi_invoice_date = "-";
		}else{
			$pi_invoice_date=date('d-m-Y', strtotime($row["pi_invoice_date"]));
		}

		if($row["pi_valid_date"] == "0000-00-00"){
			$pi_valid_date = "-";
		}else{
			$pi_valid_date=date('d-m-Y', strtotime($row["pi_valid_date"]));
		}
		$comp_id = $row['comp_id'];
	}
}
$cmd1 = "SELECT comp_name, comp_add, comp_cont_no1, comp_email FROM `company_master` WHERE comp_id = '$comp_id' ";	
$result1 = $connect->query($cmd1);
if ($result1->num_rows > 0) {
	if($row1 = $result1->fetch_assoc()) {
		$comp_name = $row1["comp_name"];
		$comp_add = $row1["comp_add"];
		$comp_cont_no1 = $row1["comp_cont_no1"];
		$comp_email = $row1["comp_email"];
	}
}

$account_name = "";
$cmd = "SELECT client_id, client_name FROM  client_master where client_id='$account_id'";
$result = $connect->query($cmd);
if ($result->num_rows > 0) {
	if($row = $result->fetch_assoc()) {
		$account_name=$row['client_name'];
	}
}

$cmd12 = "SELECT SUM(paid_amount) as paid_amt FROM pro_forma_receipt_payment WHERE pi_no = '$pi_no'";
$result12 = $connect->query($cmd12);
if($result12->num_rows > 0){
	if($row12 = $result12->fetch_assoc()){
		$paid_amount1 = $row12['paid_amt'];
	}
}
$paid_amount1 = round($paid_amount1);
$html = '
<style>
input[type=text] {
	display: none !important;
	hight:0px;
}

p{
	font-size:11.2pt;
	text-align:justify;
}

@page {
	margin: 10px 25px;
}
.underline{
	text-decoration:underline;
}
table{
	width:100%;
	border-collapse:collapsed;
	float:left;
}
tr, td, th{
	border:solid 1px #444;
	border-collapse:collapsed;
	padding:6px;
	float:left;
}
th{
	background:#eee;
}
@import url(https://fonts.googleapis.com/css?family=Open+Sans);

*{
	font-family: Arial;
}
.col-md-1 {float:left; width: 8.33%;}
.col-md-2 {float:left; width: 16.66%;}
.col-md-3 {float:left; width: 25%;}
.col-md-4 {float:left; width: 33.33%;}
.col-md-5 {float:left; width: 41.66%;}
.col-md-6 {float:left; width: 50%;}
.col-md-7 {float:left; width: 58.33%;}
.col-md-8 {float:left; width: 66.66%;}
.col-md-9 {float:left; width: 75%;}
.col-md-10 {float:left; width: 83.33%;}
.col-md-11 {float:left; width: 91.66%;}
.col-md-12 {float:left; width: 100%;}
.center{ text-align:center; }
.tbl_no_padding, tr{
	padding:0;
}
.no_border{
	border:none !important;
}
h4, p, td, tr, h3, th, b, h5, .head_title{
  font-family:"Times New Roman" !important;
  font-family:Arial !important;
  font-size:9.5pt !important; 
  line-height:15px !important;
}
</style>

<div style="height:924px;">
<div class="col-md-12" style="text-align:center;">
<br>
<img style="width:170px;" src="images/logo.png"/>
<br><br>
</div>
<table>
<tr>
<td align="center" width="320">
<p>
'.$comp_add.' Tel.No-'.$comp_cont_no1.' / '.$comp_email.' <br>
</p>				
</td>
</tr>
</table>
<div class="col-md-12"><h4 style="font-size:16pt;text-align:center;font-weight:bold;">Customer Payment Details </h4>
</div>

<div class="col-md-6">
<h4 style="font-size: 11pt;"><b>Customer Name : </b>'.$account_name.'</h4>
<h4 style="font-size: 11pt;"><b>PI Number :  </b>'.$pi_custom_number.'</h4>
<h4 style="font-size: 11pt;"><b>PI Date :  </b>'.$pi_invoice_date.'</h4>
<h4 style="font-size: 11pt;"><b>Customer LIC Number :  </b>'.$supplier_lic_no.'</h4>
</div>
<div class="col-md-6">
<h4 style="font-size: 11pt;"><b>Customer LIC Date : </b>'.$supplier_lic_date.'</h4>
<h4 style="font-size: 11pt;"><b>PI Valid Upto : </b>'.$pi_valid_date.'</h4>
<h4 style="font-size: 11pt;"><b>Total Amount : </b>'.$grand_total.'</h4>
<h4 style="font-size: 11pt;"><b>PI Payment Status : &nbsp; </b>';

if($paid_amount1 != $grand_total)
{
	$html.='<button class="btn btn-warning btn-sm btn-round">Pending</button>';
}
elseif($paid_amount1 == $grand_total)
{
	$html.='<button class="btn btn-success btn-sm btn-round">Clear</button>';
};
$html.=' 
</h4>
</div>
<table>
<tr>
<th colspan="10" style="width:100%;text-align:center;"> Payment Details
</b></th>
</tr>
<tr>
	<tr>              
		<th>Bill Date</th>
		<th>Paid Amount</th>
		<th>Payment Mode</th>
	</tr>
</tr>
';	

$final_paid_amount = 0; $total_amount = 0; 
$cmd4 = "SELECT pr.total_amount, pr.remain_amount, pr.paid_amount, pr.paid_date, p.payment_mode, p.payment_method_id FROM pro_forma_receipt_payment pr INNER JOIN customer_information p ON p.payment_method_id = pr.payment_mode WHERE pr.pi_no=".$pi_no." ORDER BY pr.customer_receipt_id ";
$result4 = $connect->query($cmd4);
while($row4 = $result4->fetch_assoc()) 
{
	if($row4['paid_date'] == "0000-00-00"){
		$paid_date = "-";
	}else{
		$paid_date = strtotime($row4['paid_date']);
		$paid_date = date('d-m-Y', $paid_date); 
	}
	$total_amount=$row4['total_amount'];
	$remain_amount=$row4['remain_amount'];
	$paid_amount=round($row4['paid_amount']);
	$payment_mode = $row4['payment_mode'];
	$payment_mode_details = "";
	if($payment_mode == "By Cash")
	{
		$cmd2 = "SELECT voucher_number, voucher_date, received_by FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			if($row2 = $result2->fetch_assoc()) {    
				$payment_mode_details.= "Voucher No. - ".$row2['voucher_number']."<br>";
				$date_stamp = strtotime($row2['voucher_date']);
				$payment_mode_details.= "Voucher Date - ".date('d-m-Y', $date_stamp)."<br>";       
				$payment_mode_details.= "Received By - ".$row2['received_by']."<br>";
			}
		}
	}
	else if($payment_mode == "By Cheque")
	{
		$cmd2 = "SELECT cheque_number, cheque_date, cheque_bank_name FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$payment_mode_details.= "Cheque No. - ".$row2['cheque_number']."<br>";
				$date_stamp = strtotime($row2['cheque_date']);
				$payment_mode_details.= "Cheque Date - ".date('d-m-Y', $date_stamp)."<br>";       
				$payment_mode_details.= "Bank Name - ".$row2['cheque_bank_name']."<br>";
			}
		}
	}
	else if($payment_mode == "By DD")
	{
		$cmd2 = "SELECT dd_number, dd_date, dd_bank_name FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$payment_mode_details.= "DD No. - ".$row2['dd_number']."<br>";
				$date_stamp = strtotime($row2['dd_date']);
				$payment_mode_details.= "DD Date - ".date('d-m-Y', $date_stamp)."<br>";       
				$payment_mode_details.= "Bank Name - ".$row2['dd_bank_name']."<br>";
			}
		}
	}
	else if($payment_mode == "By NEFT")
	{
		$cmd2 = "SELECT utr_number_neft, neft_date, bank_details_neft FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$payment_mode_details.= "NEFT No. - ".$row2['utr_number_neft']."<br>";
				$date_stamp = strtotime($row2['neft_date']);
				$payment_mode_details.= "NEFT Date - ".date('d-m-Y', $date_stamp)."<br>";  
				$payment_mode_details.= "Bank Name - ".$row2['bank_details_neft']."<br>";
			}
		}
	}
	else if($payment_mode == "By RTGS")
	{
		$cmd2 = "SELECT utr_number_rtgs, rtgs_date, bank_details_rtgs FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$payment_mode_details.= "RTGS No. - ".$row2['utr_number_rtgs']."<br>";
				$date_stamp = strtotime($row2['rtgs_date']);
				$payment_mode_details.= "RTGS Date - ".date('d-m-Y', $date_stamp)."<br>";  
				$payment_mode_details.= "Bank Name - ".$row2['bank_details_rtgs']."<br>";
			}
		}
	}
	else if($payment_mode == "By TT")
	{
		$cmd2 = "SELECT tt_date, tt_ref_number, bank_details_tt FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$date_stamp = strtotime($row2['tt_date']);
				$payment_mode_details.= "TT Date - ".date('d-m-Y', $date_stamp)."<br>";  
				$payment_mode_details.= "Ref. No. - ".$row2['tt_ref_number']."<br>";
				$payment_mode_details.= "Bank Name - ".$row2['bank_details_tt']."<br>";
			}
		}
	}
	else if($payment_mode == "By LT")
	{
		$cmd2 = "SELECT lt_date, lt_ref_number, bank_details_lt FROM customer_information WHERE payment_method_id = ".$row4['payment_method_id']." ";
		$result2 = $connect->query($cmd2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {    
				$date_stamp = strtotime($row2['lt_date']);
				$payment_mode_details.= "LT Date - ".date('d-m-Y', $date_stamp)."<br>";  
				$payment_mode_details.= "Ref. No. - ".$row2['lt_ref_number']."<br>";
				$payment_mode_details.= "Bank Name - ".$row2['bank_details_lt']."<br>";
			}
		}
	}
	$html .='
	<tr>            
	<td class="center">'.$paid_date.'</td>
	<td class="center">'.$paid_amount.'</td>
	<td class="center">'.$payment_mode.'<br>'.$payment_mode_details.'</td>
	';
	$final_paid_amount = $paid_amount + $final_paid_amount;
}
$final_total_amount = $total_amount;
$final_remaining_amount = $grand_total - $final_paid_amount;

$html .='
</table>
<h3 style="text-align:center">Total Amount : '.$grand_total.' &nbsp; &nbsp; &nbsp; Total Paid : '.$final_paid_amount.'  &nbsp; &nbsp; &nbsp; Total Remaining : '.$final_remaining_amount.'</h3>
<br>
';

require '../vendor/autoload.php';
use Mpdf\Mpdf;
	$mpdf = new Mpdf([
		'mode' => 'utf-8', // Optional: Encoding
		'format' => 'A4', // Page size
		'mirrorMargins' => true, // Mirrored margins
	]);
	$mpdf->SetDisplayMode('fullpage', 'two');
	$mpdf->WriteHTML($html);
	$mpdf->Output();

exit;
?>
