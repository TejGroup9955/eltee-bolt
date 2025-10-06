
<?php 
include_once('header.php'); 
include_once('../configuration.php'); 
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" />

<style>
 /* .btn-group{
    height: 37px;
  }*/
  #dtlRecord_filter{
    /* margin-right: -450px; */
  }
  .form-check-input {
    transform: scale(1.5);
    margin-right: 10px;
  }

  .form-check-label {
    font-size: 18px;
    font-weight: bold;
    color: #444;
  }

  .radio-label {
    color: #007bff;
    transition: color 0.3s ease-in-out;
  }

  .form-check-input:checked + .form-check-label .radio-label {
    color: #28a745;
  }

  .form-check-inline {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    transition: transform 0.3s;
  }

  .form-check-inline:hover {
    transform: scale(1.05);
    border-color: #007bff;
  }
</style>
<div class="right_col" role="main">
  <div class="container-xxl flex-grow-1">
      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive" id="divRecordPurchaseOrder"></div>     
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
                            <input type="hidden" class="form-control" id="shipmentPoId" name="shipmentPoId">
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
<div class="modal fade" id="LinkPIModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-size: 18px;" id="LinkPIModalHead"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <form id="frmlinkPI" enctype="multipart/form-data" >
                    <input type="hidden" class="form-control" id="LinkPI_PoId" name="LinkPI_PoId">
                    <div class="row" style=" margin-top: -45px;" id="divLinkPIData"> 
                           
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
  $(document).ready(function(){
    ShowPurchaseOrderList();
    $(".dropify").dropify();
    $("#frmshipmentdocuments").submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('Flag','SaveShipmentDocuments');
        documentFiles.forEach((entry) => {
            if (entry) {
                formData.append('document_files[]', entry.file);
            }
        });
        $.ajax({
            url: "operation/shipment_operation.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
               if(response=="Inserted")
                {
                    Swal.fire(
                        'Well Done!',
                        'Shipment Documents Added Successfully',
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

   });
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
  function ShowPurchaseOrderList()
  {
      $.post("operation/purchase_order_operation.php",{
            Flag:"showPoDetailsForShipment"
      },function(data,success){
            $("#divRecordPurchaseOrder").html(data);
            $("#dtlRecord").DataTable({ });
      });
  }
  function LoadDocumentModule(po_id, client_name)
  {
    $("#ShipmentDocumentModel").modal("toggle");
    $("#shipmentPoId").val(po_id);
    $("#ShipmentDocumentHead").html("Add Shipment Document : "+client_name);
    $("#divLoadshippemtndocuments").html('');
    loadDocumentList(po_id);
  }
  function loadDocumentList(po_id)
  {
        $.post("operation/shipment_operation.php",{
            Flag:"loadPODocumentList",
            po_id:po_id
        },function(data,success){
            $("#txtdocumentid").html(data);
        });
  }
  function UpdateShipmentDoc(po_id, client_name)
  {
    $.ajax({
        url:"operation/shipment_operation.php",
        type: 'POST',
        data:{Flag:"UpdateShipmentDocument",po_id :po_id},
        dataType: 'json',
        success:function(docs)
        {
            console.log(docs);
            $("#divLoadshippemtndocuments").html('');
            $("#ShipmentDocumentModel").modal("toggle");
            $("#shipmentPoId").val(po_id);
            $("#ShipmentDocumentHead").html("Update Shipment Document : "+client_name);
            loadDocumentList(po_id);
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
                // docs.forEach(doc => {

                //     documentFiles.push({
                //         name: doc.document_name,
                //         label: doc.document_name,
                //         file: doc.document_path
                //     });

                //     const rowIndex = documentFiles.length - 1;
                //     const row = document.createElement("tr");
                //     row.innerHTML = `
                //         <td>
                //             ${doc.document_name}
                //             <input type="hidden" name="document_names[]" value="${doc.document_name}">
                //             <input type="hidden" name="shipment_document_id[]" value="${doc.shipment_document_id}">
                //         </td>
                //         <td>
                //             <a href="production/${doc.document_path}" target="_blank">
                //                 <img src="production/${doc.document_path}" alt="${doc.document_name}" height="50">
                //             </a>
                //         </td>
                //         <td>
                //             <button type="button" class="btn btn-danger btn-sm" onclick="RemoveDocument(${rowIndex}, this, '${doc.document_name}');">Remove</button>
                //         </td>
                //     `;
                //     tbody.appendChild(row);
                //     setTimeout(() => {
                //          $("#txtdocumentid option[value='" + doc.document_name + "']").remove();
                //     }, 1000);
                // });

                docs.forEach(doc => {
                    fetchFile(doc.document_path).then(file => {
                        documentFiles.push({
                            name: doc.document_name,
                            label: doc.document_name,
                            file: file
                        });

                        const rowIndex = documentFiles.length - 1;
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>
                                ${doc.document_name}
                                <input type="hidden" name="document_names[]" value="${doc.document_name}">
                                <input type="hidden" name="shipment_document_id[]" value="${doc.shipment_document_id}">
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
                    });

                    setTimeout(() => {
                        $("#txtdocumentid option[value='" + doc.document_name + "']").remove();
                    }, 1000);
                });
            }
        },
    });
  }
  function LoadPILInkModule(po_id, client_name){
        $("#LinkPIModel").modal("toggle");
        $("#LinkPI_PoId").val(po_id);
        $("#LinkPIModalHead").html("Link PI For Client : "+client_name);
        $.post("operation/shipment_operation.php",{
            Flag:"LoadLinkPIContainer",
            po_id : po_id
        },function(data,success){
            $("#divLinkPIData").html(data);
        });
  }
  function SavePILinking(purchase_shipment_detail_id)
  {
        var proformano = $("#txtproformano"+purchase_shipment_detail_id+ " option:selected").val();
        if(proformano=="")
        {
            alert("Please Select Pro Forma");
            $("#txtproformano"+purchase_shipment_detail_id).focus();
            return;
        }
        var c = confirm("Are You sure to link this PI ?");
        if(c==true)
        {
            $.post("operation/shipment_operation.php",{
                Flag:"SavePILinking",
                purchase_shipment_detail_id : purchase_shipment_detail_id,
                proformano:proformano
            },function(data,success){
                if(data=="Success")
                {
                    Swal.fire(
                        'Well Done!',
                        'Pi Linked Successfully',
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
            });
        }
  }
function fetchFile(filePath) {
    

    return new Promise((resolve, reject) => {
        const url = 'operation/get_file_binary.php?File_Path=' + filePath;
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('File not found');
                }
                return response.blob();  // Get the binary content of the file
            })
            .then(blob => {
                const file = new File([blob], filePath, { type: blob.type });
                console.log(file);
                resolve(file);  // Return the file object
            })
            .then(text => console.log(url)) 
            .catch(error => reject(error));
    });
}


</script>