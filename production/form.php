<?php
 error_reporting(0);
include 'configuration.php';
$TableName = base64_decode($_GET['TableName']);

// Changes By Pallavi-25-09-24
// Form Rules  (Database Rules)
// Add One Entry in tblformrule table for create form of that table
// label - use varchar datatype
// textarea - text datatype
// Id - int datatype(auto increment)
// select box - enum datatype
// file - longtext
// load data from another table - store id in current table  then use smallint datatype
$rstrule = mysqli_query($connect,"select * from tblformrule where TableName='$TableName'");
if(mysqli_num_rows($rstrule)>0)
{
    $rwrule = mysqli_fetch_assoc($rstrule);
    $FieldName = $rwrule['FieldName'];
}
else
{
    echo "<script>alert('Form Not Defined');
    window.location.href='index.php';</script>";
}
$query = "SHOW COLUMNS FROM $TableName";
$result = mysqli_query($connect, $query);
$columns = [];             
if ($result) {
    while ($column = mysqli_fetch_assoc($result)) {
        $columns[] = $column['Field']; 
    }
}

$rstIncrement = mysqli_query($connect,"SHOW TABLE STATUS LIKE '$TableName'");
$rwIncrement = mysqli_fetch_assoc($rstIncrement);
$Auto_increment = $rwIncrement['Auto_increment'];

// print_r($_POST);

