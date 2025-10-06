<?php
   include_once( 'header.php' );
   $branchlist =[];
   if(isset($_GET['UserId']))
   {
      $UserIdd = base64_decode($_GET['UserId']);
      $rstUpdate = mysqli_query($connect,"select * from user_master where user_id='$UserIdd'");
      $rwUpdate = mysqli_fetch_assoc($rstUpdate);
      $rstbranch = mysqli_query($connect,"select b.*,bm.branch_name from branch_switcher b
      inner join branch_master bm on bm.branch_id = b.branch_id
      where b.user_id='$UserIdd'");
      if(mysqli_num_rows($rstbranch)>0){
         while($rwbranch = mysqli_fetch_assoc($rstbranch))
         {
            $branchlist[] = $rwbranch['branch_id'];
         }
      }
      extract($rwUpdate);

      echo '
      <script src="../vendors/jquery/dist/jquery.min.js"></script>
      <script>
         $(document).ready(function(){
            $("#EmployeeId").val('.$user_id.');
            $("#EmployeeName").val("'.$user_name.'");
            $("#gender").val("'.$gender.'");
            $("#Address").val("'.$user_add.'");
            $("#username").val("'.$user_email.'");
            $("#email").val("'.$user_email_address.'");
            $("#contact_number").val("'.$user_mob.'");
            $("#txtpassword").val("'.$user_pwd.'");
            $("#confirm_password").val("'.$user_pwd.'");
            $("#btnSave").val("Update");
            setTimeout(() => {
               $("#ReportTo").val("'.$report_to.'");
               $("#designation").val("'.$user_type_id.'");
            }, 1000); 
         });
      </script>';
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
   <div class="container-xxl flex-grow-1 mt-4">
      <div class="card">
         <div class="card-body">
            <form id="frmEmployee">
                  <div class="row">
                     <div class="col-md-3">
                        <label>Employee Name:</label>
                        <input type="hidden" id="EmployeeId" name="EmployeeId" class="form-control" >
                        <input type="text" id="EmployeeName" name="EmployeeName" class="form-control" placeholder="Enter Employee Name" required>
                     </div>
                     <div class="col-md-3">
                        <label for="designation" class="form-label">Designation:</label>
                        <select id="designation" name="designation" class="form-control font-size-select" required>
                           
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label for="gender" class="form-label">Gender:</label>
                        <select id="gender" name="gender" class="form-control font-size-select" required>
                           <option value="select">Select Gender</option>
                           <option value="Male">Male</option>
                           <option value="Female">Female</option>
                           <option value="Other">Other</option>
                        </select>
                     </div>                       
                     <div class="col-md-3 mt-2">
                        <label for="contact_number" class="form-label">
                           <i class="fas fa-user-tag"></i> Contact Number:
                        </label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter Contact Number" required>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="email" class="form-label">
                           <i class="fas fa-user-tag"></i> Email ID:
                        </label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email ID" required>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="User ID" class="form-label">
                           <i class="fas fa-user-tag"></i> User Name
                        </label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter User Name" required>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="password" class="form-label">
                           <i class="fas fa-user-tag"></i> Password:
                        </label>
                        <input class="form-control" type="password" id="txtpassword" name="txtpassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&amp;*]).{8,}" title="Minimum 8 Characters Including An Upper And Lower Case Letter, A Number And A Unique Character" required="">
                        <span style="position: absolute;right: 15px;top: 25px;" onclick="hideshow()">
                           <i id="slash" class="fa fa-eye-slash" style="display: none;"></i>
                           <i id="eye" class="fa fa-eye" style="display: block;"></i>
                        </span>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="confirm_password" class="form-label">
                           <i class="fas fa-user-tag"></i> Confirm Password:
                        </label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&amp;*]).{8,}" title="Minimum 8 Characters Including An Upper And Lower Case Letter, A Number And A Unique Character" required="">
                        <span style="position: absolute;right: 15px;top: 25px;" onclick="hideshow2()">
                           <i id="slash" class="fa fa-eye-slash" style="display: none;"></i>
                           <i id="eye" class="fa fa-eye" style="display: block;"></i>
                        </span>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="Upload Photo" class="form-label">
                           <i class="fas fa-user-tag"></i> Upload Photo:
                        </label>
                                       
                        <input type="file" id="UploadPhoto" name="UploadPhoto" class="form-control" accept="image/*">
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="Report To" class="form-label">Report To:</label>
                        <select id="ReportTo" name="ReportTo" class="form-control font-size-select" required>
                           
                        </select>
                     </div>


                     <div class="col-md-3 mt-2">
                        <label for="Address" class="form-label">
                           <i class="fas fa-user-tag"></i> Address:
                        </label>
                        <textarea id="Address" name="Address" class="form-control" placeholder="Enter Address" required></textarea>
                     </div>

                     <div class="col-md-3 mt-2">
                        <label for="Branch" class="form-label">Allocate Branches:</label>
                        <select id="Branch" name="Branch[]" class="form-control js-example-basic-multiple" multiple="multiple" required>
                           <?php
                                 $rstbranch = mysqli_query($connect,"select * from branch_master where comp_id='$comp_id'");
                                 while($rwbranch = mysqli_fetch_assoc($rstbranch))
                                 {
                                    $branch_id = $rwbranch['branch_id'];
                                    $branch_name = $rwbranch['branch_name'];
                                    $selected = in_array($branch_id, $branchlist) ? 'selected' : '';
                                    echo "<option value='$branch_id' $selected>$branch_name</option>";
                                 }
                           ?>
                        </select>
                     </div>

                  </div>
                  <div class=" col-md-6">
                     <input type="submit" class="btn btn-sm btn-success" id="btnSave" value="Save">
                     <a href="user_master.php"><button type="button" class="btn btn-sm btn-warning">Reset</button></a>
                     <a href="index.php"><button type="button" class="btn btn-sm btn-secondary">Close</button></a>
                  </div>
            </form>
         </div>
      </div>
      <div class="card mt-2">
         <div class="card-body">
            <div class="table-responsive" id="divRecordEmployee">

            </div>
           
         </div>
      </div>
   </div>
