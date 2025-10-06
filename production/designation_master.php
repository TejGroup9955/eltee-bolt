<?php
include_once('header.php');
include_once('../configuration.php');
?>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="post" id="frmDesignation">
                            <div class='form-group'>
                                <label for='Choose Department' class='form-label'>Choose Department</label>
                                <select name='dp_id' id="dp_id" class='form-control font-size-select' required>
                                    <option value="select">Choose Department</option>
                                    <?php
                                    $query = $connect->query("SELECT dp_id, d_name FROM department_master");
                                    $rowCount = $query->num_rows;
                                    if ($rowCount > 0) {
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['dp_id'] . '">' . $row['d_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="role">Role/ Designation</label>
                                <input type="hidden" name="role_type_id" id="role_type_id" class='form-control' placeholder="Enter Role/Designation" >
                                <input type="text" name="role_type_name" id="role_type_name" class='form-control' placeholder="Enter Role/Designation" required>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="submit" class="btn btn-success" id="btnSave">Save</button>
                                <a href="designation_master.php" class="btn btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="table-responsive" id="DivRecord"></div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
<script>
    $(document).ready(function(){
        ShowDesignation();
        $("#frmDesignation").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewDesignation");
            $.ajax({
                url: "operation/CrudOperation.php", 
                type: "POST", // Type of the request
                data: formData, // The form data to send
                processData: false, // Prevent jQuery from automatically converting the data to a query string
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                    Reset();
                   if(response=="Inserted")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Designation Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Designation Updated Successfully',
                            'success'
                        );
                   }else
                   {
                        Swal.fire(
                            'Error!',
                            response,
                            'error'
                        );
                   }
                   ShowDesignation();
                },
            });
        });
        $("#frmAssignModule").submit(function(e){
            e.preventDefault();
            var moduledata = $(this).serialize();
            var designation_id = $("#txtdesignation").val();
            $.ajax({
                type: 'POST',
                url: '../component.php',
                data: moduledata + "&designation_id=" + designation_id + "&Flag=SaveAssignedModules",
                success: function(html) {
                    if(html=="Success")
                    {
                        $("#mdlAssignModule").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: "Modules Assigned Successfully",
                        });
                        ShowDesignation();
                    }
                    else
                    {
                        $("#mdlAssignModule").modal("hide");
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: html,
                        });
                        ShowDesignation();
                    }
                }
            });
        });
    });
    function Reset()
    {
        $("#role_type_id").val('');
        $("#role_type_name").val('');
        $("#dp_id").val('');
        $("#btnSave").html("Add");
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
            $("#role_type_id").val(res.role_type_id);
            $("#role_type_name").val(res.role_type_name);
            $("#dp_id").val(res.dp_id);
            $("#btnSave").html("Update");
        });
    }
    function ShowDesignation()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowDesignation"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
    }
    function AssignModule(role_type_id,role_type_name)
    {
        $("#mdlAssignModule").modal("toggle");
        $("#txtdesignation").val(role_type_id);
        $.ajax({
            type:'POST',
            url:'selectModules.php',
            data:{designation_id:role_type_id},
            success:function(html){
                $('#assigned_module').html(html);
                $("#ASModalHeading").html("Assign Modules to "+role_type_name);
            }
        });
    }
</script>

<div class="modal fade" id="mdlAssignModule" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ASModalHeading"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <form id="frmAssignModule">          
                <div class="modal-body">
                    <input type="hidden" id="txtdesignation" name="txtdesignation">
                    <div id="assigned_module"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>



