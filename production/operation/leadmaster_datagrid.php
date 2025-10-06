<?php
session_start();
$dept_id = $_SESSION['dept_id'];
$branch_id = $_SESSION['branch_id'];
$user_type_id = $_SESSION['user_type_id'];
$user_id = $_SESSION['user_id'];
include '../../Configuration.php';
$lead_summary = @$_POST['lead_summary'];
$LeadType = @$_POST['LeadType'];
$rem_count = 0;

$requestData = $_REQUEST;

if($lead_summary)
{
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

if($lead_summary){
	$sql = "SELECT * FROM leadsummery_salesco_dashboard WHERE branch_id='$branch_id' AND client_status NOT IN('Delete') and LeadType='$LeadType'";
}
// echo $sql;
$query=mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;

if($lead_summary)
{
	if( !empty($requestData['search']['value']) )
	{
		$sql.=" AND ( client_id LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_status LIKE '".$requestData['search']['value']."%' ";    
		$sql.=" OR client_business LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_add LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR work_address LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_email LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_mob LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR client_status LIKE '".$requestData['search']['value']."%' ";
		$sql.=" OR user_id LIKE '".$requestData['search']['value']."%' )";
	}
}
if($user_type_id!="1")
{
	$sql.=" and user_id='$user_id'";
}
$sql.= " GROUP BY client_id ";

$totalFiltered = mysqli_num_rows($query);

if($lead_summary)
{
	if($requestData['length']=='-1')
	{
		$sql11 = "SELECT max(client_id) FROM `leadsummery_salesco_dashboard`";
		$query = mysqli_query($connect,$sql11);
		$row11 = $query->fetch_row();
		$requestData['length'] = $row11[0];
	}
}

if($lead_summary)
{
	if(!isset($requestData['start']))
	{
		$sql.=" ORDER BY " .$columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT 10 ";
	}
	else
	{
		$sql.=" ORDER BY client_id DESC LIMIT ".$requestData['start']." ,".$requestData['length']." ";
	}
}
// echo $sql;
$query = mysqli_query($connect, $sql) or die("fees-grid-data.php: get employees");
$data = array();
$i= $requestData['start']+1;

while( $row=mysqli_fetch_array($query) )
{
	$nestedData=array(); 
	if($lead_summary)
	{
		$client_id = $row['client_id'];
		$requirement_id = $row['requirement_id'];
		$rstnew = mysqli_query($connect,"SELECT p.* FROM `product_master` p inner join client_product_details cp on cp.product_id= p.product_id WHERE cp.client_id='$client_id' and cp.requirement_id='$requirement_id'");
		$product_name="";
		while($rwnew = mysqli_fetch_assoc($rstnew))
		{
			$product_name .= $rwnew['product_name'].",<br>";
		}
		$nestedData[] = $row["client_name"];
		$nestedData[] = $row["client_business"];
		$nestedData[] = $product_name;
		$nestedData[] = $row["client_mob"];
		$nestedData[] = '<div class="address-div1">'.$row["client_add"].'</div>';
		$nestedData[] = $row["client_email"];
		$client_status = $row['client_status'];
		
		//<a title='Add Requirement' onclick='add_requirements(".$row['client_id'].")' class='btn btn-round btn-sm btn-primary'><i class='fa fa-plus'></i></a>
		// $add_req = "<a title='View Requirement' onclick='view_client(".$row['client_id'].")' class='btn btn-round btn-sm btn-secondary' ><i class='fa fa-eye'></i></a> ";
		if($LeadType=="Lead")
		{
			$RedirectLink= "Edit-Lead-Master.php";
		}
		else{
			$RedirectLink= "Edit-Supplier-Master.php";
		}
		$nestedData[] = "<a data-toggle='tooltip' title='Edit' href='$RedirectLink?client_id=".base64_encode($client_id)."'><button type='submit' name='edit' class='btn btn-warning btn-sm btn-round' ><i class='fa fa-pencil'></i></button></a> &emsp;<a href='javascript:delete_client(".$row['client_id'].")' data-toggle='tooltip' title='Delete'><button type='submit' name='delete' class='btn btn-danger btn-sm btn-round' ><i class='fa fa-remove'></i></button></a>";
		
		$nestedData[] = '~'.$row['enq_type'].'_';
	}
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