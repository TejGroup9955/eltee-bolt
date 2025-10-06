<?php
include_once("../../configuration.php");
$Flag = $_POST['Flag'];
session_start();
//print_r($_SESSION);
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$UserNameSession = @$_SESSION['user_name'];
$dept_id = @$_SESSION['dept_id'];
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect);
}
extract($_POST);

if($Flag=="ShowAccountMaster")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Account Name</th>
                    <th>Account Type</th>
                    <th>Mobile</th>
                    <th>Debit Limit</th>
                    <th>Credit Limit</th>
                    <th>GST No.</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>';
        $rstcat = mysqli_query($connect,"SELECT a.*, at.account_type_name FROM account_master a INNER JOIN account_type_master at ON at.id = a.account_type WHERE IF(at.account_type_name != 'Supplier', IF(a.comp_id = '$dept_id', 1, 0), 1) = 1 ");
        $srno =1;
        while($rwcat = mysqli_fetch_assoc($rstcat))
        {
            extract($rwcat);
            $action = "<a href='add_account_master.php?account_id=".base64_encode($account_id)."&redirect=account_master.php' title='Edit Account' class='btn btn-warning btn-sm btn-round' ><i class='fa fa-pencil'></i></a>";

            echo "<td>$action</td>
                <td>$account_name</td>
                <td>$account_type_name</td>
                <td>$contact_no</td>
                <td>$debit_limit</td>
                <td>$credit_limit</td>
                <td>$GST_no</td>
                <td>$email_id</td>
            </tr>";
        }
    echo "</tbody>
    </table>";
}

