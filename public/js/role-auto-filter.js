document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('roleFilterForm');
    const searchInput = document.getElementById('roleSearch');
    const resultsContainer = document.getElementById('roleResults');
    const progressText = document.getElementById('roleFilterProgress');

    if (!filterForm || !searchInput || !resultsContainer) {
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
        const searchValue = searchInput.value.trim();
        const parameters = new URLSearchParams();

        if (searchValue !== '') {
            parameters.set('search', searchValue);
        }

        const queryString = parameters.toString();

        return queryString
            ? `${filterForm.action}?${queryString}`
            : filterForm.action;
    }

    async function loadRoleResults(url, updateHistory = true) {
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
                throw new Error(`Role search failed: ${response.status}`);
            }

            const html = await response.text();
            const responseDocument = new DOMParser().parseFromString(
                html,
                'text/html'
            );
            const newResults = responseDocument.getElementById('roleResults');

            if (!newResults) {
                throw new Error('Role results container was not found.');
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
            loadRoleResults(buildFilterUrl());
        }, 500);
    }

    searchInput.addEventListener('input', scheduleAutomaticSearch);

    searchInput.addEventListener('search', function () {
        window.clearTimeout(searchTimer);
        loadRoleResults(buildFilterUrl());
    });

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        window.clearTimeout(searchTimer);
        loadRoleResults(buildFilterUrl());
    });

    resultsContainer.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('.pagination-wrapper a');

        if (!paginationLink) {
            return;
        }

        event.preventDefault();
        loadRoleResults(paginationLink.href);
    });

    window.addEventListener('popstate', function () {
        const currentUrl = new URL(window.location.href);
        searchInput.value = currentUrl.searchParams.get('search') ?? '';
        loadRoleResults(window.location.href, false);
    });
});
