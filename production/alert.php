<?php 
if(empty($_GET)) {
  } else {
   $alert_type=@$_GET['alert'];
   if($alert_type == 'deleted')
   {
       $alert = '<div class="alert alert-danger  center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Client deleted successfully...
            </div>';
   }
   if($alert_type == 'Department_Deleted')
   {
       $alert = '<div class="alert alert-danger  center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Department Deleted successfully...
            </div>';
   }
   if($alert_type == 'delete')
   {
       $alert = '<div class="alert alert-danger  center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Record deleted successfully...
            </div>';
   }
   if($alert_type == 'UpdateStatus')
   {
       $alert = '<div class="alert alert-warning  center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Status Updated...
            </div>';
   }
   if($alert_type == 'success')
   {
       $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Record added successfully...
            </div>';
   }
   if($alert_type == 'import_msg')
   {
      $imp_count = $_GET['count'];
      $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Client List is Impoted...<br />
                    <b>'.$imp_count.'</b> clients imported.
                </div>';
   }
   if($alert_type == 'quotation_sent')
   {
      $alert = '<div class="alert alert-success alert-dismissable center">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  <b>Quotation request sent successfully...</b>.
              </div>';
   }
   if($alert_type == 'holiday_deleted')
   {
       $alert = '<div class="alert alert-danger alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Holiday deleted successfully...
            </div>';
   }
   if($alert_type == 'designation_deleted')
   {
       $alert = '<div class="alert alert-danger alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Designation deleted successfully...
            </div>';
   }
   if($alert_type == 'add_advance')
   {
       $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Advance Added successfully...
            </div>';
   }
   if($alert_type == 'error')
   {
       $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                   Error occurred
            </div>';
   }
   if($alert_type == 'transfer')
   {  
       $lead_count=$_GET['lead_count'];
       $old_user_name=$_GET['old_user_name'];
       $new_user_name=$_GET['new_user_name'];
       
       $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  <b>'.$lead_count.'</b> Leads transfered from '.$old_user_name.' to '.$new_user_name.' .
            </div>';
   }
   if($alert_type == 'import_msg_user')
   {
      $imp_count = $_GET['count'];
      $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    User List is Impoted...<br />
                    <b>'.$imp_count.'</b> Users imported.
                </div>';
   }
   if($alert_type == 'import_msg_attendance')
   {
      $imp_count = $_GET['count'];
      $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    User Attendance is Impoted...<br /> for
                    <b>'.$imp_count.'</b> users.
                </div>';
   }
    // print_r($_GET);
}
?>

<script type="text/javascript">

  $(document).ready(function () {
   
  window.setTimeout(function() {
      $(".alert-danger").fadeTo(1000, 0).slideUp(1000, function(){
          $(this).remove(); 
      });
    }, 2000);
   
  });
  $(document).ready(function () {
   
  window.setTimeout(function() {
      $(".alert-success").fadeTo(1000, 0).slideUp(100, function(){
          $(this).remove(); 
      });
    }, 2000);
   
  });
</script>    
     