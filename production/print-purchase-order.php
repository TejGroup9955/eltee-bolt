<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
include_once("../configuration.php");
session_start();
$comp_id = 1; 
$po_id = base64_decode($_GET['po_id']);

$query = mysqli_query($connect,"select p.*,py.*,c.client_name,c.client_business,c.client_add,u.user_name,cm.currency_code
 from purchase_order p 
    inner join client_master c on c.client_id = p.supplier_id
    inner join user_master u on u.user_id = p.user_id
    inner join purchase_order_payment py on py.purchase_order_id=p.po_id
    left join country_master cm on cm.id=p.currency_id 
    where p.po_id='$po_id'");

if(mysqli_num_rows($query)>0)
{
    $rwpro = mysqli_fetch_assoc($query);
    extract($rwpro);

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
        margin: 40px 60px;
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
    ol.upper-roman {list-style-type: upper-roman !important;}
    .border_none{
        border:none;
    }
    h4, p, td, tr, h3, th, b, h5, .head_title{
        font-family:"Times New Roman" !important;
        font-family:Arial !important;
        font-size:9.5pt !important;	
        line-height:15px !important;
    }
    </style>

    <div >
    <div class="col-md-12" style="text-align:center;">
    
    <img style="width:150px;" src="images/logo.png"/>
    <br>
    </div>
    <div class="col-md-12"><h4 style="font-size:16pt;text-align:center;font-weight:bold;">PURCHASE ORDER </h4>
    </div>

    <table>
    <tr>
    <td style="width:55%;">
    <h4>SUPPLIER / EXPORTER : <br>'.$client_name.' </h4>	
    <p>'.$client_add.'</p>	
    </td>
    <td class="tbl_no_padding no_border">
    <table class="tbl_no_padding no_border">
    <tr style="border-left:none; border-right:none;">
    <td ><b>P.O.No. : </b></td>
    <td>'.$po_custom_number.'</td>
    </tr>
    <tr style="border-left:none; border-right:none;">
    <td><b>Date : </b></td>
    <td>'.date('d-m-Y',strtotime($po_date)).'</td>
    </tr>
    <tr style="border-left:none; border-right:none;">
    <td><b>Supplier PI No. : </b></td>
    <td>'.$supplier_pi_no.'</td>
    </tr>
    <tr style="border-left:none; border-right:none;">
    <td><b>Supplier PI Date : </b> </td>
    <td>'.date('d-m-Y',strtotime($supplier_pi_date)).'</td>
    </tr>
    </table>
    </td>
    </tr>
    </table>

    <br>

    <table>
    <tr>
        <td>Sr No.</td>
        <td>Product Name</td>
        <td>Qty (MT)</td>
        <td>Rate/MT ('.$currency_code.')</td>
        <td>Total Amt.</td>
    </tr>';
    $gst = 0; $i=1; $total = 0;
    $cmd13 = "select p.*, pp.product_name,pp.status,u.u_name from purchase_order_details p 
                                            inner join product_master pp on pp.product_id= p.product_id 
                                            inner join uom_master u on u.u_id=pp.uom_id
                                            where p.purchase_order_id='$po_id'";
    $result3 = $connect->query($cmd13);
    if ($result3->num_rows > 0) 
    {
        while($row3 = $result3->fetch_assoc()) 
        {
            extract($row3);
            $qtyInMT = $total_weight/1000;
            $html .='
            <tr>
                <td>'.$i.'</td>
                <td>'.$product_name.'</td>
                <td>'.$qtyInMT.'</td>
                <td>'.$rateperton.'</td>
                <td>'.$total_amt.'</td>
            </tr>';
            $total = $total + $total_amt;
            $i++;
        }
    }

	$grand_total = $rwpro['grand_total'];
	$no_in_word = numToWordsRec($grand_total);
        $pieces = explode(' ', $no_in_word);
        $last_word = array_pop($pieces);
        if($last_word == "zero"){
            $total_in_word = rtrim($no_in_word,'zero');
        }else{
            $total_in_word = $no_in_word;
        }

        if($rwpro['discount'] != 0){
            $txtheading = "Discount";
            if(strpos($rwpro['discount'], "%") !== false){
                $txtheading = "Discount( % )";
            }else{
                $txtheading = "Discount Amount";
            }
            $html.='<tr>
                    <td colspan="3"></td>
                    <td class="right"><b>Sub Total</b></td>
                    <td class="right"><b>'.$total.'</b></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="right"><b>'.$txtheading.'</b></td>
                    <td class="right"><b>'.$rwpro['discount'].'</b></td>
                </tr>';
        }
        $html.='<tr>
                        <td colspan="3"></td>
                        <td class="right"><b>Grand Total</b></td>
                        <td class="right"><b>'.$grand_total.'</b></td>
                </tr>
                <tr>
                    <th colspan="6" class="no-border-side">IN WORDS: CFR : USD '.strtoupper($total_in_word).' ONLY </th>
                </tr>
        
	</table>';

	$paymentdesc = mysqli_query($connect,"select * from payment_description where payment_desc_id ='$payment_desc_id'");
    if(mysqli_num_rows($paymentdesc)>0)
    {
        $rwpay = mysqli_fetch_assoc($paymentdesc);
        extract($rwpay);
    }

    $paymentdesc1 = mysqli_query($connect,"select * from payment_description where payment_desc_id ='$after_payment_desc_id'");
    if(mysqli_num_rows($paymentdesc1)>0)
    {
        $rwpay1 = mysqli_fetch_assoc($paymentdesc1);
        extract($rwpay1);
    }

    $paymentdesc2 = mysqli_query($connect,"select * from payment_mode where payment_mode_id ='$payment_mode_id'");
    if(mysqli_num_rows($paymentdesc2)>0)
    {
        $rwpay2 = mysqli_fetch_assoc($paymentdesc2);
        extract($rwpay2);
    }

	$html .='
		<table>
		<tr>
			<th colspan="9" style="width:100%;text-align:center;"> Payment Details</b></th>
		</tr>
		<tr>
			<th colspan="1" >Payment Mode </th>
			<th colspan="2" >Advance % <br>('.$currency_code.')</th>
			<th colspan="1" >Description </th>
			<th colspan="2" >After % <br>('.$currency_code.')</th>
			<th colspan="1" >Description </th>
		</tr>
		';
		
			$html .='
			<tr>
			<td colspan="1" style="text-align:center;"> '.@$rwpay2['payment_mode_name'].'. </td>
			<td colspan="2" style="text-align:center;"> '.@$pay_percentage.' % ('.@$pay_in_advance.').</td>
			<td colspan="1"> '.@$rwpay['payment_description'].'.</td>
			<td colspan="2" style="text-align:center;"> '.@$after_percentage.' % ('.@$pay_later.') .</td>
			<td colspan="1"> '.@$rwpay1['payment_description'].'.</td>
			</tr>
			';
		
		$html .='</table>';
		
		$cmd = "SELECT title,discription FROM purchase_order_termscondition_detail WHERE purchase_order_id = '$po_id'";
        $result = $connect->query($cmd);
        if($result->num_rows > 0) {
        	$inc=1;
        	$html.= "<br><h4><u>Terms And Conditions</u></h4>";
            while ($row_4 = $result->fetch_assoc()) { 

                $title2 = $row_4["title"];

				$html .= "<li style='list-style-type:none;'><div class='col-md-1 center'>".$inc."</div>";    
				$terms_description = $row_4['discription'];
				$term = strip_tags($terms_description);
				$term2 = str_replace("@@", "<br>", $term);
				$term3 = str_replace(array("\r\n", "\n", "\r"), "<br>", $term2);
				$html .="<div class='col-md-11'><u><b>".$title2."</b></u> - ".$term3."</div></li>";
				$inc++;
            }
            
        }
        
        $html.='<h3>Shipment Document Details:</h3>';

        $cmd1 = "SELECT shipment_document_name FROM purchase_order_shipment_detail WHERE po_no = '$po_id'";
        $result1 = $connect->query($cmd1);
        if($result1->num_rows > 0) {
            $html.="<table><tbody>";
            while ($row1 = $result1->fetch_assoc()) { 
                $html.='<tr><td style="border:none"><img src="images/checkmark.jpg" style="width:20px;">&emsp;'.$row1['shipment_document_name'].'</td></tr>';
            }
            $html.="</tbody></table>";
        }else{
             $html.='<p>NA</p>';   
        }
	
	
	$html .='</div>';
	$html .='
    <br><br>
	<div class="col-md-6" style="text-align:left;" >
		
		<p style="margin-top:130px;"></p>
		<p style="text-align:center; margin-left: -128px;line-height:4px;">Buyers Seal & Sign</p>
		<p style="text-align:center; margin-left: -128px;line-height:4px;">Authorized Signatory</p>
	</div>
	<div class="col-md-6" style="text-align:right;" >
		
		<p style="margin-top:130px;"></p>
		<p style="text-align:center; margin-right: -150px;line-height:4px;"> Sellers Seal & Sign</p>
		<p style="text-align:center;margin-right: -150px; "><b>'.$client_name.'</b> </p>
		<p style="text-align:center;margin-right: -150px; line-height:4px;">Authorized Signatory </p>
	</div>
	
	';	

	$html .='<br><br><br>
	
	';	
}
else
{
    $html="Purchase Order Not Found";
}  
// echo $html;
// exit();
require '../vendor/autoload.php';
    use Mpdf\Mpdf;
    $mpdf = new Mpdf([
        'mode' => 'utf-8', // Optional: Encoding
        'format' => 'A4', // Page size
        'mirrorMargins' => true, // Mirrored margins
    ]);
    $mpdf->SetDisplayMode('fullpage', 'two');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Purchase-Order.pdf', 'I');
    exit;


// function numToWordsRec($number) {
//     $words = array(0 => 'zero', 1 => 'one', 2 => 'two',3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety');

//     if ($number < 20) {
//         return $words[$number];
//     }

//     if ($number < 100) {
//         return $words[10 * floor($number / 10)] .
//                ' ' . $words[$number % 10];
//     }

//     if ($number < 1000) {
//         return $words[floor($number / 100)] . ' hundred ' 
//                . numToWordsRec($number % 100);
//     }

//     if ($number < 1000000) {
//         return numToWordsRec(floor($number / 1000)) .
//                ' thousand ' . numToWordsRec($number % 1000);
//     }

//     return numToWordsRec(floor($number / 1000000)) .
//            ' million ' . numToWordsRec($number % 1000000);
// }

function numToWordsRec($number) {
    $words = array(
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen',
        17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
        20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty',
        60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );

    if ($number == 0) {
        return 'zero';
    }

    $result = '';

    if ($number >= 1000000) {
        $result .= numToWordsRec(floor($number / 1000000)) . ' million ';
        $number %= 1000000;
    }
	if ($number >= 100000) {
        $result .= numToWordsRec(floor($number / 100000)) . ' hundred ';
        $number %= 100000;
    }

    if ($number >= 1000) {
        $result .= numToWordsRec(floor($number / 1000)) . ' thousand ';
        $number %= 1000;
    }

    if ($number >= 100) {
        $result .= $words[floor($number / 100)] . ' hundred ';
        $number %= 100;
    }

    if ($number > 0) {
        if ($number < 20) {
            $result .= $words[$number];
        } else {
            $result .= $words[10 * floor($number / 10)];
            if ($number % 10 > 0) {
                $result .= ' ' . $words[$number % 10];
            }
        }
    }

    return trim(preg_replace('/\s+/', ' ', $result));
}
?>

