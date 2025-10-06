<?php
$rand_num_1 = rand(111,999);
$rand_num_2 = rand(111,999);
$rand_num_3 = rand(111,999);
$rand_num_4 = rand(111,999);
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example<?php echo $rand_num_1; ?>').DataTable( {
            "lengthMenu": [5, 10, 50, 100, 500],
            "pageLength": 5,
            "aaSorting": []
        } );
        $('#example<?php echo $rand_num_2; ?>').DataTable( {
            "lengthMenu": [5, 10, 50, 100, 500],
            "pageLength": 5,
            "aaSorting": []
        } );
        $('#example<?php echo $rand_num_3; ?>').DataTable( {
            "lengthMenu": [5, 10, 50, 100, 500],
            "pageLength": 5,
            "aaSorting": []
        } );
        $('#example<?php echo $rand_num_4; ?>').DataTable( {
            "lengthMenu": [5, 10, 50, 100, 500],
            "pageLength": 5,
            "aaSorting": []
        } );
    } );
</script>
<style type="text/css">
    
    /* #example_wrapper, #example<?php echo $rand_num_1; ?>_wrapper, #example<?php echo $rand_num_2; ?>_wrapper, #example<?php echo $rand_num_3; ?>_wrapper, #example<?php echo $rand_num_4; ?>_wrapper {
        box-shadow: none !important;
    }
    
    #example_info, #example_paginate, #example<?php echo $rand_num_1; ?>_info, #example<?php echo $rand_num_1; ?>_paginate, #example<?php echo $rand_num_2; ?>_info, #example<?php echo $rand_num_2; ?>_paginate, #example<?php echo $rand_num_3; ?>_info, #example<?php echo $rand_num_3; ?>_paginate, #example<?php echo $rand_num_4; ?>_info, #example<?php echo $rand_num_4; ?>_paginate{
        width: 50%;
        float: left;
    }

    table.dataTable.display tbody tr.odd>.sorting_1, table.dataTable.order-column.stripe tbody tr.odd>.sorting_1{
        padding: 3px !important;
    }
    .dataTables_info, .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        font-size: 10pt !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 4px!important;
        height: 35px!important;
    }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate, .dataTables_wrapper .dataTables_processing {
        padding-bottom: 4px!important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{
        margin: 0 !important;
        padding: 4px !important;
        font-size: 10pt !important;
    }
    .dataTables_wrapper{
        overflow: hidden !important;
    }
    .dataTables_wrapper .dataTables_filter input{
        margin-top: 0 !important;
    } */
</style>
<?php
include '../Check-User.php';
include '../../Configuration.php';

$client_id = intval($_GET['id']);
$requirement_id = intval($_GET['requirement_id']);

$cmd = "SELECT * FROM view_lead_data WHERE client_id = '$client_id' AND requirement_id = '$requirement_id' ";
$result = $connect->query($cmd);
if ($result->num_rows > 0)
{
    if($row = $result->fetch_assoc())
    {
        $work_address_req = $row['work_address_req'];
        $client_name=$row['client_name'];
        $requirement_details=$row['requirement_details'];
        $enq_date = strtotime($row["visit_date"]);
        $enq_date = $row['visit_date'] = date('d-m-Y', $enq_date);
        $client_status = $row['client_status'];
        $name = $row['user_name'];

        
        $client_business = $row['client_business'];
        $client_add = $row['client_add'];
        $client_mob = $row['client_mob'];
        $client_email=$row['client_email'];
        $client_person1=$row['client_person1'];
        $client_person1_mob=$row['client_person1_mob'];
        $client_person2=$row['client_person2'];
        $client_person2_mob2=$row['client_person2_mob2'];
        $client_required_details=$row['client_required_details'];
        $client_status=$row['client_status'];
        $landline1=$row['landline'];
        $fax1=$row['fax'];
        $business_type1=$row['business_type'];
        $current_status1=$row['current_status'];
        $work_address=$row['work_address'];
        $client_email2=$row['client_email2'];
        $enq_type = $row['enq_type'];
        $po_received_date = date('d-m-Y', strtotime($row['po_received_date']));
        $po_received_document = $row['po_received_document'];
        $office_city_id = $row['office_city_id'];
        $office_area_id = $row['office_area_id'];
        $work_city_id = $row['work_city_id'];
        $work_area_id = $row['work_area_id'];
        $office_pincode = $row['office_pincode'];
        $work_pincode = $row['work_pincode'];
        $maps_link = $row['maps_link'];
        $website_link = $row['website_link'];
        $visiting_card = $row['visiting_card'];
        $trial_demo_status = $row['trial_demo_status'];
    }
}

