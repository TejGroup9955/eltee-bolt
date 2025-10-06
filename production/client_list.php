
<?php include_once('header.php'); ?>
<style>
  .btn-group{
    height: 37px;
  }
  #dtlRecord_filter{
    margin-right: -485px;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl">
      <div class="card">
          <div class="card-body">
              <div class="table-responsive" id="divRecordEmployee"></div>     
          </div>
      </div>
  </div>
</div>     
<?php
  include_once('footer.php');
?>
<script>
  $(document).ready(function(){
      ShowClientList();
   });
function ShowClientList()
{
    $.post("operation/LeadOperation.php",{
          Flag:"ShowClientList"
    },function(data,success){
          $("#divRecordEmployee").html(data);
          $("#dtlRecord").DataTable({
            dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
            buttons: [
                {
                    text: 'Add Client',
                    className: 'btn btn-primary',
                    action: function () {
                        window.location.href = 'client_master.php';
                    }
                }
            ]
          });
    });
}
</script>