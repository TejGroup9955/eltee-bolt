<?php include_once('header.php'); ?>

<!-- Custom Styles -->
<style>
  .form-control {
    border-radius: 5px;
    font-size: 0.9rem; 
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
      padding-left: 20px;
   }
</style>

<!-- Page Content -->
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
          <form id="frmproduct">
            <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="productCategory" class="form-label" >Category</label>
                  <select class="form-control" id="productCategory" name="productCategory" required>
                  
                  </select>
                </div>
                <div class="col-md-5 mb-3">
                  <label for="productName" class="form-label">Product Name</label>
                  <input type="hidden" class="form-control" id="productId" name="productId">
                  <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter Product Name" required>
                </div>
                <div class="col-md-2 mb-3">
                  <label for="productCode" class="form-label">Product Code</label>
                  <input type="text" class="form-control" id="productCode" name="productCode" placeholder="Enter Product Code" required>
                </div>
                <div class="col-md-2 mb-3">
                  <label for="uom" class="form-label">UOM</label>
                  <select class="form-control" id="uom" name="uom">
                   
                  </select>
                </div>
                <div class="col-md-2 mb-3">
                  <label for="pakingtype" class="form-label">Packing Type</label>
                  <select class="form-control js-example-basic-multiple" id="pakingtype" name="pakingtype[]" multiple="multiple" >
                    <?php
                        $rstpack = mysqli_query($connect,"select * from packaging_type where status='Active'");
                        while($rwpack = mysqli_fetch_assoc($rstpack))
                        {
                          $id = $rwpack['id'];
                          $packaging_type = $rwpack['packaging_type'];
                          echo "<option value='$id'>$packaging_type</option>";
                        }
                    ?>
                  </select>
                </div>

                <div class="col-md-3 mb-3">
                  <label for="HSNRate" class="form-label">HSN No.</label>
                  <input type="text" class="form-control" id="HSNRate" name="HSNRate" placeholder="Enter HSN No" required>
                </div>

                <div class="col-md-3 mb-3">
                  <label for="goodsTax" class="form-label">Goods Tax %</label>
                  <input type="number" class="form-control" id="goodsTax" name="goodsTax" placeholder="Enter Tax %" required>
                </div>

                <!-- Button Section -->
                <div class="col-md-4 mt-3">
                  <input type="submit" class="btn btn-sm btn-success" id="btnSave" value="Save">
                  <a href="product_master.php"><button type="button" class="btn btn-sm btn-warning">Reset</button></a>
                  <a href="index.php"><button type="button" class="btn btn-sm btn-secondary">Close</button></a>
                </div>
              </div>
          </form>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-body">
          <div class="table-responsive" id="divRecordEmployee"></div>
        </div>
      </div>
  </div>
</div>
<!-- /Page Content -->
<div class="modal fade" id="LoadProductSpecificationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="ProdSpecificationHead">Add Product Specification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <form id="frmProductSpecification" enctype="multipart/form-data" >
                      <div class="col12" style="margin-top:-28px;"> 
                        <div class="col-md-5">
                          <label>Enter Description</label>
                          <input type="hidden" name="prodspecificationproductId" id="prodspecificationproductId" class="form-control" required="">
                          <input type="hidden" name="prodspecificationId" id="prodspecificationId" class="form-control" required="">
                          <input type="text" name="product_description" id="product_description" class="form-control" placeholder="Enter Product Description" required="" value="">
                        </div>
                        <div class="col-md-4">
                          <label>Enter value</label>
                          <input type="text" name="value" id="value" class="form-control " placeholder="Enter Product Value" value="" required="">
                        </div>
                        <div class="col-md-3">
                          <label>Select Range</label>
                          <select class="form-control" name="product_range" id="product_range" required>
                            <option value="">Select Range</option>
                            <option>Min</option>
                            <option>Max</option>
                            <option>MM</option>
                            <option>MESH</option>
                          </select> 
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                          <br>
                          <button name="submit_btn" class="btprn btn-primary"><i class="fa fa-check"></i> Submit</button> &nbsp; 
                          <button class="btprn btn-danger" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Cancel</button>
                        </div>
                      </div>
                    <div class="row mt-3" id="divLoadPrevProductSpecification"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
