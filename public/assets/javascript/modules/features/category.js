
import { showAlert, closeAlert } from '../utils/alerts.js';

export function category(modalControls) {
    // ======================= Config =======================

    const forms = {
        addCategory: 'add-category-form',
        editCategory: 'edit-category-form',
        deleteCategory: 'delete-category-form',
    };

    const buttons = {
        editButton: '.btn--edit.btn--edit-category',
        deleteButton: '.btn--delete.btn--delete-category',
    };

    const fields = {
        name: '#name',
    };

    // ======================= Module State =======================
    let categories = [];
    let recordsPerPage = 12;
    let currentPage = 1;
    let totalPages = 1;
    let totalCategories = 0;

    // ======================= Helper Functions =======================

    // NEW: Format date as MM/DD/YYYY
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    // ======================= DOM Utility =======================

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    const getForm = (formId) => document.getElementById(formId);
    const getInputElement = (form, field) => form ? form.querySelector(field) : null;
    const getValue = (form, field) => {
        const element = getInputElement(form, field);
        return element ? element.value.trim() : '';
    };

    // ======================= Validators =======================

    function showErrors(form, field, msg) {
        if (!form) return;
        const box = getInputElement(form, field)?.closest('.modal__input-box');
        if (box) {
            box.classList.add('error');
            const errorBox = box.querySelector('span.error-box');
            if (errorBox) errorBox.textContent = msg;
        }
    }

    function clearAllErrors(form) {
        if (!form) return;
        form.querySelectorAll('.modal__input-box').forEach(box => {
            box.classList.remove('error');
            const errorBox = box.querySelector('.error-box');
            if (errorBox) errorBox.textContent = '';
        });
    }

    // ======================= Reusable API CALLS =======================

    async function checkCategoryExistence(name, id = null) {
        const formData = id ? { id, name } : { name };
        const response = await fetch('index.php?c=category&a=existenceCheck', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();
        return result.exists;
    }

    async function fetchCategories() {
        const url = `index.php?c=category&a=fetchAll&page=${currentPage}&limit=${recordsPerPage}&t=${Date.now()}`;
        const response = await fetch(url, { method: 'GET' });
        const result = await response.json();
        categories = result.categories || [];
        totalPages = result.totalPages || 1;
        totalCategories = result.totalCategories || 0;
    }

    async function loadCategoriesToEdit(id) {
        const response = await fetch('index.php?c=category&a=populate', {
            method: 'POST',
            body: JSON.stringify(id),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();

        const form = getForm(forms.editCategory);
        if (!form) {
            return;
        }

        const nameElement = getInputElement(form, fields.name);
        if (!nameElement) return;

        nameElement.value = result.category_name.trim();
        nameElement.dataset.db = result.category_name;
        form.dataset.editId = id;
    }

    // ======================= Start Up =======================
    function startCategoryModule() {
        setupFormHandlers();
        showCategories();
    }

    // ======================= Event Delegation Form Handlers =======================

    function setupFormHandlers() {
        document.body.addEventListener('submit', async function (e) {
            const form = e.target;
            const formId = form.id;

            // Handle Add Category Form
            if (formId === forms.addCategory) {
                e.preventDefault();
                await handleAddCategory(form);
            }

            // Handle Edit Category Form
            else if (formId === forms.editCategory) {
                e.preventDefault();
                await handleEditCategory(form);
            }

            // Handle Delete Category Form
            else if (formId === forms.deleteCategory) {
                e.preventDefault();
                await handleDeleteCategory(form);
            }
        });

        document.addEventListener('click', async function (e) {
            const button = e.target;

            if (button.matches(buttons.editButton)) {
                e.preventDefault();
                const categoryId = button.dataset.id;
                console.log('Setting edit ID:', categoryId);

                await loadCategoriesToEdit(categoryId);
            }

            // Handle Delete Button Clicks
            else if (button.matches(buttons.deleteButton)) {
                e.preventDefault();
                const categoryId = button.dataset.id;
                console.log('Setting delete ID:', categoryId);

                
                
                // Setting the delete ID on the delete form
                const deleteForm = getForm(forms.deleteCategory);
                console.log('Form?', deleteForm);
                    if (deleteForm) {
                        deleteForm.dataset.deleteCategoryId = categoryId;
                    }
                
            }
        });

        document.body.addEventListener('input', function (e) {
            const input = e.target;
            const form = input.closest('form');

            if (form && form.id === forms.addCategory && input.matches('#name')) {
                handleAddFormValidation(input);
            }

            if (form && form.id === forms.editCategory && input.matches('#name')) {
                handleEditFormValidation(input);
            }
        });
    }

    // ======================= Form Real Time Validation Handlers =======================

    let addValidationTimer;
    async function handleAddFormValidation(input) {
        clearTimeout(addValidationTimer);
        addValidationTimer = setTimeout(async () => {
            const form = input.closest('form');
            clearAllErrors(form);

            const name = input.value.trim();
            if (!name) return;

            const exists = await checkCategoryExistence(name);
            input.dataset.valid = !exists;
            if (exists) showErrors(form, fields.name, 'Category Already Exists');
        }, 1000);
    }

    let editValidationTimer;
    async function handleEditFormValidation(input) {
        clearTimeout(editValidationTimer);
        editValidationTimer = setTimeout(async () => {
            const form = input.closest('form');
            clearAllErrors(form);

            const name = input.value.trim();
            if (!name) return;

            const categoryId = form.dataset.editId;
            const exists = await checkCategoryExistence(name, categoryId);
            input.dataset.valid = !exists;
            if (exists) showErrors(form, fields.name, "Category Already Exists");
        }, 1000);
    }

    // ======================= Form Handlers =======================

    async function handleAddCategory(form) {
        clearAllErrors(form);

        const errors = {};
        const name = getValue(form, fields.name);
        const nameElement = getInputElement(form, fields.name);

        if (!name) errors.name = "Enter Category Name";

        if (nameElement && nameElement.dataset.valid === 'false') {
            errors.name = "Category Already Exists";
        }

        for (let key in errors) {
            showErrors(form, fields[key], errors[key]);
        }

        if (Object.keys(errors).length === 0) {
            const formData = { name };
            const response = await fetch('index.php?c=category&a=add', {
                method: 'POST',
                body: JSON.stringify(formData),
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await response.json();

            if (result.errors) {
                for (let key in result.errors) {
                    showErrors(form, fields[key], result.errors[key]);
                }
            }
            if (result.success) {
                form.reset();
                showAlert(form, 'alert--success', result.success);
                showCategories(currentPage);
            }
            if (result.failure) {
                showAlert(form, 'alert--failure', result.failure);
            }
        }
    }

    async function handleEditCategory(form) {
        clearAllErrors(form);

        const errors = {};
        const name = getValue(form, fields.name);
        if (!name) errors.name = "Enter Category";

        const nameElement = getInputElement(form, fields.name);

        if (nameElement && nameElement.dataset.valid === 'false') {
            errors.name = "Category Already Exists";
        }

        if (nameElement && nameElement.dataset.db === name) {
            modalControls.closeModal(form.closest('.modal'));
            return;
        }

        for (let key in errors) {
            showErrors(form, fields[key], errors[key]);
        }

        if (Object.keys(errors).length === 0) {
            const categoryId = form.dataset.editId;
            const formData = { id: categoryId, name };
            const response = await fetch('index.php?c=category&a=edit', {
                method: 'POST',
                body: JSON.stringify(formData),
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await response.json();

            if (result.errors) {
                for (let key in result.errors) {
                    showErrors(form, fields[key], result.errors[key]);
                }
            }
            if (result.success) {
                showAlert(form, 'alert--success', result.success);
                showCategories(currentPage);
            }
            if (result.failure) {
                showAlert(form, 'alert--failure', result.failure);
            }
        }
    }

    // async function handleDeleteCategory(form) {
    //     const categoryId = form.dataset.deleteCategoryId;
    //     const response = await fetch('index.php?c=category&a=delete', {
    //         method: 'POST',
    //         body: JSON.stringify({ id: categoryId }),
    //         headers: { 'Content-Type': 'application/json' }
    //     });
    //     const result = await response.json();
        
    //     // Debug logging to browser console
    //     console.log('DELETE RESPONSE:', result);
    //     if (result.debug) {
    //         console.log('DEBUG INFO:', result.debug);
    //     }

    //     if (result.success) {
    //         showCategories(currentPage);
    //         modalControls.closeModal(form.closest('.modal'));
    //     }
    //     if (result.failure) {
    //         console.log('DELETE FAILED:', result.failure);
    //         // Show the failure message to user
    //         alert('Delete failed: ' + result.failure);
    //     }
    // }


    async function handleDeleteCategory(form) {
        const categoryId = form.dataset.deleteCategoryId;
        const submitBtn = form.querySelector('input[type="submit"]');
        const originalText = submitBtn.value;
        
        // UI Feedback for time taking queries
        submitBtn.disabled = true;
        submitBtn.value = 'Archiving...';
        // closeAlert(form);
    
        try {
            const response = await fetch('index.php?c=category&a=delete', {
                method: 'POST',
                body: JSON.stringify({ id: categoryId }),
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await response.json();
    
            if (result.success) {
                showAlert(form, 'alert--success', result.message);
                
                setTimeout(() => {
                    modalControls.closeModal(form.closest('.modal'));
                    showCategories(1);
                }, 1500);
                
            } else {
                showAlert(form, 'alert--failure', 
                    result.error || "Archive failed. Please try again.");
            }
        } catch (error) {
            showAlert(form, 'alert--failure', "Network error during archive");
            console.error('Delete error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }
    

    // ======================= AJAX/FETCH CRUD OPERATIONS =======================

    async function showCategories(page = currentPage) {
        currentPage = page;
        const table = $('.main-content.categories table');
        const tableBody = $('tbody');
        const alert = table.closest('.main-content').querySelector('.alert');
        const pagination = $('.pagination__controls');

        await fetchCategories();

        function render() {
            renderTable();
            renderPagination();
        }

        function renderTable() {
            tableBody.innerHTML = '';
            const start = (currentPage - 1) * recordsPerPage;
            const end = start + recordsPerPage;
            const pageData = categories.slice(start, end);
            let serialNumber = start + 1;
    
            pageData.forEach(category => {
                const tr = document.createElement('tr');
                
                // Only show edit/delete buttons if user owns the category
                const isOwner = category.created_by_user_id == window.currentUserId;
                const formattedDate = formatDate(category.created_at);
                
                tr.innerHTML = `
                    <td>${serialNumber++}</td>
                    <td>${category.category_name}</td>
                    <td>${category.creator_name || 'System'}${isOwner ? ' <b>(You)</b>' : ''}</td>
                    <td>${category.post_count || 0}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <div class="table-actions">
                            ${isOwner ? `
                                <button 
                                    type="button"
                                    class="btn btn--edit btn--edit-category" 
                                    data-id="${category.id}" 
                                    data-modal-target="#edit-modal" 
                                    data-title="Edit Category" 
                                    data-label="Category" 
                                    data-placeholder="Enter Category" 
                                    data-form="edit-category-form">
                                    Edit
                                </button>
                                <button 
                                    type="button" 
                                    class="btn btn--delete btn--delete-category" 
                                    data-id="${category.id}" 
                                    data-modal-target="#del-modal" 
                                    data-title="Delete This Category?" 
                                    data-message="This Category will be permanently deleted!" 
                                    data-form="delete-category-form">
                                    Delete
                                </button>
                            ` : `
                                <span class="category-info">Owned by ${category.creator_name || 'System'}</span>
                            `}
                        </div>
                    </td>`;
                tableBody.appendChild(tr);
            });
    
            const rangeStart = categories.length === 0 ? start : start + 1;
            const rangeEnd = Math.min(end, categories.length);
            const summaryEl = $('.pagination__summary');
            if (summaryEl) {
                const pEl = summaryEl.querySelector('p');
                if (pEl) pEl.textContent = `Showing ${rangeStart} - ${rangeEnd} of ${totalCategories}`; // Use stored total
            }
    
            if (table) table.classList.toggle('de-active', categories.length === 0);
            const paginationEl = $('.pagination');
            if (paginationEl) paginationEl.classList.toggle('de-active', categories.length === 0);
            
            if (alert && categories.length === 0) {
                showAlert(alert, 'alert--info', 'No Categories Yet!', false);
            } else {
                closeAlert(alert);
            }
        }

        function renderPagination() {
            if (!pagination) return;
            pagination.innerHTML = '';
            const ul = document.createElement('ul');

            const createArrows = (isPrev) => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.innerHTML = `<i class='bx bx-chevron-${isPrev ? 'left' : 'right'}'></i>`;
                if ((isPrev && currentPage === 1) || (!isPrev && currentPage === totalPages)) {
                    a.style.pointerEvents = 'none';
                    a.style.opacity = 0.5;
                } else {
                    a.addEventListener('click', () => {
                        currentPage += isPrev ? -1 : 1;
                        render();
                    });
                }
                li.appendChild(a);
                return li;
            };

            ul.appendChild(createArrows(true));

            const pages = paginationDesign(currentPage, totalPages);
            pages.forEach(page => {
                const li = document.createElement('li');
                if (page === '...') {
                    const p = document.createElement('p');
                    p.textContent = page;
                    li.appendChild(p);
                } else {
                    const a = document.createElement('a');
                    a.textContent = page;
                    if (page === currentPage) li.classList.add('active');
                    a.addEventListener('click', () => {
                        currentPage = page;
                        render();
                    });
                    li.appendChild(a);
                }
                ul.appendChild(li);
            });

            ul.appendChild(createArrows(false));
            pagination.appendChild(ul);
        }

        function paginationDesign(currentPage, totalPages) {
            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                if (currentPage <= 3) {
                    pages.push(1, 2, 3, 4, 5, '...', totalPages);
                } else if (currentPage >= totalPages - 3) {
                    pages.push(1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
                } else {
                    pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
                }
            }
            return pages;
        }

        render();
    }

    startCategoryModule();
}