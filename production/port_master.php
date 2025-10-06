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
                        <form method="post" id="frmPort">
                        <div class = 'form-group'>
                             <label for = 'Choose Countrye' class = 'form-label'> Choose Country </label>
                             <select id='country_id' name='country_id' class='form-control font-size-select' required>
                                <option value="select">Select  Country</option>
                                    <?php
                                        $query = $connect->query("SELECT id, countryName FROM country_master");
                                        $rowCount = $query->num_rows;
                                        if ($rowCount > 0) {
                                            while ($row = $query->fetch_assoc()) {
                                                echo '<option value="' . $row['id'] . '">' . $row['countryName'] . '</option>';
                                            }
                                        }
                                    ?>
                             </select>
                          </div>

                            <div class="form-group">
                                <label for="Port Name">Port Name</label>
                                <input type="hidden" name="port_master_id" id="port_master_id" class='form-control'>
                                <input type="text" class="form-control" id="port_name" name="port_name" placeholder="Enter Port Name" required>
                            </div>

                            <!-- <div class="form-group">
                                <label for="Address">Address</label>
                                <input type="text" class="form-control" id="port_address" name="port_address" placeholder="Enter Address" required>
                            </div> -->

                            <div class='form-group'>
                                <label for='Port/Shipment Type' class='form-label'> Port/Shipment Type </label>
                                <select id='portShipment_Type' name='portShipment_Type' class='form-control font-size-select' required>
                                    <option value = 'select'>Select Port/Shipment Type</option>
                                    <option value = 'Air'>Air</option>
                                    <option value = 'Sea'>Sea</option>
                                    <option value = 'Other'>Other</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="submit" class="btn btn-sm btn-success" id="btnSave">Save</button>
                                <a href="port_master.php" class="btn btn-sm btn-warning"><i class="fa-solid fa-rotate"></i>Reset</a>
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
        ShowPort();
        $("#frmPort").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "NewPort");
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
                            'Port Added Successfully',
                            'success'
                        );
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Port Updated Successfully',
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
                   ShowPort();
                },
            });
        });
        
    });
    
    function Reset()
    {
        $("#country_id").val('');
        $("#port_name").val('');
       // $("#port_address").val('');
        $("#portShipment_Type").val('');
        $("#btnSave").html("Add");
    }

    function ShowPort()
    {
        $.post("operation/CrudOperation.php",{
            Flag:"ShowPort"
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
            $("#country_id").val(res.country_id);
            $("#port_master_id").val(res.port_master_id);
            $("#port_name").val(res.port_name);
            //$("#port_address").val(res.port_address);
            $("#portShipment_Type").val(res.portShipment_Type);
            $("#btnSave").html("Update");
        });
    }
</script>
