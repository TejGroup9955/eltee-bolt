<?php 
	$page_heading="New Lead Entry";
	include_once('configuration.php');
	include_once('production/header.php');

	$firm_name = "";

?>
<style type="text/css">

	.fa-star{
		color: #F24646;
	}
	label{
		margin-bottom: -8px;
		margin-top: 2px;
	}
	#customer_add{
		display: none;
	}
    .form-control {
      border-radius: 5px;
      font-size: 0.9rem; 
   }
   .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
      padding-left: 20px;
   }

</style>

<script>
	var i = 1;
</script>
<script type="text/javascript">
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

	function OnSelectionChange1(select)
	{
		var selectedOption = select.options[select.selectedIndex].value;
		if(selectedOption == "Not Interested" || selectedOption == "Raw" || selectedOption == "PO Received" || selectedOption == "Order Loss" )
		{
			$('#fromdate').css('display' , 'none');
			$('.hide_elements').css('display' , 'none');
			$("#followup_time").prop('required', false);
			$("#fromdate").prop('required', false);
		} else {
			$('#fromdate').css('display' , 'block');
			$('.hide_elements').css('display' , 'block');
			$("#followup_time").prop('required', true);
			$("#fromdate").prop('required', true);
		}
		if(selectedOption == "PO Received" )
		{
			$('#attachments').css('display' , 'block');
			$('#po_received_date').prop('required', true);
			$("#followup_time").prop('required', false);
			$("#fromdate").prop('required', false);      
		}
		else
		{
			$('#attachments').css('display' , 'none');
			$('#po_received_date').prop('required', false);
		}
	}
</script>

