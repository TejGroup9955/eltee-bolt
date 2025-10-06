<?php
include_once('header.php');
include_once('../configuration.php');
?>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
             <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="post" id="frmShip">
                            <div class="form-group">
                                <label for="Shipment Document">Shipment Document</label>
                                <input type="hidden" name="shipment_document_id" id="shipment_document_id" class='form-control'>
                                <input type="text" class="form-control" id="shipment_document_name" name="shipment_document_name" placeholder="Enter Shipment Document" required>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                                <a href="shipment_document.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
                                <a href="index.php" class="btn btn-sm btn-secondary">Cancel</a>
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
        ShowShipmentDocument();
        $("#frmShip").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewShipmentDocument");
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
                            'Shipment Document Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Shipment Document Updated Successfully',
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
                   ShowShipmentDocument();
                },
            });
        });
        
    });
    
    function Reset()
    {
        $("#shipment_document_id").val('');
        $("#shipment_document_name").val('');
        $("#btnSave").html("Add");
    }

    function ShowShipmentDocument()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowShipmentDocument"
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
            $("#shipment_document_id").val(res.shipment_document_id);
            $("#shipment_document_name").val(res.shipment_document_name);
            $("#btnSave").html("Update");
        });
    }
</script>
