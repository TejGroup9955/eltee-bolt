<?php 
$page_heading="Country Wise Client Details";
include '../configuration.php';
include 'header.php';
include '../ajaxfunction.php';

$compid = "";
$comp_name = "PLEASE SELECT";
$branchid = "";
$branch_name = "Select Branch";
$CountryId = @$_GET['CountryId'];
?>

<div id="wrapper"> 
    <div class="right_col" role="main">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <select name="txtcountry" id="txtcountry" class="form-control form-select">
                            <?php
                                 echo "<option value=''>Select Country</option>";
                                 $rstdes = mysqli_query($connect,"select * from country_master where status='1'");
                                 while($rwdes = mysqli_fetch_assoc($rstdes))
                                 {
                                     $id = $rwdes['id'];
                                     $countryName = $rwdes['countryName'];
                                     echo "<option value='$id'>$countryName</option>";
                                 }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="txtsupplier" id="txtsupplier" class="form-control form-select">
                            <?php
                                 echo "<option value=''>Select Supplier</option>";
                                 $rstdes = mysqli_query($connect,"select * from client_master where client_status!='Delete' and LeadType='Supplier' ");
                                 while($rwdes = mysqli_fetch_assoc($rstdes))
                                 {
                                     $client_id = $rwdes['client_id'];
                                     $client_name = $rwdes['client_name'];
                                     echo "<option value='$client_id'>$client_name</option>";
                                 }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="txtproduct" id="txtproduct" class="form-control form-select">
                            <?php
                                 echo "<option value=''>Select Product</option>";
                                 $rstdes = mysqli_query($connect,"select * from product_master where status='Active' ");
                                 while($rwdes = mysqli_fetch_assoc($rstdes))
                                 {
                                     $product_id = $rwdes['product_id'];
                                     $product_name = $rwdes['product_name'];
                                     echo "<option value='$product_id'>$product_name</option>";
                                 }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-body" >
                <div class="row" >
                    <input type='hidden' value="<?php echo $CountryId ?>" id="txtCountryId">
                    <table id="employee-grid"  cellpadding="0" cellspacing="0" border="0" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
                            <thead>
                                <tr>
                                    <th width="5"> Client Name </th>
                                    <th width="50"> Mobile No. </th>
                                    <th width="100"> Products </th>
                                    
                                </tr>
                            </thead>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script type="text/javascript" language="javascript" >
    $(document).ready(function() {
        var quotation_followup = "quotation_followup";
        var CountryId = $("#txtCountryId").val();
        //alert(quotation_followup);
        var dataTable = $('#employee-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "bDestroy": true,       

            "columnDefs": [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
                
            } ],  

            //"alengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
            "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
            "iDisplayLength": 25,
        
            "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
            "iDisplayLength": 10,           

            "ajax":{
                url :"operation/Country-Details-datagrid.php", // json datasource
                type: "post",  // method  , by default get
                data: {quotation_followup:quotation_followup,CountryId:CountryId},
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                    
                }
            }
        } );

        $('.search-input-text').on( 'blur', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();                   
            dataTable.columns(i).search(v).draw();
        } );
        $('.search-input-select').on( 'change', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();                   
            dataTable.columns(i).search(v).draw();
        } );

        
        $('.search-input-select').on( 'change', function () {   // for select box                   
            var i =$(this).attr('data-column');                     
            var v =$(this).val();                   
            dataTable.columns(i).search(v).draw();
        } );

    } );
</script>
