//Função para cadastrar um produto
async function cadastrarProduto() {

    //Capturar o token do usuário na local storage
    const token = localStorage.getItem('access_token');
    //Verificar se o token existe
    if (!token) {
        alert('Usuário não autenticado. Faça login para cadastrar um produto.');
        return;
    }

    //Obter os valores dos campos do formulário
    const nome = document.getElementById('nome').value;
    const preco = document.getElementById('preco').value;
    const quantidade = document.getElementById('quantidade').value;

    //Criar um objeto com os dados do produto
    const produto = {
        nome: nome,
        preco: parseFloat(preco),
        quantidade: parseInt(quantidade)
    };

    //Enviar os dados para o backend usando fetch
    const response = await fetch('http://localhost:8000/api/v1/produtos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(produto)
    });

    //Verificar a resposta do backend
    if(!response.ok) {
        alert('Erro ao cadastrar produto');
        console.error('Erro:', response.statusText);
        console.error('Resposta:', await response.text());
        return;
    }

    //Obter a resposta em formato JSON
    var json = await response.json();
    alert(json.message);

    //Atualizar a lista de produtos
    consultarProdutos();

    //Limpar os campos do formulário
    document.getElementById('nome').value = '';
    document.getElementById('preco').value = '';
    document.getElementById('quantidade').value = '';
}

//função para consultar os produtos cadastrados
async function consultarProdutos() {
    
    //Capturar o token do usuário na local storage
    const token = localStorage.getItem('access_token');
    
    //Verificar se o token existe
    if (!token) {
        alert('Usuário não autenticado. Faça login para consultar os produtos.');
        return;
    }

    //Enviar a requisição para o backend usando fetch
    const response = await fetch('http://localhost:8000/api/v1/produtos', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });

    //Capturar o json da resposta
    const produtos = await response.json();

    //Popular a tabela com os produtos
    const tabela = document.getElementById('productTableBody');
    tabela.innerHTML = ''; //Limpar a tabela antes de popular

    produtos.forEach((produto, index) => {
        tabela.innerHTML += `
            <tr>
                <td scope="row">${index + 1}</td>
                <td>${produto.nome}</td>
                <td>${produto.preco}</td>
                <td>${produto.quantidade}</td>
                <td>
                    <button class="btn btn-sm btn-outline-warning me-1">Editar</button>
                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </td>
            </tr>
        `;
    });
}

//Executar a consulta de produtos ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    consultarProdutos();
});