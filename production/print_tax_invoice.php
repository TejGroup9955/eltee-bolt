<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("../configuration.php");
session_start();
$comp_id = $_SESSION['comp_id']; 
$Pi_No = base64_decode($_GET['PI_No']);
$rstpro = mysqli_query($connect,"select p.*,c.client_name,c.client_add,c.kind_attention,
co.countryName as CountryOfOrigin,cs.countryName as CountryOfShipment,cc.countryName as CurrencyCountry,
ccs.currency_code,t.invoice_number,t.invoice_date,t.bl_no,t.vessal_name,
bd.bank_id,bd.branch,bd.bank_address,bd.po_box,bd.account_name,bd.account_no,bd.iban_no,bd.swift_code,
pt.port_name,i.incoterms,i.incoterms_fullform,py.pay_percentage,py.after_percentage,py.payment_desc_id,
py.after_payment_desc_id
from pro_forma_head p
inner join tax_invoice_head t on t.pi_no=p.pi_no 
inner join client_master c on c.client_id = p.account_id
inner join country_master co on co.id=p.country_of_origin
inner join country_master cs on cs.id=p.country_of_supply
inner join country_master cc on cc.id=p.country_id
left join country_master ccs on ccs.id=p.currency_id
inner join bank_details bd on bd.id=p.bank_detail_id
inner join port_master pt on pt.port_master_id=p.port_id
inner join incoterms_master i on i.incoterms_id=p.incoterms_id
inner join pro_forma_payment py on py.pi_no=p.pi_no
where p.pi_no='$Pi_No'");

if(mysqli_num_rows($rstpro)>0)
{
    $rwpro = mysqli_fetch_assoc($rstpro);
    extract($rwpro);
    
    $rstcomp = mysqli_query($connect,"select * from company_master c where c.comp_id ='$comp_id' ");
    $rwcomp = mysqli_fetch_assoc($rstcomp);
    extract($rwcomp);

    $rstbank = mysqli_query($connect,"select * from bank_master where bank_id ='$bank_id'");
    $rwbank = mysqli_fetch_assoc($rstbank);
    extract($rwbank);

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
    // $rstproDe = mysqli_query($connect,"select ")


    // $rstpayment = mysqli_query($connect,"select * from payment_terms")
    $html=' <style>
        body { font-family: Arial, sans-serif; margin: 20px;font-size:11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left;line-height:15px; }
        .header, .footer { text-align: center; font-weight: bold; margin-bottom: 10px; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .no-border{border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important; }
        .no-border-side{border-left:0 !important; border-right:0 !important; }
        .right{ text-align:right; }
        .center{ text-align:center; }
    </style>';
    $html.='
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th class="no-border" style="text-align: left; vertical-align: middle; width: 30%;">
                    <img src="images/logo.png" style="width:150px;margin-left:-20px;">
                </th>
                <th class="no-border" style="text-align: right; vertical-align: middle; width: 70%;">
                    <h2 style="margin: 0;">Tax Invoice</h2><br><br>
                    <p style="margin: 5px 0;">
                        Invoice Number: '.$rwpro['invoice_number'].'</b><br>
                        Invoice Date: '.date('d M Y',strtotime($rwpro['invoice_date'])).'</b><br>
                        <b>PI Number: '.$rwpro['pi_custom_number'].'<br>
                        Date: '.date('d M Y',strtotime($rwpro['pi_invoice_date'])).'</b><br>
                        
                    </p>
                    
                </th>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%;">
                    <strong>SELLER: '.$rwcomp['comp_name'].'</strong><br> 
                    Address: '.$rwcomp['comp_add'].'
                </td>
                <td style="width: 50%;">
                      <strong>BUYER: '.$rwpro['client_name'].'</strong><br> 
                        Address:'.$rwpro['client_add'].'<br>
                        Contact Person : '.$rwpro['kind_attention'].'
                </td>
            </tr>
            <tr><td colspan="2" style="background-color:#1a7af1;color:white;">SHIPMENT DETAILS</td></tr>
            <tr>
                <td style="width: 50%;">
                    <strong>TIME OF SHIPMENT : </strong>'.$time_of_shipment.'<br> 
                    <strong>COUNTRY OF ORIGIN : </strong>'.$CountryOfOrigin.'<br> 
                    <strong>COUNTRY OF SUPPLY : </strong>'.$CountryOfShipment.'<br> 
                    <strong>PORT OF LOADING : </strong>'.$port_of_loading_name.'<br> 
                    <strong>DESTINATION PORT : </strong>'.$destination_port_name.'<br> 
                </td>
                 <td style="width: 50%;">
                   <strong>PART SHIPMENT : </strong>'.$part_shipment.'<br>
                   <strong>TRANSSHIPMENT : </strong>'.$trans_shipment.'<br>
                   <strong>INSURANCE : </strong>'.$insurance.'<br>
                   <strong>MARKING : </strong>'.$marking.'<br>
                   <strong>INCOTERM : </strong>'.$incoterms.' - '.$incoterms_fullform.'<br>
                   <strong>PORT : </strong>'.$port_name.'( '.$country_name.' )<br>
                </td>
            </tr>
            <tr><td><strong>B/L NO : </strong> '.$bl_no.'</td><td><strong>VESSAL NAME : </strong> '.$vessal_name.'</td></tr>
        </table>
        <table>
            <tr style="background-color:#1a7af1;">
                <th style="color:white;" class="center">Sr. No</th>
                <th style="color:white;" class="center">DESCRIPTION OF GOODS</th>
                <th style="color:white;" class="center">PACKAGING</th>
                <th style="color:white;" class="center">NO. OF BAGS</th>
                <th style="color:white;" class="center">QUANTITY <br>(MT)</th>
                <th style="color:white;" class="center">PRICE IN '.$currency_code.' <br> (PER MT)</th>
                <th style="color:white;" class="center">TOTAL AMOUNT IN '.$currency_code.'</th>
            </tr>';

        $rstprod = mysqli_query($connect,"select d.*,p.product_name as product_name from pro_forma_head_details d inner join product_master p on p.product_id = d.product_id
where d.pi_no='$Pi_No'");
        if(mysqli_num_rows($rstprod)>0)
        {
            $procnt=1;$total = 0;
            while($rwprod = mysqli_fetch_assoc($rstprod))
            {
                $permtweight = $rwprod['total_weight']/1000;
                $html.='<tr>
                        <td class="center">'.$procnt.'</td>
                        <td class="center">'.$rwprod['product_name'].'</td>
                        <td class="center">'.$rwprod['each_bag_weight'].' kg '.$rwprod['packaging_type'].' bag</td>
                        <td class="center">'.$rwprod['no_of_bags'].'</td>
                        <td class="center">'.$permtweight.'</td>
                        <td class="center">'.$rwprod['rateperton'].'</td>
                        <td class="center">'.$rwprod['total_amt'].'</td>
                    </tr>';
                $total = $total + $rwprod['total_amt'];
                $procnt++;
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

        if($rwpro['DiscountAmt'] != 0){
            $txtheading = "Discount";
            if(strpos($rwpro['DiscountAmt'], "%") !== false){
                $txtheading = "Discount( % )";
            }else{
                $txtheading = "Discount Amount";
            }
            $html.='<tr>
                    <td colspan="5"></td>
                    <td class="center"><b>Sub Total</b></td>
                    <td class="center"><b>'.$total.'</b></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td class="center"><b>'.$txtheading.'</b></td>
                    <td class="center"><b>'.$rwpro['DiscountAmt'].'</b></td>
                </tr>';
        }
        $html.='<tr>
                        <td colspan="5"></td>
                        <td class="center"><b>Grand Total</b></td>
                        <td class="center"><b>'.$grand_total.'</b></td>
                </tr>
                <tr>
                    <th colspan="7" class="no-border-side">IN WORDS: '.$incoterms.' : '.$currency_code.' '.strtoupper($total_in_word).' ONLY </th>
                </tr>';
        
            
        $html.='</table>  
        <br>     
        <h3>OUR BANKERS </h3>
        <h4 >Beneficiary Bank details:  </h4>
        <p style="border-bottom:1px solid black;">
        Bank Name: '.$rwbank['bank_name'].'<br>
        Branch: '.$rwpro['branch'].'<br>
        Bank address: '.$rwpro['bank_address'].'<br>
        P.O. BOX : '.$rwpro['po_box'].'<br>
        Account name: '.$rwpro['account_name'].'<br>
        Account number: '.$rwpro['account_no'].'<br>
        IBAN No: '.$rwpro['iban_no'].'<br>
        Swift code: '.$rwpro['swift_code'].'        
        </p>';
        
        $html.='<h3>Payment Terms:</h3>';

        // $cmd2 = "SELECT payment_term FROM payment_terms WHERE comp_id = '$comp_id' AND status = 1";
        // $result2 = $connect->query($cmd2);
        // if($result2->num_rows > 0) {
        //     $i = 1;
        //     $html.="<table><tbody>";
        //     while ($row2 = $result2->fetch_assoc()) { 
        //         $html.='<tr><td style="border:none">'.$i.'.&emsp;'.$row2['payment_term'].'</td></tr>';
        //         $i++;
        //     }
        //     $html.="</tbody></table>";
        // }else{
        //      $html.='<p>Not Found</p>';   
        // }

        $html.="<table><tbody>";
        if(!empty($pay_percentage)){
            $html.='<tr><td style="border:none"><i style="font-size:8px" class="fa">&#9679;</i>&emsp;Advance '.$pay_percentage.'%&nbsp;'.@$rwpay['payment_description'].'</td></tr>';
        }
        if(!empty($after_percentage)){
            $html.='<tr><td style="border:none"><i style="font-size:8px" class="fa">&#9679;</i>&emsp;Balance '.$after_percentage.'%&nbsp;'.@$rwpay1['payment_description'].'</td></tr>';
        }
        $html.="</tbody></table>";

         $html.='<h3>Shipment Document Details:</h3>';

        $cmd1 = "SELECT shipment_document_name FROM pro_forma_head_shipment_detail WHERE pi_no = '$Pi_No'";
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

        $html.='<h3>Terms / Conditions :</h3>';

        $cmd = "SELECT title,discription FROM pro_forma_head_termcondition_detail WHERE pi_no = '$Pi_No'";
        $result = $connect->query($cmd);
        if($result->num_rows > 0) {
            $html.="<table><tbody>";
            while ($row = $result->fetch_assoc()) { 

                $html.='<tr><td style="border:none"><i style="font-size:8px" class="fa">&#9679;</i>&emsp;<b>'.$row['title'].'</b></td></tr><tr><td style="border:none">&emsp;&emsp;'.$row['discription'].'</td></tr>';
            }
            $html.='<tr><td style="border:none"><i style="font-size:8px" class="fa">&#9679;</i>&emsp;<b>Validity of contract: </b></td></tr><tr><td style="border:none">&emsp;&emsp;The present contract comes into force from the date of its signing till its fulfilment. The buyer must send back the contract with
            his or her signature to confirm the deal within three working days from the issuing date.</td></tr>';
            $html.="</tbody></table>";
        }else{
             $html.='<p>NA</p>';   
        }

       
        // <h3>Claim & Discharge Terms:</h3>
        // <p>Buyer must notify seller of any claims within 30 days of receipt.</p>
        // <p>Buyer is responsible for all charges at discharge port.</p>
        $html.='
        <table>
	        <tr>
	            <td class="no-border text-center" >
                    <img src="images/sign.png" style="width:180px;">
	            	<br><b>Seller\'s Signature & Seal</b>
	            </td>
	            <td class="no-border text-center" style="width:35%;padding-top:120px;">
	            	
	            	<br><b>Buyer\'s Signature & Seal</b>
	            </td>
	        </tr>
	    </table>';
}
else
{
    $html="Pro-Forma Not Found";
} 
// echo $html;exit();
    require '../vendor/autoload.php';
    use Mpdf\Mpdf;
    $mpdf = new Mpdf([
        'mode' => 'utf-8', // Optional: Encoding
        'format' => 'A4', // Page size
        'mirrorMargins' => true, // Mirrored margins
    ]);
    $mpdf->SetDisplayMode('fullpage', 'two');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Pro-Forma.pdf', 'I');
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