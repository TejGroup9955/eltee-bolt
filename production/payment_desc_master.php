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
                        <form method="post" id="frmpayment_desc">
                           
                            <div class="form-group">
                                <label for="role">Payment Description</label>
                                <input type="hidden" name="payment_desc_id" id="payment_desc_id" class='form-control'>
                                <input type="text" name="payment_description" id="payment_description" class='form-control' placeholder="Enter Bank Name" required>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                                <a href="payment_desc_master.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
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
        ShowPaymentDesc();
        $("#frmpayment_desc").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewpaymentDesc");
            $.ajax({
                url: "operation/payment_desc_operation.php", 
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
                            'Payment Description Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Payment Description Updated Successfully',
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
                   ShowPaymentDesc();
                },
            });
        });
    });
    function Reset()
    {
        $("#payment_desc_id").val('');
        $("#payment_description").val('');
        $("#btnSave").html("Add");
    }
    function UpdateFunction(TableName, CompareField, CompareId)
    {
        $.post("operation/payment_desc_operation.php",{
            Flag:"UpdateFunction",
            TableName: TableName,
            CompareField:CompareField,
            CompareId: CompareId
        },function(data,success){
            var res = JSON.parse(data);
            console.log(res);
            $("#payment_desc_id").val(res.payment_desc_id);
            $("#payment_description").val(res.payment_description);
            $("#btnSave").html("Update");
        });
    }
    function ShowPaymentDesc()
    {
        $.post("operation/payment_desc_operation.php",{
            Flag:"ShowPaymentDesc"
        },function(data,success){
            $("#DivRecord").html(data);
            $("#dtlRecord").DataTable({});
        });
    }
</script>




