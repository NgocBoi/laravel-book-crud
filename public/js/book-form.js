(() => {
    const normalizeText = (str) => str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/Đ/g, 'D')
        .toLowerCase()
        .trim();

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
    let createdCounter = 0;

    choices.forEach(choice => {
        const value = choice.querySelector('[data-choice-value]');
        const searchInput = choice.querySelector('[data-choice-search]');
        const addButton = choice.querySelector('[data-choice-add]');
        const addButtonText = choice.querySelector('[data-choice-add-text]');
        const listContainer = choice.querySelector('[data-choice-list]');
        const noMatchHint = choice.querySelector('[data-choice-no-match]');
        const createFieldName = choice.dataset.allowCreate;

        const getOptionLabels = () => [...choice.querySelectorAll('.book-choice-option')];

        const updateSelection = () => {
            const selected = [...choice.querySelectorAll('input[type="checkbox"]:checked')]
                .map(input => input.closest('label').querySelector('span').textContent.trim());
            value.textContent = selected.join(', ') || value.dataset.placeholder;
        };

        const filterOptions = () => {
            if (!searchInput) return;

            const rawKeyword = searchInput.value.trim();
            const keyword = normalizeText(rawKeyword);
            const labels = getOptionLabels();
            let visibleCount = 0;
            let exactMatch = false;

            labels.forEach(label => {
                const optionName = label.querySelector('span')?.textContent.trim() || '';
                const normalizedOption = normalizeText(optionName);
                const matches = keyword === '' || normalizedOption.includes(keyword);

                label.classList.toggle('d-none', !matches);
                if (matches) visibleCount++;
                if (keyword !== '' && optionName.toLowerCase() === rawKeyword.toLowerCase()) {
                    exactMatch = true;
                }
            });

            if (noMatchHint) {
                noMatchHint.classList.toggle('d-none', visibleCount > 0 || keyword === '');
            }

            if (addButton && addButtonText && createFieldName) {
                const canCreate = rawKeyword.length > 0 && !exactMatch;
                addButton.classList.toggle('d-none', !canCreate);
                if (canCreate) {
                    addButtonText.textContent = `Thêm tác giả mới: "${rawKeyword}"`;
                }
            }
        };

        const createNewOption = () => {
            if (!searchInput || !createFieldName || !listContainer) return;

            const rawName = searchInput.value.trim();
            if (!rawName) return;

            const existing = getOptionLabels().find(
                label => (label.querySelector('span')?.textContent.trim().toLowerCase() || '') === rawName.toLowerCase()
            );

            if (existing) {
                const checkbox = existing.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = true;
            } else {
                createdCounter++;
                const id = `created-author-${Date.now()}-${createdCounter}`;
                const label = document.createElement('label');
                label.className = 'book-choice-option';
                label.htmlFor = id;

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = createFieldName;
                input.id = id;
                input.value = rawName;
                input.className = 'form-check-input';
                input.checked = true;

                const nameSpan = document.createElement('span');
                nameSpan.textContent = rawName;

                const badge = document.createElement('span');
                badge.className = 'badge bg-primary-subtle text-primary ms-auto';
                badge.textContent = 'Mới';

                label.append(input, nameSpan, badge);
                listContainer.prepend(label);
            }

            searchInput.value = '';
            filterOptions();
            updateSelection();
            searchInput.focus();
        };

        if (searchInput) {
            searchInput.addEventListener('input', filterOptions);
            searchInput.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if (addButton && !addButton.classList.contains('d-none')) {
                        createNewOption();
                    }
                }
            });
        }

        if (addButton) {
            addButton.addEventListener('click', createNewOption);
        }

        choice.addEventListener('change', event => {
            if (event.target.matches('input[type="checkbox"]')) {
                updateSelection();
            }
        });

        choice.addEventListener('toggle', () => {
            if (choice.open) {
                choices.filter(other => other !== choice).forEach(other => { other.open = false; });
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 10);
                }
            } else if (searchInput && searchInput.value !== '') {
                searchInput.value = '';
                filterOptions();
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
