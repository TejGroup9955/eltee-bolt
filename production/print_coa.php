<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("../configuration.php");
session_start();
//print_r($_SESSION);
//exit;
$Pi_No = base64_decode($_GET['PI_No']);

$rstpro = mysqli_query($connect,"select p.*,c.client_name,c.client_add,c.GST_no,c.kind_attention
from pro_forma_head p
inner join client_master c on c.client_id = p.account_id
where p.pi_no='$Pi_No'");
if(mysqli_num_rows($rstpro)>0)
{
    $rwpro = mysqli_fetch_assoc($rstpro);
    extract($rwpro);
    
$html = '
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 13px;
        letter-spacing: 0.2px;
        line-height: 1.6;
    }
    .container { padding: 0px; }
    .header {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
    }
    .section-title {
        margin-top: 20px;
        font-weight: bold;
    }
    .details, .table, .statement {
        margin-top: 10px;
        width: 100%;
    }
    .table th, .table td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }
    .table {
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 12.5px;
    }
    .note {
        font-size: 12px;
        margin-top: 15px;
    }
    .left {
        text-align: left;
    }
</style>

<div class="col-md-12" style="text-align:center;">
    <img style="width:200px;" src="images/logo.png"/>
    <hr style="color:#094ea5;">
</div>

<div class="container">
    <div class="header">
        <u>CERTIFICATE OF ANALYSIS</u>
    </div>

    <div class="details">
        <strong>To:</strong><br>
        <strong>'.$rwpro['client_name'].'</strong><br>
        Address: '.$rwpro['client_add'].'<br>
        GST: '.$rwpro['GST_no'].'
    </div>

    <div class="section-title">
        We hereby certify that the composition of our product -
    </div>';

     $rstprod = mysqli_query($connect,"select d.*,p.product_id as product_id,p.product_name as product_name,p.product_code as product_code from pro_forma_head_details d inner join product_master p on p.product_id = d.product_id
where d.pi_no='$Pi_No'");
        if(mysqli_num_rows($rstprod)>0)
        {
            $procnt=1;$total = 0;
            while($rwprod = mysqli_fetch_assoc($rstprod))
            {
                $html.='
                        <div class="details">
                            '.$rwprod['product_name'].'<br>
                            HS CODE: '.$rwprod['product_code'].'<br>
                            PRODUCTION DATE: SEP 7, 2024<br>
                            EXPIRY DATE: SEP 6, 2026<br>
                            BATCH NO: 5111052MAP24003
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Item</th>
                                    <th>Standard</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>';
                $product_id = $rwprod['product_id'];
                $rstprod1 = mysqli_query($connect,"select pd.product_description,pd.value,pp.product_range from 
                proforma_product_description pp 
                inner join product_description pd on pd.product_description_id = pp.description_id 
                where pp.po_no='$Pi_No' and pp.product_id ='$product_id'");
               
                if(mysqli_num_rows($rstprod1)>0)
                {   
                    $i = 1;
                    while($rwprod1 = mysqli_fetch_assoc($rstprod1))
                    {   
                        $html.=' <tr>
                            <td class="center">'.$i.'</td>
                            <td class="center">'.$rwprod1['product_description'].'</td>
                            <td class="center">'.$rwprod1['value'].'</td>
                            <td class="center">'.$rwprod1['product_range'].'</td>
                        </tr>';
                        $i++;
                    }
                }else{
                        $html.=' <tr>
                                    <td colspan="4" class="center">NO ITEMS</td>
                                 </tr>';
                }


                $html.= ' </tbody>
                        </table>        
                        <div class="details">
                            WEIGHT / QUANTITY: '.$rwprod['each_bag_weight'].'  PACKED IN '.$rwprod['no_of_bags'].' BAGS<br>
                            TOTAL: NET WEIGHT: '.floatval($rwprod['total_weight']).' KGS &nbsp;&nbsp; GROSS WEIGHT: '.floatval($rwprod['total_weight']).' KGS
                        </div><hr>';

                        
                $total = $total + $rwprod['total_amt'];
                $procnt++;
            }
        }

   
    
    $html.=' <img src="images/sign.png" style="width:180px;"><br>

    <div class="details"><strong>Date:</strong> '.date('d/m/Y',strtotime($COAPrintDate)).'</div>

    <div class="note">
        <strong>Statement:</strong><br>
        1. A copy of this report is invalid if altered.<br>
        2. The result relates only to the sample tested.
    </div>
    <br><hr> 
    <div style="text-align:center;">
        Unit No. 1309, 13th Floor, Fortune Tower, Cluster C,<br>
        Jumeirah Lake Towers, Dubai, U.A.E. Ph. No. +971 457 79865, info@elteedmcc.com<br><br>
    </div>
</div>
';
}
else
{
    $html="Pro-Forma Not Found";
} 

require '../vendor/autoload.php';
use Mpdf\Mpdf;

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'mirrorMargins' => true,
]);

$mpdf->SetDisplayMode('fullpage', 'two');
$mpdf->WriteHTML($html);
$mpdf->Output('Certificate-of-Analysis.pdf', 'I');
exit;
?>
