<?php
include_once('configuration.php');
session_start();
$user_id = $_SESSION['user_id'];
$branchid=@$_POST['branchid'];
if(isset($branchid))
{
    $sql = "SELECT branch_id, branch_name FROM branch_master WHERE branch_id='" .$branchid. "' " ;
    $result = $connect->query($sql);
    if ($result->num_rows > 0) {
        if($row = $result->fetch_assoc()) {
            $branch_id_new= $row["branch_id"];
            $branch_name_new= $row["branch_name"];
        }
    }
    $_SESSION['branch_id'] = $branch_id_new;
    $_SESSION['branch_name'] = $branch_name_new;


    $cmd="UPDATE user_master set branch_id = '$branchid' where user_id='$user_id'";
    $connect->query($cmd);
    $_SESSION['branch_id'] = $branchid;
}
?>