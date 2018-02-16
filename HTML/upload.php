<?php
   if(isset($_FILES['csv'])){
      $errors= array();
      $file_name = $_FILES['csv']['name'];
      $file_size = $_FILES['csv']['size'];
      $file_tmp = $_FILES['csv']['tmp_name'];
      $file_type = $_FILES['csv']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['csv']['name'])));
      
      $expensions= array("csv");
      
      if(in_array($file_ext,$expensions)=== false){
         $errors[]="extension not allowed, please choose a CSV file.";
      }
      
      if($file_size > 2097152) {
         $errors[]='File size must be excately 2 MB';
      }
      
      if(empty($errors)==true) {
         move_uploaded_file($file_tmp,"TestUpload/".$file_name);
         echo "Success";
      }else{
         print_r($errors);
      }
   }
?>