// if($maps_link != "")
// {
//     $maps_link = '<a target="_blank" href="'.$maps_link.'" class="btn btn-danger btn-round text_normal btn-xs"><b><i class="fa-solid fa-location-dot"></i> Map </b></a>';
// }
// else
// {
//     $maps_link = '<a class="btn btn-danger disabled btn-round text_normal btn-xs"><b><i class="fa-solid fa-location-dot"></i> Map </b></a>';
// }

if($website_link != "")
{
    $website_link = '<a target="_blank" href="'.$website_link.'" class="btn btn-dark btn-round text_normal btn-xs"><b><i class="fa fa-globe"></i></b> Website</a>';
}
else
{
    $website_link = '<a class="btn btn-dark disabled btn-round text_normal btn-xs"><b><i class="fa fa-globe"></i></b> Website</a>';
}

if($visiting_card != "")
{
    $visiting_card = '<a target="_blank" href="../../images/visiting_card/'.$visiting_card.'" class="btn btn-warning btn-round text_normal btn-xs"><b><i class="fa fa-user"></i> </b> Card</a>';
}
else
{
    $visiting_card = '<a class="btn btn-warning disabled btn-round text_normal btn-xs"><b><i class="fa fa-user"></i> </b> Card</a>';        
}

$contact_person_details = ""; $contact_person_designation = "";
$cmd = "SELECT contact_person_name, contact_person_mobile, contact_person_email, designation, client_req_contact_id FROM client_requirement_contact_master WHERE client_id = '$client_id' AND requirement_id = '$requirement_id' ";
$result = $connect->query($cmd);
if ($result->num_rows > 0)
{
  while($row = $result->fetch_assoc())
  {
    if($row['designation'] != "")
    {
        $contact_person_designation.= '<a class="btn btn-dark btn-xs btn-round" onclick="contact_toggle('.$row['client_req_contact_id'].')">'.$row['designation'].'</a>';
        $contact_person_details.= '<div class="col12 contact_toggle" id="contact_toggle_'.$row['client_req_contact_id'].'" style="border-bottom:dotted 2px #666; padding:4px;margin:2px 0;">
        ';
    }
    if($row['client_req_contact_id'] != "")
    {
        $contact_person_details.= '<h6>'.$row['contact_person_name'].'</h6>';
    }
    if($row['contact_person_mobile'] != "")
    {
        $contact_person_details.= '<a href="tel:'.$row['contact_person_mobile'].'" class="btn btn-primary btn-round text_normal btn-xs"><b><i class="fa fa-phone"></i> </b>'.$row['contact_person_mobile'].'</a>';
    }
    if($row['contact_person_email'] != "")
    {
        $contact_person_details.= '<a href="mailto:'.$row['contact_person_email'].'" class="btn btn-default btn-round text_normal btn-xs"><b><i class="fa fa-envelope"></i> </b>'.$row['contact_person_email'].'</a>';
    }
    $contact_person_details.= '</div>';    
  }
}

if($office_city_id != 0)
{
    $cmd = "SELECT city_name FROM city_master WHERE city_id = '$office_city_id' ";
    $result = $connect->query($cmd);
    if ($result->num_rows > 0)
    {
        if($row = $result->fetch_assoc()) { $office_city_name = $row['city_name']; }
    }
}
if($office_area_id != 0)
{
    $cmd = "SELECT area_name FROM area_master WHERE area_id = '$office_area_id' ";
    $result = $connect->query($cmd);
    if ($result->num_rows > 0)
    {
        if($row = $result->fetch_assoc()) { $office_area_name = $row['area_name'].', '; }
    }
}
if($work_city_id != 0)
{
    $cmd = "SELECT city_name FROM city_master WHERE city_id = '$work_city_id' ";
    $result = $connect->query($cmd);
    if ($result->num_rows > 0)
    {
        if($row = $result->fetch_assoc()) { $work_city_name = $row['city_name']; }
    }        
}
if($work_area_id != 0)
{
    $cmd = "SELECT area_name FROM area_master WHERE area_id = '$work_area_id' ";
    $result = $connect->query($cmd);
    if ($result->num_rows > 0)
    {
        if($row = $result->fetch_assoc()) { $work_area_name = $row['area_name'].', '; }
    }
}

