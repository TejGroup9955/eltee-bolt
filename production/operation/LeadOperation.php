<?php
include_once("../../configuration.php");
session_start();
$user_id = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$dept_id = @$_SESSION['dept_id'];
$branch_id = @$_SESSION['branch_id'];
$UserNameSession = @$_SESSION['user_name'];
$Flag = $_POST['Flag'];
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect);
}
extract($_POST);

if($Flag=="SaveLeadDetails")
{
    if($client_status == "Not Interested") { 
        $nextdate = date('Y-m-d');
        $followup_time = date('H:i:s');
    } else if($client_status == "Raw") { 
        $nextdate = date('Y-m-d');
        $followup_time = date('H:i:s');
    } else {
        $nextdate=$_POST['fromdate'];
    }
    if(!isset($office_area_id ))
    {
        $office_area_id =0;
    }
    $dt=date("Y-m-d");  
    if (!isset($_POST['product_id']) || empty($_POST['product_id']))
    {
        echo "Please select Atleast One Product";       
    }
    else if($nextdate=="") {
       echo "Please select Next Followup Date.";
    } 
    else {
        $cmd2 = "SELECT client_mob FROM client_master WHERE dept_id=$dept_id AND client_mob='$client_mob'  ";
        $result2 = $connect->query($cmd2);
        if ($result2->num_rows > 0) {
            echo "Customer mobile Number is already registred...";
        }
        else
        {
            $client_name = addslashes($client_name);
            $firm_name = addslashes($client_name);
            $client_req = addslashes($client_req);
            $client_add1 = addslashes($client_add);
            $client_add = str_replace(array('\n','\r','\v','\t','\e','\f','\\','\$','\"'),'',$client_add1);
        
            $office_country_id = mysqli_real_escape_string($connect, $_POST['ddlCountry']);

            $cmd = "INSERT INTO client_master(client_name, client_business, client_add, client_email,
             client_mob, client_required_details, user_id, client_status, branch_id, 
             dept_id, office_city_id, office_area_id, office_pincode, 
             office_country_id, GST_no, city, state, office_state_id, kind_attention,LeadType) 
             values('" .$client_name. "','" .$client_business. "','" .$client_add. "',
            '" .$client_email. "','" .$client_mob. "',
            '" .$client_req."',
            '" .$executive. "','" .$client_status. "',
            '".$branch_id."','".$dept_id."', '$office_city_id', '$office_area_id',
            '$office_pincode', '$office_country_id',
            '$client_gst_number', '".$office_city_id."','".$_POST['ddlState']."',
            '".$_POST['ddlState']."', '$kind_attention','$LeadType')";	
            // print_r($cmd);exit;
            $query_res1 = $connect->query($cmd) or die($cmd);
            if($query_res1 > 0) {
                $client_id_new = mysqli_insert_id($connect);
                
                $cmd_requirement = "INSERT INTO client_requirement_details (requirement_details, 
                client_status, client_id, user_id, visit_id) VALUES('$client_req', 
                '$client_status', '$client_id_new', '$executive','0') ";
                $query_res3 = mysqli_query($connect,$cmd_requirement);
        
                if($query_res3 > 0) {
                    $requirement_id = mysqli_insert_id($connect);
                    $i = 0;
                    if(isset($contact_person_name)) {
                        foreach ($contact_person_name as $contact_person_name1) {
                            if($contact_person_name1 != "") {
                                $cmd_contact = "INSERT INTO client_requirement_contact_master (client_id,
                                 requirement_id, contact_person_name, contact_person_mobile, designation,
                                  contact_person_email) VALUES ('$client_id_new', '$requirement_id',
                                   '$contact_person_name1', '$contact_person_mobile[$i]', 
                                   '$designation[$i]', '$contact_person_email[$i]') ";
                                $query_res2 = mysqli_query($connect,$cmd_contact);
                                    
                                if($query_res2 == 0) {
                                    echo $error_message;
                                }
                            }
                            $i++;
                        }
                    } else {
                        $query_res2 = 1;
                    }
            
                    $cmd1 = "INSERT INTO user_visit_record (visit_date,visit_type,client_id,user_id,
                    client_feedback,next_folloup_date, visit_time, followup_time,
                     requirement_id, status) VALUES('" .$dt. "','Fresh','" .$client_id_new. "',
                     '" .$user_id. "','" .$client_feedback. "','" .$nextdate. "',
                      '".date('H:i:s')."', '$followup_time', '$requirement_id', '$client_status')";
                    if($connect->query($cmd1) === TRUE)
                    {
                        $sql_load = "SELECT visit_id FROM user_visit_record WHERE client_id = '$client_id_new' ORDER BY visit_id DESC LIMIT 1 ";
                        $res_load = $connect->query($sql_load);
                        if ($res_load->num_rows > 0)
                        {
                            while($row_load = $res_load->fetch_assoc())
                            {
                                $new_visit_id = $row_load['visit_id'];
                            }
                        }

                        if(isset($_FILES['visiting_card']))
                        {
                            $errors= array();
                            $file_name = $_FILES['visiting_card']['name'];
                            $file_size =$_FILES['visiting_card']['size'];
                            $file_tmp =$_FILES['visiting_card']['tmp_name'];
                            $file_type=$_FILES['visiting_card']['type'];
                            $file_ext=end((explode('.', $file_name)));

                            $expensions= array("jpeg","jpg","png");

                            if($file_size > 209710052)
                            {
                                $errors[]='File size must be excately 2 MB';
                            }
                            $file_name = $client_id_new.".".$file_ext;
                            if(empty($errors)==true && $file_name != "")
                            {
                                if(move_uploaded_file($file_tmp,"../../images/visiting_card/".$file_name))
                                {
                                    $sql1 = "UPDATE client_master SET visiting_card = '$file_name' WHERE client_id = '$client_id_new' "; 
                                    $query_res4 = $connect->query($sql1);
                                    if($query_res4 == 0)
                                    {
                                        echo $error_message;
                                    }
                                }
                                else
                                {
                                    $query_res4 = 1;
                                }              
                            }
                            else
                            {
                                $query_res4 = 1;
                            }
                        }
                        else
                        {
                            $query_res4 = 1;
                        }
            
                        $cmd_load = "UPDATE client_master SET visit_id = '$new_visit_id' WHERE client_id = '".$client_id_new."' ";
                        $query_res5 = mysqli_query($connect,$cmd_load);

                        $cmd_visit_id = "UPDATE client_requirement_details SET visit_id = '$new_visit_id' WHERE requirement_id = '$requirement_id' AND client_id = '".$client_id_new."' ";
                        $query_res6 = mysqli_query($connect,$cmd_visit_id);

                        if(isset($product_id))
                        {
                            foreach ($product_id as $product_id_selected)
                            {
                                if($product_id_selected != "")
                                {
                                    $cmd_requirement = "INSERT INTO client_product_details (client_id, requirement_id, product_id) VALUES('$client_id_new', '$requirement_id', '$product_id_selected') ";
                                    
                                    $query_res7 = mysqli_query($connect,$cmd_requirement);
                                    if($query_res7 == 0)
                                    {
                                        echo $error_message;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $query_res7 = 1;
                        }
                        
                        if($query_res4 > 0 && $query_res5 > 0 && $query_res6 > 0 && $query_res7 > 0)
                        {
                            mysqli_commit($connect);
                            echo "Inserted";
                        }
                        else { echo $error_message; }
                    }
                    else { echo $error_message; }
                }
                else { echo $error_message; }
            }
            else { echo $error_message; }
        }
    }
}

