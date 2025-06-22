<?php
// app/views/contacts/form.php

// Le variabili $contact (se in modalità modifica), $form_title, $submit_button_text, $action_url, $cancel_url
// e $client_types (ora array di stringhe), $users_for_assignment sono passate dal ContactController.

$is_edit_mode = isset($contact['id']) && $contact['id'] !== null;
$form_title = $form_title ?? ($is_edit_mode ? 'Modifica Contatto' : 'Crea Nuovo Contatto');
$submit_button_text = $submit_button_text ?? ($is_edit_mode ? 'Aggiorna Contatto' : 'Salva Contatto');

// Gestione degli URL per add/edit: Assicurati che $contact['id'] sia gestito anche se non esiste
$contact_id_for_url = htmlspecialchars($contact['id'] ?? ''); // Utilizza ?? '' per evitare null con htmlspecialchars

$action_url = $action_url ?? ($is_edit_mode ? "index.php?route=contacts/edit&id=" . $contact_id_for_url : "index.php?route=contacts/add");
$cancel_url = $cancel_url ?? ($is_edit_mode ? "index.php?route=contacts/view&id=" . $contact_id_for_url : "index.php?route=contacts/list");

// Pre-popola i valori del form, usando l'operatore di coalescenza per gestire valori null
$first_name = htmlspecialchars($contact['first_name'] ?? '');
$last_name = htmlspecialchars($contact['last_name'] ?? '');
$email = htmlspecialchars($contact['email'] ?? '');
$phone = htmlspecialchars($contact['phone'] ?? '');
$company = htmlspecialchars($contact['company'] ?? '');
$last_contact_date = htmlspecialchars($contact['last_contact_date'] ?? '');
$contact_medium = htmlspecialchars($contact['contact_medium'] ?? '');
$order_executed = isset($contact['order_executed']) && $contact['order_executed'] == 1 ? 'checked' : '';
$client_type = htmlspecialchars($contact['client_type'] ?? ''); // ! Importante: usa client_type (stringa)
$tax_code = htmlspecialchars($contact['tax_code'] ?? '');
$vat_number = htmlspecialchars($contact['vat_number'] ?? '');
$sdi = htmlspecialchars($contact['sdi'] ?? '');
$company_address = htmlspecialchars($contact['company_address'] ?? '');
$company_city = htmlspecialchars($contact['company_city'] ?? '');
$company_zip = htmlspecialchars($contact['company_zip'] ?? '');
$company_province = htmlspecialchars($contact['company_province'] ?? '');
$pec = htmlspecialchars($contact['pec'] ?? '');
$mobile_phone = htmlspecialchars($contact['mobile_phone'] ?? '');
$assigned_to_user_id = htmlspecialchars($contact['assigned_to_user_id'] ?? '');
$contact_status = htmlspecialchars($contact['status'] ?? 'New');

$contact_statuses = ['New', 'Contacted', 'Qualified', 'Lost', 'Won', 'Active Client']; // Stati contatto

?>

<h2 class="text-2xl font-semibold mb-4"><?php echo $form_title; ?></h2>

<!-- Messaggio flash per errori di validazione del form o successo -->
<?php if (!empty($_SESSION['message'])): ?>
    <div class="flash-message flash-<?php echo htmlspecialchars($_SESSION['message_type']); ?> mb-4">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
    </div>
    <?php 
    // Pulisci il messaggio flash dopo averlo visualizzato nel form
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
<?php endif; ?>

