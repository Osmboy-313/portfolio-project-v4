export function showAlert(ref, classType, msg, autoClose = true, duration = 5000) {
    let box = null;

    // console.log('Show Alert Works!', 'Ref : ', ref, ', classType : ', classType, ', msg : ', msg, ', autoClose : ', autoClose, ', duration : ', duration);

    if (ref.classList?.contains('alert')) {
        box = ref;
    } else if (ref.querySelector?.('.alert')) {
        box = ref.querySelector('.alert');
    } else if (ref.closest) {
        box = ref.closest('.modal__body, .main-content')?.querySelector('.alert');
    }

    if (!box) return;

    box.classList.remove('fade-out', 'de-active', 'show');
    void box.offsetWidth;

    box.className = `alert ${classType}`;

    const msgEl = box.querySelector('.alert__msg');
    if (msgEl) msgEl.textContent = msg;

    requestAnimationFrame(() => {
        box.classList.add('show');
    });

    const closeBtn = box.querySelector('.alert__close');
    if (closeBtn && !closeBtn.hasListener) {
        closeBtn.addEventListener('click', () => hideAlert(box));
        closeBtn.hasListener = true;
    }

    if (autoClose) {
        setTimeout(() => hideAlert(box), duration);
    }
}

export function closeAlert(ref) {
    let box = null;

    if (ref.classList?.contains('alert')) {
        box = ref;
    } else if (ref.closest) {
        box = ref.closest('.modal__body, .main-content')?.querySelector('.alert');
    }

    if (!box) return;

    hideAlert(box);
}


export function hideAlert(el) {
    el.classList.add('fade-out');
    setTimeout(() => el.classList.add('de-active'), 300);
}


export function attachAlertListeners(alertEl, autoClose = true, duration = 5000) {
    const closeBtn = alertEl.querySelector('.alert__close');
    if (closeBtn && !closeBtn.hasListener) {
        closeBtn.addEventListener('click', () => hideAlert(alertEl));
        closeBtn.hasListener = true;
    }

    if (autoClose) {
        setTimeout(() => hideAlert(alertEl), duration);
    }

    // Add show class if not already
    requestAnimationFrame(() => {
        // console.log('[attachAlertListeners] Adding show class');
        alertEl.classList.add('show');
    });
}

// 🆕 Call this after DOM load to initialize PHP-rendered alerts


export function initAlerts({ autoClose = true, duration = 5000 } = {}) {
    const scope = document.querySelector('.content-card, .posts-page');
    // console.log('[initAlerts] Finding scope : ', scope);
    if (!scope) return;

    const alerts = scope.querySelectorAll('.alert');
    const validClasses = ['alert--success', 'alert--failure', 'alert--warning', 'alert--info'];

    // console.log('[initAlerts] Found alerts:', alerts.length, alerts);

    alerts.forEach(alert => {
        if (alert.dataset.initialized) return;
        if (alert.classList.contains('de-active')) return;

        const hasValidClass = validClasses.some(type => alert.classList.contains(type));
        if (!hasValidClass) return;

        attachAlertListeners(alert, autoClose, duration);
        alert.dataset.initialized = 'true';
    });
}

