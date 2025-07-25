<?php
/**
 * Processa o formulário e calcula o valor do frete usando API ViaCEP
 */

// Configurações
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para limpar e validar CEP
function limparCEP($cep) {
    return preg_replace('/[^0-9]/', '', $cep);
}

// Função para validar CEP
function validarCEP($cep) {
    $cep_limpo = limparCEP($cep);
    return strlen($cep_limpo) === 8 && is_numeric($cep_limpo);
}

// Função para formatar CEP
function formatarCEP($cep) {
    $cep_limpo = limparCEP($cep);
    return substr($cep_limpo, 0, 5) . '-' . substr($cep_limpo, 5, 3);
}

// Função para consultar CEP na API ViaCEP
function consultarCEPViaCEP($cep) {
    $cep_limpo = limparCEP($cep);
    $url = "https://viacep.com.br/ws/{$cep_limpo}/json/";
    
    // Configurações do cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'FreteCalc/2.0');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("Erro na consulta do CEP: {$error}");
    }
    
    if ($http_code !== 200) {
        throw new Exception("API indisponível (código: {$http_code})");
    }
    
    $data = json_decode($response, true);
    
    if (!$data || isset($data['erro'])) {
        throw new Exception("CEP não encontrado");
    }
    
    return $data;
}

// Função para buscar dados do CEP (com fallback para dados locais)
function buscarDadosCEP($cep) {
    try {
        // Tenta consultar na API ViaCEP primeiro
        $dados_api = consultarCEPViaCEP($cep);
        
        // Calcula distância baseada na região/estado
        $distancia = calcularDistanciaPorEstado($dados_api['uf']);
        
        return [
            'cidade' => $dados_api['localidade'],
            'estado' => $dados_api['uf'],
            'bairro' => $dados_api['bairro'] ?? '',
            'logradouro' => $dados_api['logradouro'] ?? '',
            'distancia' => $distancia,
            'regiao' => obterRegiaoPorEstado($dados_api['uf']),
            'fonte' => 'ViaCEP'
        ];
        
    } catch (Exception $e) {
        // Em caso de erro na API, usa dados locais como fallback
        return buscarDadosCEPLocal($cep);
    }
}

// Função para calcular distância baseada no estado - REALISTA
function calcularDistanciaPorEstado($uf) {
    // Distâncias realistas de São Paulo (capital) para capitais dos estados
    $distancias_estados = [
        'SP' => rand(0, 80),       // São Paulo - região metropolitana
        'RJ' => rand(400, 450),    // Rio de Janeiro
        'MG' => rand(500, 600),    // Minas Gerais
        'ES' => rand(850, 950),    // Espírito Santo
        'PR' => rand(350, 420),    // Paraná
        'SC' => rand(550, 650),    // Santa Catarina
        'RS' => rand(950, 1100),   // Rio Grande do Sul
        'GO' => rand(800, 950),    // Goiás
        'DF' => rand(950, 1050),   // Distrito Federal
        'MT' => rand(950, 1200),   // Mato Grosso
        'MS' => rand(850, 1000),   // Mato Grosso do Sul
        'BA' => rand(1400, 1600),  // Bahia
        'SE' => rand(1800, 2000),  // Sergipe
        'AL' => rand(1900, 2100),  // Alagoas
        'PE' => rand(2000, 2200),  // Pernambuco
        'PB' => rand(2100, 2300),  // Paraíba
        'RN' => rand(2200, 2400),  // Rio Grande do Norte
        'CE' => rand(2300, 2500),  // Ceará
        'PI' => rand(2200, 2400),  // Piauí
        'MA' => rand(2400, 2600),  // Maranhão
        'TO' => rand(1100, 1300),  // Tocantins
        'PA' => rand(2200, 2500),  // Pará
        'AP' => rand(2700, 3000),  // Amapá
        'AM' => rand(2500, 2800),  // Amazonas
        'RR' => rand(3000, 3300),  // Roraima
        'AC' => rand(2300, 2600),  // Acre
        'RO' => rand(1800, 2100),  // Rondônia
    ];
    
    return $distancias_estados[$uf] ?? rand(1200, 1800);
}