<div class="right_col" role="main">
    <form id="frmLead">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="comp_id" id="comp_id" value="<?php echo @$dept_id; ?>">
                    <input type="text" class="form-control" style="display: none;" name="visit_type" value="Fresh" disabled="disabled" />
                    <select style="display: none;" class="form-control required" required="" name="executive">
                        <option><?php echo $user_id; ?></option>
                    </select>
                    
                    <div class="col-md-6" data-toggle="tooltip" data-placement="left" title="Please Enter Customer Name" >
                        <label>Customer / Organization Name</label>
                        <input type="text" placeholder="Enter Customer Name" name="client_name" onkeypress="return isAlpha(event)" value="<?php echo @$client_name;?>" class="form-control required" required="" />
                    </div>

                    <div class="col-md-3 col-sm-12 col-xs-12" >
                        <label>Customer Business</label>
                        <input type="text" name="client_business" placeholder="Enter Business" onkeypress="return isAlpha(event)" value="<?php echo @$client_business;?>" class="form-control" />
                    </div>
                    <div class="col-md-3 col-sm-12 col-xs-12" >
                        <label>Tax Id Number</label>
                        <input type="text" name="client_gst_number" placeholder="Enter Tax id Number"  value="" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label>Office Address</label>
                        <textarea class="form-control" name="client_add" placeholder="Office Address" rows="4"><?php echo @$client_add;?></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-6">
                            <label>Country</label>                                    
                                <select class="form-control required" required="" id='ddlCountry' name="ddlCountry" onchange="LoadState();">
                                   
                            </select> 
                        </div>
                        
                        <div class="col-md-6">
                            <label>State</label>                                    
                            <select class="form-control required" required="" id='ddlState' name="ddlState" onchange="LoadCity();">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>City</label>                                    
                            <select class="form-control " id='office_city_id' name="office_city_id" onchange="LoadArea();">
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Area</label>
                            <select class="form-control" id='office_area_id' name="office_area_id">
                            </select>
                        </div>
                    </div>   
                    <div class="col-md-3" data-toggle="tooltip" data-placement="left" title="Please Enter Customer Mobile/Telephone">
                        <label>Customer Mobile</label>
                        <input type="text" id="client_mob" name="client_mob" placeholder="Enter Customer Mobile" onkeypress="return isNumber(event)" value="<?php echo @$client_mob;?>" class="form-control required" required />
                    </div>                            
                    <div class="col-md-3" >
                        <label>Post Code / Zip Code</label>
                        <input type="text" class="form-control no_space Number" maxlength="10" minlength="6" placeholder="Enter PIN Code" name="office_pincode" value="<?php echo @$office_pincode; ?>" />
                    </div>
                    <div class="col-md-6" data-toggle="tooltip" data-placement="left" title="Please Enter Customer Email">
                        <label>Customer Email</label>
                        <input type="email" name="client_email" placeholder="Enter Customer Email ID" value="<?php echo @$client_email;?>" class="form-control no_space" />
                    </div>
                    <div class="col-md-4 mt-2">
                        <label for="product_id" class="form-label">Interested Products</label>
                        <select id="product_id" name="product_id[]" class="form-control js-example-basic-multiple" multiple="multiple" required>
                            
                        </select>
                    </div>
                    <div class="col-md-3 mt-2" data-toggle="tooltip" data-placement="left" title="Please Select Customer Status">
                        <label>Customer Status</label>
                        <select onchange="OnSelectionChange1(this)" id="client_status" class="form-control required" required="" name="client_status">
                            <option value="">Select Status</option>
                            <option value="Hot">Hot</option>
                            <option value="Warm">Warm</option>
                            <option value="Cold">Cold</option>
                        <!-- 	<option value="Raw">Raw</option>
                            < -->option value="Not Interested">Not Interested</option>
                        </select>    
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="Follow_up_Date" class="form-label">
                            <i class="fas fa-user-tag"></i> Follow Up Date:
                        </label>
                        <input type="date" id="fromdate" name="fromdate" class="form-control" required>
                    </div>
                    <div class="col-md-2 mt-2">
                        <label for="followup_time" class="form-label">
                            <i class="fas fa-user-tag"></i> Follow Up Time:
                        </label>
                        <input type="time" id="followup_time" name="followup_time" class="form-control" required>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12" >
                        <label>Kind Attention</label>
                        <input type="text" class="form-control required" placeholder="Enter Kind Attention" value="" id="kind_attention" name="kind_attention" required>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="left" title="Please Enter Customer Requirement Details">
                        <label>Customer Requirement Details</label>
                        <input type="text" class="form-control " name="client_req" placeholder="Customer Requirement Details" value="<?php echo @$client_req;?>" >
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="right" title="Please Enter Customer's Feedback">
                        <label>Feedback Details</label>
                        <textarea class="form-control" name="client_feedback" id='client_feedback' placeholder="Feedback Details" rows="3"><?php echo @$client_feedback;?></textarea>
                    </div>
                </div>
                <div style="text-align: center;" class="col-md-12">
                    <br>
                    <button type="submit" name="submit" class="btn btn-round btn-primary"><i class="fa fa-save"></i> Submit</button>
                    <a href="" class="btn btn-round btn-success"><i class="fa fa-refresh"></i> Reset</a>
                    <a href="Lead-Master.php" class="btn btn-round btn-secondary"><i class="fa fa-chevron-left"></i> Back</a>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include 'production/footer.php';?>
<script>
 $(document).ready(function(){
      LoadCountry();
      LoadProduct();
      $('.js-example-basic-multiple').select2({
         placeholder:"Select Products"
      });

      $("#frmLead").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            var form = event.target;
            formData.append("Flag", "SaveLeadDetails");
            $.ajax({
                url: "operation/LeadOperation.php", 
                type: "POST", // Type of the request
                data: formData, // The form data to send
                processData: false, // Prevent jQuery from automatically converting the data to a query string
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                   if(response=="Inserted")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Client Added Successfully',
                            'success'
                        );
                        setTimeout(() => {
                           window.location.href="client_list.php";
                        }, 2000);
                   }
                   else if(response=="Updated")
                   {
                        Swal.fire(
                            'Well Done!',
                            'Client Details Updated Successfully',
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
                },
            });
      });

   });
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
   function LoadProduct()
   {
      $.post("../component.php",{
         Flag:"LoadProduct"
      },function(data,success){
         $("#product_id").html(data);
      });   
   }
</script>