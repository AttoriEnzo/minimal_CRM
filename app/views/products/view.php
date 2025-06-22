<!-- app/views/products/view.php -->
<h2 class="text-2xl font-semibold mb-4">Dettagli Prodotto</h2>

<table class="table-auto w-full bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <tr>
        <th class="text-left p-2">Codice Prodotto:</th>
        <td class="p-2"><?= htmlspecialchars($product_data['product_code'] ?? '') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Tipo Prodotto:</th>
        <td class="p-2"><?= htmlspecialchars($product_data['product_type'] ?? '') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Nome Prodotto:</th>
        <td class="p-2"><?= htmlspecialchars($product_data['product_name'] ?? '') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Descrizione:</th>
        <td class="p-2"><?= nl2br(htmlspecialchars($product_data['description'] ?? '')) ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Prezzo di Listino Netto:</th>
        <td class="p-2">€ <?= htmlspecialchars($product_data['default_price_net'] ?? '0.00') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Prezzo di Listino Lordo:</th>
        <td class="p-2">€ <?= htmlspecialchars($product_data['default_price_gross'] ?? '0.00') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Ampere:</th>
        <td class="p-2"><?= htmlspecialchars($product_data['amperes'] ?? '') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Volt:</th>
        <td class="p-2"><?= htmlspecialchars($product_data['volts'] ?? '') ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Altre Specifiche:</th>
        <td class="p-2"><?= nl2br(htmlspecialchars($product_data['other_specs'] ?? '')) ?></td>
    </tr>
    <tr>
        <th class="text-left p-2">Attivo nel Catalogo:</th>
        <td class="p-2">
            <form method="POST" action="index.php?page=products_catalog&action=toggle_active&id=<?= htmlspecialchars($product_data['id']) ?>" style="display:inline;">
                <input type="hidden" name="from_view" value="1">
                <input type="checkbox" name="is_active" value="1" <?= (isset($product_data['is_active']) && $product_data['is_active'] == 1) ? 'checked' : '' ?>>
                <button type="submit" class="btn btn-primary" style="margin-left:10px;">Salva stato</button>
            </form>
        </td>
    </tr>
</table>

<div class="flex items-center justify-between mt-6 max-w-2xl mx-auto">
    <a href="index.php?page=products_catalog&action=edit&id=<?= htmlspecialchars($product_data['id']) ?>" class="btn btn-primary">Modifica</a>
    <a href="index.php?page=products_catalog" class="btn btn-secondary ml-4">Annulla</a>
</div>