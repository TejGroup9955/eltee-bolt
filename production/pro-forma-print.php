<?php
include_once("../configuration.php");
session_start();
$comp_id = $_SESSION['comp_id']; 
$Pi_No = base64_decode($_GET['pi_no']);
$rstpro = mysqli_query($connect,"select p.*,c.client_name,c.client_add,c.kind_attention from pro_forma_head p
inner join client_master c on c.client_id = p.account_id where p.pi_no='$Pi_No'");
if(mysqli_num_rows($rstpro)>0)
{
    $rwpro = mysqli_fetch_assoc($rstpro);
    extract($rwpro);

    $rstcomp = mysqli_query($connect,"select * from company_master c where c.comp_id ='$comp_id' ");
    $rwcomp = mysqli_fetch_assoc($rstcomp);
    extract($rwcomp);
    // $rstproDe = mysqli_query($connect,"select ")


    // $rstpayment = mysqli_query($connect,"select * from payment_terms")
    $html=' <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header, .footer { text-align: center; font-weight: bold; margin-bottom: 10px; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .no-border{border-top:0 !important; border-bottom:0 !important; border-left:0 !important; border-right:0 !important; }
    </style>';
    $html.='
        <div class="header"><img src="images/logo.png" style="width:250px;"></div>
        <div class="header" style="text-decoration:underline;">Proforma Invoice & Sales Contract</div>
        <p style="margin-top:40px;"><strong>Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>  '.date('d M Y',strtotime($rwpro['pi_invoice_date'])).'<br>
        <strong>PI Number:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>'.$rwpro['pi_custom_number'].'<br>
        <strong>Seller: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; '.$rwcomp['comp_name'].'</strong><br> 
        <strong>Address: </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$rwcomp['comp_add'].'</p>
        
        <p><strong>Buyer:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;  '.$rwpro['client_name'].',</strong>
        <br><strong>Address: </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>'.$rwpro['client_add'].' <br>
        <strong>Contact Person:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rwpro['kind_attention'].'</p>
        
        <table>
            <tr>
                <th>NO. & KIND OF PACKAGES</th>
                <th>DESCRIPTION OF GOODS</th>
                <th>QUANTITY (MT)</th>
                <th>RATE IN USD <br>CFR NHAVASHEVA</th>
                <th>AMOUNT IN USD<br>CFR</th>
            </tr>
            <tr>
                <td>03 x 20\' FCL</td>
                <td>MONOAMMONIUM PHOSPHATE N:P:K: 12:61:00 (100% WATER SOLUBLE) FERTIGATION GRADE</td>
                <td>78.00</td>
                <td>1040.00</td>
                <td>81,120.00</td>
            </tr>
            <tr>
                <td colspan="4">IN WORDS: CFR: USD EIGHTY ONE THOUSAND ONE HUNDRED AND TWENTY ONLY</td>
               
                <td><b>81,120.00</b></td>
            </tr>
        </table>        
        <h3>OUR BANKERS </h3>
        <h4>Beneficiary Bank details:  </h4>
        <p>
        Bank Name: '.$rwcomp['bank_name'].'<br>
        Branch: '.$rwcomp['bank_branch'].'<br>
        Bank address: '.$rwcomp['bank_address'].'<br>
        P.O. BOX 6564: '.$rwcomp['po_box'].'<br>
        Account name: '.$rwcomp['account_name'].'<br>
        Account number: '.$rwcomp['account_no'].'<br>
        IBAN No: '.$rwcomp['iban_no'].'<br>
        Swift code: '.$rwcomp['swift_code'].'        
        </p>
        
        <h3>Payment Terms:</h3>
        <p>1. 100% payment against scan copy of OBL.<br> 
        2. Failure to pay grants seller the right to alter shipment, cancel contract, or retain deposit.</p>
        
        <p >Failure of the buyer to abide by the payment clause will mean seller has the right to alter 
            the shipment position, and / or price of the contract, and / or to cancel the contract 
            altogether, and / or keep any proportion of the part deposit payment as damages for such 
            failure to abide by the payment clause. 
        </p>
        <h3>Shipment Details:</h3>
        <p><strong>Country of Origin:</strong> China</p>
        <p><strong>Destination Port:</strong> Nhava Sheva, India</p>
        <p><strong>Insurance:</strong> On Seller\'s Account</p>
        
        <h3>Claim & Discharge Terms:</h3>
        <p>Buyer must notify seller of any claims within 30 days of receipt.</p>
        <p>Buyer is responsible for all charges at discharge port.</p>
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

?>