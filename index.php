<?php
session_start();

function buscarConteudo($command) {
    $url = "http://localhost:4567/" . rawurlencode($command);
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        return "Erro ao processar o comando: $command. Tente novamente mais tarde.";
    }

    $responseData = json_decode($response, true);
    
    if ($command === 'start' && isset($responseData['descricao'])) {
        return $responseData['descricao']; // Retorna apenas a descrição da cena
    }

    return $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['command'])) {
    $_SESSION['history'] = []; 

    $_SESSION['history'][] = [
        'command' => '',
        'response' => 'Digite START para começar o jogo.'
    ];

    unset($_SESSION['last_command']);
}

if (isset($_GET['command'])) {
    $command = str_replace('+', ' ', $_GET['command']);

    $response = buscarConteudo($command);

    if (strtolower($command) === 'quit') {
        $_SESSION['history'] = []; 
        $_SESSION['history'][] = [
            'command' => $command,
            'response' => "Saindo do jogo... Digite START para começar o jogo."
        ];
        $_SESSION['last_command'] = $command;
    }

    elseif (strtolower($command) === 'restart') {
        $_SESSION['history'] = []; 
        $_SESSION['history'][] = [
            'command' => '',
            'response' => 'Digite START para começar o jogo.'
        ];
        $_SESSION['last_command'] = $command;
    }

    else {
        if (!isset($_SESSION['last_command']) || $_SESSION['last_command'] !== $command) {
            $_SESSION['history'][] = [
                'command' => $command,
                'response' => $response
            ];

            $_SESSION['last_command'] = $command;
        }
    }
}

include "template.phtml";
