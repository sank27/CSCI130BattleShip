<?php
session_start();
//verify that the person has a valid session variable
//invalid variable? - redirect to index
if (!$_SESSION['valid'] || empty($_SESSION['user'])){
    header('Location: index.php');
}
?>