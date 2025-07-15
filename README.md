🚚 FreteCalc - Calculadora de Frete por CEP

Uma aplicação web moderna e responsiva para calcular valores de frete baseados no CEP de destino, desenvolvida em PHP com integração à API ViaCEP.

✨ Funcionalidades

•
Consulta de CEP em tempo real via API ViaCEP

•
Cálculo inteligente de frete baseado em distância, peso e região

•
Interface moderna e responsiva com animações suaves

•
Fallback para dados locais quando a API não está disponível

•
Validação de dados em tempo real

•
Resultados detalhados com informações completas da entrega

•
Sistema de logs para auditoria das consultas

🛠️ Tecnologias Utilizadas

•
Frontend: HTML5, CSS3, JavaScript

•
Backend: PHP 8.1+

•
API Externa: ViaCEP (consulta de CEPs brasileiros)

•
Estilo: CSS Grid, Flexbox, Animações CSS

•
Fontes: Google Fonts (Inter)

•
Ícones: Font Awesome 6

📋 Pré-requisitos

•
PHP 8.1 ou superior

•
Extensão PHP cURL habilitada

•
Servidor web (Apache, Nginx ou PHP built-in server)

•
Conexão com internet (para API ViaCEP)

🚀 Instalação

1. Clone ou baixe o projeto

Bash


git clone <url-do-repositorio>
cd frete-calc


2. Instale o PHP e extensões necessárias

Bash


# Ubuntu/Debian
sudo apt update
sudo apt install php php-cli php-curl

# CentOS/RHEL
sudo yum install php php-cli php-curl

# Windows (XAMPP recomendado)
# Baixe e instale o XAMPP do site oficial


3. Inicie o servidor

Bash


# Usando servidor built-in do PHP (desenvolvimento)
php -S localhost:8080

# Ou configure em Apache/Nginx (produção)


4. Acesse a aplicação

Abra seu navegador e acesse: http://localhost:8080

📁 Estrutura do Projeto

Plain Text


frete-calc/
├── index.html          # Página principal com formulário
├── style.css           # Estilos CSS responsivos
├── resultado.css       # Estilos CSS responsivos para a tela de resultado
├── script.js       # Animações da tela de resultado, hover e botão de copiar informações
├── resultado.js    # Validação de CEP, peso e envio do formulário com animações e atalhos
├── calcular.php        # Lógica de cálculo de frete
├── resultado.php       # Página de exibição dos resultados
├── logs/              # Diretório de logs (criado automaticamente)
│   └── consultas.log  # Log das consultas realizadas


🎯 Como Usar

1.
Acesse a aplicação no navegador

2.
Digite o CEP de destino no formato 00000-000

3.
Informe o peso do produto em quilogramas (0,1kg a 30kg)

4.
Clique em "Calcular Frete"

5.
Visualize o resultado com todos os detalhes da entrega

🔧 Configurações

Personalizar Cálculo de Frete

Edite o arquivo calcular.php e ajuste as variáveis na função calcularFrete():

PHP


// Configurações de cálculo
$taxa_base = 12.00;           // Taxa base fixa
$taxa_por_km = 0.18;          // Taxa por quilômetro
$taxa_por_kg = 2.80;          // Taxa por quilograma
$taxa_minima = 18.00;         // Valor mínimo de frete


Adicionar Novos CEPs Locais

Edite o arquivo ceps.json para adicionar novos CEPs à base local:

JSON


{
  "12345678": {
    "cidade": "Sua Cidade",
    "estado": "XX",
    "bairro": "Seu Bairro",
    "logradouro": "Sua Rua",
    "distancia": 500,
    "regiao": "Sua Região"
  }
}


🌐 API Utilizada

ViaCEP

•
URL: https://viacep.com.br/

•
Documentação: https://viacep.com.br/

•
Limite: Sem limite oficial, mas recomenda-se uso responsável

•
Formato: JSON

•
Exemplo: https://viacep.com.br/ws/01310100/json/

📊 Funcionalidades Avançadas

Sistema de Logs

•
Todas as consultas são registradas em logs/consultas.log

•
Inclui timestamp, IP, CEP consultado e valor calculado

•
Útil para análise e auditoria

Fallback Inteligente

•
Se a API ViaCEP falhar, usa dados locais do ceps.json

•
Busca por CEP exato, faixa ou região

•
Garante funcionamento mesmo offline

Cálculo Dinâmico

•
Considera distância, peso e região

•
Aplica descontos para entregas locais

•
Taxa adicional para regiões Norte/Nordeste

•
Prazo de entrega baseado na distância

🎨 Personalização Visual

Cores

Edite as variáveis CSS no arquivo style.css:

CSS


:root {
    --primary-color: #2563eb;
    --secondary-color: #f59e0b;
    --success-color: #10b981;
    /* ... outras cores */
}


Fontes

Para alterar a fonte, modifique o link no HTML:

HTML


<link href="https://fonts.googleapis.com/css2?family=SuaFonte:wght@300;400;500;600;700&display=swap" rel="stylesheet">


🔒 Segurança

•
Validação de entrada no frontend e backend

•
Sanitização de dados com htmlspecialchars()

•
Timeout configurado para requisições à API

•
Logs protegidos contra acesso direto

🐛 Solução de Problemas

Erro "CEP não encontrado"

•
Verifique se o CEP está no formato correto (8 dígitos)

•
Teste com CEPs conhecidos (ex: 01310-100)

•
Verifique conexão com internet

Erro "API indisponível"

•
A aplicação usará dados locais automaticamente

•
Verifique se a extensão cURL está instalada

•
Teste a API manualmente: curl https://viacep.com.br/ws/01310100/json/

Problemas de Performance

•
Configure cache no servidor web

•
Otimize imagens e recursos estáticos

•
Use CDN para bibliotecas externas

📈 Melhorias Futuras




Cache de consultas de CEP




Integração com múltiplas APIs de frete




Painel administrativo




API REST para integração




Suporte a múltiplos produtos




Cálculo de prazo mais preciso




Integração com correios

📄 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.

👨‍💻 Desenvolvedor - David Mendes

Desenvolvido com ❤️ usando tecnologias web modernas.





FreteCalc - Calculadora de Frete Inteligente © 2025

