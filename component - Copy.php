<?php

include_once('configuration.php');
foreach ($_POST as $key => $value) {
    $_POST[$key] = sanitizeInput($value, $connect); 
}
extract($_POST);
$Flag=$_POST['Flag'];
session_start();
$user_id_session = @$_SESSION['user_id'];
$user_type_id = @$_SESSION['user_type_id'];
$role_type_name = @$_SESSION['role_type_name'];
$comp_id = @$_SESSION['comp_id'];
$UserNameSession = @$_SESSION['user_name'];
$UserNameLogin = @$_SESSION['UserNameLogin'];

if($Flag=="Login")
{
    $temp= array();
    $rstuser = mysqli_query($connect,"select * from user_master where user_email='$username'");
    if(mysqli_num_rows($rstuser)>0)
    {
        $rwuser = mysqli_fetch_assoc($rstuser);
        extract($rwuser);
        if($status=="Active")
        {
            if($user_pwd==$password)
            {
                    $rstrole = mysqli_query($connect,"select * from role_type_master where role_type_id='$user_type_id'");
                    $rwrole = mysqli_fetch_assoc($rstrole);
                    $role_type_name = $rwrole['role_type_name'];
                    $dp_id = $rwrole['dp_id'];
                
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_type_id'] = $user_type_id;
                    $_SESSION['dept_id'] = $dp_id;
                    $_SESSION['user_name'] = $user_name;
                    $_SESSION['user_img'] = $user_img;
                    $_SESSION['user_mob'] = $user_mob;
                    $_SESSION['user_add'] = $user_add;
                    $_SESSION['user_img'] = $user_img;
                    $_SESSION['comp_id'] = $comp_id;
                    $_SESSION['branch_id'] = $branch_id;
                    $_SESSION['UserNameLogin'] = $user_email;
                    $_SESSION['role_type_name'] = $role_type_name;
                    $_SESSION['user_email_address'] = $user_email_address;
                    $_SESSION['financial_year'] = $year_id;
                    $temp['message'] =  "Login Successfully";
                    $temp['role_name'] =  $role_type_name;
            }
            else
            {
                $temp['message'] =  "Wrong Password Please Verify";
            }
        }
        else
        {
            $temp['message'] =  "Your account is Deactivated Contact To Administrator";
        }
    }
    else
    {
        $temp['message'] =  "No Record Found";
    }
    echo json_encode($temp);
    
}
if($Flag=="checkCredential")
{
    $value = $_POST['value'];
    $messagetext = $_POST['messagetext'];
    $rstcon = mysqli_query($connect,"select * from user_master where user_mob='$value' OR user_email_address='$value' OR user_email='$value'");
    if(mysqli_num_rows($rstcon)>0 && $value!= $UserNameLogin)
    {
        echo "$messagetext Already Exist....";
    }
}
if($Flag=="LoadDesignation")
{
    echo "<option value=''>Select Designation</option>";
    $rstdes = mysqli_query($connect,"select * from role_type_master where dp_id!=0 and status='Active'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $role_type_id = $rwdes['role_type_id'];
        $role_type_name = $rwdes['role_type_name'];
        $dp_id = $rwdes['dp_id'];
        echo "<option value='$role_type_id' data-attr='$dp_id'>$role_type_name</option>";
    }
}
if($Flag=="LoadReportUser")
{
    echo "<option value=''>Select Report To</option>";
    $rstdes = mysqli_query($connect,"select * from user_master where status='Active'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $user_id = $rwdes['user_id'];
        $user_name = $rwdes['user_name'];
        echo "<option value='$user_id'>$user_name</option>";
    }
}
if($Flag=="LoadCategory")
{
    echo "<option value=''>Select Category</option>";
    $rstdes = mysqli_query($connect,"select * from category_master where status='Active'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $c_id = $rwdes['c_id'];
        $c_name = $rwdes['c_name'];
        echo "<option value='$c_id'>$c_name</option>";
    }
}
if($Flag=="LoadUOM")
{
    echo "<option value=''>Select UOM</option>";
    $rstdes = mysqli_query($connect,"select * from uom_master where status='Active'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $u_id = $rwdes['u_id'];
        $u_name = $rwdes['u_name'];
        echo "<option value='$u_id'>$u_name</option>";
    }
}
if($Flag=="LoadCountry")
{
    echo "<option value=''>Select Country</option>";
    $rstdes = mysqli_query($connect,"select * from country_master where status='1'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $id = $rwdes['id'];
        $countryName = $rwdes['countryName'];
        echo "<option value='$id'>$countryName</option>";
    }
}
if($Flag=="LoadState")
{
    $CountryId = $_POST['CountryId'];
    echo "<option value=''>Select State</option>";
    $rstdes = mysqli_query($connect,"select * from state_master where countryID='$CountryId'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $id = $rwdes['id'];
        $stateName = $rwdes['stateName'];
        echo "<option value='$id'>$stateName</option>";
    }
}
if($Flag=="LoadCity")
{
    $StateId = $_POST['StateId'];
    echo "<option value=''>Select State</option>";
    $rstdes = mysqli_query($connect,"select * from city_master where stateID='$StateId'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $city_id = $rwdes['city_id'];
        $city_name = $rwdes['city_name'];
        echo "<option value='$city_id'>$city_name</option>";
    }
}
if($Flag=="LoadArea")
{
    $CityId = $_POST['CityId'];
    echo "<option value=''>Select State</option>";
    $rstdes = mysqli_query($connect,"select * from area_master where city_id='$CityId'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $area_id = $rwdes['area_id'];
        $area_name = $rwdes['area_name'];
        echo "<option value='$area_id'>$area_name</option>";
    }
}
if($Flag=="LoadProduct")
{
    echo "<option value=''>Select Products</option>";
    $rstdes = mysqli_query($connect,"select product_id,product_name from product_master where status='Active'");
    while($rwdes = mysqli_fetch_assoc($rstdes))
    {
        $product_id = $rwdes['product_id'];
        $product_name = $rwdes['product_name'];
        echo "<option value='$product_id'>$product_name</option>";
    }
}
if($Flag=="loadClientState")
{
    $account_id = $_POST['account_id'];
    $rststate = mysqli_query($connect,"select c.office_state_id,s.stateName from client_master c
    inner join state_master s on s.id = c.office_state_id where c.client_id ='$account_id'");
    $rwstate = mysqli_fetch_assoc($rststate);
    echo "<option value='".$rwstate['office_state_id']."'>".$rwstate['stateName']."</option>";
}
if($Flag=="loadClientCountry")
{
    $account_id = $_POST['account_id'];
    $rststate = mysqli_query($connect,"select c.office_country_id,s.countryName from client_master c
    inner join country_master s on s.id = c.office_country_id where c.client_id ='$account_id'");
    $rwstate = mysqli_fetch_assoc($rststate);
    echo "<option value='".$rwstate['office_country_id']."'>".$rwstate['countryName']."</option>";
}
if($Flag=="LoadCurrencyBankDetails")
{
    $currency_id = $_POST['currency_id'];
    $rstquery = mysqli_query($connect,"SELECT b.bank_name,bb.account_no,bb.id FROM bank_details bb inner join bank_master b on b.bank_id=bb.bank_id WHERE b.status='Active' and bb.status='Active' and bb.currency_id LIKE '%$currency_id%'");
    echo "<option value=''>Select Bank</option>";
    while($rwbank = mysqli_fetch_assoc($rstquery))
    {
        $bank_name = $rwbank['bank_name'];
        $account_no = $rwbank['account_no'];
        $id = $rwbank['id'];
        echo "<option value='$id'>$bank_name/$account_no</option>";
    }
}
if($Flag=="LoadClientDetails")
{
    $account_id = $_POST['account_id'];
    $rstclient = mysqli_query($connect,"select kind_attention,client_mob,client_add from client_master where client_id ='$account_id'");
    $rwclient = mysqli_fetch_assoc($rstclient);
    echo json_encode($rwclient);
}
if($Flag=="SaveAssignedModules")
{
    $designation_id = $_POST['designation_id'];
    if(!isset($_POST['AllCheckBox']) && !isset($_POST['selected_sub_module']))
    {
        echo "Please Assign Atleast one module";
    }
    else
    {
        $modules = $_POST['AllCheckBox'] ?? [];
        $submodules = $_POST['selected_sub_module'] ?? [];
        $delete_modules_query = "DELETE FROM assign_module WHERE role_id = '$designation_id'";
        mysqli_query($connect, $delete_modules_query);

        $delete_submodules_query = "DELETE FROM assign_submodule WHERE role_id = '$designation_id'";
        mysqli_query($connect, $delete_submodules_query);

        foreach ($modules as $module_id) {
            $assign_module_query = "INSERT INTO assign_module (role_id, module_id) VALUES ('$designation_id', '$module_id')";
            mysqli_query($connect, $assign_module_query);
        }

        foreach ($submodules as $submodule_id) {
            $assign_submodule_query = "INSERT INTO assign_submodule (role_id, submodule_id) VALUES ('$designation_id', '$submodule_id')";
            mysqli_query($connect, $assign_submodule_query);
        }

        echo "Success";
    }
}
?>