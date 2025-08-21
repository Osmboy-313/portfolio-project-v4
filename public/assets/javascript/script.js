document.addEventListener("DOMContentLoaded", async function () {

    const params = new URLSearchParams(window.location.search);
    const c = params.get('c');
    const a = params.get('a');
    // console.log(c);


    const { openCloseModal } = await import('./modules/utils/open-close-modal.js');
    const modalControls = openCloseModal();



    if (c == 'profile' && a == 'myProfile') {
        import('./modules/features/profile.js').then(module => module.profile());

    }

    if (c == 'auth') {
        import('./modules/features/authentication.js').then(module => module.authentication());

    }

    if (c == 'category') {
        import('./modules/features/category.js').then(module => module.category(modalControls));
    }
    
    if (c === 'user') {
        import('./modules/utils/tab-manager.js').then(module => {
            const loadTabContent = module.handleTabs('user', '#user');
        });
    }
    
    if (c === 'code') {
        import('./modules/utils/tab-manager.js').then(module => {
            module.handleDynamicModalButton();
            const loadTabContent = module.handleTabs('code', '#admin');
    
            import('./modules/features/code.js').then(codeModule => {
                codeModule.code(modalControls, loadTabContent);
            });
        });
    }
    

    setTimeout(() => {
        import('./modules/utils/alerts.js').then(module => {
            // console.log('[initAlerts] Running after short delay');
            module.initAlerts({ autoClose: false });
        });
    }, 50); 


    Promise.all([
        import('./modules/utils/sidebar-class-toggle.js'),
        import('./modules/utils/sidebar-menu-items-active.js'),
        import('./modules/utils/toggle-dropdown-menu.js'),
        import('./modules/utils/custom-file-input.js'),
        import('./modules/utils/tag-chip-input.js'),
    ]).then(([sidebarToggle, menuItems, dropdown, fileInput, tagInput]) => {
        sidebarToggle.sidebar();
        menuItems.sideBarMenuItems();
        dropdown.dropdown();
        fileInput.customFileUpload();
        tagInput.tagInput();
    });
    

});


