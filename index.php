<?php
session_start();

if(isset($_GET['comando']) && ($_GET['save'])) {

    $command = rawurlencode($_GET['comando']);
    $save - rawurlencode($_GET['save']);
    $content = file_get_contents("http://localhost:4567/{$command}/{$save}");

} else if (isset($_GET['comando'])) {

    $command = rawurlencode($_GET['comando']);
    $content = file_get_contents("http://localhost:4567/{$command}");

} else {

    $content = file_get_contents("http://localhost:4567"); 
}


$arrayAssociative = json_decode($content);
$_SESSION['history'] = isset($_SESSION['history']) ? array_merge($_SESSION['history'], $arrayAssociative->messages) : [];
$messages = $_SESSION['history'];

include "template.phtml";