import './bootstrap';
import Validator from './common/validator';
import CustomerSearch from './complaints/search';

if (document.querySelector('[data-validate]')) {
    new Validator()
}

if (document.querySelector('[data-customer-search]')) {
    new CustomerSearch();
}