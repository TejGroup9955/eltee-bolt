	<?php
	/* Database connection start */
	include '../configuration.php';

	extract($_POST);
	$lead_summary = @$_POST['lead_summary'];
	$rem_count = 0;
	
	// storing  request (ie, get/post) global array to a variable  
	$requestData = $_REQUEST;

	if($lead_summary){

		$columns = array( 
			0 => 's.id',
			1 => 's.stateName'
		);
	} 
	$ydate = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-1,date("Y")));

	if($lead_summary){

		$sql = "select s.*,c.countryName from state_master s 
		left join country_master c on c.id=s.countryID
		where 1=1";
	}
	$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
	$totalData = mysqli_num_rows($query);
	$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

	$query=mysqli_query($connect, $sql) or die("Index.php: get employees");

	if($lead_summary){
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND (s.stateName LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR c.countryName LIKE '".$requestData['search']['value']."%' )";
		}
	} 

	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.

	if($lead_summary){
		if($requestData['length']=='-1'){
			$sql11 = "SELECT max(id) FROM `state_master`";
			$query = mysqli_query($connect,$sql11);
			$row11 = $query->fetch_row();
		    $requestData['length'] = $row11[0];
		}
	}

	if($lead_summary){
		if(!isset($requestData['start']))
		{
			$sql.=" ORDER BY " .$columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT 10   ";  // adding length
		}
		else
		{
			$sql.=" ORDER BY s.id DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
		}
	}
	$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");

	$data = array();

	$i= $requestData['start']+1;

	while( $row=mysqli_fetch_array($query) ) {  // preparing an array

		$nestedData=array(); 
		
		if($lead_summary){			
			if($row['status'] =="1")
			{
				$deleteBtn = "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"delete_c(".$row["id"].",".$row["status"].")\">
					Deactivate
				</button>";
			}
			else{
				$deleteBtn = "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"delete_c(".$row2["id"].",".$row2["status"].")\">
					Activate
				</button>";
			}

			$nestedData[] = " <a data-toggle='tooltip' title='Edit State' class='btn btn-warning btn-sm btn-round' href='State-Master.php?state_id=".base64_encode($row['id'])."'> <i class='fa fa-pencil'></i></a>
                  <button ".@$disabled." class='btn btn-danger btn-sm btn-round' data-toggle='tooltip' title='Delete State'  onclick='delete_c(".$row["id"].")'><i class='fa fa-remove'></i></button>";
			$nestedData[] = $row["stateName"];
			$nestedData[] = $row["countryName"];
			

		}
		$data[] = $nestedData;
	}

	$json_data = array(
				"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
				"recordsTotal"    => intval( $totalData ),  // total number of records
				"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
				"data"            => $data   // total data array
				);

	echo json_encode($json_data);  // send data as json format

?>