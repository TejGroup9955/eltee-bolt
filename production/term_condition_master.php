<?php
include_once('header.php');
?>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
            <!-- Add Category Form (left side) -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form name="termfrm" id="termfrm" method="POST">
                            <div class = 'form-group'>
                                <input type="hidden" name="terms_id" id="terms_id" class='form-control'>
                                <label for = 'Choose Type' class = 'form-label'> Choose Type </label>
                                <select id = 'term_type' name = 'term_type' class = 'form-control font-size-select' required>
                                    <option value = ''>Select  Type</option>
                                    <option value = 'Purchase'>Purchase</option>
                                    <option value = 'Sales'>Sales</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="Term/Condition">Term/Condition Heading:</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter Term or Condition " required>
                            </div>

                            <div class="form-group">
                                <label for="Description">Description</label>
                                <textarea class="form-control" id="discription" name="discription" placeholder="Enter some description " rows="3"></textarea>
                            </div>

                            <div class="col-md-12">
                                <input type="submit" class="btn btn-sm btn-success" id="btnSave" value="Save">
                                <a href="term_condition_master.php"><button type="button" class="btn btn-sm btn-warning">Reset</button></a>
                                <a href="index.php"><button type="button" class="btn btn-sm btn-secondary">Close</button></a>
                          
                            </div>
                        </form>
                    </div>
                </div>
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
<script type="text/javascript">

    $(document).ready(function(){
        ShowTerm();
        $("#termfrm").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewTerm");
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
                            'Terms/Conditions Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Terms/Conditions Updated Successfully',
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
                   ShowTerm();
                },
            });
        });
        
    });
    
    function Reset()
    {
        $("#term_type").val('');
        $("#title").val('');
        $("#discription").val('');
        $("#btnSave").html("Add");
    }

    function ShowTerm()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowTerm"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
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
            $("#term_type").val(res.term_type);
            $("#terms_id").val(res.terms_id);
            $("#title").val(res.title);
            $("#discription").val(res.discription);
            $("#btnSave").html("Update");
        });
    }

    function DeleteTermFunction(TableName, Status, CompareField, CompareId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change the status?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, send the AJAX request
                $.post("operation/CrudOperation.php", {
                    Flag: "UpdateTermStatus",
                    TableName: TableName,
                    Status: Status,
                    CompareField:CompareField,
                    CompareId: CompareId
                },function(data,success){
                    if(data=="Updated")
                    {
                        Swal.fire(
                            'Changed!',
                            'The status has been changed.',
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
                            'There was an issue changing the status.',
                            'error'
                        );
                    }
                });
            }
        });
    }

</script>

