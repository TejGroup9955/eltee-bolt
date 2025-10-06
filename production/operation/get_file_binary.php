<?php
echo "hii";
$filePath = 'production/' . $_GET['File_Path'];
echo $filePath;
if (file_exists($filePath)) {
    header('Content-Type: ' . mime_content_type($filePath));  // Set the correct content type
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
    readfile($filePath);  // Output the file contents
} else {
    echo "File not found!";
}

?>