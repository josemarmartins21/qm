<?php
include "../config/database.php";
include "../layout/header.php";

$sql = "SELECT f.*, c.primeiro_nome 
        FROM faturas f
        JOIN client c ON c.client_id = f.client_id
        ORDER BY f.data_emissao DESC";
$result = $qmanager->query($sql);
?>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">
        <i class="bi bi-receipt me-2"></i>Faturas
    </h4>
    <a href="create.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nova Fatura
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4">ID</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($f = $result->fetch_assoc()): 
                        // Determinar classe do badge de status
                        $badgeClass = $f['status']=='paga' ? 'success' : ($f['status']=='vencida' ? 'danger' : 'warning');
                        
                        // Determinar ícone do status
                        $iconClass = $f['status']=='paga' ? 'check-circle' : ($f['status']=='vencida' ? 'exclamation-triangle' : 'clock');
                    ?>
                    <tr>
                        <td class="px-4 fw-bold text-secondary">
                            #<?= str_pad($f['fatura_id'], 4, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <?= htmlspecialchars($f['primeiro_nome']) ?>
                            </div>
                        </td>
                        <td class="fw-bold text-dark">
                            <?= number_format($f['valor'], 2, ',', '.') ?> Kz
                        </td>
                        <td>
                            <i class="bi bi-calendar-event text-secondary me-1"></i>
                            <?= date('d/m/Y', strtotime($f['data_vencimento'])) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $badgeClass ?> d-inline-flex align-items-center">
                                <i class="bi bi-<?= $iconClass ?> me-1"></i>
                                <?= ucfirst($f['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                
                                <!-- IMPRIMIR -->
                                <a href="print_fatura.php?id=<?= $f['fatura_id'] ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="Imprimir Fatura"
                                   target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                
                                <!-- PDF -->
                                <a href="pdf_fatura.php?id=<?= $f['fatura_id'] ?>" 
                                   class="btn btn-outline-danger btn-sm" 
                                   title="Baixar PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                
                                <!-- BAIXAR -->
                                <a href="download_fatura.php?id=<?= $f['fatura_id'] ?>" 
                                   class="btn btn-outline-success btn-sm" 
                                   title="Baixar Dados">
                                    <i class="bi bi-download"></i>
                                </a>
                                
                                <!-- PAGAR -->
                                <?php if($f['status'] != 'paga'): ?>
                                <a href="pay.php?id=<?= $f['fatura_id'] ?>" 
                                   class="btn btn-success btn-sm" 
                                   title="Registrar Pagamento">
                                    <i class="bi bi-cash-coin me-1"></i>Pagar
                                </a>
                                <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="bi bi-check-all me-1"></i>Paga
                                </button>
                                <?php endif; ?>
                                
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Nenhuma fatura encontrada
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>