<?php 
    include_once('header.php'); 
    if(isset($_GET['po_id']))
    {
        $po_id =base64_decode($_GET['po_id']);
        $rstpurchase = mysqli_query($connect,"select p.po_custom_number, p.po_date,a.client_name,
        ph.container_numbers,ph.shipment_id from 
        purchase_order p inner join client_master a on a.client_id= p.supplier_id
        inner join purchase_shipment_head ph on ph.po_number = p.po_id 
        where p.po_id='$po_id' ");
        if(mysqli_num_rows($rstpurchase)>0)
        {
            $rwpurchase = mysqli_fetch_assoc($rstpurchase);
            $container_numbers = $rwpurchase['container_numbers'];
            $shipment_id = $rwpurchase['shipment_id'];
        }
    }        
?>
<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <div class="card">
            <div class="card-body">
                <!-- PO Summary Section -->
                <div class="mb-4 border-bottom pb-1">
                    <h5>Container Allocation</h5>
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
                <form id="shipmentForm">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Select Container</label>
                            <input type="hidden" id="hidden_po_id" name="hidden_po_id" value="<?= $po_id; ?>">
                            <input type="hidden" id="hidden_po_shipment_id" name="hidden_po_shipment_id" value="<?= $shipment_id; ?>">
                            <select class="form-control"  name="txtcontainer_no" id="txtcontainer_no">
                                <?php
                                    echo "<option value=''>Select Option</option>";
                                    $container_numbers = explode(',',$container_numbers);
                                    foreach($container_numbers as $Containers)
                                    {
                                        echo "<option value='$Containers'>$Containers</option>";
                                    }
                                ?>
                            </select>
                        </div>
                       <div class="form-group col-md-3">
                            <label>Select Product</label>
                            <select class="form-control"  name="txtproduct_id" id="txtproduct_id">
                               <?php
                                    echo "<option value=''>Select Product</option>";
                                    $rstpro = mysqli_query($connect,"select p.product_name,pp.product_id,um.u_name,pp.total_weight
                                    from purchase_order_details pp
                                    inner join product_master p on p.product_id = pp.product_id 
                                    left join uom_master um on um.u_id = p.uom_id
                                    where pp.purchase_order_id ='$po_id' ");
                                    if(mysqli_num_rows($rstpro)>0)
                                    {
                                        while($rwpro = mysqli_fetch_assoc($rstpro))
                                        {
                                            $product_id = $rwpro['product_id'];
                                            $product_name = $rwpro['product_name'];
                                            $u_name = $rwpro['u_name'];
                                            $total_weight = $rwpro['total_weight'];
                                            echo "<option value='$product_id' data-attr='$u_name' data-qty='$total_weight'>$product_name</option>";
                                        }
                                    }
                               ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>UOM</label>
                            <input type="text" readonly class="form-control"  name="product_uom" id="product_uom" />
                        </div>
                        <div class="form-group col-md-2">
                            <label>Enter Qty</label>
                            <input type="number" min="1" class="form-control"  name="product_qty" id="product_qty" oninput="check_avail_qty();"/>
                        </div>
                        <div class="form-group col-md-1 mt-3">
                            <button type='button' class="btn btn-sm btn-success " onclick="LoadAllocationProducts();"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                     <div class="form-row mt-2" id="divShipmentAllocation">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Container</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="shipmentAllocTableBody"></tbody>
                        </table>
                     </div>
                    <input type="submit"  class="btn btn-primary" value="Save">
                </form>

            </div>
        </div>
    </div>
</div>
<?php include_once('footer.php'); ?>
<script>
    function LoadAllocationProducts() {
        var container = $('#txtcontainer_no').val(); // First dropdown
        var product = $('#txtproduct_id').val();   // Second dropdown
        var productText = $('#txtproduct_id option:selected').text();
        var qty = $('#product_qty').val();

        if (container === '' || product === '' || qty === '') {
            Swal.fire('Validation Error', 'Please select container, product and enter quantity.', 'warning');
            return;
        }
        if(qty<=0)
        {
            swal.fire('Error','Please Add Valid Product Qty','error');
            return;
        }
        var row = `
            <tr>
                <td><input type="hidden" name="alloc_container[]" value="${container}">${container}</td>
                <td><input type="hidden" name="alloc_product[]" value="${product}" data-attr="${productText}" >${productText}</td>
                <td><input type="hidden" name="alloc_qty[]" value="${qty}">${qty}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removecontainerproduct(this, '${product}');"><i class="fa fa-trash"></i></button></td>
            </tr>
        `;
        $('#shipmentAllocTableBody').append(row);
        $('#txtcontainer_no').val('');
        $('#txtproduct_id').val('');
        $('#product_qty').val('');
        $("#txtproduct_id option[value='" + product + "']").remove();
    }
    function removecontainerproduct(button, productId) {
       const row = $(button).closest('tr');
        row.remove();
        const productText = row.find('input[name="alloc_product[]"]').data('attr');
        $('#txtproduct_id').append(`<option value="${productId}">${productText}</option>`);
    }
    $(document).ready(function(){

        $("#txtproduct_id").on('change',function(){
            var product_uom = $("#txtproduct_id option:selected").data('attr');
            $("#product_uom").val(product_uom);
            $("#product_qty").val(0);
        });
        $("#shipmentForm").submit(function(e){
            e.preventDefault();
            let fd = new FormData(this);
            fd.append("Flag","SaveShipmentAllocation");
            $.ajax({
                url: "operation/shipment_operation.php",
                data : fd,
                type: "POST",
                contentType: false,
                processData: false,
                success:function(response)
                {
                    if(response=="Success")
                    {
                        Swal.fire(
                            'Well Done!',
                            'Container Allocated Successfully',
                            'success'
                        );
                        setTimeout(() => {
                            window.location.href = "po_shipment.php";
                        }, 2000);
                    }else{
                        Swal.fire(
                            'oops',
                            response,
                            'error'
                        );
                    }
                },
            });
        });
    });
    function check_avail_qty()
    {
        var product_qty = $("#product_qty").val();
        var product_actual_qty = $("#txtproduct_id option:selected").data('qty');
        var product_uom = $("#txtproduct_id option:selected").data('attr');
        if(product_qty > product_actual_qty)
        {
            swal.fire(
                'PO Qty - '+product_actual_qty+' '+product_uom,
                'Exceed Qty Not Allowed',
                'error'
            );
            $("#product_qty").val('');
            $("#product_qty").focus();
        }
    }
</script>