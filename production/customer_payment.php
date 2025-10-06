
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -150px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPaymentReceive"></div>     
            </div>
        </div>
      </div>
  </div>
</div>     
<?php
  include_once('footer.php');
?>
<script>
  $(document).ready(function(){
    ShowPaymentList();
   });

    function ShowPaymentList()
    {
        $.post("operation/payment_operation.php",{
              Flag:"ShowPaymentList"
        },function(data,success){
              $("#divRecordPaymentReceive").html(data);
              $("#dtlRecord").DataTable({
                dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
                buttons: [
                    {
                        
                    }
                ]
              });
        });
    }
    function sendcustomerpaymentmail(pi_no, client_id, pi_custom_number, pi_date, payment_amount, currency_code)
    {
        var c=confirm("Do You Really Want to send mail?");
        if(c==true)
        {
            showSpinner();
            $.post("send_mail_request.php",{
                Flag:"SendCustomerPaymentMail",
                pi_no:pi_no,
                client_id:client_id,
                pi_custom_number:pi_custom_number,
                pi_date:pi_date,
                payment_amount:payment_amount,
                currency_code:currency_code
            },function(data,success){
                hideSpinner();
                if(data=="Mail Send")
                {
                    Swal.fire(
                        'Well Done!',
                        'Customer Payment Mail Send Successfully',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'Error!',
                        data,
                        'error'
                    );
                }
                ShowPaymentList();
            });
        }
    }
    function sendcustomerpaymentWhatsApp(client_name, client_mob, pi_custom_number, pi_date, payment_amount, currency_code) {
        let message =
            "Dear "+client_name+",\n\n" +
            "Greetings from Eltee Group…!!\n" +
            "We hereby confirm the receipt of payment amount "+payment_amount+" "+currency_code+" made by you against our Proforma Invoice number: " + pi_custom_number + "  dated " + pi_date + ".\n\n" +
            "Thank you for your continued business with us.\n\n" +
            "*Best Regards,*\n" +
            "ELTEE DMCC\n";
        let encodedMessage = encodeURIComponent(message);
        let whatsappLink = "https://wa.me/" + client_mob + "?text=" + encodedMessage;
        window.open(whatsappLink, '_blank');
    }
   
</script>