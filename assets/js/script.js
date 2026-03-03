// assets/js/script.js
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Configurar datas padrão para o resumo semanal
    configurarDatasPadrao();
    
    // Adicionar máscaras e validações
    configurarValidacoes();
    
    // Configurar máscara de telefone
    configurarMascaraTelefone();
    
    // Configurar eventos dinâmicos
    configurarEventos();
});

function configurarDatasPadrao() {
    // Configurar data atual como padrão para campos de data
    const hoje = new Date().toISOString().split('T')[0];
    
    // Definir data atual para campos de data de compra e consumo
    const dataCompraInput = document.getElementById('data_compra');
    const dataConsumoInput = document.getElementById('data_consumo');
    
    if (dataCompraInput && !dataCompraInput.value) {
        dataCompraInput.value = hoje;
    }
    
    if (dataConsumoInput && !dataConsumoInput.value) {
        dataConsumoInput.value = hoje;
    }
    
    // Configurar datas da semana atual (segunda a sexta) para o resumo
    const dataInicioInput = document.getElementById('data_inicio');
    const dataFimInput = document.getElementById('data_fim');
    
    if (dataInicioInput && dataFimInput && !dataInicioInput.value && !dataFimInput.value) {
        const { segunda, sexta } = calcularSemanaAtual();
        dataInicioInput.value = segunda;
        dataFimInput.value = sexta;
    }
}

function calcularSemanaAtual() {
    const hoje = new Date();
    const diaSemana = hoje.getDay(); // 0 = Domingo, 1 = Segunda, ..., 6 = Sábado
    
    // Calcular data da última segunda-feira
    const diffSegunda = diaSemana === 0 ? -6 : 1 - diaSemana;
    const segunda = new Date(hoje);
    segunda.setDate(hoje.getDate() + diffSegunda);
    
    // Calcular data da próxima sexta-feira
    const sexta = new Date(segunda);
    sexta.setDate(segunda.getDate() + 4);
    
    return {
        segunda: segunda.toISOString().split('T')[0],
        sexta: sexta.toISOString().split('T')[0]
    };
}

function configurarMascaraTelefone() {
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            
            // Aplica máscara conforme o tamanho
            if (valor.length <= 10) {
                // Telefone fixo: (00) 0000-0000
                valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else {
                // Celular: (00) 00000-0000
                valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            }
            
            e.target.value = valor;
        });
        
        telefoneInput.addEventListener('blur', function() {
            // Remove a máscara se o campo estiver vazio ou incompleto
            if (this.value.replace(/\D/g, '').length < 10) {
                this.value = '';
            }
        });
    }
}

function configurarValidacoes() {
    // Validar valor monetário
    const valorInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    valorInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const valor = parseFloat(this.value);
            if (valor < 0) {
                this.value = '0.00';
                mostrarAlerta('O valor não pode ser negativo.', 'warning');
            } else if (valor > 0) {
                // Formatar para 2 casas decimais
                this.value = valor.toFixed(2);
            }
        });
    });

    // Validar datas futuras
    const dataInputs = document.querySelectorAll('input[type="date"]');
    dataInputs.forEach(input => {
        input.addEventListener('change', function() {
            const dataSelecionada = new Date(this.value);
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);
            
            if (dataSelecionada > hoje) {
                mostrarAlerta('Não é possível selecionar uma data futura.', 'warning');
                this.value = hoje.toISOString().split('T')[0];
            }
        });
    });
}

function configurarEventos() {
    // Botão para usar semana atual no resumo
    const btnSemanaAtual = document.getElementById('btn-semana-atual');
    if (btnSemanaAtual) {
        btnSemanaAtual.addEventListener('click', function() {
            const { segunda, sexta } = calcularSemanaAtual();
            const dataInicioInput = document.getElementById('data_inicio');
            const dataFimInput = document.getElementById('data_fim');
            
            if (dataInicioInput && dataFimInput) {
                dataInicioInput.value = segunda;
                dataFimInput.value = sexta;
                mostrarAlerta('Datas da semana atual configuradas!', 'success');
            }
        });
    }

    // Validação de formulários antes do envio
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validarFormulario(this)) {
                e.preventDefault();
            }
        });
    });

    // Filtro dinâmico para itens disponíveis no consumo
    const itemSelect = document.getElementById('id_item');
    if (itemSelect) {
        itemSelect.addEventListener('change', function() {
            atualizarInfoItem(this.value);
        });
    }

    // Auto-complete para nomes de itens comuns
    const itemNomeInput = document.getElementById('nome');
    if (itemNomeInput) {
        configurarAutoComplete(itemNomeInput);
    }
    
    // Botões WhatsApp no resumo
    const btnWhatsApp = document.querySelectorAll('.btn-whatsapp');
    btnWhatsApp.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Não usar preventDefault para permitir que o mobile abra o app
            enviarWhatsApp(this);
        }, { passive: true });
    });
}

