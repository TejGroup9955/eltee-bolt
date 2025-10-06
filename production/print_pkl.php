<?php
include_once("../configuration.php");
session_start();
//print_r($_SESSION);
//exit;
$Pi_No = base64_decode($_GET['PI_No']);
$comp_id = $_SESSION['comp_id']; 
$branch_id = $_SESSION['branch_id']; 

$rstcomp = mysqli_query($connect,"select * from company_master c where c.comp_id ='$comp_id' ");
$rwcomp = mysqli_fetch_assoc($rstcomp);
extract($rwcomp);

$rstbranch = mysqli_query($connect,"select * from branch_master b where b.branch_id ='$branch_id' ");
$rwbranch = mysqli_fetch_assoc($rstbranch);
extract($rwbranch);

$rstpro = mysqli_query($connect,"select p.*,c.client_name,c.client_add,c.GST_no,c.kind_attention,
co.countryName as CountryOfOrigin,pt.port_name,t.invoice_number,t.invoice_date,t.bl_no,t.vessal_name
from pro_forma_head p
inner join tax_invoice_head t on t.pi_no=p.pi_no 
inner join country_master co on co.id=p.country_of_origin
inner join port_master pt on pt.port_master_id=p.port_id
inner join client_master c on c.client_id = p.account_id
where p.pi_no='$Pi_No'");
if(mysqli_num_rows($rstpro)>0)
{
    $rwpro = mysqli_fetch_assoc($rstpro);
    extract($rwpro);
    if($PKLPrintStatus!="1")
    {
        $date = date('Y-m-d');
        $rstproupdate = mysqli_query($connect,"update pro_forma_head set PKLPrintStatus='1',PKLPrintDate='$date' where pi_no='$Pi_No' ");
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
    strong{ font-size:11px; }
    p,li{ font-size:10px; }
</style>

<div class="container">
    <table>
        <tr>
            <td style="border:none"><img style="width:130px;" src="images/logo.png"/></td>
            <td style="border:none"><br><br><h4>'.$rwcomp['comp_name'].'</h4>
                <p>'.$rwcomp['comp_add'].'<br>
                Tel.: '.$rwbranch['branch_cont_no1'].', Email: '.$rwbranch['branch_email'].'<br><p></td>
        </tr>
    </table>
    <hr style="height:3px">
    <table class="table">
        <tr>
            <td colspan="2" style="text-align:center"><b>Packing List</b></td>
        </tr>
        <tr>
            <td>
                <strong><u>Exporter:</u></strong><br>
                <h6>'.$rwcomp['comp_name'].'</h6>
                <p>'.$rwcomp['comp_add'].'<br>
                Tel.: '.$rwbranch['branch_cont_no1'].', Email: '.$rwbranch['branch_email'].'</p>
            </td>
            <td>
                <table>
                    <tr> 
                        <td style="border:none">
                            <strong>Invoice No. & Date</strong><br>
                            <p>'.$invoice_number.'<br>
                            Date : '.date("d-M-Y",strtotime($invoice_date)).'  </p>  
                        </td>
                        <hr>
                    </tr>
                    <tr>
                        <td style="border:none">
                            <strong>PI No. & Date</strong><br>
                            <p>'.$pi_custom_number.'<br>
                            Date : '.date("d-M-Y",strtotime($pi_invoice_date)).' </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <strong><u>Consignee:</u></strong><br>
                <p><b>'.$rwpro['client_name'].'</b><br>
                Address: '.$rwpro['client_add'].'<br>
                IEC: AAMCP2662E, GST: '.$rwpro['GST_no'].', PAN:  AAMCP2662E</p>
            </td>
            <td>
                <strong><u>Notify Party:</u></strong><br>
                <p>SAME AS CONSIGNEE</p>
            </td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>COUNTRY OF ORIGIN : '.$CountryOfOrigin.'</li>
                    <li>PRE - CARRIAGE BY  : SEA </li>
                    <li>VESSEL NO & VOY : '.$vessal_name.'</li>
                    <li>BILL OF LADING : '.$bl_no.'</li>
                    <li>PORT OF LOADING : '.$port_of_loading_name.'</li>
                    <li>PORT OF DISCHARGE : '.$destination_port_name.'   </li> 
                </ul>
            </td>
            <td>
                <strong><u>Terms of Delivery & Payment</u></strong><br>
                <p><b>PRICE : </b>CIF Nhava Sheva, India</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <table>
                <thead>
                    <tr>
                       <th><strong>Marks & Nos. Container Nos.</strong></th>
                       <th><strong>No & kinds of Packages</strong></th>
                       <th><strong>Description of Goods</strong></th>
                       <th><strong>Total Bags</strong></th>
                       <th><strong>Net. wt. Bag Kgs</strong></th>
                       <th><strong>Total Net. Wt. In KG</strong></th>
                       <th><strong>Total Gr. Wt. In KG</strong></th>
                    </tr>
                </thead>
                <tbody>';
        $shipmentDetails = [];
        $result = mysqli_query($connect, "SELECT * FROM purchase_shipment_details WHERE pro_forma_no = '$Pi_No'");
        while ($row = mysqli_fetch_assoc($result)) {
            $key = $row['product_id'] . '_' . $row['pro_forma_no'];
            $shipmentDetails[$key][] = $row;
        }  

        $total = 0;
        $rstprod = mysqli_query($connect,"select d.*,ps.pro_forma_no as pro_forma_no,p.product_id as product_id,p.product_name as product_name,p.product_code as product_code from pro_forma_head_details d 
            inner join product_master p on p.product_id = d.product_id
            left join purchase_shipment_details ps on ps.pro_forma_no = d.pi_no
            where d.pi_no='$Pi_No'");
        
        if(mysqli_num_rows($rstprod)>0)
        {
            $procnt=1;
            while($rwprod = mysqli_fetch_assoc($rstprod))
            {     
                $product_id = $rwprod['product_id'];
                $pro_forma_no = $rwprod['pro_forma_no'];
                $key = $product_id . '_' . $pro_forma_no;

                $containerCount = isset($shipmentDetails[$key]) ? count($shipmentDetails[$key]) : 0;

                $containerList = '';
                $containerList = $containerCount.'<br><br><br><br>Container No<br>';
                $productList = $rwprod['total_weight'].' KG <br> ( '.$rwprod['no_of_bags'].' '.$rwprod['packaging_type'].') <br><br><br><br>';
                if (!empty($shipmentDetails[$key])) {
                    foreach ($shipmentDetails[$key] as $rwship) {
                        $containerList .= $rwship['container_no'];
                        $productList .= $rwship['product_qty'].' '.$rwprod['packaging_type'];
                    }
                }

                $html .= '<tr>
                        <td style="text-align:center"><p>'.$containerList.'</p></td>
                        <td style="text-align:center"><p>'.$productList.'</p></td>
                        <td style="text-align:center"><div class="details">
                            <p>'.$rwprod['product_name'].'<br>
                            HS CODE: '.$rwprod['product_code'].'</p><br>
                        </div></p></td>
                        <td style="text-align:center"><p>'.$rwprod['no_of_bags'].'</p></td>
                        <td style="text-align:center"><p>'.$rwprod['each_bag_weight'].'</p></td>
                        <td style="text-align:center"><p>'.$rwprod['total_weight'].'</p></td>
                        <td style="text-align:center"><p>'.$rwprod['total_weight'].'</p></td>
                    </tr>';
                
                $total = $total + $rwprod['total_weight'];
            }

        }

        $html .=  '</tbody>
            </table>
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <table>
                    <tr>
                        <td style="border:none">
                            <strong><b>TOTAL NET WEIGHT : '.floatval($total).'</b></strong><br><br>
                            <strong><b>TOTAL GROSS WEIGHT : '.floatval($total).'</b></strong> 
                        </td>
                        <hr>
                    </tr>
                    <tr>
                        <td style="border:none"><strong>Declaration:</strong><br>
                            <p>We declare that this Invoice shows the actual price of the goods described and that all particulars are true and correct</p>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <h5>For ELTEE DMCC</h5><br>
                <img src="images/sign.png" style="width:180px;"><br>
                <h5>Authorized Signatory</h5>
            </td>
        </tr>
    </table>
</div>';
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