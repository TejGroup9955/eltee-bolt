<?php 
	$page_heading="Edit Lead Entry";
	include_once 'configuration.php';
	include_once 'header.php';
	$client_id=base64_decode($_GET['client_id']);
	
	$firm_name = "";
	extract($_POST);

	$cmd = "SELECT c.*,r.requirement_details,r.requirement_id
			FROM client_master c
            inner join client_requirement_details r on r.client_id = c.client_id
			WHERE c.client_id='" .$client_id."'";
	$result = $connect->query($cmd);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $visiting_card1 = $row['visiting_card'];
            $maps_link1 = $row['maps_link'];
            $website_link1 = $row['website_link'];
            $client_name1=$row['client_name'];
            $firm_name1=$row['firm_name'];
            $client_business1=$row['client_business'];
            $client_add1=$row['client_add'];
            $work_address1=$row['work_address'];
            $client_email1=$row['client_email'];
            $client_mob1=$row['client_mob'];
            $kind_attention1=$row['kind_attention'];
            $client_person11=$row['client_person1'];
            $client_person1_mob1=$row['client_person1_mob'];
            $client_person21=$row['client_person2'];
            $client_person2_mob21=$row['client_person2_mob2'];
            $client_required_details1=$row['client_required_details'];
            $client_status1=$row['client_status'];
            $landline1=$row['landline'];
            $fax1=$row['fax'];
            $business_type1=$row['business_type'];
            $current_status1=$row['current_status'];
            $requirement_details=$row['requirement_details'];
            $requirement_id=$row['requirement_id'];
            // $source_from=$row['source_from'];
            
            //Below Two Fields Are Not Required Anymore
            $business_type1 = '';
            $current_status1 = '';
            
            $client_strength1=$row['client_strength'];
            
            $office_country_id = $row['office_country_id'];
            $work_country_id = $row['work_country_id'];
            $client_gst_number = $row['GST_no'];
            $office_state_id = $row['office_state_id'];
            $work_state_id = $row['state'];
            
            $office_city_id1 = $row['office_city_id'];
            $office_area_id1 = $row['office_area_id'];
            $office_pincode1 = $row['office_pincode'];

            $work_city_id1 = $row['work_city_id'];
            $work_area_id1 = $row['work_area_id'];
            $work_pincode1 = $row['work_pincode'];

        }
    }
    $client_products = [];
    $rstproduct = mysqli_query($connect,"SELECT cr.*,p.product_name FROM client_product_details cr
    inner join product_master p on p.product_id = cr.product_id
    WHERE cr.client_id = '" .$client_id."'");
    if(mysqli_num_rows($rstproduct)>0)
    {
        while($rwproduct = mysqli_fetch_assoc($rstproduct)){
            $client_products[] = $rwproduct['product_id'];
        }
    }
    $inc = 1;
    $i = 0;
    $contact_person = "";$contact_person_designation = "";
    $cmd_1 = "SELECT contact_person_name, contact_person_mobile, designation, contact_person_email FROM client_requirement_contact_master WHERE client_id = '$client_id' ";
    $result_1 = $connect->query($cmd_1);
    if ($result_1->num_rows > 0)
    {
        while($row_1 = $result_1->fetch_assoc())
        {
            $contact_person_designation.= '
            <div style="display:none;" class="contact_person_update col12" id="contact_person_update_'.$i.'" style="padding:3px; border:solid 1px #aaa;border-radius: 15px;" ><label>Contact Person Name</label><input type="text" id="contact_person_name_'.$i.'" name="contact_person_name[]" class="form-control required " placeholder="Contact Person Name" value="'.$row_1['contact_person_name'].'" /><div class="col6"><label>SupplierMobile/Telephone</label><input type="number" id="contact_person_mobile_'.$i.'" name="contact_person_mobile[]" value="'.$row_1['contact_person_mobile'].'" onkeypress="return isNumber(event)" class="form-control required Number" placeholder="SupplierMobile/Telephone" /></div><div class="col6"><label>Designation</label><input type="text" name="designation[]" id="designation_'.$i.'" value="'.$row_1['designation'].'" class="form-control required" placeholder="Designation" /></div><label>Contact Email </label><input type="email" name="contact_person_email[]" id="contact_person_email_'.$i.'" class="form-control no_space" placeholder="Contact Email" value="'.$row_1['contact_person_email'].'"  /> <div class="center mt-2"><a onclick="update_contact('.$i.')" class="btn btn-warning  btn-round btn-sm"><i class="fa fa-pencil"></i> Update</a></div></div>';
            $contact_person.='<tr id="contact_div_'.$i.'"><td>'.$row_1['contact_person_name'].'</td><td>'.$row_1['contact_person_mobile'].'</td><td><div class="center" style="display:flex;padding:0;margin-right: 0px;"><a class="btn btn-sm btn-round btn-warning " onclick="update_contact_details('.$i.')"><i class="fa fa-pencil"></i></a><a class="btn btn-sm btn-round btn-danger " onclick="remove_contact_details('.$i.')"><i class="fa fa-remove"></i></a> </div></td></tr>';
            $inc++;
            $i++;
        }
    }
    echo '
    <script>
    var i = '.$i.';
    </script>
    ';
?>
<style type="text/css">
    .fa-star{
        color: #F24646;
    }
    label{
        margin-bottom: -8px;
        margin-top: 2px;
    }
</style>

<div class="right_col" role="main" >
    <div class="container-xxl flex-grow-1">
        <form id="frmLead">
            <div class="row">
                <div class="col-md-8">         
                    <div class="card">
                        <div class="card-body">
                            <div id="result"></div>
                            <input type="hidden" name="comp_id" id="comp_id" value="<?php echo @$dept_id; ?>">
                            <input type="hidden" name="client_id" id="client_id" value="<?php echo @$client_id; ?>">
                            <input type="hidden" name="requirement_id" id="requirement_id" value="<?php echo @$requirement_id; ?>">
                            <input type="text" class="form-control" style="display: none;" name="visit_type" value="Fresh" disabled="disabled" />
                            <div class="col12 custom_header_panel custom_header_panel_back_1"><i class="fa fa-user"></i> Business Details</div><hr>
                            <div class="row">
                                <div class="col-md-4" data-toggle="tooltip" data-placement="left" title="Please Enter SupplierName" >
                                    <label>Supplier/ Organization Name</label>
                                    <input type="text" placeholder="Enter Supplier Name" name="client_name" onkeypress="return isAlpha(event)" value="<?php echo @$client_name1;?>" class="form-control required " required="" />
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="left" title="Please Enter Supplier Business">
                                    <label>Supplier Business</label>
                                    <input type="text" name="client_business" placeholder="Enter Business" onkeypress="return isAlpha(event)" value="<?php echo @$client_business1;?>" class="form-control " />
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12" >
                                    <label>Kind Attention</label>
                                    <input type="text" class="form-control required" placeholder="Enter Kind Attention" value="<?php echo $kind_attention1;?>" name="kind_attention" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="left" title="Please Enter Office Address">
                                    <label>Office Address</label>
                                    <textarea class="form-control "  name="client_add" placeholder="Office Address" rows="4"><?php echo @$client_add1;?></textarea>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Country</label>
                                            <!--<div onclick="show_hide('add_office_country');" data-toggle="tooltip" title="Add Country" class="btn btn-dark btn-xs col2 pull-right"><i class="fa fa-plus"></i></div>-->
                                            <select class="form-control required " required="" id='ddlCountry' name="ddlCountry" onchange="LoadState();">
                                                <option value="">Select Country</option>
                                                <?php
                                                    $cmd2 = "SELECT * FROM country_master where status=1";
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        while($row2 = $result2->fetch_assoc()) {
                                                            if($office_country_id == $row2['id']) { $selected='selected'; } else { $selected=''; }
                                                            echo '<option '.$selected.' value="'.$row2['id'].'">'.$row2['countryName'].'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>State</label>
                                            <!--<div onclick="show_hide('office_state');" data-toggle="tooltip" title="Add Country" class="btn btn-dark btn-xs col2 pull-right"><i class="fa fa-plus"></i></div>-->
                                            <select class="form-control required " required="" id='ddlState' name="ddlState" onchange="LoadCity();">
                                                
                                                <?php
                                                    $sql = "SELECT * FROM state_master WHERE status=1 and id = '$office_state_id'";
                                                    // echo $sql;
                                                    $query = mysqli_query($connect, $sql);
                                                    $res = mysqli_fetch_assoc($query);
                                                    echo '<option value="'.$res['id'].'">'.$res['stateName'].'</option>';
                                                    $cmd2 = "SELECT * FROM state_master WHERE status=1 and id != '$office_state_id' AND countryID = '$office_country_id'";
                                                    // echo $cmd2;
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        while($row2 = $result2->fetch_assoc()) {
                                                            if($office_state_id == $row2['id']) { $selected='selected'; } else { $selected=''; }
                                                            echo '<option '.$selected.' value="'.$row2['id'].'">'.$row2['stateName'].'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>City</label>
                                            <select class="form-control mt-2"  id='office_city_id' name="office_city_id" onchange="LoadArea();">
                                                <option value="">Select City</option>
                                                <?php
                                                    $cmd2 = "SELECT city_id, city_name FROM city_master WHERE status=1 and  stateID='".$office_state_id."'";
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        while($objState = mysqli_fetch_object($result2)) {
                                                            $selected ="";
                                                            if($office_city_id1 == $objState->city_id) {
                                                                $selected ="selected";
                                                            } 
                                                            echo '<option value="'.$objState->city_id.'" '.$selected.'>'.$objState->city_name.'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label >Area</label>
                                            <select class="form-control" id='office_area_id' name="office_area_id">
                                                <?php
                                                if($office_area_id1 != "0") {
                                                    $cmd2 = "SELECT area_id, area_name FROM area_master WHERE status=1 and area_id = '$office_area_id1' ";
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        if($row2 = $result2->fetch_assoc()) {
                                                            echo '<option value="'.$row2['area_id'].'">'.$row2['area_name'].'</optin>';
                                                        }
                                                    }
                                                    
                                                    $cmd2 = "SELECT area_id, area_name FROM area_master WHERE status=1 and  area_id != '$office_area_id1' AND city_id = '$office_city_id1' ";
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        while($row2 = $result2->fetch_assoc()) {
                                                            echo '<option value="'.$row2['area_id'].'">'.$row2['area_name'].'</optin>';
                                                        }
                                                    }
                                                } else {
                                                    echo '<option value="">Select Area</option>';
                                                    $cmd2 = "SELECT area_id, area_name FROM area_master WHERE status=1 and area_id != '$office_area_id1' AND city_id = '$office_city_id1' ";
                                                    $result2 = $connect->query($cmd2);
                                                    
                                                    if ($result2->num_rows > 0) {
                                                        while($row2 = $result2->fetch_assoc()) {
                                                            echo '<option value="'.$row2['area_id'].'">'.$row2['area_name'].'</optin>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row  mt-2">
                                        <div class="col-md-4">
                                            <label>PIN Code</label>
                                            <input type="text" class="form-control no_space Number " maxlength="10" minlength="6" placeholder="Enter PIN Code" name="office_pincode" value="<?php echo @$office_pincode1; ?>" />
                                        </div>   
                                        
                                        <div class="col-md-4" data-toggle="tooltip" data-placement="left" title="Please Enter Supplier Email">
                                            <label>Supplier Email</label>
                                            <input type="text" name="client_email" placeholder="Enter Supplier Email ID" value="<?php echo @$client_email1;?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" class="form-control no_space" />
                                        </div>

                                        <div class="col-md-4" data-toggle="tooltip" data-placement="left" title="Please Enter Supplier Mobile">
                                            <label>Supplier Mobile</label>
                                            <input type="text" id="client_mob" name="client_mob" placeholder="Enter Supplier Mobile"  onkeypress="return isNumber(event)" value="<?php echo @$client_mob1;?>" class="form-control required "  required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label for="product_id" class="form-label">Interested Products</label>
                                    <select id="product_id" name="product_id[]" class="form-control js-example-basic-multiple required" multiple="multiple" required>
                                        <!-- <option value="select">Select Products</option> -->
                                        <?php
                                            $rstpro = mysqli_query($connect,"select product_id,product_name from product_master where status='Active'");
                                            if(mysqli_num_rows($rstpro)>0){
                                                while($rwpro = mysqli_fetch_assoc($rstpro))
                                                {
                                                    $product_id = $rwpro['product_id'];
                                                    $product_name = $rwpro['product_name'];
                                                    $selected = in_array($product_id, $client_products) ? 'selected' : '';
                                                    echo "<option value='$product_id' $selected>$product_name</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-12 col-xs-12" >
                                    <label>GST Number</label>
                                    <input type="text" name="client_gst_number" placeholder="Enter GST Number"  value="<?php echo $client_gst_number;?>" class="form-control " />
                                </div>
                            </div>
                                
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div id="contact_person_designation">
                                <?php
                                     echo $contact_person_designation;
                                ?>
                            </div>
                            <div class="col12" id="contact_person_new">
                                    <label>Contact Person Name</label>
                                    <input type="text" id="contact_person_name" class="form-control" placeholder="Contact Person Name" />
                                    
                                    <div class="col6">
                                        <label>Contact Person Mobile</label>
                                        <input type="text" id="contact_person_mobile" class="form-control Number" placeholder="Contact Person Mobile" />
                                    </div>
                                    <div class="col6">
                                        <label>Designation</label>
                                        <input type="text" id="designation" class="form-control" placeholder="Designation" onkeypress="return isAlpha(event)"/>
                                    </div>
                                    <label>Contact Email</label>
                                    <input type="email" id="contact_person_email" class="form-control no_space" placeholder="Contact Email" />
                                <!-- </div> -->
                                <div class="center mt-2 mb-2" >
                                    <a onclick="add_more_contact_details()" style="color:white" class="btn btn-secondary btn-round btn-sm"><i class="fa fa-plus"></i> Add More</a>
                                </div>
                            </div>  
                            <div class="table-responsive">          
                                <table class="mt-4 display table-bordered table-responsive table table-striped">
                                    <thead>
                                        <tr>
                                            <td>Contact Person Name</td>
                                            <td>Contact Person Mobile</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="contact_person">
                                        <?php
                                        echo $contact_person; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>  
                        </div>
                    </div>
                </div>
                <div style="text-align: center;" class="col-md-12">
                        <br>
                        <button type="submit" name="update" class="btn btn-round btn-warning"><i class="fa fa-save"></i> &nbsp;Update</button>
                        <!-- <a href="" class="btn btn-round btn-success"><i class="fa fa-refresh"></i> Reset</a> -->
                        <a href="supplier-Summary.php" class="btn btn-round btn-secondary"><i class="fa fa-chevron-left"></i>&nbsp; Back</a>
                        <br>
                        <br>
                </div>
                
                
            </div>
        </form>
    </div>
</div>
            

<?php include 'footer.php';
include_once '../ajaxfunction.php';
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.js-example-basic-multiple').select2({
            placeholder:"Select Products"
        });
		$('#ddlCountry').select2({
			placeholder: "Select Country",
			tags:false,
		});

		$('#ddlState').select2({
			placeholder: "Select State",
			tags:false,
		});

		$('#office_city_id').select2({
			placeholder: "Select City",
			tags:false,
		});

		$('#office_area_id').select2({
			placeholder: "Select Area",
			tags:false,
		});

        $("#frmLead").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            var form = event.target;
            formData.append("Flag", "UpdateLeadDetails");
            showSpinner();
            $.ajax({
                url: "operation/LeadOperation.php", 
                type: "POST", // Type of the request
                data: formData, // The form data to send
                processData: false, // Prevent jQuery from automatically converting the data to a query string
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                    hideSpinner();
                   if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Supplier Details Updated Successfully',
                            'success'
                        );
                        setTimeout(() => {
                           window.location.href="supplier-Summary.php";
                        }, 2000);
                   }
                    else
                   {
                        Swal.fire(
                            'Error!',
                            response,
                            'error'
                        );
                   }
                },
            });
        });

	});
   

    window.submittingForm = false;
    $('input[novalidate]').bind('invalid', function(e) {
        if(!window.submittingForm){
            window.submittingForm = true;
            $(e.target.form).submit();
            setTimeout(function(){window.submittingForm = false;}, 100);
        }
        e.preventDefault();
        return false;
    });

    function OnSelectionChange (select) {
        var selectedOption = select.options[select.selectedIndex].value;
        if(selectedOption == "Close")
        {
            $('#fromdate').css('display' , 'none');
            $('.Zebra_DatePicker_Icon').css('display' , 'none');

        } else {
            $('#fromdate').css('display' , 'block');
            $('.Zebra_DatePicker_Icon').css('display' , 'block');
        }
    }
    function LoadCountry(){
      $.post("../component.php",{
         Flag:"LoadCountry"
      },function(data,success){
         $("#ddlCountry").html(data);
      });
   }
   function LoadState(){
      $.post("../component.php",{
         Flag:"LoadState",
         CountryId :$("#ddlCountry option:selected").val()
      },function(data,success){
         $("#ddlState").html(data);
      });
   }
   function LoadCity(){
      $.post("../component.php",{
         Flag:"LoadCity",
         StateId :$("#ddlState option:selected").val()
      },function(data,success){
         $("#office_city_id").html(data);
      });
   }
   function LoadArea()
   {
      $.post("../component.php",{
         Flag:"LoadArea",
         CityId :$("#office_city_id option:selected").val()
      },function(data,success){
         $("#office_area_id").html(data);
      });       
   }
</script>