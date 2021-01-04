<?php
    $headurl = $_POST['headurl'];
    $link = mysqli_connect('localhost','activity_luncode','SeAPCwJfgaatRMraP','activity_luncode');
    if($link){
        $sql="DELETE FROM luckuser WHERE headimgurl='$headurl'";
        echo $sql;
        if(mysqli_query($link,$sql)){
            echo 'User Remove SUCCESS';
        }else{
            echo 'User Remove ERROR';
        }
    }else{
        echo 'MYSQL ERROR';
    }
     mysqli_close($link);
?>