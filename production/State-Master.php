<?php
$page_heading="State Master";
include '../configuration.php';
include 'header.php';

$display_add = 'inline;'; $display_update = '';
$state_id = @base64_decode($_GET['state_id']);
$state_id_delete = @$_GET['state_id_delete'];

if(isset($state_id))
{
  $cmd = "SELECT stateName, id, countryID FROM state_master WHERE id = '$state_id' ";
  $result = $connect->query($cmd);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $state_name1 = $row['stateName'];
      $state_id1 = $row['id'];
      $country_id1 = $row['countryID'];
    }
    $display_add = ''; $display_update = 'inline;';
    $disabled = "disabled";
    echo '<script>$(document).ready(function(){$(".btn-danger").prop("disabled", true  );});</script>';
  }
}

if($state_id_delete != '' )
{
  $status = mysqli_real_escape_string($connect, $_GET['status']);
  if($status=="1")
  {
    $UpdateStatus = 0;
  }else{ $UpdateStatus=1; }
				
  $cmd2 = "update state_master set status='$UpdateStatus' WHERE id = '$state_id_delete'";
  $connect->query($cmd2);
  $alert = '<div class="alert alert-danger alert-dismissable center">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   Status Updated Sucessfully...
  </div>';
}
extract($_POST);

if(isset($submit))
{
  $q2 = "SELECT stateName FROM state_master WHERE stateName='$state_name' AND id = '$state_id' ";
  $r2=mysqli_query($connect,$q2);
  $res2 = $connect->query($q2);
  if ($res2->num_rows > 0) {
    if($row2 = $res2->fetch_assoc()) {
      $alert = '<div class="alert alert-danger alert-dismissable center">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
      State is already exists...
      </div>';
    }
  }
  else{ 
    $cmd2 = "INSERT INTO  state_master (stateName, countryID) VALUES('$state_name', '$country_id') ";
    $connect->query($cmd2);
    $alert = '<div class="alert alert-success alert-dismissable center">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    State Added Sucessfully...
    </div>';
    $state_name = "";
  }
}

if(isset($update))
{
  $cmd2 = "UPDATE  state_master SET stateName = '$state_name',countryID='$country_id' WHERE id = '$state_id' ";
  $connect->query($cmd2);
  $alert = '<div class="alert alert-warning alert-dismissable center">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
  State Sucessfully...
  </div>';
  $cmd = "SELECT stateName, countryID FROM state_master WHERE id = '$state_id' ";
  $result = $connect->query($cmd);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $state_name1 = $row['stateName'];
      $country_id1 = $row['countryID'];
    }
    $disabled = "disabled";
    $display_add = ''; $display_update = 'inline;';
  }
  else{
    $display_add = 'inline;'; $display_update = '';
  }
  $state_name = "";
}

