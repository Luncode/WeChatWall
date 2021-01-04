<?php
    $useropenid=$_POST['openid'];
    $link=mysqli_connect('localhost','activity_luncode','SeAPCwJfgaatRMraP','activity_luncode','3306');
    if($link){
        $where_sql="select * from userinfo where openid='$useropenid' limit 1";
        $rs = mysqli_query($link,$where_sql);
        if(mysqli_num_rows($rs)>0){
                echo "301";
        }
    }
    mysqli_close($link);
?>
   