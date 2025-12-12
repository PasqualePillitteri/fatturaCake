<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ExcelImportService;
use App\Service\ExcelTemplateService;
use App\Service\XmlImportService;
use Cake\Http\Response;

/**
 * Import Controller
 *
 * Handles Excel import for invoices and related data.
 */
class ImportController extends AppController
{
    /**
     * Upload directory for import files.
     */
    protected const UPLOAD_DIR = TMP . 'imports' . DS;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Disable CRUD for this controller
        if ($this->components()->has('Crud')) {
            $this->components()->unload('Crud');
        }
    }

    /**
     * Before filter - unlock file fields for form protection.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): ?\Cake\Http\Response
    {
        parent::beforeFilter($event);

        // Unlock file upload and option fields from form protection
        if ($this->components()->has('FormProtection')) {
            $this->FormProtection->setConfig('unlockedFields', [
                'excel_file',
                'xml_file',
                'create_anagrafiche',
                'create_prodotti',
                'skip_errors',
                'skip_duplicates',
                'tipo_default',
            ]);
        }

        return null;
    }

    /**
     * Index action - redirect to fatture.
     *
     * @return \Cake\Http\Response
     */
    public function index(): \Cake\Http\Response
    {
        return $this->redirect(['action' => 'fatture']);
    }

    /**
     * Fatture import page - upload form.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function fatture()
    {
        $this->set('title', 'Import Fatture da Excel');
    }

    /**
     * Download import template.
     *
     * @return \Cake\Http\Response
     */
    public function downloadTemplate(): Response
    {
        $service = new ExcelTemplateService();
        $content = $service->getTemplateContent();

        $response = $this->response
            ->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="import_fatture_template.xlsx"')
            ->withHeader('Cache-Control', 'no-cache, must-revalidate')
            ->withStringBody($content);

        return $response;
    }

    /**
     * Preview uploaded file.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function preview()
    {
        $this->request->allowMethod(['post']);

        // Get uploaded file
        $uploadedFile = $this->request->getUploadedFile('excel_file');

        if (!$uploadedFile || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->Flash->error(__('Errore nel caricamento del file.'));

            return $this->redirect(['action' => 'fatture']);
        }

        // Validate file type
        $clientFilename = $uploadedFile->getClientFilename();
        $extension = strtolower(pathinfo($clientFilename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['xlsx', 'xls'])) {
            $this->Flash->error(__('Formato file non valido. Utilizzare file .xlsx o .xls'));

            return $this->redirect(['action' => 'fatture']);
        }

        // Ensure upload directory exists
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Save file temporarily
        $tempFilename = uniqid('import_') . '.' . $extension;
        $tempPath = self::UPLOAD_DIR . $tempFilename;
        $uploadedFile->moveTo($tempPath);

        // Store in session for later import
        $this->request->getSession()->write('import.filepath', $tempPath);
        $this->request->getSession()->write('import.filename', $clientFilename);

        // Parse and validate
        $service = new ExcelImportService();

        if (!$service->parseFile($tempPath)) {
            $this->Flash->error(__('Errore nella lettura del file: ') . implode(', ', $service->getErrors()));

            return $this->redirect(['action' => 'fatture']);
        }

        // Validate data
        $service->validate();

        $previewData = $service->getPreviewData();

        $this->set('filename', $clientFilename);
        $this->set('preview', $previewData);
        $this->set('hasErrors', $service->hasErrors());
        $this->set('title', 'Anteprima Import');
    }

    /**
     * Execute the import.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function execute()
    {
        $this->request->allowMethod(['post']);

        $session = $this->request->getSession();
        $filepath = $session->read('import.filepath');
        $filename = $session->read('import.filename');

        if (!$filepath || !file_exists($filepath)) {
            $this->Flash->error(__('Sessione scaduta. Ricaricare il file.'));

            return $this->redirect(['action' => 'fatture']);
        }

        // Get tenant ID
        $tenantId = $this->getCurrentTenantId();
        if (!$tenantId) {
            $this->Flash->error(__('Tenant non configurato.'));

            return $this->redirect(['action' => 'fatture']);
        }

        // Get options from form
        $options = [
            'create_anagrafiche' => (bool)$this->request->getData('create_anagrafiche', true),
            'create_prodotti' => (bool)$this->request->getData('create_prodotti', true),
            'skip_errors' => (bool)$this->request->getData('skip_errors', false),
        ];

        // Execute import
        $service = new ExcelImportService();

        if (!$service->parseFile($filepath)) {
            $this->Flash->error(__('Errore nella lettura del file.'));

            return $this->redirect(['action' => 'fatture']);
        }

        if (!$service->validate() && !$options['skip_errors']) {
            $this->Flash->error(__('File contiene errori di validazione.'));

            return $this->redirect(['action' => 'preview']);
        }

        $success = $service->import($tenantId, $options);

        // Cleanup
        @unlink($filepath);
        $session->delete('import.filepath');
        $session->delete('import.filename');

        $stats = $service->getStats();

        $this->set('success', $success);
        $this->set('stats', $stats);
        $this->set('errors', $service->getErrors());
        $this->set('filename', $filename);
        $this->set('title', 'Risultato Import');
    }

    /**
     * Get current tenant ID.
     *
     * @return int|null
     */
    protected function getCurrentTenantId(): ?int
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity) {
            return $identity->get('tenant_id');
        }

        return null;
    }

    /**
     * XML/ZIP import page - upload form.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function fattureXml()
    {
        $this->set('title', 'Import Fatture da XML/ZIP');
    }

    /**
     * Preview uploaded XML/ZIP file.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function previewXml()
    {
        $this->request->allowMethod(['post']);

        $uploadedFile = $this->request->getUploadedFile('xml_file');

        if (!$uploadedFile || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->Flash->error(__('Errore nel caricamento del file.'));

            return $this->redirect(['action' => 'fattureXml']);
        }

        $clientFilename = $uploadedFile->getClientFilename();
        $extension = strtolower(pathinfo($clientFilename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['xml', 'zip', 'p7m'])) {
            $this->Flash->error(__('Formato file non valido. Utilizzare file .xml, .zip o .p7m'));

            return $this->redirect(['action' => 'fattureXml']);
        }

        // Ensure upload directory exists
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Save file temporarily
        $tempFilename = uniqid('import_xml_') . '.' . $extension;
        $tempPath = self::UPLOAD_DIR . $tempFilename;
        $uploadedFile->moveTo($tempPath);

        // Store in session
        $this->request->getSession()->write('import_xml.filepath', $tempPath);
        $this->request->getSession()->write('import_xml.filename', $clientFilename);

        // Parse file
        $service = new XmlImportService();

        if (!$service->parseFile($tempPath)) {
            $this->Flash->error(__('Errore nella lettura del file: ') . implode(', ', $service->getErrors()));

            return $this->redirect(['action' => 'fattureXml']);
        }

        // Validate
        $service->validate();

        $previewData = $service->getPreviewData();

        $this->set('filename', $clientFilename);
        $this->set('preview', $previewData);
        $this->set('hasErrors', $service->hasErrors());
        $this->set('title', 'Anteprima Import XML');
    }

    /**
     * Execute XML import.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function executeXml()
    {
        $this->request->allowMethod(['post']);

        $session = $this->request->getSession();
        $filepath = $session->read('import_xml.filepath');
        $filename = $session->read('import_xml.filename');

        if (!$filepath || !file_exists($filepath)) {
            $this->Flash->error(__('Sessione scaduta. Ricaricare il file.'));

            return $this->redirect(['action' => 'fattureXml']);
        }

        $tenantId = $this->getCurrentTenantId();
        if (!$tenantId) {
            $this->Flash->error(__('Tenant non configurato.'));

            return $this->redirect(['action' => 'fattureXml']);
        }

        // Get options from form
        $options = [
            'create_anagrafiche' => (bool)$this->request->getData('create_anagrafiche', true),
            'skip_duplicates' => (bool)$this->request->getData('skip_duplicates', true),
            'tipo_default' => $this->request->getData('tipo_default', 'passiva'),
        ];

        // Execute import
        $service = new XmlImportService();

        if (!$service->parseFile($filepath)) {
            $this->Flash->error(__('Errore nella lettura del file.'));

            return $this->redirect(['action' => 'fattureXml']);
        }

        $service->validate();
        $success = $service->import($tenantId, $options);

        // Cleanup
        @unlink($filepath);
        $session->delete('import_xml.filepath');
        $session->delete('import_xml.filename');

        $stats = $service->getStats();

        $this->set('success', $success);
        $this->set('stats', $stats);
        $this->set('errors', $service->getErrors());
        $this->set('warnings', $service->getWarnings());
        $this->set('filename', $filename);
        $this->set('title', 'Risultato Import XML');
    }
}
