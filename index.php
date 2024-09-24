<?php
session_start();

// Função para buscar o conteúdo a partir da rota do comando
function buscarConteudo($command) {
    // Usa rawurlencode para codificar corretamente o comando, incluindo espaços
    $url = "http://localhost:4567/" . rawurlencode($command);
    
    // Usa file_get_contents com supressão de erro e verifica se a resposta é válida
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        // Exibe uma mensagem de erro amigável
        return "Erro ao processar o comando: $command. Tente novamente mais tarde.";
    }

    return $response;
}

// Limpa o histórico ao recarregar a página
if (!isset($_GET['command'])) {
    $_SESSION['history'] = [];
    
    // Carrega a cena inicial se o histórico estiver vazio
    $cenaInicialUrl = "http://localhost:4567/start";
    $response = @file_get_contents($cenaInicialUrl);
    $responseData = json_decode($response, true);

    // Adiciona a cena inicial ao histórico
    $_SESSION['history'][] = [
        'command' => 'start',
        'response' => $responseData['descricao'] ?? 'Cena inicial não encontrada.'
    ];
}

// Se um comando for enviado pelo formulário, processamos e atualizamos o histórico
if (isset($_GET['command'])) {
    // Captura o comando diretamente do GET e substitui + por espaço
    $command = str_replace('+', ' ', $_GET['command']); // Decodifica os espaços
    $response = buscarConteudo($command);

    // Salva o comando e a resposta no histórico
    $_SESSION['history'][] = [
        'command' => $command,
        'response' => $response
    ];
}

include "template.phtml";
