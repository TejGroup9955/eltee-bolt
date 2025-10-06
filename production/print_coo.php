<?php
include_once("../configuration.php");
session_start();
//print_r($_SESSION);
//exit;
$Pi_No = base64_decode($_GET['PI_No']);
$comp_id = $_SESSION['comp_id']; 
$branch_id = $_SESSION['branch_id']; 
$financial_year = $_SESSION['financial_year']; 

$rstcomp = mysqli_query($connect,"select * from company_master c where c.comp_id ='$comp_id' ");
$rwcomp = mysqli_fetch_assoc($rstcomp);
extract($rwcomp);

$rstbranch = mysqli_query($connect,"select * from branch_master b where b.branch_id ='$branch_id' ");
$rwbranch = mysqli_fetch_assoc($rstbranch);
extract($rwbranch);
$Pi_No = base64_decode($_GET['PI_No']);

$certificate_no = '';
$year_part = "00";
$cmd_year = "SELECT year_name FROM financial_year WHERE year_id = '$financial_year' LIMIT 1";
$res_year = $connect->query($cmd_year);
if ($res_year->num_rows > 0) {
    $row_year = $res_year->fetch_assoc();
    $fyParts = explode("-", $row_year['year_name']);
    if (count($fyParts) === 2) {
        $year_part = substr($fyParts[0], -2); // get '24' from '2024-2025'
    }
}
$cmd_ed = "SELECT pi_custom_number,coo_certificate_no FROM pro_forma_head WHERE pi_no = '$Pi_No' LIMIT 1";
$res_ed = $connect->query($cmd_ed);

if ($res_ed->num_rows > 0) {
    $row_ed = $res_ed->fetch_assoc();
    $pi_custom_number = $row_ed['pi_custom_number']; 
    $coo_certificate_no_previous = $row_ed['coo_certificate_no']; 
    if (preg_match('/ED-(\d{4})/', $pi_custom_number, $matches)) {
        $existing_no = intval($matches[1]); 
    }
    $padded_number = str_pad($existing_no, 2, "0", STR_PAD_LEFT); 
    $certificate_no = "ED/COO/{$year_part}{$existing_no}";
   
    if($coo_certificate_no_previous=='')
    {
        $rstupdatepi = mysqli_query($connect,"update pro_forma_head set coo_certificate_no='$certificate_no' where pi_no = '$Pi_No'");
    }
}




$rstpro = mysqli_query($connect,"select p.*,c.client_name,c.client_add,c.GST_no,c.kind_attention,pt.port_name,
t.invoice_number,t.invoice_date,t.bl_no,t.vessal_name
from pro_forma_head p
inner join tax_invoice_head t on t.pi_no=p.pi_no 
inner join port_master pt on pt.port_master_id=p.port_id
inner join client_master c on c.client_id = p.account_id
where p.pi_no='$Pi_No'");
if(mysqli_num_rows($rstpro)>0)
{
    $rwpro = mysqli_fetch_assoc($rstpro);
    extract($rwpro);
    if($COOPrintStatus!="1")
    {
        $date = date('Y-m-d');
        $rstproupdate = mysqli_query($connect,"update pro_forma_head set COOPrintStatus='1',COOPrintDate='$date' where pi_no='$Pi_No' ");
    }

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
                <strong>'.$rwcomp['comp_name'].'</strong><br>
                Address: '.$rwcomp['comp_add'].'<br>
                Tel No. '.$rwbranch['branch_cont_no1'].', Email: '.$rwbranch['branch_email'].'
            </td>
            <td colspan="2" width="50%"><strong>2. CERTIFICATE NO. '.$coo_certificate_no.'</strong></td>
        </tr>

        <tr>
            <td colspan="2"><strong>3. Consignee (name, address, country)</strong><br><br>
                <strong>'.$rwpro['client_name'].'</strong><br>
                Address: '.$rwpro['client_add'].'<br>
                GST: '.$rwpro['GST_no'].'
            </td>
            <td colspan="2"><strong>4. Notify Party (name, address, country)</strong><br><br>
                SAME AS CONSIGNEE
            </td>
        </tr>

        <tr>
            <td colspan="2"><strong>5. Particulars of transport (where required)<br>
                Through sea.<br><br>
                Port of delivery: '.$port_name.'</strong>
            </td>
            <td colspan="2">
                <h2 style="text-align:center;">CERTIFICATE OF ORIGIN</h2>
            </td>
        </tr>';

        $html .= '<tr>
                        <td colspan="2" style="border-bottom:none;"><strong>6. Marks & Numbers and kind of packages:<br>Description Of the goods.</strong><br><br>
                        </td>
                        <td width="25%">
                            <strong>GROSS WEIGHT</strong>
                        </td>
                        <td width="25%">
                            <strong>NET WEIGHT</strong>
                        </td>
                    </tr>';
        $rstprod = mysqli_query($connect,"select d.*,p.product_id as product_id,p.product_name as product_name,p.product_code as product_code from pro_forma_head_details d inner join product_master p on p.product_id = d.product_id where d.pi_no='$Pi_No'");
        if(mysqli_num_rows($rstprod)>0)
        {
            $procnt=1;$total = 0;
            while($rwprod = mysqli_fetch_assoc($rstprod))
            {
                $permtweight = $rwprod['total_weight']/1000;
                    $html .= '<tr>
                        <td colspan="2">
                            '.$rwprod['product_name'].'
                            HS CODE: '.$rwprod['product_code'].'<br>
                            03×20 ft containers
                        </td>
                        <td width="25%">
                            '.$permtweight.' MT
                        </td>
                        <td width="25%">
                            '.$permtweight.' MT
                        </td>
                    </tr>';
            }
        }

$html .= ' <tr>
            <td colspan="2"><strong>7. Other information</strong><br><br>
                Invoice No. '.$invoice_number.' Date: '.date('d/m/Y',strtotime($invoice_date)).'<br>
                BL No. '.$bl_no.'<br>
                Vessel & Voyage: '.$vessal_name.'
            </td>
            <td colspan="2">
                This is only to certify that the above goods have been loaded in above vessel and voyage no by the above shipper to the consignee and origin of the goods is CHINA.<br>
                <hr style="color:black;"><img src="images/sign.png" class="stamp-sign"><br>
                Place and date of issue U.A.E. '.date('d/m/Y',strtotime($COOPrintDate)).'
            </td>
        </tr>
    </table>
    <br><br>
    <br><hr> 
    <div style="text-align:center;">
       '.$rwcomp['comp_add'].'<br>
        Ph. No. '.$rwbranch['branch_cont_no1'].', Email : '.$rwbranch['branch_email'].'<br><br>
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

$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->Output('COO-Certificate.pdf', 'I');
exit;
?>