<form method="POST" action="<?= $action_url ?>" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md max-w-full mx-auto">
    <?php if ($is_edit_mode): ?>
        <!-- Campo nascosto per l'ID del contatto in modalità modifica -->
        <input type="hidden" name="id" value="<?php echo $contact_id_for_url; ?>">
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Colonna 1: Dati Personali -->
        <div class="space-y-4">
            <!-- Nome e Cognome (obbligatori) -->
            <div class="form-group">
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" 
                        required class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Cognome *</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" 
                        required class="w-full px-3 py-2 border rounded-md">
            </div>

            <!-- Contatti -->
            <div class="form-group">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefono Fisso</label>
                <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" 
                        pattern="[0-9\s\-\(\)\+]{7,20}" title="Formato: 02 1234567" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="mobile_phone" class="block text-sm font-medium text-gray-700 mb-1">Cellulare</label>
                <input type="tel" id="mobile_phone" name="mobile_phone" value="<?php echo $mobile_phone; ?>" 
                        pattern="[0-9\s\-\(\)\+]{7,20}" title="Formato: 333 1234567" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>

        <!-- Colonna 2: Dati Aziendali -->
        <div class="space-y-4">
            <!-- Campo Azienda (condizionale) -->
            <div class="form-group" id="company-field">
                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Azienda</label>
                <input type="text" id="company" name="company" value="<?php echo $company; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="client_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo Cliente *</label>
                <select id="client_type" name="client_type" required class="w-full px-3 py-2 border rounded-md">
                    <option value="">Seleziona Tipo</option>
                    <?php 
                    // $client_types è ora un array di stringhe (es. ['Privato', 'Azienda'])
                    foreach ($client_types ?? [] as $type_name): ?>
                        <option value="<?php echo htmlspecialchars($type_name); ?>"
                                <?php echo ($client_type == $type_name) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Campi Fiscali (condizionali) -->
            <div class="form-group fiscal-field" id="vat_number_field">
                <label for="vat_number" class="block text-sm font-medium text-gray-700 mb-1">P.IVA</label>
                <input type="text" id="vat_number" name="vat_number" value="<?php echo $vat_number; ?>" 
                        pattern="\d{11}" title="11 cifre" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field" id="tax_code_field">
                <label for="tax_code" class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
                <input type="text" id="tax_code" name="tax_code" value="<?php echo $tax_code; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
                <p class="text-xs text-gray-500 mt-1">Es: RSSMRA80A01H501R</p>
            </div>

            <div class="form-group">
                <label for="pec" class="block text-sm font-medium text-gray-700 mb-1">PEC</label>
                <input type="email" id="pec" name="pec" value="<?php echo $pec; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>

        <!-- Colonna 3: Dati Aggiuntivi -->
        <div class="space-y-4">
            <div class="form-group fiscal-field">
                <label for="sdi" class="block text-sm font-medium text-gray-700 mb-1">Codice SDI</label>
                <input type="text" id="sdi" name="sdi" value="<?php echo $sdi; ?>" 
                        pattern="[A-Z0-9]{7}" title="7 caratteri alfanumerici" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                <input type="text" id="company_address" name="company_address" 
                        value="<?php echo $company_address; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_city" class="block text-sm font-medium text-gray-700 mb-1">Città</label>
                <input type="text" id="company_city" name="company_city" 
                        value="<?php echo $company_city; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_zip" class="block text-sm font-medium text-gray-700 mb-1">CAP</label>
                <input type="text" id="company_zip" name="company_zip" 
                        value="<?php echo $company_zip; ?>" 
                        pattern="\d{5}" title="5 cifre" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_province" class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
                <input type="text" id="company_province" name="company_province" 
                        value="<?php echo $company_province; ?>" 
                        pattern="[A-Z]{2}" title="2 lettere" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="last_contact_date" class="block text-sm font-medium text-gray-700 mb-1">Ultimo Contatto</label>
                <input type="date" id="last_contact_date" name="last_contact_date" 
                        value="<?php echo $last_contact_date; ?>" 
                        class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="contact_medium" class="block text-sm font-medium text-gray-700 mb-1">Mezzo Contatto</label>
                <select id="contact_medium" name="contact_medium" class="w-full px-3 py-2 border rounded-md">
                    <option value="">Seleziona...</option>
                    <option value="Telefono" <?= ($contact_medium ?? '') === 'Telefono' ? 'selected' : '' ?>>Telefono</option>
                    <option value="Email" <?= ($contact_medium ?? '') === 'Email' ? 'selected' : '' ?>>Email</option>
                    <option value="Meeting" <?= ($contact_medium ?? '') === 'Meeting' ? 'selected' : '' ?>>Meeting</option>
                    <option value="Altro" <?= ($contact_medium ?? '') === 'Altro' ? 'selected' : '' ?>>Altro</option>
                </select>
            </div>

           <div class="form-group flex items-center">
                <input type="checkbox" id="order_executed" name="order_executed" value="1" 
                        <?php echo $order_executed; ?> class="h-4 w-4 text-indigo-600 rounded">
                <label for="order_executed" class="ml-2 text-sm text-gray-700">Ordine Eseguito</label>
            </div>
        </div>
    </div> 

    <div class="mt-8 flex justify-end space-x-3">
        <a href="<?php echo htmlspecialchars($cancel_url); ?>" class="btn btn-secondary">Annulla</a>
        <button type="submit" class="btn btn-primary"><?php echo $submit_button_text; ?></button>
    </div>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    // Questo e.preventDefault() è utile per permettere al console.log di mostrare i dati prima dell'invio.
    // Se non hai bisogno di validazioni JS complesse prima dell'invio e vuoi solo il log,
    // puoi lasciare così per il debug. Una volta finito il debug, potresti voler
    // rimuovere e.preventDefault() e this.submit() per lasciare che il browser gestisca l'invio normale.
    e.preventDefault(); 
    console.log("Dati che verranno inviati:", new FormData(this));
    this.submit(); // Rimuovi questa riga dopo il debug se e.preventDefault() non è più necessario
});
</script>

<!-- Script per gestione dinamica dei campi -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selettore ora per 'client_type' (stringa)
    const clientTypeSelect = document.getElementById('client_type'); 
    const fiscalFields = document.querySelectorAll('.fiscal-field');
    const companyField = document.getElementById('company-field');

    function toggleFields() {
        const selectedClientType = clientTypeSelect.value;
        const isPrivate = (selectedClientType === 'Privato'); // Confronta direttamente la stringa
        
        // Mostra/nascondi campi fiscali e azienda
        fiscalFields.forEach(field => field.style.display = isPrivate ? 'none' : 'block');
        companyField.style.display = isPrivate ? 'none' : 'block';
        
        // Resetta i valori se nascosti (utile per evitare invio di dati non pertinenti)
        if(isPrivate) {
            fiscalFields.forEach(field => {
                const input = field.querySelector('input');
                if(input) input.value = '';
            });
            document.getElementById('company').value = '';
            document.getElementById('pec').value = ''; 
        }
    }

    // Esegui la funzione al cambiamento e al caricamento
    clientTypeSelect.addEventListener('change', toggleFields);
    toggleFields(); // Esegui al caricamento della pagina per impostare lo stato iniziale
});
</script>
