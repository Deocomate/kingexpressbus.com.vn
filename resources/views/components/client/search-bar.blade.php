<?php
// Ghi chú: Trong một dự án Laravel, phần Blade view và phần PHP class component sẽ nằm ở 2 file riêng biệt.
// View: resources/views/components/client/search-bar.blade.php
// Class: app/View/Components/Client/SearchBar.php
// Tôi sẽ trình bày cả hai phần trong cùng một file để tiện cho việc review của anh.

// =================================================================
// Phần 1: Nội dung file view (search-bar.blade.php)
// =================================================================
?>
@php
    $defaults = $searchData['defaults'] ?? [];
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons+Round">
        <style>
            .ksb-form {
                width: 100%;
                margin: 0;
                background: #ffffff;
                border-radius: 28px;
                padding: 1rem;
                box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
                border: 1px solid #e2e8f0;
            }

            .ksb-shell {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                align-items: stretch;
            }

            .ksb-fields {
                flex: 1 1 auto;
                display: grid;
                grid-template-columns: minmax(220px, 1.15fr) 72px minmax(220px, 1.15fr) minmax(170px, 0.9fr) minmax(170px, 0.9fr);
                align-items: stretch;
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                /* overflow: hidden; <- This was hiding the dropdown. Removed. */
            }

            .ksb-field {
                position: relative;
                display: flex;
                align-items: center;
                padding: 12px 18px;
                gap: 16px;
                border-right: 1px solid #E4E7EC;
                min-width: 0;
                background: transparent;
            }

            .ksb-field:last-child {
                border-right: none;
            }

            .ksb-field__body {
                display: flex;
                align-items: center;
                gap: 16px;
                min-width: 0;
                width: 100%;
            }

            .ksb-icon {
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .ksb-icon img {
                width: 24px;
                height: 24px;
            }

            .ksb-content {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .ksb-label {
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: #64748B;
            }

            .ksb-field.has-value .ksb-label,
            .ksb-field.is-focused .ksb-label {
                color: #1D4ED8;
            }

            .ksb-input,
            .ksb-value-input {
                border: none;
                background: transparent;
                padding: 0;
                font-size: 16px;
                font-weight: 600;
                color: #0F172A;
                outline: none;
                width: 100%;
            }

            .ksb-input::placeholder,
            .ksb-value-input::placeholder {
                color: #94A3B8;
                font-weight: 500;
            }

            .ksb-value-input {
                cursor: pointer;
            }

            .ksb-caption {
                font-size: 12px;
                color: #94A3B8;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ksb-field.has-value .ksb-caption {
                color: #475569;
            }

            .ksb-dropdown {
                position: absolute;
                left: 0;
                right: 0;
                top: calc(100% + 12px);
                background: #ffffff;
                border-radius: 18px;
                border: 1px solid #E2E8F0;
                box-shadow: 0 30px 50px -25px rgba(15, 23, 42, 0.45);
                padding: 14px 0;
                display: none;
                z-index: 30;
                max-height: 320px;
                overflow-y: auto;
            }

            .ksb-dropdown.is-open {
                display: block;
            }

            .ksb-option-list {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .ksb-option-header {
                padding: 12px 24px 6px;
                font-size: 12px;
                color: #64748B;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: 0.04em;
                background-color: #f8f9fa;
            }

            .ksb-option {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 24px;
                cursor: pointer;
                transition: background-color 0.2s ease;
            }

            .ksb-option:hover {
                background: #EAF1FF;
            }

            .ksb-option__info {
                flex: 1;
                min-width: 0;
            }

            .ksb-option__name {
                font-size: 15px;
                font-weight: 600;
                color: #0F172A;
                line-height: 1.4;
            }

            .ksb-option__context {
                font-size: 13px;
                color: #64748B;
                margin-top: 2px;
                line-height: 1.3;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ksb-dropdown-empty {
                padding: 16px 24px;
                font-size: 14px;
                color: #94A3B8;
            }

            .ksb-option mark {
                background-color: #FEF3C7;
                color: #B45309;
                padding: 0 2px;
                border-radius: 3px;
            }

            .ksb-field--swap {
                padding: 0;
                align-items: center;
                justify-content: center;
            }

            #ksb-swap-button {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: 1px solid #D7DCE5;
                background: #F3F6FB;
                box-shadow: 0 16px 32px -18px rgba(29, 78, 216, 0.45);
                color: #1D4ED8;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            }

            #ksb-swap-button:hover {
                background: #E8EEFF;
                color: #1E40AF;
                transform: scale(1.05);
                box-shadow: 0 18px 36px -18px rgba(29, 78, 216, 0.6);
            }

            .ksb-field--return {
                position: relative;
            }

            .ksb-return-empty {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #2563EB;
                font-weight: 600;
            }

            .ksb-return-add {
                border: none;
                background: transparent;
                color: inherit;
                font: inherit;
                cursor: pointer;
                padding: 0;
            }

            .ksb-return-selected {
                display: none;
                align-items: center;
                gap: 12px;
                width: 100%;
            }

            .ksb-field--return.has-date .ksb-return-selected {
                display: flex;
            }

            .ksb-field--return.has-date .ksb-return-empty {
                display: none;
            }

            .ksb-return-clear {
                border: none;
                background: transparent;
                color: #6B7280;
                cursor: pointer;
                border-radius: 50%;
                padding: 6px;
                transition: background-color 0.2s ease, color 0.2s ease;
            }

            .ksb-return-clear:hover {
                background: rgba(239, 68, 68, 0.12);
                color: #EF4444;
            }

            .ksb-submit {
                border: none;
                border-radius: 10px;
                background: linear-gradient(135deg, #FACC15, #F59E0B);
                color: #0F172A;
                font-weight: 700;
                font-size: 16px;
                padding: 10px 36px;
                min-width: 160px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 20px 40px -20px rgba(245, 158, 11, 0.65);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                flex-shrink: 0;
            }

            .ksb-submit:hover {
                transform: translateY(-1px);
                box-shadow: 0 24px 48px -20px rgba(245, 158, 11, 0.75);
            }

            .ksb-submit:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            @media (max-width: 1280px) {
                .ksb-fields {
                    grid-template-columns: minmax(200px, 1fr) 64px minmax(200px, 1fr) minmax(160px, 0.9fr) minmax(160px, 0.9fr);
                }
            }

            @media (max-width: 1023px) {
                .ksb-form {
                    padding: 0.75rem;
                    border-radius: 20px;
                }

                .ksb-shell {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .ksb-fields {
                    grid-template-columns: 1fr;
                    border-radius: 16px;
                    position: relative;
                    /* Added for swap button positioning */
                }

                .ksb-field {
                    border-right: none;
                    border-bottom: 1px solid #E4E7EC;
                    padding: 16px 20px;
                }

                .ksb-field:last-child {
                    border-bottom: none;
                }

                .ksb-field--swap {
                    display: none;
                    /* Hide the original placeholder */
                }

                #ksb-swap-button {
                    position: absolute;
                    top: 47px;
                    /* Position it between the first and second field */
                    right: 20px;
                    z-index: 10;
                    width: 42px;
                    height: 42px;
                    box-shadow: 0 8px 16px -12px rgba(29, 78, 216, 0.45);
                    transform: none;
                }

                #ksb-swap-button:hover {
                    transform: scale(1.05);
                    /* Keep hover effect */
                }


                .ksb-field--destination {
                    order: 2;
                    /* Adjust order */
                }

                .ksb-field--date {
                    order: 3;
                    /* Adjust order */
                }

                .ksb-field--return {
                    order: 4;
                    /* Adjust order */
                }

                .ksb-submit {
                    width: 100%;
                    min-height: 52px;
                    border-radius: 16px;
                }
            }

            @media (max-width: 640px) {
                .ksb-field {
                    padding: 14px 16px;
                }

                .ksb-dropdown {
                    top: calc(100% + 8px);
                }
            }
        </style>
    @endpush
@endonce
@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    @endpush
@endonce

<form id="client-search-form" action="{{ $action }}" method="GET" class="ksb-form" novalidate>
    <input type="hidden" name="origin_id" id="origin_id"
        value="{{ old('origin_id', data_get($defaults, 'origin.id')) }}">
    <input type="hidden" name="origin_type" id="origin_type"
        value="{{ old('origin_type', data_get($defaults, 'origin.type')) }}">
    <input type="hidden" name="origin_label" id="origin_label"
        value="{{ old('origin_label', data_get($defaults, 'origin.name')) }}">
    <input type="hidden" name="destination_id" id="destination_id"
        value="{{ old('destination_id', data_get($defaults, 'destination.id')) }}">
    <input type="hidden" name="destination_type" id="destination_type"
        value="{{ old('destination_type', data_get($defaults, 'destination.type')) }}">
    <input type="hidden" name="destination_label" id="destination_label"
        value="{{ old('destination_label', data_get($defaults, 'destination.name')) }}">
    <input type="hidden" name="return_date" id="ksb-return-value"
        value="{{ old('return_date', data_get($defaults, 'return_date')) }}">
    <input type="hidden" name="departure_date" id="ksb-departure-value"
        value="{{ old('departure_date', data_get($defaults, 'departure_date')) }}">

    <div class="ksb-shell">
        <div class="ksb-fields">
            <div class="ksb-field ksb-field--origin {{ data_get($defaults, 'origin.id') ? 'has-value' : '' }}"
                data-role="origin">
                <div class="ksb-field__body">
                    <div class="ksb-icon">
                        <img src="https://229a2c9fe669f7b.cmccloud.com.vn/svgIcon/pickup_vex_blue_24dp.svg"
                            alt="Điểm xuất phát" loading="lazy">
                    </div>
                    <div class="ksb-content">
                        <span class="ksb-label">Nơi xuất phát</span>
                        <input type="text" id="ksb-origin-input" class="ksb-input" placeholder="Chọn điểm đón"
                            autocomplete="off" spellcheck="false" inputmode="search"
                            value="{{ old('origin_label', data_get($defaults, 'origin.name')) }}">
                        <span class="ksb-caption"
                            data-caption="origin">{{ data_get($defaults, 'origin.context') ?: data_get($defaults, 'origin.type_label') }}</span>
                    </div>
                </div>
                <div class="ksb-dropdown" data-dropdown="origin"></div>
            </div>
            <div class="ksb-field ksb-field--swap">
                <button type="button" id="ksb-swap-button" aria-label="Đổi chiều">
                    <span class="material-icons-outlined">import_export</span>
                </button>
            </div>
            <div class="ksb-field ksb-field--destination {{ data_get($defaults, 'destination.id') ? 'has-value' : '' }}"
                data-role="destination">
                <div class="ksb-field__body">
                    <div class="ksb-icon">
                        <img src="https://229a2c9fe669f7b.cmccloud.com.vn/svgIcon/dropoff_new_24dp.svg" alt="Điểm đến"
                            loading="lazy">
                    </div>
                    <div class="ksb-content">
                        <span class="ksb-label">Nơi đến</span>
                        <input type="text" id="ksb-destination-input" class="ksb-input" placeholder="Chọn điểm đến"
                            autocomplete="off" spellcheck="false" inputmode="search"
                            value="{{ old('destination_label', data_get($defaults, 'destination.name')) }}">
                        <span class="ksb-caption"
                            data-caption="destination">{{ data_get($defaults, 'destination.context') ?: data_get($defaults, 'destination.type_label') }}</span>
                    </div>
                </div>
                <div class="ksb-dropdown" data-dropdown="destination"></div>
            </div>
            <div class="ksb-field ksb-field--date" data-role="departure">
                <div class="ksb-field__body">
                    <div class="ksb-icon">
                        <img src="https://storage.googleapis.com/fe-production/svgIcon/event_vex_blue_24dp.svg"
                            alt="Ngày đi" loading="lazy">
                    </div>
                    <div class="ksb-content">
                        <span class="ksb-label">Ngày đi</span>
                        <input type="text" id="ksb-departure-display" class="ksb-value-input"
                            placeholder="Chọn ngày"
                            value="{{ old('departure_date', data_get($defaults, 'departure_date')) }}" readonly>
                    </div>
                </div>
            </div>
            <div class="ksb-field ksb-field--return{{ old('return_date', data_get($defaults, 'return_date')) ? ' has-date' : '' }}"
                data-role="return">
                <div class="ksb-return-empty" id="ksb-return-empty">
                    <span class="material-icons-round">add</span>
                    <button type="button" id="ksb-add-return" class="ksb-return-add">Thêm ngày về</button>
                </div>
                <div class="ksb-return-selected" id="ksb-return-selected">
                    <div class="ksb-field__body">
                        <div class="ksb-icon">
                            <img src="https://storage.googleapis.com/fe-production/svgIcon/event_vex_blue_24dp.svg"
                                alt="Ngày về" loading="lazy">
                        </div>
                        <div class="ksb-content">
                            <span class="ksb-label">Ngày về</span>
                            <input type="text" id="ksb-return-display" class="ksb-value-input"
                                placeholder="Chọn ngày"
                                value="{{ old('return_date', data_get($defaults, 'return_date')) }}" readonly>
                        </div>
                    </div>
                    <button type="button" class="ksb-return-clear" id="ksb-clear-return" aria-label="Xóa ngày về">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
        </div>
        <button type="submit" class="ksb-submit">{{ $submitLabel }}</button>
    </div>
</form>

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('client-search-form');
            if (!form) {
                return;
            }

            const searchData = @json($searchData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            const typeLabels = {
                province: 'Tỉnh/Thành phố',
                district: 'Quận/Huyện',
                stop: 'Điểm đón/trả'
            };

            const typeOrder = {
                province: 0,
                district: 1,
                stop: 2
            };

            function normalizeText(value) {
                if (!value) {
                    return '';
                }

                return value.toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/Đ/g, 'D')
                    .toLowerCase()
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function escapeHtml(value) {
                return value
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function highlightText(text, query) {
                if (!text) {
                    return '';
                }
                if (!query) {
                    return escapeHtml(text);
                }
                const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                let highlighted = escapeHtml(text);

                try {
                    const regex = new RegExp(safeQuery, 'gi');
                    highlighted = highlighted.replace(regex, function(match) {
                        return '<mark>' + match + '</mark>';
                    });
                } catch (error) {
                    // ignore invalid regex
                }

                return highlighted;
            }

            function buildLocation(raw) {
                const id = Number(raw && raw.id);
                const type = raw && raw.type ? String(raw.type) : 'province';
                const name = raw && raw.name ? String(raw.name).trim() : '';

                if (!id || !name) {
                    return null;
                }

                const context = raw && raw.context ? String(raw.context).trim() : '';
                const address = raw && raw.address ? String(raw.address).trim() : '';
                const typeLabel = raw && raw.type_label ? String(raw.type_label) : (typeLabels[type] || '');

                const location = {
                    id: id,
                    type: type,
                    typeLabel: typeLabel,
                    name: name,
                    context: context,
                    address: address,
                    priority: Number(raw && raw.priority ? raw.priority : 0)
                };

                location.normalized = normalizeText(name);
                location.contextNormalized = normalizeText(context);
                location.addressNormalized = normalizeText(address);
                location.fullNormalized = normalizeText([name, context, address].filter(Boolean).join(' '));

                return location;
            }

            const locationItems = [];
            const typeBuckets = {
                province: [],
                district: [],
                stop: []
            };

            function registerLocation(location) {
                const key = location.type + ':' + location.id;

                for (let i = 0; i < locationItems.length; i += 1) {
                    const existing = locationItems[i];
                    if ((existing.type + ':' + existing.id) === key) {
                        return existing;
                    }
                }

                locationItems.push(location);

                if (typeBuckets[location.type]) {
                    typeBuckets[location.type].push(location);
                }

                return location;
            }

            (searchData.locations || []).forEach(function(raw) {
                const location = buildLocation(raw);
                if (location) {
                    registerLocation(location);
                }
            });

            function locationOrderValue(type) {
                return Object.prototype.hasOwnProperty.call(typeOrder, type) ? typeOrder[type] : 99;
            }

            function compareLocations(a, b) {
                const priorityDiff = (b.priority || 0) - (a.priority || 0);
                if (priorityDiff !== 0) {
                    return priorityDiff;
                }

                const typeDiff = locationOrderValue(a.type) - locationOrderValue(b.type);
                if (typeDiff !== 0) {
                    return typeDiff;
                }

                return a.name.localeCompare(b.name, 'vi', {
                    sensitivity: 'base'
                });
            }

            Object.keys(typeBuckets).forEach(function(type) {
                typeBuckets[type].sort(compareLocations);
            });

            function buildDefaultSuggestions() {
                const collected = [];

                ['province', 'district', 'stop'].forEach(function(type) {
                    const bucket = typeBuckets[type] || [];
                    for (let i = 0; i < bucket.length && collected.length < 18; i += 1) {
                        if (!collected.includes(bucket[i])) {
                            collected.push(bucket[i]);
                        }
                    }
                });

                if (collected.length < 18) {
                    locationItems.forEach(function(location) {
                        if (collected.length >= 18) {
                            return;
                        }
                        if (!collected.includes(location)) {
                            collected.push(location);
                        }
                    });
                }

                return collected;
            }

            let defaultSuggestions = buildDefaultSuggestions();

            function refreshBuckets() {
                Object.keys(typeBuckets).forEach(function(type) {
                    typeBuckets[type].sort(compareLocations);
                });
                defaultSuggestions = buildDefaultSuggestions();
            }

            function findLocation(id, type) {
                const key = String(type) + ':' + Number(id);

                for (let i = 0; i < locationItems.length; i += 1) {
                    const item = locationItems[i];
                    if (item.type + ':' + item.id === key) {
                        return item;
                    }
                }

                return null;
            }

            function ensureLocation(raw) {
                if (!raw || !raw.id || !raw.type) {
                    return null;
                }

                const existing = findLocation(raw.id, raw.type);
                if (existing) {
                    if (raw.name && raw.name !== existing.name) {
                        existing.name = String(raw.name);
                        existing.normalized = normalizeText(existing.name);
                    }
                    if (raw.context && raw.context !== existing.context) {
                        existing.context = String(raw.context);
                        existing.contextNormalized = normalizeText(existing.context);
                    }
                    if (raw.address && raw.address !== existing.address) {
                        existing.address = String(raw.address);
                        existing.addressNormalized = normalizeText(existing.address);
                    }
                    existing.fullNormalized = normalizeText([existing.name, existing.context, existing.address]
                        .filter(Boolean).join(' '));
                    return existing;
                }

                const location = buildLocation(raw);
                if (!location) {
                    return null;
                }

                registerLocation(location);
                refreshBuckets();

                return location;
            }

            const fields = {
                origin: {
                    input: document.getElementById('ksb-origin-input'),
                    idInput: document.getElementById('origin_id'),
                    typeInput: document.getElementById('origin_type'),
                    labelInput: document.getElementById('origin_label'),
                    wrapper: form.querySelector('[data-role="origin"]'),
                    caption: form.querySelector('[data-caption="origin"]'),
                    dropdown: form.querySelector('[data-dropdown="origin"]')
                },
                destination: {
                    input: document.getElementById('ksb-destination-input'),
                    idInput: document.getElementById('destination_id'),
                    typeInput: document.getElementById('destination_type'),
                    labelInput: document.getElementById('destination_label'),
                    wrapper: form.querySelector('[data-role="destination"]'),
                    caption: form.querySelector('[data-caption="destination"]'),
                    dropdown: form.querySelector('[data-dropdown="destination"]')
                }
            };

            const state = {
                origin: null,
                destination: null
            };

            const suggestionCache = {
                origin: [],
                destination: []
            };

            function closeDropdown(fieldName) {
                const refs = fields[fieldName];
                if (!refs || !refs.dropdown) {
                    return;
                }
                refs.dropdown.classList.remove('is-open');
            }

            function renderSuggestions(fieldName, query) {
                const refs = fields[fieldName];
                if (!refs || !refs.dropdown) {
                    return;
                }

                const trimmed = query ? query.trim() : '';
                let suggestions = [];

                if (!trimmed) {
                    suggestions = defaultSuggestions.slice(0, 18);
                } else {
                    const normalizedQuery = normalizeText(trimmed);
                    if (!normalizedQuery) {
                        suggestions = defaultSuggestions.slice(0, 18);
                    } else {
                        const matches = [];

                        locationItems.forEach(function(location) {
                            if (!location.fullNormalized || location.fullNormalized.indexOf(
                                    normalizedQuery) === -1) {
                                return;
                            }

                            const nameIndex = location.normalized.indexOf(normalizedQuery);
                            const contextIndex = location.contextNormalized ? location.contextNormalized
                                .indexOf(normalizedQuery) : -1;
                            const addressIndex = location.addressNormalized ? location.addressNormalized
                                .indexOf(normalizedQuery) : -1;

                            matches.push({
                                location: location,
                                sortKey: [
                                    nameIndex === 0 ? 0 : 1,
                                    nameIndex >= 0 ? 0 : 1,
                                    contextIndex === 0 ? 0 : 1,
                                    contextIndex >= 0 ? 0 : 1,
                                    addressIndex === 0 ? 0 : 1,
                                    addressIndex >= 0 ? 0 : 1,
                                    locationOrderValue(location.type),
                                    -(location.priority || 0),
                                    location.name.length,
                                    location.name
                                ]
                            });
                        });

                        matches.sort(function(a, b) {
                            const aKey = a.sortKey;
                            const bKey = b.sortKey;

                            for (let i = 0; i < aKey.length; i += 1) {
                                const diff = aKey[i] - bKey[i];
                                if (diff !== 0) {
                                    return diff;
                                }
                            }

                            return 0;
                        });

                        suggestions = matches.slice(0, 18).map(function(item) {
                            return item.location;
                        });
                    }
                }

                suggestionCache[fieldName] = suggestions;

                let html = '';

                if (!suggestions.length) {
                    html += '<div class="ksb-dropdown-empty">Không tìm thấy địa điểm phù hợp.</div>';
                } else {
                    html += '<ul class="ksb-option-list">';

                    const groupedSuggestions = {};
                    const typeOrderForGrouping = ['province', 'district', 'stop'];

                    suggestions.forEach(loc => {
                        const typeLabel = loc.typeLabel || typeLabels[loc.type] || 'Khác';
                        if (!groupedSuggestions[typeLabel]) {
                            groupedSuggestions[typeLabel] = [];
                        }
                        groupedSuggestions[typeLabel].push(loc);
                    });

                    const sortedGroupNames = Object.keys(groupedSuggestions).sort((a, b) => {
                        const typeA = Object.keys(typeLabels).find(key => typeLabels[key] === a);
                        const typeB = Object.keys(typeLabels).find(key => typeLabels[key] === b);
                        return typeOrderForGrouping.indexOf(typeA) - typeOrderForGrouping.indexOf(typeB);
                    });

                    sortedGroupNames.forEach(groupName => {
                        html += `<li class="ksb-option-header">${groupName}</li>`;
                        groupedSuggestions[groupName].forEach(function(location) {
                            const name = highlightText(location.name, trimmed);
                            const context = location.context ? highlightText(location.context,
                                trimmed) : '';
                            const address = !context && location.address ? highlightText(location
                                .address, trimmed) : '';

                            html += '<li class="ksb-option" data-option="true" data-id="' + location
                                .id + '" data-type="' + location.type + '">';
                            html += '<div class="ksb-option__info"><div class="ksb-option__name">' +
                                name + '</div>';
                            if (context) {
                                html += '<div class="ksb-option__context">' + context + '</div>';
                            } else if (address) {
                                html += '<div class="ksb-option__context">' + address + '</div>';
                            }
                            html += '</div>';
                            html += '</li>';
                        });
                    });
                    html += '</ul>';
                }

                refs.dropdown.innerHTML = html;
                refs.dropdown.classList.add('is-open');
                refs.dropdown.scrollTop = 0;

                const options = refs.dropdown.querySelectorAll('[data-option]');
                options.forEach(function(option) {
                    function handleOption(event) {
                        event.preventDefault();
                        const id = Number(option.getAttribute('data-id'));
                        const type = option.getAttribute('data-type');
                        const location = findLocation(id, type);
                        if (location) {
                            applySelection(fieldName, location);
                        }
                    }

                    option.addEventListener('mousedown', handleOption);
                    option.addEventListener('touchstart', handleOption, {
                        passive: false
                    });
                });
            }

            function applySelection(fieldName, location, options) {
                if (!location) {
                    return;
                }

                const refs = fields[fieldName];
                if (!refs) {
                    return;
                }

                const opts = options || {};

                state[fieldName] = location;

                if (refs.input) {
                    refs.input.value = location.name || '';
                    refs.input.setCustomValidity('');
                }

                if (refs.caption) {
                    refs.caption.textContent = location.context || location.typeLabel || '';
                }

                if (refs.idInput) {
                    refs.idInput.value = location.id;
                }

                if (refs.typeInput) {
                    refs.typeInput.value = location.type;
                }

                if (refs.labelInput) {
                    refs.labelInput.value = location.name || '';
                }

                if (refs.wrapper) {
                    refs.wrapper.classList.add('has-value');
                    refs.wrapper.classList.remove('is-focused');
                }

                if (!opts.silent) {
                    closeDropdown(fieldName);
                }
            }

            function resetField(fieldName) {
                const refs = fields[fieldName];
                if (!refs) {
                    return;
                }

                state[fieldName] = null;

                if (refs.input) {
                    refs.input.value = '';
                    refs.input.setCustomValidity('');
                }

                if (refs.caption) {
                    refs.caption.textContent = '';
                }

                if (refs.idInput) {
                    refs.idInput.value = '';
                }

                if (refs.typeInput) {
                    refs.typeInput.value = '';
                }

                if (refs.labelInput) {
                    refs.labelInput.value = '';
                }

                if (refs.wrapper) {
                    refs.wrapper.classList.remove('has-value');
                    refs.wrapper.classList.remove('is-focused');
                }

                closeDropdown(fieldName);
            }

            Object.keys(fields).forEach(function(fieldName) {
                const refs = fields[fieldName];
                if (!refs || !refs.input) {
                    return;
                }

                refs.input.addEventListener('focus', function() {
                    if (refs.wrapper) {
                        refs.wrapper.classList.add('is-focused');
                    }
                    renderSuggestions(fieldName, refs.input.value);
                    refs.input.select();
                });

                refs.input.addEventListener('input', function() {
                    state[fieldName] = null;
                    if (refs.idInput) {
                        refs.idInput.value = '';
                    }
                    if (refs.typeInput) {
                        refs.typeInput.value = '';
                    }
                    if (refs.labelInput) {
                        refs.labelInput.value = '';
                    }
                    if (refs.caption) {
                        refs.caption.textContent = '';
                    }
                    if (refs.wrapper) {
                        refs.wrapper.classList.remove('has-value');
                    }
                    refs.input.setCustomValidity('');
                    renderSuggestions(fieldName, refs.input.value);
                });

                refs.input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const suggestions = suggestionCache[fieldName] || [];
                        const firstSuggestion = suggestions[0];
                        if (firstSuggestion) {
                            applySelection(fieldName, firstSuggestion);
                        }
                    }
                });

                refs.input.addEventListener('blur', function() {
                    window.setTimeout(function() {
                        closeDropdown(fieldName);
                        if (refs.wrapper) {
                            refs.wrapper.classList.remove('is-focused');
                        }
                    }, 150);

                    if (!state[fieldName]) {
                        refs.input.setCustomValidity(
                            'Vui lòng chọn địa điểm trong danh sách gợi ý.');
                    } else {
                        refs.input.setCustomValidity('');
                    }
                });
            });

            document.addEventListener('click', function(event) {
                if (!form.contains(event.target)) {
                    closeDropdown('origin');
                    closeDropdown('destination');
                    Object.keys(fields).forEach(function(fieldName) {
                        const wrapper = fields[fieldName] && fields[fieldName].wrapper;
                        if (wrapper) {
                            wrapper.classList.remove('is-focused');
                        }
                    });
                }
            });

            const defaults = searchData.defaults || {};
            const defaultOrigin = ensureLocation(defaults.origin);
            if (defaultOrigin) {
                applySelection('origin', defaultOrigin, {
                    silent: true
                });
            }

            const defaultDestination = ensureLocation(defaults.destination);
            if (defaultDestination) {
                applySelection('destination', defaultDestination, {
                    silent: true
                });
            }

            if (!state.origin && defaultSuggestions.length) {
                applySelection('origin', defaultSuggestions[0], {
                    silent: true
                });
            }

            if (!state.destination && defaultSuggestions.length) {
                let fallback = null;
                for (let i = 0; i < defaultSuggestions.length; i += 1) {
                    const candidate = defaultSuggestions[i];
                    if (!state.origin || candidate.id !== state.origin.id || candidate.type !== state.origin.type) {
                        fallback = candidate;
                        break;
                    }
                }
                if (!fallback) {
                    fallback = defaultSuggestions[0];
                }
                if (fallback) {
                    applySelection('destination', fallback, {
                        silent: true
                    });
                }
            }

            const swapButton = document.getElementById('ksb-swap-button');
            if (swapButton) {
                swapButton.addEventListener('click', function() {
                    const currentOrigin = state.origin;
                    const currentDestination = state.destination;

                    if (!currentOrigin && !currentDestination) {
                        return;
                    }

                    if (currentDestination) {
                        applySelection('origin', currentDestination);
                    } else {
                        resetField('origin');
                    }

                    if (currentOrigin) {
                        applySelection('destination', currentOrigin);
                    } else {
                        resetField('destination');
                    }
                });
            }

            function pad(value) {
                return String(value).padStart(2, '0');
            }

            function formatValueDate(date) {
                return pad(date.getDate()) + '/' + pad(date.getMonth() + 1) + '/' + date.getFullYear();
            }

            function parseInputDate(value) {
                if (!value) {
                    return null;
                }

                const parts = value.split('/');
                if (parts.length !== 3) {
                    return null;
                }

                const day = Number(parts[0]);
                const month = Number(parts[1]) - 1;
                const year = Number(parts[2]);

                if (!day || month < 0 || month > 11 || !year) {
                    return null;
                }

                const parsed = new Date(year, month, day);
                if (Number.isNaN(parsed.getTime())) {
                    return null;
                }

                return parsed;
            }

            const departureDisplay = document.getElementById('ksb-departure-display');
            const departureValueInput = document.getElementById('ksb-departure-value');
            const returnDisplay = document.getElementById('ksb-return-display');
            const returnValueInput = document.getElementById('ksb-return-value');
            const returnFieldElement = form.querySelector('[data-role="return"]');
            const addReturnBtn = document.getElementById('ksb-add-return');
            const clearReturnBtn = document.getElementById('ksb-clear-return');

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const defaultDepartureValue = departureValueInput ? departureValueInput.value : '';
            if (departureDisplay && defaultDepartureValue) {
                departureDisplay.value = defaultDepartureValue;
            }

            let departurePicker = null;

            if (typeof Litepicker === 'function' && departureDisplay) {
                departurePicker = new Litepicker({
                    element: departureDisplay,
                    singleMode: true,
                    minDate: today,
                    lang: 'vi-VN',
                    format: 'DD/MM/YYYY',
                    autoApply: true,
                    tooltipText: {
                        one: 'ngày',
                        other: 'ngày'
                    },
                    buttonText: {
                        apply: 'Chọn',
                        cancel: 'Hủy'
                    },
                    onSelect: function(date) {
                        if (!date) {
                            return;
                        }
                        const jsDate = date.toJSDate();
                        const formatted = formatValueDate(jsDate);

                        if (departureDisplay) {
                            departureDisplay.value = formatted;
                        }
                        if (departureValueInput) {
                            departureValueInput.value = formatted;
                        }

                        if (returnPicker) {
                            returnPicker.setOptions({
                                minDate: jsDate
                            });
                            const selectedReturn = returnPicker.getDate();
                            if (selectedReturn && selectedReturn.toJSDate() < jsDate) {
                                clearReturnDate();
                            }
                        }
                    }
                });

                const parsedDeparture = parseInputDate(defaultDepartureValue);
                if (parsedDeparture) {
                    departurePicker.setDate(parsedDeparture);
                } else {
                    departurePicker.setDate(today);
                }
            } else if (departureDisplay && !departureDisplay.value) {
                const formattedToday = formatValueDate(today);
                departureDisplay.value = formattedToday;
                if (departureValueInput) {
                    departureValueInput.value = formattedToday;
                }
            }

            let returnPicker = null;

            function ensureReturnPicker() {
                if (returnPicker || typeof Litepicker !== 'function' || !returnDisplay) {
                    return returnPicker;
                }

                let minDate = today;
                if (departurePicker && departurePicker.getDate()) {
                    minDate = departurePicker.getDate().toJSDate();
                }

                returnPicker = new Litepicker({
                    element: returnDisplay,
                    singleMode: true,
                    minDate: minDate,
                    lang: 'vi-VN',
                    format: 'DD/MM/YYYY',
                    autoApply: true,
                    tooltipText: {
                        one: 'ngày',
                        other: 'ngày'
                    },
                    buttonText: {
                        apply: 'Chọn',
                        cancel: 'Hủy'
                    },
                    onSelect: function(date) {
                        if (!date) {
                            return;
                        }
                        const jsDate = date.toJSDate();
                        const formatted = formatValueDate(jsDate);

                        if (returnValueInput) {
                            returnValueInput.value = formatted;
                        }
                        if (returnDisplay) {
                            returnDisplay.value = formatted;
                        }
                        if (returnFieldElement) {
                            returnFieldElement.classList.add('has-date');
                        }
                    }
                });

                return returnPicker;
            }

            function clearReturnDate() {
                if (returnPicker) {
                    returnPicker.clearSelection();
                }
                if (returnValueInput) {
                    returnValueInput.value = '';
                }
                if (returnDisplay) {
                    returnDisplay.value = '';
                }
                if (returnFieldElement) {
                    returnFieldElement.classList.remove('has-date');
                }
            }

            if (addReturnBtn) {
                addReturnBtn.addEventListener('click', function() {
                    if (returnFieldElement) {
                        returnFieldElement.classList.add('has-date');
                    }

                    const picker = ensureReturnPicker();
                    if (picker) {
                        let minDate = today;
                        if (departurePicker && departurePicker.getDate()) {
                            minDate = departurePicker.getDate().toJSDate();
                        }
                        picker.setOptions({
                            minDate: minDate
                        });
                        picker.show();
                    }
                });
            }

            if (clearReturnBtn) {
                clearReturnBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    clearReturnDate();
                });
            }

            const defaultReturnValue = returnValueInput ? returnValueInput.value : '';
            if (defaultReturnValue) {
                const parsedReturn = parseInputDate(defaultReturnValue);
                if (parsedReturn) {
                    if (returnDisplay) {
                        returnDisplay.value = formatValueDate(parsedReturn);
                    }
                    if (returnFieldElement) {
                        returnFieldElement.classList.add('has-date');
                    }
                    const picker = ensureReturnPicker();
                    if (picker) {
                        picker.setDate(parsedReturn);
                    }
                }
            }

            form.addEventListener('submit', function(event) {
                let isValid = true;

                ['origin', 'destination'].forEach(function(fieldName) {
                    if (!state[fieldName]) {
                        const refs = fields[fieldName];
                        if (refs && refs.input) {
                            refs.input.setCustomValidity(
                                'Vui lòng chọn địa điểm trong danh sách gợi ý.');
                            refs.input.reportValidity();
                        }
                        isValid = false;
                    }
                });

                if (!departureValueInput || !departureValueInput.value) {
                    isValid = false;
                    if (departureDisplay) {
                        departureDisplay.focus();
                    }
                }

                if (!isValid) {
                    event.preventDefault();
                    return;
                }

                const submitButton = form.querySelector('.ksb-submit');
                if (submitButton) {
                    if (!submitButton.dataset.originalText) {
                        submitButton.dataset.originalText = submitButton.textContent;
                    }
                    submitButton.disabled = true;
                    submitButton.textContent = 'Đang tìm...';
                }
            });

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    const submitButton = form.querySelector('.ksb-submit');
                    if (submitButton && submitButton.dataset.originalText) {
                        submitButton.disabled = false;
                        submitButton.textContent = submitButton.dataset.originalText;
                    }
                }
            });
        });
    </script>
@endpush
