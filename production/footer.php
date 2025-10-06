<!-- footer content -->
			<footer>
				<div class="pull-right">
					Designed & Developed By <a href="https://tejitsolutions.com/" target="_blank">Tej IT Solutions Pvt Ltd</a>
				</div>
				<div class="clearfix"></div>
			</footer>
			<!-- /footer content -->
		</div>
	</div>

	<!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="../vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="../vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="../vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="../vendors/Flot/jquery.flot.js"></script>
    <script src="../vendors/Flot/jquery.flot.pie.js"></script>
    <script src="../vendors/Flot/jquery.flot.time.js"></script>
    <script src="../vendors/Flot/jquery.flot.stack.js"></script>
    <script src="../vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="../vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="../vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="../vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="../vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="../vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="../vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="../vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    
    <!-- Bootstrap -->
   <script src="../vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- Datatables -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="modal fade" id="FollowUpHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="font-size: 18px;" id="FollowUpHistoryModalHeading">Follow-Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr>
                <div class="modal-body">
                    <div class="row"> 
                        <div class="table-responsive" id="DivFollowupHistory" style="margin-top: -25px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="FollowUpNewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="font-size: 18px;" id="FollowUpModalHeading">Follow-Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr>
                <div class="modal-body">
                    <form id="frmFollowUpDate">
                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="col-md-12" style="margin-top: -25px;">
                                    <label for="">Call Status</label>
                                    <input type="hidden" id="HiddenFollowuprequirement_id" name="HiddenFollowuprequirement_id" class="form-control">
                                    <input type="hidden" id="HiddenClientId" name="HiddenClientId" class="form-control">
                                    <select id="FollowUpCallStatus" name="FollowUpCallStatus" class="form-control required" required>
                                        <option value="" data-attr="">Select Option</option>
                                        <?php
                                            $rstFollowStatus = mysqli_query($connect,"select * from tblfollowupmaster where FollowUpFor='Lead' and status='Active'");
                                            while($rwFollowup = mysqli_fetch_assoc($rstFollowStatus))
                                            {
                                                $FollowUpLabel = $rwFollowup['FollowUpLabel'];
                                                echo "<option value='$FollowUpLabel'>$FollowUpLabel</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Follow-Up Remark</label>
                                    <textarea id="FollowupRemarl" name="FollowupRemarl" class="form-control required" required rows="3"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Follow Up Status</label>
                                    <select id="FollowupSttaus" name="FollowupSttaus" class="form-control required" required>
                                        <option value="" data-attr="">Select Option</option>
                                        <?php
                                            $rstFollowStatus = mysqli_query($connect,"select * from leadstatus_master where status='Active'");
                                            while($rwFollowup = mysqli_fetch_assoc($rstFollowStatus))
                                            {
                                                $lead_status = $rwFollowup['lead_status'];
                                                echo "<option value='$lead_status'>$lead_status</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Next Follow-Up Date</label>
                                    <input type="date" id="Followupdate" name="Followupdate" class="form-control required" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="">Next Follow-Up Time</label>
                                    <input type="time" id="FollowupTime" name="FollowupTime" class="form-control required" required>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <input type="submit" class="btn btn-primary btn-sm" value="SAVE">
                                </div>
                            </div>
                            <div class="col-md-8 table-responsive" style="margin-top: -5px;">
                                <div id="DivFollowupRecords"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<div id="spinner-wrapper">
    <div class="spinner-grow text-success" role="status">
        <span class="visually-hidden"></span>
    </div>
</div>
</body></html>
<script>
    function DeleteFunction(TableName, Status, CompareField, CompareId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change the status?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, send the AJAX request
                $.post("operation/CrudOperation.php", {
                    Flag: "UpdateStatus",
                    TableName: TableName,
                    Status: Status,
                    CompareField:CompareField,
                    CompareId: CompareId
                },function(data,success){
                    if(data=="Updated")
                    {
                        Swal.fire(
                            'Changed!',
                            'The status has been changed.',
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
                            'There was an issue changing the status.',
                            'error'
                        );
                    }
                });
            }
        });
    }
    function get_this_id(branchid)
    {
      $('#modal_loading').css('display', 'block');
      $.ajax({
          type:'POST',
          url:'../ChangeUser.php',
          data: {branchid:branchid},
          success:function(html){
          $('#result').html(html);
          window.location.href='index.php';
          }
      }); 
    }
