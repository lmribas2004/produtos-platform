<?php

namespace Tests\Feature;

use Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProdutoTest extends TestCase
{
    #Usar a trait 'RefreshDatabase' para garantir que o banco 
    #de dados seja limpo e atualizado antes de cada teste
    use RefreshDatabase;

    /*
        Método 'setUp' é executado antes de cada teste.
    */
    protected function setUp(): void
    {
        parent::setUp(); # Chama o método 'setUp' da classe pai

        #Ignorar todos os middlewares para evitar autenticação 
        #e outras verificações durante os testes
        $this->withoutMiddleware();
    
        #Executar os migrations para garantir que 
        #o banco de dados esteja atualizado
        $this->artisan('migrate');

        #Limpar o cache antes de cada teste
        Cache::flush();
    }
    
    #[Test]
    public function deve_criar_produto_com_sucesso()
    {
        //ARRANGE -> Preparar os dados necessários para o teste
        $nome = fake()->word();
        $preco = fake()->randomFloat(2, 10, 10000);
        $quantidade = fake()->numberBetween(1, 100);

        //ACT -> Faz requisição POST para criar um novo produto
        $response = $this->postJson('/api/v1/produtos', [
             'nome' => $nome,
             'preco' => $preco,
             'quantidade' => $quantidade 
        ]);

        //ASSERT -> Verifica se a resposta tem status 201 (Criado)
        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Produto criado com sucesso'
            ]);     
            
        //ASSERT -> Verifica se o produto foi realmente criado no banco de dados
        $this->assertDatabaseHas('produtos', [
            'nome' => $nome,
            'preco' => $preco,
            'quantidade' => $quantidade
        ]);
    }

    #[Test]
    public function nao_deve_criar_produto_com_dados_invalidos()
    {
         //ARRANGE -> Preparar os dados necessários para o teste
        $nome = ''; // Nome vazio para simular dados inválidos
        $preco = -10; // Preço negativo para simular dados inválidos
        $quantidade = -1; // Quantidade negativa para simular dados inválidos

        //ACT -> Faz requisição POST para criar um novo produto
        $response = $this->postJson('/api/v1/produtos', [
             'nome' => $nome,
             'preco' => $preco,
             'quantidade' => $quantidade 
        ]);
        
        //ASSERT -> Verifica se a resposta tem status 422 (Dados inválidos)
        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Ocorreram erros de validação'
            ]);     
    }

    #[Test]
    public function deve_listar_produtos()
    {
        //ARRANGE -> Preparar os dados necessários para o teste
        //Loop para criar 2 produtos na API
        for ($i = 0; $i < 2; $i++) {
            $this->postJson('/api/v1/produtos', [
                'nome' => fake()->word(),
                'preco' => fake()->randomFloat(2, 10, 10000),
                'quantidade' => fake()->numberBetween(1, 100)
            ]);
        }

        //ACT -> Faz requisição GET para listar os produtos
        $response = $this->getJson('/api/v1/produtos');

        //ASSERT -> Verifica se a resposta tem status 200 (OK)
        $response->assertStatus(200);

        //ASSERT -> Verifica se a resposta contém um array com pelo menos 2 produtos
        $this->assertGreaterThanOrEqual(2, count($response->json()));
    }

    #[Test]
    public function deve_buscar_produto_por_id()
    {
        //ARRANGE -> Preparar os dados necessários para o teste
        $nome = fake()->word();
        $preco = fake()->randomFloat(2, 10, 10000);
        $quantidade = fake()->numberBetween(1, 100);

        //ARRANGE -> Faz requisição POST para criar um novo produto
        $response = $this->postJson('/api/v1/produtos', [
             'nome' => $nome,
             'preco' => $preco,
             'quantidade' => $quantidade 
        ]);

        //Capturar o ID do ultimo produto cadastrado no banco de dados
        $produto = \App\Models\Produto::latest()->first();

        //ACT -> Extrai o ID do produto criado a partir da resposta
        $response = $this->getJson("/api/v1/produtos/{$produto->id}");

        //ASSERT -> Verifica se a resposta tem status 200 (OK)
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $produto->id,
                'nome' => $nome
            ]); 
    }

    #[Test]
    public function deve_atualizar_produto()
    {
        //ARRANGE -> Faz requisição POST para criar um novo produto
        $this->postJson('/api/v1/produtos', [
             'nome' => fake()->word(),
             'preco' => fake()->randomFloat(2, 10, 10000),
             'quantidade' => fake()->numberBetween(1, 100) 
        ]);

        //Capturar o ID do ultimo produto cadastrado no banco de dados
        $produto = \App\Models\Produto::latest()->first();

        //ACT -> Atualizar o produto criado com novos dados
        $nomeAtualizado = fake()->word();
        $precoAtualizado = fake()->randomFloat(2, 10, 10000);
        $quantidadeAtualizada = fake()->numberBetween(1, 100);

        //ACT -> Faz requisição PUT para atualizar o produto
        $response = $this->putJson("/api/v1/produtos/{$produto->id}", [
             'nome' => $nomeAtualizado,
             'preco' => $precoAtualizado,
             'quantidade' => $quantidadeAtualizada
        ]);

        //ASSERT -> Verifica se a resposta tem status 200 (OK)
        $response->assertStatus(status: 200)
            ->assertJsonFragment([
                'message' => 'Produto atualizado com sucesso'
            ]);     

        //ASSERT -> Verifica se o produto foi realmente criado no banco de dados
        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'nome' => $nomeAtualizado,
            'preco' => $precoAtualizado,
            'quantidade' => $quantidadeAtualizada
        ]);
    }

    #[Test]
    public function deve_excluir_produto()
    {
        //ARRANGE -> Faz requisição POST para criar um novo produto
        $this->postJson('/api/v1/produtos', [
             'nome' => fake()->word(),
             'preco' => fake()->randomFloat(2, 10, 10000),
             'quantidade' => fake()->numberBetween(1, 100) 
        ]);

        //Capturar o ID do ultimo produto cadastrado no banco de dados
        $produto = \App\Models\Produto::latest()->first();

        //ACT -> Faz requisição DELETE para excluir o produto
        $response = $this->deleteJson("/api/v1/produtos/{$produto->id}");

        //ASSERT -> Verifica se a resposta tem status 200 (OK)
         $response->assertStatus(status: 200)
            ->assertJsonFragment([
                'message' => 'Produto removido com sucesso'
            ]);     

        //ASSERT -> Verifica se o produto foi realmente excluído do banco de dados
        $this->assertDatabaseMissing('produtos', [
            'id' => $produto->id
        ]);
    }
}
