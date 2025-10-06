<?php 
include '../configuration.php';

extract($_POST);
$designation_id= $_POST['designation_id'];
?>


<div class="">
 <div class="col12 col-sm-12 col-xs-12">
  <?php

  if(isset($designation_id) && !empty($designation_id))
  {
    $assign_query = "SELECT m.module_id, m.module_name FROM all_modules m";
    $res_module = mysqli_query($connect,$assign_query);
    if(mysqli_num_rows($res_module)>0)
    {
      ?>
      <div class="col-md-12">
      <table id="employee-grid"  cellpadding="0" cellspacing="0" border="0" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
          <tr>
            <th width="5">Sr.Id</th>
            <th width="15"> Module Name</th>
            <th>Sub Module</th>
          </tr>
        </thead>
        <?php
        $i = 1;
        while ($module_row=mysqli_fetch_array($res_module)) 
        {
          $usercheck = mysqli_query($connect,"SELECT `role_id` FROM `assign_module` WHERE module_id = ".$module_row["module_id"]." and role_id = '$designation_id'"); 
          if(mysqli_num_rows($usercheck)>0)
          {
            while($userrow = mysqli_fetch_array($usercheck))
            {
              if($userrow['role_id'] = $designation_id)
              { 
                ?>
                <tr> 
                  <td class="form_center" > 
                    <?php 
                    echo $i; 
                    ?>
                  </td>                                   
                  <td width="15">
                    <?php 
                      echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px; display: inline-flex; align-items: center;'>";
                      echo "<input type='checkbox' style='margin-right: 8px;' name='AllCheckBox[]' value=".$module_row["module_id"]." checked/>";
                      echo "<span style='white-space: nowrap;'>".$module_row["module_name"]."</span>";
                      echo "<span class='checkmark' style='margin-left: 4px;'></span></label>"; 
                    ?>
                    <?php 
                    // echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px;'><input type='checkbox'  style='margin-right: 3px;' name='AllCheckBox[]' value=".$module_row["module_id"]." checked/>";
                    // echo $module_row["module_name"].'<span style="margin-top: 2px; margin-left:4px;" class="checkmark"></span></label>'; 
                    ?>
                  </td>
                  <td>
                    <?php
                    $sel_submodule = "SELECT `submodule_id`, `submodule_name`, `submodule_url`, `module_id` FROM `all_submodule` WHERE module_id = ".$module_row["module_id"]." ";
                    $res_module1 = mysqli_query($connect,$sel_submodule);
                    while ($module_row1=mysqli_fetch_array($res_module1)) 
                    {
                      $user_subcheck2 = mysqli_query($connect,"SELECT role_id FROM `assign_submodule` WHERE submodule_id=".$module_row1['submodule_id']." and role_id = ".$designation_id." ");
                      if(mysqli_num_rows($user_subcheck2)>0)
                      {
                        while ($userrow2 = mysqli_fetch_array($user_subcheck2)) 
                        {
                          if($userrow2['role_id'] = $designation_id)
                          { 
                            echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px;'><input type='checkbox'  style='margin-right: 3px;' name='selected_sub_module[]' class='deleteRow' value=".$module_row1["submodule_id"]." checked/>";
                            echo $module_row1["submodule_name"].'<span style="margin-top: 2px; margin-left:4px;" class="checkmark"></span></label>';
                          }
                        }
                      }
                      else
                      {
                        echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px;'><input type='checkbox'  style='margin-right: 3px;' name='selected_sub_module[]' class='deleteRow' value=".$module_row1["submodule_id"]." />";
                        echo $module_row1["submodule_name"].'<span style="margin-top: 2px; margin-left:4px;" class="checkmark"></span></label>';
                      }
                    }
                    ?>
                  </td>
                </tr>
                <?php
                $i++;
              }
              ?>
              <?php 
            }
          } 
          else
          {
            ?> 
            <tr>
              <td width="5"><?php echo $i; ?></td>
              <td width="15"> 
                <?php 
                  // echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px;'><input type='checkbox'  style='margin-right: 3px;' name='AllCheckBox[]' value=".$module_row['module_id']." />";
                  // echo $module_row["module_name"].'<span style="margin-top: 2px; margin-left:4px;" class="checkmark"></span></label>'; 
                ?>
                <?php 
                  echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px; display: inline-flex; align-items: center;'>";
                  echo "<input type='checkbox' style='margin-right: 8px;' name='AllCheckBox[]' value=".$module_row["module_id"]." />";
                  echo "<span style='white-space: nowrap;'>".$module_row["module_name"]."</span>";
                  echo "<span class='checkmark' style='margin-left: 4px;'></span></label>"; 
                ?> 
              </td>
              <td>
                <?php
                $sel_submodule = "SELECT `submodule_id`, `submodule_name`, `submodule_url`, `module_id` FROM `all_submodule` WHERE module_id = ".$module_row['module_id']."";
                $res_module1 = mysqli_query($connect,$sel_submodule);
                if(mysqli_num_rows($res_module1)>0)
                {
                  while ($module_row1=mysqli_fetch_array($res_module1)) 
                  {
                    echo "<label class='container_checkbox btn_check_box btn btn-default' style='margin-top: 5px;'><input type='checkbox'  style='margin-right: 3px;' name='selected_sub_module[]' class='deleteRow' value=".$module_row1["submodule_id"]." />";
                    echo $module_row1["submodule_name"].'<span style="margin-top: 2px; margin-left:4px;" class="checkmark"></span></label>';
                  }
                }
                ?>
              </td>  
            </tr>    
            <?php
          }
          $i++;
        }
      }  
    }
    ?>
  </table>
</div>
</div>
</div>
