<?php include_once('header.php'); ?>

<!-- Custom Styles -->
<style>
  .form-control {
    border-radius: 5px;
    font-size: 0.9rem; 
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
      padding-left: 20px;
   }
</style>
<!-- Page Content -->
<div class="right_col" role="main">
    <form method="POST" enctype="multipart/form-data"  novalidate  autocomplete="off" id="expenseForm">
        <div class="container-xxl flex-grow-1 mt-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                <h6 class="mb-0" style='color:white'>New Expenses</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Account Name</label>
                            <select name="expense_account_id" id="expense_account_id" required class="form-control">
                                <option value="">Choose Account</option> 
                                <?php
                                $cmd = "SELECT account_id, account_name FROM account_master";
                                $result = $connect->query($cmd);
                                if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<option value='.$row['account_id'].'>'.$row['account_name'].'</option> ';
                                }
                            }
                            ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expense Type</label>
                            <select class="form-control" name="expense_type" id="expense_type" required>
                                <option value="">Select Type</option>
                                <option value="Direct Expenses">Direct Expenses</option>
                                <option value="Shipment">Against Shipment</option>
                            </select>  
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Expense Amount</label>
                            <input type="text" class="form-control" name="expense_amt" id="expense_amt" required>
                        </div>
                    
                    </div>
                    <div class="row mt-3 shipmentFields">
                        <div class="col-md-4">
                            <label class="form-label">Invoice No</label>
                            <input type="text" class="form-control" name="invoice_no" id="invoice_no">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">B/L No</label>
                            <input type="text" class="form-control" name="bill_no" id="bill_no">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                          <label class="form-label">Remark</label>
                          <textarea class="form-control" name="remark" id="remark" rows="3" required></textarea>
                        </div>
                    
                        <div class="col-md-4">
                          <label class="form-label">Attachments</label>
                          <input type="file" class="form-control" name="attachments[]" multiple>
                          <small class="text-muted">You can select multiple files.</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <form>
</div>
<!-- /Page Content -->

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function(){
        $('.shipmentFields').hide();

        $('#expense_type').on('change', function () {
          if ($(this).val() === 'Shipment') {
              $('.shipmentFields').show();
          } else {
              $('.shipmentFields').hide();
              $('#invoice_no').val('');
              $('#bill_no').val('');
          }
        });

        $('#expenseForm').submit(function (e) {
                e.preventDefault(); // prevent form submit once
                let account_id = $('#expense_account_id').val();
                let type = $('#expense_type').val();
                let amount = $('#expense_amt').val();
                let remark = $('#remark').length ? $('#remark').val().trim() : "";
                let invoiceNo = $('#invoice_no').length ? $('#invoice_no').val().trim() : "";
                let billNo = $('#bill_no').length ? $('#bill_no').val().trim() : "";

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
                if (type === 'Shipment') {
                    if (invoiceNo === '' || billNo === '') {
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
    });
</script>