if($po_received_date == "01-01-1970") { $po_received_date = ""; }
if($po_received_document != ""){ $po_received_document = '<a class="btn btn-dark btn-round btn-xs" href="../../images/po_received_document/'.$po_received_document.'" target="_blank" ><i class="fa fa-download"></i> File</a>'; }

$cmd3 = "SELECT c.client_id, e.emp_name as user_name, u.user_id FROM user_visit_record c INNER JOIN user_master u ON u.user_id = c.user_id 
inner join employee_master e on e.emp_id = u.emp_id WHERE c.client_id='$client_id' AND u.dept_id = $dept_id AND u.branch_id = $branch_id AND c.requirement_id = '$requirement_id' AND (u.user_type_id = 7 OR u.user_type_id = 6) ";
$result3 = $connect->query($cmd3);
if ($result3->num_rows > 0)
{
    if($row3 = $result3->fetch_assoc()) 
    {
        $coname = $row3['user_name'];   
        $userid = $row3['user_id'];         
    }
}
?>      
<div class="row">
    <div class="col-md-5">
            <h3><?php echo $client_name; ?>
                <div class="btn btn-default btn-round btn-xs"><?php echo $client_business; ?></div>
            </h3>
            <div class="row">
                <h6 class="col-md-6 client_data">
                    <b><i class="fa fa-location-dot"></i> Office Address</b>
                    <br>
                    <?php echo $client_add; ?>
                    <br> <?php echo @$office_area_name; ?>
                    <?php echo @$office_city_name . ' ' . $office_pincode; ?>
                </h6>

                <h6 class="col-md-6 client_data" style="border-left: dotted 1px #000;">
                    <b><i class="fa fa-location-dot"></i> Work Address</b>
                    <br>
                    <?php echo $work_address_req; ?>
                </h6>
            </div>
            <h6 class="col12 center">
                <a href="tel:<?php echo $client_mob; ?>" class="btn btn-primary btn-round text_normal btn-xs"><b><i class="fa fa-phone"></i> </b><?php echo $client_mob; ?></a>
                <a href="mailto:<?php echo $client_email; ?>" class="btn btn-default btn-round text_normal btn-xs"><b><i class="fa fa-envelope"></i> </b><?php echo $client_email; ?></a>
                <?php echo $maps_link; ?>
                <?php echo $website_link; ?>
                <?php echo $visiting_card; ?>
            </h6>
    </div>

    <div class="col-md-7">
        <div class="row">
            <div class="col-md-5" >
                <h6 class="btn btn-default btn-round btn-sm text_normal">Req. - <b><?php echo $requirement_details; ?></b></h6>
                <?php
                if ($current_status1 != "") {
                    echo '<h5>Current Status : ' . $current_status1 . '</h5>';
                }
                if ($business_type1 != "") {
                    echo '<h5>Structure : ' . $business_type1 . '</h5>';
                }
                ?>
            </div>
            <?php
                $cmd5 = "SELECT c.quot_send_date, c.quot_id, c.client_id, c.quotation_sent_type FROM client_quotation c WHERE c.client_id = '$client_id' AND c.requirement_id = '$requirement_id' ORDER BY c.quot_id ";
                $result5 = $connect->query($cmd5);
                if ($result5->num_rows > 0) {
                    while ($row5 = $result5->fetch_assoc()) {
                        $quot_send_date = strtotime($row5["quot_send_date"]);
                        $quot_send_date = $row5['quot_send_date'] = date('d-m-Y', $quot_send_date);

                        // echo '<h2 class="cursor_default btn btn-primary btn-round btn-xs" > <b>Quotation Date - '.$quot_send_date.' | '.$row5['quotation_sent_type'].' </b></h2>';
                        ?>
                        <!-- <div class="table-responsive">
                            <table id="example<?php echo $rand_num_4; ?>" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
                                <thead>
                                    <tr>
                                        <td>Remark Date</td>
                                        <td>Calling Status</td>
                                        <td>Remark</td>
                                        <td>Next Followup Date</td>
                                    </tr>
                                </thead>
                                <?php
                                $cmd4 = "SELECT c.remark_date, c.calling_status, c.remark, c.next_followup_date FROM client_remark c WHERE c.client_id='$client_id' AND c.quot_id = '".$row5['quot_id']."' AND c.status ='Quotation' AND c.requirement_id = '$requirement_id' ORDER BY c.remark_id";
                                $result4 = $connect->query($cmd4);
                                if ($result4->num_rows > 0) {
                                    while ($row4 = $result4->fetch_assoc()) {
                                        $remark_date = strtotime($row4["remark_date"]);
                                        $remark_date = $row4['remark_date'] = date('d-m-Y', $remark_date);
                                        $calling_status = $row4['calling_status'];
                                        $remark = $row4['remark'];
                                        $next_followup_date = strtotime($row4["next_followup_date"]);
                                        $next_followup_date = $row4['next_followup_date'] = date('d-m-Y', $next_followup_date);
                                        ?>
                                        <tr>
                                            <td><?php echo $remark_date; ?></td>
                                            <td><?php echo $calling_status; ?></td>
                                            <td><?php echo $remark; ?></td>
                                            <td><?php echo $next_followup_date; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </div> -->
                        <?php
                    }
                }
            ?>
            <div class="col-md-7">
                <h5>Contact Details</h5>
                <?php
                echo $contact_person_designation;
                echo $contact_person_details;
                ?>
            </div>
        </div>
       
        <?php
        if ($trial_demo_status == 1) {
            ?>
            <label style="width: auto;float: right;" class='container_checkbox btn btn-default btn-round'>
                <input type='checkbox' id="trial_demo" disabled="" checked="" />
                <span style="margin-top: 3px;margin-left: 7px;" class='checkmark'></span> Trial Demo
            </label>
            <?php
        } else {
            ?>
            <label onclick="trial_demo_click(<?php echo $client_id; ?>, <?php echo $requirement_id; ?>);" style="width: auto;float: right;" class='container_checkbox btn btn-default btn-round'>
                <input type='checkbox' id="trial_demo" />
                <span style="margin-top: 3px;margin-left: 7px;" class='checkmark'></span> Trial Demo
            </label>
            <?php
        }
        ?>
        <h2 class="pull-left cursor_default btn btn-info btn-sm btn-round">Enquiry - <b><?php echo @$enq_date; ?></b></h2>
        <span class="pull-left btn btn-primary btn-round btn-sm"><i class="fa fa-globe"></i> <?php echo $enq_type; ?></span>
        <span class="pull-left">
            <?php
            if ($client_status == "Hot") {
                echo "<a class='cursor_default btn btn-round btn-danger btn-sm bg-hot'>$client_status</a>";
            } else if ($client_status == "Warm") {
                echo "<a class='cursor_default btn btn-round btn-warning btn-sm bg-warm'>$client_status</a>";
            } else if ($client_status == "Cold") {
                echo "<a class='cursor_default btn btn-round btn-info btn-sm bg-cold'>$client_status</a>";
            } else if ($client_status == "Quotation") {
                echo "<a class='cursor_default btn btn-round btn-primary btn-sm bg-quotation'>$client_status</a>";
            } else if ($client_status == "Requested") {
                echo "<a class='cursor_default btn btn-round btn-dark btn-sm bg-quotation'>$client_status</a>";
            } else if ($client_status == "Given") {
                echo "<a class='cursor_default btn btn-round btn-success btn-sm bg-quotation'>$client_status</a>";
            } else if ($client_status == "Revised") {
                echo "<a class='cursor_default btn btn-round btn-danger btn-sm bg-quotation'>$client_status</a>";
            } else if ($client_status == "Raw") {
                echo "<a class='cursor_default btn btn-round btn-danger btn-sm bg-raw'>$client_status</a>";
            } else if ($client_status == "PO Received") {
                echo "<a class='cursor_default btn btn-round btn-danger btn-sm bg-close'>$client_status</a>";
            }
            ?>
        </span>
        <a title="Edit Customer" data-toggle="tooltip" href="Edit-Lead-Master.php?client_id=<?php echo base64_encode($client_id); ?>" class="pull-left btn btn-warning btn-round btn-sm"><i class="fa fa-pencil"></i></a>

        <?php if ($client_status == "PO Received") { ?>
        <div class="col-md-12">
            <h2 class="pull-left cursor_default btn btn-default btn-xs btn-round">PO Received - <b><?php echo @$po_received_date; ?></b></h2>
            <?php echo $po_received_document; ?>
        </div>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>


<div class="table-responsive ">
    <h2 class="btn btn-success btn-round btn-xs" style="margin-top: 10px;"> <b>Sales Executive - <?php echo $name;?></b></h2>
    <table id="example" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
            <tr>
                <td style="width: 20%">Executive</td>
                <td style="width: 15%">Followup Date</td>
                <td style="width: 40%">Customer Feedback</td>
                <td style="width: 15%">Next Followup Date</td>
                <td style="width: 7%">Status</td>
                <td style="width: 3%">Edit</td>
            </tr>
        </thead>
        <?php
        $cmd1 = "SELECT u.visit_id, u.visit_time, u.followup_time, u.visit_date, u.client_feedback, u.next_folloup_date, em.emp_name as user_name, m.user_type_id, u.status, u.user_id FROM user_visit_record u INNER JOIN user_master m ON m.user_id = u.user_id
        inner join employee_master em on em.emp_id = m.emp_id WHERE u.client_id='$client_id' AND u.requirement_id = '$requirement_id' AND (m.user_type_id = 7 OR m.user_type_id = 6) ORDER BY u.visit_id DESC";
        $result1 = $connect->query($cmd1);
        if ($result1->num_rows > 0)
        {
            $inc = 0;
            while($row1 = $result1->fetch_assoc())
            {
                $visit_date = strtotime($row1["visit_date"]);
                $visit_date = date('d-m-Y', $visit_date);  
                $client_feedback = $row1['client_feedback'];
                $next_folloup_date = strtotime($row1["next_folloup_date"]);
                $next_folloup_date = $row1['next_folloup_date'] = date('d-m-Y', $next_folloup_date);  
                $exname = $row1['user_name']; 
                $status = $row1['status'];
                ?>
                <tr>
                    <td><?php echo $exname;?></td>
                    <td class="center"><?php echo $visit_date.', '.$row1['visit_time'];?></td>
                    <td><?php echo $client_feedback;?></td>
                    <td class="center"><?php echo $next_folloup_date.', '.$row1['followup_time'];?></td>
                    <td class="center"><?php echo $status;?></td>
                    <td class="center">
                    <?php
                    if($row1['user_id'] == $user_id && $inc == 0 && strtotime($row1['visit_date']) >= strtotime(date('Y-m-d')) )
                    {
                        echo '<a class="btn btn-warning btn-xs btn-round" href="Edit-Remark.php?client_id='.$client_id.'&requirement_id='.$requirement_id.'&visit_id='.$row1['visit_id'].'"><i class="fa fa-pencil"></i></a>';
                    }
                    else
                    {
                        echo '<a class="btn btn-warning btn-xs btn-round disabled" ><i class="fa fa-pencil"></i></a>';
                    }
                    ?>
                    </td>
                    
                </tr>
                <?php
            $inc++;                
            }
        }
        ?>
    </table>
</div>
<div class="table-responsive ">
    <h2 class="cursor_default btn btn-success btn-round btn-xs" style="margin-top: 10px;" > <b>Sales Co-Ordinator Executive - <?php echo @$coname;?></b></h2>
    <table id="example<?php echo $rand_num_1; ?>" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
            <tr>
                <td style="width: 20%">Executive</td>
                <td style="width: 15%">Followup Date</td>
                <td style="width: 40%">Customer Feedback</td>
                <td style="width: 15%">Next Followup Date</td>
                <td style="width: 7%">Status</td>
                <td style="width: 3%">Edit</td>
            </tr>
        </thead>
        <?php
        $cmd1 = "SELECT u.visit_id, u.visit_time, u.followup_time, u.visit_date, u.client_feedback, 
        u.next_folloup_date, u.status, u.client_strength, e.emp_name as user_name, m.user_id FROM user_visit_record u 
        INNER JOIN user_master m ON m.user_id = u.user_id 
        inner join employee_master e on e.emp_id = m.emp_id 
        WHERE u.client_id='$client_id' AND u.status 
        NOT IN ('Done','Postponed','Upcomming','Cancel','Meeting') AND u.requirement_id = '$requirement_id' 
        AND (m.user_type_id = 8 OR m.user_type_id = 6) ORDER BY u.visit_id DESC";
        $result1 = $connect->query($cmd1);
        if ($result1->num_rows > 0)
        {
            $inc = 0;
            while($row1 = $result1->fetch_assoc())
            {
                $visit_date = strtotime($row1["visit_date"]);
                $visit_date = $row1['visit_date'] = date('d-m-Y', $visit_date);
                $client_feedback = $row1['client_feedback'];
                $next_folloup_date = strtotime($row1["next_folloup_date"]);
                $next_folloup_date = date('d-m-Y', $next_folloup_date);
                $exname = $row1['user_name'];
                $status = $row1['status'];
                $client_strength = $row1['client_strength'];
                ?>
                <tr>
                    <td class="form_left"><?php
                    if($user_id == $row1['user_id'])
                    {
                        echo 'ME';
                    }
                    else
                    {
                        echo $exname;
                    }
                    ?></td>
                    <td class="center"><?php echo $visit_date.', '.$row1['visit_time'];?></td>
                    <td><?php echo $client_feedback;?></td>
                    <td class="center"><?php echo $next_folloup_date.', '.$row1['followup_time'];?></td>
                    <td class="center"><?php echo $status;?></td>
                    <td class="center">
                        <?php
                        if($row1['user_id'] == $user_id && $inc == 0 && strtotime($row1['next_folloup_date']) >= strtotime(date('Y-m-d')) )
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round" href="Edit-Remark.php?client_id='.$client_id.'&requirement_id='.$requirement_id.'&visit_id='.$row1['visit_id'].'"><i class="fa fa-pencil"></i></a>';
                        }
                        else
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round disabled" ><i class="fa fa-pencil"></i></a>';
                        }
                        ?>        
                    </td>
                </tr>
                <?php
                $inc++;
            }
        }
        ?>
    </table>
</div>
<div class="table-responsive">
    <h2 class="cursor_default btn btn-success btn-round btn-xs" style="margin-top: 10px;" > <b>Appointment History</b></h2>
    <table id="example<?php echo $rand_num_2; ?>" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
            <tr>
                <td style="width: 20%">Executive</td>
                <td style="width: 15%">Appointment Date</td>
                <td style="width: 40%">Location Details</td>
                <td style="width: 15%">Appointment With</td>
                <td style="width: 7%">Time</td>
                <td style="width: 3%">Edit</td>
            </tr>
        </thead>
        <?php
        $dt=date("Y-m-d");
        $cmd1 = "SELECT l.visit_id, l.apointment_id, l.meeting_with, l.user_id, l.apointment_id,
         l.appointment_date, l.from_time, l.end_time, l.location_type, l.location_details, l.agenda,
          l.status, e.emp_name AS user_with, em.emp_name AS executive FROM appointment_master l 
          INNER JOIN user_master u ON u.user_id = l.meeting_with 
          inner join employee_master e on e.emp_id = u.emp_id
          INNER JOIN user_master m ON m.user_id = l.user_id 
          inner join employee_master em on em.emp_id = m.emp_id
          WHERE l.client_id = $client_id AND 
          l.requirement_id = '$requirement_id' ORDER BY l.apointment_id DESC  ";
        $result1 = $connect->query($cmd1);
        if ($result1->num_rows > 0)
        {
            $inc = 0;
            while($row1 = $result1->fetch_assoc())
            {
                if(!$row1['apointment_id'] == "")
                {
                    $row1['apointment_id']; 
                    $appointment_dt = strtotime($row1["appointment_date"]);
                    $appointment_dt = date('d-m-Y', $appointment_dt);
                    $from_time=$row1['from_time'];
                    $end_time=$row1['end_time'];
                    $location_type=$row1['location_type'];
                    $location_details=$row1['location_details'];
                    $agendam=$row1['agenda'];
                    $status=$row1['status'];
                    $appointment_with=$row1['user_with'];
                    $executive=$row1['executive'];
                    ?>
                    <tr>
                        <td><?php
                        if($user_id == $row1['user_id'])
                        {
                            echo 'ME';
                        }
                        else
                        {
                            echo $executive;
                        }
                        ?></td>
                        <td class="center"><?php echo $appointment_dt;?></td>
                        <!-- <td class="center"><?php echo $location_type;?></td> -->
                        <td><?php echo $location_details;?></td>
                        <td><?php
                        if($user_id == $row1['meeting_with'])
                        {
                            echo 'ME';
                        }
                        else
                        {
                            echo $appointment_with;
                        }
                        ?></td>
                        <td class="center"><?php echo $from_time;?></td>
                        <td class="center">
                        <?php
                        if($row1['user_id'] == $user_id && $inc == 0 && strtotime($row1['appointment_date']) >= strtotime(date('Y-m-d')) )
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round" href="Edit-Remark.php?client_id='.$client_id.'&requirement_id='.$requirement_id.'&visit_id='.$row1['visit_id'].'&apointment_id='.$row1['apointment_id'].'"><i class="fa fa-pencil"></i></a>';
                        }
                        else
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round disabled" ><i class="fa fa-pencil"></i></a>';
                        }
                        ?>       
                        </td>
                    </tr>
                    <?php  
                }
                $inc++;
            }
        }
        ?>
    </table>
