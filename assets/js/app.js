
document.addEventListener('DOMContentLoaded', () => {
    const presetButton = document.getElementById('load-epub-preset');
    const presetCard = document.getElementById('preset-preview');
    const presetData = window.APP_PRESETS || {};

    function updateConditionalRequirement(selectEl) {
        const factorKey = selectEl.dataset.factor;
        const value = parseInt(selectEl.value, 10);
        const justification = document.querySelector(`[name="${factorKey}_justification"]`);
        const helper = document.getElementById(`${factorKey}-justification-helper`);

        if (!justification) return;

        if (value <= 1) {
            justification.required = true;
            justification.classList.add('justification-required');
            if (helper) helper.textContent = 'Obligatori perquè has seleccionat un valor 0 o 1.';
        } else {
            justification.required = false;
            justification.classList.remove('justification-required');
            if (helper) helper.textContent = 'Només és obligatori si marques 0 o 1.';
        }
    }

    document.querySelectorAll('.factor-select').forEach(selectEl => {
        updateConditionalRequirement(selectEl);
        selectEl.addEventListener('change', () => updateConditionalRequirement(selectEl));
    });

    if (presetButton) {
        presetButton.addEventListener('click', () => {
            const preset = presetData.epub;
            if (!preset) return;

            const setValue = (name, value) => {
                const field = document.querySelector(`[name="${name}"]`);
                if (field) field.value = value ?? '';
            };

            setValue('format', preset.format);
            setValue('version', preset.version);

            Object.entries(preset.values || {}).forEach(([key, value]) => setValue(key, value));
            Object.entries(preset.notes || {}).forEach(([key, value]) => setValue(`${key}_note`, value));
            Object.entries(preset.justifications || {}).forEach(([key, value]) => setValue(`${key}_justification`, value));
            Object.entries(preset.evidence || {}).forEach(([key, value]) => setValue(`${key}_evidence`, value));

            document.querySelectorAll('.factor-select').forEach(selectEl => updateConditionalRequirement(selectEl));

            if (presetCard) {
                presetCard.classList.remove('d-none');
                presetCard.querySelector('.preset-title').textContent = preset.label || 'Fitxa precargada';
                presetCard.querySelector('.preset-summary').textContent = preset.summary || '';
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
