<?php 
$page_heading="Lead Master";
include 'configuration.php';
include 'production/header.php';
$dt=date("Y-m-d");

extract($_POST);
if(isset($_POST['save_requirements']))
{
  $dd = substr($next_folloup_date, 0,2);
  $mm = substr($next_folloup_date, 3,2);
  $year = substr($next_folloup_date, 6,4);
  $next_folloup_date = $year."-".$mm."-".$dd; 
  $work_address = addslashes($work_address);
  $requirement_details = addslashes($requirement_details);

  $cmd_requirement = "INSERT INTO client_requirement_details (requirement_details, work_address, client_status, client_id, user_id, visit_id, business_type, current_status) VALUES('$requirement_details', '$work_address', '$client_status', '$client_id_req', '$executive', '0', '$business_type', '$current_status') ";
  mysqli_query($connect,$cmd_requirement);
  $requirement_id = mysqli_insert_id($connect);

  $cmd1 = "INSERT INTO user_visit_record (visit_date, visit_type, client_id, user_id, client_feedback,  next_folloup_date, visit_time, followup_time, requirement_id, status) VALUES('".date('Y-m-d')."','Repeat','" .$client_id_req. "','" .$user_id. "','" .$client_feedback. "','" .$next_folloup_date. "', '".date('H:i:s')."', '$followup_time', '$requirement_id', 'Fresh')";
  mysqli_query($connect,$cmd1);
  $visit_id = mysqli_insert_id($connect);

  $cmd_requirement = "UPDATE client_requirement_details SET visit_id = '$visit_id' WHERE requirement_id = '$requirement_id' AND client_id = '$client_id_req' ";
  mysqli_query($connect,$cmd_requirement);
  if(isset($product_id))
  {
    foreach ($product_id as $product_id_selected)
    {
      if($product_id_selected != "")
      {
        $cmd_requirement = "INSERT INTO client_product_details (client_id, requirement_id, product_id) VALUES('$client_id_req', '$requirement_id', '$product_id_selected') ";
        mysqli_query($connect,$cmd_requirement);
      }
    }
  }

  $i = 0;
  if(isset($contact_person_name))
  {
    foreach ($contact_person_name as $contact_person_name1)
    {    
      if($contact_person_name1 != "")
      {
        $cmd_contact = "INSERT INTO client_requirement_contact_master (client_id, requirement_id, contact_person_name, contact_person_mobile, designation, contact_person_email) VALUES ('$client_id_req', '$requirement_id', '$contact_person_name1', '$contact_person_mobile[$i]', '$designation[$i]', '$contact_person_email[$i]') ";
        mysqli_query($connect,$cmd_contact);
      }
      $i++;
    }
  }
  echo "<script>alert('Requirements added...');window.location.href='Lead-Summary.php';</script>";
}
?>
<style>
  /* .btn-group{
    height: 37px;
  } */
  #employee-grid_filter{
    margin-right: -655px;
  }
