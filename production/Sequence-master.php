<?php
   $page_heading="Sequence Master";
   include 'configuration.php';
   include "production/header.php";
   
   if(isset($_GET['act'])) {
   	if($_GET['act'] == "del") {
   		if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id'])) {
   			$id = mysqli_real_escape_string($connect, $_GET['id']);
   			$status = mysqli_real_escape_string($connect, $_GET['status']);
   			$qry = "update sequence_master SET status='$status' WHERE Sequence_No='$id'";
   			$rs = mysqli_query($connect, $qry);
   			
   			if($rs) {
   				$alert ='<div class="alert alert-success alert-dismissable center">
   				  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   				  Sequence Status Updated Sucessfully...
   				</div>';
   			}
   		}
   	}
   	
   	if($_GET['act'] == "edit") {
   		if(isset($_GET['id']) && $_GET['id'] != "" && is_numeric($_GET['id'])) {
   			$id = mysqli_real_escape_string($connect, $_GET['id']);
   			$qry = "SELECT * FROM sequence_master WHERE Sequence_No='$id'";
   			$rs = mysqli_query($connect, $qry);
   			
   			if(mysqli_num_rows($rs) > 0) {
   				$arr = mysqli_fetch_array($rs);
   			}
   			
   			if(isset($_POST['submit_value'])) {
          $name = mysqli_real_escape_string($connect, $_POST['txtname']);
          $Prefix = mysqli_real_escape_string($connect, $_POST['txtPrefix']);
          $Suffix = mysqli_real_escape_string($connect, $_POST['txtSuffix']);
          $sequence = mysqli_real_escape_string($connect, $_POST['txtsequence']);
          $PrefixYear = @$_POST['txtPrefixYear'];
          $SuffixYear = @$_POST['txtSuffixYear'];
          $Year = date('Y');
          $PrefixIncludingYear = ''; 
          $SufixIncludingYear = '';
          if($PrefixYear=="on")
          {
            $PrefixIncludingYear = $Year;
          }
          if($SuffixYear=="on")
          {
            $SufixIncludingYear = $Year;
          }
   				$qry_exists = "SELECT * FROM sequence_master WHERE LOWER(Name)='".strtolower($name)."' AND Sequence_No!='$id'";
   				$rs_exists = mysqli_query($connect, $qry_exists);
   				
   				if(mysqli_num_rows($rs_exists) > 0) {
   					$alert ='<div class="alert alert-danger alert-dismissable center">
   							  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   							  Sorry! Duplicate value...
   							</div>';
   				} else {
   					$qry = "UPDATE sequence_master SET Name='$name',Prefix='$Prefix',Sufix='$Suffix',Sequence='$sequence',PrefixIncludingYear='$PrefixIncludingYear',SufixIncludingYear='$SufixIncludingYear' WHERE Sequence_No='$id'";
   					$rs = mysqli_query($connect, $qry);
   					
   					echo '<script>window.location.href = "Sequence-master.php?m=2";</script>';
   					exit;
   				}
   			}
   		}
   	}
   	 // $_GET['act'] == "del"
   } else {
   	if(isset($_POST['submit_value'])) {
   		$name = mysqli_real_escape_string($connect, $_POST['txtname']);
   		$Prefix = mysqli_real_escape_string($connect, $_POST['txtPrefix']);
   		$Suffix = mysqli_real_escape_string($connect, $_POST['txtSuffix']);
      $sequence = mysqli_real_escape_string($connect, $_POST['txtsequence']);
   		$PrefixYear = @$_POST['txtPrefixYear'];
   		$SuffixYear = @$_POST['txtSuffixYear'];
      $Year = date('Y');
      $PrefixIncludingYear ='';
      $SufixIncludingYear ='';
      if($PrefixYear=="on")
      {
        $PrefixIncludingYear = $Year;
      }
      if($SuffixYear=="on")
      {
        $SufixIncludingYear = $Year;
      }
   		$qry_exists = "SELECT * FROM sequence_master WHERE LOWER(Name)='".strtolower($name)."'";
   		$rs_exists = mysqli_query($connect, $qry_exists);
   		
   		if(mysqli_num_rows($rs_exists) > 0) {
   			$alert ='<div class="alert alert-danger alert-dismissable center">
   					  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   					  Sorry! Duplicate value...
   					</div>';
   		} else {
   			$qry =  "INSERT INTO sequence_master (Name, Prefix, Sufix,PrefixIncludingYear,SufixIncludingYear,Sequence)
   			 VALUES('$name','$Prefix','$Suffix','$PrefixIncludingYear','$SufixIncludingYear','$sequence') ";
   			$rs = mysqli_query($connect, $qry);
   			
   			if($rs) {
   				$alert ='<div class="alert alert-success alert-dismissable center">
   				  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   				  Sequence Added Sucessfully...
   				</div>';
   			}
   		}
   	}
   }
   if(isset($_GET['m'])) {
   	if($_GET['m'] == "2") {
   		$alert ='<div class="alert alert-success alert-dismissable center">
   		  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
   		  Sequence Updated Sucessfully...
   		</div>';
   	}	
   }
   ?>
   <div class="right_col" role="main">
      <div class="container-xxl flex-grow-1 container-p-y">
          <form method="POST">
            <div class="row">
              <div class="col-md-12">
                  <?php echo @$alert;?>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="panel panel-primary" >
                      <div class="panel-heading text-primary"><b><i class="fa fa-pencil"></i> Sequence Master </b></div>
                    </div>
                    <div class="row pt-2">
                      <div class="col-md-4 pt-2">
                          <h6>Select Name</h6>
                      </div>
                      <div class="col-md-8">
                          <select class="form-control" id="txtname" name="txtname">
                              <?php
                                  $query = "SHOW COLUMNS FROM sequence_master LIKE 'Name'";
                                  $result = $connect->query($query);
                                  
                                  if($result && $result->num_rows > 0) {

                                      $row = $result->fetch_assoc();
                                      $enumString = str_replace("enum('", "", $row['Type']);
                                      $enumString = str_replace("')", "", $enumString);
                                      $enumValues = explode("','", $enumString);
                                      echo "<option value=''>Select Option</option>";
                                      foreach($enumValues as $value) {
                                        $selected ='';
                                        if(@$arr['Name']== $value)
                                        {
                                          $selected='selected';
                                        }
                                          echo '<option value="' . $value . ' "'.$selected.'>' . $value . '</option>';
                                      }
                                  } 
                              ?> 
                          </select>
                      </div>
                      <div class="col-md-4 pt-3">
                          <h6>Prefix</h6>
                      </div>
                      <div class="col-md-5 pt-2">
                          <input type="text" name="txtPrefix" id="txtPrefix" placeholder="Enter Prefix"  class="form-control" value="<?PHP if(!empty($arr)) { echo htmlspecialchars($arr['Prefix']); } ?>"  />
                      </div>
                      <div class="col-md-3 pt-2">
                        <span style="font-size:12px">Include Year</span>
                        <?PHP 
                          $isChecked ='';
                          if(!empty($arr) && !empty($arr['PrefixIncludingYear'])) 
                          { 
                              $isChecked = 'checked'; 
                              echo "<script>
                                $(document).ready(function(){
                                  CheckPrefixCondition();
                                });
                              </script>";
                          } 
                        ?>
                        <input type="checkbox" name="txtPrefixYear" id="txtPrefixYear" onchange="CheckPrefixCondition();" <?= $isChecked; ?>>
                      </div>
                      <div class="col-md-4 pt-3">
                          <h6>Suffix</h6>
                      </div>
                      <div class="col-md-5 pt-2">
                        <input type="text" name="txtSuffix" id="txtSuffix" placeholder="Enter Suffix" class="form-control " value="<?PHP if(!empty($arr)) { echo htmlspecialchars($arr['Sufix']); } ?>"  />
                      </div>
                      <div class="col-md-3 pt-2">
                        <span style="font-size:12px">Include Year</span>
                        <?PHP 
                          $isCheckedSuffix ='';
                          if(!empty($arr) && !empty($arr['SufixIncludingYear'])) 
                          { 
                              $isCheckedSuffix = 'checked'; 
                              echo "<script>
                                $(document).ready(function(){
                                  CheckSuffixCondition();
                                });
                              </script>";
                            
                          } 
                        ?>
                        <input type="checkbox" name="txtSuffixYear" id="txtSuffixYear" onchange="CheckSuffixCondition()" <?= $isCheckedSuffix; ?> >
                      </div>
                      <div class="col-md-4 pt-2">
                          <h6>Sequence</h6>
                      </div>
                      <div class="col-md-8 pt-2">
                          <input type="text" id="txtsequence" name="txtsequence" placeholder="Add Sequence" 
                          value="<?PHP if(!empty($arr)) { echo $arr['Sequence']; } ?>"  class="form-control" required onkeypress="return isNumber(event)" minlength="2" maxlength="7">
                      </div>
                      <div style="text-align: center;" class="col-md-12">
                          <br> 
                          <input type="hidden" value="1" name="submit_value">
                          <button type="submit" name="btnSubmit" class="btn btn-primary">
                          <?PHP if(isset($_GET['act']) && $_GET['act'] == "edit") { ?>
                          <i class="fa fa-plus"></i> Update Sequence
                          <?PHP } else { ?>
                          <i class="fa fa-plus"></i> Add Sequence
                          <?PHP } ?>
                          </button>
                          <a href="Sequence-master.php" class="btn btn-success"  style="height: 32px;"><i class="fa fa-files-o"></i> Reset</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive" >
                      <table class="table table-striped table-bordered dt-responsive nowrap" id="tblCountry">
                        <thead class="table-dark">
                          <tr>
                              <th>Action</th>
                              <th>Name</th>
                              <th>Prefix</th>
                              <th>Inc. Year</th>
                              <th>Suffix</th>
                              <th>Inc. Year</th>
                          </tr>
                        </thead>
                        <?php
                          $cmd2 = "SELECT * FROM sequence_master ORDER BY Sequence_No DESC";
                          $result2 = $connect->query($cmd2);
                          
                          if ($result2->num_rows > 0) {
                              while($row2 = $result2->fetch_assoc()) { 
                                  if($row2['status']=="1")
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
                                  $PrefixIncludingYear='❌';
                                  if(!empty($row2['PrefixIncludingYear']))
                                  {
                                    $PrefixIncludingYear ="✅";
                                  }
                                  $SufixIncludingYear='❌';
                                  if(!empty($row2['SufixIncludingYear']))
                                  {
                                    $SufixIncludingYear ="✅";
                                  }

                                    echo "
                                    <tr>
                                      <td>
                                          <a href='Sequence-master.php?act=edit&id=".$row2['Sequence_No']."' 
                                          ><button class='btn btn-round btn-sm btn-warning' type='button'>
                                            <i class='fa fa-pencil' style='color:white;'></i>
                                        </button></a>";
                                        if($status=="Active")
                                        {
                                            echo " <a href='Sequence-master.php?act=edit&id=".$row2['Sequence_No']."' 
                                              ><button class='btn btn-round btn-sm btn-danger' type='button'>
                                                Deactivate
                                            </button></a>";
                                        }
                                        else{
                                            echo " <a href='Sequence-master.php?act=edit&id=".$row2['Sequence_No']."' 
                                              ><button class='btn btn-round btn-sm btn-success'  style='color:white;' type='button'>
                                                Activate
                                            </button></a>";
                                        }         
                                      echo "</td>
                                      <td>".$row2['Name']."</td>
                                      <td>".$row2['Prefix']."</td>
                                      <td>".$PrefixIncludingYear."</td>
                                      <td>".$row2['Sufix']."</td>
                                      <td>".$SufixIncludingYear."</td>
                                    </tr>
                                    ";
                            }
                          }
                        ?>
                      </table>
                    </div>
                  </div>
              </div>
              </div>
            </div>
          </form>
      </div>
    </div>
