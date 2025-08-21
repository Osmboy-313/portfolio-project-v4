
import { showAlert } from '../utils/alerts.js';
// import { closeModal } from './open-close-modal.js';


export function code(modalControls, loadTabContent) {
    // ======================= Config =======================
    const fields = {
        name: '#name',
    };

    // ======================= DOM Utility =======================

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // Updated to work with dynamic form IDs
    const getForm = (formId) => document.getElementById(formId) || document.querySelector(`[id="${formId}"]`);
    const getInputElement = (form, field) => form ? form.querySelector(field) : null;
    const getValue = (form, field) => {
        const element = getInputElement(form, field);
        return element ? element.value.trim() : '';
    };

    // ======================= Error Handlers =======================

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

    // ======================= Real Time validation =======================


    async function doesCodeExist(column, name, id = null) {
        const formData = id ? { id, name, column } : { name, column };
        const response = await fetch('index.php?c=code&a=doesExist', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();
        return result.exists;
    }


    // ======================= Load Modal with fields Prefilled 🔥 =======================

    async function loadCodeToEdit(codeId, columnName) {
        try {
            const response = await fetch('index.php?c=code&a=getById', {
                method: 'POST',
                body: JSON.stringify({ id: codeId, column: columnName }),
                headers: { 'Content-Type': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.code) {
                const code = result.code;
                
                // Find the edit form (it should exist by now since modal is open)
                const editForm = document.querySelector('#edit-admin-form, #edit-boss-form');
                
                if (editForm) {
                    // Set the edit ID on the form
                    editForm.dataset.editId = codeId;
                    
                    // Populate the form fields
                    const nameInput = editForm.querySelector('#name');
                    if (nameInput) {
                        nameInput.value = code;
                        nameInput.dataset.db = code;
                        nameInput.dataset.valid = 'true'; // Reset validation state
                    }
                    
                    console.log('Edit form populated with:', code);
                } else {
                    console.error('Edit form not found!');
                }
            } else {
                console.error('Failed to load code data:', result);
            }
        } catch (error) {
            console.error('Error loading code data:', error);
        }
    }

    // ======================= Refresh tabs to see updated records 🔥 =======================

    async function refreshDisplayedCodes() {
        try {

            const activeTab = document.querySelector('.tab.selected')?.dataset.tabTarget;
            const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
            console.log('INSIDE REFRESH DISPLAY CODES FUNCTION')
           
            if (activeTab && typeof loadTabContent === 'function') {
                await loadTabContent(activeTab, currentPage);
                console.log('INSIDE REFRESH DISPLAY CODES FUNCTION AND JS FOUND THE LOADTABCONTENT TO REFRESH')
            }
            
        } catch (error) {
            console.error('Error refreshing codes:', error);
        }
    }

    // ======================= Start Up =======================

    function startCodeModule() {
        setupFormHandlers();
    }



    // ======================= Event Delegation Form Handlers =======================

    function setupFormHandlers() {


        document.addEventListener('submit', async function (e) {
            const form = e.target;
            const formId = form.id;

            if (!formId || typeof formId !== 'string') {
                console.warn('[Submit] Ignoring form with no valid ID:', form);
                return;
            }

            console.log('Form Id in setupFormHandlers Function : ', formId);

            let columnName = '';


            if (formId.includes('admin')) {
                columnName = 'admin_code';
            } else if (formId.includes('boss')) {
                columnName = 'boss_code';
            }

            if (formId === 'add-admin-form' || formId === 'add-boss-form') {
                e.preventDefault();
                await handleAddCode(form, formId, columnName);
            }

            else if (formId === 'edit-admin-form' || formId === 'edit-boss-form') {
                e.preventDefault();
                await handleEditCode(form, formId, columnName);
            }

            else if (formId === 'delete-admin-form' || formId === 'delete-boss-form') {
                e.preventDefault();
                await handleDeleteCode(form, formId, columnName);
            }
        });

        document.addEventListener('click', async function (e) {
            const button = e.target;
            
            if (button.matches('.btn--edit.btn-edit-code')) {
                e.preventDefault();

                const codeId =  button.dataset.id;
                const columnName = button.dataset.column;
                
                console.log('Edit button clicked for code ID:', codeId, 'Column:', columnName);
                
                await loadCodeToEdit(codeId, columnName);
            }
            
            else if (button.matches('.btn--delete.btn--delete-code')) {
                e.preventDefault();
                const codeId = button.dataset.id;
                const columnName = button.dataset.column;
                
                console.log('Delete button clicked for code ID:', codeId, 'Column:', columnName);
                
                const deleteForm = document.querySelector('#delete-admin-form, #delete-boss-form');
                if (deleteForm) {
                    deleteForm.dataset.deleteId = codeId;
                }
            }

        });

        // Handle Real Time Validation

        document.addEventListener('input', function (e) {
            const input = e.target;
            const form = input.closest('form');
            const formId = form.id;

            let columnName = '';


            if (formId.includes('admin')) {
                columnName = 'admin_code';
            } else if (formId.includes('boss')) {
                columnName = 'boss_code';
            }

            if (form && (form.id === 'add-admin-form' || form.id === 'add-boss-form') && input.matches('#name')) {
                handleAddFormValidation(columnName, input);
            }

            if (form && (form.id === 'edit-admin-form' || form.id === 'edit-boss-form') && input.matches('#name')) {
                handleEditFormValidation(columnName,input);
            }
        });
    }

    // ======================= Form Handlers =======================


    let addValidationTimer;
    async function handleAddFormValidation(column, input) {
        clearTimeout(addValidationTimer);
        addValidationTimer = setTimeout(async () => {
            const form = input.closest('form');
            clearAllErrors(form);

            const name = input.value.trim();
            if (!name) return;

            const exists = await doesCodeExist(column, name);
            input.dataset.valid = !exists;
            if (exists) showErrors(form, fields.name, `This Code Already Exists !`);
        }, 1000);
    }

    let editValidationTimer;
    async function handleEditFormValidation(column, input) {
        clearTimeout(editValidationTimer);
        editValidationTimer = setTimeout(async () => {
            const form = input.closest('form');
            clearAllErrors(form);

            const name = input.value.trim();
            if (!name) return;

            const codeId = form.dataset.editId;
            if (!codeId) {
                console.warn('No edit ID found for validation');
                return;
            }
            
            const exists = await doesCodeExist(column, name, codeId);
            input.dataset.valid = !exists;
            if (exists) showErrors(form, fields.name, "This Code Already Exists !");
        }, 1000);
    }


    // ======================= AJAX/FETCH CRUD OPERATIONS =======================

    // ======================= Add code =======================


    async function handleAddCode(form, formId, columnName) {
        clearAllErrors(form);

        const errors = {};
        const name = getValue(form, fields.name);
        const nameElement = getInputElement(form, fields.name);
        
        if (!name) errors.name = "Enter code !";
        
        if (name && nameElement && nameElement.dataset.valid === 'false') {
            errors.name = "This Code Already Exists !";
        }

        console.log('Processing form:', formId, 'Errors:', errors);

        for (let key in errors) {
            showErrors(form, fields[key], errors[key]);
        }

        if (Object.keys(errors).length === 0) {

            // console.log('Form is ready to be submitted !', '+ column Name : ' , columnName); 
            
            const formData = {
                code : name,
                columnName : columnName,
            };

            const response = await fetch('index.php?c=code&a=add', {
                method : 'POST',
                body : JSON.stringify(formData),
                headers : {'Content-Type': 'application/json'},
            });

            const result = await response.json();
            
            // console.log("Result from php : ", result);

            if(result.error){
                for(let key in result.error){
                    showErrors(form, fields[key], result.error[key]);
                }

            }

            if(result.success){
                showAlert(form, 'alert--success', result.success);
                form.reset();
                refreshDisplayedCodes();
            }
            if(result.failure){
                showAlert(form, 'alert--failure', result.error);
            }

        }
    }

    // ======================= Edit code =======================

    async function handleEditCode(form, formId, columnName) {
        clearAllErrors(form);

        const errors = {};
        const name = getValue(form, fields.name);
        const nameElement = getInputElement(form, fields.name);
        const id = form.dataset.editId;

        if (!name) errors.name = "Enter code!";
        
        if (nameElement && nameElement.dataset.valid === 'false') {
            errors.name = "This Code Already Exists !";
        }
        
        if (nameElement && nameElement.dataset.db === name) {
            modalControls.closeModal(form.closest('.modal'));
            return;
        }

        for (let key in errors) {
            showErrors(form, fields[key], errors[key]);
        }

        console.log('Processing form :', formId, '  Errors:', errors, 'ID OF THE RECORD/CODE : ', id);

        if (Object.keys(errors).length === 0) {
            const codeId = form.dataset.editId;
            console.log('READY TO BE SENT TO PHP, THE BOSS');

            const formData = {
                id : id,
                code : name,
                column : columnName,
            };

            const response = await fetch('index.php?c=code&a=edit', {
                method : 'POST',
                body : JSON.stringify(formData),
                headers : {'Content-Type': 'application/json'},
            });

            const result = await response.json();

            console.log('RESPONSE FROM PHP :', result);

            if(result.error){
                for(let key in result.error){
                    showErrors(form, fields[key], result.error[key]);
                }
            }

            if(result.noChange){
                modalControls.closeModal(form.closest('.modal'));
                return;
            }

            if(result.success){
                showAlert(form, 'alert--success', result.success);
                refreshDisplayedCodes();
            }

            if(result.failure){
                showAlert(form, 'alert--failure', result.failure);
            }

        }
    }

    // ======================= Delete code =======================

    async function handleDeleteCode(form, formId, columnName) {

        const codeId = form.dataset.deleteId;

        const formData = {
            id : codeId,
            column : columnName,
        };

        const response = await fetch('index.php?c=code&a=delete', {
            method : 'POST',
            body :JSON.stringify(formData),
            headers : {'Content-Type' : 'application/json'},
        });
        const result = await response.json();

        console.log('Response from php : ' ,result);

        if(result.success){
            modalControls.closeModal(form.closest('.modal'));
            refreshDisplayedCodes();
        }
        
    }


    startCodeModule();
}