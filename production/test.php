<div class="row">
    <div class="col-md-6" class="checkbox">
        <label>All&nbsp;<input type="checkbox" name="checktermall" id="checktermall" value="all"> Terms / Conditions :</label>
            <div id="termdiv">
            <?php 
                $checkedTerms = [];
                $checkedDescriptions = [];
                $checkQuery = "SELECT terms_id,discription FROM pro_forma_head_termcondition_detail WHERE pi_no = '$PI_NoNew'";
                $checkResult = $connect->query($checkQuery);
                while ($row = $checkResult->fetch_assoc()) {
                    $checkedTerms[] = $row['terms_id'];
                    $checkedDescriptions[$row['terms_id']] = addslashes($row['discription']);
                }

                $cmd = "SELECT * FROM terms_conditions WHERE comp_id = '$comp_id' AND status = 1";
                $result = $connect->query($cmd);
                if($result->num_rows > 0) {
                    $i = 1;$strcnt = 0;$script = "";
                    while ($row = $result->fetch_assoc()) { 
                        $isChecked = in_array($row['terms_id'], $checkedTerms) ? "checked" : "";
                        $termId = $row['terms_id'];
                        if ($isChecked == "checked" && isset($checkedDescriptions[$termId])) {
                            $discriptionData = $checkedDescriptions[$termId];
                            $script .= "$('#termdiv_$termId').css('display','inline'); $('#termdiv_$termId').html('$discriptionData');\n";
                        }

                        if(strpos($row['discription'], "@@@") !== false){
                            $strarr = explode(" ", $row['discription']);
                            $resarr = [];
                            foreach ($strarr as $value) {
                                if($value == "@@@"){
                                    $replacestr = substr_replace($value,"<input type='text' class='specialcharint' id='specialcharinput_".$strcnt."' style='display:none'><span class='specialinput' id='specialchar_".$strcnt."'>_ _ _ _</span>",0);
                                    array_push($resarr,$replacestr);
                                    $strcnt++;
                                }else{
                                    array_push($resarr,$value);
                                }
                            }
                            $result_discription = implode(" ",$resarr);
                            echo '<div class="checkbox">
                                    <label>'.$i.'. &nbsp;<input type="checkbox" class="termcheckboxes" id="termchk_'.$row['terms_id'].'" value="'.$row['title'].'" '.$isChecked.'> '.$row['title'].'</label>
                                    &emsp;&emsp;&emsp;<label id="termdiv_'.$row['terms_id'].'" style="display:none">'.$result_discription.'</label> 
                                </div>';
                        }else{
                        
                            echo '<div class="checkbox">
                                    <label>'.$i.'. &nbsp;<input type="checkbox" class="termcheckboxes" id="termchk_'.$row['terms_id'].'" value="'.$row['title'].'" '.$isChecked.'> '.$row['title'].'</label>
                                    &emsp;&emsp;&emsp;<label id="termdiv_'.$row['terms_id'].'" style="display:none">'.$row['discription'].'</label> 
                                </div>';
                        }
                        if (!empty($script)) {
                            echo "<script>
                                $(document).ready(function() {
                                    $script
                                });
                            </script>";
                        }
                        $i++;
                    }
                }
            ?>
        </div>
    </div>
<!-- </div>
<div class="row mt-2"> -->
    <div class="col-md-6" class="checkbox">
        <label>All&nbsp;<input type="checkbox" name="checkshipall" id="checkshipall" value="all"> Shipment Document :</label>
        <div id="shipmentdiv">
        <?php
            $checkedTerms = [];
            $checkQuery = "SELECT shipment_document_id FROM pro_forma_head_shipment_detail WHERE pi_no = '$PI_NoNew'";
            $checkResult = $connect->query($checkQuery);
            while ($rowdd = $checkResult->fetch_assoc()) {
                $checkedTerms[] = $rowdd['shipment_document_id'];
            }

            $cmd = "SELECT * FROM shipment_document WHERE status = 'Active'";
            $result = $connect->query($cmd);
            if($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) { 
                    $isChecked = in_array($row['shipment_document_id'], $checkedTerms) ? "checked" : "";
                    echo '<div class="checkbox">
                            <label>'.$i.'. &nbsp;<input type="checkbox" class="shipcheckboxes" value="'.$row['shipment_document_name'].'" data-attr="'.$row['shipment_document_id'].'" '.$isChecked.'> '.$row['shipment_document_name'].'</label>
                            </div>';
                            $i++;
                }
            }
        ?>
        </div>
    </div>
</div>