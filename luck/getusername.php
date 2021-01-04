<?php
    $link = mysqli_connect('localhost','activity_luncode','SeAPCwJfgaatRMraP','activity_luncode');
    if($link){
        $sql = "select * from luckuser";
        $rs = mysqli_query($link,$sql);
        //var_dump($rs);
        while($row=mysqli_fetch_assoc($rs)) $res[] = $row['nickname'];
        echo json_encode($res,true);
    }else{
        echo 'ERROR';
    }
 mysqli_close($link);
?>