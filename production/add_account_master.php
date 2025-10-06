<?php
   include_once( 'header.php' );

   $edit_mode = false;
   $account_data = [];
   $contacts_data = [];
   $account_id  = '';
   if (isset($_GET['account_id'])) {
       $deaccount_id = base64_decode($_GET['account_id']);
       if(is_numeric($deaccount_id)){
          $edit_mode = true;
          $account_id = $deaccount_id;

          // Get account data
          $sql = "SELECT * FROM account_master WHERE account_id = $account_id LIMIT 1";
          $result = mysqli_query($connect, $sql);
          if ($result && mysqli_num_rows($result) > 0) {
              $account_data = mysqli_fetch_assoc($result);
          }

          // Get contact data
          $sql_contacts = "SELECT * FROM account_contact_details WHERE account_id = $account_id";
          $result_contacts = mysqli_query($connect, $sql_contacts);
          while ($row = mysqli_fetch_assoc($result_contacts)) {
              $contacts_data[] = $row;
          }
      }
   }
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
   <form method="POST" enctype="multipart/form-data"  autocomplete="off" id="frmaccountmaster">
      <div class="container-xxl flex-grow-1 mt-4">
         <div class="card">
            <div class="card-header bg-info text-white">
               <h6 class="mb-0" style='color:white'><?= $edit_mode ? 'Edit Account' : 'New Account' ?></h6>
            </div>
            <div class="card-body">
               <div class="row">
                  <div class="col-md-3">
                     <label class="form-label">Account Name</label>
                     <input type="text" class="form-control required" id="account_name" name="account_name" value="<?= $edit_mode ? htmlspecialchars($account_data['account_name']) : '' ?>"  placeholder="Enter Account Name" required>
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">GST No.</label>
                     <input type="text" class="form-control" id="GST_no" name="GST_no" placeholder="Enter GST No." value="<?= $edit_mode ? htmlspecialchars($account_data['GST_no']) : '' ?>">
                  </div>
                  <!-- <div class="col-md-3">
                     <label class="form-label">Fertilizer License No.</label>
                     <input type="text" class="form-control" id="fertilizer_license_no" name="fertilizer_license_no" value="<?= $edit_mode ? htmlspecialchars($account_data['fertilizer_license_no']) : '' ?>"  placeholder="Enter Fertilizer License No" required>
                  </div>
               </div>
               <div class="row mt-3"> -->
                  <div class="col-md-3">
                     <label class="form-label">Account Type</label>
                     <select name="account_type" id="account_type" required class="form-control required">
                         <option value="">Select</option> 
                         <?php
                         $cmd = "SELECT id, account_type_name FROM account_type_master";
                         $result = $connect->query($cmd);
                         if ($result->num_rows > 0) {
                           while($row = $result->fetch_assoc()) {
                            $selected = ($edit_mode && $row['id'] == ($account_data['account_type'] ?? '')) ? 'selected' : '';
                                    
                            echo '<option value='.$row['id'].' '.$selected.'>'.$row['account_type_name'].'</option> ';
                         }
                      }
                      ?>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Company Email ID</label>
                     <input type="text" class="form-control required" id="email_id" name="email_id" placeholder="Enter Email ID" value="<?= $edit_mode ? htmlspecialchars($account_data['email_id']) : '' ?>"  required>
                  </div>
                  <!-- <div class="col-md-3">
                     <label class="form-label">License Validity Date</label>
                     <input type="date" class="form-control" id="license_validity_date" name="license_validity_date" value="<?= $edit_mode ? htmlspecialchars($account_data['license_validity_date']) : '' ?>"  placeholder="Enter License Validity Date" required>
                  </div> -->
               </div>
               <div class="row mt-3">
                  <div class="col-md-3">
                     <label class="form-label">Address</label>
                     <textarea type="text" class="form-control required" id="address" name="address" placeholder="Enter Address" rows="2" required><?= $edit_mode ? htmlspecialchars($account_data['address']) : '' ?></textarea>
                  </div>
                  <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select Country">
                       <label>Country</label>
                       <select required="" data-live-search="true" name="country_id" id="country_id" class="form-control small-input selectpicker1 has_data required"  onchange="loadClientState();">
                           <option value=''>Select Country</option>
                       </select>
                       <br>
                  </div>
                  <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Select State">
                       <label>State</label>
                       <select required="" data-live-search="true" name="state_id" id="state_id" class="form-control small-input selectpicker1 has_data required">
                           <option value=''>Select State</option>
                       </select>
                       <br>
                  </div>
                  <div class="col-md-3" data-toggle="tooltip" data-placement="right" title="" data-original-title="Enter Mobile No.">
                       <label>Mobile No</label>
                        <input type="number" class="form-control Number required" id="contact_no" name="contact_no" placeholder="Enter Mobile Number" value="<?= $edit_mode ? htmlspecialchars($account_data['contact_no']) : '' ?>" maxlength="15" minlength="10">
                       <br>
                  </div>
               </div>
               <div class="row mt-3" style="display:none">
                  <div class="col-md-3">
                     <label class="form-label">Landline Number</label>
                     <!-- <input type="tel" class="form-control Number" id="contact_no" name="contact_no" placeholder="Enter Landline Number" value="<?= $edit_mode ? htmlspecialchars($account_data['contact_no']) : 'NA' ?>" maxlength="15" minlength="10"> -->
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Debit Limit</label>
                     <input type="text" class="form-control Number" id="debit_limit" name="debit_limit" placeholder="Enter Debit Limit" value="<?= $edit_mode ? htmlspecialchars($account_data['debit_limit']) : 'NA' ?>">
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Credit Limit</label>
                     <input type="text" class="form-control" id="credit_limit" name="credit_limit" placeholder="Enter Credit Limit" value="<?= $edit_mode ? htmlspecialchars($account_data['credit_limit']) : 'NA' ?>">
                  </div>
               </div>
               <div class="row mt-3">
                  <h6>Bank Details</h6>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <label class="form-label">Bank Name</label>
                     <input type="text" class="form-control required" id="bank_name" name="bank_name" placeholder="Enter Bank Name" value="<?= $edit_mode ? htmlspecialchars($account_data['bank_name']) : '' ?>" required>
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Branch Name</label>
                     <input type="text" class="form-control required" id="bank_branch_name" name="bank_branch_name" value="<?= $edit_mode ? htmlspecialchars($account_data['bank_branch_name']) : '' ?>" placeholder="Enter Branch Number" required>
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Bank Address</label>
                     <textarea type="text" class="form-control required" id="bank_address" name="bank_address" placeholder="Enter Bank Address" rows="2" required><?= $edit_mode ? htmlspecialchars($account_data['bank_address']) : '' ?></textarea>
                  </div>
                  <div class="col-md-3">
                     <label class="form-label">Account Number</label>
                     <input type="text" class="form-control required" id="bank_acc_no" name="bank_acc_no" placeholder="Enter Account Number" value="<?= $edit_mode ? htmlspecialchars($account_data['bank_acc_no']) : '' ?>" required>
                  </div>
               </div>
               <div class="row mt-3">
                
                  <div class="col-md-3">
                     <label class="form-label">IBAN No.</label>
                     <input type="text" class="form-control required" id="IBAN_No" name="IBAN_No" placeholder="Enter IBAN No" value="<?= $edit_mode ? htmlspecialchars($account_data['IBAN_No']) : '' ?>" required>
                  </div>
               
                  <div class="col-md-3">
                     <label class="form-label">Swift Code</label>
                     <input type="text" class="form-control required" id="swit_code" name="swit_code" placeholder="Enter Swit Code" value="<?= $edit_mode ? htmlspecialchars($account_data['swit_code']) : '' ?>" required>
                  </div>
                  
               </div>
               <div class="row mt-3">
                  <h6>Contact Details</h6>
               </div>
               
               <div id="contact_details_section">
                        <?php if ($edit_mode && !empty($contacts_data)){ ?>
                            <?php foreach ($contacts_data as $contact){ ?>
                                <div class="row contact-row mb-2">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="contact_person_name[]" value="<?= htmlspecialchars($contact['person_name']) ?>" placeholder="Enter Contact Person Name" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="contact_person_number[]" value="<?= htmlspecialchars($contact['contact_number']) ?>" placeholder="Enter Contact Number" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="email" class="form-control" name="contact_person_email[]" value="<?= htmlspecialchars($contact['email']) ?>" placeholder="Enter Contact Email" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-danger btn-sm remove_contact">&times;</button>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="row contact-row mb-2">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="contact_person_name[]" placeholder="Enter Contact Person Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="contact_person_number[]" placeholder="Enter Contact Number">
                            </div>
                            <div class="col-md-3">
                                <input type="email" class="form-control" name="contact_person_email[]" placeholder="Enter Contact Email">
                            </div>
                            <div class="col-md-3">
                               <button type="button" class="btn btn-success btn-sm mb-3" id="add_more_contact"><i class="fa fa-plus"></i> Add More</button>
                            </div>
                        </div>
               </div>
               
               <div class="row">
                  <div class="col-md-12 text-end">
                     <button type="submit" class="btn btn-primary">Save Account</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </form>
</div>
<!-- /page content -->

<?php
   include_once( 'footer.php' );
?>
<script>
   $(document).ready(function(){
      loadClientCountry();
      let isEdit = <?= $edit_mode ? 'true' : 'false' ?>;
      let selectedCountry = "<?= $edit_mode ? htmlspecialchars($account_data['country_id'] ?? '') : '' ?>";
      let selectedState = "<?= $edit_mode ? htmlspecialchars($account_data['state_id'] ?? '') : '' ?>";
      loadClientCountry(selectedCountry,selectedState);

      $("#frmaccountmaster").submit(function(e){
          e.preventDefault();
          var formData = new FormData(this);
          var form = event.target;
          formData.append("Flag", isEdit ? "UpdateAccount" : "NewAccount");
          if (isEdit) {
              formData.append("account_id", <?= $edit_mode ? $account_id : 'null' ?>);
          }
          $.ajax({
              url: "operation/AccountMasterOperation.php", 
              type: "POST", 
              data: formData,
              processData: false,
              contentType: false, 
              success: function(response) {
                  if(response=="Inserted")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Account Details Added Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          window.location.href = "account_master.php";
                      }, 2000);
                  }
                  else if(response=="Updated")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Account Updated Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          window.location.href = "account_master.php";
                      }, 2000);
                  }else
                  {
                      Swal.fire(
                          'Error!',
                          response,
                          'error'
                      );
                  }
              },
          });
      });

      $('#add_more_contact').click(function(){
          let html = `<div class="row contact-row mb-2">
              <div class="col-md-3">
                  <input type="text" class="form-control" name="contact_person_name[]" placeholder="Enter Person Name" required>
              </div>
              <div class="col-md-3">
                  <input type="text" class="form-control" name="contact_person_number[]" placeholder="Enter Contact Number" required>
              </div>
              <div class="col-md-3">
                  <input type="email" class="form-control" name="contact_person_email[]" placeholder="Enter Email" required>
              </div>
              <div class="col-md-1">
                  <button type="button" class="btn btn-danger btn-sm remove_contact">&times;</button>
              </div>
          </div>`;
          $('#contact_details_section').append(html);
      });

   });

   $(document).on('click', '.remove_contact', function(){
       $(this).closest('.contact-row').remove();
   });

    function loadClientCountry(selectedCountry = '',selectedState = '')
    {
        $.post("../component.php",{
            Flag:"LoadCountry",
        },function(data,success){
            $("#country_id").html(data);

            if(selectedCountry){
               $("#country_id").val(selectedCountry);
               loadClientState(selectedCountry,selectedState);
            }
        });
    }
    
    function loadClientState(countryId = '',selectedState = '')
    {
         if (!countryId) {
            countryId = $("#country_id").val();
         }

        $.post("../component.php",{
            Flag:"LoadState",
            CountryId : countryId
        },function(data,success){
            $("#state_id").html(data);
            if(selectedState){
               $("#state_id").val(selectedState);
            }
        });
    } 
   
   $(document).on('change', '#country_id', function() {
       loadClientState($(this).val());
   });
</script>