function validarFormulario(form) {
    let valido = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valido = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validação específica para valores monetários
    const valorInput = form.querySelector('input[type="number"][step="0.01"]');
    if (valorInput && valorInput.value) {
        const valor = parseFloat(valorInput.value);
        if (isNaN(valor) || valor <= 0) {
            valorInput.classList.add('is-invalid');
            mostrarAlerta('Por favor, insira um valor válido maior que zero.', 'warning');
            valido = false;
        }
    }

    if (!valido) {
        mostrarAlerta('Por favor, preencha todos os campos obrigatórios corretamente.', 'danger');
    }

    return valido;
}

function atualizarInfoItem(itemId) {
    // Esta função poderia buscar informações adicionais do item via AJAX
    // Por enquanto, vamos apenas mostrar um loading
    const itemSelect = document.getElementById('id_item');
    if (itemSelect && itemId) {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        console.log('Item selecionado:', selectedOption.text);
    }
}

function configurarAutoComplete(input) {
    const itensComuns = [
        'Marmita', 'Sorvete', 'Refrigerante', 'Água', 'Suco',
        'Pizza', 'Hambúrguer', 'Batata Frita', 'Salada', 'Frango',
        'Carne', 'Peixe', 'Arroz', 'Feijão', 'Macarrão',
        'Café', 'Chá', 'Leite', 'Pão', 'Queijo',
        'Presunto', 'Iogurte', 'Fruta', 'Bolo', 'Biscoito'
    ];

    let currentFocus = -1;

    input.addEventListener('input', function(e) {
        const val = this.value;
        closeAllLists();
        if (!val) return false;
        
        currentFocus = -1;
        
        const list = document.createElement('div');
        list.setAttribute('id', this.id + '-autocomplete-list');
        list.setAttribute('class', 'autocomplete-items');
        this.parentNode.appendChild(list);

        itensComuns.forEach(item => {
            if (item.substr(0, val.length).toLowerCase() === val.toLowerCase()) {
                const itemElement = document.createElement('div');
                itemElement.innerHTML = '<strong>' + item.substr(0, val.length) + '</strong>';
                itemElement.innerHTML += item.substr(val.length);
                itemElement.innerHTML += '<input type=\'hidden\' value=\'' + item + '\'>';
                
                itemElement.addEventListener('click', function() {
                    input.value = this.getElementsByTagName('input')[0].value;
                    closeAllLists();
                });
                
                list.appendChild(itemElement);
            }
        });
    });

    input.addEventListener('keydown', function(e) {
        let x = document.getElementById(this.id + '-autocomplete-list');
        if (x) x = x.getElementsByTagName('div');
        
        if (e.keyCode == 40) { // Seta para baixo
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { // Seta para cima
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add('autocomplete-active');
    }

    function removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            x[i].classList.remove('autocomplete-active');
        }
    }

    function closeAllLists(elmnt) {
        const x = document.getElementsByClassName('autocomplete-items');
        for (let i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != input) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    document.addEventListener('click', function(e) {
        closeAllLists(e.target);
    });
}

function mostrarAlerta(mensagem, tipo = 'info') {
    // Remover alertas existentes
    const alertasExistentes = document.querySelectorAll('.alert-dismissible');
    alertasExistentes.forEach(alerta => {
        if (alerta.classList.contains('alert-' + tipo)) {
            alerta.remove();
        }
    });

    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Inserir no início do container principal
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alerta, container.firstChild);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 5000);
    }
}

// Funções utilitárias
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

function calcularTotalGasto(itens) {
    return itens.reduce((total, item) => total + parseFloat(item.valor), 0);
}

