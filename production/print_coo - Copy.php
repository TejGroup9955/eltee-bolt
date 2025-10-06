<?php
$html = '
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 13px;
        line-height: 1.5;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    td, th {
        border: 1px solid #000;
        padding: 6px;
        vertical-align: top;
    }
    .no-border {
        border: none !important;
    }
    .header {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 10px;
    }
    .small {
        font-size: 12px;
    }
    .right-text {
        text-align: right;
    }
    .center-text {
        text-align: center;
    }
    .stamp-sign {
        width: 140px;
    }
</style>

<div>
    <div class="col-md-12" style="text-align:center;">
        <img style="width:200px;" src="images/logo.png"/>
        <hr style="color:#094ea5;">
    </div>
    <br><br>
    <table>
        <tr>
            <td colspan="2" width="50%"><strong>1. Exporter (name, address, country)</strong><br><br>
                <strong>ELTEE DMCC</strong><br>
                Address: Unit No. 1309, 13TH Floor, Fortune Tower, Cluster C,<br>
                Jumeirah Lake Towers, Dubai, U.A.E.<br>
                Tel No. +971 457 79865, info@elteedmcc.com
            </td>
            <td colspan="2" width="50%"><strong>2. CERTIFICATE NO. ED/COO/2497 </strong></td>
        </tr>

        <tr>
            <td colspan="2"><strong>3. Consignee (name, address, country)</strong><br><br>
                <strong>Pure Grow Agri Solutions Pvt. Ltd.</strong><br>
                Address: GAT NO. 1080, PIR VASTI, VILLAGE URULI DEVACHI,<br>
                PUNE - 412308, MAHARASHTRA, INDIA.<br>
                IEC: AAMCP2662E, GST: 27AAMCP2662E1ZB,<br>
                PAN: AAMCP2662E
            </td>
            <td colspan="2"><strong>4. Notify Party (name, address, country)</strong><br><br>
                SAME AS CONSIGNEE
            </td>
        </tr>

        <tr>
            <td colspan="2"><strong>5. Particulars of transport (where required)<br>
                Through sea.<br><br>
                Port of delivery: NHAVA SHEVA, INDIA</strong>
            </td>
            <td colspan="2">
                <h2 style="text-align:center;">CERTIFICATE OF ORIGIN</h2>
            </td>
        </tr>

        <tr>
            <td colspan="2"><strong>6. Marks & Numbers and kind of packages:<br>Description Of the goods.</strong><br><br>
                3120 BAGS OF MONOAMMONIUM PHOSPHATE N:P:K: 12:61:00 25KG Bags (100% WATER SOLUBLE)<br>
                FERTIGATION GRADE FOR AGRICULTURAL USE ONLY.<br>
                HS CODE: 31054000<br>
                03×20 ft containers
            </td>
            <td width="25%">
                <strong>GROSS WEIGHT</strong><br>78.312 MT
            </td>
            <td width="25%">
                <strong>NET WEIGHT</strong><br>78.000 MT
            </td>
        </tr>

        <tr>
            <td colspan="2"><strong>7. Other information</strong><br><br>
                Invoice No. ED/EXP/2497 Date: 23/11/2024<br>
                BL No. EGLV153408009789<br>
                Vessel & Voyage: INTERASIA AMPLIFY W004
            </td>
            <td colspan="2">
                This is only to certify that the above goods have been loaded in above vessel and voyage no by the above shipper to the consignee and origin of the goods is CHINA.<br>
                <hr style="color:black;"><img src="images/sign.png" class="stamp-sign"><br>
                Place and date of issue U.A.E. 23/11/2024
            </td>
        </tr>
    </table>
    <br><br>
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

$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->Output('COO-Certificate.pdf', 'I');
exit;
?>
