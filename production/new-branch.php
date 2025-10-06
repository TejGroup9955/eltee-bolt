<?php 
$page_heading="New Branch";
// include '../Check-User.php';
include '../configuration.php';
include 'header.php';
foreach ($_POST as $key => $value) {
    $_POST[$key] = mysqli_real_escape_string($connect, $value);
}
extract($_POST);
$cid="";
$cname="PLEASE SELECT";

$cmd2 = "SELECT comp_name, comp_id FROM company_master WHERE comp_id = '$dept_id'";
$res2=mysqli_query($connect,$cmd2);
$result2 = $connect->query($cmd2);
if ($result2->num_rows > 0) {
    while($row2 = $result2->fetch_assoc()) {
       $cname = $row2['comp_name'];
       $cid = $row2['comp_id'];
    }
}
if(isset($_GET['act'])) {
   
    if($_GET['act'] == "edit") {
        if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id'])) {
            $id = mysqli_real_escape_string($connect, $_GET['id']);
            $qry = "SELECT * FROM branch_master WHERE branch_id='$id'";
            $rs = mysqli_query($connect, $qry);
            
            if(mysqli_num_rows($rs) > 0) {
                $arr = mysqli_fetch_array($rs);
            }
            
            if(isset($_POST['submit_value'])) {
                extract($_POST);
                $cmd = "SELECT * FROM branch_master WHERE LOWER(branch_name)='".strtolower($br_name)."' AND branch_id != '$id' ";
                $rs_exists=mysqli_query($connect,$cmd);   
                if(mysqli_num_rows($rs_exists) > 0) {
                    $alert ='<div class="alert alert-danger alert-dismissable center">
                              <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                              Sorry! Duplicate Branch Name...
                            </div>';
                } else {
                   $cmd3 = "update branch_master SET branch_name= '$br_name',comp_id='$comp_id', 
                    branch_add ='$branch_add', branch_head ='$branch_head', branch_cont_no1 = '$branch_cont_no1', 
                    branch_cont_no2 = '$branch_cont_no2', branch_email = '$branch_email' where branch_id = '$id' ";
                    $connect->query($cmd3);
                    echo '<script>window.location.href = "new-branch.php?m=2";</script>';
                    exit;
                }
            }
        }
    }
} 
else
{
    if(isset($_POST['submit_value']))
    { 
        $cmd = "SELECT branch_name, comp_id FROM branch_master WHERE branch_name='$br_name' AND comp_id = '$comp_id' ";
        $res=mysqli_query($connect,$cmd);                                          
        $result = $connect->query($cmd);
        if ($result->num_rows > 0) {
        if($row = $result->fetch_assoc()) {}
        $alert = '<div class="alert alert-danger alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    Branch name is alerady exists.
            </div>';
        }
        else{
            $cmd1 = "SELECT branch_cont_no1, branch_email, comp_id FROM branch_master WHERE (branch_cont_no1='$branch_cont_no1' OR branch_email = '$branch_email') AND comp_id = '$comp_id' ";
            $res1=mysqli_query($connect,$cmd1);                                          
            $result1 = $connect->query($cmd1);
            if ($result->num_rows > 0) {
            if($row1 = $result1->fetch_assoc()) {}
            $alert = '<div class="alert alert-danger alert-dismissable center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        Branch mobile Number or Email-ID is already exists...
                </div>';
            }
            else{

                $cmd3 = "INSERT INTO branch_master (branch_name, comp_id, branch_add, branch_head, branch_cont_no1, branch_cont_no2, branch_email) values('$br_name', '$comp_id','$branch_add' , '$branch_head', '$branch_cont_no1', '$branch_cont_no2', '$branch_email')";
                $connect->query($cmd3);
                $alert = '<div class="alert alert-success alert-dismissable center">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            Branch Added sucessfully...
                        </div>';
            }
        }
    }
}
if(isset($_GET['m'])) {
    if($_GET['m'] == "2") {
        $alert ='<div class="alert alert-success alert-dismissable center">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          Branch Updated Sucessfully...
        </div>';
    }	
}
?>
<style>
    label{
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
<div class="right_col " role="main">
    <form method="post" id="frnbranch">
        <div class="row">
            <?php echo @$alert;?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <!-- <div class="panel-heading text-primary"><b><i class="fa fa-pencil"></i> New Branch</b></div> -->
                        <div class="col-md-12">
                            <label>Branch Name</label>
                            <input type="hidden" name="hiddenbranch_id" id="hiddenbranch_id" placeholder="Enter Branch Name"  /> 
                            <input type="text" name="br_name" id="br_name" placeholder="Enter Branch Name"  class="form-control required" required="" /> 
                            
                            <label>Address</label>
                            <textarea class="form-control required" placeholder="Enter Address" name="branch_add" id="branch_add" required="" rows="3"></textarea>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Contact Number 1</label>
                                    <input type="text" placeholder="Enter No 1" name="branch_cont_no1" id="branch_cont_no1" maxlength="10" minlength="10" onkeypress="return isNumber(event)" class="form-control required" required />
                                </div>
                                <div class="col-md-6">
                                    <label>Contact Number 2</label>
                                    <input type="text" placeholder="Enter No 2" name="branch_cont_no2" id="branch_cont_no2" maxlength="10" minlength="10" onkeypress="return isNumber(event)" class="form-control "  />
                                </div>
                            </div>
                            <label>Email-ID</label>
                            <input type="email" placeholder="Enter Email ID" name="branch_email" id="branch_email"  class="form-control required" required />    
                            <label>Branch Head</label>
                            <input type="text" name="branch_head" id="branch_head" placeholder="Enter Branch Head" class="form-control required" required />    
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" name="submit" class="btn btn-success btn-sm" id="btnSave">Save</button>
                            <a href="new-branch.php" class="btn btn-warning btn-sm"><i class="fa-solid fa-rotate"></i>Reset</a>
                            <a href="index.php" class="btn btn-secondary btn-sm">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- <a href="production/new-branch.php" class="btn btn-success btn-round" title="New Branch" data-toggle="tooltip" style="left: 80%;"><i class="fa fa-plus"></i> New Branch</a>  -->
                        <div class="table-responsive">
                        <table id="tblbranch" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th width="75"> Actions </th>
                                        <th width="5"> Company Name </th>
                                        <th width="5"> Branch Name </th>
                                        <th width="100"> Address </th>
                                        <th width="5"> Branch Head </th>
                                        <th width="50"> Contact No. 1 </th>
                                        <th width="100"> Contact No. 2 </th>
                                        <th width="100"> Email </th>
                                    </tr>
                                </thead>
                                <?php
                                    $cmd1 = "SELECT c.comp_name, b.branch_name, b.branch_add, b.branch_head, b.branch_cont_no1, b.branch_cont_no2, b.branch_email, b.branch_id,b.status FROM company_master c INNER JOIN branch_master b ON c.comp_id = b.comp_id WHERE b.comp_id=$comp_id";
                                    $res1=mysqli_query($connect,$cmd1);
                                    $result1 = $connect->query($cmd1);
                                    if ($result1->num_rows > 0)
                                    {
                                        while($row1 = $result1->fetch_assoc()) 
                                        {
                                            $comp_name = $row1['comp_name'];
                                            $branch_name = $row1['branch_name'];
                                            $branch_add = $row1['branch_add'];
                                            $branch_head = $row1['branch_head'];
                                            $branch_cont_no1 = $row1['branch_cont_no1'];
                                            $branch_cont_no2 = $row1['branch_cont_no2'];
                                            $branch_email = $row1['branch_email'];
                                            $branch_id = $row1['branch_id'];
                                            $status = $row1['status'];
                                ?>
                                    <tr>
                                        <td width="10">
                                        <?php 
                                        	  echo "<button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('branch_master ','branch_id',".$branch_id.")\">
                                                    <i class='fa fa-pencil' style='color:white;'></i>
                                                </button>";
                                            if($status=="Active")
                                            {
                                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('branch_master ','".$status."','branch_id',".$branch_id.")\">
                                                    Deactivate
                                                </button>";
                                            }
                                            else{
                                                echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('branch_master','".$status."','branch_id',".$branch_id.")\">
                                                    Activate
                                                </button>";
                                            }
                                        ?>
                                        </td>
                                        <td><?php echo $comp_name;?></td>
                                        <td><?php echo $branch_name;?></td>
                                        <td><?php echo $branch_add;?></td>
                                        <td><?php echo $branch_head;?></td>
                                        <td><?php echo $branch_cont_no1;?></td>
                                        <td><?php echo $branch_cont_no2;?></td>
                                        <td><?php echo $branch_email;?></td>
                                    </tr>
                                <?php } }?>
                            </table> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- </div> -->
<?php include('footer.php'); 
include '../ajaxfunction.php';
?>    
<script type="text/javascript">
    $(document).ready(function(){
        $("#tblbranch").DataTable({});
        $("#frnbranch").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewBranch");
            $.ajax({
                url: "operation/CrudOperation.php", 
                type: "POST", // Type of the request
                data: formData, // The form data to send
                processData: false, // Prevent jQuery from automatically converting the data to a query string
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                   if(response=="Inserted")
                   {
                        Swal.fire(
                            'Well Done!',
                            'New Branch Added Successfully',
                            'success'
                        );
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Branch Details Updated Successfully',
                            'success'
                        );
                        setTimeout(()=>{
                            location.reload();
                        },1000);
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
    });
    function UpdateFunction(TableName, CompareField, CompareId)
    {
        $.post("operation/CrudOperation.php",{
            Flag:"UpdateFunction",
            TableName: TableName,
            CompareField:CompareField,
            CompareId: CompareId
        },function(data,success){
            var res = JSON.parse(data);
            $("#hiddenbranch_id").val(res.branch_id);
            $("#br_name").val(res.branch_name);
            $("#branch_add").val(res.branch_add);
            $("#branch_email").val(res.branch_email);
            $("#branch_cont_no1").val(res.branch_cont_no1);
            $("#branch_cont_no2").val(res.branch_cont_no2);
            $("#branch_head").val(res.branch_head);
            $("#btnSave").html("Update");
        });
    }
</script>