<script>
  $(document).ready(function(){
    ShowProduct();
    LoadCategory();
    LoadUOM();
    $('.js-example-basic-multiple').select2({
         placeholder:"Select packaging Type"
      });
    $("#frmproduct").submit(function(e){
          e.preventDefault();
          var formData = new FormData(this);
          var form = event.target;
          formData.append("Flag", "NewProduct");
          $.ajax({
              url: "operation/EmployeeOperation.php", 
              type: "POST", // Type of the request
              data: formData, // The form data to send
              processData: false, // Prevent jQuery from automatically converting the data to a query string
              contentType: false, // Prevent jQuery from setting the content type
              success: function(response) {
                  if(response=="Inserted")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Product Added Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          location.reload();
                      }, 2000);
                  }
                  else if(response=="Updated")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Product Updated Successfully',
                          'success'
                      );
                      setTimeout(() => {
                          location.reload();
                      }, 2000);
                  }else
                  {
                      Swal.fire(
                          'Error!',
                          response,
                          'error'
                      );
                  }
                  ShowProduct();
              },
          });
    });
     $("#frmProductSpecification").submit(function(e){
          e.preventDefault();
          var formData = new FormData(this);
          var form = event.target;
          formData.append("Flag", "AddProductSpecification");
          $.ajax({
              url: "operation/EmployeeOperation.php", 
              type: "POST", // Type of the request
              data: formData, // The form data to send
              processData: false, // Prevent jQuery from automatically converting the data to a query string
              contentType: false, // Prevent jQuery from setting the content type
              success: function(response) {
                  if(response=="Inserted")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Product Specification Added Successfully',
                          'success'
                      );
                       $("#LoadProductSpecificationModal").modal("toggle");
                  }
                  else if(response=="Updated")
                  {
                      Swal.fire(
                          'Well Done!',
                          'Product Specification Updated Successfully',
                          'success'
                      );
                       $("#LoadProductSpecificationModal").modal("toggle");
                  }else
                  {
                      Swal.fire(
                          'Error!',
                          response,
                          'error'
                      );
                  }
                  ShowProduct();
              },
          });
    });
  });
  function ShowProduct()
    {
        $.post("operation/EmployeeOperation.php",{
              Flag:"ShowProduct"
        },function(data,success){
              $("#divRecordEmployee").html(data);
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
               $("#productId").val(res.product_id);
               $("#productCategory").val(res.category_id);
               $("#productName").val(res.product_name);
               $("#productCode").val(res.product_code);
               $("#uom").val(res.uom_id);
               $("#HSNRate").val(res.hsn_rate);
               $("#goodsTax").val(res.goods_tax);
               $("#btnSave").val("Update");
               $("#pakingtype").val([]).trigger("change"); 
                if (res.packing_type !== "") {
                  var PackagingArray = res.packing_type.split(","); 
                  var SelectedIDs = []; 
                  $("#pakingtype option").each(function() {
                      var option = $(this);
                      var optionText = option.text().trim(); 
                      var optionValue = option.val(); 
                      if (PackagingArray.includes(optionValue)) {
                          SelectedIDs.push(optionValue); 
                      }
                  });
                  $("#pakingtype").val(SelectedIDs).trigger("change");
                }
         });
      }
      function LoadCategory(){
         $.post("../component.php",{
            Flag:"LoadCategory"
         },function(data,success){
            $("#productCategory").html(data);
         });
      }
      function LoadUOM(){
         $.post("../component.php",{
            Flag:"LoadUOM"
         },function(data,success){
            $("#uom").html(data);
         });
      }
      function AddProductSpecification(ProductName, ProductId)
      {
          $("#LoadProductSpecificationModal").modal("toggle");
          $("#prodspecificationproductId").val(ProductId);
          $("#ProdSpecificationHead").html("Add Product Specification For : "+ProductName);
          $("#divLoadPrevProductSpecification").html('');
          LoadPrevProductSpecification(ProductId);
          $("#product_description").val('');
          $("#value").val('');
          $("#product_range").val('');
          $("#prodspecificationId").val('');
      }
      function LoadPrevProductSpecification(ProductId)
      {
        $.post("operation/EmployeeOperation.php",{
          Flag:"LoadPrevProductSpecification",
          ProductId:ProductId
        },function(data,success){
          $("#divLoadPrevProductSpecification").html(data);
        })
      }
      function update_productspecification(product_id, product_spec_id,product_desc,value,product_range)
      {
          $("#product_description").val(product_desc);
          $("#value").val(value);
          $("#product_range").val(product_range);
          $("#prodspecificationproductId").val(product_id);
          $("#prodspecificationId").val(product_spec_id);
      }
</script>