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
  <div class="container-xxl flex-grow-1">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                <form id="frmBankDetails">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                        <label class="form-label">Bank Name</label>
                            <input type="hidden" class="form-control" id="bankId" name="bankId">
                            <select type="text" class="form-control form-select" id="bankname" name="bankname" required>
                                <option value="">Select Bank</option>
                                <?php
                                    $rstbank = mysqli_query($connect,"select * from bank_master where status='Active'");
                                    while($rwbank = mysqli_fetch_assoc($rstbank))
                                    {
                                        $bank_id = $rwbank['bank_id'];
                                        $bank_name = $rwbank['bank_name'];
                                        echo "<option value='$bank_id'>$bank_name</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" class="form-control" id="branch" name="branch" placeholder="Enter Branch" required>
                        </div>
                        <div class="col-md-12 mb-3">
                        <label class="form-label">Bank Address</label>
                        <textarea type="text" class="form-control" id="bankaddress" name="bankaddress" placeholder="Enter Branch Address" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select State">
                            <label>Select Currency</label>
                            <select required="" data-live-search="true" name="currency_id[]" id="currency_id" class="form-control js-example-basic-multiple has_data" multiple="multiple">
                                <?php
                                    $rstcur = mysqli_query($connect,"select id,currency_code from country_master where currency_code!=''");
                                    while($rwcur = mysqli_fetch_assoc($rstcur))
                                    {
                                        $id = $rwcur['id'];
                                        $currency_code = $rwcur['currency_code'];
                                        echo "<option value='$id'>$currency_code</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label">P.O. BOX</label>
                        <input type="text" class="form-control" id="pobox" name="pobox" placeholder="Enter P.O.Box" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="accountName" name="accountName" placeholder="Enter Account Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="accountNumber" name="accountNumber" placeholder="Enter Account Number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label">IBAN No</label>
                        <input type="text" class="form-control" id="ibannumber" name="ibannumber" placeholder="Enter IBAN No" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Swift Code</label>
                        <input type="text" class="form-control" id="Swiftcode" name="Swiftcode" placeholder="Enter Swift Code" required>
                        </div>
                        <div class="col-md-12 mt-0">
                        <input type="submit" class="btn btn-sm btn-success" id="btnSave" value="Save">
                        <a href="bank_master.php"><button type="button" class="btn btn-sm btn-warning">Reset</button></a>
                        <a href="index.php"><button type="button" class="btn btn-sm btn-secondary">Close</button></a>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                <div class="table-responsive" id="divRecordEmployee"></div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<!-- /Page Content -->

<?php include_once('footer.php'); ?>
<script>
  $(document).ready(function(){
    ShowBankDetails();
    $('.js-example-basic-multiple').select2({
        placeholder:"Select Currency"
    });
    $("#frmBankDetails").submit(function(e){
          e.preventDefault();
          var formData = new FormData(this);
          var form = event.target;
          formData.append("Flag", "NewBankDetails");
          $.ajax({
              url: "operation/bank_operation.php", 
              type: "POST", // Type of the request
              data: formData, // The form data to send
              processData: false, // Prevent jQuery from automatically converting the data to a query string
              contentType: false, // Prevent jQuery from setting the content type
              success: function(response) {
                  if(response=="Inserted")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Bank Details Added Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          location.reload();
                      }, 2000);
                  }
                  else if(response=="Updated")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Bank Details Updated Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          location.reload();
                      }, 2000);
                  }else
                  {
                      Swal.fire(
                          'Error!',
                          response,
                          'error'
                      );
                  }
                  ShowBankDetails();
              },
          });
    });
  });
  function ShowBankDetails()
    {
        $.post("operation/bank_operation.php",{
              Flag:"ShowBankDetails"
        },function(data,success){
              $("#divRecordEmployee").html(data);
              $("#dtlRecord").DataTable({});
        });
    }
    function UpdateFunction(TableName, CompareField, CompareId)
      {
        $.post("operation/CrudOperation.php",{
               Flag:"UpdateFunction",
               TableName: TableName,
               CompareField:CompareField,
               CompareId: CompareId
         },function(data,success){
               var res = JSON.parse(data);
               console.log(res);
               $("#bankId").val(res.id);
               $("#bankname").val(res.bank_id);
               $("#bankaddress").val(res.bank_address);
               $("#branch").val(res.branch);
               $("#pobox").val(res.po_box);
               $("#accountName").val(res.account_name);
               $("#accountNumber").val(res.account_no);
               $("#ibannumber").val(res.iban_no);
               $("#Swiftcode").val(res.swift_code);
               $("#btnSave").val("Update");
               $("#currency_id").val([]).trigger("change"); 
                if (res.currency_id !== "") {
                  var PackagingArray = res.currency_id.split(","); 
                  var SelectedIDs = []; 
                  $("#currency_id option").each(function() {
                      var option = $(this);
                      var optionText = option.text().trim(); 
                      var optionValue = option.val(); 
                      if (PackagingArray.includes(optionValue)) {
                          SelectedIDs.push(optionValue); 
                      }
                  });
                  $("#currency_id").val(SelectedIDs).trigger("change");
                }
         });
      }

</script>
