<?php
   include_once( 'header.php' );
?>
<style>
   .form-control {
      border-radius: 5px;
      font-size: 0.9rem; 
   }
   .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
      padding-left: 20px;
   }
</style>


<!-- page content -->
<div class="right_col" role="main">
   <div class="container-xxl flex-grow-1 mt-4">
      <div class="card">
         <div class="card-body">
            <div class="mb-3 text-right">
               <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#expenseModal" style="padding: 0.200rem 0.200rem; font-size: 12px;">
                  <i class="fa fa-plus"></i> New Expenses
               </button>
            </div>
            <div class="table-responsive" id="divRecordAccountExpenses">

            </div>
           
         </div>
      </div>


      <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
          <form method="POST" enctype="multipart/form-data" id="expenseForm" autocomplete="off">
         <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
               <div class="modal-header bg-info text-white">
                  <h5 class="modal-title" id="expenseModalLabel">New Expense</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>

               <!-- Modal Body -->
               <div class="modal-body">
                 
                     <div class="row">
                        <!-- Account Name -->
                        <div class="col-md-4">
                           <label class="form-label">Account Name</label>
                           <select name="expense_account_id" id="expense_account_id" required class="form-control">
                              <option value="">Choose Account</option>
                              <?php
                              $cmd = "SELECT account_id, account_name FROM account_master";
                              $result = $connect->query($cmd);
                              if ($result->num_rows > 0) {
                                  while($row = $result->fetch_assoc()) {
                                      echo '<option value="'.$row['account_id'].'">'.$row['account_name'].'</option>';
                                  }
                              }
                              ?>
                           </select>
                        </div>

                        <!-- Expense Type -->
                        <div class="col-md-4">
                           <label class="form-label">Expense Type</label>
                           <select class="form-control" name="expense_type" id="expense_type" required>
                              <option value="">Select Type</option>
                              <option value="Direct Expenses">Direct Expenses</option>
                              <option value="Shipment">Against Shipment</option>
                           </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-4">
                           <label class="form-label">Expense Amount</label>
                           <input type="number" class="form-control" name="expense_amt" id="expense_amt" required>
                        </div>
                     </div>

                     <div class="row mt-3 expenseFields" style="display:none;">
                        <div class="col-md-4">
                           <label class="form-label">Invoice Date</label>
                           <input type="date" class="form-control" name="invoice_date" id="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Invoice No</label>
                           <input type="text" class="form-control" name="invoice_no" id="invoice_no">
                        </div>
                     </div>

                     <!-- Shipment Fields -->
                     <div class="row mt-3 shipmentFields" style="display:none;">
                        <div class="col-md-4">
                           <label class="form-label">Invoice No</label>
                           <input type="text" class="form-control" name="inv_no" id="inv_no">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">B/L No</label>
                           <select name="bl_no" id="bl_no" class="form-control">
                              <option value="">Choose</option>
                              <?php
                              $cmd = "SELECT bl_no FROM tax_invoice_head";
                              $result = $connect->query($cmd);
                              if ($result->num_rows > 0) {
                                  while($row = $result->fetch_assoc()) {
                                      echo '<option value="'.$row['bl_no'].'">'.$row['bl_no'].'</option>';
                                  }
                              }
                              ?>
                           </select>
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Purpose</label>
                           <select class="form-control" name="purpose" id="purpose">
                              <option value="">Select Purpose</option>
                              <option value="Switch BL">Switch BL</option>
                              <option value="Shipline Tracking">Shipline Tracking</option>
                           </select>
                        </div>
                     </div>

                     <!-- Remarks & Attachments -->
                     <div class="row mt-3">
                        <div class="col-md-6">
                           <label class="form-label">Remark</label>
                           <textarea class="form-control" name="remark" id="remark" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                           <label class="form-label">Attachments</label>
                           <input type="file" class="form-control" name="attachments[]" multiple>
                           <small class="text-muted">You can select multiple files.</small>
                        </div>
                     </div>
                 
               </div>

               <!-- Modal Footer -->
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" id="saveExpenseBtn" class="btn btn-primary">Save Expense</button>
               </div>
            </div>
         </div>
          </form>
      </div>
         
  </div>
