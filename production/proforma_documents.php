
<?php include_once('header.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />

<style>
 /* .btn-group{
    height: 37px;
  }*/

</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordProForma"></div>     
            </div>
        </div>
      </div>
  </div>
</div>     
<?php
  include_once('footer.php');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" ></script>

<div class="modal fade" id="ShipmentDocumentModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="ShipmentDocumentHead">Follow-Up</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <form id="frmshipmentdocuments" enctype="multipart/form-data" >
                    <div class="row" style=" margin-top: -30px;"> 
                        <div class="col-md-4">
                            <input type="hidden" class="form-control" id="shipmentPINo" name="shipmentPINo">
                            <label for="">Select Document</label>
                            <select name="txtdocumentid" id="txtdocumentid" class="form-control form-select">
                                
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Document</label>
                            <input type="file" id="txtshipmentdocumentproof" name="txtshipmentdocumentproof" class="form-control dropify"  data-height="70">
                        </div>
                        <div class="col-md-4 mt-3">
                            <button type="button" class="btn btn-info btn-round btn-sm" onclick="LoadImagesIndiv();"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                    <div class="row mt-3" id="divLoadshippemtndocuments"></div>
                    <div class="col-md-3 mt-2">
                        <input type="submit" class="btn btn-primary btn-sm" value="SAVE">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="LoadCOAProductModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="COAProductModalHead">Add Product Specification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <form id="frmCOAProductSave" enctype="multipart/form-data" >
                    <input type="hidden" class="form-control" id="COAProductPiNo" name="COAProductPiNo">
                    <input type="hidden" class="form-control" id="COAProductSpecification" name="COAProductSpecification">
                    <div class="row" style=" margin-top: -30px;" id="LoadCOAProductSpecificationDiv"></div>
                    <div class="col-md-3 mt-2">
                        <input type="submit" class="btn btn-primary btn-sm" value="SAVE">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="taxInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="taxInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate Tax Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="taxInvoiceForm">
          <div class="form-group">
            <label for="bl_number">B/L Number</label>
            <input type="text" class="form-control" id="bl_number" placeholder="Enter B/L Number">
          </div>
          <div class="form-group">
            <label for="vessel_name">Vessel Name</label>
            <input type="text" class="form-control" id="vessel_name" placeholder="Enter Vessel Name">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="GenerateTaxInvoice()">Generate</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    ShowProFormaList();
    $("#AllCheckBox").on('click', function() {
        console.log(checkBoxes);
        var checkBoxes = $("input[name='check[]']");
        if ($(this).prop("checked")) {
            checkBoxes.prop("checked", true);
        } else {
            checkBoxes.prop("checked", false);
        }
    });

    $(".dropify").dropify();

    $("#frmshipmentdocuments").submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('Flag','SaveSalesDocuments');
        documentFiles.forEach((entry) => {
            if (entry) {
                formData.append('document_files[]', entry.file);
            }
        });
        $.ajax({
            url: "operation/pro_forma_operation.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
               if(response=="Inserted")
                {
                    Swal.fire(
                        'Well Done!',
                        'Sales Documents Added Successfully',
                        'success'
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }else{
                    Swal.fire(
                        'oops',
                        response,
                        'error'
                    );
                }
            }
        });
    });

    $("#frmCOAProductSave").submit(function(e){
        e.preventDefault();
        var PI_no = $("#COAProductPiNo").val();
        var COAProductSpecificationFlag = $("#COAProductSpecification").val();

        if (isNaN(COAProductSpecificationFlag) || COAProductSpecificationFlag <= 0) {
            Swal.fire({
                title: 'Action Denied',
                text: 'Product specification details are missing. Please add specifications before generating the COA print.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        var formData = new FormData(this); // Use FormData instead of serialize
        formData.append("Flag", "SaveCOAProductUpdate");

        let tableData = [];
        $("#tblproductdetails tbody tr").each(function () {
            const row = $(this);
            const productId = row.find("td:eq(0)").attr("value");
            const selectedDescriptions = [];
            const selectedDescriptionsStdValue = [];
            const selectedDescriptionsValue = [];
            row.find("input.checkprodspecification:checked").each(function () {
                var descriptionId = $(this).val();
                var descResultValue = $("#descResultValue_"+descriptionId).val();
                var descStdValye = $("#descSTDValue_"+descriptionId).val();
                selectedDescriptions.push(descriptionId);
                selectedDescriptionsValue.push(descResultValue);
                selectedDescriptionsStdValue.push(descStdValye);
            });
            const rowData = {
                product_id: productId,
                descriptions: selectedDescriptions,
                selectedDescriptionsStdValue: selectedDescriptionsStdValue,
                selectedDescriptionsValue: selectedDescriptionsValue
            };

            tableData.push(rowData);
        });

        if(tableData.length=="0" || tableData=="[]")
        {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Please Add At least One Product",
            }).then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    $("#product_id").focus();
                }
            });
        }
        else
        {
             Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to generate the print?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, generate it!',
                cancelButtonText: 'No, cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    formData.append("tableData", JSON.stringify(tableData));
                    $.ajax({
                        url:"operation/pro_forma_operation.php",
                        type: "POST", 
                        data: formData,
                        contentType: false, 
                        processData: false, 
                        success: function (response) {
                            if(response=="Updated")
                            {
                                Swal.fire({
                                    title: 'Success',
                                    text: "Product Specification Updated",
                                    icon: 'success',
                                });
                                $("#LoadCOAProductModel").modal("toggle");
                                ShowProFormaList();
                            }
                            else
                            {
                                Swal.fire({
                                    title: 'OOps',
                                    text: response,
                                    icon: 'warning',
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log("AJAX Error: ", textStatus, errorThrown);
                            alert("An error occurred while submitting the data.");
                        }
                    });
                }
            });
        }
    });
});
function ShowProFormaList()
{
    $.post("operation/pro_forma_operation.php",{
          Flag:"ShowProFormaListForSalesDocument"
    },function(data,success){
          $("#divRecordProForma").html(data);
          $("#dtlRecord").DataTable({
                columnDefs: [
                    { orderable: false, targets: 0 } // 0 is the index of "Sr. No." column
                ],
                // dom: '<"d-flex justify-content-between"lfB>rtip', // l = Length, f = Filter (Search Box), B = Custom Button
                // buttons: [
                //     {
                //         text: '<i class="fa fa-plus"></i> Send Mail',
                //         className: 'btn btn-primary btn-sm btn-round',
                //         action: function () {
                //             SendSalesDocumentMail();
                //         }
                //     }
                // ]
          });
    });
}