if (isset($_POST['submit'])) {
    $formData = $_POST;
    $emptyFields = false;
    foreach ($formData as $field => $value) {
        if ($field === 'submit') continue;
        if (empty($value)) {
            $emptyFields = true;
            break;
        }
    }
   
    if ($emptyFields) {
        $alert = '<div class="alert alert-danger alert-dismissable center">
  				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
  				Please fill all the fields
  			</div>';
    } else {
        $uniqueField = null;
        $queryUniqueFields = "SHOW INDEX FROM $TableName WHERE Non_unique = 0";
        $resultUniqueFields = mysqli_query($connect, $queryUniqueFields);

        $uniqueFieldCount = 0;
        while ($indexRow = mysqli_fetch_assoc($resultUniqueFields)) {
            $uniqueFieldCount++;
            if ($uniqueFieldCount == 2) {
                $uniqueField = $indexRow['Column_name'];
                break;  
            }
        }

        $uniqueValue = mysqli_real_escape_string($connect, $_POST[$uniqueField]);
        $checkUniqueQuery = "SELECT * FROM $TableName WHERE $uniqueField = '$uniqueValue'";
        $checkResult = mysqli_query($connect, $checkUniqueQuery);
        
        // if (mysqli_num_rows($checkResult) > 0) {
        //     $alert = '<div class="alert alert-danger alert-dismissable center">
  		// 		<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
  		// 		Duplicate value found for '.$uniqueField.'
  		// 	</div>';
        // } else {
            $columnsInsertField = implode(", ", array_keys(array_filter($formData, function($field) {
                return $field !== 'submit';
            }, ARRAY_FILTER_USE_KEY)));
            $valuesInsertField = implode(", ", array_map(function($value) use ($connect) {
                return "'" . mysqli_real_escape_string($connect, $value) . "'";
            }, array_filter($formData, function($field) {
                return $field !== 'submit';
            }, ARRAY_FILTER_USE_KEY)));


            $insertQuery = "INSERT INTO $TableName ($columnsInsertField) VALUES ($valuesInsertField)";
            // echo $insertQuery;
            if (mysqli_query($connect, $insertQuery)) {
                $alert = '<div class="alert alert-success alert-dismissable center">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            Data inserted successfully...
                        </div>';
            } else {
                // $alert = '<div class="alert alert-danger alert-dismissable center">
                //     <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                //     '.mysqli_error($connect).'
                // </div>';
            }
           
        // }
    }
}
if(isset($_POST['Update']))
{
    $formData = $_POST;
    $emptyFields = false;
    foreach ($formData as $field => $value) {
        if ($field === 'Update') continue;
        if (empty($value)) {
            $emptyFields = true;
            break;
        }
    }
    if ($emptyFields) {
        $alert = '<div class="alert alert-danger alert-dismissable center">
  				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
  				Please fill all the fields
  			</div>';
    } else {
        $UpdateColumn = array_key_first($formData);
        $updateFields = array_filter($formData, function($key) {
            return $key !== 'Update';
        }, ARRAY_FILTER_USE_KEY);

        $setClause = implode(", ", array_map(function($key, $value) use ($connect) {
            return "$key = '" . mysqli_real_escape_string($connect, $value) . "'";
        }, array_keys($updateFields), $updateFields));     
        $updateId = $formData[$UpdateColumn]; 
        $updateQuery = "UPDATE $TableName SET $setClause WHERE $UpdateColumn = '$updateId'";
        if (mysqli_query($connect, $updateQuery)) {
            $alert = '<div class="alert alert-success alert-dismissable center">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        Data Updated successfully...
                    </div>';
        } else {
            $alert = '<div class="alert alert-danger alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                '.mysqli_error($connect).'
            </div>';
        }
    }
}
if(isset($_GET['DeleteField']) && isset($_GET['DeleteColumnName'])) {
    $DeleteField = $_GET['DeleteField'];
    $status = $_GET['status'];
    $DeleteColumnName = $_GET['DeleteColumnName'];

    if($status=="1"){ $status='Active'; }
    if($status=="0"){ $status='Deactive'; }
    $cmd2 = "update $TableName SET status='$status' WHERE $DeleteColumnName = '$DeleteField'";
    $connect->query($cmd2);

    $alert = '<div class="alert alert-success alert-dismissable center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                Status Updated Sucessfully...
            </div>';
}
if(isset($_GET['UpdateFields']))
{
    $UpdateId = $_GET['UpdateFields'];
    $UpdateColumnName = $_GET['UpdateColumnName'];
    $rstupdate = mysqli_query($connect,"select * from $TableName where $UpdateColumnName='$UpdateId'");
    $rwupdate = mysqli_fetch_assoc($rstupdate);
}
?>
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
      <?php echo @$alert;?>
      <div class="col-md-5"  >
        <div class="card">
          <div class="card-body">
            <div class="panel panel-primary" style="margin:-10px;">
              <div class="panel-heading text-primary"><b><i class="fa fa-pencil"></i> New <?= $FieldName ?></b></div>
            </div><br>
            <form method="POST">
                <div class="row pt-0"><hr>
                    <?php 
                        $queryInputForm = "SHOW COLUMNS FROM $TableName";
                        $resultInputForm = mysqli_query($connect, $queryInputForm);
                        $i =0;
                        while ($columnInputForm = mysqli_fetch_assoc($resultInputForm)){ 
                            $hiddenclass='';
                            if($i==0)
                            {
                                $hiddenclass='hidden';
                            }
                           
                        ?>   
                        <div class="col-md-4 pt-3" <?= $hiddenclass ?>>
                            <h6><?php echo ucfirst($columnInputForm['Field']); ?></h6>
                        </div>
                        <div class="col-md-8 pt-2" <?= $hiddenclass ?>>
                        <?php
                        $field = $columnInputForm['Field'];
                        $type = $columnInputForm['Type'];
                        $extra = $columnInputForm['Extra'];
                        $valueUpdate = isset($rwupdate[$field]) ? $rwupdate[$field] : '';
                        if(isset($rwupdate[$field]))
                        {
                            $Auto_increment = $rwupdate[$field];
                        }

                        if (strpos($type, 'int') !== false && strpos($extra, 'auto_increment') !== false) {
                            echo "<input type='text' name='$field' class='form-control' value='$Auto_increment' readonly />";
                        } 
                        elseif (strpos($type, 'text') !== false) {
                            echo "<textarea name='$field' id='$field' class='form-control required' required placeholder='Enter $field'>$valueUpdate</textarea>";
                        } 
                        elseif (strpos($type, 'varchar') !== false && strpos($type, '(250)') !== false) {
                            echo "<input type='text' name='$field' id='$field' class='form-control required' required placeholder='Enter $field' value='$valueUpdate'/>";
                        } 
                        elseif (strpos($type, 'enum') !== false) {
                            preg_match("/^enum\((.*)\)$/", $type, $matches);
                            $enumValues = str_getcsv($matches[1], ',', "'");
                            echo "<select name='$field' class='form-control form-select required' required name='$field' id='$field'>";
                            echo "<option value=''>Select Option</option>";
                            foreach ($enumValues as $value) {
                                $selected = ($value == $valueUpdate) ? "selected" : "";
                                echo "<option value='$value' $selected>$value</option>";
                            }
                            echo "</select>";
                        } 
                        else {
                            echo "<input type='text' name='$field' class='form-control required' required placeholder='Enter $field' value='$valueUpdate'/>";
                        }
                        ?>
                        </div>
                    
                    <?php  $i++;
                    } ?>
                </div>
                <br>
                <?php if(isset($_GET['UpdateFields']) && isset($_GET['UpdateColumnName'])){ ?>
                    <button type="submit" name="Update" class="btn btn-primary" ><i class="fa fa-pencil"></i>&nbsp; Update</button>  
                <?php }else{ ?>
                    <button type="submit" name="submit" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp; Add </button>  
                <?php } ?>
                <a href="#" class="btn btn-success" onclick="reload();"  style="height: 32px;"><i class="fa fa-files-o"></i> Reset</a>
                <br>
            </form>
          </div>
        </div>
      </div>       
      <div class="col-md-7">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive text-nowrap">
              <table id="<?= $TableName; ?>" class="table table-bordered table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Action</th>
                        <?php 
                            foreach ($columns as $index => $column) {
                                if ($index > 0) { // Skip the first field, as it is treated as ID
                                    echo "<th>" . $column . "</th>";
                                }
                            }    
                       ?>
                  </tr>
                </thead> 
                  <?php
                      $result2 = mysqli_query($connect,"select * from $TableName");
                      if ($result2->num_rows > 0) {
                        while($row2 = $result2->fetch_assoc()) { 
                            $id = $row2[$columns[0]];
                            $DeleteColumnName = $columns[0];
                          if($row2['status']=="1" || $row2['status']=="Active")
                          {
                              $status ="Active";
                              $btncolor ="success";
                              $actionbtn ="DeActivate";
                              $actionicon ="mdi-trash-can-outline";
                              $btnvalue =0;
                          }
                          else{
                              $status ="DeActive";
                              $btncolor ="primary";
                              $actionbtn ="Activate";
                              $actionicon ="mdi-delete-restore";
                              $btnvalue =1;
                          }
                           // Start generating the table row
                            echo "<tr>";
                            
                            // Action buttons with the first field as the ID
                            echo "
                            <td>
                                <div class='dropdown'>
                                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'>
                                    <img src='../../images/action.png' style='width:45px'>
                                    </button>
                                    <div class='dropdown-menu'>
                                        <button class='dropdown-item' onclick = 'UpdateFields($id,\"$DeleteColumnName\")'>
                                            <i class='mdi mdi-pencil-outline me-1'></i> Edit
                                        </button>
                                        <button class='dropdown-item' onclick='DeAcivate($id, $btnvalue,\"$DeleteColumnName\");'>
                                            <i class='mdi $actionicon me-1'></i> $actionbtn
                                        </button>
                                    </div>
                                </div>
                            </td>";

                            // Dynamically print all columns except the first one
                            foreach ($columns as $index => $column) {
                                if ($index > 0) { // Skip the first field, as it is treated as ID
                                    echo "<td>" . $row2[$column] . "</td>";
                                }
                            }

                            echo "</tr>";
                        }
                      }
                  ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</div>

