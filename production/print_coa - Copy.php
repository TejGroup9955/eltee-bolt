<?php

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
        <strong>Pure Grow Agri Solutions Pvt. Ltd.</strong><br>
        Address: GAT NO. 1080, PIR VASTI, VILLAGE URULI DEVACHI, PUNE - 412308, MAHARASHTRA, INDIA.<br>
        IEC: AAMCP2662E, GST: 27AAMCP2662E1ZB, PAN: AAMCP2662E
    </div>

    <div class="section-title">
        We hereby certify that the composition of our product -
    </div>

    <div class="details">
        MONOAMMONIUM PHOSPHATE N:P:K 12:61:00 25KG BAGS (100% WATER SOLUBLE) FERTIGATION GRADE FOR AGRICULTURAL USE ONLY.<br>
        HS CODE: 31054000<br>
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
        <tbody>
            <tr><td>1</td><td class="left">Moisture per cent by weight maximum</td><td>0.50</td><td>0.19</td></tr>
            <tr><td>2</td><td class="left">Ammoniacal Nitrogen per cent by weight minimum</td><td>12.00</td><td>12.04</td></tr>
            <tr><td>3</td><td class="left">Water Soluble Phosphorous (as P<sub>2</sub>O<sub>5</sub>) per cent by weight minimum</td><td>61.00</td><td>61.23</td></tr>
            <tr><td>4</td><td class="left">Sodium as NACL per cent by weight maximum</td><td>0.50</td><td>0.03</td></tr>
            <tr><td>5</td><td class="left">Matter insoluble in water per cent by weight maximum</td><td>0.50</td><td>0.01</td></tr>
            <tr><td>6</td><td class="left">Appearance</td><td>White Crystal</td><td>Ok</td></tr>
        </tbody>
    </table>

    <div class="details">
        WEIGHT / QUANTITY: 78 MTS IN 03 FCLS 20’ PACKED IN 3120 BAGS<br>
        TOTAL: NET WEIGHT: 78,000.00 KGS &nbsp;&nbsp; GROSS WEIGHT: 78,312.00 KGS
    </div>

    <br>
    <img src="images/sign.png" style="width:180px;"><br>

    <div class="details"><strong>Date:</strong> 23/11/2024</div>

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
