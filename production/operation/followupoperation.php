<?php
include_once("../../configuration.php");
$Flag = $_POST['Flag'];
session_start();
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$dept_id = @$_SESSION['dept_id'];
$branch_id = @$_SESSION['branch_id'];
$UserNameSession = @$_SESSION['user_name'];
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect);
}
extract($_POST);

if($Flag=="LoadFollowupDetails")
{
    echo '<table class="table table-hover table-bordered" id="tblFollowUpShort">
            <thead class="table-dark" width="100%">
                <tr>
                    <th>Follow-Up Date</th>
                    <th>Next Follow-Up Date</th>
                    <th>Status</th>
                    <th>Remark</th>
                    <th>User Name</th>
                </tr>
            </thead>
        <tbody>';
    $rstfollow = mysqli_query($connect,"select f.*,um.user_name from user_visit_record f
    inner join user_master um on um.user_id =f.user_id
    inner join client_master c on c.client_id = f.client_id
    inner join client_requirement_details cr on cr.requirement_id=f.requirement_id
    where c.branch_id='$branch_id' and f.client_id='$client_id' and f.requirement_id='$requirement_id' order by visit_id DESC limit 5");
    if(mysqli_num_rows($rstfollow)>0)
    {
        $srno =1;
        while($rwfollow = mysqli_fetch_assoc($rstfollow))
        {
            extract($rwfollow);
            echo "<tr>
                <td>".date('d/m/Y h:i a',strtotime($visit_date))."</td>
                <td>".date('d/m/Y',strtotime($next_folloup_date))."<br>".date('h:i a',strtotime($followup_time))."</td>
                <td>$status</td>
                <td>$client_feedback</td>
                <td>$user_name</td>
            </tr>";
            $srno++;
        }
    }
    echo "</tbody></table>";
}
if($Flag=="LoadFollowupHistory")
{
    echo '<table class="table table-hover table-bordered" width="100%" id="tblFollowUpHistory">
            <thead class="table-dark">
                <tr>
                    <th>Sr No</th>
                    <th>Follow-Up Date</th>
                    <th>Next Follow-Up Date</th>
                    <th>Feedback</th>
                    <th>Visit Type</th>
                    <th>Status</th>
                    <th>User Name</th>
                </tr>
            </thead>
        <tbody>';
    $rstfollow = mysqli_query($connect,"select f.*,um.user_name from user_visit_record f
    inner join user_master um on um.user_id =f.user_id
    inner join client_master c on c.client_id = f.client_id
    inner join client_requirement_details cr on cr.requirement_id=f.requirement_id
    where c.branch_id='$branch_id' and f.client_id='$client_id' and f.requirement_id='$requirement_id' order by visit_id DESC");
    if(mysqli_num_rows($rstfollow)>0)
    {
        $srno =1;
        while($rwfollow = mysqli_fetch_assoc($rstfollow))
        {
            extract($rwfollow);
            echo "<tr>
                <td>$srno</td>
                <td>".date('d/m/Y h:i a',strtotime($visit_date))."</td>
                <td>".date('d/m/Y',strtotime($next_folloup_date))."<br>".date('h:i a',strtotime($followup_time))."</td>
                <td>$client_feedback</td>
                <td>$visit_type</td>
                <td>$status</td>
                <td>$user_name</td>
            </tr>";
            $srno++;
        }
    }
    echo "</tbody></table>";
}
if($Flag=="AddFollowupDetails")
{

    $rstfollowup = mysqli_query($connect,"INSERT INTO `user_visit_record`(`next_folloup_date`, `followup_time`, 
     `visit_type`,`client_feedback`, `status`, `user_id`,client_id,requirement_id,visit_date,visit_time)
     values('$Followupdate','$FollowupTime','$FollowUpCallStatus','$FollowupRemarl','$FollowupSttaus',
     '$user_id_session','$HiddenClientId','$HiddenFollowuprequirement_id','".date('Y-m-d')."','".date('H:i:s')."')");
    if($rstfollowup)
    {
        $visit_id = mysqli_insert_id($connect);
        $rstreq = mysqli_query($connect,"update client_requirement_details set 
        client_status='$FollowupSttaus',visit_id='$visit_id' where 
        requirement_id='$HiddenFollowuprequirement_id'");
        $rstreq = mysqli_query($connect,"update client_master set client_status='$FollowupSttaus',visit_id='$visit_id' where
        client_id='$HiddenClientId' ");
        echo "Added";
    }
    else
    {
        echo "Unable To Add Follow Up Details";
    }
}
?>