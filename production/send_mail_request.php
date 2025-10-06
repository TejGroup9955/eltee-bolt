<?php
include_once("../configuration.php");
include_once("../sendmail/mail.php");
session_start();
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$dept_id = @$_SESSION['dept_id'];
$branch_id = @$_SESSION['branch_id'];
$UserNameSession = @$_SESSION['user_name'];
$financial_year = @$_SESSION['financial_year'];

$Flag = $_POST['Flag'];
extract($_POST);

if($Flag=="SendProFormaMail")
{
    $rstcompany = mysqli_query($connect,"select comp_name,comp_cont_no1,comp_add,comp_email from company_master where comp_id='$comp_id' ");
    $rwcompany = mysqli_fetch_assoc($rstcompany);
    extract($rwcompany);

    $rstclient = mysqli_query($connect,"select client_email,client_name from client_master where client_id='$client_id' ");
    if(mysqli_num_rows($rstclient)>0)
    {
        $rwclient = mysqli_fetch_assoc($rstclient);
        $client_email = $rwclient['client_email'];
        $client_name = $rwclient['client_name'];
        // <h2 style="color: #2b6cb0;">Thank you for choosing us!</h2>

        $body = '
        <div style="font-family: Arial, sans-serif; color: #333;">
            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 6px;">     
                <p>Dear '.$client_name.',</p>
                <p>Thank you for your order.</p>
                <p>Please find the <strong>Proforma Invoice number ' . $pi_custom_number . '</strong> dated <strong>' . date('d M Y',strtotime($pi_date)) . '</strong> attached to this email.</p>
                <a href="'.$config_url.'production/print-pro-forma.php?pi_no='.base64_encode($pi_no).'" target="_blank" style="color: #2b6cb0; text-decoration: underline;">Click here to view or print</a>                               
                <p>Should you have any questions or require any further information, pls do not hesitate to contact us.</p>
                <p>We appreciate your business and look forward to continuing our successful partnership.</p><br>
                <p>Best Regards,</p>
                <img src="images/logo.png" alt="Company Logo" style="max-width: 150px;">
                <p style="font-size: 14px; color: #555;">
                    '.$comp_name.'<br>
                    '.$comp_add.'<br>
                    '.$comp_cont_no1.'<br>
                    '.$comp_email.'
                </p>
            </div>
        </div>';
        
        $returnmailmsg = sendMail(
            [$client_email],     
            '',                                    
            'Proforma Invoice Attached '.$pi_custom_number ,
            $body,
            ''                                                                                              
        );
        echo $returnmailmsg; 
    }else{
        echo "Receiver Mail Not Found";
    }
}

if($Flag=="SendPurchaseOrderMail")
{
    $rstcompany = mysqli_query($connect,"select comp_name,comp_cont_no1,comp_add,comp_email from company_master where comp_id='$comp_id' ");
    $rwcompany = mysqli_fetch_assoc($rstcompany);
    extract($rwcompany);

    $rstclient = mysqli_query($connect,"select client_email,client_name from client_master where client_id='$client_id' ");
    if(mysqli_num_rows($rstclient)>0)
    {
        $rwclient = mysqli_fetch_assoc($rstclient);
        $client_email = $rwclient['client_email'];
        $client_name = $rwclient['client_name'];
        // <h2 style="color: #2b6cb0;">Thank you for choosing us!</h2>

        $body = '
        <div style="font-family: Arial, sans-serif; color: #333;">
            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 6px;">   
                <p>Dear '.$client_name.',</p>
                <p>Thank you for your order.</p>
                <p>Please find our <strong>Purchase Order number ' . $po_custom_number . '</strong>  attached to this email.</p>
                <a href="'.$config_url.'production/print-purchase-order.php?po_id='.base64_encode($po_id).'" target="_blank" style="color: #2b6cb0; text-decoration: underline;">Click here to view or print</a>               
                <p>Thank you for your continued business with us.</p><br>
                <p>Best Regards,</p>
                <img src="images/logo.png" alt="Company Logo" style="max-width: 150px;">
                <p style="font-size: 14px; color: #555;">
                    '.$comp_name.'<br>
                    '.$comp_add.'<br>
                    '.$comp_cont_no1.'<br>
                    '.$comp_email.'
                </p>
            </div>
        </div>';
        
        $returnmailmsg = sendMail(
            [$client_email],     
            '',                                    
            'Purchase Order Attached '.$po_custom_number ,
            $body,
            ''                                                                                              
        );
        echo $returnmailmsg; 
    }else{
        echo "Receiver Mail Not Found";
    }
}

if($Flag=="SendCustomerPaymentMail")
{
    $rstcompany = mysqli_query($connect,"select comp_name,comp_cont_no1,comp_add,comp_email from company_master where comp_id='$comp_id' ");
    $rwcompany = mysqli_fetch_assoc($rstcompany);
    extract($rwcompany);

    $rstclient = mysqli_query($connect,"select client_email,client_name from client_master where client_id='$client_id' ");
    if(mysqli_num_rows($rstclient)>0)
    {
        $rwclient = mysqli_fetch_assoc($rstclient);
        $client_email = $rwclient['client_email'];
        $client_name = $rwclient['client_name'];
        // <h2 style="color: #2b6cb0;">Thank you for choosing us!</h2>

        $body = '
        <div style="font-family: Arial, sans-serif; color: #333;">
            <div style="background-color: #f7f7f7; padding: 20px; border-radius: 6px;">     
                <p>Dear '.$client_name.',</p>
                <p>Greetings from Eltee Group…!!</p>
                <p>We hereby confirm the receipt of payment amount <strong>' . $payment_amount . ' ' . $currency_code . '</strong> made by you against our <strong>Proforma Invoice number ' . $pi_custom_number . '</strong> dated <strong>' . date('d M Y',strtotime($pi_date)) . '</strong></p>
                <p>Thank you for your continued business with us.</p>
                <p>Best Regards,</p>
                <img src="images/logo.png" alt="Company Logo" style="max-width: 150px;">
                <p style="font-size: 14px; color: #555;">
                    '.$comp_name.'<br>
                    '.$comp_add.'<br>
                    '.$comp_cont_no1.'<br>
                    '.$comp_email.'
                </p>
            </div>
        </div>';
        
        $returnmailmsg = sendMail(
            [$client_email],     
            '',                                    
            ' Payment Receipt Confirmation' ,
            $body,
            ''                                                                                              
        );
        echo $returnmailmsg; 
    }else{
        echo "Receiver Mail Not Found";
    }
}

?>