<?php
if (isset($_FILES['upload']['name'])) {
    $file = $_FILES['upload']['tmp_name'];
    $file_name = $_FILES['upload']['name'];
    $file_name_array = explode(".", $file_name); 
    $extension = strtolower(end($file_name_array));
    $new_image_name = rand() . '.' . $extension; 

    $jsonFile = __DIR__.'/../settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';


    // Dosya uzantı kontrolü
    if (!in_array($extension, ["jpg", "jpeg", "png",'webp'])) {
        echo "<script>alert('Sorry, only JPG, JPEG, & PNG & webp files are allowed.');</script>";
        exit;
    }
    // Dosya boyutu kontrolü
    if ($_FILES["upload"]["size"] > 1000000) {
        echo "<script>alert('Image is too large: Upload image under 1 MB.');</script>";
        exit;
    }

    // Dosya yükleme
    $destination = "upload/" . $new_image_name;
    if (move_uploaded_file($file, $destination)) {
        $function_number = $_GET['CKEditorFuncNum'];
        $url = $dynamicUrl."/up/upload/" . $new_image_name;
        $message = '';
        echo "<script>window.parent.CKEDITOR.tools.callFunction($function_number, '$url', '$message');</script>";   
    } else {
        echo "<script>alert('Failed to move uploaded file.');</script>";
    }
}
?>
