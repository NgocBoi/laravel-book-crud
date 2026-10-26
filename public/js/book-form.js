(() => {
    const stockInput = document.getElementById('stockQuantity');
    const stockStatus = document.getElementById('stockStatus');

    if (stockInput && stockStatus) {
        const updateStockStatus = () => {
            const quantity = Number(stockInput.value);
            const valid = stockInput.value.trim() !== '' && Number.isInteger(quantity)
                && quantity >= 0 && quantity <= Number(stockInput.max);

            stockStatus.classList.toggle('d-none', !valid);
            if (!valid) return;

            const lowStock = Number(stockInput.dataset.lowStock);
            const state = quantity === 0 ? 'out' : quantity < lowStock ? 'low' : 'available';
            stockStatus.classList.remove('out', 'low', 'available');
            stockStatus.classList.add(state);
            stockStatus.textContent = quantity === 0 ? 'Hết hàng'
                : quantity < lowStock ? 'Sắp hết hàng' : 'Còn hàng';
        };

        stockInput.addEventListener('input', updateStockStatus);
        window.addEventListener('pageshow', updateStockStatus);
        updateStockStatus();
    }

    const choices = [...document.querySelectorAll('[data-book-choice]')];
    choices.forEach(choice => {
        const value = choice.querySelector('[data-choice-value]');
        const updateSelection = () => {
            const selected = [...choice.querySelectorAll('input:checked')]
                .map(input => input.closest('label').querySelector('span').textContent.trim());
            value.textContent = selected.join(', ') || value.dataset.placeholder;
        };

        choice.addEventListener('change', updateSelection);
        choice.addEventListener('toggle', () => {
            if (choice.open) {
                choices.filter(other => other !== choice).forEach(other => { other.open = false; });
            }
        });
        choice.addEventListener('keydown', event => {
            if (event.key === 'Escape' && choice.open) {
                choice.open = false;
                choice.querySelector('summary').focus();
            }
        });
        window.addEventListener('pageshow', updateSelection);
        updateSelection();
    });

    document.addEventListener('click', event => {
        choices.filter(choice => !choice.contains(event.target)).forEach(choice => { choice.open = false; });
    });

    const validationAlert = document.getElementById('bookValidationAlert');
    if (validationAlert) validationAlert.focus({ preventScroll: true });
})();