</div>
<!-- /page content -->

<?php
   include_once( 'footer.php' );
?>
<script>
   $(document).ready(function(){
      ShowAccountExpensesMaster();

      $('.shipmentFields').hide();
      $('.expenseFields').hide();
      
      $('#expense_type').on('change', function () {
          if ($(this).val() === 'Shipment') {
              $('.shipmentFields').show();
              $('.expenseFields').hide();
          } else if ($(this).val() === 'Direct Expenses') {
              $('.expenseFields').show();
              $('.shipmentFields').hide();
          } else{
              $('.shipmentFields').hide();
              $('.expenseFields').hide();
          }
      });

      $('#expenseForm').submit(function (e) {
                e.preventDefault(); // prevent form submit once
                let account_id = $('#expense_account_id').val();
                let type = $('#expense_type').val();
                let amount = $('#expense_amt').val();
                let remark = $('#remark').length ? $('#remark').val().trim() : "";
                let invoiceNo = $('#invoice_no').length ? $('#invoice_no').val().trim() : "";
                let invNo = $('#inv_no').length ? $('#inv_no').val().trim() : "";
                let billNo = $('#bl_no').length ? $('#bl_no').val().trim() : "";

                // Validate account
                if (account_id === '') {
                    Swal.fire('Missing Field', 'Please select account.', 'warning');
                    return;
                }

                // Validate type
                if (type === '') {
                    Swal.fire('Missing Field', 'Please select expense type.', 'warning');
                    return;
                }

                // Validate amount
                if (amount === '') {
                    Swal.fire('Missing Field', 'Please add expenses amount', 'warning');
                    return;
                }

                // Validate remark
                if (remark === '') {
                    Swal.fire('Missing Field', 'Please enter a remark.', 'warning');
                    return;
                }

                // Validate shipment-specific fields
                if (type === 'Direct Expenses') {
                    if (invoiceNo === '') {
                        Swal.fire('Missing Field', 'Please enter Invoice No', 'warning');
                        return;
                    }
                }else if (type === 'Shipment') {
                    if (invNo === '' || billNo === '') {
                        Swal.fire('Missing Field', 'Please enter both Invoice No and Bill No for Against Shipment.', 'warning');
                        return;
                    }
                }
                
                // Prepare form data
                let formData = new FormData(this);
                formData.append("Flag", "AddExpenses");

                // AJAX submit
                $.ajax({
                    url: "operation/AccountMasterOperation.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        Swal.fire(
                            'Well Done!',
                            'Expense saved successfully!',
                            'success'
                        );
                        setTimeout(() => {
                            window.location.href = "expense_master.php";
                        }, 2000);
                    }
                });
      });


      $('#bl_no').on('change', function () {
         var blNo = $(this).val();
         var $purpose = $('#purpose');

         $purpose.empty().append('<option value="">Loading...</option>');

         if (blNo === "") {
               $purpose.append('<option value="">Select Purpose</option>');
               return;
         }

         $.ajax({
               url: 'operation/AccountMasterOperation.php',
               type: 'POST',
               data: { Flag: 'IsTelexReleased', bl_no: blNo },
               dataType: 'json',
               success: function (response) {
                  //console.log(response);
                  $purpose.empty();

                  if (response.status === 'success' && response.options.length > 0) {
                     $purpose.append('<option value="">Select Purpose</option>');
                     $.each(response.options, function (index, option) {
                           $purpose.append('<option value="' + option + '">' + option + '</option>');
                     });
                  } else {
                     $purpose.append('<option value="">No Purposes Available</option>');
                  }
               },
               error: function (xhr, status, error) {
                  console.log('AJAX Error: ' + error);
                  $purpose.empty().append('<option value="">Error loading purposes</option>');
               }
         });
      });


   });
   
   function ShowAccountExpensesMaster()
   {
         $.post("operation/AccountMasterOperation.php",{
               Flag:"ShowAccountExpensesMaster"
         },function(data,success){
               $("#divRecordAccountExpenses").html(data);
               $("#dtlRecord").DataTable({});
         });
   }
      
      
</script>
