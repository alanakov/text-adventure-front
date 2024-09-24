<?php
session_start();

// Função para buscar o conteúdo a partir da rota do comando
function buscarConteudo($command) {
    $url = "http://localhost:4567/" . rawurlencode($command);
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        return "Erro ao processar o comando: $command. Tente novamente mais tarde.";
    }

    // Decodifica o JSON para extrair apenas a descrição, se for o comando 'start'
    $responseData = json_decode($response, true);
    
    if ($command === 'start' && isset($responseData['descricao'])) {
        return $responseData['descricao']; // Retorna apenas a descrição da cena
    }

    return $response;
}

// Limpa o histórico ao recarregar a página
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['command'])) {
    $_SESSION['history'] = []; // Limpa o histórico

    // Adiciona uma mensagem inicial pedindo para digitar "start"
    $_SESSION['history'][] = [
        'command' => '',
        'response' => 'Digite START para começar o jogo.'
    ];

    // Limpa o último comando processado
    unset($_SESSION['last_command']);
}

// Se um comando for enviado
if (isset($_GET['command'])) {
    $command = str_replace('+', ' ', $_GET['command']);

    // Se o comando for "quit", limpa o histórico e exibe a mensagem de encerramento
    if (strtolower($command) === 'quit') {
        $_SESSION['history'] = []; // Limpa o histórico
        $_SESSION['history'][] = [
            'command' => $command,
            'response' => "Saindo do jogo... Digite START para começar o jogo."
        ];
        $_SESSION['last_command'] = $command;
    }
    // Se o comando for "restart", limpa o histórico e exibe a mensagem inicial
    elseif (strtolower($command) === 'restart') {
        $_SESSION['history'] = []; // Limpa o histórico
        $_SESSION['history'][] = [
            'command' => '',
            'response' => 'Digite START para começar o jogo.'
        ];
        $_SESSION['last_command'] = $command;
    }
    // Caso contrário, processa o comando normalmente
    else {
        if (!isset($_SESSION['last_command']) || $_SESSION['last_command'] !== $command) {
            $response = buscarConteudo($command);

            // Se o comando for "start", carrega a cena 1
            if (strtolower($command) === 'start') {
                $_SESSION['history'][] = [
                    'command' => $command,
                    'response' => $response
                ];
            } else {
                // Processa qualquer outro comando normalmente
                $_SESSION['history'][] = [
                    'command' => $command,
                    'response' => $response
                ];
            }

            $_SESSION['last_command'] = $command;
        }
    }
}

include "template.phtml";
