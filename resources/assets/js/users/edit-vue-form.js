import Vue from 'vue';
import VueForm from 'vue-form';
import axios from 'axios';
import { elVal } from '../common/util';

let Edit;

if (document.querySelector('#users-edit')) {
    // Using VueForm for client-side validation
    Vue.use(VueForm, {
        inputClasses: {
            valid: 'is-valid',
            invalid: 'is-invalid'
        },
        validators: {
            // Custom validators referenced in input tags

            'role-validator': function(value, attrValue, vnode) {
                return value === '1' ? true : false;
            },

            'line-manager-validator': function(value, attrValue, vnode) {
                const lineManagerInt = parseInt(value)
                const roleIdInt = parseInt(elVal('#role_id'));

                if (roleIdInt === 1 && lineManagerInt === 0) {
                    console.log('line-manager-validator', 'roleIdInt === 1 && lineManagerInt === 0');
                    return false;
                } else if (roleIdInt > 1 && lineManagerInt > 0) {
                    console.log('line-manager-validator', 'roleIdInt > 1 && lineManagerInt > 0')
                    return false;
                } else {
                    console.log('line-manager-validator', 'OK');
                    return true;
                }
            },

            'active-validator': function(value, attrValue, vnode) {
                console.log('active', value);
                return new Promise((resolve, reject) => {
                    axios.get(`/users/${document.querySelector('#id').value}/has-subordinates`)
                        .then((response) => {                        
                            console.log('active-validator', response.data);

                            // resolve according to checkbox status + response.data together

                        resolve(response.data);
                        })
                        .catch(function(error) {
                            window.location.replace('/login');
                        });
                });
            },

            'my-custom-validator': function(value, attrValue, vnode) {
                return value === '1' ? true : false;
            }
        }
    });

    // axios.get(`/users/${document.querySelector('#id').value}`)
    //     .then((response) => {
    //         console.log('edit.js', response.data.user.first_name);
    //         Edit = makeEditVue();
    //         Edit.resetState();
    //     });

    Edit = makeEditVue();
    Edit.resetState();
}

function makeEditVue(user) {
    return new Vue({
        el: '#users-edit',
        data: {
            formstate: {},
            model: {
                first_name: '',
                last_name: '',
                email: '',
                role_id: '',
                line_manager_user_id: '',
                active: '',
                // first_name: user.first_name,
                // last_name: elVal('#last_name'),
                // email: elVal('#email'),
                // role_id: elVal('#role_id'),
                // line_manager_user_id: elVal('#line_manager_user_id'),
                // active: elVal('#active'),
            },    
        },
        methods: {
            fieldClassName(field) {
                if(!field) {
                return '';
                }
                if((field.$touched || field.$submitted) && field.$valid) {
                return '';
                }
                if((field.$touched || field.$submitted) && field.$invalid) {
                return 'has-danger';
                }
            },
            onSubmit() {
                if(this.formstate.$invalid) {
                // alert user and exit early
                return;
                }
                // otherwise submit form
                document.querySelector('#users-edit-form').submit();
            },
            resetState() {
                this.formstate._reset();
            }
        },
    });
    
}

export default Edit;