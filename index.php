<?php
$conteudo = file_get_contents("http://localhost:4567"); 
$arrayAssociativo = json_decode($conteudo);

include "template.phtml";