?>
<input type="hidden" id="disabled" value="<?php echo @$disabled; ?>">
<div class="right_col" role="main">
  <form method="POST">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php echo @$alert;?>
      <div class="row">
        <div class="col-md-4">
          <div class="tile-stats">
           
            <div class="col-md-12 mt-3">
              <div class="">
                <div class="" data-toggle="tooltip" data-placement="right" title="Please Select Country">
                  <label>Select Country</label>
                  <select class="form-control required" required="" id='country_id' name="country_id">
                    <?php
                    if(isset($country_id1))
                    {
                      $cmd2 = "SELECT c.countryName,c.id FROM country_master c WHERE c.id = '$country_id1' ";
                      $result2 = $connect->query($cmd2);
                      if ($result2->num_rows > 0) {
                        while($row2 = $result2->fetch_assoc()) {
                          echo '<option value="'.$row2['id'].'">'.$row2['countryName'].'</optin>';
                        }
                      }
                      $cmd2 = "SELECT c.countryName,c.id FROM country_master c  WHERE c.id != '$country_id1' ";
                      $result2 = $connect->query($cmd2);
                      if ($result2->num_rows > 0) {
                        while($row2 = $result2->fetch_assoc()) {
                          echo '<option value="'.$row2['id'].'">'.$row2['countryName'].'</optin>';
                        }
                      }
                    }
                    else 
                    {
                      echo '<option value="">Selet Country</option>';
                      $cmd2 = "SELECT c.countryName,c.id FROM country_master c  ORDER BY c.countryName ";
                      $result2 = $connect->query($cmd2);
                      if ($result2->num_rows > 0) {
                        while($row2 = $result2->fetch_assoc()) {
                          echo '<option value="'.$row2['id'].'">'.$row2['countryName'].'</optin>';
                        }
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="mt-3">
                <div class="" data-toggle="tooltip" data-placement="right" title="Please Enter State Name">
                  <label>State Name</label>
                  <input type="text" name="state_name" placeholder="Enter State Name" value="<?php echo @$state_name1;?>" class="form-control required" required />
                </div>
              </div>
            </div>
            <div style="text-align: center;" class="col-md-12 mt-2">
              <?php
              if($display_add != "")
              {
                echo '<button type="submit" name="submit" class="btn btn-primary" style="display: <?php echo $display_add; ?>" ><i class="fa fa-plus"></i> Add State</button>';
              }
              if($display_update != "")
              {
                echo '<button type="submit" name="update" class="btn btn-warning" style="display: <?php echo $display_update; ?>" ><i class="fa fa-pencil"></i> Update State </button>';
              }
              ?>
              <a href="State-Master.php" class="btn btn-success"><i class="fa fa-files-o"></i> Reset</a>
              <br> <br>
            </div>
          </div>
        </div>
        <div class="col-md-8 ">  
          <div class="tile-stats">
              <div class="col-md-12 my-3">
                <table id="employee-grid" cellpadding="0" cellspacing="0" border="0" class=" my-3 display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action " width="100%">
                  <thead>
                    <tr>
                      <th>Action</th>
                      <th>State</th>
                      <th>Country</th>
                    </tr>
                  </thead>
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php
include 'footer.php';
?>
<script type="text/javascript" language="javascript" >
  $(document).ready(function() {
    var lead_summary = "lead_summary";
    var disabled = $('#disabled').val() || 0;
    var dataTable = $('#employee-grid').DataTable( {
      "processing": true,
      "serverSide": true,
      "bDestroy": true,       
      "columnDefs": [ {
        "targets": 0,
        "orderable": false,
        "searchable": false

      } ],  

      "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
      "iDisplayLength": 10,

       

      "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
      "iDisplayLength": 10,           

      "ajax":{
        url :"State-Master-datagrid.php", 
        type: "post",
        data: {lead_summary:lead_summary, disabled:disabled},
        error: function(){ 
          $(".employee-grid-error").html("");
          $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
          $("#employee-grid_processing").css("display","none");

        }
      }
    } );

    $("#employee-grid_filter").css("display","block"); 
    $('.search-input-text').on( 'blur', function () { 
      var i =$(this).attr('data-column'); 
      var v =$(this).val(); 
      dataTable.columns(i).search(v).draw();
    } );
    $('.search-input-select').on( 'change', function () {
      var i =$(this).attr('data-column');
      var v =$(this).val();  
      dataTable.columns(i).search(v).draw();
    } );
    $('.search-input-select').on( 'change', function () {
      var i =$(this).attr('data-column');
      var v =$(this).val();                   
      dataTable.columns(i).search(v).draw();
    } );
  } );
</script>
<script type="text/javascript">
  function delete_c(id, statuss) {
    var r = confirm("Do you really want to change status of this State?");
		
		if (r == true) {
			window.location.href='State-Master.php?state_id_delete='+id+'&status='+status;
		}
  }
</script>
<script type="text/javascript">
	$(document).ready(function () {
		window.setTimeout(function() {
			$(".alert-danger").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).remove();
			});
		}, 1500);

    window.setTimeout(function() {
			$(".alert-success").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).remove();
			});
		}, 1500);
	});

  $(document).ready(function () {
      window.setTimeout(function() {
        $(".alert-warning").fadeTo(1000, 0).slideUp(1000, function() {
          $(this).remove();
        });
        // window.location.href='index.php';
      }, 1500);
	});


  
</script>