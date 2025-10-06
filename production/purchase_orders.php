
<?php include_once('header.php'); ?>
<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    margin-right: -450px;
  }
  .form-check-input {
    transform: scale(1.5);
    margin-right: 10px;
  }

  .form-check-label {
    font-size: 18px;
    font-weight: bold;
    color: #444;
  }

  .radio-label {
    color: #007bff;
    transition: color 0.3s ease-in-out;
  }

  .form-check-input:checked + .form-check-label .radio-label {
    color: #28a745;
  }

  .form-check-inline {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    transition: transform 0.3s;
  }

  .form-check-inline:hover {
    transform: scale(1.05);
    border-color: #007bff;
  }

  .swal2-custom-title {
      font-size: 20px !important;
      font-weight: bold;
      background-color: #007bff;
      color: white;
      text-align: -webkit-center;
      padding-bottom: 12px;
  }

  .swal2-custom-confirm, .swal2-custom-cancel {
      font-size: 12px !important;
      padding: 10px 20px !important; 
  }
  .readonly-select {
      pointer-events: none;
      background-color: #e9ecef;  /* Optional: make it look disabled */
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPurchaseOrder"></div>     
            </div>
        </div>
      </div>
  </div>
</div> 

<div class="modal fade" id="OpenPopUpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <form id="modalForm">
            <input type="hidden" class="form-control" id="txtpopupPINo" name="txtpopupPINo">
            <h5>Select PO Type</h5>
            <div class="container mt-3">
              <div class="row">
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purchaseOption" id="directPo" value="direct_po">
                    <label class="form-check-label" for="directPo">
                      <span class="radio-label">Direct PO</span>
                    </label>
                  </div>
                </div>
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="purchaseOption" id="usingProForma" value="using_proforma">
                    <label class="form-check-label" for="usingProForma">
                      <span class="radio-label">Using ProForma</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="LoadRefundPOModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="RefundPOModalHead"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <div id="POBasicDetailsContent"> 
                           
                </div>
                <br>
                <div id="RefundDetailsContent"> 
                           
                </div>
                <br>
                <div class="row">
                      <form id="frmRefundPO" enctype="multipart/form-data" >
                      <div class="col-md-4" style="max-width: 100% !important;">
                        
                          <input type="hidden" class="form-control" id="RefundPOId" name="RefundPOId">
                          <input type="hidden" class="form-control" id="POTotalAmount" name="POTotalAmount">
                          <input type="hidden" class="form-control" id="POPaidAmount" name="POPaidAmount">
                              <label for="refund_amount">Refund Amount <span class="text-danger">*</span></label>
                              <input type="number" class="form-control" id="refund_amount" name="refund_amount" required min="0">
                        
                      </div>
                     
                      <div class="col-md-4">
                        <br>
                          <button type="submit" class="btn btn-success btn-sm">Add Refund</button>
                      </div>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="LinkToPIModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="LinkToPIHead"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <input type="hidden" name="linkPO_id" id="linkPO_id">
                <div id="LinkToPIContent"> 
                           
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveLinkToPI">Save</button>
            </div>
        </div>
    </div>
</div>
<?php
  include_once('footer.php');
?>
<script>
  $(document).ready(function(){
    ShowPurchaseOrderList();

    $('#frmRefundPO').submit(function(e) {
        e.preventDefault();

        let refundAmount = parseFloat($('#refund_amount').val());
        let paidAmount = parseFloat($('#POPaidAmount').val());

        if (isNaN(refundAmount) || refundAmount <= 0) {
            Swal.fire('Invalid Input', 'Please enter a valid refund amount.', 'warning');
            return;
        }

        if (refundAmount > paidAmount) {
            Swal.fire('Invalid Refund', 'Refund amount cannot be greater than PO paid amount.', 'error');
            return;
        }

        Swal.fire({
          title: 'Are you sure?',
          text: "Do you Really Want To Refund Amount?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Submit!'
        }).then((result) => {
            if (result.isConfirmed) {

              var formData = {
                  Flag: 'SaveRefundPO',
                  po_id: $('#RefundPOId').val(),
                  refund_amount: $('#refund_amount').val()
              };

              $.post("operation/purchase_order_operation.php", formData, function(response) {
                  if (response.trim() === "Success") {
                          Swal.fire(
                              'Well Done!',
                              'Refund saved successfully.',
                                'success'
                          );
                      //alert("Refund saved successfully.");
                      $('#RefundPOId').val('');
                      $('#POTotalAmount').val('');
                      $('#POPaidAmount').val('');
                      $('#refund_amount').val('');
                      $('#LoadRefundPOModal').modal('hide');
                      location.reload();
                  } else {
                      alert("Error: " + response);
                  }
              });


            }
        });
    });

  });


  function ShowPurchaseOrderList()
  {
      $.post("operation/purchase_order_operation.php",{
            Flag:"ShowPurchaseOrderList"
      },function(data,success){
            $("#divRecordPurchaseOrder").html(data);
            $("#dtlRecord").DataTable({
              dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
              buttons: [
                  {
                      text: '<i class="fa fa-plus"></i> New Purchase Order',
                      className: 'btn btn-primary btn-sm btn-round',
                      action: function () {
                        LoadPurchaseModel();
                      }
                  }
              ]
            });
      });
  }
  function SendForApproval(po_id)
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
          $.post("operation/purchase_order_operation.php", {
            Flag: "SendForApproval",
            po_id: po_id,
          }, function(data, success) {
            if (data == "Approved") {
              Swal.fire(
                'Well Done!',
                'Purchase Order Sent For Approval Sucessfully.',
                'success'
              );
            } else {
              Swal.fire(
                'Error!',
                data,
                'error'
              );
            }
            ShowPurchaseOrderList();
          }); 
        }
      });
  }
  function LoadPurchaseModel(PONO)
  {
      $("#OpenPopUpModal").modal("toggle");
  }
  document.querySelectorAll('input[name="purchaseOption"]').forEach((radio) => {
    radio.addEventListener('change', function () {
      if (this.value === 'direct_po') {
        window.location.href = 'purchase_entry.php'; // Redirect to Direct PO page
      } else if (this.value === 'using_proforma') {
        window.location.href = 'purchase_direct_po.php'; // Redirect to Purchase Entry page
      }
    });
  });

  function sendpurchasemail(po_id, client_id, po_custom_number, po_date)
  {
      var c=confirm("Do You Really Want to send mail?");
      if(c==true)
      {
          showSpinner();
          $.post("send_mail_request.php",{
              Flag:"SendPurchaseOrderMail",
              po_id:po_id,
              client_id:client_id,
              po_custom_number:po_custom_number,
              po_date:po_date
          },function(data,success){
              hideSpinner();
              if(data=="Mail Send")
              {
                  Swal.fire(
                      'Well Done!',
                      'Purchase Order Mail Send Successfully',
                      'success'
                  );
              } else {
                  Swal.fire(
                      'Error!',
                      data,
                      'error'
                  );
              }
              ShowPurchaseOrderList();
          });
      }
  }
  function sendpurchaseWhatsApp(supplier_name, supplier_mob, po_custom_number, po_date) {
      let message =
          "Dear "+supplier_name+",\n\n" +
          "We are pleased to confirm our order.\n" +
          "Please find our Purchase Order number: "+po_custom_number+" attached to this email.\n\n" +
          "Thank you for your continued business with us.\n\n" +
          "*Best Regards,*\n" +
          "ELTEE DMCC\n";
      let encodedMessage = encodeURIComponent(message);
      let whatsappLink = "https://wa.me/" + supplier_mob + "?text=" + encodedMessage;
      window.open(whatsappLink, '_blank');
  }

  function CancelPO(po_id) 
  {
    Swal.fire({
        title: 'Cancel Purchase Order',
        html: `
            <textarea id="cancel_remark" class="swal2-textarea" placeholder="Enter cancellation remark" rows="4"></textarea>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Close',
        customClass: {
            title: 'swal2-custom-title',
            confirmButton: 'swal2-custom-confirm',
            cancelButton: 'swal2-custom-cancel'
        },
        preConfirm: () => {
            const remarkInput = Swal.getPopup().querySelector('#cancel_remark');
            const remark = remarkInput.value.trim();
            if (!remark) {
                Swal.showValidationMessage('Remark is required!');
            }
            return remark;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const remark = result.value;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to cancel this PO?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel it!'
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    $.post("operation/purchase_order_operation.php", {
                        Flag: "CancelPO",
                        po_id: po_id,
                        remark: remark
                    }, function(data, success) {
                        console.log(data);
                        if (data == "Success") {
                            Swal.fire(
                                'Well Done!',
                                'Purchase Order Cancelled Successfully.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                data,
                                'error'
                            );
                        }
                        ShowPurchaseOrderList();
                    });
                }
            });
        }
    });
  }



  function RefundPO(po_id, po_custom_number)
  {
      $("#LoadRefundPOModal").modal("toggle");
      $("#RefundPOModalHead").html("Refund PO : "+po_custom_number);
      $("#RefundPOId").val(po_id);
      $.post("operation/purchase_order_operation.php",{
        Flag:"LoadRefundDetails",
        po_id:po_id
      }, function(data) {
        //console.log(data);
        var res = JSON.parse(data);
        $("#POTotalAmount").val(res.grand_total);
        $("#POPaidAmount").val(res.popaidamount);
        $("#POBasicDetailsContent").html(res.basicpodetails);
        $("#RefundDetailsContent").html(res.popaymentdetails);
      });
  }

  function openLinkToPIModal(po_id){
    $('#linkPO_id').val(po_id);
    $('#LinkToPIModal').modal('show');
    $.post("operation/purchase_order_operation.php",{
        Flag:"LoadPODetails",
        po_id:po_id
      }, function(data) {
        var res = JSON.parse(data);
        $("#LinkToPIContent").html(res.basicPIdetails);
        $("#LinkToPIHead").html(res.heading);
    });
  }
      
  $(document).on('change', '.proforma-select', function() {
      var selectedQty = $(this).find(':selected').data('qty') || 0;
      var row = $(this).closest('tr');

      row.find('.qty-val').text(selectedQty);

      row.find('.use-qty').val('');
  });

  $(document).on('click', '#saveLinkToPI', function() {
        var po_id = $('#linkPO_id').val();
        var linkData = [];

        Swal.fire({
          title: 'Are you sure?',
          text: "Do you Really Want To Link PI?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Submit!'
        }).then((result) => {
            if (result.isConfirmed) {

                  $('#LinkToPIContent').find('tr').each(function() {
                      var proforma_detail_id = $(this).find('.proforma-select').val();
                      var pi_available_qty = $(this).find('.proforma-select option:selected').data('qty') || 0;
                      var actual_qty = $(this).find('.actual-qty').html();
                      var product_id = $(this).find('.product_id').html();
                      var use_qty = $(this).find('input[type="text"]').val() || 0;

                      if (proforma_detail_id) {
                          linkData.push({
                              proforma_detail_id: proforma_detail_id,
                              product_id:product_id,
                              pi_available_qty:pi_available_qty,
                              actual_qty:actual_qty,
                              use_qty: use_qty
                          });
                      }
                  });

                  $.post("operation/purchase_order_operation.php", {
                      Flag: "SaveLinkedPI",
                      po_id: po_id,
                      linkData: JSON.stringify(linkData)
                  }, function(response) {
                      //console.log(response);
                      if (response.trim() === "Success") {
                          Swal.fire("Saved!", "Linked PI data saved successfully", "success");
                          $('#LinkToPIModal').modal('hide');
                      } else {
                          Swal.fire("Error", "Failed to save data", "error");
                      }
                      ShowPurchaseOrderList();
                  });
            }
        });
    });

    $(document).on('change', '.use-qty', function () {
        validateRow($(this).closest('tr'));
    });

    function validateRow(row) {
        var actualQty = parseFloat(row.find('.actual-qty').text()) || 0;
        var product_id = parseFloat(row.find('.product_id').text()) || 0;
        var availableQty = parseFloat(row.find('.proforma-select option:selected').data('qty')) || 0;
        var enteredQty = parseFloat(row.find('.use-qty').val()) || 0;
        var selectElem = row.find('#proformaproduct_'+product_id);
        console.log(selectElem.length);  // Should be >= 1
        selectElem.addClass('readonly-select');
        // row.find('proforma-select_'+product_id).addClass('readonly-select');
        if (availableQty >= 0 && enteredQty > availableQty) {
            Swal.fire("Invalid Quantity", "You cannot enter more than " + availableQty + " bags (available in PI).", "warning");
            row.find('.use-qty').val(availableQty);
            return false;
        }
        else if (actualQty >= 0 && enteredQty > actualQty) {
            Swal.fire("Invalid Quantity", "You cannot enter more than " + actualQty + " bags (actual PO qty).", "warning");
            row.find('.use-qty').val(actualQty);
            return false;
        }
        else if (enteredQty < actualQty) {
            Swal.fire("Invalid Quantity", "You cannot enter less than " + actualQty + " bags (actual PO qty).", "warning");
            row.find('.use-qty').val(actualQty);
            return false;
        }
        else{
          return true;
        } 
    }
</script>