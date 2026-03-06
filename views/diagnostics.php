<?php $pageTitle = 'Diagnósticos'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-network-wired me-2 text-primary"></i>Diagnóstico de Rede</h4>
        <small class="text-muted">Ferramentas internas de suporte técnico</small>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-satellite-dish me-2"></i>Teste de Conectividade</div>
            <div class="card-body">
                <p class="text-muted small">Verifica a conectividade com hosts externos. Útil para diagnosticar problemas de rede e firewall.</p>
                <form method="GET" action="<?= BASE_URL ?>/index.php">
                    <input type="hidden" name="page" value="diagnostics">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">HOST / IP</label>
                        <input type="text" name="host" class="form-control font-monospace"
                               value="<?= htmlspecialchars($_GET['host'] ?? '') ?>"
                               placeholder="Ex: google.com, 8.8.8.8">
                        <div class="form-text">Insira um hostname ou endereço IP para testar</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-play me-1"></i> Executar Ping
                    </button>
                </form>

                <?php if (!empty($output)): ?>
                <div class="mt-3">
                    <label class="form-label small fw-semibold text-muted">SAÍDA:</label>
                    <pre class="bg-dark text-success p-3 rounded small" style="max-height:200px;overflow-y:auto;"><?= htmlspecialchars($output) ?></pre>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-server me-2"></i>Informações do Servidor</div>
            <div class="card-body">
                <table class="table table-sm mb-0 small">
                    <?php foreach ($serverInfo as $key => $value): ?>
                    <tr>
                        <td class="text-muted fw-semibold text-uppercase" style="font-size:0.7rem;"><?= str_replace('_', ' ', $key) ?></td>
                        <td class="font-monospace"><?= htmlspecialchars($value) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="fas fa-history me-2"></i>Últimas Execuções</div>
    <div class="card-body">
        <p class="text-muted small mb-0">
            Histórico de diagnósticos não está sendo registrado nesta versão. 
            <a href="<?= BASE_URL ?>/index.php?page=admin_logs">Ver logs gerais →</a>
        </p>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
