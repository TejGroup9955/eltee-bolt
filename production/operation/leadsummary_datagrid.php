<?php
	// include_once '../Check-User.php';
	include_once '../../configuration.php';
	$lead_summary = @$_POST['lead_summary'];
	$LeadType = @$_POST['LeadType'];
	$rem_count = 0;
	session_start();
	$dept_id = $_SESSION['dept_id'];
	$branch_id = $_SESSION['branch_id'];
	$user_id = $_SESSION['user_id'];
	$user_type_id = $_SESSION['user_type_id'];
	$requestData = $_REQUEST;
	$followup_type ="";
	// $filter_status = $_POST['filter_status'];
	
	if($lead_summary){
	
		$columns = array( 
			0 => 'client_id',
			1 => 'client_id',
			4 => 'client_name',
			5 => 'client_add',
			6 => 'client_mob',
			7 => 'client_id',
			8 => 'client_id',
			9 => 'visit_type',
			10 => 'client_id',	
			11 => 'client_status'
		);
	} 
	
	$ydate = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-1,date("Y")));
	$from_day_date = date('Y-m-d');
	$to_day_date = date('Y-m-d', strtotime($from_day_date. ' + 0 day'));

	if($lead_summary) {
		// $sql = "SELECT * FROM leadsummery_salesco_lead_summery WHERE dept_id = '$dept_id' AND branch_id = '$branch_id' AND client_status IN('Hot','Warm','Cold')";
		if($user_type_id != 1){
			$sql = "SELECT * FROM leadsummery_salesco_lead_summery WHERE user_id='$user_id' AND branch_id='$branch_id' AND client_status IN('Hot','Warm','Cold') ";
		}else{
			$sql = "SELECT * FROM leadsummery_salesco_lead_summery WHERE branch_id='$branch_id' AND client_status IN('Hot','Warm','Cold') ";
		}
	}

// echo $sql;
	if($lead_summary) {
		if( !empty($requestData['columns'][0]['search']['value']) ) {
			$enq_type = $requestData['columns'][0]['search']['value'];
			
			if($enq_type == 'Justdial') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			} elseif($enq_type == 'IndiaMart') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			} elseif($enq_type == 'mtmspune') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			} elseif($enq_type == 'testingmachine') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			} elseif($enq_type == 'genesissengineers') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			} elseif($enq_type == 'Manual') {
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			}
			else{
				$sql.=" AND ( enq_type = '".$enq_type."' ) ";
			}
		}
		if( !empty($requestData['columns'][9]['search']['value']) ) {
			$userid = $requestData['columns'][9]['search']['value'];
			$sql.=" AND ( user_id = '".$userid."' ) ";
		}
		if( !empty($requestData['columns'][1]['search']['value']) ) {
			$client_status = $requestData['columns'][1]['search']['value'];			
			$sql.=" AND ( client_status = '".$client_status."' ) ";			
		}
		if( !empty($requestData['columns'][2]['search']['value']) ) {
			$fromdate = $requestData['columns'][2]['search']['value'];
			$dd = substr($fromdate, 0,2);
			$mm = substr($fromdate, 3,2);
			$year = substr($fromdate, 6,4);
			$fromdate = $year."-".$mm."-".$dd; 
		}
		if( !empty($requestData['columns'][3]['search']['value']) ) {
			$todate = $requestData['columns'][3]['search']['value'];
			$dd = substr($todate, 0,2);
			$mm = substr($todate, 3,2);
			$year = substr($todate, 6,4);
			$todate = $year."-".$mm."-".$dd; 			
			if($fromdate!="YYYY-MM-DD" && $todate!="YYYY-MM-DD")
			{
				if(!empty($userid)) {
					$sql.=" AND  next_folloup_date Between '$fromdate' AND '$todate' AND user_id = '$userid'";
				} else {
					$sql.=" AND  next_folloup_date Between '$fromdate' AND '$todate'";
				}
			}
		}
	
	
		if( !empty($requestData['columns'][4]['search']['value']) ){
			$followup_type = $requestData['columns'][4]['search']['value'];
			$todate = date("Y-m-d");
			
			if($followup_type == "1") {
				if(!empty($userid)) {
					$sql.=" AND next_folloup_date='$todate'  AND user_id= $userid ";
				} else {
					$sql.=" AND next_folloup_date='$todate' ";
				}
			}
			
			if($followup_type == "3") {	
				if(!empty($userid)) {
					$sql.=" AND next_folloup_date < '$todate' AND user_id= $userid ";
				} else {
					$sql.=" AND next_folloup_date < '$todate'";	
				}
			}
	
			if($followup_type == "2") {
				if(!empty($userid)) {
					$sql.=" AND  next_folloup_date > '$todate'  AND user_id= $userid ";
				}
				else{
					$sql.=" AND  next_folloup_date > '$todate' ";			
				}
			}
	
			if($followup_type == "4") {	
				if(!empty($userid)) {
					$sql.=" AND user_id= $userid ";
				} else {
					$sql.=" ";	
				}
			}
		}
	
		// // if($filter_status != "all") {
		// // 	$sql.= " AND client_status = '$filter_status' ";
		// }
	}


