<?php
include_once("../../configuration.php");
$Flag = $_POST['Flag'];
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect);
}
extract($_POST);

if($Flag=="UpdateStatus")
{
    if($Status=="Active"){
        $UpdateStatus ="Deactive";
    }
    if($Status=="Deactive"){ $UpdateStatus="Active"; }
    $rstupdate = mysqli_query($connect,"update $TableName SET status='$UpdateStatus' where $CompareField='$CompareId'");
    if($rstupdate)
    {
        echo "Updated";
    }
    else
    {
        echo "Unable To Update";
    }
}
if($Flag=="UpdateFunction")
{
    $rstUpdate = mysqli_query($connect,"select * from $TableName where $CompareField='$CompareId'");
    $rwUpdate = mysqli_fetch_assoc($rstUpdate);
    echo json_encode($rwUpdate);
}

if($Flag=="NewDesignation")
{   
    if($role_type_id=="")
    {
        $q2 = "SELECT role_type_name FROM role_type_master WHERE role_type_name = '$role_type_name' AND dp_id = '$dp_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Designation Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO role_type_master (role_type_name, dp_id) VALUES('$role_type_name','$dp_id')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Update";
            }
        }
    }
    else
    {
        $q2 = "SELECT role_type_name FROM role_type_master WHERE role_type_name = '$role_type_name' AND dp_id = '$dp_id' AND role_type_id != '$role_type_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Designation already exists";
        } 
        else {
            $cmd2 = "UPDATE role_type_master SET role_type_name = '$role_type_name',dp_id = '$dp_id' WHERE role_type_id = '$role_type_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowDesignation")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Department</th>
                            <th>Role/ Designation</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT r.*, d.d_name FROM role_type_master r INNER JOIN department_master d ON d.dp_id=r.dp_id order by r.role_type_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-success btn-sm' onclick='AssignModule(".$role_type_id.",\"".$role_type_name."\");'><span class='fa fa-cog'></span></button>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('role_type_master','role_type_id',".$role_type_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('role_type_master','".$status."','role_type_id',".$role_type_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('role_type_master','".$status."','role_type_id',".$role_type_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['d_name']}</td>
                            <td>{$row['role_type_name']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}
if($Flag=="NewCategory")
{   
    if($c_id=="")
    {
        $q2 = "SELECT c_name FROM category_master WHERE c_name = '$c_name' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Category Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO category_master (c_name) VALUES('$c_name')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Update";
            }
        }
    }
    else
    {
        $q2 = "SELECT c_name FROM category_master WHERE c_name = '$c_name' AND c_id != '$c_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Category already exists";
        } 
        else {
            $cmd2 = "UPDATE category_master SET c_name = '$c_name' WHERE c_id = '$c_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowCategory")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Sr. No.</th>
                    <th>Category Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
        $rstcat = mysqli_query($connect,"select * from category_master order by c_id desc");
        $srno =1;
        while($rwcat = mysqli_fetch_assoc($rstcat))
        {
            extract($rwcat);
            echo "<tr>
                    <td>
                        <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('category_master','c_id',".$c_id.")\">
                            <i class='fa fa-pencil' style='color:white;'></i>
                        </button>";
                    if($status=="Active")
                    {
                        echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('category_master','".$status."','c_id',".$c_id.")\">
                            Deactivate
                        </button>";
                    }
                    else{
                        echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('category_master','".$status."','c_id',".$c_id.")\">
                            Activate
                        </button>";
                    }   
               echo "<td>$srno</td>
                <td>$c_name</td>
                <td>$status</td>
            </tr>";
            $srno++;
        }
    echo "</tbody>
    </table>";
}
if($Flag=="NewUOM")
{   
    if($u_id=="")
    {
        $q2 = "SELECT u_name FROM uom_master WHERE u_name = '$u_name' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "UOM Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO uom_master (u_name) VALUES('$u_name')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Update";
            }
        }
    }
    else
    {
        $q2 = "SELECT u_name FROM uom_master WHERE u_name = '$u_name' AND u_id != '$u_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "UOM already exists";
        } 
        else {
            $cmd2 = "UPDATE uom_master SET u_name = '$u_name' WHERE u_id = '$u_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowUOM")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Sr. No.</th>
                    <th>UOM Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
        $rstcat = mysqli_query($connect,"select * from uom_master order by u_id desc");
        $srno =1;
        while($rwcat = mysqli_fetch_assoc($rstcat))
        {
            extract($rwcat);
            echo "<tr>
                    <td>
                        <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('uom_master','u_id',".$u_id.")\">
                            <i class='fa fa-pencil' style='color:white;'></i>
                        </button>";
                    if($status=="Active")
                    {
                        echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('uom_master','".$status."','u_id',".$u_id.")\">
                            Deactivate
                        </button>";
                    }
                    else{
                        echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('uom_master','".$status."','u_id',".$u_id.")\">
                            Activate
                        </button>";
                    }   
               echo "<td>$srno</td>
                <td>$u_name</td>
                <td>$status</td>
            </tr>";
            $srno++;
        }
    echo "</tbody>
    </table>";
}
?>