<?php

try{

    $pdo = new PDO('mysql:host=localhost;dbname=pos_edward_db','root','');

}catch(PDOException $e  ){

echo $e->getMessage();


}





//echo'connection success';




?>