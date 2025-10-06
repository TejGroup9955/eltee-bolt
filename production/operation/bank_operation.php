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

if($Flag=="NewBank")
{   
    if($bank_id=="")
    {
        $q2 = "SELECT bank_name FROM bank_master WHERE bank_name = '$bank_name'";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Bank Already Exist";
        }
        else{
            $cmd2 = "INSERT INTO bank_master (bank_name) VALUES('$bank_name')";
            if ($connect->query($cmd2)) {
                echo "Inserted";
            } else {
                echo "Unable To Update";
            }
        }
    }
    else
    {
        $q2 = "SELECT bank_name FROM bank_master WHERE bank_name = '$bank_name' AND bank_id != '$bank_id' ";
        $res2 = $connect->query($q2);
        if ($res2->num_rows > 0) {
            echo "Bank already exists";
        } 
        else {
            $cmd2 = "UPDATE bank_master SET bank_name = '$bank_name' WHERE bank_id = '$bank_id'";
            if ($connect->query($cmd2)) {
                echo "Updated";
            } else {
                echo "Unable To Update";
            }
        }
    }
}
if($Flag=="ShowBank")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Bank Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT r.* FROM bank_master r order by r.bank_id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('bank_master','bank_id',".$bank_id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('bank_master','".$status."','bank_id',".$bank_id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('bank_master','".$status."','bank_id',".$bank_id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['bank_name']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}

if($Flag=="NewBankDetails")
{
    $currency_id_data ='';
    if($currency_id!="")
    {
        foreach($currency_id as $currency)
        {
            $currency_id_data.=$currency.',';
        }
    }
    if($bankId=="")
    {
        $rstbank = mysqli_query($connect,"select id from bank_details where account_no='$accountNumber'");
        if(mysqli_num_rows($rstbank)>0)
        {
            echo "Bank Number Already Exist";
        }
        else
        {
            $rstadd = mysqli_query($connect,"INSERT INTO `bank_details`(`bank_id`, `branch`, `bank_address`,
            `currency_id`, `po_box`, `account_name`, `account_no`, `iban_no`, `swift_code`) 
            VALUES('$bankname','$branch','$bankaddress','$currency_id_data','$pobox','$accountName',
            '$accountNumber','$ibannumber','$Swiftcode')");
            if($rstadd)
            {
                echo "Inserted";
            }
            else
            {
                echo "Unable To Add";
            }
        }
    }
    else
    {
        $rstbank = mysqli_query($connect,"select id from bank_details where account_no='$accountNumber' and id!='$bankId'");
        if(mysqli_num_rows($rstbank)>0)
        {
            echo "Bank Number Already Exist";
        }
        else
        {
            $rstadd = mysqli_query($connect,"update `bank_details` SET `bank_id`='$bankname',
            `branch`='$branch',`bank_address`='$bankaddress',`currency_id`='$currency_id_data',
            `po_box`='$pobox',`account_name`='$accountName',`account_no`='$accountNumber',
            `iban_no`='$ibannumber',`swift_code`='$Swiftcode' WHERE id='$bankId' ");
            if($rstadd)
            {
                echo "Updated";
            }
            else
            {
                echo "Unable To Add";
            }
        }
    }
}

if($Flag=="ShowBankDetails")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Sr. No.</th>
                            <th>Bank Name</th>
                            <th>Branch</th>
                            <th>Bank Address</th>
                            <th>Currency</th>
                            <th>PO Box</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
                            <th>IBAN No</th>
                            <th>Swift Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            $cmd = "SELECT bb.*,b.bank_name FROM bank_details bb inner join 
            bank_master b  on b.bank_id=bb.bank_id order by bb.id desc";
            $result = $connect->query($cmd);
            if ($result->num_rows > 0) {
                $sr_no = 1;
                while ($row = $result->fetch_assoc()) {
                    extract($row);
                    echo "<tr>
                            <td>
                                <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('bank_details','id',".$id.")\">
                                    <i class='fa fa-pencil' style='color:white;'></i>
                                </button>";
                            if($status=="Active")
                            {
                                echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('bank_details','".$status."','id',".$id.")\">
                                    Deactivate
                                </button>";
                            }
                            else{
                                echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('bank_details','".$status."','id',".$id.")\">
                                    Activate
                                </button>";
                            }
    
                        echo "</td>
                            <td>{$sr_no}</td>
                            <td>{$row['bank_name']}</td>
                            <td>{$row['branch']}</td>
                            <td>{$row['bank_address']}</td>
                            <td>{$row['currency_id']}</td>
                            <td>{$row['po_box']}</td>
                            <td>{$row['account_name']}</td>
                            <td>{$row['account_no']}</td>
                            <td>{$row['iban_no']}</td>
                            <td>{$row['swift_code']}</td>
                            <td>{$row['status']}</td>
                        </tr>";

                    $sr_no++;
                }
            }
       echo '</tbody>
    </table>';
}
?>