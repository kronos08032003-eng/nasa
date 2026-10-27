<?php

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class NasaTest extends TestCase
{
    private function getApp()
    {
        // Força o arquivo a retornar a instância de $app configurada
        return require __DIR__ . '/../src/app.php';
    }

    // 1. TESTE DO GET (Listar todas as missões)
    public function test_deve_listar_todas_as_missoes_nasa(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/nasa')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        // Verifica se retornou uma lista e se o primeiro item é o Sputnik 1
        $this->assertIsArray($corpo);
        $this->assertEquals('Sputnik 1', $corpo[0]['nome']);
    }

    // 2. TESTE DO GET POR ID (Buscar missão id 1)
    public function test_deve_buscar_missao_por_id(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/nasa/id/1')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals(1, $corpo['id']);
        $this->assertEquals('Sputnik 1', $corpo['nome']);
    }

    // 3. TESTE DO POST (Criar nova missão)
    public function test_deve_criar_nova_missao(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/nasa')
            ->withHeader('Content-Type', 'application/json');

        // Dados enviados no corpo do POST
        $dadosNovaMissao = [
            'nome' => 'Artemis I',
            'ano' => 2022,
            'agencia' => 'NASA',
            'status' => 'Concluída'
        ];
        
        $request->getBody()->write(json_encode($dadosNovaMissao));
        $request->getBody()->rewind();
        
        // Configura o Slim para entender os dados JSON enviados no corpo da requisição
        $request = $request->withParsedBody($dadosNovaMissao);

        $response = $app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Artemis I', $corpo['nome']);
        $this->assertarrayHasKey('id', $corpo); // Garante que o ID automático foi gerado
    }

    // 4. TESTE DO PUT (Atualizar missão existente)
    public function test_deve_atualizar_missao_existente(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/nasa/id/2')
            ->withHeader('Content-Type', 'application/json');

        $dadosAtualizados = [
            'nome' => 'Apollo 8 Modificada',
            'ano' => 1968,
            'agencia' => 'NASA',
            'status' => 'Concluída'
        ];

        $request->getBody()->write(json_encode($dadosAtualizados));
        $request->getBody()->rewind();
        $request = $request->withParsedBody($dadosAtualizados);

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Apollo 8 Modificada', $corpo['nome']);
    }

    // 5. TESTE DO DELETE (Remover missão)
    public function test_deve_deletar_missao(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/nasa/id/3')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        // O seu código do DELETE retorna 204 No Content quando dá certo
        $this->assertEquals(204, $response->getStatusCode());
    }
}