</script>

<script>
    function showSpinner() {
        document.getElementById('spinner-wrapper').classList.add('active');
    }
    function hideSpinner() {
        document.getElementById('spinner-wrapper').classList.remove('active');
    }
    $(document).ready(function(){
        $("#frmFollowUpDate").submit(function(event){
            event.preventDefault();
            var formData = new FormData(this);
            formData.append("Flag", "AddFollowupDetails");
            showSpinner();
            $.ajax({
                url: 'operation/followupoperation.php', // Server-side script to handle form submission
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    hideSpinner();
                    $("#FollowUpNewModal").modal("toggle");
                    if(response=="Added")
                    {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: "New Follow-Up Added Successfully",
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response,
                        });
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    $("#HiddenFollowuprequirement_id").val('');
                    $("#HiddenClientId").val('');
                    $("#FollowUpCallStatus").val('');
                    $("#FollowupRemarl").val('');
                    $("#Followupdate").val('');
                    $("#FollowupTime").val('');
                    $("#FollowupSttaus").val('');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error: " + textStatus + " - " + errorThrown);
                }
            });
        });
    });
    function hideshow(){
        var password = document.getElementById("txtpassword");
        var slash = document.getElementById("slash");
        var eye = document.getElementById("eye");
        
        if(password.type === 'password'){
            password.type = "text";
            slash.style.display = "block";
            eye.style.display = "none";
        }
        else{
            password.type = "password";
            slash.style.display = "none";
            eye.style.display = "block";
        }
    }
    function AddFollowup(client_id,requirement_id,client_name)
    {
        $("#FollowUpNewModal").modal("toggle");
        $("#FollowUpModalHeading").html('Add Follow Up (<small style="font-size: 55%; font-weight: bold;">' + client_name + '</small>)');
        $("#HiddenClientId").val(client_id);
        $("#HiddenFollowuprequirement_id").val(client_id);
        LoadFollowupDetails(client_id, requirement_id);
    }
    function LoadFollowupDetails(client_id, requirement_id)
    {
        $.post("operation/followupoperation.php",{
            Flag:"LoadFollowupDetails",
            client_id:client_id,
            requirement_id:requirement_id
        },function(data,success){
            $("#DivFollowupRecords").html(data);
            // $("#tblFollowUpShort").DataTable({ });
        });
    }
    function ShowFollowupHistory(client_id, requirement_id,client_name)
    {
        $.post("operation/followupoperation.php",{
            Flag:"LoadFollowupHistory",
            client_id:client_id,
            requirement_id:requirement_id
        },function(data,success){
            $("#FollowUpHistoryModal").modal("toggle");
            $("#FollowUpHistoryModalHeading").html('Follow Up Details (<small style="font-size: 55%; font-weight: bold;">' + client_name + '</small>)');
            $("#DivFollowupHistory").html(data);
            // $("#tblFollowUpHistory").DataTable({ });
            if (!$("#tblFollowUpHistory tfoot").length) {
                $("#tblFollowUpHistory").append('<tfoot><tr></tr></tfoot>');
                $("#tblFollowUpHistory thead th").each(function (index) {
                    if (index === 0) {
                        $("#tblFollowUpHistory tfoot tr").append("<th></th>"); // Empty column for the first one
                    } else {
                        var title = $(this).text();
                        $("#tblFollowUpHistory tfoot tr").append('<th><input type="text" placeholder="Search ' + title + '" /></th>');
                    }
                });
            }

            var table = $("#tblFollowUpHistory").DataTable({
                destroy: true, // Destroy existing DataTable before reinitializing
                initComplete: function () {
                    // Apply the search
                    this.api().columns().every(function () {
                        var that = this;
                        $("input", this.footer()).on("keyup change", function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                }
            });
        });
    }

</script>