// Função para obter região por estado
function obterRegiaoPorEstado($uf) {
    $regioes = [
        'SP' => 'Sudeste', 'RJ' => 'Sudeste', 'MG' => 'Sudeste', 'ES' => 'Sudeste',
        'PR' => 'Sul', 'SC' => 'Sul', 'RS' => 'Sul',
        'GO' => 'Centro-Oeste', 'DF' => 'Centro-Oeste', 'MT' => 'Centro-Oeste', 'MS' => 'Centro-Oeste',
        'BA' => 'Nordeste', 'SE' => 'Nordeste', 'AL' => 'Nordeste', 'PE' => 'Nordeste',
        'PB' => 'Nordeste', 'RN' => 'Nordeste', 'CE' => 'Nordeste', 'PI' => 'Nordeste', 'MA' => 'Nordeste',
        'TO' => 'Norte', 'PA' => 'Norte', 'AP' => 'Norte', 'AM' => 'Norte',
        'RR' => 'Norte', 'AC' => 'Norte', 'RO' => 'Norte'
    ];
    
    return $regioes[$uf] ?? 'Desconhecida';
}

// Função para buscar dados locais (fallback)
function buscarDadosCEPLocal($cep) {
    $cep_limpo = limparCEP($cep);
    
    // Carrega dados do arquivo JSON
    $dados_ceps = carregarDadosCEPs();
    
    // Busca CEP exato primeiro
    if (isset($dados_ceps[$cep_limpo])) {
        $dados_ceps[$cep_limpo]['fonte'] = 'Local';
        return $dados_ceps[$cep_limpo];
    }
    
    // Busca por faixa de CEP (primeiros 5 dígitos)
    $faixa_cep = substr($cep_limpo, 0, 5);
    foreach ($dados_ceps as $cep_base => $dados) {
        if (substr($cep_base, 0, 5) === $faixa_cep) {
            $dados['fonte'] = 'Local';
            return $dados;
        }
    }
    
    // Se não encontrar, tenta buscar por região 
    $regiao_cep = substr($cep_limpo, 0, 2);
    foreach ($dados_ceps as $cep_base => $dados) {
        if (substr($cep_base, 0, 2) === $regiao_cep) {
            // Adiciona variação na distância para CEPs da mesma região
            $dados['distancia'] += rand(50, 200);
            $dados['fonte'] = 'Local';
            return $dados;
        }
    }
    
    // CEP não encontrado - retorna dados padrão
    return [
        'cidade' => 'Cidade não identificada',
        'estado' => 'XX',
        'bairro' => '',
        'logradouro' => '',
        'distancia' => rand(500, 1500),
        'regiao' => 'Desconhecida',
        'fonte' => 'Padrão'
    ];
}

// Função para carregar dados de CEPs locais
function carregarDadosCEPs() {
    $arquivo_ceps = __DIR__ . '/ceps.json';
    
    if (file_exists($arquivo_ceps)) {
        $conteudo = file_get_contents($arquivo_ceps);
        $dados = json_decode($conteudo, true);
        
        if ($dados !== null) {
            return $dados;
        }
    }
    
    // Retorna dados padrão se não conseguir carregar o arquivo
    return [
        '01000000' => [
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'bairro' => 'Sé',
            'logradouro' => '',
            'distancia' => 0,
            'regiao' => 'Sudeste'
        ]
    ];
}

// Função para calcular frete 
function calcularFrete($distancia, $peso, $estado = '') {
    // Configurações de cálculo
    $taxa_base = 12.00;           // Taxa base fixa
    $taxa_por_kg = 3.00;          // Taxa por quilograma 
    $taxa_minima = 18.00;         // Valor mínimo de frete
    
    // Taxa por km progressiva 
    $taxa_por_km = 0.025;         // Taxa base por km 
    if ($distancia > 1000) {
        $taxa_por_km = 0.020;     // Reduz para distâncias longas
    }
    if ($distancia > 2000) {
        $taxa_por_km = 0.015;     // Reduz ainda mais para distâncias muito longas
    }
    
    // Cálculo base
    $valor_distancia = $distancia * $taxa_por_km;
    $valor_peso = $peso * $taxa_por_kg;
    $valor_total = $taxa_base + $valor_distancia + $valor_peso;
    
    // Aplica desconto para distâncias curtas 
    if ($distancia < 100) {
        $valor_total *= 0.85; // 15% de desconto
    }
    
    // Taxa MODERADA para regiões Norte e Nordeste 
    $estados_taxa_adicional = ['AM', 'AC', 'RR', 'PA', 'AP', 'TO', 'MA', 'PI', 'CE', 'RN', 'PB', 'PE', 'AL', 'SE', 'BA'];
    if (in_array($estado, $estados_taxa_adicional)) {
        $valor_total *= 1.05; // Apenas 5% adicional (era 8%)
    }
    
    // Desconto progressivo para peso baixo
    if ($peso <= 0.5) {
        $valor_total *= 0.90; // 10% de desconto para produtos muito leves
    } elseif ($peso <= 1.0) {
        $valor_total *= 0.95; // 5% de desconto para produtos leves
    }
    
    // Taxa adicional para produtos pesados
    if ($peso > 10) {
        $valor_total *= 1.12; // 12% adicional para produtos pesados
    } elseif ($peso > 5) {
        $valor_total *= 1.06; // 6% adicional para produtos médio-pesados
    }
    
    // Garante valor mínimo
    $valor_total = max($valor_total, $taxa_minima);
    
    return round($valor_total, 2);
}

