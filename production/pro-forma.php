
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -490px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordProForma"></div>     
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
    ShowProFormaList();
   });
function ShowProFormaList()
{
    $.post("operation/pro_forma_operation.php",{
          Flag:"ShowProFormaList"
    },function(data,success){
          $("#divRecordProForma").html(data);
          $("#dtlRecord").DataTable({
            dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
            buttons: [
                {
                    text: '<i class="fa fa-plus"></i> New Pro-Forma Invoice',
                    className: 'btn btn-primary btn-sm btn-round',
                    action: function () {
                        window.location.href = 'Pro-Forma-Invoice.php';
                    }
                }
            ]
          });
    });
}
function SendForApproval(pi_no)
{
    Swal.fire({
			title: 'Are you sure?',
			text: "Do you Really Want To Send For Approval?",
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Submit!'
		}).then((result) => {
			if (result.isConfirmed) {
				$.post("operation/pro_forma_operation.php", {
					Flag: "SendForApproval",
					pi_no: pi_no,
				}, function(data, success) {
					if (data == "Approved") {
						Swal.fire(
							'Well Done!',
							'Pro-Forma Sent For Approval Sucessfully.',
							'success'
						);
					} else {
						Swal.fire(
							'Error!',
              data,
							'error'
						);
					}
          ShowProFormaList();
				});	
			}
		});
}
function ApproveProForma(pi_no, ApproveStatus)
{
    if(ApproveStatus=="1")
    {
        var btntext = "Do You Want To Approve The Request?";
    }
    else
    {
        var btntext = "Do You Want To Deactivate This Pro-Forma Invoice ?";
    }
    Swal.fire({
        title: 'Are you sure?',
        text: btntext,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Submit!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("operation/pro_forma_operation.php", {
                Flag: "ProFormaRequestAction",
                pi_no:pi_no,
                ApproveStatus: ApproveStatus,
            }, function(data, success) {
                if (data == "Approved") {
                    Swal.fire(
                        'Well Done!',
                        'Pro-Forma Status Updated.',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'Error!',
                        data,
                        'error'
                    );
                }
                ShowProFormaList();
            });	
        }
    });
}
function sendproformamail(pi_no, client_id, pi_custom_number, pi_date)
{
    var c=confirm("Do You Really Want to send mail?");
    if(c==true)
    {
        showSpinner();
        $.post("send_mail_request.php",{
            Flag:"SendProFormaMail",
            pi_no:pi_no,
            client_id:client_id,
            pi_custom_number:pi_custom_number,
            pi_date:pi_date
        },function(data,success){
            hideSpinner();
            if(data=="Mail Send")
            {
                Swal.fire(
                    'Well Done!',
                    'ProForma Mail Send Successfully',
                    'success'
                );
            } else {
                Swal.fire(
                    'Error!',
                    data,
                    'error'
                );
            }
            ShowProFormaList();
        });
    }
}
function sendProformaWhatsApp(client_name, client_mob, pi_custom_number, pi_date) {
    let message =
        "Dear "+client_name+",\n\n" +
        "Thank you for your order.\n" +
        "Please find the *Proforma Invoice* number *" + pi_custom_number + "*  dated *" + pi_date + "*.\n\n" +
        "Should you have any questions, please do not hesitate to contact us.\n\n" +
        "We appreciate your business and look forward to our successful partnership.\n\n" +
        "*Best Regards,*\n" +
        "ELTEE DMCC\n";
    let encodedMessage = encodeURIComponent(message);
    let whatsappLink = "https://wa.me/" + client_mob + "?text=" + encodedMessage;
    window.open(whatsappLink, '_blank');
}


</script>