if($lead_summary) {
	if( !empty($requestData['search']['value']) ) {
		$sql.=" AND ( client_id LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR firm_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_status LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_business LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_add LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR work_address LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_email LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_mob LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_person1 LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_person1_mob LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_person2 LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_person2_mob2 LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_status LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR requirement_details LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR user_id LIKE '".$requestData['search']['value']."%' )";
	}
}

$sql.= " and LeadType='$LeadType'";

$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;
$query=mysqli_query($connect, $sql) or die("Index.php: get employees");

$totalFiltered = mysqli_num_rows($query);

if($lead_summary) {
	if(!isset($requestData['start'])) {
		$sql.=" ORDER BY client_id DESC LIMIT 10 ";
	} else {
		$sql.=" ORDER BY client_id  DESC LIMIT ".$requestData['start']." ,".$requestData['length']." ";
	}
}
// echo $sql;die;
$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
$data = array();

$i= $requestData['start']+1;

while( $row=mysqli_fetch_array($query) ) {
	$nestedData=array();
	
	if($lead_summary) {
		$color = "#ffffff";

		$timestamp1 = strtotime($row["next_folloup_date"]);
		$print_next_follow_date = date('d-m-Y', $timestamp1);
		$next_folloup_date = $row["next_folloup_date"];

		$print_client_feedback = $row["client_feedback"];	

		$timestamp1 = strtotime($row["next_folloup_date"]);
		$print_next_follow_date = date('d-m-Y', $timestamp1);
		
		if(strtotime($row['next_folloup_date']) == strtotime(date('Y-m-d'))) {
			$followup_time = date('h:i A', strtotime($row['followup_time']));
			$print_next_follow_date = $followup_time;
		} else {
			$followup_time = date('h:i A', strtotime($row['followup_time']));
			$print_next_follow_date = $print_next_follow_date.' '.$followup_time;
		}

		$date2=date('Y-m-d');	        
		if(strtotime($next_folloup_date) < strtotime($date2)  ){
			$color = "#FFCCCC";
		}
		if(strtotime($next_folloup_date) > strtotime($date2)){
			$color = "#ccf7db";
		}
		if(strtotime($next_folloup_date) == strtotime($date2)){
			$color = "#FBFFCC";
		}

		$rstfollowupcount = mysqli_query($connect,"select count(uv.visit_id) as FollowupCount from user_visit_record uv
		inner join client_master c on c.client_id = uv.client_id
		inner join client_requirement_details cr on cr.requirement_id= uv.requirement_id
		where uv.client_id =".$row['client_id']." and uv.requirement_id=".$row['requirement_id']." 
		and c.LeadType='$LeadType'");
		$rwfollowupcount = mysqli_fetch_assoc($rstfollowupcount);
		//  <a class='btn btn-warning btn-sm btn-round' data-toggle='tooltip' title='Add Appointment'  href='Add-Appointment.php?client_id=".base64_encode($row["client_id"])."&requirement_id=".$row['requirement_id']."'><i class='fa fa-calendar-o'></i> </a>
		$nestedData[] = "
		<div class='center' style='display:flex;'><button class='btn btn-primary btn-sm btn-round' type='button' onclick='AddFollowup(".$row['client_id'].", ".$row['requirement_id'].",\"".$row["client_name"]."\");'><i class='mdi mdi-phone-plus'></i></button>&nbsp;
		</div>" ;
		
		// <a href='javascript:view_client_history(".$row['client_id'].", ".$row['requirement_id'].")' 
		// class='btn btn-dark btn-sm btn-round'><i class='fa fa-eye'></i></a>

		if($row["firm_name"]) {
			$nestedData[] = $row["firm_name"].'<hr>'.$row["client_name"];
		} else {
			$nestedData[] = $row["client_name"];
		}
		$nestedData[] = $row["client_business"];

		// $rstpro = mysqli_query($co)
		$client_id = $row['client_id'];
		$requirement_id = $row['requirement_id'];
		$rstnew = mysqli_query($connect,"SELECT p.* FROM `product_master` p inner join client_product_details cp on cp.product_id= p.product_id WHERE cp.client_id='$client_id' and cp.requirement_id='$requirement_id'");
		$product_name="";
		while($rwnew = mysqli_fetch_assoc($rstnew))
		{
			$product_name .= $rwnew['product_name'].",<br>";
		}

		$nestedData[] ='<div class="address-div">'. $product_name.'</div>';
		$nestedData[] = '<div class="address-div">'.$row["client_add"].'</div>';
		$nestedData[] = $row["client_mob"];
		$nestedData[] = $row["client_email"];

		$nestedData[] = $print_next_follow_date;
		$nestedData[] = $print_client_feedback."&nbsp;<button class='btn btn-success btn-sm btn-round' onclick='ShowFollowupHistory(".$row['client_id'].", ".$row['requirement_id'].",\"".$row["client_name"]."\");'>".$rwfollowupcount['FollowupCount']."</button>
		";
		$business_type1=$row['business_type'];
		$current_status1=$row['current_status'];
		$print_data = $business_type1."<br>".$current_status1;

		if(strtolower($row['client_status'])== 'hot') {
			$nestedData[] = "<button class='btn btn-round btn-danger btn-sm bg-hot'>Hot</button>";
		} else if(strtolower($row['client_status']) == 'warm') {
			$nestedData[] = "<button class='btn btn-round btn-warning btn-sm bg-warm'>Warm</button>";
		} else if(strtolower($row['client_status']) == 'cold') {
			$nestedData[] = "<button class='btn btn-round btn-info btn-sm bg-cold'>Cold</button>";
		} else if(strtolower($row['client_status']) == 'quotation') {
			$nestedData[] = "<button class='btn btn-round btn-primary btn-sm bg-quotation'>Quotation</button>";
		} else if(strtolower($row['client_status']) == 'requested') {
			$nestedData[] = "<button class='btn btn-round btn-dark btn-sm bg-requested'>Requested</button>";    	
		} else if(strtolower($row['client_status']) == 'given') {
			$nestedData[] = "<button class='btn btn-round btn-success btn-sm bg-quotation'>Given</button>";    	
		} else if(strtolower($row['client_status']) == 'revised') {
			$nestedData[] = "<button class='btn btn-round btn-danger btn-sm bg-quotation'>Revised</button>";
		}

		if($LeadType=="Lead")
		{
			$RedirectLink= "Edit-Lead-Master.php";
		}
		else{
			$RedirectLink= "Edit-Supplier-Master.php";
		}
		
		$nestedData[] = "<a data-toggle='tooltip' title='Edit' href='$RedirectLink?client_id=".base64_encode($client_id)."'><button type='submit' name='edit' class='btn btn-warning btn-sm btn-round' ><i class='fa fa-pencil'></i></button></a> &nbsp;<a href='javascript:delete_client(".$row['client_id'].")' data-toggle='tooltip' title='Delete'><button type='submit' name='delete' class='btn btn-danger btn-sm btn-round' ><i class='fa fa-remove'></i></button></a>"; 

		$nestedData[] = '~'.$row['enq_type'].'_';
		$nestedData[] = $color;
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