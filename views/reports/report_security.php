<?php $pageTitle = 'Relatório de Segurança'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>
<div class="topbar"><div><h4 class="mb-0 fw-bold">Relatório de Segurança</h4></div></div>
<div class="card">
  <div class="card-header">Verificações de Segurança</div>
  <div class="card-body">
    <table class="table small">
      <tr><td>HTTPS</td><td><span class="badge bg-warning">Não configurado</span></td></tr>
      <tr><td>Headers de segurança</td><td><span class="badge bg-warning">Parcial</span></td></tr>
      <tr><td>Uploads</td><td><span class="badge bg-warning">Verificar</span></td></tr>
      <tr><td>Sessões</td><td><span class="badge bg-success">OK</span></td></tr>
    </table>
    <p class="text-muted small mt-2">Caminho do banco: <code><?= DB_PATH ?></code></p>
    <p class="text-muted small">Pasta uploads: <code><?= UPLOAD_PATH ?></code></p>
  </div>
</div>
<?php include BASE_PATH . '/views/layout_footer.php'; ?>