function LoadDocumentModule(pi_no, client_name)
  {
    $("#ShipmentDocumentModel").modal("toggle");
    $("#shipmentPINo").val(pi_no);
    $("#ShipmentDocumentHead").html("Add Sales Document : "+client_name);
    loadDocumentList(pi_no);
    $("#divLoadshippemtndocuments").html('');
  }
  function loadDocumentList(pi_no)
  {
        $.post("operation/pro_forma_operation.php",{
            Flag:"loadPIDocumentList",
            pi_no:pi_no
        },function(data,success){
            $("#txtdocumentid").html(data);
        });
  }
  function UpdateShipmentDoc(pi_no, client_name)
  {
    $.ajax({
        url:"operation/pro_forma_operation.php",
        type: 'POST',
        data:{Flag:"UpdateSalesDocument",pi_no :pi_no},
        dataType: 'json',
        success:function(docs)
        {
            $("#divLoadshippemtndocuments").html('');
            $("#ShipmentDocumentModel").modal("toggle");
            $("#shipmentPINo").val(pi_no);
            $("#ShipmentDocumentHead").html("Update Shipment Document : "+client_name);
            loadDocumentList(pi_no);
            if (docs.length > 0) {
                if (!document.getElementById("shipmentDocTable")) {
                    document.getElementById("divLoadshippemtndocuments").innerHTML = `
                        <table class="table table-bordered" id="shipmentDocTable">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    `;
                }

                const tbody = document.querySelector("#shipmentDocTable tbody");
                docs.forEach(doc => {

                    documentFiles.push({
                        name: doc.document_name,
                        label: doc.document_name,
                        file: doc.document_path
                    });

                    const rowIndex = documentFiles.length - 1;
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>
                            ${doc.document_name}
                            <input type="hidden" name="document_names[]" value="${doc.document_name}">
                        </td>
                        <td>
                            <a href="production/${doc.document_path}" target="_blank">
                                <img src="production/${doc.document_path}" alt="${doc.document_name}" height="50">
                            </a>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="RemoveDocument(${rowIndex}, this, '${doc.document_name}');">Remove</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                    setTimeout(() => {
                         $("#txtdocumentid option[value='" + doc.document_name + "']").remove();
                    }, 1000);
                });
            }
        },
    });
  }
    let documentFiles = [];

    function LoadImagesIndiv() {
        const docSelect = document.getElementById("txtdocumentid");
        const docName = docSelect.value;
        const docText = docSelect.options[docSelect.selectedIndex].text;
        const fileInput = document.getElementById("txtshipmentdocumentproof");
        const file = fileInput.files[0];

        if (!docName || !file) {
            alert("Please select a document and choose a file.");
            return;
        }

        // Save in JS array for FormData
        documentFiles.push({
            name: docName,
            label: docText,
            file: file
        });

        // Create table if not exists
        let tableWrapper = document.getElementById("divLoadshippemtndocuments");
        if (!document.getElementById("shipmentDocTable")) {
            tableWrapper.innerHTML = `
                <table class="table table-bordered" id="shipmentDocTable">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>File</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `;
        }

        const tbody = document.querySelector("#shipmentDocTable tbody");
        const rowIndex = documentFiles.length - 1;
        const row = document.createElement("tr");

        const reader = new FileReader();
        reader.onload = function (e) {
            row.innerHTML = `
                <td>
                    ${docText}
                    <input type="hidden" name="document_names[]" value="${docName}">
                </td>
                <td>
                    <span style="display:none;">${file.name}</span>
                    <img src="${e.target.result}" alt="Uploaded" height="50">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="RemoveDocument(${rowIndex}, this, '${docName}')">Remove</button>
                </td>
            `;
            tbody.appendChild(row);
        };
        reader.readAsDataURL(file);

        // Remove option from dropdown
        docSelect.remove(docSelect.selectedIndex);
        docSelect.selectedIndex = 0;

        // Clear Dropify
        let drEvent = $('#txtshipmentdocumentproof').data('dropify');
        drEvent.resetPreview();
        drEvent.clearElement();
    }

    function RemoveDocument(index, button, docName) {
        documentFiles[index] = null; // Mark for removal
        const docSelect = document.getElementById("txtdocumentid");
        const option = document.createElement("option");
        option.value = docName;
        option.text = docName;
        docSelect.appendChild(option);
        button.closest('tr').remove();
    }
    function SendSalesDocumentMail(pi_no)
    {
        var c=confirm("Do You really want to send mail?");
        if(c==true)
        {
            $.ajax({
                url: 'operation/pro_forma_operation.php', // replace with your actual endpoint
                type: 'POST',
                data: { pi_no: pi_no,"Flag":"SendSalesDocumentMail" },
                success: function (response) {
                    swal.fire(
                        'Success',
                        'Mail Sent Successfully',
                        'success'
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function (xhr) {
                    swal.fire(
                        'Oops',
                        'Failed to send mail.',
                        'error'
                    );
                }
            });
        }
    }
    function SendForSalesChecking(pi_no)
    {
         var c=confirm("Do You really want to send for Checking?");
        if(c==true)
        {
            $.ajax({
                url: 'operation/pro_forma_operation.php', // replace with your actual endpoint
                type: 'POST',
                data: { pi_no: pi_no,"Flag":"SendForSalesChecking" },
                success: function (response) {
                    if(response=="Success")
                    {
                        swal.fire(
                            'Success',
                            'Documents Send For Checking',
                            'success'
                        );
                    }else{
                        swal.fire(
                            'Oops',
                            'Unable To Send',
                            'error'
                        );
                    }
                    ShowProFormaList();
                },
            
            });
        }
    }
    function GeneratePrints(pi_no, client_name, print_name) {
        if (print_name !== '') {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to generate the print?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, generate it!',
                cancelButtonText: 'No, cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open("print_" + print_name + ".php?PI_No=" + btoa(pi_no), '_blank');
                    setTimeout(() => {
                        ShowProFormaList();
                    }, 1000);
                }
            });
        }
    }
    let current_pi_no = '';
    function LoadTaxInvoiceModal(pi_no, client_name)
    {
        current_pi_no = pi_no;
        $('#bl_number').val('');
        $('#vessel_name').val('');
        $('#taxInvoiceModal').modal('show');
    }
    function GenerateTaxInvoice()
    {
        const bl_number = $('#bl_number').val().trim();
        const vessel_name = $('#vessel_name').val().trim();

        if (!bl_number || !vessel_name) {
            Swal.fire('Warning', 'Please fill all required fields', 'warning');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to generate Tax Invoice?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, generate it!',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("operation/pro_forma_operation.php", {
                    Flag: "GenerateTaxInvoice",
                    pi_no: current_pi_no,
                    bl_number: bl_number,
                    vessel_name: vessel_name
                }, function (data, success) {
                    if (data === "Success") {
                        Swal.fire('Success', 'Tax Invoice Generated Successfully', 'success');
                        $('#taxInvoiceModal').modal('hide');
                        setTimeout(() => {
                            window.open("print_tax_invoice.php?PI_No=" + btoa(current_pi_no), '_blank');
                        }, 1000);
                    } else {
                        Swal.fire('Oops', 'Unable to Generate', 'error');
                    }
                    ShowProFormaList();
                });
            }
        });
    }
    function GenerateCOAPrint(pi_no, client_name)
    {
        $("#LoadCOAProductModel").modal("toggle");
        $("#COAProductPiNo").val(pi_no);
        $("#COAProductModalHead").html("Add Product Specification : "+client_name);
        $.post("operation/pro_forma_operation.php",{
            Flag:"LoadCOAProductDetails",
            pi_no:pi_no
        },function(data,success){
            var res = JSON.parse(data);
            $("#LoadCOAProductSpecificationDiv").html(res.coaproductdetails);
            $("#COAProductSpecification").val(res.IsSpecificationFlag);
        })
    }
    $(document).on('change', '.checkprodspecification', function() {
    let descId = $(this).val();
    if ($(this).is(':checked')) {
        $('#spec_inputs_' + descId).css('display','block');
    } else {
        $('#spec_inputs_' + descId).css('display','none');
    }
});

   

</script>