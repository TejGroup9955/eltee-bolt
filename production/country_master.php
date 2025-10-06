<?php
	$page_heading="Country Master";
	include '../configuration.php';
	include 'header.php';
	
	if(isset($_GET['act'])) {
		if($_GET['act'] == "del") {
			if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id'])) {
				$id = mysqli_real_escape_string($connect, $_GET['id']);
				$status = mysqli_real_escape_string($connect, $_GET['status']);
				if($status=="1")
				{
					$UpdateStatus = 0;
				}else{ $UpdateStatus=1; }
				
				$qry = "update country_master set status='$UpdateStatus' WHERE id='$id'";
				$rs = mysqli_query($connect, $qry);
				
				if($rs) {
					$alert ='<div class="alert alert-success alert-dismissable center">
					  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
					  Country Status Updated Sucessfully...
					</div>';
				}
			}
		}
		
		if($_GET['act'] == "edit") {
			if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id'])) {
				$id = mysqli_real_escape_string($connect, $_GET['id']);
				$qry = "SELECT * FROM country_master WHERE id='$id'";
				$rs = mysqli_query($connect, $qry);
				
				if(mysqli_num_rows($rs) > 0) {
					$arr = mysqli_fetch_array($rs);
				}
				
				if(isset($_POST['submit_value'])) {
					$countryName = mysqli_real_escape_string($connect, $_POST['txtCountryName']);
					$currency_code = mysqli_real_escape_string($connect, $_POST['txtCurrencycode']);
					
					$qry_exists = "SELECT * FROM country_master WHERE LOWER(countryName)='".strtolower($countryName)."' AND id!='$id'";
					$rs_exists = mysqli_query($connect, $qry_exists);
					
					if(mysqli_num_rows($rs_exists) > 0) {
						$alert ='<div class="alert alert-danger alert-dismissable center">
								  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
								  Sorry! Duplicate value...
								</div>';
					} else {
						$qry = "UPDATE country_master SET countryName='$countryName', currency_code='$currency_code' WHERE id='$id'";
						$rs = mysqli_query($connect, $qry);
						
						echo '<script>window.location.href = "country_master.php?m=2";</script>';
						exit;
					}
				}
			}
		}
		 // $_GET['act'] == "del"
	} else {
		if(isset($_POST['submit_value'])) {
			$countryName = mysqli_real_escape_string($connect, $_POST['txtCountryName']);
			$currency_code = mysqli_real_escape_string($connect, $_POST['txtCurrencycode']);
			
			$qry_exists = "SELECT * FROM country_master WHERE LOWER(countryName)='".strtolower($countryName)."'";
			$rs_exists = mysqli_query($connect, $qry_exists);
			
			if(mysqli_num_rows($rs_exists) > 0) {
				$alert ='<div class="alert alert-danger alert-dismissable center">
						  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
						  Sorry! Duplicate value...
						</div>';
			} else {
				$qry = "INSERT INTO country_master SET countryName='$countryName', currency_code='$currency_code'";
				$rs = mysqli_query($connect, $qry);
				
				if($rs) {
					$alert ='<div class="alert alert-success alert-dismissable center">
					  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
					  Country Added Sucessfully...
					</div>';
				}
			}
		}
	}
	if(isset($_GET['m'])) {
		if($_GET['m'] == "2") {
			$alert ='<div class="alert alert-success alert-dismissable center">
			  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
			  Country Added Sucessfully...
			</div>';
		}	
	}
?>
	<div class="right_col" role="main">
  		<form method="POST">
    	<div class="row">
			<div class="col-md-12">
        		<?PHP if(isset($alert)) { echo $alert; } ?>
        	</div>
            <div class="col-md-4">                
                <div class="tile-stats">
                	<div class="row">
						<div class="col-md-12 mt-3">
							<div class="form-group">
								<div class="col-md-12 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="right" title="Please Enter Country Nam">
									<label>Enter Country Name</label>
									<input type="text" name="txtCountryName" placeholder="Enter Country Name" 
										value="<?PHP if(!empty($arr)) { echo $arr['countryName']; } ?>" class="form-control" required />
								</div>
							</div>
						</div>
						<div class="col-md-12 mt-3">
							<div class="form-group">
								<div class="col-md-12 col-sm-12 col-xs-12" data-toggle="tooltip" data-placement="right" title="Please Enter Currency">
									<label>Currency </label>
									<input type="text" name="txtCurrencycode" placeholder="Enter Currency" 
										value="<?PHP if(!empty($arr)) { echo $arr['currency_code']; } ?>" class="form-control" />
								</div>
							</div>
						</div>
				
						<div class="col-md-12 ml-3">
							<br> 
							<input type="hidden" value="1" name="submit_value">
							<button type="submit" name="btnSubmit" class="btn btn-primary btn-sm">
								<?PHP if(isset($_GET['act']) && $_GET['act'] == "edit") { ?>
									<i class="fa fa-plus"></i> Edit Country
								<?PHP } else { ?>
									<i class="fa fa-plus"></i> Add Country
								<?PHP } ?>
							</button>
							<a href="country_master.php" class="btn btn-success btn-sm"><i class="fa fa-files-o"></i> Reset</a>
						</div>
					</div>
        		</div>
      		</div>
            <div class="col-md-8">
        		<div class="tile-stats">
					<div class="row mt-3">
						<table id="example" class="table table-striped jambo_table bulk_action display dataTable no-footer">
							<thead>
								<tr>
									<th>Action</th>
									<th>Country Name</th>
									<th>Currency Code</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$cmd2 = "SELECT * FROM country_master ORDER BY countryName";
									$result2 = $connect->query($cmd2);
									
									if ($result2->num_rows > 0) {
										while($row2 = $result2->fetch_assoc()) {
											if($row2['status'] =="1")
											{
												$deleteBtn = "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"delete_c(".$row2["id"].",".$row2["status"].")\">
													Deactivate
												</button>";
											}
											else{
												$deleteBtn = "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"delete_c(".$row2["id"].",".$row2["status"].")\">
													Activate
												</button>";
											}
										echo "
										<tr>
											<td>
												<a data-toggle='tooltip' title='Edit Country' class='btn btn-warning btn-sm btn-round' href='country_master.php?act=edit&id=".$row2['id']."'>
													<i class='fa fa-pencil'></i>
												</a>
												".$deleteBtn."
											</td>
											<td>".$row2['countryName']."</td>
											<td>".$row2['currency_code']."</td>
										</tr>
										";
										}
									}
								?>
							</tbody>
						</table>
					</div>
      			</div>
    		</div>
  		</div>
		</form>
	</div>

<?php include_once('footer.php'); ?>
<script type="text/javascript">
	$(document).ready(function () {
		$("#example").DataTable({ });
		window.setTimeout(function() {
			$(".alert-danger").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).remove(); 
			});
		}, 2500);
	});

  	$(document).ready(function () {
		window.setTimeout(function() {
			$(".alert-success").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).remove();
				window.location.href='country_master.php';
			});
		}, 300);
	});
  
	function delete_c(id, status) {
		var r = confirm("Do you really want to change status of this Country?");
		
		if (r == true) {
			window.location.href='country_master.php?act=del&id='+id+'&status='+status;
		}
	}
</script>