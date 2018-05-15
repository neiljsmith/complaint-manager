/**
 * Used by Vue/VeeValidate
 */

import VeeValidate, { Validator } from 'vee-validate';

const myRule = {
    getMessage(field, params, data) {
        return (data && data.message) || 'Something went wrong';
    },
    validate(value) {
        return new Promise(resolve => {
            resolve({
                valid: value === 'trigger' ? false : !!value,
                //data: value !== 'trigger' ? undefined : { message: 'Not this value' }
                data: null
            });
        });
    }
};
Validator.extend('myRuleName', myRule);

let globalVals = {
    test_field: 'foo'
};

const roleId = {
    getMessage(field, params, data) {
        return (data && data.message) || 'roleId default message';
    },
    validate(value) {
        return new Promise(resolve => {
            console.log('roleId', value, 'test_field', globalVals.test_field);
            resolve({
                valid: value === '1' ? !!value : false,
                data: value !== '1' ? { message: 'Custom message for !== 1' } : undefined 
            });
        });
    }
};
Validator.extend('roleId', roleId);

export default {
    el: '#users-edit-form',
    data: {
        test_field: globalVals.test_field,
    },
    methods: {
        validateOnSubmit() {
            this.$validator.validateAll()
                .then((result) => {
                    if (result) {
                        document.querySelector('#users-edit-form').submit();
                    } else {
                        alert('Please correct form errors!');
                    }
                });
        }
    },
    computed: {
        testField() {
            return 'testField ' + this.test_field;
        }
        // myTest() {
        //     if (this.errors) {
        //         console.log(this.errors);
        //     }
        //     console.log('myTest ' + this.testData);
        //     return 'myTest ' + this.testData;

        // }
    },
    created() {
        //this.errors.add('first_name', 'Newsletter Email is not valid', 'required');
    }
}