</style>
  <div class="right_col" role="main" >
    <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="table-responsive">
              <table id="employee-grid" cellpadding="0" cellspacing="0" border="0" class="display table-bordered  table table-striped " width="100%">
                <thead>
                  <tr>
                    <th style="width: 200px;">Supplier Name</th>
                    <th>Business</th>
                    <th>Products</th>
                    <th>Mobile No.</th>
                    <th>Office Address</th>
                    <th>Email</th>
                    <th style="width: 110px">
                      View More
                    </th>
                  </tr>
                </thead>
              </table>
            </div>     
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                  <h6 class="modal-title" id="deleteModalLabel">
                      <i class="fa fa-pencil"></i> Are you sure?
                  </h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="client_idDelete" id="client_idDelete">
                  <label>Enter Supplier delete reason:</label>
                  <textarea required class="form-control" name="remarkDelete" id="remarkDelete" placeholder="Enter Reason..." rows="3"></textarea>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary btn-sm" name="submit" onclick="deleteclientfunction();">
                      <i class="fa fa-plus"></i> Submit
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                      <i class="fa fa-close"></i> Cancel
                  </button>
              </div>
          </div>
      </div>
  </div>

  <form method="POST">
    <div id='view_client_modal' class='modal' style="top:-40px;height: auto;z-index: 9999999!important;">
      <div class='modal-content' style="width:60% !important;">
        <button type="button" onclick="close_report()" class="btn btn-danger btn-sm pull-right" data-dismiss="startupmodel"><i class="fa fa-remove"></i></button>
        <textarea type="text" name="client_id" id="jqValue" hidden=""></textarea>
        <div class="panel panel-default" style="margin:-20px;">
          <div class="panel-heading"><i class="fa fa-cogs"></i> Details</div>
        </div>   
        <br/> <br/>
        <div id="result"></div>
        <iframe id="client_requirement_contact" style="width: 100%;height: 99vh;border:none;"></iframe>
      </div>
    </div>
  </form>

  <?php include('production/footer.php'); ?>
  <script type="text/javascript" language="javascript" >
    $(document).ready(function() {
      var lead_summary = "lead_summary";
      var dataTable = $('#employee-grid').DataTable( {
        "processing": true,
        "serverSide": true,
        "bDestroy": true,       
        "columnDefs": [ {
          "targets": [0,1,2,3,4,5],
          "orderable": false,
          "searchable": false

        } ],  
        dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
        buttons: [
            {
                // text: '<i class="fa fa-plus"></i> New Supplier',
                // className: 'btn btn-primary btn-round btn-sm',
                // action: function () {
                //     window.location.href = 'Supplier-fresh-Visit.php';
                // }
            }
        ],
        "aLengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
        "iDisplayLength": 10,
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          var str = "."+aData+"";
          var enquire_from = str.substring(
            str.lastIndexOf("~") + 1, 
            str.lastIndexOf("_")
            );
          if(enquire_from == "Manual")
          {
            // $('td', nRow).css('font-weight', 'bold');
            // $('td', nRow).css('color', '#000');
          }
          else
          {
            // $('td', nRow).css('color', '#333');
          }
        },

        "ajax":{
          url :"operation/leadmaster_datagrid.php",
          type: "post",
          data: {lead_summary:lead_summary,LeadType:"Supplier"},
          error: function(){ 
            $(".employee-grid-error").html("");
            $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
            $("#employee-grid_processing").css("display","none");
          }
        }
      } );

      $("#employee-grid_filter").css("display","block"); 
      $('.search-input-text').on( 'blur', function () {
        var i =$(this).attr('data-column');
        var v =$(this).val();
        dataTable.columns(i).search(v).draw();
      } );

      $('.search-input-select').on( 'change', function () {                
        var i =$(this).attr('data-column');                     
        var v =$(this).val();  
        dataTable.columns(i).search(v).draw();
      } );

      $('.search-input-select').on( 'change', function () {                
        var i =$(this).attr('data-column');                     
        var v =$(this).val();                   
        dataTable.columns(i).search(v).draw();
      } );
    } );
  </script>
  <script type="text/javascript">
    var view_client_modal = document.getElementById('view_client_modal');
    var temp={};

    function view_client(id)
    {
      $('#client_requirement_contact').css('display', 'none');
      document.getElementById('client_requirement_contact').src = 'about:blank';
      $('#modal_loading').css('display', 'block');
      var id = id;
      $.ajax({
        type:'GET',
        url:'View-Client-Requirements.php',
        data: {id:id},
        success:function(html){
          $('#result').html(html);
          $('#modal_loading').css('display', 'none');
          view_client_modal.style.display = 'block';
        }
      });
    }

    var i = 1;
    function add_requirements(client_id)
    {
      $('#contact_person').val('');
      var add_requirements_modal = document.getElementById('add_requirements_modal');
      add_requirements_modal.style.display = 'block';
      $('#client_id_req').val(client_id);
    }
    function deleteclientfunction() {
      var client_id = $("#client_idDelete").val();
      var remark = $("#remarkDelete").val();
      if(remark=="")
      {
        Swal.fire(
              'Oops!',
              'Please Enter Remark.',
              'error'
          );
      }
      else
      {
        Swal.fire({
        title: 'Are you sure?',
            text: "Do you want to Delete this Client?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../component.php", {
                    Flag: "DeleteClient",
                    client_id: client_id,
                    remark: remark,
                },function(data,success){
                    if(data=="Updated")
                    {
                        Swal.fire(
                            'Well Done!',
                            'Supplier Deleted Successfully.',
                            'success'
                        );
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                    else
                    {
                        Swal.fire(
                            'Error!',
                            'Unable to Delete',
                            'error'
                        );
                    }
                });
            }
        });
      }
    }
    function delete_client(id)       
    {
        $("#delete_modal").modal("toggle");  
        $("#client_idDelete").val(id);
    }
  </script>