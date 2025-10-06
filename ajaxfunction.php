<script type="text/javascript">
    function add_more_contact_details()
	{
		i++;
		var id = i;

		var contact_person_name = $('#contact_person_name').val();
		var contact_person_mobile = $('#contact_person_mobile').val();
		var designation = $('#designation').val();
		var contact_person_email = $('#contact_person_email').val();

		var email_id_pattern = new RegExp("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$");
		var contact_person_mobile_pattern = new RegExp("[789][0-9]{9}");
		var mobile_length = contact_person_mobile.toString().length;
		if(contact_person_name=="")
		{
			alert('Please Add Contact Person Name');
			$('#contact_person_name').focus();
		}
		else if(contact_person_mobile=="")
		{
			alert('Please Add Mobile No');
			$('#contact_person_mobile').focus();
		}
		else if (contact_person_email != "" && !email_id_pattern.test(contact_person_email)) {
			alert('Please check Email Id');
			$('#contact_person_email').focus();
		} else {
			$('#contact_person_designation').append('<div class="contact_person_update col-md-12" id="contact_person_update_'+id+'" style="padding:3px;display:none;" ><label>Contact Person Name</label><input type="text" id="contact_person_name_'+id+'" name="contact_person_name[]" class="form-control required " placeholder="Contact Person Name" value="'+contact_person_name+'" /><label>Customer Mobile/Telephone</label><input type="text" id="contact_person_mobile_'+id+'" name="contact_person_mobile[]" value="'+contact_person_mobile+'" onkeypress="return isNumber(event)"  class="form-control required Number" placeholder="Customer Mobile/Telephone" /><label>Designation</label><input type="text" name="designation[]" id="designation_'+id+'" value="'+designation+'" class="form-control" placeholder="Designation" /><label>Contact Email </label><input type="email" name="contact_person_email[]" id="contact_person_email_'+id+'" class="form-control" placeholder="Contact Email" value="'+contact_person_email+'"  /> <div class="center mt-2 mb-1"><a onclick="update_contact('+id+')" class="btn btn-warning btn-round btn-sm" style="color:white">Update</a></div></div>');

			$('#contact_person').append('<tr id="contact_div_'+id+'"><td>'+contact_person_name+'</td><td>'+contact_person_mobile+'</td><td><div class="center" style="display:flex;padding:0;margin-right: 0px;"><a class="btn btn-sm btn-warning btn-round " onclick="update_contact_details('+id+')"><i class="fa fa-pencil"></i></a>&nbsp;<a class="btn btn-sm btn-danger btn-round" onclick="remove_contact_details('+id+')"><i class="fa fa-remove"></i></a></div></td></tr>'); 
			$('#contact_person_name').val('');
			$('#contact_person_mobile').val('');
			$('#designation').val('');
			$('#contact_person_email').val('');
			$('#contact_person_name').focus();
		}
	}

	function remove_contact_details(id)
	{
		var r = confirm("Are you sure to Delete this Contact?");
		if (r == true)
		{
			$('#contact_person_update_'+id).remove();
			$('#contact_div_'+id).remove();
			$('#contact_person_new').css('display', 'block');
			$('.contact_person_update').css('display', 'none'); 
		}
	}

	function update_contact_details(id)
	{
		$('#contact_person_new').css('display', 'none');
		$('.contact_person_update').css('display', 'block');                      
		$('#contact_person_update_'+id).css('display', 'block');
	}

	function update_contact(id)
	{
		var contact_person_name = $('#contact_person_name_'+id).val();
		var contact_person_mobile = $('#contact_person_mobile_'+id).val();
		var designation = $('#designation_'+id).val();
		var contact_person_email = $('#contact_person_email_'+id).val();

		var email_id_pattern = new RegExp("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$");
		var contact_person_mobile_pattern = new RegExp("[789][0-9]{9}");
		var mobile_length = contact_person_mobile.toString().length;
		if (contact_person_name == "")
		{
			alert('Please check Contact Name');
			$('#contact_person_name').focus();
		}
		// else if(!contact_person_mobile_pattern.test(contact_person_mobile))
		// {
		//     alert('Please check Customer Mobile/Telephone');
		//     $('#contact_person_mobile').focus();
		// }
		// else if (mobile_length != 10)
		// {
		//     alert('Please check Customer Mobile/Telephone');
		//     $('#contact_person_mobile').focus();
		// }
		// else if (designation == "")
		// {
		// 	alert('Please check Designation');
		// 	$('#designation').focus();
		// }
		else if (contact_person_email != "" && !email_id_pattern.test(contact_person_email))
		{
			alert('Please check Email Id');
			$('#contact_person_email').focus();
		}
		else
		{
			$('#contact_div_'+id).html('<td>'+contact_person_name+'</td><td>'+contact_person_mobile+'</td><td><div class="btn" style="display:flex;padding:0;margin-right: 0px;"><a class="btn btn-sm btn-warning btn-round" onclick="update_contact_details('+id+')"><i class="fa fa-pencil"></i></a>&nbsp;<a class="btn btn-sm btn-danger btn-round" onclick="remove_contact_details('+id+')"><i class="fa fa-remove"></i></a> </div></td>');
			$('#contact_person_new').css('display', 'block');
			$('#contact_person_update_'+id).css('display', 'none'); 

		}
	}

</script>