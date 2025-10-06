<?php 
    include_once('header.php'); 
    if(isset($_GET['po_id']))
    {
        $po_id =base64_decode($_GET['po_id']);
        $rstpurchase = mysqli_query($connect,"select p.po_custom_number, p.po_date,a.client_name from 
        purchase_order p inner join client_master a on a.client_id= p.supplier_id
        where p.po_id='$po_id' ");
        if(mysqli_num_rows($rstpurchase)>0)
        {
            $rwpurchase = mysqli_fetch_assoc($rstpurchase);
        }
    }        
?>
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <div class="card">
            <div class="card-body">
                <!-- PO Summary Section -->
                <div class="mb-4 border-bottom pb-1">
                    <h5>PO Shipment Details</h5>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <strong>PO Number:</strong> <span class="text-primary" id="po_number"><?= @$rwpurchase['po_custom_number']; ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>PO Date:</strong> <span class="text-primary" id="po_date"><?= @$rwpurchase['po_date']; ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Supplier Name:</strong> <span class="text-primary" id="client_name"><?= @$rwpurchase['client_name']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Shipment Form -->
                <form id="shipmentForm">
                    <div class="form-row">
                        
                        <div class="form-group col-md-3">
                            <label>Shipment Date</label>
                            <input type="hidden" name="po_number" id="po_number" value="<?= $po_id; ?>" />
                            <input type="date" class="form-control required" required name="shipment_date" id="shipment_date" />
                        </div>

                        <div class="form-group col-md-3">
                            <label>Mode Of Shipment</label>
                            <select class="form-control required " required name="shipment_by" id="shipment_by">
                                <option value="">Select Option</option>
                                <option value="Air">Air</option>
                                <option value="Sea">Sea</option>
                                <option value="Road">Road</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Select Incoterms</label>
                            <select id="modeOfShipment" class="form-control required" required name="modeOfShipment" onchange="loadNoofcontainers();">
                                
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Supplier Invoice Number</label>
                            <input type="text" class="form-control required" required name="supplier_invoice" id="supplier_invoice"/>
                        </div>

                        <div class="form-group col-md-3">
                            <label>ETA</label>
                            <input type="date" class="form-control required" required name="eta" id="eta"/>
                        </div>

                        <div class="form-group col-md-3">
                            <label>ETD</label>
                            <input type="date" class="form-control required" required name="etd" id="etd" />
                        </div>

                        <div class="form-group col-md-3">
                            <label>Number of Containers</label>
                            <input type="number" min="1" class="form-control required" required name="no_of_container" id="no_of_container" oninput="loadNoofcontainers();"/>
                        </div>
                    </div>
                    <div class="form-row mb-3" id="divloadnoofcontainer" style="border: 1px solid black; padding: 12px 7px;"></div>
                    <input type="submit"  class="btn btn-primary" value="Save">
                </form>

            </div>
        </div>
    </div>
</div>
<?php include_once('footer.php'); ?>
<script>
    const containerGroup = document.getElementById("containerNumbersGroup");
    $(document).ready(function(){
        loadmodeOfShipment();
        $('#shipmentForm').on('submit', function(e){
            e.preventDefault();
            var form = this; 
            var fd = new FormData(form);
            fd.append('Flag', "SaveShipmentDetails");

            var mode = $('#modeOfShipment').val();
            var valid = true;
            var errMsg = '';
            let containerNumbers = [];
            // if(mode === 'FOB') {
                $('#divloadnoofcontainer input[name="container_numbers[]"]').each(function(i, el){
                    let val = $(el).val().trim();
                    if (!val) {
                        valid = false;
                        errMsg = 'Please fill in all container numbers.';
                        return false; 
                    }
                    if (containerNumbers.includes(val)) {
                        valid = false;
                        errMsg = 'Duplicate container number found: ' + val;
                        return false;
                    }

                    containerNumbers.push(val);
                });
            // } else {
            //     var single = $('#divloadnoofcontainer input[name="container_qty_single"]').val();
            //     if (!single || single.trim() === '') {
            //         valid = false;
            //         errMsg = 'Please enter total container quantity.';
            //     }
            // }
            if (!valid) {
                 Swal.fire(
                    'oops',
                    errMsg,
                    'error'
                );
                return false;
            }
            else{
                $.ajax({
                    url:"operation/shipment_operation.php",
                    data:fd,
                    type:"POST",
                    contentType: false, 
                    processData: false,
                    success:function(response)
                    {
                        if(response=="Success")
                        {
                            Swal.fire(
                                'Well Done!',
                                'Shipment Document Added Successfully',
                                'success'
                            );
                            setTimeout(() => {
                                window.location.href="po_shipment.php";
                            }, 1000);
                        }else{
                            Swal.fire(
                                'oops',
                                response,
                                'error'
                            );
                        }  
                    },
                });
            }
        });
    });
    function loadNoofcontainers(){
        $("#divloadnoofcontainer").html('');
        var modevalue = $("#modeOfShipment option:selected").val();
        var NoOfContainer = $("#no_of_container").val();
        // if (modevalue === "FOB") {
            if (NoOfContainer > 0) {
                rawdata ='';
                for (var i = 1; i <= NoOfContainer; i++) {
                    rawdata += `
                        <div class="form-group col-md-3">
                            <label>Container ${i} Number</label>
                            <input type="text" class="form-control" name="container_numbers[]" placeholder="Enter container ${i} number" />
                        </div>
                    `;
                }
            } else {
                rawdata = ``;
            }
        // } else {
        //     rawdata = `
        //         <div class="form-group col-md-3">
        //             <label>Total Container Quantity</label>
        //             <input type="number" class="form-control" name="container_qty_single" id="container_qty_single" placeholder="Enter total container quantity" />
        //         </div>
        //     `;
        // }

        $("#divloadnoofcontainer").html(rawdata);
    }
    function loadmodeOfShipment()
    {
        $.post("../component.php",{
            Flag:"loadModeOfShipment",
        },function(data,success){     
            $("#modeOfShipment").html(data);
        });
    }

</script>