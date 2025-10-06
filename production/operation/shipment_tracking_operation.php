<?php
include_once("../../configuration.php");
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

if($Flag=="LoadShipmentTrackingReport")
{
    $SearchData = $_POST['SearchData'];
    $rstpurchase = mysqli_query($connect,"select po.po_custom_number,po.po_date,pf.pi_custom_number,
    cm.client_name,th.vessal_name, pd.*,ph.* 
    from purchase_shipment_details pd 
    inner join purchase_shipment_head ph on ph.shipment_id=pd.purchase_shipment_head_id 
    left join purchase_order po on po.po_id=pd.purchase_id 
    left join pro_forma_head pf on pf.pi_no=pd.pro_forma_no left join client_master cm on 
    cm.client_id=po.supplier_id 
    left join tax_invoice_head th on th.pi_no=pf.pi_no
    where pd.linking_status=1 
    and pf.pi_custom_number LIKE '%$SearchData%' OR po.po_custom_number LIKE '%$SearchData%'");
    if(mysqli_num_rows($rstpurchase)>0)
    {
        while($rwpurchase = mysqli_fetch_assoc($rstpurchase))
        {
            extract($rwpurchase);
            echo ' <div class="po-header">PO No: '.$po_custom_number.' | Vendor: '.$client_name.' | PO Date: '.date('d-M-Y',strtotime($po_date)).'</div>
                <table>
                    <thead>
                    <tr>
                        <th>PFI No</th>
                        <th>Vessel</th>
                        <th>No. of Containers</th>
                        <th>Import ETA</th>
                        <th>Import Status</th>
                        <th>Dispatch Date</th>
                        <th>Dispatch Status</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>'.$pi_custom_number.'</td>
                        <td>'.$vessal_name.'</td>
                        <td>'.$no_of_container.'</td>
                        <td>'.date('d-M-Y',strtotime($eta)).'</td>
                        <td>-</td>
                        <td>'.date('d-M-Y',strtotime($etd)).'</td>
                        <td>-</td>
                        <td><a href="print_po_shipment.php?po_no='.base64_encode($purchase_id).'" class="btn" target="_blank"><i class="fa fa-eye"></i></a></td>
                    </tr>
                    
                    </tbody>
            </table>';
        }
    }else{
        echo  "Oops !!! No Data Found";
    }
}
?>