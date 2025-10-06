<?php
    include_once("../../configuration.php");
    error_reporting(0);
    $Flag = $_POST['Flag'];
    session_start();
    $user_id_session = @$_SESSION['user_id'];
    $user_type_id = @$_SESSION['user_type_id'];
    $role_type_name = @$_SESSION['role_type_name'];
    $comp_id = @$_SESSION['comp_id'];
    $dept_id = @$_SESSION['dept_id'];
    $branch_id = @$_SESSION['branch_id'];
    $UserNameSession = @$_SESSION['user_name'];
    $financial_year = @$_SESSION['financial_year'];
    extract($_POST);

if($Flag=="SaveShipmentDocuments")
{   
    $shipmentPoId = $_POST['shipmentPoId'];
    $document_names = $_POST['document_names'];
    $document_files = $_FILES['document_files'];
    // print_r($_POST);
    // exit;
    $isuploaded = false;
    for ($i = 0; $i < count($document_names); $i++) {
        $docName = mysqli_real_escape_string($connect, $document_names[$i]);
        $fileName = $document_files['name'][$i];
        $tmpName = $document_files['tmp_name'][$i];

        if (!empty($fileName) && is_uploaded_file($tmpName)) {
            mysqli_query($connect, "DELETE FROM purchase_shipment_documents WHERE purchase_id='$shipmentPoId'");
            $uploadDir = '../PoShipmentDocs/ShipmentDocument'.$shipmentPoId.'/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // create directory if it doesn't exist
            }
            $newFileName = uniqid() . '_' . basename($fileName);
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                mysqli_query($connect, "INSERT INTO purchase_shipment_documents(purchase_id, document_name, document_path) 
                    VALUES('$shipmentPoId', '$docName', '$targetPath')");

                mysqli_query($connect,"update purchase_order set shipment_document_status='1' where po_id='$shipmentPoId' ");
                $isuploaded = true;
            }
        }
    }
    if($isuploaded==true)
    {
        echo "Inserted";
    }
    else{
        echo "Please Upload Valid Image";
    }
}
if($Flag=="UpdateShipmentDocument")
{
    $uploadedDocs = [];
    $rstdoc = mysqli_query($connect, "SELECT * FROM purchase_shipment_documents WHERE purchase_id='$po_id'");
    if (mysqli_num_rows($rstdoc) > 0) {
        while ($rwdoc = mysqli_fetch_assoc($rstdoc)) {
            $uploadedDocs[] = [
                'document_name' => $rwdoc['document_name'],
                'document_path' => $rwdoc['document_path'],
                'shipment_document_id' => $rwdoc['shipment_document_id'],
            ];
        }
    }
    echo json_encode($uploadedDocs);
    exit;
}
if($Flag=="SaveShipmentDetails")
{
    $container_numbers = null;
    $container_qty_single     = null;

    // if ($modeOfShipment == 'FOB') {
        if (!empty($_POST['container_numbers']) && is_array($_POST['container_numbers'])) {
            $container_numbers = implode(',', array_map('trim', $_POST['container_numbers']));
        }
    // } else {
    //     $container_qty_single = $_POST['container_qty_single'];
    // }

    $shipment_number = "SH00".$po_number;
    $rstsql = mysqli_query($connect,"INSERT INTO `purchase_shipment_head` 
            (`po_number`, `shipment_number`, `shipment_date`, `shipment_by`, `modeOfShipment`,
             `supplier_invoice`, `eta`, `etd`, `no_of_container`, `container_numbers`, `container_qty`) 
            VALUES ('$po_number', '$shipment_number', '$shipment_date', '$shipment_by', '$modeOfShipment',
            '$supplier_invoice', '$eta', '$etd', '$no_of_container', '$container_numbers', '$container_qty_single')" );
    if ($rstsql) {
        mysqli_query($connect,"update purchase_order set shipment_status='1' where po_id='$po_number' ");
        echo "Success";
    } else {
        echo "Error saving shipment: ".mysqli_error($connect);
    }
}

if($Flag=="SaveShipmentAllocation")
{
    $po_id = $_POST['hidden_po_id'];
    $shipment_head_id = $_POST['hidden_po_shipment_id'];

    $containers = $_POST['alloc_container'];
    $products = $_POST['alloc_product'];
    $quantities = $_POST['alloc_qty'];

    $isinserted = false;
    for ($i = 0; $i < count($containers); $i++) {
        $container = mysqli_real_escape_string($connect, $containers[$i]);
        $product = mysqli_real_escape_string($connect, $products[$i]);
        $qty = mysqli_real_escape_string($connect, $quantities[$i]);

        $insertSQL = "INSERT INTO `purchase_shipment_details` 
            (`purchase_id`, `purchase_shipment_head_id`, `container_no`, `product_id`, `product_qty`) 
            VALUES ('$po_id', '$shipment_head_id', '$container', '$product', '$qty')";

        mysqli_query($connect, $insertSQL);
        $isinserted = true;
    }
    if($isinserted==true)
    {
        mysqli_query($connect,"update purchase_order set container_allocation_status='1' where po_id='$po_id' ");
        echo "Success";
    }else{
        echo "unable to Add Shipment details";
    }
}

if($Flag =="LoadLinkPIContainer")
{
    $po_id = $_POST['po_id'];
    $rstpo = mysqli_query($connect,"select ps.container_no,ps.product_id,p.product_name,ps.product_qty,
    ps.purchase_shipment_detail_id,ps.pro_forma_no,po.po_type,pps.pi_no
    from purchase_shipment_details ps 
    inner join product_master p on p.product_id=ps.product_id
    inner join purchase_order po on po.po_id = ps.purchase_id
    inner join purchase_order_details pps on pps.purchase_order_id = ps.purchase_id
    where ps.purchase_id='$po_id' ");
    if(mysqli_num_rows($rstpo)>0)
    {
        while($rwpo = mysqli_fetch_assoc($rstpo))
        {
            extract($rwpo);
            echo '<div class="col-md-3 mt-3">
                    <label for="">Container No.</label>
                    <select name="txtcontainerno[]" id="txtcontainerno" class="form-control form-select" >
                        <option value='.$container_no.' selected>'.$container_no.'</option>
                    </select>
                </div>
                <div class="col-md-3 mt-3">
                    <label for="">Product Name</label>
                    <select name="txtproduct_id[]" id="txtcontainerno" class="form-control form-select" >
                        <option value='.$product_id.' selected>'.$product_name.'</option>
                    </select>
                </div>
                <div class="col-md-1 mt-3">
                    <label for="">Qty</label>
                    <select name="txtproductqty[]" id="txtproductqty" class="form-control form-select" style=" width: 55px;">
                        <option value='.$product_qty.' selected>'.$product_qty.'</option>
                    </select>
                </div>
                <div class="col-md-3 mt-3">
                    <label for="">Select Pro-Farma</label>
                    <select name="txtproformano[]" id="txtproformano'.$purchase_shipment_detail_id.'" class="form-control form-select">
                    ';
                
                if($pro_forma_no==0)
                {
                    echo '<option value="">Select Pro Forma</option>';
                    if($po_type =="Direct PO")
                    {
                        $rstproforma = mysqli_query($connect,"SELECT p.pi_no, p.pi_custom_number 
                        FROM pro_forma_head_details ph 
                        inner join pro_forma_head p on p.pi_no=ph.pi_no 
                        where ph.po_total_qty=0 and ph.product_id='$product_id' group by ph.pi_no");
                    }
                    else{
                        $pi_no_explode = explode(',',$pi_no);
                        foreach($pi_no_explode as $pi_no_new)
                        {
                            $rstproforma = mysqli_query($connect,"SELECT p.pi_no,p.pi_custom_number 
                            FROM pro_forma_head_details ph
                            inner join pro_forma_head p on p.pi_no = ph.pi_no 
                            where ph.product_id='$product_id' and p.pi_no='$pi_no_new' group by ph.pi_no ");
                        }
                    }
                }
                else{
                    $rstproforma = mysqli_query($connect,"select p.pi_no,p.pi_custom_number 
                    from pro_forma_head p where p.pi_no='$pro_forma_no' ");
                }
                if(mysqli_num_rows($rstproforma)>0)
                {
                    while($rwproforma = mysqli_fetch_assoc($rstproforma))
                    {
                        $pi_no = $rwproforma['pi_no'];
                        $pi_custom_number = $rwproforma['pi_custom_number'];
                        echo "<option value='$pi_no'>$pi_custom_number</option>";
                    }
                }
                echo '</select>
                </div>
                <div class="col-md-2 mt-4">';
                if($pro_forma_no==0)
                {
                    echo '<button type="button" class="btn btn-sm btn-round btn-primary" style="margin-top: 10px;" onclick="SavePILinking('.$purchase_shipment_detail_id.');">Link PI</button>';
                }
                else{
                    echo '<button type="button" class="btn btn-sm btn-round btn-info" style="margin-top: 10px;">PI Linked</button>';
                }
                echo '</div>';
        }
    }
}

if($Flag=="SavePILinking")
{
    $rstproforma = mysqli_query($connect,"update purchase_shipment_details set pro_forma_no='$proformano',
    linking_status='1' where purchase_shipment_detail_id='$purchase_shipment_detail_id' ");
    if($rstproforma)
    {
        echo "Success";
    }
    else{
        echo "Unable to link PI";
    }

}
if($Flag=="loadPODocumentList")
{
    $po_id = $_POST['po_id'];
    echo "<option value=''>Select Document</option>";
    $rstdoc = mysqli_query($connect,"select * from purchase_order_shipment_detail where po_no='$po_id' ");
    while($rwdoc = mysqli_fetch_assoc($rstdoc))
    {
        $shipment_document_name = $rwdoc['shipment_document_name'];
        echo "<option value='$shipment_document_name'>$shipment_document_name</option>";
    }
}
?>