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

if($Flag=="NewpaymentDesc")
{   
    if($payment_desc_id=="")
    {
        $q2 = "SELECT payment_description FROM payment_description WHERE payment_description = '$payment_description'";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Payment Desc Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO payment_description (payment_description) VALUES('$payment_description')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Update";
            }
        }
    }
    else
    {
        $q2 = "SELECT payment_description FROM payment_description WHERE payment_description = '$payment_description' AND payment_desc_id  != '$payment_desc_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Payment Desc already exists";
        } 
        else {
            $cmd2 = "UPDATE payment_description SET payment_description = '$payment_description' WHERE payment_desc_id  = '$payment_desc_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowPaymentDesc")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Payment Desc</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT r.* FROM payment_description r order by r.payment_desc_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('payment_description','payment_desc_id',".$payment_desc_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('payment_description','".$status."','payment_desc_id',".$payment_desc_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('payment_description','".$status."','payment_desc_id',".$payment_desc_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['payment_description']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}

?>