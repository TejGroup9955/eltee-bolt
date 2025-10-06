<?php 
$page_heading="Lead Summary";
include '../configuration.php';
include 'header.php';
$compid = "";
$comp_name = "Select Company";
$branchid = "";
$branch_name = "Select Branch";
$roleid = "";
$role_name = "PLEASE SELECT";
$gender1 = "PLEASE SELECT";
if(isset($comp_id)){
    $cmd = "SELECT comp_name, comp_id FROM company_master WHERE comp_id = $comp_id";
 
    $result = $connect->query($cmd);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
           $comp_name = $row['comp_name'];
           $compid = $row['comp_id'];
        }
    }
}
if(isset($br_id)){
    $cmd = "SELECT branch_id, branch_name FROM branch_master WHERE branch_id = $br_id";
    $res=mysqli_query($connect,$cmd);
    $result = $connect->query($cmd);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
           $branch_name = $row['branch_name'];
           $branchid = $row['branch_id'];
        }
    }
}

$rem_count= 0;
	$today_count= 0;
	$next_count= 0;
	
	$dt=date("Y-m-d");

	if(isset($_GET['filter_status'])) {
		$filter_status = $_GET['filter_status'];
	} else {
		$filter_status = 'all';
	}
	
	if($filter_status == "hot"){ $hot_selected = "selected"; }
	if($filter_status == "cold"){ $cold_selected = "selected"; }
	if($filter_status == "warm"){ $warm_selected = "selected"; }
$usernm="User";
?>

<style>
.x_title h2 {
    font-size: 12pt;
    color: #000;
    background: #fff;
    width: 20%;
    border-bottom: none;
    float: left;
    font-weight: bold;
    margin: 2px 0;
    padding: 4px 0;
}
#employee-grid_filter{
    margin-right: -490px;
  }
</style>

<div class="right_col" role="main">
    <div class="card" >
        <div class="card-body">
            <div class="col-md-2">
                <select name="sales_id" data-column="9" id="sales_id" class="search-input-select form-control">        
                    <?php
                        if($user_type_id=="1")
                        {
                             echo '<option value="">Select User</option> 
                            <option value="">All</option>';
                            $rstuser = mysqli_query($connect,"select user_name,user_id from user_master where status='Active' and user_type_id='4' ");
                            while($rwuser = mysqli_fetch_assoc($rstuser))
                            {
                                $user_name = $rwuser['user_name'];
                                $user_id = $rwuser['user_id'];
                                echo "<option value='$user_id'>$user_name</option>";
                            }
                        }
                        else{
                            echo '<option value="">Select User</option> ';
                            echo "<option value='$user_id'>$user_name</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <select data-column="4" class="search-input-select form-control input-md" name="followup_type" id="data_condition">
                    <option value=""> Followup Type</option>
                    <option value="">All</option> 
                    <option value="1">Today's Followup</option>
                    <option value="3">Remaining Followup</option>
                    <option value="2">Next Followup</option>
                </select>
            </div>
            <div class="col-md-1" style="padding: 0 2px;">
                <select data-column="1" class="search-input-select form-control input-md" id="client_status" name="client_status">
                    <option value="">Status</option>
                    <option value="">All</option> 
                    <!-- <option value="Quotation">Quotation</option> -->
                    <option value="Hot">Hot</option>
                    <option value="Warm">Warm</option>
                    <option value="Cold">Cold</option>
                </select>
            </div>
            <div class="col-md-2" style="width: 14%;">
                <input type="text" data-column="2" id="datepicker-example1" name="fromdate" placeholder="FROM DATE" class="search-input-text form-control "/></input>
            </div>
            <div class="col-md-2" style="width: 14%;">
                <input type="text" data-column="3" id="datepicker-example14" name="fromdate" placeholder="TO DATE" class="search-input-text form-control "/></input>
            </div>
            <div class="col-md-1" style="width: 5%;">
                <button class="btn btn-primary btn-round"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table id="employee-grid" cellpadding="0" cellspacing="0" border="0" class="display table-bordered  table table-striped " width="100%">
                        <thead>
                            <tr>
                                <th width="80" class="noExport" > Remark </th>
                                <th style="width: 200px;"> Supplier Name </th>
                                <th width="50"> Business </th>
                                <th style="width: 250px;">Products</th>
                                <th width="200"> Address </th>
                                <th> Mobile No.</th>
                                <!-- <th> Contact Person</th> -->
                                <th width="10"> Email</th>
                                <!-- <th width="75"> Appointment Date </th> -->
                                <th width="70"> Followup On </th>
                                <th width="100"> Last Remark </th>
                                <!-- <th width="100">  Business Type <br> Current Status </th> -->
                                <!-- <th width="50"> Meeting Date </th> -->
                                <th width="5"> Lead Type </th>
                                <th width="5">Action</th>
                            </tr>
                        </thead>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>
        
<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                  <h6 class="modal-title" id="deleteModalLabel">
                      <i class="fa fa-pencil"></i> Are you sure?
                  </h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="client_idDelete" id="client_idDelete">
                  <label>Enter Supplier delete reason:</label>
                  <textarea required class="form-control" name="remarkDelete" id="remarkDelete" placeholder="Enter Reason..." rows="3"></textarea>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary btn-sm" name="submit" onclick="deleteclientfunction();">
                      <i class="fa fa-plus"></i> Submit
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                      <i class="fa fa-close"></i> Cancel
                  </button>
              </div>
          </div>
      </div>
  </div>

<?php include('footer.php'); ?>
<script type="text/javascript" language="javascript" >
    $(document).ready(function() {

        $(".dp_clear").click(function(){
            $("#datepicker-example1").val('');
            $("#datepicker-example14").val('');
        });
        var lead_summary = "lead_summary";
        //alert(lead_summary);
        var dataTable = $('#employee-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "bDestroy": true,       

            "columnDefs": [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
                
            } ],  
            dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
             buttons: [
            {
                text: '<i class="fa fa-plus"></i> New Supplier',
                className: 'btn btn-primary btn-round btn-sm',
                action: function () {
                    window.location.href = 'Supplier-fresh-Visit.php';
                }
            }
            ],
            //"alengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
            "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
            "iDisplayLength": 10,
            "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
            "iDisplayLength": 10,           

            "ajax":{
                url :"operation/leadsummary_datagrid.php", // json datasource
                type: "post",  // method  , by default get
                data: {lead_summary:lead_summary,LeadType:"Supplier"},
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                    
                }
            }
        } );

        $("#employee-grid_filter").css("display","block");  // hiding global search box
        $('.search-input-text').on( 'blur', function () {   // for text boxes
            var i =$(this).attr('data-column');  // getting column index
            var v =$(this).val();  // getting search input value
            dataTable.columns(i).search(v).draw();
            //  alert(v);
        } );
        
        $('.search-input-select').on( 'change', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();  
            dataTable.columns(i).search(v).draw();

        } );
        $('.search-input-select').on( 'change', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();  
            dataTable.columns(i).search(v).draw();

        } );
        
        $('.search-input-select').on( 'change', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();                   
            dataTable.columns(i).search(v).draw();

            // alert(v);

        } );

        
    } );
