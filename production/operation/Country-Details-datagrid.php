<?php
	include '../../Configuration.php';

	$quotation_followup = @$_POST['quotation_followup'];
	$CountryId = @$_POST['CountryId'];
	$requestData = $_REQUEST;

	if($quotation_followup){
		$columns = array( 
			0 => 'e.'.'emp_id',
			1 => 'e.'.'emp_id', 
			2 => 'e.'.'emp_id',
			3 => 'e.'.'emp_id',
			4 => 'e.'.'emp_id',
			5 => 'e.'.'emp_id',
		);
	} 

	if($quotation_followup){
		// office_country_id='101' 
		$sql = "SELECT * from client_master where client_status!='Delete' and LeadType='Lead'";

	}

	$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
	$totalData = mysqli_num_rows($query);
	$totalFiltered = $totalData;

	if($quotation_followup)
	{
		if( !empty($requestData['search']['value']) )
		{

			$sql.=" AND ( client_id LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR client_mob LIKE '".$requestData['search']['value']."%' )";
		}
	} 
	
	$totalFiltered = mysqli_num_rows($query);

	if($quotation_followup){
		if($requestData['length']=='-1'){
			$sql11 = "SELECT max(emp_id) FROM `employee_master`";
			$query = mysqli_query($connect,$sql11);
			$row11 = $query->fetch_row();
		    $requestData['length'] = $row11[0];
		}
	}
	 
	if($quotation_followup){
		$sql.="ORDER BY client_id desc LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
	}
	
	$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
	$data = array();
	$i= $requestData['start']+1;
	$SrNo = 1;
	while( $row=mysqli_fetch_array($query) )
	{
		$nestedData=array(); 
		if($quotation_followup)
		{		
			$client_id=$row['client_id'];
			$rstnew = mysqli_query($connect,"SELECT p.* FROM `product_master` p inner join client_product_details cp on cp.product_id= p.product_id WHERE cp.client_id='$client_id'");
			$product_name="";
			while($rwnew = mysqli_fetch_assoc($rstnew))
			{
				$product_name .= $rwnew['product_name'].",<br>";
			}
			$nestedData[] = $row["client_name"];	
			$nestedData[] = $row["client_mob"];
		
			$nestedData[] = $product_name;
			
		}
		$SrNo++;
		$data[] = $nestedData;
	}

	$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
				);

	echo json_encode($json_data);

?>