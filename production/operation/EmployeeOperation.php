<?php
include_once("../../configuration.php");
$Flag = $_POST['Flag'];
session_start();
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$UserNameSession = @$_SESSION['user_name'];
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect);
}
extract($_POST);

if($Flag=="NewEmployee")
{
    if($txtpassword == $confirm_password)
    {
        if($EmployeeId=="")
        {
            $rstcheck = mysqli_query($connect,"select user_id from user_master where user_mob='$contact_number'");
            $rstcheck2 = mysqli_query($connect,"select user_id from user_master where user_email='$username'");
            if(mysqli_num_rows($rstcheck)>0)
            {
                echo "Duplicate Mobile No";
            }
            else if(mysqli_num_rows($rstcheck2)>0)
            {
                echo "Duplicate User Name";
            }
            else
            {
                $rstuser = mysqli_query($connect,"INSERT INTO `user_master`(`user_type_id`, `user_name`,
                `user_email`,`user_pwd`, `gender`, `branch_id`, `dept_id`, `comp_id`,  
                `user_mob`,`user_add`, `user_email_address`,report_to) VALUES ('$designation','$EmployeeName',
                '$username','$txtpassword','$gender','".$Branch[0]."','$department','$comp_id',
                '$contact_number','$Address','$email','$ReportTo')");
                if($rstuser)
                {
                    $UserId = mysqli_insert_id($connect);
                    if(isset($Branch))
                    {
                        foreach($Branch as $branch_id)
                        {
                            $rstbranch = mysqli_query($connect,"insert into branch_switcher(user_id,branch_id) values('$UserId','$branch_id')");
                        }
                    }
                    uploadFiles('user_master','user_img','UploadPhoto','user_id',$UserId, $connect);
                    echo "Inserted";
                }
                else{
                    echo "Unable To Insert";
                }
            }
        }
        else
        {
            $rstcheck = mysqli_query($connect,"select user_id from user_master where user_mob='$contact_number' and user_id!='$EmployeeId'");
            $rstcheck2 = mysqli_query($connect,"select user_id from user_master where user_email='$username' and user_id!='$EmployeeId'");
            if(mysqli_num_rows($rstcheck)>0)
            {
                echo "Duplicate Mobile No";
            }
            else if(mysqli_num_rows($rstcheck2)>0)
            {
                echo "Duplicate User Name";
            }
            else
            {
                $rstuser = mysqli_query($connect,"update `user_master` SET `user_type_id`='$designation',
                 `user_name`='$EmployeeName',report_to ='$ReportTo',
                `user_email`='$username',`user_pwd`='$txtpassword', `gender`='$gender', 
                `branch_id`='".$Branch[0]."', `dept_id`='$department', `comp_id`='$comp_id',  
                `user_mob`='$contact_number',`user_add`='$Address', `user_email_address`='$email'
                 where user_id='$EmployeeId'");
                if($rstuser)
                {
                    if(isset($Branch))
                    {
                        $rstdel = mysqli_query($connect,"delete from branch_switcher where user_id='$EmployeeId'");
                        foreach($Branch as $branch_id)
                        {
                            $rstbranch = mysqli_query($connect,"insert into branch_switcher(user_id,branch_id) values('$EmployeeId','$branch_id')");
                        }
                    }
                    uploadFiles('user_master','user_img','UploadPhoto','user_id',$EmployeeId, $connect);
                    echo "Updated";
                }
                else{
                    echo "Unable To Insert";
                }
            }
        }
    }
    else
    {
        echo "Password Mismatch";
    }
}
if($Flag=="ShowEmployee")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Sr. No.</th>
                    <th>Employee Name</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Email ID</th>
                    <th>Image</th>
                    <th>Designation</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
        $rstcat = mysqli_query($connect,"SELECT u.*,r.role_type_name FROM user_master u 
        inner join role_type_master r on r.role_type_id=u.user_type_id 
        where u.user_type_id!='1' order by u.user_id desc");
        $srno =1;
        while($rwcat = mysqli_fetch_assoc($rstcat))
        {
            extract($rwcat);
            echo "<tr>
                    <td>
                        <a href='user_master.php?UserId=".base64_encode($user_id)."'><button class='btn btn-round btn-sm btn-warning' type='button'>
                            <i class='fa fa-pencil' style='color:white;'></i>
                        </button></a>";
                    if($status=="Active")
                    {
                        echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('user_master','".$status."','user_id',".$user_id.")\">
                            Deactivate
                        </button>";
                    }
                    else{
                        echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('user_master','".$status."','user_id',".$user_id.")\">
                            Activate
                        </button>";
                    }   
            echo "<td>$srno</td>
                <td>$user_name</td>
                <td>$gender</td>
                <td>$user_add</td>
                <td>$user_mob</td>
                <td>$user_email_address</td>
                <td><img src='Production/$user_img' style='width:70px;'></td>
                <td>$role_type_name</td>
                <td>$status</td>
            </tr>";
            $srno++;
        }
    echo "</tbody>
    </table>";
}

if($Flag=="NewProduct")
{
    $AllPackaging='';
    foreach($pakingtype as $Packaging)
    {
        $AllPackaging .=$Packaging.',';
    }
    if($productId=="")
    {
        $rstcheck = mysqli_query($connect,"select product_id from product_master where product_name='$productName'");
        if(mysqli_num_rows($rstcheck)>0)
        {
            echo "Duplicate Product Name";
        }
        else
        {
            $rstuser = mysqli_query($connect,"INSERT INTO `product_master`(`product_name`, `product_code`,
            `category_id`,`uom_id`, `packing_type`, `goods_tax`,hsn_rate) VALUES ('$productName','$productCode',
            '$productCategory','$uom','$AllPackaging','$goodsTax','$HSNRate')");
            if($rstuser)
            {
                $ProductId = mysqli_insert_id($connect);
                echo "Inserted";
            }
            else{
                echo "Unable To Insert";
            }
        }
    }
    else
    {
        $rstcheck = mysqli_query($connect,"select product_id from product_master where product_name='$productName' and product_id!='$productId'");
        if(mysqli_num_rows($rstcheck)>0)
        {
            echo "Duplicate Product Name";
        }
        else
        {
            $rstuser = mysqli_query($connect,"update `product_master` SET `product_name`='$productName',
            `product_code`='$productCode',category_id ='$productCategory',
            `uom_id`='$uom',`packing_type`='$AllPackaging', `goods_tax`='$goodsTax', `hsn_rate`='$HSNRate'
            where product_id ='$productId'");
            if($rstuser)
            {
                echo "Updated";
            }
            else{
                echo "Unable To Insert";
            }
        }
    }
}
if($Flag=="ShowProduct")
{
    echo '<table id="dtlRecord" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Product Specification</th>
                    <th>Sr. No.</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Category</th>
                    <th>UOM</th>
                    <th>Packing type</th>
                    <th>HSN No</th>
                    <th>Goods Tax %</th>
                </tr>
            </thead>
            <tbody>';
            $rstcat = mysqli_query($connect,"SELECT p.*,r.u_name,c.c_name FROM product_master p
            inner join uom_master r on r.u_id=p.uom_id
            inner join category_master c on c.c_id = p.category_id
             order by p.product_id desc");
            $srno =1;
            while($rwcat = mysqli_fetch_assoc($rstcat))
            {
                extract($rwcat);
                $Package = explode(',',$packing_type);
                $PackNames ='';
                foreach($Package as $Packs)
                {
                    if($Packs!='')
                    {
                        $rstpack = mysqli_query($connect,"select * from packaging_type where id='$Packs'");
                        $rwpack = mysqli_fetch_assoc($rstpack);
                        $PackNames .= $rwpack['packaging_type'].'<br>';
                    }
                }
                echo "<tr>
                        <td>
                            <button class='btn btn-round btn-sm btn-warning' type='button' onclick=\"UpdateFunction('product_master','product_id',".$product_id.")\">
                                <i class='fa fa-pencil' style='color:white;'></i>
                            </button>";
                        if($status=="Active")
                        {
                            echo "<button class='btn btn-round btn-sm btn-danger' type='button' onclick=\"DeleteFunction('product_master','".$status."','product_id',".$product_id.")\">
                                Deactivate
                            </button>";
                        }
                        else{
                            echo "<button class='btn btn-round btn-sm btn-warning'  style='color:white;' type='button' onclick=\"DeleteFunction('product_master','".$status."','product_id',".$product_id.")\">
                                Activate
                            </button>";
                        }   
                echo "<td><button class='btn btn-round btn-sm btn-info'  style='color:white;' type='button' onclick=\"AddProductSpecification('".$product_name."',".$product_id.")\">
                                <i class='fa fa-plus'></i> 
                            </button>
                    </td>
                    <td>$srno</td>
                    <td>$product_name</td>
                    <td>$product_code</td>
                    <td>$c_name</td>
                    <td>$u_name</td>
                    <td>$PackNames</td>
                    <td>$hsn_rate</td>
                    <td>$goods_tax</td>
                </tr>";
                $srno++;
            }
         echo '</tbody>
        </table>';
}

if($Flag=="LoadPrevProductSpecification")
{
    $ProductId = $_POST['ProductId'];
    echo '<table class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action " style="margin-top:15px;" width="100%">
          <tr class="bg_primary">
            <th>Sr. No.</th>
            <th>Product Description</th>
            <th>Value</th>
            <th>Range</th>
            <th>Action</th>
          </tr>';

          $i = 1;
          $cmd_user = "SELECT * FROM product_description WHERE product_id='" .$ProductId. "' ";
          $result = $connect->query($cmd_user);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) 
            {  
              $product_description=$row['product_description'];
              $value=$row['value'];
              $product_range=$row['product_range'];
              $product_id=$row['product_id'];
              $product_description_id=$row['product_description_id'];

                echo '<tr>
                    <td class="center">'.$i.'</td>
                    <td>'.$product_description.'</td>
                    <td>'.$value.' </td>
                    <td>'.$product_range.'</td>
                    <td class="center" width="125">
                    <a class="btn btn-warning btn-sm btn-round" onclick="update_productspecification(\''.$product_id.'\',\''.$product_description_id.'\',\''.$product_description.'\',\''.$value.'\',\''.$product_range.'\')" data-toggle="tooltip" title="Edit Product Description">
                        <i class="fa fa-pencil"></i>
                    </a>
                    </td>
                </tr>';
                //  <a href="delete_productspecificatio('.$row['product_description_id'].') data-toggle="tooltip" title="Delete Product Description" class="btn btn-danger btn-xs  btn-round"><i class="fa fa-remove"></i></a>
                $i = $i + 1;
            }
          }
          else{
            echo "
            <th>No Product Description Added</th>
            ";
          }
        echo "</table>";
}

if($Flag=="AddProductSpecification")
{
    if(!empty($prodspecificationproductId))
    {
        if(empty($prodspecificationId))
        {
            $rstpro = mysqli_query($connect,"select product_id from product_description where product_description='$product_description' and product_id='$prodspecificationproductId' ");
            if(mysqli_num_rows($rstpro)==0)
            {
                $sql1 = mysqli_query($connect, "INSERT INTO product_description (product_id, 
                product_description, value, product_range) VALUES('$prodspecificationproductId', 
                '$product_description', '$value', '$product_range')" );
                if($sql1)
                {
                    echo "Inserted";
                }else{
                    echo "Unable To Insert";
                }
            }
            else{
                echo "Product Specification Already Exist";
            }
        }else{            
            $rstpro = mysqli_query($connect,"select product_id from product_description where 
            product_description='$product_description' and product_id='$prodspecificationproductId' and product_description_id!='$prodspecificationId' ");
            if(mysqli_num_rows($rstpro)==0)
            {
                $sql1 = mysqli_query($connect, "UPDATE product_description SET 
                product_description = '$product_description', 
                value = '$value', product_range = '$product_range'  
                WHERE product_description_id = '$prodspecificationId' " );
                if($sql1)
                {
                    echo "Updated";
                }else{
                    echo "Unable To Insert";
                }
            }
            else{
                echo "Product Specification Already Exist";
            }
        }
    }
}

function uploadFiles($tablename, $fieldName, $value, $CompareField, $CompareId, $connect) {
    if (isset($_FILES[$value]) && $_FILES[$value]['error'] == 0) {
        $file_name = $_FILES[$value]['name'];
        $file_tmp = $_FILES[$value]['tmp_name'];
        $upload_dir = "../EmployeeDocuments/$value/"; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_file_name = uniqid('BD_', true) . '.' . $file_ext;             
        $target_file = $upload_dir . $unique_file_name;

       // $target_file = $upload_dir . basename($file_name);
        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = mysqli_query($connect,"UPDATE $tablename SET $fieldName='$target_file' WHERE $CompareField='$CompareId'");
        } else {
            echo "Error uploading file: " . $file_name;
        }
    }
}
?>