if($Flag=="NewAccount")
{
    $account_name           = trim($_POST['account_name']);
    $GST_no                 = trim($_POST['GST_no']);
    // $fertilizer_license_no  = trim($_POST['fertilizer_license_no']);
    $account_type           = intval($_POST['account_type']);
    $email_id               = trim($_POST['email_id']);
    // $license_validity_date  = $_POST['license_validity_date'];
    $address                = trim($_POST['address']);
    $country_id             = intval($_POST['country_id']);
    $state_id               = intval($_POST['state_id']);
    $contact_no             = trim($_POST['contact_no']);
    $debit_limit            = trim($_POST['debit_limit']);
    $credit_limit           = trim($_POST['credit_limit']);
    $bank_name              = trim($_POST['bank_name']);
    $bank_acc_no            = trim($_POST['bank_acc_no']);
    //$IFSC_Code              = trim($_POST['IFSC_Code']);
    $IFSC_Code              = "NA";
    $swit_code              = trim($_POST['swit_code']);
    $bank_branch_name       = trim($_POST['bank_branch_name']);
    $bank_address           = trim($_POST['bank_address']);
    $IBAN_No                = trim($_POST['IBAN_No']);
    $contact_person_name   = isset($_POST['contact_person_name']) ? (array) $_POST['contact_person_name'] : [];
    $contact_person_number = isset($_POST['contact_person_number']) ? (array) $_POST['contact_person_number'] : [];
    $contact_person_email  = isset($_POST['contact_person_email']) ? (array) $_POST['contact_person_email'] : [];

    $intermilitery_bank_name = $intermilitery_acc_no = $intermilitery_swit_code = $intermilitery_branch_name = "NA";
   
    $rstaccount = mysqli_query($connect,"select account_id from account_master where contact_no='$contact_no' ");
    echo "select account_id from account_master where contact_no='$contact_no' ";
    if(mysqli_num_rows($rstaccount)==0)
    {
        $sql = "INSERT INTO account_master (account_name,account_type,address,country_id,state_id,contact_no,bank_name,bank_acc_no,IFSC_Code,swit_code,bank_branch_name,intermilitery_bank_name,intermilitery_acc_no,intermilitery_swit_code,intermilitery_branch_name,debit_limit,credit_limit,GST_no,email_id,status,comp_id, bank_address,IBAN_no ) VALUES ('$account_name','$account_type','$address','$country_id','$state_id','$contact_no','$bank_name','$bank_acc_no','$IFSC_Code','$swit_code','$bank_branch_name','$intermilitery_bank_name','$intermilitery_acc_no','$intermilitery_swit_code','$intermilitery_branch_name','$debit_limit','$credit_limit','$GST_no','$email_id', 1,'$comp_id', '$bank_address','$IBAN_No')";

        if (mysqli_query($connect, $sql)) {
            $account_id = mysqli_insert_id($connect);

            for ($i = 0; $i < count($contact_person_name); $i++) {
                $name   = $contact_person_name[$i];
                $number = $contact_person_number[$i];
                $email  = $contact_person_email[$i];

                if (!empty($name) && !empty($number)) {
                    mysqli_query($connect, "INSERT INTO account_contact_details (account_id, person_name, contact_number, email) 
                    VALUES ('$account_id', '$name', '$number', '$email')");
                }
            }

            echo "Inserted";
        } else {
            echo "error: " . mysqli_error($connect);
        }
    }else{
        echo "Duplicate Mobile No";
    }
}

if($Flag=="UpdateAccount")
{ 
    $account_id = intval($_POST['account_id']);
    $account_name           = trim($_POST['account_name']);
    $GST_no                 = trim($_POST['GST_no']);
    // $fertilizer_license_no  = trim($_POST['fertilizer_license_no']);
    $account_type           = intval($_POST['account_type']);
    $email_id               = trim($_POST['email_id']);
    // $license_validity_date  = $_POST['license_validity_date'];
    $address                = trim($_POST['address']);
    $country_id             = intval($_POST['country_id']);
    $state_id               = intval($_POST['state_id']);
    $contact_no             = trim($_POST['contact_no']);
    $debit_limit            = trim($_POST['debit_limit']);
    $credit_limit           = trim($_POST['credit_limit']);
    $bank_name              = trim($_POST['bank_name']);
    $bank_acc_no            = trim($_POST['bank_acc_no']);
    //$IFSC_Code              = trim($_POST['IFSC_Code']);
    $IFSC_Code              = "NA";
    $swit_code              = trim($_POST['swit_code']);
    $bank_branch_name       = trim($_POST['bank_branch_name']);
    $bank_address           = trim($_POST['bank_address']);
    $IBAN_No                = trim($_POST['IBAN_No']);
    $contact_person_name   = isset($_POST['contact_person_name']) ? (array) $_POST['contact_person_name'] : [];
    $contact_person_number = isset($_POST['contact_person_number']) ? (array) $_POST['contact_person_number'] : [];
    $contact_person_email  = isset($_POST['contact_person_email']) ? (array) $_POST['contact_person_email'] : [];

    $rstaccount = mysqli_query($connect,"select account_name from account_master where contact_no='$contact_no' and account_id != '$account_id' ");
    if(mysqli_num_rows($rstaccount)==0)
    {
        $sqlUpdate = "UPDATE account_master SET account_name='$account_name',account_type='$account_type',address='$address',country_id='$country_id',state_id='$state_id',contact_no='$contact_no',bank_name='$bank_name',bank_acc_no='$bank_acc_no',IFSC_Code='$IFSC_Code',swit_code='$swit_code',bank_branch_name='$bank_branch_name',debit_limit='$debit_limit', credit_limit='$credit_limit', GST_no='$GST_no', email_id='$email_id', bank_address='$bank_address', IBAN_No='$IBAN_No' WHERE account_id = '$account_id'";

        if ($connect->query($sqlUpdate)) {
            mysqli_query($connect, "DELETE FROM account_contact_details WHERE account_id = '$account_id'");

            for ($i = 0; $i < count($contact_person_name); $i++) {
                $name   = $contact_person_name[$i];
                $number = $contact_person_number[$i];
                $email  = $contact_person_email[$i];

                if (!empty($name) && !empty($number)) {
                    mysqli_query($connect, "INSERT INTO account_contact_details (account_id, person_name, contact_number, email) 
                    VALUES ('$account_id', '$name', '$number', '$email')");
                }
            }

            echo "Updated";
        } else {
            echo "error: " . mysqli_error($connect);
        }
    }else{
        echo "Duplicate Mobile No";
    }
}

if($Flag=="AddExpenses")
{
    $expense_account_id = $_POST['expense_account_id'];
    $expense_amt = $_POST['expense_amt'];
    $remark = $_POST['remark'];
    $invoice_date = $_POST['invoice_date'];
        
    if($_POST['expense_type'] == 'Direct Expenses'){
        $type = 'Direct Expenses';
        $invoice_no = $_POST['invoice_no'];
        $bill_no = '';
        $purpose = '';
    } else {
        $type = 'Against Shipment';
        $invoice_no = $_POST['inv_no'];
        $bill_no = $_POST['bl_no'];
        $purpose = $_POST['purpose'];
    }

    $uploadedFiles = [];

    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            $fileName = time() . '_' . basename($_FILES['attachments']['name'][$key]);
            $targetPath = '../expensesDocs/' . $fileName;

            if (move_uploaded_file($tmp_name, $targetPath)) {
                $uploadedFiles[] = $targetPath;
            }
        }
    }

    // Convert array to JSON for saving
    $attachmentsJson = mysqli_real_escape_string($connect, json_encode($uploadedFiles));
    $typeEsc = mysqli_real_escape_string($connect, $type);
    $remarkEsc = mysqli_real_escape_string($connect, $remark);
    $invoiceEsc = mysqli_real_escape_string($connect, $invoice_no);
    $billEsc = mysqli_real_escape_string($connect, $bill_no);
    $expenseAmtEsc = mysqli_real_escape_string($connect, $expense_amt);
    $expenseAccountIdEsc = mysqli_real_escape_string($connect, $expense_account_id);
    $invoiceDateEsc = mysqli_real_escape_string($connect, $invoice_date);
    $purposeEsc = mysqli_real_escape_string($connect, $purpose);
    

    $sql = "INSERT INTO expenses (account_id, type, amount, remark, invoice_no, invoice_date, bill_no, purpose, attachments, created_at)    VALUES ('$expenseAccountIdEsc', '$typeEsc', '$expenseAmtEsc', '$remarkEsc', '$invoiceEsc', '$invoiceDateEsc', '$billEsc', '$purposeEsc','$attachmentsJson', NOW())";

    if (mysqli_query($connect, $sql)) {
        echo "Inserted";
    } else {
        echo "error: " . mysqli_error($connect);
    }
}

