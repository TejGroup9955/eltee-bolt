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

if($Flag=="ShowPaymentList")
{
        echo '<table id="dtlRecord" class="display table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th style="width: 35px;">Sr. No.</th>
                    <th>Action</th>
                    <th>PI No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Grand Total</th>
                    <th>Received Amount</th>
                    <th>Remaining Amount</th>
                </tr>
                </thead>
                <tbody>';
        $query = "SELECT * FROM `pro_forma_receipt_payment` order by customer_receipt_id desc";
        $rstclient = mysqli_query($connect,$query);
        $srno =1;
        if(mysqli_num_rows($rstclient)>0)
        {
            while($rwclient = mysqli_fetch_assoc($rstclient))
            {
                extract($rwclient);
                $grand_total = round($rwclient['total_amount']);
                $query1 = $connect->query("SELECT P.`pi_custom_number`, P.`pi_invoice_date`,P.`account_id`,
                C.`client_name`,C.client_mob, cm.currency_code FROM `pro_forma_head` P INNER JOIN client_master C on C.`client_id` = P.`account_id` 
                inner join country_master cm on cm.id=P.currency_id  WHERE P.`pi_no`= '".$pi_no."'");
                $rowCount = $query1->num_rows;
                if($rowCount > 0){
                    if($row1 = $query1->fetch_assoc()){
                        $pi_custom_number = $row1['pi_custom_number'];
                        $pi_invoice_date = $row1['pi_invoice_date'];
                        $account_id = $row1['account_id'];
                        $client_name = $row1['client_name'];
                        $client_mob = $row1['client_mob'];
                        $currency_code = $row1['currency_code'];
                    }
                }
                $query1 = "SELECT sum(`tax_amount`) as tax_amount FROM `pro_forma_tax_payment` WHERE `customer_receipt_id`='$customer_receipt_id'";
                $rstclient1 = mysqli_query($connect,$query1);
                while($rwclient1 = mysqli_fetch_assoc($rstclient1))
                {
                    $total_tax_amount = $rwclient1['tax_amount'];
                }
                if($paid_amount > 0){
                    $SendMailAndWhatsappbtn='';
                    if($total_tax_amount < $paid_amount){
                        $proFormabutton = "<a href='add_payment_details.php?id=".base64_encode($customer_receipt_id)."'  class='btn btn-info btn-round btn-sm' ><i class='fa fa-plus'></i>&nbsp;Add</a>";
                    }else{
                        $proFormabutton = "<a href='add_payment_details.php?id=".base64_encode($customer_receipt_id)."'  class='btn btn-secondary btn-round btn-sm' ><i class='fa fa-eye'></i>&nbsp;View</a>";
                        $SendMailAndWhatsappbtn = "<button class='btn btn-sm btn-round btn-info' onclick='sendcustomerpaymentmail($pi_no, $account_id, \"".$pi_custom_number."\",\"".$pi_invoice_date."\",\"".$paid_amount."\",\"".$currency_code."\")'><i class='fa fa-envelope'></i></button>
                        <button class='btn btn-sm btn-round btn-success' onclick='sendcustomerpaymentWhatsApp(\"".$client_name."\", \"".$client_mob."\",  \"".$pi_custom_number."\",\"".$pi_invoice_date."\", \"".$paid_amount."\", \"".$currency_code."\")'><i class='fa fa-whatsapp'></i></button>";
                    
                    }

                    $payment_tt_copy_view ='';
                    if(!empty($payment_tt_copy))
                    {
                        $payment_tt_copy_view = "<a href='production/$payment_tt_copy' target='_blank' class='btn btn-sm btn-round btn-success' 
                        data-toggle='tooltip' data-placement='top' title='View TT Copy'><i class='fa fa-eye'></i></a>";
                    }

                    echo '<tr>
                        <td>'.$srno.'</td>
                        <td>'.$proFormabutton.' '.$payment_tt_copy_view.' '.$SendMailAndWhatsappbtn.'</td>
                        <td>'.$pi_custom_number.'</td>
                        <td>'.date('d-m-Y',strtotime($pi_invoice_date)).'</td>
                        <td>'.$client_name.'</td>
                        <td>'.$grand_total.' '.$currency_code.'</td>
                        <td>'.$paid_amount.' '.$currency_code.'</td>
                        <td>'.$remain_amount.' '.$currency_code.'</td>
                    </tr>';
                    $srno++;
                }
            } 
        }       
       echo '</tbody></table>';
}

if($Flag == "SavePaymentDetails"){

    $total = 0;
    $total = $amount+$total_amount;
    echo "<tr>
            <td value='$tax_id'>$tax_name</td>
            <td class='tdamount'>$amount</td>
            <td  style='display:none;' class='tdtotalamount'>$total</td>
            <td><button class='btn btn-danger btn-sm btnremovetax'><i class='fa fa-close'></i></button></td>
        </tr>";

}

if($Flag == "SaveTaxPayment"){

    $query = "select pi_no from pro_forma_head where pi_no ='$pi_no'";
    $rst = mysqli_query($connect,$query);
    if(mysqli_num_rows($rst) > 0)
    {
        if(isset($_POST['tableData'])){
            $i = 0;
            $tableDataNew = json_decode($_POST['tableData'], true);
            $totsize = count($tableDataNew);
            $res = mysqli_query($connect,"delete from pro_forma_tax_payment where customer_receipt_id ='$customer_receipt_id'");
            foreach ($tableDataNew as $tax) {
                $tax_id = $tax['tax_id'];
                $tax_name = $tax['tax_name'];
                $tax_amount = $tax['tax_amount'];
                $query =" INSERT INTO `pro_forma_tax_payment`(customer_receipt_id,pi_no,tax_id, tax_name, tax_amount) 
                    VALUES('$customer_receipt_id','$pi_no','$tax_id','$tax_name','$tax_amount')";
                $query_res2 = $connect->query($query);       
                $i++;
            }
            if($totsize == $i){
                echo 1;
            }
        }
    }
    
    die;

}



























































?>