</div>
<!-- /page content -->

<?php
   include_once( 'footer.php' );
?>
<script>
   $(document).ready(function(){
      ShowEmployee();
      LoadDesignation();
      LoadReportUser();
      $('.js-example-basic-multiple').select2({
         placeholder:"Select Branch"
      });

      $("#frmEmployee").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            var form = event.target;
            var fileInput = form.querySelector('input[name="UploadPhoto"]'); 
            if (fileInput && fileInput.files[0]) {
                formData.append("UploadPhoto", fileInput.files[0]);
            }
            formData.append("Flag", "NewEmployee");
            formData.append("department", $("#designation option:selected").data('attr'));
            $.ajax({
                url: "operation/EmployeeOperation.php", 
                type: "POST", // Type of the request
                data: formData, // The form data to send
                processData: false, // Prevent jQuery from automatically converting the data to a query string
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                   console.log(response);
                   if(response=="Inserted")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Employee Details Added Successfully',
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
                            'Employee Details Updated Successfully',
                            'success'
                        );
                        setTimeout(() => {
                           location.href = 'user_master.php';
                        }, 2000);
                   }else
                   {
                        Swal.fire(
                            'Error!',
                            response,
                            'error'
                        );
                   }
                   ShowEmployee();
                },
            });
      });

   });
   function hideshow2(){
			var password = document.getElementById("confirm_password");
			var slash = document.getElementById("slash");
			var eye = document.getElementById("eye");
			
			if(password.type === 'password'){
				password.type = "text";
				slash.style.display = "block";
				eye.style.display = "none";
			}
			else{
				password.type = "password";
				slash.style.display = "none";
				eye.style.display = "block";
			}
		}
      function ShowEmployee()
      {
         $.post("operation/EmployeeOperation.php",{
               Flag:"ShowEmployee"
         },function(data,success){
               $("#divRecordEmployee").html(data);
               $("#dtlRecord").DataTable({});
         });
      }
      function LoadDesignation(){
         $.post("../component.php",{
            Flag:"LoadDesignation"
         },function(data,success){
            $("#designation").html(data);
         });
      }
      function LoadReportUser(){
         $.post("../component.php",{
            Flag:"LoadReportUser"
         },function(data,success){
            $("#ReportTo").html(data);
         });
      }  
      
</script>
