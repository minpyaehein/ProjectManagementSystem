@php
    $oldContacts = old('contacts');

    if (is_array($oldContacts) && count($oldContacts) > 0) {
        $contactRows = collect($oldContacts)->values();
        $primaryIndex = (int) old('primary_contact', 0);
    } elseif (isset($supplier) && $supplier->relationLoaded('contacts') && $supplier->contacts->isNotEmpty()) {
        $contactRows = $supplier->contacts->map(fn ($contact) => [
            'supplier_contact_id' => $contact->supplier_contact_id,
            'name' => $contact->name,
            'position' => $contact->position,
            'phone' => $contact->phone,
            'email' => $contact->email,
            'is_primary' => $contact->is_primary,
        ])->values();
        $primaryIndex = (int) $contactRows->search(fn ($contact) => (bool) $contact['is_primary']);
    } else {
        $contactRows = collect([[
            'supplier_contact_id' => null,
            'name' => isset($supplier) ? ($supplier->contact_person ?? '') : '',
            'position' => '',
            'phone' => isset($supplier) ? ($supplier->phone ?? '') : '',
            'email' => isset($supplier) ? ($supplier->email ?? '') : '',
            'is_primary' => true,
        ]]);
        $primaryIndex = 0;
    }
@endphp

<section class="supplier-contacts-section">
    <div class="supplier-contacts-heading">
        <div>
            <h2>Contact Persons</h2>
            <p>Add one or more contacts and select one primary contact.</p>
        </div>

        <button
            class="btn btn-soft supplier-add-contact"
            id="addSupplierContact"
            type="button"
        >
            + Add Contact Person
        </button>
    </div>

    <div id="supplierContactsList" class="supplier-contacts-list">
        @foreach ($contactRows as $index => $contact)
            <div class="supplier-contact-row" data-contact-row>
                <input
                    type="hidden"
                    name="contacts[{{ $index }}][supplier_contact_id]"
                    value="{{ $contact['supplier_contact_id'] ?? '' }}"
                    data-contact-field="supplier_contact_id"
                >

                <div class="field">
                    <label>Name</label>
                    <input
                        name="contacts[{{ $index }}][name]"
                        type="text"
                        value="{{ $contact['name'] ?? '' }}"
                        maxlength="255"
                        placeholder="Contact name"
                        data-contact-field="name"
                        required
                    >
                </div>

                <div class="field">
                    <label>Position</label>
                    <input
                        name="contacts[{{ $index }}][position]"
                        type="text"
                        value="{{ $contact['position'] ?? '' }}"
                        maxlength="255"
                        placeholder="Position / Department"
                        data-contact-field="position"
                    >
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input
                        name="contacts[{{ $index }}][phone]"
                        type="tel"
                        value="{{ $contact['phone'] ?? '' }}"
                        maxlength="30"
                        placeholder="Phone"
                        inputmode="tel"
                        data-contact-field="phone"
                    >
                </div>

                <div class="field">
                    <label>Email</label>
                    <input
                        name="contacts[{{ $index }}][email]"
                        type="email"
                        value="{{ $contact['email'] ?? '' }}"
                        maxlength="255"
                        placeholder="Email"
                        data-contact-field="email"
                    >
                </div>

                <div class="supplier-contact-primary">
                    <span>Primary</span>
                    <label>
                        <input
                            type="radio"
                            name="primary_contact"
                            value="{{ $index }}"
                            @checked($index === $primaryIndex)
                            data-primary-contact
                        >
                        Primary
                    </label>
                </div>

                <button
                    class="supplier-remove-contact"
                    type="button"
                    data-remove-contact
                >
                    Remove
                </button>
            </div>
        @endforeach
    </div>

    @error('contacts')
        <span class="field-error">{{ $message }}</span>
    @enderror
    @error('contacts.*.name')
        <span class="field-error">{{ $message }}</span>
    @enderror
    @error('contacts.*.phone')
        <span class="field-error">{{ $message }}</span>
    @enderror
    @error('contacts.*.email')
        <span class="field-error">{{ $message }}</span>
    @enderror
    @error('primary_contact')
        <span class="field-error">{{ $message }}</span>
    @enderror
</section>

<template id="supplierContactTemplate">
    <div class="supplier-contact-row" data-contact-row>
        <input type="hidden" data-contact-field="supplier_contact_id">

        <div class="field">
            <label>Name</label>
            <input type="text" maxlength="255" placeholder="Contact name" data-contact-field="name" required>
        </div>
        <div class="field">
            <label>Position</label>
            <input type="text" maxlength="255" placeholder="Position / Department" data-contact-field="position">
        </div>
        <div class="field">
            <label>Phone</label>
            <input type="tel" maxlength="30" placeholder="Phone" inputmode="tel" data-contact-field="phone">
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" maxlength="255" placeholder="Email" data-contact-field="email">
        </div>
        <div class="supplier-contact-primary">
            <span>Primary</span>
            <label>
                <input type="radio" name="primary_contact" data-primary-contact>
                Primary
            </label>
        </div>
        <button class="supplier-remove-contact" type="button" data-remove-contact>Remove</button>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('supplierContactsList');
    const template = document.getElementById('supplierContactTemplate');
    const addButton = document.getElementById('addSupplierContact');

    if (!list || !template || !addButton) return;

    function reindexContacts() {
        const rows = Array.from(list.querySelectorAll('[data-contact-row]'));

        rows.forEach(function (row, index) {
            row.querySelectorAll('[data-contact-field]').forEach(function (field) {
                field.name = `contacts[${index}][${field.dataset.contactField}]`;
            });

            const primary = row.querySelector('[data-primary-contact]');
            if (primary) primary.value = index;
        });

        if (!list.querySelector('[data-primary-contact]:checked')) {
            list.querySelector('[data-primary-contact]')?.click();
        }

        rows.forEach(function (row) {
            const removeButton = row.querySelector('[data-remove-contact]');
            if (removeButton) removeButton.disabled = rows.length === 1;
        });
    }

    addButton.addEventListener('click', function () {
        const fragment = template.content.cloneNode(true);
        list.appendChild(fragment);
        reindexContacts();
        list.lastElementChild?.querySelector('[data-contact-field="name"]')?.focus();
    });

    list.addEventListener('click', function (event) {
        const removeButton = event.target.closest('[data-remove-contact]');
        if (!removeButton) return;

        const rows = list.querySelectorAll('[data-contact-row]');
        if (rows.length <= 1) return;

        const row = removeButton.closest('[data-contact-row]');
        const wasPrimary = row.querySelector('[data-primary-contact]')?.checked;
        row.remove();
        reindexContacts();

        if (wasPrimary) {
            list.querySelector('[data-primary-contact]')?.click();
        }
    });

    reindexContacts();
});
</script>
@endpush
