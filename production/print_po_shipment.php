<?php
include '../configuration.php';

$po_id = base64_decode($_GET['po_no']);
$grand_total = 0;
$account_id = "";
$shipment_head = [];
$shipment_details = [];

// 1. Get Purchase Order + Client + User
$cmd = "SELECT p.*, c.client_name, c.client_mob,c.client_add,c.client_email, u.user_name FROM purchase_order p
        INNER JOIN client_master c ON c.client_id = p.supplier_id
        INNER JOIN user_master u ON u.user_id = p.user_id
        WHERE p.po_id='$po_id'";
$result = $connect->query($cmd);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $supplier_pi_no = $row['supplier_pi_no'];
    $supplier_pi_date = ($row['supplier_pi_date'] == "0000-00-00") ? "-" : date('d-m-Y', strtotime($row["supplier_pi_date"]));
    $po_date = ($row['po_date'] == "0000-00-00") ? "-" : date('d-m-Y', strtotime($row["po_date"]));
    $valid_upto = ($row['valid_upto'] == "0000-00-00") ? "-" : date('d-m-Y', strtotime($row["valid_upto"]));
    $po_custom_number = $row['po_custom_number'];
    $grand_total = round($row['grand_total']);
    $account_id = $row['user_id'];
    $supplier_name = $row['client_name'];
    $client_mob = $row['client_mob'];
    $client_add = $row['client_add'];
    $client_email = $row['client_email'];
    $comp_id = $row['comp_id'];
    $supplier_pi_no = $row['supplier_pi_no'];
}

// 2. Get Company Info
$cmd = "SELECT comp_name, comp_add, comp_cont_no1, comp_email FROM company_master WHERE comp_id='$comp_id'";
$result = $connect->query($cmd);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $comp_name = $row['comp_name'];
    $comp_add = $row['comp_add'];
    $comp_contact = $row['comp_cont_no1'];
    $comp_email = $row['comp_email'];
}

// 3. Get Shipment Head
$cmd = "SELECT * FROM purchase_shipment_head WHERE po_number = '$po_id'";
$result = $connect->query($cmd);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $shipment_head = $row;
    $shipment_id = $row['shipment_id'];
}

// 4. Get Shipment Details
$cmd = "SELECT d.*, p.product_name ,pp.pi_custom_number
        FROM purchase_shipment_details d
        left JOIN product_master p ON p.product_id = d.product_id
        left join pro_forma_head pp on pp.pi_no = d.pro_forma_no
        WHERE d.purchase_shipment_head_id = '$shipment_id'";

$result = $connect->query($cmd);
while ($row = $result->fetch_assoc()) {
    $shipment_details[] = $row;
}
$html="";
$html .="
<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order Shipment</title>
    <style>
        body { font-family: Arial; font-size: 14px; margin: 40px; color: #333; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        .header-table td { border: none; }
        .section-title { background: #f2f2f2; font-weight: bold; }
        .no-border { border: none !important; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
<div class='col-md-12' style='text-align:center;'>
<img style='width:170px;' src='images/logo.png'/>
<br>
<h3>PURCHASE ORDER SHIPMENT</h3>
<table>
<tr>
<td align='center' width='320'>
<p>
$comp_add Tel.No-$comp_contact / $comp_email <br>
</p>				
</td>
</tr>
</table>
<br><br>
<table class=\"header-table\">
    <tr>
        <td><strong>Supplier:</strong> {$supplier_name}<br>
            <strong>Address:</strong> {$client_add}<br>
            <strong>Email:</strong> {$client_email}<br>
            <strong>Contact:</strong> {$client_mob}
        </td>
        <td class=\"right\">
            <strong>PO No:</strong> {$po_custom_number}<br>
            <strong>PO Date:</strong> {$po_date}<br>
            <strong>Valid Upto:</strong> {$valid_upto}<br>
            <strong>Supplier PI No:</strong> {$supplier_pi_no}
        </td>
    </tr>
</table>

<table>
    <tr class=\"section-title\">
        <td colspan=\"4\">Shipment Details</td>
    </tr>
    <tr>
        <td><strong>Shipment No:</strong> {$shipment_head['shipment_number']}</td>
        <td><strong>Shipment Date:</strong> " . date('d-m-Y', strtotime($shipment_head['shipment_date'])) . "</td>
        <td><strong>Shipment By:</strong> {$shipment_head['shipment_by']}</td>
        <td><strong>Mode of Shipment:</strong> {$shipment_head['modeOfShipment']}</td>
    </tr>
    <tr>
        <td><strong>Supplier Invoice:</strong> {$shipment_head['supplier_invoice']}</td>
        <td><strong>ETA:</strong> " . date('d-m-Y', strtotime($shipment_head['eta'])) . "</td>
        <td><strong>ETD:</strong> " . date('d-m-Y', strtotime($shipment_head['etd'])) . "</td>
        <td><strong>No. of Containers:</strong> {$shipment_head['no_of_container']}</td>
    </tr>
  
</table>

<table>
    <tr class=\"section-title\">
        <td colspan=\"6\">Container & Product Details</td>
    </tr>
    <tr>
        <th>Sr.</th>
        <th>Container No</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Pro Forma No</th>
        <th>Status</th>
    </tr>";
    $sr = 1;
    foreach ($shipment_details as $item) {
        $status = ($item['linking_status'] == '1') ? 'Linked' : 'Not Linked';
        $html .="
        <tr>
            <td>{$sr}</td>
            <td>{$item['container_no']}</td>
            <td>{$item['product_name']}</td>
            <td>{$item['product_qty']}</td>
            <td>{$item['pi_custom_number']}</td>
            <td>{$status}</td>
        </tr>";
        $sr++;
    }
$html .="
</table>

</body>
</html>
";

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