<?php include_once('production/footer.php'); ?>
<script type="text/javascript">
   $(document).ready(function () {
      $('#tblCountry').DataTable().destroy();
       $('#tblCountry').DataTable({
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
      });
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
            <?php if(isset($_GET['m']) || isset($_GET['act'])) { ?>
               window.location.href='Sequence-master.php';
            <?php } ?>
          });
        }, 300);
    });
   function DeAcivate(id,status)
   {
   	var c= confirm("Do You Want to Change Status of This Sequence?");
   	if(c==true)
   	{
   		window.location.href='Sequence-master.php?act=del&id='+id+'&status='+status;
   	}
   }
    function CheckPrefixCondition() {
        var prefixCheckbox = document.getElementById('txtPrefixYear');
        var suffixCheckbox = document.getElementById('txtSuffixYear');
        if (prefixCheckbox.checked) {
            suffixCheckbox.disabled = true;
        } else {
            suffixCheckbox.disabled = false;
        }
    }
    function CheckSuffixCondition() {
        var suffixCheckbox = document.getElementById('txtSuffixYear');
        var prefixCheckbox = document.getElementById('txtPrefixYear');
        if (suffixCheckbox.checked) {
            prefixCheckbox.disabled = true;
        } else {
            prefixCheckbox.disabled = false;
        }
    }
</script>