if($Flag=="UpdateLeadDetails")
{
    // mysqli_autocommit($connect,FALSE);
    $cmd2 = "SELECT client_mob FROM client_master WHERE dept_id=$dept_id AND client_mob='$client_mob' and client_id!='$client_id' ";
    $result2 = $connect->query($cmd2);
    if ($result2->num_rows > 0) {
        echo "Customer mobile Number is already registred...";
    }
    else
    {   
        $newadd = $client_add;
        $client_add = str_replace(array('\n','\r','\v','\t','\e','\f','\\','\$','\"'),'',$newadd);
        $que = "UPDATE client_master SET 
            client_name='".$client_name."', client_business='".$client_business."', 
            client_add ='" .$client_add. "',client_email='" .$client_email. "', 
            client_mob='" .$client_mob. "', 
            office_country_id='".$ddlCountry."',	
            GST_no = '$client_gst_number',
            kind_attention = '$kind_attention',
            office_state_id='".$ddlState."',
            office_city_id = '$office_city_id', 
            office_area_id = '$office_area_id', 
            office_pincode = '$office_pincode', 
            state ='".$ddlState."'
            WHERE client_id='".$client_id."'";
            // echo $que;
        $query_res1 = mysqli_query($connect,$que);
        if($query_res1 > 0) {
            // $rstreq = mysqli_query($connect,"update client_requirement_details set 
            // requirement_details='$client_req' where client_id='".$client_id."'
            // and requirement_id='$requirement_id'");
            $i=0;
            if(isset($contact_person_name)) {
                $rstdelete = mysqli_query($connect,"delete from client_requirement_contact_master where client_id= '$client_id'");
                foreach ($contact_person_name as $contact_person_name1) {
                    if($contact_person_name1 != "") {
                        $cmd_contact = "INSERT INTO client_requirement_contact_master (client_id,
                         requirement_id, contact_person_name, contact_person_mobile, designation,
                          contact_person_email) VALUES ('$client_id', '$requirement_id',
                           '$contact_person_name1', '$contact_person_mobile[$i]', 
                           '$designation[$i]', '$contact_person_email[$i]') ";
                        $query_res2 = mysqli_query($connect,$cmd_contact);
                            
                        if($query_res2 == 0) {
                            echo $error_message;
                        }
                    }
                    $i++;
                }
            } else {
                $query_res2 = 1;
            }

            if(isset($product_id))
            {
                $rstdelete = mysqli_query($connect,"delete from client_product_details where client_id= '$client_id'");
                foreach ($product_id as $product_id_selected)
                {
                    if($product_id_selected != "")
                    {
                        $cmd_requirement = "INSERT INTO client_product_details (client_id, requirement_id, product_id) VALUES('$client_id', '$requirement_id', '$product_id_selected') ";
                        
                        $query_res7 = mysqli_query($connect,$cmd_requirement);
                        if($query_res7 == 0)
                        {
                            echo $error_message;
                        }
                    }
                }
            }
            else
            {
                $query_res7 = 1;
            }
            echo "Updated";         
        } else { 
            echo "Unable To Update client Details";
        }
    }
}
if($Flag=="ShowClientList")
{
    echo ' <table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Client name</th>
                    <th>Products</th>
                    <th>Business</th>
                    <th>Mobile No</th>
                    <th>Office Adrress</th>
                    <th>Email Id</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>';
        $rstclient = mysqli_query($connect,"select * from client_master where client_status IN ('Hot','Warm','Cold')");
        $srno =1;
        while($rwclient = mysqli_fetch_assoc($rstclient))
        {
            extract($rwclient);
            $rstpro = mysqli_query($connect,"select p.product_name from client_product_details c
            inner join product_master p on p.product_id = c.product_id 
            where c.client_id='$client_id'");
            $prodetail ='';
            if(mysqli_num_rows($rstpro)>0)
            {   
                $count =1;
                while($rwpro = mysqli_fetch_assoc($rstpro))
                {
                    extract($rwpro);
                    $prodetail.=$count.'. '.$product_name."<br>";
                    $count++;
                }
            }
            if($client_status=="Hot")
            {
                $client_btn = '<button type="button" class="btn btn-round btn-sm btn-danger">'.$client_status.'</button>';
            }
            else if($client_status=="Warm")
            {
                $client_btn = '<button type="button" class="btn btn-round btn-sm btn-info">'.$client_status.'</button>';
            }
            else if($client_status=="Cold")
            {
                $client_btn = '<button type="button" class="btn btn-round btn-sm btn-warning">'.$client_status.'</button>';
            }
            else{
                $client_btn = $client_status;
            }
            echo '<tr>
            <td>'.$srno.'</td>
            <td>'.$client_name.'</td>
            <td>'.$prodetail.'</td>
            <td>'.$client_business.'</td>
            <td>'.$client_mob.'</td>
            <td>'.$client_add.'</td>
            <td>'.$client_email.'</td>
            <td>'.$client_btn.'</td>
            </tr>';
            $srno++;
        }        
    echo '</tbody>
    </table>';
}
?>