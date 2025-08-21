export function handleTabs(controller, defaultTab = '#user') {
    const tabButtons = document.querySelectorAll('.tabs .tab');
    const activeIndicator = document.querySelector('.tabs .active');
    const contentWrapper = document.querySelector('.main-content.tab-content');

    const urlParams = new URLSearchParams(window.location.search);
    let savedTab = urlParams.get('tab') || localStorage.getItem('activeTab') || defaultTab;

    let activeIndex = 0;
    tabButtons.forEach((button, index) => {
        if (button.dataset.tabTarget === savedTab) {
            activeIndex = index;
        }
    });

    function activateTab(index, skipTransition = false) {
        const selectedTab = tabButtons[index].dataset.tabTarget;

        if (skipTransition) activeIndicator.classList.add('no-transition');
        activeIndicator.style.transform = `translateX(${index * 200}px)`;

        // Update selected class
        tabButtons.forEach(btn => btn.classList.remove('selected'));
        tabButtons[index].classList.add('selected');

        // Update localStorage and URL
        localStorage.setItem('activeTab', selectedTab);
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', selectedTab);
        currentUrl.searchParams.set('page', 1); // reset page on tab switch
        history.replaceState({}, '', currentUrl);

        loadTabContent(selectedTab, 1); // AJAX load

        if (skipTransition) {
            setTimeout(() => {
                activeIndicator.classList.remove('no-transition');
            }, 50);
        }
    }

    function loadTabContent(tab, page = 1) {
        const url = `?c=${controller}&a=index&tab=${encodeURIComponent(tab)}&page=${page}&ajax=1`;
        fetch(url)
            .then(res => res.text())
            .then(html => {
                contentWrapper.innerHTML = html;
                initPaginationHandlers(); // re-attach events inside loaded HTML

                import('./alerts.js').then(module => {
                    module.initAlerts({ autoClose: false });
                });

            });
    }

    function initPaginationHandlers() {
        document.querySelectorAll('.pagination__controls a').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = new URL(link.href);
                const tab = url.searchParams.get('tab') || defaultTab;
                const page = url.searchParams.get('page') || 1;
                loadTabContent(tab, page);

                // update URL without reload
                history.replaceState({}, '', url);
            });
        });
    }

    activateTab(activeIndex, true); // Initial load

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            activateTab(index);
        });
    });

    return loadTabContent;
}

export function handleDynamicModalButton() {
    const addBtn = document.querySelector('.btn--add-code');

    addBtn.addEventListener('click', () => {
        const activeTab = document.querySelector('.tab.selected')?.dataset.tabTarget;

        // Set dynamic attributes based on active tab
        if (activeTab === '#admin') {
            addBtn.dataset.title = "Add Admin Code";
            addBtn.dataset.label = "Admin Code";
            addBtn.dataset.placeholder = "Enter admin code";
            addBtn.dataset.form = "add-admin-form";
        } else if (activeTab === '#boss') {
            addBtn.dataset.title = "Add Boss Code";
            addBtn.dataset.label = "Boss Code";
            addBtn.dataset.placeholder = "Enter boss code";
            addBtn.dataset.form = "add-boss-form";
        }

    });
}