</div>
<div class="table-responsive">
    <h2 class="cursor_default btn btn-success btn-round btn-xs" style="margin-top: 10px;"> <b>Appointment Follow up</b></h2>
    <table id="example<?php echo $rand_num_3; ?>" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
            <tr>
                <td style="width: 20%">Executive</td>
                <td style="width: 15%">Visit Date & Time</td>
                <td style="width: 40%">Customer Feedback</td>
                <td style="width: 15%">Next Followup Date</td>
                <td style="width: 7%">Status</td>
                <td style="width: 3%">Edit</td>
            </tr>
        </thead>
        <?php
        $cmd1 = "SELECT l.apointment_id, u.visit_id, u.visit_time, u.followup_time, u.visit_date, 
        u.client_feedback, u.next_folloup_date, e.emp_name as user_name, l.status, u.client_strength, u.user_id 
        FROM user_visit_record u INNER JOIN appointment_master l ON l.visit_id = u.visit_id 
        INNER JOIN user_master m ON m.user_id = u.user_id 
        inner join employee_master e on e.emp_id = m.emp_id
        WHERE u.client_id='$client_id' AND l.status IN ('Done','Postponed','Upcomming','Cancel') AND 
        u.requirement_id = '$requirement_id' ORDER BY u.visit_id DESC ";
        $result1 = $connect->query($cmd1);
        if ($result1->num_rows > 0)
        {
            $inc = 0;
            while($row1 = $result1->fetch_assoc())
            {
                $visit_date = strtotime($row1["visit_date"]);
                $visit_date = $row1['visit_date'] = date('d-m-Y', $visit_date);  
                $client_feedback = $row1['client_feedback'];
                $next_folloup_date = strtotime($row1["next_folloup_date"]);
                $next_folloup_date = date('d-m-Y', $next_folloup_date);  
                $exname = $row1['user_name']; 
                $status = $row1['status'];
                $client_strength = $row1['client_strength'];
                ?>
                <tr>
                    <td><?php echo $exname;?></td>
                    <td class="center"><?php echo $visit_date.', '.$row1['visit_time'];?></td>
                    <td><?php echo $client_feedback;?></td>
                    <td class="center"><?php echo $next_folloup_date.', '.$row1['followup_time'];?></td>
                    <td class="center"><?php echo $status;?></td>
                    <td class="center">
                        <?php 
                        if($row1['user_id'] == $user_id && $inc == 0 && strtotime($row1['next_folloup_date']) >= strtotime(date('Y-m-d')) )
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round" href="Edit-Remark.php?client_id='.$client_id.'&requirement_id='.$requirement_id.'&visit_id='.$row1['visit_id'].'&apointment_id_visit='.$row1['apointment_id'].'"><i class="fa fa-pencil"></i></a>';
                        }
                        else
                        {
                            echo '<a class="btn btn-warning btn-xs btn-round disabled" ><i class="fa fa-pencil"></i></a>';
                        }
                        ?>
                    </td>
                </tr>
                <?php
                $inc++;
            }
        }
        ?>
    </table>
</div>

<script type="text/javascript">
    function contact_toggle(client_contact_id)
    {
        $('.contact_toggle').css('display', 'none');
        var client_contact_id = client_contact_id;
        $("#contact_toggle_"+client_contact_id).toggle(100);
    }
</script>