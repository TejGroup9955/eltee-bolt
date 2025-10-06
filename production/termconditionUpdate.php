<?php
include_once('header.php');
?>

<style>
    .form-control {
        border-radius: 10px;
    }
</style>
<?php if(!empty($_GET['termid'])){ 
        $termid = $_GET['termid'];
        $query = $connect->query("SELECT `term_type`, `title`, `discription` FROM terms_conditions WHERE `terms_id`='$termid'");
        $rowCount = $query->num_rows;
        $title = $termtype = $desc = "";
        if($rowCount > 0){
            while ($row = $query->fetch_assoc()) {
                $title = $row['title'];
                $termtype = $row['term_type'];
                $desc = $row['discription'];
            }
        }
    ?>
<!-- Page content -->
<div class="right_col" role="main">
    <div class="container">
        
        <!-- Row to align the form and table on the same line -->
        <div class="row">
            <!-- Add Category Form (left side) -->
            <div class="col-md-4">
                <form name="termfrm" id="termfrm" method="POST" action="operation/termconditionOperation.php">
                <div class = 'form-group'>
                    <input type="hidden" id = "operation" name="operation" value = "update">
                    <input type="hidden" id = "termid" name="termid" value = "<?php echo $termid ?>">
                     <label for = 'Choose Type' class = 'form-label'> Choose Type </label>
                     <select id = 'termtype' name = 'termtype' class = 'form-control font-size-select' required>
                        <option value = ''>Select  Type</option>
                        <option value = 'Purchase' <?php if($termtype == "Purchase") {?> selected <?php } ?>>Purchase</option>
                        <option value = 'Sales' <?php if($termtype == "Sales") {?> selected <?php } ?>>Sales</option>
                     </select>
                  </div>

                    <div class="form-group">
                        <label for="Term/Condition">Term/Condition Heading:</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Term or Condition " value = "<?php echo $title; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Description">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter some description " rows="3"><?php echo $desc; ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <input type="submit" class="btn btn-success" id="btnSave" value="Update">
                        <a href="term_condition_master.php"><button type="button" class="btn btn-warning">Reset</button></a>
                        <a href="index.php"><button type="button" class="btn btn-secondary">Close</button></a>
                  
                    </div>
                </form>
            </div>

            <!-- Table displaying categories (right side) -->
           
        </div>

    </div>
</div>
<?php }else{
    echo "Invalid Term Id";
} ?>
<?php
include_once('footer.php');
?>
