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
               <a href="add_account_master.php" class="btn btn-primary" style="padding: 0.200rem 0.200rem; font-size: 12px;">
                  <i class="fa fa-plus"></i> New Account
               </a>
            </div>
            <div class="table-responsive" id="divRecordAccount">

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
      ShowAccountMaster();
   });
   
      function ShowAccountMaster()
      {
         $.post("operation/AccountMasterOperation.php",{
               Flag:"ShowAccountMaster"
         },function(data,success){
               $("#divRecordAccount").html(data);
               $("#dtlRecord").DataTable({});
         });
      }
      
      
</script>
