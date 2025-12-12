<?php
/**
 * Export Fatture - Selection Page
 *
 * @var \App\View\AppView $this
 */
?>
<div class="export-fatture content">
    <div class="page-header">
        <h3><i data-lucide="download" style="width:24px;height:24px;"></i> <?= __('Export Fatture') ?></h3>
    </div>

    <div class="row">
        <!-- Filtri -->
        <div class="col-lg-4 mb-4">
            <div class="form-card">
                <div class="form-card-header">
                    <i data-lucide="filter" style="width:18px;height:18px;"></i>
                    Filtri Export
                </div>
                <div class="form-card-body">
                    <?= $this->Form->create(null, ['type' => 'get', 'id' => 'export-filters']) ?>

                    <div class="mb-3">
                        <label class="form-label">Direzione Fattura</label>
                        <select name="tipo" class="form-select">
                            <option value="">Tutte</option>
                            <option value="emessa">Emesse (vendita)</option>
                            <option value="ricevuta">Ricevute (acquisto)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data Da</label>
                        <input type="date" name="data_da" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data A</label>
                        <input type="date" name="data_a" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stato</label>
                        <select name="stato" class="form-select">
                            <option value="">Tutti</option>
                            <option value="bozza">Bozza</option>
                            <option value="registrata">Registrata</option>
                            <option value="inviata">Inviata</option>
                            <option value="consegnata">Consegnata</option>
                            <option value="pagata">Pagata</option>
                        </select>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-lg-8">
            <div class="row">
                <!-- Export Excel -->
                <div class="col-md-6 mb-4">
                    <div class="form-card h-100">
                        <div class="form-card-header bg-success text-white">
                            <i data-lucide="file-spreadsheet" style="width:18px;height:18px;"></i>
                            Export Excel
                        </div>
                        <div class="form-card-body">
                            <p class="mb-3">
                                Esporta le fatture in formato <strong>Excel (.xlsx)</strong> con due fogli:
                            </p>
                            <ul class="mb-4">
                                <li><strong>Fatture</strong> - Dati principali</li>
                                <li><strong>Righe</strong> - Dettaglio righe</li>
                            </ul>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-light text-dark">Fatture attive</span>
                                <span class="badge bg-light text-dark">Fatture passive</span>
                            </div>
                            <button type="button" class="btn btn-success w-100" onclick="exportFatture('excel')">
                                <i data-lucide="download" style="width:16px;height:16px;"></i>
                                Scarica Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Export XML -->
                <div class="col-md-6 mb-4">
                    <div class="form-card h-100">
                        <div class="form-card-header bg-primary text-white">
                            <i data-lucide="file-code" style="width:18px;height:18px;"></i>
                            Export XML (FatturaPA)
                        </div>
                        <div class="form-card-body">
                            <p class="mb-3">
                                Esporta le fatture in formato <strong>XML FatturaPA</strong> (archivio ZIP):
                            </p>
                            <ul class="mb-4">
                                <li>Standard SDI v1.2</li>
                                <li>Un file XML per fattura</li>
                            </ul>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary">Solo fatture attive</span>
                            </div>
                            <button type="button" class="btn btn-primary w-100" onclick="exportFatture('xml')">
                                <i data-lucide="download" style="width:16px;height:16px;"></i>
                                Scarica XML (ZIP)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <div class="d-flex">
                    <i data-lucide="info" style="width:20px;height:20px;" class="me-2 flex-shrink-0"></i>
                    <div>
                        <strong>Nota:</strong> L'export XML FatturaPA e disponibile solo per le fatture <strong>attive</strong> (emesse).
                        Le fatture passive (ricevute) possono essere esportate solo in formato Excel.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->append('script'); ?>
<script>
function exportFatture(format) {
    const form = document.getElementById('export-filters');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    let url = '/export/' + format;
    if (params) {
        url += '?' + params;
    }

    window.location.href = url;
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
<?php $this->end(); ?>
