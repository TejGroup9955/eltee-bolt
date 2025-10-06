<?php
include '../configuration.php';
include 'header.php';
?>
<style>
.po-card {
    background: #fff;
    border: 1px solid #ccc;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.po-header {
    font-weight: bold;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 14px;
}

th {
    background-color: #3498db;
    color: white;
}

.btn {
    background: #2980b9;
    color: white;
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn:hover {
    background: #1c6ca1;
}
</style>

<div class="right_col" role="main">
    <div class="container-xxl flex-grow-1">
        <div class="card shadow-sm rounded">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0" style='color:white'>PO-wise Shipment and Dispatch Tracking</h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-2 mt-1">
                        <label ><b>Search BY PO/PI No.</b></label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="txtsearch" name="txtsearch">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-sm btn-success" type="button" onclick="LoadShipmentDetails();"><i class="fa fa-search"></i></button> 
                    </div>
                </div>
                <br>
                <div class="po-card" id="DivShipmentTractingDetails">
                   
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="productModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:99;">
  <div class="modal-content" style="background:white; padding:20px; width:30%; margin:100px auto; border-radius:10px;">
    <span onclick="closeModal()" style="float:right; cursor:pointer; font-size:20px;">&times;</span>
    <h4>Product Details</h4>
    <p id="productInfo"></p>
  </div>
</div>

<?php
include 'footer.php';
?>

<script>
  function showDetails(info) {
    document.getElementById("productInfo").innerText = info;
    document.getElementById("productModal").style.display = "block";
  }

  function closeModal() {
    document.getElementById("productModal").style.display = "none";
  }
  function LoadShipmentDetails()
  {
    $.post("operation/shipment_tracking_operation.php",{
        Flag:"LoadShipmentTrackingReport",
        SearchData : $("#txtsearch").val()
    },function(data,success){
        $("#DivShipmentTractingDetails").html(data);
    });
  }
</script>

