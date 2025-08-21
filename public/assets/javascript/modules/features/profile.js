
import { showAlert, closeAlert } from '../utils/alerts.js';

export async function profile() {
    // ========== Constants ==========
    const forms = {
        details: 'update-details',
        role: 'update-role',
        password: 'update-password',
    };

    const fields = {
        username: '#username',
        email: '#email',
        userType: '#user-type-select',
        code: '#code',
        currentPassword: '#current-password',
        password: '#password',
        confirmPassword: '#confirm-password',
    };

    // ========== DOM Utils ==========
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    const getFormById = (id) => document.getElementById(id);
    const getInput = (form, field) => form?.querySelector(field) || null;
    const getValue = (form, field) => getInput(form, field)?.value.trim() || '';
    const getElement = (selector) => $(selector);
    const capitalizeFirst = (str) => str.charAt(0).toUpperCase() + str.slice(1);

    // ========== Error Handling ==========
    function showErrors(form, fieldSelector, message) {
        const field = getInput(form, fieldSelector);
        if (!field) return;

        const box = field.closest('.input-box');
        if (!box) return;

        box.classList.add('error');
        const errorSpan = box.querySelector('span');
        if (errorSpan) errorSpan.textContent = message;
    }

    function clearError(form, fieldSelector) {
        const field = getInput(form, fieldSelector);
        if (!field) return;

        const box = field.closest('.input-box');
        if (!box) return;

        box.classList.remove('error');
        const errorSpan = box.querySelector('span');
        if (errorSpan) errorSpan.textContent = '';
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.input-box').forEach(box => {
            box.classList.remove('error');
            const errorSpan = box.querySelector('span');
            if (errorSpan) errorSpan.textContent = '';
        });
    }

    // ========== API Calls ==========
    async function fetchUser() {
        const response = await fetch('index.php?c=profile&a=get', { method: 'POST' });
        return await response.json();
    }

    async function fetchCodes() {
        const response = await fetch('index.php?c=code&a=get', { method: 'POST' });
        return await response.json();
    }

    async function doesUserExists(column, value, id) {
        const formData = id ? { id, value, column } : { value, column };
        const response = await fetch('index.php?c=profile&a=doesUserExists', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: { 'Content-Type': 'application/json' },
        });
        const result = await response.json();
        return result.valid;
    }

    // ========== Real-time Validation ==========
    let debounce;
    async function handleProfileDetailsUpdateValidation(form, input, column) {
        clearTimeout(debounce);
        debounce = setTimeout(async () => {
            const value = input.value.trim();
            if (!value) return;

            const user = await fetchUser();
            const valid = await doesUserExists(column, value, user.id);

            input.dataset.valid = valid;
            if (!valid) {
                showErrors(form, fields[column], `This ${capitalizeFirst(column)} Already Exists!`);
            } else {
                clearError(form, fields[column]);
            }

        }, 800);
    }

    // ========== Event Delegation ==========
    function setupFormHandlers() {
        document.body.addEventListener('submit', async (e) => {
            const form = e.target;
            if (!form || !form.id) return;

            e.preventDefault();

            switch (form.id) {
                case forms.details:
                    handleProfileDetailsUpdate(form);
                    break;
                case forms.role:
                    await handleProfileRoleUpdate(form);
                    break;
                case forms.password:
                    handleProfilePasswordUpdate(form);
                    break;
            }
        });

        document.body.addEventListener('input', async (e) => {
            const input = e.target;
            const form = input.closest('form');
            if (!form || !form.id) return;

            if (form.id === forms.details && fields[input.id]) {
                await handleProfileDetailsUpdateValidation(form, input, input.id);
            }
        });
    }

    // ========== AJAX: Profile Display ==========
    async function showProfile() {
        const user = await fetchUser();

        const usernameInput = getElement(fields.username);
        const emailInput = getElement(fields.email);
        const userTypeSelect = getElement(fields.userType);

        if (usernameInput) {
            usernameInput.value = user.username;
            usernameInput.dataset.value = user.username;
        }

        if (emailInput) {
            emailInput.value = user.email;
            emailInput.dataset.value = user.email;
        }

        if (userTypeSelect) userTypeSelect.value = user.user_type;

        const codeInput = getElement(fields.code);
        const codeBox = codeInput?.parentElement;
        const codeLabel = codeBox?.querySelector('label');

        userTypeSelect?.addEventListener('change', () => {
            clearFormErrors(getFormById(forms.role));

            const selectedRole = getValue(getFormById(forms.role), fields.userType);
            const currentRole = user.user_type;
            const isSame = selectedRole === currentRole;
            const isUser = selectedRole === 'user';

            if (!isSame && !isUser) {
                codeBox?.classList.remove('hidden');
                if (codeLabel) codeLabel.textContent = `${capitalizeFirst(selectedRole)} Code`;
                if (codeInput) codeInput.placeholder = `Enter ${capitalizeFirst(selectedRole)} Code`;
            } else {
                codeBox?.classList.add('hidden');
            }
        });
    }

    // ========== AJAX: Update Role ==========
    async function handleProfileRoleUpdate(form) {
        clearFormErrors(form);

        const codes = await fetchCodes();
        const user = await fetchUser();
        const errors = {};

        const selectedRole = getValue(form, fields.userType);
        const enteredCode = getValue(form, fields.code);
        const isSameRole = selectedRole === user.user_type;
        const isUserRole = selectedRole === 'user';

        if (isSameRole) {
            errors.nothing = 'Nothing Changed';
            showAlert(form, 'alert--warning', 'Nothing Changed');
        }

        if (!isSameRole && !isUserRole) {
            if (!enteredCode) {
                errors.code = 'Enter Code!';
            } else {
                const match = codes.find(code =>
                    selectedRole === 'admin'
                        ? code.admin_code === enteredCode
                        : code.boss_code === enteredCode
                );
                if (!match) {
                    errors.code = `Incorrect ${capitalizeFirst(selectedRole)} code`;
                }
            }
        }

        for (let key in errors) showErrors(form, fields[key], errors[key]);

        if (Object.keys(errors).length === 0) {
            const response = await fetch('index.php?c=profile&a=update', {
                method: 'POST',
                body: JSON.stringify({ id: user.id, userType: selectedRole }),
                headers: { 'Content-Type': 'application/json' },
            });

            const result = await response.json();

            if (result.success) return location.reload();
            if (result.failure) showAlert(form, 'failure', result.failure);
            if (result.errors) {
                for (let key in result.errors) showErrors(form, fields[key], result.errors[key]);
            }
        }
    }

    // ========== AJAX: Update Details ==========

    async function handleProfileDetailsUpdate(form) {
        clearFormErrors(form);
        const errors = {};
        const user = await fetchUser();

        const username = getValue(form, fields.username);
        const email = getValue(form, fields.email);

        const usernameField = getElement(fields.username);
        const emailField = getElement(fields.email);

        if (!username) errors.username = 'Enter your username';
        if (!email) errors.email = 'Enter your email';

        if (username && usernameField.dataset.valid === 'false') {
            errors.username = 'This Username Already Exists';
        }

        if (email && emailField.dataset.valid === 'false') {
            errors.email = 'This Email Already Exists';
        }

        if (username === usernameField.dataset.value && email === emailField.dataset.value) {
            errors.nothing = 'Nothing Changed';
            showAlert(form, 'alert--warning', 'Nothing Changed');
        }

        for (let key in errors) showErrors(form, fields[key], errors[key]);

        if (Object.keys(errors).length === 0) {
            const response = await fetch('index.php?c=profile&a=update', {
                method: 'POST',
                body: JSON.stringify({ id: user.id, username, email }),
                headers: { 'Content-Type': 'application/json' },
            });

            const result = await response.json();

            if (result.success) return location.reload();
            if (result.failure) showAlert(form, 'failure', result.failure);
            if (result.errors) {
                for (let key in result.errors) showErrors(form, fields[key], result.errors[key]);
            }
        }
    }

    // ========== AJAX: Update Password ==========
    async function handleProfilePasswordUpdate(form) {
        clearFormErrors(form);
        const errors = {};
        const user = await fetchUser();

        const currentPassword = getValue(form, fields.currentPassword);
        const password = getValue(form, fields.password);
        const confirmPassword = getValue(form, fields.confirmPassword);

        if (!currentPassword) errors.currentPassword = 'Enter Current Password';
        if (!password) errors.password = 'Enter New Password';
        if (!confirmPassword) errors.confirmPassword = 'Confirm New Password';

        if (password && password.length < 8) {
            errors.password = 'Password must be at least 8 characters long';
        }

        if (password && confirmPassword && password !== confirmPassword) {
            errors.confirmPassword = 'Passwords Don’t Match';
        }

        for (let key in errors) showErrors(form, fields[key], errors[key]);

        if (Object.keys(errors).length === 0) {
            const response = await fetch('index.php?c=profile&a=update', {
                method: 'POST',
                body: JSON.stringify({ id: user.id, password, currentPassword }),
                headers: { 'Content-Type': 'application/json' },
            });

            const result = await response.json();

            if (result.success) {
                form.reset();
                showAlert('success', 'Successfully Updated the Password');
            }

            if (result.failure) showAlert(form, 'failure', result.failure);
            if (result.errors) {
                for (let key in result.errors) showErrors(form, fields[key], result.errors[key]);
            }
        }
    }

    // ========== Init ==========
    setupFormHandlers();
    showProfile();
}

