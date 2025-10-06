<?php
include_once('header.php');
?>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
            <!-- Add Category Form (left side) -->
            <div class="col-md-4">
                <form id="frmCat">
                    <div class="form-group">
                        <label for="categoryName">Category Name</label>
                        <input type="hidden" class="form-control" id="c_id" name="c_id" >
                        <input type="text" class="form-control" id="c_name" name="c_name" placeholder="Enter category name" required>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                        <a href="product_category.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
                        <a href="index.php" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                      
                </form>
            </div>

            <!-- Table displaying categories (right side) -->
            <div class="col-md-8" >
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
        $("#frmCat").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewCategory");
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
                            'Category Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Category Updated Successfully',
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
        $("#c_id").val('');
        $("#c_name").val('');
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
            $("#c_id").val(res.c_id);
            $("#c_name").val(res.c_name);
            $("#btnSave").html("Update");
        });
    }
    function ShowDesignation()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowCategory"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
    }
</script>
