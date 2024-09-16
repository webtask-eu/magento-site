<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['test_file'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file["name"]);

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        echo "The file ". basename($file["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<form action="upload_test.php" method="POST" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="test_file">
    <input type="submit" value="Upload File">
</form>
