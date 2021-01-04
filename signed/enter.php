<?php
    $useropenid=$_POST['openid'];
    $nickname=$_POST['nickname'];
    $userface=$_POST['userface'];
    $name=$_POST['name'];
    $link=mysqli_connect('localhost','activity_luncode','SeAPCwJfgaatRMraP','activity_luncode','3306');
     if($link){
          $where_sql="select * from userinfo where openid='$useropenid' limit 1";
          $rs = mysqli_query($link,$where_sql);
     if(mysqli_num_rows($rs)>0){
          echo "201";
      }else{
              $insert_sql="INSERT INTO `userinfo` (`id`, `nickname`, `headimgurl`, `name`, `openid`,`is_show`) values (NULL,'$nickname','$userface','$name','$useropenid','0')";
              if(mysqli_query($link,$insert_sql)){
                   $insert_lucksql="INSERT INTO `luckuser` (`id`, `nickname`, `headimgurl`, `name`, `openid`) values (NULL,'$nickname','$userface','$name','$useropenid')";
                   if(mysqli_query($link,$insert_lucksql)){
                        echo "200";
                   }
              }
     }
   }else{
        echo "n";
   }
   mysqli_close($link);
?>