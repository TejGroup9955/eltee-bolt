<?php
include_once('header.php');
?>

<style>
    /* Global Styling */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
    }

    /* Custom Styling for Form Controls */
    .form-control {
        width: 100%;
        max-width: 350px;
        border-radius: 10px;
        padding: 10px;
        border: 1px solid #ddd;
    }

    /* Header Styling */
    h2 {
        font-weight: bold;
        color: #007bff;
        text-align: center;
        margin-bottom: 20px;
    }

    h3 {
        color: #555;
        font-weight: 500;
        margin-bottom: 15px;
    }

    /* Table Styling */
    table {
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
        width: 100%;
    }

    table th, table td {
        text-align: center;
        padding: 12px;
        font-size: 14px;
    }

    table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Action Buttons Styling */
    .btn {
        font-size: 14px;
        border-radius: 8px;
        padding: 8px 16px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-sm {
        font-size: 12px;
        padding: 5px 10px;
    }

    /* Flexbox Container for Search Bar */
    .form-inline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .form-inline input {
        width: 70%;
    }

    .form-inline button {
        width: 25%;
        margin-left: 10px;
    }

    /* Align form and table on the same line */
    .row {
        display: flex;
        justify-content: space-between;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        .row {
            flex-direction: column;
        }

        .col-md-4, .col-md-8 {
            width: 100%;
            margin-bottom: 20px;
        }

        .form-inline input, .form-inline button {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    .form-control::placeholder {
        font-size: 0.9rem; 
   }
</style>

<!-- Page content -->
<div class="right_col" role="main">
    <div class="container">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
            <!-- Add Category Form (left side) -->
            <div class="col-md-4">
                <form>
                    <div class="form-group">
                        <label for="documentName">Document Name</label>
                        <input type="text" class="form-control" id="categoryName" placeholder="Enter Document name" required>
                    </div>

                    <div class="form-group">
                        <label for="documenttype">Document Type</label>
                        <input type="text" class="form-control" id="documenttype" placeholder="Enter Document type" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary col-md-5">Add</button>
                    </div>
                </form>
            </div>

            <!-- Table displaying categories (right side) -->
            <div class="col-md-8">
                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Document Name</th>
                            <th>Document Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Test</td>
                            <td>pdf</td>
                            <td>Active</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Test</td>
                            <td>svg</td>
                            <td>Active</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Test</td>
                            <td>docx</td>
                            <td>InActive</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php
include_once('footer.php');
?>
