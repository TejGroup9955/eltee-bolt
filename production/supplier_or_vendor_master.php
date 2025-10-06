<?php
   include_once( 'header.php' );
?>

<!-- Custom CSS for Styling -->
<style>
   .form-container {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba( 0, 0, 0, 0.1 );
   }

   .form-control {
      width: 100%;
      border-radius: 5px;
      padding: 10px;
   }

   .form-control:focus {
      box-shadow: 0 0 10px rgba( 0, 123, 255, 0.5 );
   }

   .table th, .table td {
      text-align: center;
   }

   .table-container {
      margin-top: 20px;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba( 0, 0, 0, 0.1 );
      padding: 20px;
   }

   .btn-custom {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
   }

   .btn-custom:hover {
      background-color: #0056b3;
      border-color: #0056b3;
   }

   .form-heading {
      text-align: center;
      font-size: 24px;
      margin-bottom: 20px;
      color: #007bff;
   }

   .table-heading {
      background-color: #007bff;
      color: white;
   }

   #form_theme {
      background-color: #ffffff;
   }

   .form-control::placeholder {
      font-size: 0.9rem;
   }

   .font-size-select {
      font-size: 0.9rem;
   }

   .form-row {
      display: flex;
      flex-wrap: wrap;
   }

   .form-row .col-md-4 {
      flex: 1 1 33%;
      padding: 10px;
   }

   .form-row .col-md-6 {
      flex: 1 1 50%;
      padding: 10px;
   }

   .form-row .col-md-12 {
      flex: 1 1 100%;
      padding: 10px;
   }
</style>

<!-- Page Content -->
<div class="right_col" role="main">
   <div class="container mt-4">
      <div class="row">
         <!-- Form Section -->
         <div class="col-md-12">
            <div class="form-container mt-4" id="form_theme">
               <form action="" method="post">
                  <div class="form-row">
                     <div class="col-md-4">
                        <label for="supplier/vendor name" class="form-label">
                           <i class="fas fa-user-tag"></i> Supplier/Vendor Name:
                        </label>
                        <input type="text" id="short_designation" name="short_designation" class="form-control" placeholder="Enter Client Name" required>
                     </div>
                     <div class="col-md-4">
                        <label for="department" class="form-label">Country</label>
                        <select id="department" name="department" class="form-control font-size-select" required>
                           <option value="select">Select Country</option>
                           <option value="Admin">India</option>
                           <option value="HR">Australia</option>
                           <option value="Purchase">UK</option>
                        </select>
                     </div>
                     <div class="col-md-4">
                        <label for="state" class="form-label">State</label>
                        <select id="state" name="state" class="form-control font-size-select" required>
                           <option value="select">Select State</option>
                           <option value="Admin">Maharashtra</option>
                           <option value="HR">Melbourne</option>
                           <option value="Purchase">England</option>
                        </select>
                     </div>
                  </div>

                  <div class="form-row">
                     <div class="col-md-4">
                        <label for="region" class="form-label">Region</label>
                        <select id="region" name="region" class="form-control font-size-select" required>
                           <option value="select">Select Region</option>
                           <option value="DELHI">DELHI</option>
                           <option value="RAJASTHAN">RAJASTHAN</option>
                           <option value="RANTHAMBORE">RANTHAMBORE NATIONAL PARK</option>
                           <option value="JAIPUR">JAIPUR</option>
                           <option value="JODHPUR">JODHPUR</option>
                           <option value="NARLAI">NARLAI</option>
                           <option value="UDAIPUR">UDAIPUR</option>
                           <option value="JAISALMER">JAISALMER</option>
                        </select>
                     </div>

                     <div class="col-md-4">
                        <label for="address" class="form-label">
                           <i class="fas fa-user-tag"></i> Address:
                        </label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Enter Address" required>
                     </div>
                     <div class="col-md-4">
                        <label for="contact_person" class="form-label">
                           <i class="fas fa-user-tag"></i> Contact Person:
                        </label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Enter Contact Person" required>
                     </div>
                  </div>

                  <div class="form-row">
                     <div class="col-md-4">
                        <label for="contact_number" class="form-label">
                           <i class="fas fa-user-tag"></i> Contact Number:
                        </label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter Contact Number" required>
                     </div>
                     <div class="col-md-4">
                        <label for="email" class="form-label">
                           <i class="fas fa-user-tag"></i> Email ID:
                        </label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email ID" required>
                     </div>
                     <div class="col-md-4">
                        <label for="lic_number" class="form-label">
                           <i class="fas fa-user-tag"></i> Lic Number:
                        </label>
                        <input type="text" id="lic_number" name="lic_number" class="form-control" placeholder="Enter Lic Number" required>
                     </div>
                  </div>

                  <div class="form-row">
                     <div class="col-md-4">
                        <label for="add_contact_name" class="form-label">
                           <i class="fas fa-user-tag"></i> Add More Contact Number and Name:
                        </label>
                        <input type="text" id="add_contact_name" name="add_contact_name" class="form-control" placeholder="Enter Contact and Name" required>
                     </div>

                     <div class="col-md-6">
                        <button type="submit" class="btn btn-custom">Submit</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- /Page Content -->
<?php
   include_once( 'footer.php' );
?>
