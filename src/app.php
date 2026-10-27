<?php
 
require_once(dirname(__FILE__) . "/../vendor/autoload.php");
 
use Slim\Factory\AppFactory;
 
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
 
$nasa = [
    ['id' => 1, 'nome' => 'Sputnik 1', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 2, 'nome' => 'Apollo 8', 'ano' => 1968, 'agencia' => "NASA", 'status' => 'Em andamento'],
    ['id' => 3, 'nome' => 'Apollo 10', 'ano' => 1969, 'agencia' => "NASA", 'status' => 'Planejada'],
    ['id' => 4, 'nome' => 'Apollo 11', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 5, 'nome' => 'Apollo 13', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 6, 'nome' => 'Gemini 4', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 7, 'nome' => 'Mercury-Atlas 6', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 8, 'nome' => 'Voyager 1', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 9, 'nome' => 'Voyager 2', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 10, 'nome' => 'Viking 1', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 11, 'nome' => 'Viking 2', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 12, 'nome' => 'Mariner 4', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 13, 'nome' => 'Pioneer 10', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 14, 'nome' => 'Skylab', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 15, 'nome' => 'Space Shuttle Columbia', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 16, 'nome' => 'Cassini-Huygens', 'ano' => 1967, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 17, 'nome' => 'STS-1', 'ano' => 1983, 'agencia' => "NASA", 'status' => 'Planejada'],
    ['id' => 18, 'nome' => 'STS-41-D', 'ano' => 1984, 'agencia' => "NASA", 'status' => 'Concluída'],
    ['id' => 19, 'nome' => 'STS-51-B', 'ano' => 1985, 'agencia' => "NASA", 'status' => 'Em andamento'],
    ['id' => 20, 'nome' => 'Voyager 2 Encounter', 'ano' => 1986, 'agencia' => "NASA", 'status' => 'Planejada'],
];
 
 
 
$app->get('/nasa', function ($request, $response) use ($nasa) {
 
    $response->getBody()->write(json_encode($nasa));
 
    return $response
 
        ->withHeader('Content-Type', 'application/json')
 
        ->withStatus(200);
 
});
 
$app->get('/nasa/id/{id}', function ($request, $response, $args) use ($nasa) {
    // 1. Converte o ID recebido para inteiro
    $id = (int)$args['id']; 
 
    // 2. Procura a missão correta dentro da lista
    $encontrado = null;
    foreach ($nasa as $missao) {
        if ($missao['id'] === $id) {
            $encontrado = $missao;
            break;
        }
    }
 
    // 3. Se encontrou, retorna os dados da missão
    if ($encontrado !== null) {
        $response->getBody()->write(json_encode($encontrado));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
 
    // 4. Se não encontrou, retorna 404
    $response->getBody()->write(json_encode([
        'erro' => 'Missão não encontrada'
    ]));
 
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});
$app->post('/nasa', function ($request, $response) use (&$nasa) {
    $body = $request->getParsedBody();
    
    // Validação simples de campos obrigatórios
    if (empty($body['nome']) || empty($body['ano']) || empty($body['agencia']) || empty($body['status'])) {
        $response->getBody()->write(json_encode(["erro" => "Campos obrigatórios ausentes"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    // Descobre o próximo ID baseado no maior ID existente no array
    $proximoId = $nasa ? max(array_column($nasa, 'id')) + 1 : 1;
 
    $nova = [
        "id"      => $proximoId,
        "nome"    => $body['nome'],
        "ano"     => (int) $body['ano'],
        "agencia" => $body['agencia'],
        "status"  => $body['status'],
    ];
    
    $nasa[] = $nova; // Adiciona no array
 
    $response->getBody()->write(json_encode($nova));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});
 
// PUT /nasa/id/{id}
$app->put('/nasa/id/{id}', function ($request, $response, $args) use (&$nasa) {
    $id = (int) $args['id'];
    $body = $request->getParsedBody();
    
    // Busca o índice do elemento dentro do array $nasa
    $indiceEncontrado = null;
    foreach ($nasa as $key => $missao) {
        if ($missao['id'] === $id) {
            $indiceEncontrado = $key;
            break;
        }
    }
    
    if ($indiceEncontrado === null) {
        $response->getBody()->write(json_encode(["erro" => "Missão não encontrada"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
 
    // Validação de campos obrigatórios para a atualização
    if (empty($body['nome']) || empty($body['ano']) || empty($body['agencia']) || empty($body['status'])) {
        $response->getBody()->write(json_encode(["erro" => "Campos obrigatórios ausentes"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
 
    // Atualiza a missão na posição encontrada
    $nasa[$indiceEncontrado] = [
        "id"      => $id,
        "nome"    => $body['nome'],
        "ano"     => (int) $body['ano'],
        "agencia" => $body['agencia'],
        "status"  => $body['status'],
    ];
 
    $response->getBody()->write(json_encode($nasa[$indiceEncontrado]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});
 
// DELETE /nasa/id/{id}
$app->delete('/nasa/id/{id}', function ($request, $response, $args) use (&$nasa) {
    $id = (int) $args['id'];
    
    // Busca o índice do elemento para exclusão
    $indiceEncontrado = null;
    foreach ($nasa as $key => $missao) {
        if ($missao['id'] === $id) {
            $indiceEncontrado = $key;
            break;
        }
    }
    
    if ($indiceEncontrado === null) {
        $response->getBody()->write(json_encode(["erro" => "Missão não encontrada"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
 
    // Remove do array na memória
    unset($nasa[$indiceEncontrado]);
 
    // Retorna status 204 No Content (padrão HTTP para deleções bem-sucedidas)
    return $response->withStatus(204);
});

return $app;
