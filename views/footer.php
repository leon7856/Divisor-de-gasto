<?php
// views/footer.php
?>
    </div> <!-- Fechamento do container -->

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-money-bill-wave me-2"></i>Divisor de Gastos</h5>
                    <p class="mb-0">Sistema para divisão de gastos semanais entre amigos.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-code me-1"></i>
                        Desenvolvido com PHP 7.4+, MySQL e Bootstrap 5
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        <?php echo date('d/m/Y H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">Tem certeza que deseja realizar esta ação?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Detalhes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsModalBody">
                    <!-- Conteúdo dinâmico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js para gráficos (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <?php
    // Scripts específicos por página
    switch($current_page) {
        case 'resumo':
            echo '
            <script>
            // Gráfico de divisão de gastos
            function renderizarGrafico(resumoData) {
                const ctx = document.getElementById("graficoGastos");
                if (!ctx) return;
                
                const labels = [];
                const valores = [];
                const cores = [];
                
                Object.values(resumoData.consumoPorPessoa).forEach((pessoa, index) => {
                    if (pessoa.total > 0) {
                        labels.push(pessoa.usuario.nome);
                        valores.push(pessoa.total);
                        cores.push(getCorPorIndex(index));
                    }
                });
                
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            backgroundColor: cores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom"
                            },
                            title: {
                                display: true,
                                text: "Divisão de Gastos"
                            }
                        }
                    }
                });
            }
            
            function getCorPorIndex(index) {
                const cores = [
                    "#007bff", "#28a745", "#dc3545", "#ffc107", "#6f42c1",
                    "#e83e8c", "#fd7e14", "#20c997", "#6610f2", "#0dcaf0"
                ];
                return cores[index % cores.length];
            }
            </script>
            ';
            break;
            
        case 'itens':
            echo '
            <script>
            // Ordenação da tabela de itens
            function ordenarTabela(colIndex) {
                const table = document.querySelector(".table");
                const tbody = table.querySelector("tbody");
                const rows = Array.from(tbody.querySelectorAll("tr"));
                
                const isNumeric = colIndex === 1; // Coluna de valor
                
                rows.sort((a, b) => {
                    const aVal = a.cells[colIndex].textContent.trim();
                    const bVal = b.cells[colIndex].textContent.trim();
                    
                    if (isNumeric) {
                        const aNum = parseFloat(aVal.replace("R$ ", "").replace(",", "."));
                        const bNum = parseFloat(bVal.replace("R$ ", "").replace(",", "."));
                        return aNum - bNum;
                    } else {
                        return aVal.localeCompare(bVal);
                    }
                });
                
                // Reinserir linhas ordenadas
                rows.forEach(row => tbody.appendChild(row));
            }
            </script>
            ';
            break;
    }
    ?>
</body>
</html>