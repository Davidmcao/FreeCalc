// Máscara para CEP com validação aprimorada
const cepInput = document.getElementById('cep');
if (cepInput) {
    cepInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        }
        e.target.value = value;

        // Validação visual em tempo real
        if (value.length === 9) {
            e.target.style.borderColor = 'var(--success-color)';
            e.target.style.background = 'rgba(16, 185, 129, 0.05)';
        } else if (value.length > 0) {
            e.target.style.borderColor = 'var(--warning-color)';
            e.target.style.background = 'rgba(245, 158, 11, 0.05)';
        } else {
            e.target.style.borderColor = '';
            e.target.style.background = '';
        }
    });

    cepInput.addEventListener('blur', function (e) {
        const cep = e.target.value.replace(/\D/g, '');
        if (cep.length !== 8 && cep.length > 0) {
            e.target.setCustomValidity('CEP deve ter 8 dígitos');
            e.target.style.borderColor = 'var(--danger-color)';
            e.target.style.background = 'rgba(239, 68, 68, 0.05)';
        } else {
            e.target.setCustomValidity('');
        }
    });
}

// Validação do peso com feedback visual
const pesoInput = document.getElementById('peso');
if (pesoInput) {
    pesoInput.addEventListener('input', function (e) {
        const peso = parseFloat(e.target.value);
        const helpText = e.target.nextElementSibling;

        if (peso > 0 && peso <= 30) {
            e.target.style.borderColor = 'var(--success-color)';
            e.target.style.background = 'rgba(16, 185, 129, 0.05)';
            if (peso > 10) {
                helpText.textContent = 'Peso acima de 10kg terá taxa adicional de 15%';
                helpText.style.color = 'var(--warning-color)';
            } else if (peso > 5) {
                helpText.textContent = 'Peso acima de 5kg terá taxa adicional de 8%';
                helpText.style.color = 'var(--warning-color)';
            } else {
                helpText.textContent = 'Peso entre 0,1kg e 30kg - valores maiores têm taxa adicional';
                helpText.style.color = '';
            }
        } else if (peso > 30) {
            e.target.style.borderColor = 'var(--danger-color)';
            e.target.style.background = 'rgba(239, 68, 68, 0.05)';
            helpText.textContent = 'Peso máximo permitido: 30kg';
            helpText.style.color = 'var(--danger-color)';
        } else {
            e.target.style.borderColor = '';
            e.target.style.background = '';
            helpText.textContent = 'Peso entre 0,1kg e 30kg - valores maiores têm taxa adicional';
            helpText.style.color = '';
        }
    });
}

// Animações e envio
const form = document.getElementById('freteForm');
const btn = document.getElementById('btnCalcular');
let formSubmitted = false;

if (form && btn && cepInput && pesoInput) {
    form.addEventListener('submit', function (e) {
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }

        const cep = cepInput.value;
        const peso = pesoInput.value;

        if (!cep || cep.length !== 9) {
            e.preventDefault();
            alert('Por favor, digite um CEP válido no formato 00000-000');
            cepInput.focus();
            return;
        }

        if (!peso || parseFloat(peso) <= 0 || parseFloat(peso) > 30) {
            e.preventDefault();
            alert('Por favor, digite um peso válido entre 0,1kg e 30kg');
            pesoInput.focus();
            return;
        }

        formSubmitted = true;
        btn.classList.add('loading');
        btn.disabled = true;

        setTimeout(() => {
            // Formulário será enviado normalmente
        }, 500);
    });
}

// Efeitos visuais aprimorados
document.querySelectorAll('.feature').forEach((feature, index) => {
    feature.style.animationDelay = `${0.6 + index * 0.1}s`;
    feature.style.animation = 'fadeInUp 0.8s ease-out both';
});

document.querySelectorAll('.step').forEach((step, index) => {
    step.style.animationDelay = `${0.8 + index * 0.2}s`;
    step.style.animation = 'fadeInUp 0.8s ease-out both';
});

// Feedback tátil para dispositivos móveis
if ('vibrate' in navigator && btn) {
    btn.addEventListener('click', function () {
        navigator.vibrate(50);
    });
}

// Auto-focus no primeiro campo
document.addEventListener('DOMContentLoaded', function () {
    if (cepInput) {
        cepInput.focus();
    }
});

// Atalhos de teclado
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (form) form.requestSubmit(); // envio seguro
    }

    if (e.key === 'Escape') {
        if (cepInput && pesoInput) {
            cepInput.value = '';
            pesoInput.value = '1.0';
            cepInput.focus();
        }
    }
});