</script>
<script type="text/javascript">
    function change_selection(id)
    {
        var select_id = id;
        $("select#data_condition").val(select_id);  
        $( "#data_condition" ).trigger( "change" );
    }
    var modal = document.getElementById('myModal');
    var temp={};
    function change(id) {
            modal.style.display = 'block';
            temp = id;
        // if(confirm('Are you sure to request the Quotation ?')) {  
            
        //    window.location.href='Request-Quotation.php?client_id='+id;

        // }
    }
    function send() {
            //alert(temp); // Alerts 'green'
            window.location.href='Request-Quotation.php?client_id='+temp;
    }
    function deleteclientfunction() {
      var client_id = $("#client_idDelete").val();
      var remark = $("#remarkDelete").val();
      if(remark=="")
      {
        Swal.fire(
              'Oops!',
              'Please Enter Remark.',
              'error'
          );
      }
      else
      {
        Swal.fire({
        title: 'Are you sure?',
            text: "Do you want to Delete this Client?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../component.php", {
                    Flag: "DeleteClient",
                    client_id: client_id,
                    remark: remark,
                },function(data,success){
                    if(data=="Updated")
                    {
                        Swal.fire(
                            'Well Done!',
                            'Supplier Deleted Successfully.',
                            'success'
                        );
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                    else
                    {
                        Swal.fire(
                            'Error!',
                            'Unable to Delete',
                            'error'
                        );
                    }
                });
            }
        });
      }
    }
    function delete_client(id)       
    {
        $("#delete_modal").modal("toggle");  
        $("#client_idDelete").val(id);
    }
</script>
<script>
    var modal = document.getElementById('myModal');
    var span = document.getElementsByClassName('close')[0];
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    span.onclick = function() {
        modal.style.display = 'none';
    }
</script>
<div id='myModal' class='modal'>
    <div class='modal-content'>
    <span class='close'>×</span>
    <h4>Are you really want to send request</h4>
    <a class='btn btn-primary' href='javascript:send(<?php $client_id;?>);'>Send </a> 
    <a class="btn btn-danger" href="Lead-Summary.php">Cancel</a>
    </div>
</div>

              