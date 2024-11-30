class FormValidator {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.options = {
            validateOnInput: true,
            ...options
        };
        
        this.setupValidation();
    }

    setupValidation() {
        if (this.options.validateOnInput) {
            this.form.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => this.validateField(input));
            });
        }

        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.getAttribute('name');
        const formGroup = field.closest('.form-group');
        
        // Remove existing states
        formGroup.classList.remove('error', 'success');
        
        // Basic validation rules
        if (field.hasAttribute('required') && !value) {
            this.setError(field, 'This field is required');
            return false;
        }

        if (field.type === 'email' && !this.isValidEmail(value)) {
            this.setError(field, 'Please enter a valid email address');
            return false;
        }

        if (fieldName === 'password' && value.length < 6) {
            this.setError(field, 'Password must be at least 6 characters');
            return false;
        }

        this.setSuccess(field);
        return true;
    }

    setError(field, message) {
        const formGroup = field.closest('.form-group');
        formGroup.classList.add('error');
        formGroup.classList.add('error-shake');
        
        let errorMessage = formGroup.querySelector('.error-message');
        if (!errorMessage) {
            errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            formGroup.appendChild(errorMessage);
        }
        errorMessage.textContent = message;

        // Remove shake animation after it completes
        setTimeout(() => {
            formGroup.classList.remove('error-shake');
        }, 500);
    }

    setSuccess(field) {
        const formGroup = field.closest('.form-group');
        formGroup.classList.add('success');
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    handleSubmit(e) {
        e.preventDefault();
        
        let isValid = true;
        this.form.querySelectorAll('input').forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (isValid) {
            this.form.submit();
        }
    }
}

// Initialize validation
new FormValidator('loginForm'); 