// Função para calcular prazo de entrega
function calcularPrazo($distancia, $estado = '') {
    $prazo_base = 0;
    
    // Prazo baseado na distância (realista)
    if ($distancia <= 100) {
        $prazo_base = rand(1, 2);
    } elseif ($distancia <= 300) {
        $prazo_base = rand(2, 3);
    } elseif ($distancia <= 600) {
        $prazo_base = rand(3, 5);
    } elseif ($distancia <= 1000) {
        $prazo_base = rand(4, 6);
    } elseif ($distancia <= 1500) {
        $prazo_base = rand(5, 8);
    } elseif ($distancia <= 2000) {
        $prazo_base = rand(6, 10);
    } else {
        $prazo_base = rand(8, 12);
    }
    
    // Adiciona dias extras apenas para regiões mais remotas
    $estados_prazo_adicional = ['AM', 'AC', 'RR', 'PA', 'AP'];
    if (in_array($estado, $estados_prazo_adicional)) {
        $prazo_base += rand(1, 2); // Reduzido
    }
    
    return $prazo_base;
}

// Função para registrar log (opcional)
function registrarLog($dados) {
    $log_file = __DIR__ . '/logs/consultas.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $fonte = $dados['fonte'] ?? 'unknown';
    $log_entry = "[{$timestamp}] IP: {$ip} | CEP: {$dados['cep']} | Cidade: {$dados['cidade']} | Estado: {$dados['estado']} | Valor: R$ {$dados['valor_frete']} | Fonte: {$fonte}\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Processamento principal
try {
    // Verifica se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método de requisição inválido');
    }
    
    // Recebe e valida dados do formulário
    $cep_input = $_POST['cep'] ?? '';
    $peso_input = $_POST['peso'] ?? '';
    
    // Validações
    if (empty($cep_input)) {
        throw new Exception('CEP é obrigatório');
    }
    
    if (empty($peso_input) || !is_numeric($peso_input)) {
        throw new Exception('Peso deve ser um número válido');
    }
    
    $peso = floatval($peso_input);
    
    if ($peso <= 0 || $peso > 30) {
        throw new Exception('Peso deve estar entre 0,1kg e 30kg');
    }
    
    if (!validarCEP($cep_input)) {
        throw new Exception('CEP inválido. Use o formato 00000-000');
    }
    
    // Processa CEP
    $cep_limpo = limparCEP($cep_input);
    $cep_formatado = formatarCEP($cep_limpo);
    
    // Busca dados do CEP
    $dados_cep = buscarDadosCEP($cep_limpo);
    
    // Calcula frete e prazo
    $distancia = $dados_cep['distancia'];
    $valor_frete = calcularFrete($distancia, $peso, $dados_cep['estado']);
    $prazo = calcularPrazo($distancia, $dados_cep['estado']);
    
    // Prepara dados para o resultado
    $resultado = [
        'cep' => $cep_formatado,
        'cidade' => $dados_cep['cidade'],
        'estado' => $dados_cep['estado'],
        'bairro' => $dados_cep['bairro'] ?? '',
        'peso' => number_format($peso, 1, ',', '.'),
        'distancia' => number_format($distancia, 0, ',', '.'),
        'valor_frete' => number_format($valor_frete, 2, ',', '.'),
        'prazo' => $prazo,
        'fonte' => $dados_cep['fonte']
    ];
    
    // Registra log da consulta
    registrarLog($resultado);
    
    // Redireciona para página de resultado com sucesso
    $query_params = http_build_query(array_merge($resultado, ['success' => '1']));
    header("Location: resultado.php?{$query_params}");
    exit;
    
} catch (Exception $e) {
    // Em caso de erro, redireciona para página de resultado com erro
    $erro = urlencode($e->getMessage());
    header("Location: resultado.php?success=0&erro={$erro}");
    exit;
}

// Função auxiliar para debug (remover em produção)
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
?>

