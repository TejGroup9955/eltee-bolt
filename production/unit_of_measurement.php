<?php
include_once('header.php');
?>

<style>
    
    .form-control::placeholder {
        font-size: 0.9rem; 
   }
   
</style>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
            <!-- Add Category Form (left side) -->
            <div class="col-md-4">
                <form id="frmUom">
                    <div class="form-group">
                        <label for="categoryName">UOM</label>
                        <input type="hidden" class="form-control" id="u_id" name="u_id">
                        <input type="text" class="form-control" id="u_name" name="u_name" placeholder="Enter UOM name" required>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                        <a href="unit_of_measurement.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
                        <a href="index.php" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <!-- Table displaying categories (right side) -->
            <div class="col-md-8">
                <div class="table-responsive" id="DivRecord"></div>
            </div>
        </div>

    </div>
</div>

<?php
include_once('footer.php');
?>
<script>
    $(document).ready(function(){
        ShowDesignation();
        $("#frmUom").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewUOM");
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
                            'UOM Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'UOM Updated Successfully',
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
    });
    function Reset()
    {
        $("#u_id").val('');
        $("#u_name").val('');
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
            $("#u_id").val(res.u_id);
            $("#u_name").val(res.u_name);
            $("#btnSave").html("Update");
        });
    }
    function ShowDesignation()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowUOM"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
    }
</script>

