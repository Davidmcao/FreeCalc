<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Frete - FreteCalc Pro</title>
    <link rel="stylesheet" href="/css/resultado.css">
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-shipping-fast"></i>
                <h1>FreteCalc Pro</h1>
            </div>
        </header>

        <main class="main-content">
            <div class="result-container">
                <?php
                // Verifica se há dados de resultado
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    // Dados do resultado
                    $cep = $_GET['cep'] ?? '';
                    $cidade = $_GET['cidade'] ?? '';
                    $estado = $_GET['estado'] ?? '';
                    $bairro = $_GET['bairro'] ?? '';
                    $peso = $_GET['peso'] ?? '';
                    $valor_frete = $_GET['valor_frete'] ?? '';
                    $prazo = $_GET['prazo'] ?? '';
                    $distancia = $_GET['distancia'] ?? '';
                    $fonte = $_GET['fonte'] ?? 'Local';

                    $fonte_class = strtolower($fonte);
                    if ($fonte === 'ViaCEP')
                        $fonte_class = 'api';
                    elseif ($fonte === 'Local')
                        $fonte_class = 'local';
                    elseif ($fonte === 'Padrão')
                        $fonte_class = 'padrao';
                    ?>

                    <div class="result-card">
                        <div class="result-header">
                            <i class="fas fa-check-circle"></i>
                            <h2>Frete Calculado com Sucesso!</h2>
                            <div class="result-subtitle">
                                <span>Confira os detalhes da sua entrega</span>
                                <span class="fonte-badge <?php echo $fonte_class; ?>">
                                    <i
                                        class="fas fa-<?php echo $fonte === 'ViaCEP' ? 'cloud' : ($fonte === 'Local' ? 'database' : 'exclamation-triangle'); ?>"></i>
                                    <?php echo $fonte === 'ViaCEP' ? 'API ViaCEP' : $fonte; ?>
                                </span>
                            </div>
                        </div>

                        <div class="result-details">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>CEP de Destino</span>
                                </div>
                                <div class="detail-value"><?php echo htmlspecialchars($cep); ?></div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-city"></i>
                                    <span>Cidade/Estado</span>
                                </div>
                                <div class="detail-value"><?php echo htmlspecialchars($cidade . ' - ' . $estado); ?></div>
                            </div>

                            <?php if (!empty($bairro)): ?>
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="fas fa-map"></i>
                                        <span>Bairro</span>
                                    </div>
                                    <div class="detail-value"><?php echo htmlspecialchars($bairro); ?></div>
                                </div>
                            <?php endif; ?>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-weight-hanging"></i>
                                    <span>Peso do Produto</span>
                                </div>
                                <div class="detail-value"><?php echo htmlspecialchars($peso); ?> kg</div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-route"></i>
                                    <span>Distância Estimada</span>
                                </div>
                                <div class="detail-value"><?php echo htmlspecialchars($distancia); ?> km</div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-clock"></i>
                                    <span>Prazo de Entrega</span>
                                </div>
                                <div class="detail-value"><?php echo htmlspecialchars($prazo); ?> dias úteis</div>
                            </div>
                        </div>

                        <div class="price-highlight">
                            <div class="price-label">Valor do Frete</div>
                            <div class="price-value">R$ <?php echo htmlspecialchars($valor_frete); ?></div>
                            <div class="price-note">
                                Valor calculado com base na distância, peso e região de destino
                                <?php if ($fonte === 'ViaCEP'): ?>
                                    <br><strong>Dados obtidos via API ViaCEP em tempo real</strong>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="actions">
                            <a href="index.html" class="btn btn-primary">
                                <i class="fas fa-calculator"></i>
                                <span>Nova Consulta</span>
                            </a>
                            <button onclick="window.print()" class="btn btn-secondary">
                                <i class="fas fa-print"></i>
                                <span>Imprimir</span>
                            </button>
                            <button onclick="copiarInformacoes()" class="btn btn-tertiary">
                                <i class="fas fa-copy"></i>
                                <span>Copiar Dados</span>
                            </button>
                        </div>
                    </div>

                    <?php
                } else {
                    ?>

                    <div class="result-card error-card">
                        <div class="result-header error-header">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h2>Erro no Cálculo</h2>
                            <p class="result-subtitle">Não foi possível calcular o frete</p>
                        </div>

                        <div class="result-details">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Motivo</span>
                                </div>
                                <div class="detail-value">
                                    <?php
                                    echo isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : 'Dados inválidos ou incompletos';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="actions">
                            <a href="index_profissional.html" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i>
                                <span>Voltar</span>
                            </a>
                            <a href="index_profissional.html" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                                <span>Tentar Novamente</span>
                            </a>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; 2025 FreteCalc Pro - Calculadora de Frete Profissional</p>
        </footer>
    </div>
</body>

<script src="/js/resultado.js"></script>

</html>