// Função para enviar resumo por WhatsApp
function enviarWhatsApp(button) {
    const dados = {
        nome: button.getAttribute('data-nome'),
        telefone: button.getAttribute('data-telefone'),
        total: parseFloat(button.getAttribute('data-total')),
        itens: JSON.parse(button.getAttribute('data-itens')),
        periodo_inicio: button.getAttribute('data-periodo-inicio'),
        periodo_fim: button.getAttribute('data-periodo-fim'),
        dividasPagador: JSON.parse(button.getAttribute('data-dividas-pagador') || '{}')
    };
    
    if (!dados.telefone) {
        mostrarAlerta('Telefone não cadastrado para esta pessoa.', 'warning');
        return;
    }
    
    // Formatar período
    const dataInicio = new Date(dados.periodo_inicio + 'T00:00:00');
    const dataFim = new Date(dados.periodo_fim + 'T00:00:00');
    const periodoFormatado = dataInicio.toLocaleDateString('pt-BR') + ' a ' + dataFim.toLocaleDateString('pt-BR');
    
    // Construir mensagem
    let mensagem = `📊 *Resumo de Gastos*\n\n`;
    mensagem += `👤 *${dados.nome}*\n\n`;
    mensagem += `📅 *Período:* ${periodoFormatado}\n\n`;
    mensagem += `💰 *Total a Pagar:* R$ ${dados.total.toFixed(2).replace('.', ',')}\n\n`;
    
    // Lista de itens consumidos
    mensagem += `🍽️ *Itens Consumidos:*\n`;
    dados.itens.forEach((item, index) => {
        const dataItem = new Date(item.data + 'T00:00:00');
        mensagem += `${index + 1}. ${item.nome} - R$ ${parseFloat(item.valor_individual).toFixed(2).replace('.', ',')}\n`;
        mensagem += `   📅 ${dataItem.toLocaleDateString('pt-BR')} | 💳 Pago por: ${item.pagador}\n`;
    });
    
    // Informação sobre pagamento - mostrar dívidas por pagador
    const dividas = Object.values(dados.dividasPagador).filter(d => d.valor > 0 && d.pagador.nome !== dados.nome);
    
    if (dividas.length > 0) {
        mensagem += `\n💵 *Deve pagar:*\n`;
        dividas.forEach(divida => {
            mensagem += `   • Para *${divida.pagador.nome}*: R$ ${divida.valor.toFixed(2).replace('.', ',')}\n`;
        });
    } else if (dados.total > 0) {
        // Se não tem dívidas mas tem total, pode ser que seja um pagador
        mensagem += `\n✅ *Você não possui dívidas pendentes.*`;
    }
    
    // Limpar telefone (remover caracteres não numéricos)
    const telefoneLimpo = dados.telefone.replace(/\D/g, '');
    
    // Adicionar código do país se não tiver (assumindo Brasil - 55)
    const telefoneCompleto = telefoneLimpo.startsWith('55') ? telefoneLimpo : '55' + telefoneLimpo;
    
    // Codificar mensagem para URL
    const mensagemEncoded = encodeURIComponent(mensagem);
    
    // Abrir WhatsApp Web/App
    const urlWhatsApp = `https://wa.me/${telefoneCompleto}?text=${mensagemEncoded}`;
    
    // Detectar se é mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // No mobile, usar window.location.href diretamente
        // Isso funciona melhor para abrir o app nativo do WhatsApp
        window.location.href = urlWhatsApp;
    } else {
        // No desktop, usar window.open para abrir em nova aba
        window.open(urlWhatsApp, '_blank');
    }
}

// Exportar funções para uso global (se necessário)
window.ExpenseSplitter = {
    formatarMoeda,
    calcularTotalGasto,
    mostrarAlerta,
    calcularSemanaAtual,
    enviarWhatsApp
};

// CSS para auto-complete
// Verificar se o estilo já foi adicionado para evitar duplicação
if (!document.getElementById('autocomplete-styles')) {
    const style = document.createElement('style');
    style.id = 'autocomplete-styles';
    style.textContent = `
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
        }
        
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        
        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }
        
        .autocomplete-active {
            background-color: #007bff !important;
            color: #ffffff;
        }
    `;
    document.head.appendChild(style);
}