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
                        <form method="post" id="frmbank">
                           
                            <div class="form-group">
                                <label for="role">Bank Name</label>
                                <input type="hidden" name="bank_id" id="bank_id" class='form-control'>
                                <input type="text" name="bank_name" id="bank_name" class='form-control' placeholder="Enter Bank Name" required>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                                <a href="bank_master_basic.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
                                <a href="index.php" class="btn btn-sm btn-secondary">Cancel</a>
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
        ShowBank();
        $("#frmbank").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewBank");
            $.ajax({
                url: "operation/bank_operation.php", 
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
                            'Bank Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Bank Updated Successfully',
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
                   ShowBank();
                },
            });
        });
    });
    function Reset()
    {
        $("#bank_id").val('');
        $("#bank_name").val('');
        $("#btnSave").html("Add");
    }
    function UpdateFunction(TableName, CompareField, CompareId)
    {
        $.post("operation/bank_operation.php",{
            Flag:"UpdateFunction",
            TableName: TableName,
            CompareField:CompareField,
            CompareId: CompareId
        },function(data,success){
            var res = JSON.parse(data);
            $("#bank_id").val(res.bank_id);
            $("#bank_name").val(res.bank_name);
            $("#btnSave").html("Update");
        });
    }
    function ShowBank()
    {
        $.post("operation/bank_operation.php",{
            Flag:"ShowBank"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
    }
</script>




