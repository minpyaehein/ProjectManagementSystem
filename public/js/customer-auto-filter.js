document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('customerFilterForm');
    const searchInput = document.getElementById('customerSearch');
    const statusSelect = document.getElementById('customerStatus');
    const resultsContainer = document.getElementById('customerResults');
    const progressText = document.getElementById('customerFilterProgress');

    if (!filterForm || !searchInput || !statusSelect || !resultsContainer) {
        return;
    }

    let searchTimer = null;
    let activeRequest = null;
    let requestSequence = 0;

    function setLoading(isLoading) {
        resultsContainer.classList.toggle('is-filtering', isLoading);
        resultsContainer.setAttribute('aria-busy', String(isLoading));

        if (progressText) {
            progressText.hidden = !isLoading;
        }
    }

    function buildFilterUrl() {
        const formData = new FormData(filterForm);
        const parameters = new URLSearchParams();
        const searchValue = String(formData.get('search') ?? '').trim();
        const statusValue = String(formData.get('status') ?? '').trim();

        if (searchValue !== '') {
            parameters.set('search', searchValue);
        }

        if (statusValue !== '') {
            parameters.set('status', statusValue);
        }

        const queryString = parameters.toString();

        return queryString
            ? `${filterForm.action}?${queryString}`
            : filterForm.action;
    }

    async function loadCustomerResults(url, updateHistory = true) {
        if (activeRequest) {
            activeRequest.abort();
        }

        const currentRequest = ++requestSequence;
        activeRequest = new AbortController();
        setLoading(true);

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error(`Customer search failed: ${response.status}`);
            }

            const html = await response.text();
            const responseDocument = new DOMParser().parseFromString(
                html,
                'text/html'
            );
            const newResults = responseDocument.getElementById('customerResults');

            if (!newResults) {
                throw new Error('Customer results container was not found.');
            }

            resultsContainer.innerHTML = newResults.innerHTML;

            if (updateHistory) {
                window.history.pushState({}, '', url);
            }

            searchInput.focus({ preventScroll: true });
            const cursorPosition = searchInput.value.length;
            searchInput.setSelectionRange(cursorPosition, cursorPosition);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error(error);
            window.location.assign(url);
        } finally {
            if (currentRequest === requestSequence) {
                setLoading(false);
                activeRequest = null;
            }
        }
    }

    function scheduleAutomaticSearch() {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadCustomerResults(buildFilterUrl());
        }, 500);
    }

    searchInput.addEventListener('input', scheduleAutomaticSearch);

    searchInput.addEventListener('search', function () {
        window.clearTimeout(searchTimer);
        loadCustomerResults(buildFilterUrl());
    });

    statusSelect.addEventListener('change', function () {
        window.clearTimeout(searchTimer);
        loadCustomerResults(buildFilterUrl());
    });

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        window.clearTimeout(searchTimer);
        loadCustomerResults(buildFilterUrl());
    });

    resultsContainer.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('.pagination-wrapper a');

        if (!paginationLink) {
            return;
        }

        event.preventDefault();
        loadCustomerResults(paginationLink.href);
    });

    window.addEventListener('popstate', function () {
        const currentUrl = new URL(window.location.href);
        searchInput.value = currentUrl.searchParams.get('search') ?? '';
        statusSelect.value = currentUrl.searchParams.get('status') ?? '';
        loadCustomerResults(window.location.href, false);
    });
});
