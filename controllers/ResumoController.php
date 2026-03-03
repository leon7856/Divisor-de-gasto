<?php
// controllers/ResumoController.php
class ResumoController {
    private $consumoController;
    private $usuarioController;
    private $itemController;

    public function __construct($db) {
        $this->consumoController = new ConsumoController($db);
        $this->usuarioController = new UsuarioController($db);
        $this->itemController = new ItemController($db);
    }

    public function gerarResumo($data_inicio, $data_fim) {
        // Buscar consumos do período
        $consumos = $this->consumoController->getConsumosPorPeriodo($data_inicio, $data_fim);
        $usuarios = $this->usuarioController->listarUsuarios();
        $pagador = $this->usuarioController->getPagador();
        $todosPagadores = $this->usuarioController->getAllPagadores();

        // Calcular total gasto (soma dos valores individuais)
        $totalGasto = 0;
        foreach($consumos as $consumo) {
            $totalGasto += $consumo['valor'];
        }

        // Organizar consumo por pessoa
        $consumoPorPessoa = [];
        foreach($usuarios as $usuario) {
            $consumoPorPessoa[$usuario['id']] = [
                'usuario' => $usuario,
                'itens' => [],
                'total' => 0,
                'dividasPorPagador' => [] // Novo: dívidas separadas por pagador
            ];
        }

        // Inicializar dívidas por pagador para cada pessoa
        foreach($todosPagadores as $pag) {
            foreach($consumoPorPessoa as $userId => &$pessoa) {
                $pessoa['dividasPorPagador'][$pag['id']] = [
                    'pagador' => $pag,
                    'valor' => 0
                ];
            }
        }

        // Agrupar consumos por item+data+pagador para mostrar agrupado
        $consumosAgrupados = [];
        foreach($consumos as $consumo) {
            $userId = $consumo['id_usuario'];
            $pagadorId = $consumo['id_pagador'];
            
            $consumoPorPessoa[$userId]['itens'][] = [
                'nome' => $consumo['nome_item'],
                'valor_individual' => $consumo['valor'],
                'data' => $consumo['data_consumo'],
                'pagador' => $consumo['nome_pagador'],
                'id_pagador' => $pagadorId
            ];
            
            $consumoPorPessoa[$userId]['total'] += $consumo['valor'];
            
            // Adicionar à dívida específica do pagador
            if(isset($consumoPorPessoa[$userId]['dividasPorPagador'][$pagadorId])) {
                $consumoPorPessoa[$userId]['dividasPorPagador'][$pagadorId]['valor'] += $consumo['valor'];
            }
        }
        
        // Calcular saldo líquido (quanto deve pagar) para cada pessoa
        foreach($consumoPorPessoa as $userId => &$pessoa) {
            $totalConsumido = $pessoa['total'];
            $totalPagoParaOutros = 0;
            $totalPagoParaSiMesmo = 0;
            
            // Calcular quanto esta pessoa pagou
            foreach($consumos as $consumo) {
                if($consumo['id_pagador'] == $userId) {
                    if($consumo['id_usuario'] == $userId) {
                        // Pagou para si mesmo - não conta como pagamento real
                        $totalPagoParaSiMesmo += $consumo['valor'];
                    } else {
                        // Pagou para outra pessoa - conta como pagamento
                        $totalPagoParaOutros += $consumo['valor'];
                    }
                }
            }
            
            // Saldo líquido = Total consumido - Total pago para outros
            // Pagamentos para si mesmo não contam, então subtraímos do total consumido
            // Exemplo: Consumiu R$ 15, pagou R$ 10 para si mesmo, deve R$ 5 para outro
            // Saldo = R$ 15 - R$ 10 (pago para si) - R$ 0 (pago para outros) = R$ 5
            // Mas se queremos mostrar 0 quando não deve nada, precisamos calcular diferente
            
            // Nova lógica: Saldo = Total que deve para outras pessoas
            // Se pagou para si mesmo, isso reduz o que ele deve
            $totalDevido = $totalConsumido - $totalPagoParaSiMesmo;
            $pessoa['saldoLiquido'] = $totalDevido - $totalPagoParaOutros;
        }
        unset($pessoa); // Liberar referência

        return [
            'periodo' => ['inicio' => $data_inicio, 'fim' => $data_fim],
            'totalGasto' => $totalGasto,
            'pagador' => $pagador,
            'todosPagadores' => $todosPagadores,
            'consumoPorPessoa' => $consumoPorPessoa,
            'consumos' => $consumos
        ];
    }
}
?>