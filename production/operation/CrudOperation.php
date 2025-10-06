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
$financial_year = @$_SESSION['financial_year'];
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
if($Flag=="NewPort")
{   
    if($port_master_id=="")
    {
        $q2 = "SELECT port_name FROM port_master WHERE port_name = '$port_name' AND country_id = '$country_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Port Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO port_master (port_name, portShipment_Type, country_id) VALUES('$port_name','$portShipment_Type','$country_id')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Insert";
            }
        }
    }
    else
    {
        $q2 = "SELECT port_name FROM port_master WHERE port_name = '$port_name' AND country_id = '$country_id' AND port_master_id != '$port_master_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Port already exists";
        } 
        else {
            $cmd2 = "UPDATE port_master SET port_name = '$port_name', portShipment_Type = '$portShipment_Type',country_id = '$country_id' WHERE port_master_id = '$port_master_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowPort")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Country</th>
                            <th>Port Name</th>
                            <th>Port/Shipment Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT p.*, c.countryName FROM port_master p INNER JOIN country_master c ON c.id=p.country_id order by p.port_master_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('port_master','port_master_id',".$port_master_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('port_master','".$status."','port_master_id',".$port_master_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-success'  style='color:white;' type='button' onclick=\"DeleteFunction('port_master','".$status."','port_master_id',".$port_master_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['countryName']}</td>
                            <td>{$row['port_name']}</td>
                            <td>{$row['portShipment_Type']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}
if($Flag=="NewTerm")
{   
    if($terms_id=="")
    {
        $q2 = "SELECT title FROM terms_conditions WHERE term_type = '$term_type' AND title = '$title' AND terms_id = '$terms_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Terms/Conditions Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO terms_conditions (term_type, title, discription, comp_id) VALUES('$term_type','$title','$discription','$comp_id')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Insert";
            }
        }
    }
    else
    {
        $q2 = "SELECT title FROM terms_conditions WHERE term_type = '$term_type' AND title = '$title' AND terms_id != '$terms_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Terms/Conditions already exists";
        } 
        else {
            $cmd2 = "UPDATE terms_conditions SET term_type = '$term_type',title = '$title',discription = '$discription' WHERE terms_id = '$terms_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowTerm")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Type</th>
                            <th>Term/Condition Heading</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT * FROM terms_conditions WHERE comp_id = '$comp_id' order by terms_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    $rowstatus = "";
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('terms_conditions','terms_id',".$terms_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status == 1)
                            {
                                $rowstatus = "Active";
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteTermFunction('terms_conditions','".$status."','terms_id',".$terms_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                $rowstatus = "Deactive";
                                echo "<button class='btn btn-round btn-sm btn-success'  style='color:white;' type='button' onclick=\"DeleteTermFunction('terms_conditions','".$status."','terms_id',".$terms_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['term_type']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['discription']}</td>
                            <td>{$rowstatus}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}
if($Flag=="UpdateTermStatus")
{
    if($Status==1){
        $UpdateStatus = 0;
    }
    if($Status==0){ $UpdateStatus=1; }
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
if($Flag=="NewShipmentDocument")
{   
    if($shipment_document_id=="")
    {
        $q2 = "SELECT shipment_document_name FROM shipment_document WHERE shipment_document_name = '$shipment_document_name'";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Shipment Document Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO shipment_document (shipment_document_name) VALUES('$shipment_document_name')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Insert";
            }
        }
    }
    else
    {
        $q2 = "SELECT shipment_document_name FROM shipment_document WHERE shipment_document_name = '$shipment_document_name' AND shipment_document_id != '$shipment_document_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Shipment Document already exists";
        } 
        else {
            $cmd2 = "UPDATE shipment_document SET shipment_document_name = '$shipment_document_name' WHERE shipment_document_id = '$shipment_document_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowShipmentDocument")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Shipment Document</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT * FROM shipment_document order by shipment_document_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('shipment_document','shipment_document_id',".$shipment_document_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('shipment_document','".$status."','shipment_document_id',".$shipment_document_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-success'  style='color:white;' type='button' onclick=\"DeleteFunction('shipment_document','".$status."','shipment_document_id',".$shipment_document_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['shipment_document_name']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}

if($Flag=="NewBranch")
{
    if($hiddenbranch_id=="")
    {
        $rstbranch = mysqli_query($connect,"select branch_id from branch_master where branch_name='$br_name' ");
        if(mysqli_num_rows($rstbranch)>0)
        {
            echo "Branch Name Already Exist";
        }
        else{
            $rstbranchup = mysqli_query($connect,"INSERT INTO `branch_master`(`branch_name`,`branch_add`,
             `branch_head`, `branch_cont_no1`, `branch_cont_no2`, `branch_email`,comp_id) VALUES('$br_name',
             '$branch_add','$branch_head','$branch_cont_no1','$branch_cont_no2','$branch_email','$comp_id')");
            if($rstbranchup)
            {
                echo "Inserted";
            }
            else
            {
                echo "Unable To Insert Branch";
            }
        }
    }
    else
    {
        $rstbranch = mysqli_query($connect,"select branch_id from branch_master where branch_name='$br_name' and branch_id!='$hiddenbranch_id'");
        if(mysqli_num_rows($rstbranch)>0)
        {
            echo "Branch Name Already Exist";
        }
        else{
            $rstbranchup = mysqli_query($connect,"UPDATE `branch_master` SET 
            `branch_name`='$br_name',`branch_add`='$branch_add',`branch_head`='$branch_head',
            `branch_cont_no1`='$branch_cont_no1',`branch_cont_no2`='$branch_cont_no2',
            `branch_email`='$branch_email' WHERE branch_id='$hiddenbranch_id'");
            if($rstbranchup)
            {
                echo "Updated";
            }
            else
            {
                echo "Unable To Update";
            }
        }
    }
}
?>