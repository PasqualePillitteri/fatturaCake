<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Parameters seed.
 *
 * Parametri di sistema globali (tenant_id = null).
 * I parametri tenant sovrascrivono quelli globali quando presente.
 */
class ParametersSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * @return void
     */
    public function run(): void
    {
        $now = DateTime::now();

        $data = [
            // ========== GENERAL ==========
            ['tenant_id' => null, 'name' => 'app_name', 'value' => 1, 'opt1' => 'My Application', 'descr' => 'Nome applicazione', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_description', 'value' => 0, 'opt1' => '', 'descr' => 'Descrizione applicazione', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_keywords', 'value' => 0, 'opt1' => '', 'descr' => 'Keywords SEO (separate da virgola)', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_name', 'value' => 0, 'opt1' => '', 'descr' => 'Ragione sociale', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_vat', 'value' => 0, 'opt1' => '', 'descr' => 'Partita IVA', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_address', 'value' => 0, 'opt1' => '', 'descr' => 'Indirizzo sede', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_phone', 'value' => 0, 'opt1' => '', 'descr' => 'Telefono', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_email', 'value' => 0, 'opt1' => '', 'descr' => 'Email aziendale', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_pec', 'value' => 0, 'opt1' => '', 'descr' => 'PEC', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'company_website', 'value' => 0, 'opt1' => '', 'descr' => 'Sito web', 'category' => 'general', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== APPEARANCE ==========
            ['tenant_id' => null, 'name' => 'app_logo', 'value' => 0, 'opt1' => '', 'descr' => 'Logo applicazione (URL)', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_logo_dark', 'value' => 0, 'opt1' => '', 'descr' => 'Logo per tema scuro (URL)', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_favicon', 'value' => 0, 'opt1' => '', 'descr' => 'Favicon (URL)', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_theme', 'value' => 1, 'opt1' => 'light', 'descr' => 'Tema: light, dark, auto', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_primary_color', 'value' => 1, 'opt1' => '#0d6efd', 'descr' => 'Colore primario', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_secondary_color', 'value' => 1, 'opt1' => '#6c757d', 'descr' => 'Colore secondario', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_success_color', 'value' => 1, 'opt1' => '#198754', 'descr' => 'Colore successo', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_danger_color', 'value' => 1, 'opt1' => '#dc3545', 'descr' => 'Colore errore', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_footer_text', 'value' => 0, 'opt1' => '', 'descr' => 'Testo footer', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_custom_css', 'value' => 0, 'opt1' => '', 'descr' => 'CSS personalizzato', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'sidebar_collapsed', 'value' => 0, 'opt1' => '0', 'descr' => 'Sidebar chiusa di default', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'show_breadcrumbs', 'value' => 1, 'opt1' => '1', 'descr' => 'Mostra breadcrumbs', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_show_credit', 'value' => 0, 'opt1' => '0', 'descr' => 'Mostra credit footer', 'category' => 'appearance', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== MAIL ==========
            ['tenant_id' => null, 'name' => 'mail_from_email', 'value' => 1, 'opt1' => 'noreply@example.com', 'descr' => 'Email mittente', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_from_name', 'value' => 1, 'opt1' => 'App Mailer', 'descr' => 'Nome mittente', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_reply_to', 'value' => 0, 'opt1' => '', 'descr' => 'Email Reply-To', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_smtp_host', 'value' => 1, 'opt1' => 'localhost', 'descr' => 'SMTP Host', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_smtp_port', 'value' => 25, 'opt1' => '25', 'descr' => 'SMTP Port', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_smtp_user', 'value' => 0, 'opt1' => '', 'descr' => 'SMTP Username', 'category' => 'mail', 'display' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_smtp_pass', 'value' => 0, 'opt1' => '', 'descr' => 'SMTP Password', 'category' => 'mail', 'display' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_smtp_encryption', 'value' => 0, 'opt1' => '', 'descr' => 'SMTP Encryption (tls/ssl)', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_signature', 'value' => 0, 'opt1' => '', 'descr' => 'Firma email HTML', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_footer', 'value' => 0, 'opt1' => '', 'descr' => 'Footer email', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'mail_send_copy_to', 'value' => 0, 'opt1' => '', 'descr' => 'Invia copia email a', 'category' => 'mail', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== SYSTEM ==========
            ['tenant_id' => null, 'name' => 'maintenance_mode', 'value' => 0, 'opt1' => '0', 'descr' => 'Modalità manutenzione', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'maintenance_message', 'value' => 1, 'opt1' => 'Sito in manutenzione, torna presto!', 'descr' => 'Messaggio manutenzione', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'maintenance_allowed_ips', 'value' => 0, 'opt1' => '', 'descr' => 'IP ammessi in manutenzione (separati da virgola)', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'registration_enabled', 'value' => 1, 'opt1' => '1', 'descr' => 'Registrazione abilitata', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'registration_requires_approval', 'value' => 0, 'opt1' => '0', 'descr' => 'Registrazione richiede approvazione', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'default_user_role', 'value' => 1, 'opt1' => 'user', 'descr' => 'Ruolo default nuovi utenti', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'login_max_attempts', 'value' => 5, 'opt1' => '5', 'descr' => 'Max tentativi login', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'login_lockout_duration', 'value' => 900, 'opt1' => '900', 'descr' => 'Durata blocco login (secondi)', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'session_timeout', 'value' => 3600, 'opt1' => '3600', 'descr' => 'Timeout sessione (secondi)', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'password_min_length', 'value' => 8, 'opt1' => '8', 'descr' => 'Lunghezza minima password', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'password_require_special', 'value' => 0, 'opt1' => '0', 'descr' => 'Password richiede caratteri speciali', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'enable_2fa', 'value' => 0, 'opt1' => '0', 'descr' => 'Abilita autenticazione 2FA', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'api_rate_limit', 'value' => 60, 'opt1' => '60', 'descr' => 'Rate limit API (richieste/minuto)', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'debug_mode', 'value' => 0, 'opt1' => '0', 'descr' => 'Modalità debug', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'log_level', 'value' => 1, 'opt1' => 'warning', 'descr' => 'Livello log: error, warning, info, debug', 'category' => 'system', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== LOCALE ==========
            ['tenant_id' => null, 'name' => 'app_timezone', 'value' => 1, 'opt1' => 'Europe/Rome', 'descr' => 'Timezone', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_language', 'value' => 1, 'opt1' => 'it-IT', 'descr' => 'Lingua default', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_date_format', 'value' => 1, 'opt1' => 'dd/MM/yyyy', 'descr' => 'Formato data', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_time_format', 'value' => 1, 'opt1' => 'HH:mm', 'descr' => 'Formato ora', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_datetime_format', 'value' => 1, 'opt1' => 'dd/MM/yyyy HH:mm', 'descr' => 'Formato data/ora', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_currency', 'value' => 1, 'opt1' => 'EUR', 'descr' => 'Valuta', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_currency_symbol', 'value' => 1, 'opt1' => '€', 'descr' => 'Simbolo valuta', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_decimal_separator', 'value' => 1, 'opt1' => ',', 'descr' => 'Separatore decimali', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_thousands_separator', 'value' => 1, 'opt1' => '.', 'descr' => 'Separatore migliaia', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'app_first_day_of_week', 'value' => 1, 'opt1' => '1', 'descr' => 'Primo giorno settimana (0=Dom, 1=Lun)', 'category' => 'locale', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== NOTIFICATIONS ==========
            ['tenant_id' => null, 'name' => 'notify_email_enabled', 'value' => 1, 'opt1' => '1', 'descr' => 'Notifiche email abilitate', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'notify_new_user', 'value' => 1, 'opt1' => '1', 'descr' => 'Notifica nuova registrazione', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'notify_new_booking', 'value' => 1, 'opt1' => '1', 'descr' => 'Notifica nuova prenotazione', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'notify_booking_reminder', 'value' => 1, 'opt1' => '1', 'descr' => 'Promemoria prenotazioni', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'notify_reminder_days', 'value' => 1, 'opt1' => '1', 'descr' => 'Giorni anticipo promemoria', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'notify_admin_email', 'value' => 0, 'opt1' => '', 'descr' => 'Email admin per notifiche', 'category' => 'notifications', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== FEATURES ==========
            ['tenant_id' => null, 'name' => 'feature_todo', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita To-Do List', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_notes', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita Blocco Note', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_calendar', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita Calendario', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_export_pdf', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita export PDF', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_export_excel', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita export Excel', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_import', 'value' => 0, 'opt1' => '0', 'descr' => 'Abilita import dati', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_api', 'value' => 0, 'opt1' => '0', 'descr' => 'Abilita API', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'feature_bulk_actions', 'value' => 1, 'opt1' => '1', 'descr' => 'Abilita azioni bulk', 'category' => 'features', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== BOOKING ==========
            ['tenant_id' => null, 'name' => 'booking_min_days', 'value' => 1, 'opt1' => '1', 'descr' => 'Giorni minimi prenotazione', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_max_days', 'value' => 30, 'opt1' => '30', 'descr' => 'Giorni massimi prenotazione', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_advance_days', 'value' => 365, 'opt1' => '365', 'descr' => 'Giorni anticipo max prenotazione', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_checkin_time', 'value' => 1, 'opt1' => '15:00', 'descr' => 'Orario check-in', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_checkout_time', 'value' => 1, 'opt1' => '10:00', 'descr' => 'Orario check-out', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_confirmation_required', 'value' => 1, 'opt1' => '1', 'descr' => 'Conferma prenotazione richiesta', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_deposit_percent', 'value' => 30, 'opt1' => '30', 'descr' => 'Percentuale acconto', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_cancellation_days', 'value' => 7, 'opt1' => '7', 'descr' => 'Giorni per cancellazione gratuita', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_auto_confirm', 'value' => 0, 'opt1' => '0', 'descr' => 'Conferma automatica prenotazioni', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_send_reminder', 'value' => 1, 'opt1' => '1', 'descr' => 'Invia promemoria check-in', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'booking_reminder_days', 'value' => 3, 'opt1' => '3', 'descr' => 'Giorni prima per promemoria', 'category' => 'booking', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],

            // ========== INVOICING ==========
            ['tenant_id' => null, 'name' => 'invoice_prefix', 'value' => 1, 'opt1' => 'INV-', 'descr' => 'Prefisso fatture', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'invoice_next_number', 'value' => 1, 'opt1' => '1', 'descr' => 'Prossimo numero fattura', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'invoice_vat_rate', 'value' => 22, 'opt1' => '22', 'descr' => 'Aliquota IVA %', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'invoice_payment_terms', 'value' => 30, 'opt1' => '30', 'descr' => 'Termini pagamento (giorni)', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'invoice_notes', 'value' => 0, 'opt1' => '', 'descr' => 'Note standard fattura', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['tenant_id' => null, 'name' => 'invoice_bank_details', 'value' => 0, 'opt1' => '', 'descr' => 'Coordinate bancarie', 'category' => 'invoicing', 'display' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        $table = $this->table('parameters');
        $table->insert($data)->save();
    }
}
