<?php
include 'configuration.php';

$client_id = intval($_GET['id']);

$cmd = "SELECT * FROM view_lead_data WHERE client_id = '$client_id' AND visit_type ='Fresh' ";
$result = $connect->query($cmd);
if ($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $firm_name = $row['firm_name'];
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
        $business_type1=$row['business_type'];
        $client_email2=$row['client_email2'];
        $enq_type = $row['enq_type'];
        $po_received_date = date('d-m-Y', strtotime($row['po_received_date']));
        $po_received_document = $row['po_received_document'];
        $office_city_id = $row['office_city_id'];
        $office_area_id = $row['office_area_id'];
        $office_pincode = $row['office_pincode'];
    }
}

$cmd = "SELECT requirement_details, client_status, requirement_id FROM client_requirement_details WHERE client_id = '$client_id' ";
$result = $connect->query($cmd);
if ($result->num_rows > 0)
{
    $inc = 1;
    echo '
    <table cellpadding="0" cellspacing="0" border="0" class="display table-bordered table-hover table-condensed tablesaw tablesaw-stack table table-striped jambo_table bulk_action" width="100%">
        <thead>
            <tr>
                <td style="width:15px;">SR.NO.</td>
                <td>Requirements</td>
                <td style="width:45px;">Contact</td>
                <td style="width:25px;">Status</td>
                <td style="width:15px;">Details</td>
            </tr>
        </thead>
    ';
    while($row = $result->fetch_assoc())
    {
        $client_status = $row['client_status'];
        if($client_status=="Hot"){
            $client_status = "<a class='cursor_default btn btn-round btn-danger btn-sm bg-hot'>$client_status</a>";
        } else if($client_status=="Warm"){
            $client_status = "<a class='cursor_default btn btn-round btn-warning btn-sm bg-warm'>$client_status</a>";
        } else if($client_status=="Cold"){
            $client_status = "<a class='cursor_default btn btn-round btn-info btn-sm bg-cold'>$client_status</a>";
        } else if($client_status=="Quotation"){
            $client_status = "<a class='cursor_default btn btn-round btn-primary btn-sm bg-quotation'>$client_status</a>";
        } else if($client_status=="Requested"){
            $client_status = "<a class='cursor_default btn btn-round btn-dark btn-sm bg-quotation'>$client_status</a>";
        } else if($client_status=="Given"){
            $client_status = "<a class='cursor_default btn btn-round btn-success btn-sm bg-quotation'>$client_status</a>";
        } else if($client_status=="Revised"){
            $client_status = "<a class='cursor_default btn btn-round btn-danger btn-sm bg-quotation'>$client_status</a>";
        } else if($client_status=="Raw"){
            $client_status = "<a class='cursor_default btn btn-round btn-danger btn-sm bg-raw'>$client_status</a>";
        } else if($client_status=="PO Received"){
            $client_status = "<a class='cursor_default btn btn-round btn-danger btn-sm bg-close'>$client_status</a>";
        }
        $RequirementDiv = '';
      
        echo '
        <tr>
            <td class="center">'.$inc.'</td>
            <td>
                '.$row['requirement_details'].'
                <a class="btn btn-default btn-xs btn-round" onclick="show_edit_requirement('.$client_id.','.$row['requirement_id'].')"><i class="fa fa-pencil"></i></a>
                <div class="edit_requirements" id="edit_requirements_'.$client_id.'_'.$row['requirement_id'].'">
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <select class="form-control" id="requirement_details_' . $client_id . '_' . $row['requirement_id'] . '">
                                ' . $RequirementDiv . '
                            </select>
                            
                        </div>
                        <div class="col-md-3 ">
                            <a onclick="update_requirement_details('.$client_id.','.$row['requirement_id'].')" class="btn btn-warning btn-sm btn-round"><i class="fa fa-save"></i> Update</a>
                        </div>
                        <div class="col-md-1">
                            <a onclick="close_edit_requirement('.$client_id.','.$row['requirement_id'].')" class="btn btn-danger btn-sm btn-round"><i class="fa fa-remove"></i></a>
                        </div>
                    </div>
                </div>
            </td>
            <td class="center"><a class="btn btn-warning btn-sm btn-round" onclick="update_contact_details_req('.$client_id.','.$row['requirement_id'].')"><i class="fa fa-edit"></i></a></td>
            <td class="center">'.$client_status.'</td>
            <td class="center"><a title="View Client" href="javascript:view_client_history('.$client_id.', '.$row["requirement_id"].')" class="btn btn-dark btn-sm btn-round"><i class="fa fa-eye"></i></a></td>
        </tr>
        ';
        $inc++;
    }
}
?>
<script type="text/javascript">
    function close_edit_requirement(client_id, requirement_id)
    {
        $('#edit_requirements_'+client_id+'_'+requirement_id).css('display', 'none');
    }
    function show_edit_requirement(client_id, requirement_id)
    {
        $('#edit_requirements_'+client_id+'_'+requirement_id).css('display', 'block');
    }
    
    function update_requirement_details(client_id, requirement_id)
    {
        var requirement_details = $('#requirement_details_'+client_id+'_'+requirement_id).val();
        if(requirement_details == "")
        {
            alert('Please enter requirement...');
        }
        else
        {
            $('#modal_loading').css('display', 'block');
            $.ajax({
                type:'POST',
                url:'component.php',
                data:{client_id_update:client_id, requirement_id_update:requirement_id, requirement_details_update:requirement_details},
                success:function(html){
                    $('#print_result').html(html);
                    $('#modal_loading').css('display', 'none');
                }
            });
        }
    }
    
</script>