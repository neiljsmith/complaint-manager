import axios from 'axios';
import { size } from 'lodash';

/**
 * Used for form validation
 */
class Validator {

    constructor() {

        const self = this;

        this.form = document.querySelector('[data-validate]');

        this.roleIds = {
            agent: 1,
            lineManager: 2,
            superAdmin: 3,
        };

        /**
         * Bootstrap CSS classes that may be applied to inputs to show/hide
         * or display error status
         */
        this.cssClasses = {
            invalidInput: 'is-invalid',
            invalidMessage: 'text-danger',
            hidden: 'd-none'
        };

        /**
         * Validation rule methods. All take the input element object as a single param.
         * Note these functions are bound to the object when called so 'this' refers
         * to Validator object scope.
         */
        this.rules = {
            required({ value }) {
                return value.length > 0 ? true : false;
            },

            email({ value }) {
                const isEmail = (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value));

                return value.length === 0 || isEmail;
            },

            emailExists(inputEl) {
                // Only perform AJAX call if email is valid
                if (!this.rules.email(inputEl)) {
                    return true;
                }

                const promise = new Promise((resolve, reject) => {
                    // Create a default string to pass to include in the API URL to prevent errors
                    // in case of empty email value
                    const email = inputEl.value ? inputEl.value : '0';
                    axios.get(`/users/${this.form.querySelector('#id').value}/duplicate-email/${email}`)
                        .then(response => {
                            resolve(response.data === 0);
                        }).catch(error => {
                            this.catchAjaxError(error);
                        });
                });

                return promise;
            },

            roleId({ value }) {
                const lineManagerSelect = this.form.querySelector('#line_manager_user_id');

                if (parseInt(value) === this.roleIds.agent && lineManagerSelect.value === '0') {
                    lineManagerSelect.value = lineManagerSelect.options[1].value;
                } else if (parseInt(value) > this.roleIds.agent) {
                    lineManagerSelect.value = '0';
                }

                return true;
            },

            lineManagerId({ value }) {
                const roleIdSelect = this.form.querySelector('#role_id');

                if (parseInt(value) === 0 && parseInt(roleIdSelect.value) === this.roleIds.agent) {
                    roleIdSelect.value = this.roleIds.lineManager;
                } else if (parseInt(value) !== 0 && parseInt(roleIdSelect.value) !== this.roleIds.agent) {
                    roleIdSelect.value = this.roleIds.agent;
                }

                return true;
            },

            activeHasSubordinates({ checked }) {
                if (parseInt(this.form.querySelector('#role_id').value) === this.roleIds.agent) {
                    return true;
                } else if (checked === false && parseInt(this.form.querySelector('#has-subordinates').value) === 1) {
                    return false;
                }

                return true;
            },

            activeTooFewSuperAdmins({ checked }) {
                const minNumSuperAdmins = 2;
                if (
                    checked === false
                    && this.form.querySelector('#num-super-admins').value <= minNumSuperAdmins
                    && parseInt(this.form.querySelector('#role_id').value) === this.roleIds.superAdmin
                ) {
                    return false;
                }

                return true;
            },

            asyncExample({ value }) {
                const minNumSuperAdmins = 2;

                const promise = new Promise((resolve, reject) => {
                    axios.get('/users/num-super-admins')
                        .then(response => {
                            result = response.data <= minNumSuperAdmins ? false : true;
                            resolve(result);
                        }).catch(error => {
                            this.catchAjaxError(error);
                        });
                });

                return promise;
            }
        };

        this.addValidateAll();
    }

    catchAjaxError(error) {
        window.location.replace('/login');
    }

    /**
     * Attaches a validation event listener to a form field element. 
     * Uses the data-val-rule element attribute to determine the 
     * validation rules to apply.
     * 
     * @param {object} inputEl 
     */
    valEventListener(inputEl) {
        let errorEl = null;

        // Get the siblings of the input element whose class indicates they are error message containers
        const errorEls = inputEl.parentNode.querySelectorAll('.' + this.cssClasses.invalidMessage);

        // Get names of rule functions to be applied from the 'data-val-rule' attribute value,
        // separated by '|' if more than one.
        const rules = inputEl.dataset.valRule.split('|');

        for (var rule of rules) {
            if (errorEls.length === 1) {
                errorEl = errorEls[0];
            } else {
                // If more than one error display element (errorEls) they should have a 'data-val-msg'
                // attribute to display if the corresponding rule fails.
                for (var el of errorEls) {
                    if (el.dataset.valMsg === rule) {
                        errorEl = el;
                        break;
                    }
                }
            }

            this.runRule(rule, inputEl, errorEl);

            if (inputEl.classList.contains(this.cssClasses.invalidInput)) {
                break;
            }
        }
    }

    updateSubmitDisable(inputEl) {
        const form = inputEl.closest('form');
        const numInvalid = size(form.querySelectorAll('.' + this.cssClasses.invalidInput));
        const submitBtn = form.querySelector('button[type="submit"]');
        if (numInvalid === 0) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', '');
        }
    }

    runRule(rule, inputEl, errorEl) {
        const ruleReturn = this.rules[rule].bind(this)(inputEl);
        if (ruleReturn instanceof Promise) {
            ruleReturn.then((resolve) => {
                this.elUpdate(resolve, inputEl, errorEl);
                this.updateSubmitDisable(inputEl)
            });
        } else {
            this.elUpdate(ruleReturn, inputEl, errorEl);
            this.updateSubmitDisable(inputEl);
        }
    }

    elUpdate(ruleResult, inputEl, errorEl) {
        if (ruleResult) {
            // Pass
            inputEl.classList.remove(this.cssClasses.invalidInput);
            errorEl.classList.add(this.cssClasses.hidden);
        } else {
            // Fail
            inputEl.classList.add(this.cssClasses.invalidInput);
            errorEl.classList.remove(this.cssClasses.hidden);
        }
    }

    /**
     * Returns the appropriate event type to trigger validation
     * dependent on the input tag name and type.
     * 
     * @param {object} inputEl 
     */
    elEvent(inputEl) {
        const elDescriptor = inputEl.tagName.toLowerCase() + '_' + inputEl.type;
        switch (elDescriptor) {
            case 'select_select-one':
            case 'input_checkbox':
                return 'change';

            default:
                return 'keyup';
        }
    }

    /**
     * Attaches validation event listeners to all elements of the form with ID specified
     * that have a 'data-val-rule' attribue
     */
    addValidateAll() {
        const inputEls = this.form.querySelectorAll('[data-val-rule]');
        inputEls.forEach(inputEl => {
            inputEl.addEventListener(this.elEvent(inputEl), this.valEventListener.bind(this, inputEl));
        });
    }
}

export default Validator;