if($Flag=="ShowAccountExpensesMaster")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Account Name</th>
                    <th>Expense Type</th>
                    <th>Expense Amount</th>
                    <th>Remark</th>
                    <th>Invoice No</th>
                    <th>Bill No.</th>
                    <th>Purpose</th>
                    <th>Attachments</th>
                </tr>
            </thead>
            <tbody>';
        $rstcat = mysqli_query($connect,"SELECT e.*, a.account_name FROM expenses e INNER JOIN account_master a ON a.account_id = e.account_id ");
        $srno =1;
        while($rwcat = mysqli_fetch_assoc($rstcat))
        {
            extract($rwcat);
            $action = "";
            echo "
                <td>$srno</td>    
                <td>$account_name</td>
                <td>$type</td>
                <td>$amount</td>
                <td>$remark</td>
                <td>$invoice_no</td>
                <td>$bill_no</td>
                <td>$purpose</td>
                <td>";

                if (!empty($attachments)) {
                    $files = json_decode($attachments, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($files)) {
                        foreach ($files as $file) {
                            $fileName = basename($file);
                            echo "<a href='expensesDocs/$file' target='_blank' style='margin:2px;' title='view file'><i class='fa fa-file'></i></a>";
                        }
                    } else {
                        echo "<span class='text-danger'>Invalid Data</span>";
                    }
                } else {
                    echo "<span class='text-muted'>No Attachments</span>";
                }

                echo "</td>
                    </tr>";
            $srno++;
        }
    echo "</tbody>
    </table>";
}

if($Flag=="IsTelexReleased")
{
    $bl_no = $_POST['bl_no'];

    $query = "select p.pi_no from pro_forma_head p inner join tax_invoice_head t on t.pi_no = p.pi_no where t.bl_no='$bl_no' and p.telex_release_status='1'";
    $rst = mysqli_query($connect,$query);


    $allPurposes = ["Switch BL", "Telex release", "Shipline Tracking"];

    $finalPurposes = [];

    foreach ($allPurposes as $option) {
        $query1 = "SELECT purpose FROM expenses WHERE bill_no='$bl_no' AND purpose='$option'";
        $result1 = mysqli_query($connect, $query1);

        if ($result1 && mysqli_num_rows($result1) > 0) {
            continue;
        }

        if ((!$rst || mysqli_num_rows($rst) == 0) && $option === "Telex release") {
            continue;
        }

       $finalPurposes[] = $option;
    }

    $response['options'] = $finalPurposes;
    $response['status'] = 'success';
    
    echo json_encode($response);
}
