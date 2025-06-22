<?php
// Variabili predefinite per i campi
$is_edit_mode = isset($contact['id']) && $contact['id'] !== null;
$form_title = $form_title ?? ($is_edit_mode ? 'Modifica Contatto' : 'Nuovo Contatto');
$submit_button_text = $submit_button_text ?? ($is_edit_mode ? 'Aggiorna' : 'Salva');
$order_executed_checked = isset($contact['order_executed']) && $contact['order_executed'] == 1 ? 'checked' : '';
?>

<h2 class="text-2xl font-semibold mb-4"><?php echo $form_title; ?></h2>

<!--  <form method="POST" action="<?php // echo $action_url; ?>" -->
<form method="POST" action="<?= $action_url ?>" enctype="multipart/form-data"><class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Colonna 1: Dati Personali -->
        <div class="space-y-4">
            <!-- Nome e Cognome (obbligatori) -->
            <div class="form-group">
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($contact['first_name'] ?? ''); ?>" 
                       required class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Cognome *</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($contact['last_name'] ?? ''); ?>" 
                       required class="w-full px-3 py-2 border rounded-md">
            </div>

            <!-- Contatti -->
            <div class="form-group">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefono Fisso</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($contact['phone'] ?? ''); ?>" 
                       pattern="[0-9\s\-\(\)\+]{7,20}" title="Formato: 02 1234567" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="mobile_phone" class="block text-sm font-medium text-gray-700 mb-1">Cellulare</label>
                <input type="tel" id="mobile_phone" name="mobile_phone" value="<?php echo htmlspecialchars($contact['mobile_phone'] ?? ''); ?>" 
                       pattern="[0-9\s\-\(\)\+]{7,20}" title="Formato: 333 1234567" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>

        <!-- Colonna 2: Dati Aziendali -->
        <div class="space-y-4">
            <!-- Campo Azienda (condizionale) -->
            <div class="form-group" id="company-field">
                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Azienda</label>
                <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($contact['company'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <!-- Tipo Cliente -->
            <div class="form-group">
                <label for="client_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo Cliente *</label>
                <select id="client_type" name="client_type" required class="w-full px-3 py-2 border rounded-md">
                    <option value="Privato" <?= ($contact['client_type'] ?? '') === 'Privato' ? 'selected' : '' ?>>Privato</option>
                    <option value="Ditta Individuale" <?= ($contact['client_type'] ?? '') === 'Ditta Individuale' ? 'selected' : '' ?>>Ditta Individuale</option>
                    <option value="Azienda/Società" <?= ($contact['client_type'] ?? '') === 'Azienda/Società' ? 'selected' : '' ?>>Azienda/Società</option>
                    <option value="Fornitore" <?= ($contact['client_type'] ?? '') === 'Fornitore' ? 'selected' : '' ?>>Fornitore</option>
                </select>
            </div>

            <!-- Campi Fiscali (condizionali) -->
            <div class="form-group fiscal-field" id="vat_number_field">
                <label for="vat_number" class="block text-sm font-medium text-gray-700 mb-1">P.IVA</label>
                <input type="text" id="vat_number" name="vat_number" value="<?php echo htmlspecialchars($contact['vat_number'] ?? ''); ?>" 
                       pattern="\d{11}" title="11 cifre" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field" id="tax_code_field">
                <label for="tax_code" class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
                <input type="text" id="tax_code" name="tax_code" value="<?php echo htmlspecialchars($contact['tax_code'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
                <p class="text-xs text-gray-500 mt-1">Es: RSSMRA80A01H501R</p>
            </div>

            <div class="form-group">
                <label for="pec" class="block text-sm font-medium text-gray-700 mb-1">PEC</label>
                <input type="email" id="pec" name="pec" value="<?php echo htmlspecialchars($contact['pec'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>

        <!-- Colonna 3: Dati Aggiuntivi -->
        <div class="space-y-4">
            <div class="form-group fiscal-field">
                <label for="sdi" class="block text-sm font-medium text-gray-700 mb-1">Codice SDI</label>
                <input type="text" id="sdi" name="sdi" value="<?php echo htmlspecialchars($contact['sdi'] ?? ''); ?>" 
                       pattern="[A-Z0-9]{7}" title="7 caratteri alfanumerici" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                <input type="text" id="company_address" name="company_address" 
                       value="<?php echo htmlspecialchars($contact['company_address'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_city" class="block text-sm font-medium text-gray-700 mb-1">Città</label>
                <input type="text" id="company_city" name="company_city" 
                       value="<?php echo htmlspecialchars($contact['company_city'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_zip" class="block text-sm font-medium text-gray-700 mb-1">CAP</label>
                <input type="text" id="company_zip" name="company_zip" 
                       value="<?php echo htmlspecialchars($contact['company_zip'] ?? ''); ?>" 
                       pattern="\d{5}" title="5 cifre" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group fiscal-field">
                <label for="company_province" class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
                <input type="text" id="company_province" name="company_province" 
                       value="<?php echo htmlspecialchars($contact['company_province'] ?? ''); ?>" 
                       pattern="[A-Z]{2}" title="2 lettere" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="last_contact_date" class="block text-sm font-medium text-gray-700 mb-1">Ultimo Contatto</label>
                <input type="date" id="last_contact_date" name="last_contact_date" 
                       value="<?php echo htmlspecialchars($contact['last_contact_date'] ?? ''); ?>" 
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="form-group">
                <label for="contact_medium" class="block text-sm font-medium text-gray-700 mb-1">Mezzo Contatto</label>
                <select id="contact_medium" name="contact_medium" class="w-full px-3 py-2 border rounded-md">
                    <option value="">Seleziona...</option>
                    <option value="Telefono" <?= ($contact['contact_medium'] ?? '') === 'Telefono' ? 'selected' : '' ?>>Telefono</option>
                    <option value="Email" <?= ($contact['contact_medium'] ?? '') === 'Email' ? 'selected' : '' ?>>Email</option>
                    <option value="Meeting" <?= ($contact['contact_medium'] ?? '') === 'Meeting' ? 'selected' : '' ?>>Meeting</option>
                    <option value="Altro" <?= ($contact['contact_medium'] ?? '') === 'Altro' ? 'selected' : '' ?>>Altro</option>
                </select>
            </div>

         <div class="form-group flex items-center">
                <input type="checkbox" id="order_executed" name="order_executed" value="1" 
                       <?php echo $order_executed_checked; ?> class="h-4 w-4 text-indigo-600 rounded">
                <label for="order_executed" class="ml-2 text-sm text-gray-700">Ordine Eseguito</label>
            </div>
        </div>
    </div> 

    <div class="mt-8 flex justify-end space-x-3">
        <a href="<?php echo htmlspecialchars($cancel_url); ?>" class="btn btn-secondary">Annulla</a>
        <button type="submit" class="btn btn-primary"><?php echo $submit_button_text; ?></button>
    </div>
</form>

// <script>
// document.querySelector('form').addEventListener('submit', function(e) {
//    e.preventDefault();
//    console.log("Dati che verranno inviati:", new FormData(this));
//    this.submit(); // Rimuovi questa riga dopo il debug
// });
// </script>

<!-- Script per gestione dinamica dei campi -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientType = document.getElementById('client_type');
    const fiscalFields = document.querySelectorAll('.fiscal-field');
    const companyField = document.getElementById('company-field');

    function toggleFields() {
        const isPrivate = clientType.value === 'Privato';
        
        // Mostra/nascondi campi fiscali e azienda
        fiscalFields.forEach(field => field.style.display = isPrivate ? 'none' : 'block');
        companyField.style.display = isPrivate ? 'none' : 'block';
        
        // Resetta i valori se nascosti
        if(isPrivate) {
            fiscalFields.forEach(field => {
                const input = field.querySelector('input');
                if(input) input.value = '';
            });
            document.getElementById('company').value = '';
        }
    }

    // Esegui al cambiamento e al caricamento
    clientType.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