<?php
  include '../Footer.php';
  include '../ajaxfunction.php';
?>
<script>
     $(document).ready(function() {
        $("#<?= $TableName ?>").DataTable().destroy();
        $("#<?= $TableName ?>").DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
    });
    function reload()
    {
        const queryString = window.location.search; 
        const urlParams = new URLSearchParams(queryString); 
        const UpdateFields = urlParams.get('UpdateFields'); 
        if(UpdateFields!== null)
        {
            urlParams.delete('UpdateFields'); 
            urlParams.delete('UpdateColumnName'); 
            var newUrl = window.location.pathname + '?' + urlParams.toString(); 
            window.location.href = newUrl;
        }else {
            window.location.reload();
        }
    }
    $(document).ready(function () {
		window.setTimeout(function() {
			$(".alert-danger").fadeTo(1000, 0).slideUp(1000, function(){
				$(this).remove(); 
			});
		}, 2500);
	});

  	$(document).ready(function () {
      window.setTimeout(function() {
        $(".alert-success").fadeTo(1000, 0).slideUp(1000, function(){
          $(this).remove();
            const queryString = window.location.search; 
            const urlParams = new URLSearchParams(queryString); 
            const DeleteField = urlParams.get('DeleteField'); 
            const UpdateFields = urlParams.get('UpdateFields'); 
            if (DeleteField !== null) {
                urlParams.delete('DeleteField'); 
                urlParams.delete('status'); 
                urlParams.delete('DeleteColumnName'); 
                var newUrl = window.location.pathname + '?' + urlParams.toString(); 
                window.location.href = newUrl;
            } 
            else if(UpdateFields!== null)
            {
                urlParams.delete('UpdateFields'); 
                urlParams.delete('UpdateColumnName'); 
                var newUrl = window.location.pathname + '?' + urlParams.toString(); 
                window.location.href = newUrl;
            }else {
                window.location.reload();
            }
        });
      }, 300);
      window.setTimeout(function() {
        $(".alert-warning").fadeTo(1000, 0).slideUp(1000, function(){
          $(this).remove();
          window.location.reload();
        });
      }, 300);
    });

    function DeAcivate(id,status,DeleteColumnName)
	{
		var c= confirm("Do You Want to Change the Status?");
		if(c==true)
		{
            const queryString = window.location;
            // alert(queryString+'&DeleteFieled='+id+'&status='+status);
			window.location.href = queryString+'&DeleteField='+id+'&DeleteColumnName='+DeleteColumnName+'&status='+status;
		}
	}
    function UpdateFields(id,UpdateColumnName)
    {
        const queryString = new URL(window.location);
        const params = queryString.searchParams;
        if (params.has('UpdateFields')) {
            params.set('UpdateFields', id);
        } else {
            params.append('UpdateFields', id);
            params.append('UpdateColumnName', UpdateColumnName);
        }
        window.location.href = queryString.toString();
    }
</script>