//Função para fazer o login do usuário
async function login() {

    //capturar os campos de nome de usuário e senha através do ID
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    //Montando a chamada da API (Keycloak)
    const params = new URLSearchParams();
    params.append('client_id', 'produtos-client');
    params.append('grant_type', 'password');
    params.append('username', username);
    params.append('password', password);

    //fecth para fazer a requisição POST para o Keycloak
    const response = await fetch('http://localhost:8080/realms/produtos-realm/protocol/openid-connect/token', {
        method: 'POST', //Tipo de requisição
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded' //Tipo de conteúdo para enviar os dados do formulário
        },
        body: params //Corpo da requisição com os parâmetros
    });

    //Ler os dados da resposta em JSON
    const data = await response.json();

    //Verificar se a resposta contém um token de acesso
    if (data.access_token) {
        //Armazenar o token de acesso no localStorage para uso futuro
        localStorage.setItem('access_token', data.access_token);
        //Redirecionar para a página de produtos após o login bem-sucedido
        window.location.href = 'produtos.html';

    } else {
        alert('Falha no login. Verifique suas credenciais.');
    }    
}