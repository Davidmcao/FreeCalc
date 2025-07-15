// Animação de entrada
    document.addEventListener('DOMContentLoaded', function () {
        const card = document.querySelector('.result-card');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';

            setTimeout(() => {
                card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }
    });

// Função para copiar informações
function copiarInformacoes() {
    const detalhes = document.querySelectorAll('.detail-item');
    let texto = 'Detalhes do Frete:\n\n';

    detalhes.forEach(item => {
        const label = item.querySelector('.detail-label span').textContent.trim();
        const value = item.querySelector('.detail-value').textContent.trim();
        texto += `${label}: ${value}\n`;
    });

    const preco = document.querySelector('.price-value');
    if (preco) {
        texto += `\nValor Total: ${preco.textContent.trim()}`;
    }

    if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(() => {
            // Feedback visual
            const btn = event.target.closest('.btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i><span>Copiado!</span>';
            btn.style.background = 'linear-gradient(135deg, var(--success-color), var(--secondary-dark))';

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 2000);
        }).catch(() => {
            alert('Erro ao copiar. Tente novamente.');
        });
    } else {
        // Fallback para navegadores mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = texto;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Informações copiadas para a área de transferência!');
        } catch (err) {
            alert('Erro ao copiar. Tente novamente.');
        }
        document.body.removeChild(textArea);
    }
}

// Efeitos de hover aprimorados
document.querySelectorAll('.detail-item').forEach(item => {
    item.addEventListener('mouseenter', function () {
        const icon = this.querySelector('i');
        if (icon) {
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        }
    });

    item.addEventListener('mouseleave', function () {
        const icon = this.querySelector('i');
        if (icon) {
            icon.style.transform = 'scale(1) rotate(0deg)';
        }
    });
});