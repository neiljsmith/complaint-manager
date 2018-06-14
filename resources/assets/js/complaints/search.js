import axios from 'axios';
import { isEmail } from '../common/util';

class CustomerSearch {

    constructor() {

        this.cssClasses = {
            hidden: 'd-none'
        };

        this.searchInput = document.querySelector('[data-customer-search]');
        this.complaintsTable = document.querySelector('[data-complaints-table]');
        this.searchingMessage = document.querySelector('[data-customer-searching]');
        this.customerFound = document.querySelector('[data-customer-found]');
        this.customerDetail = document.querySelector('[data-customer-detail]');
        this.customerSuggest = document.querySelector('[data-customer-search-suggest]');
        
        this.searchInput.addEventListener('keyup', this.search.bind(this));
    }

    /**
     * Method triggered by search input
     */
    search() {
        const minSearchLength = 0;
        if (this.searchInput.value.length > minSearchLength) {
            axios.get(`/complaints/find/${this.searchInput.value}`)
                .then(response => {
                    this.handleSearchResponse(response);
                });
        } else {
            // No search. Reset default display of complaints table
            this.hide(this.customerSuggest);
            this.hide(this.customerDetail);
            this.hide(this.customerFound);
            this.show(this.complaintsTable);
        }
    }

    /**
     * Parses the response object returned by HTTP search call
     */
    handleSearchResponse(response) {
        if (response.data) {
            if (response.data.length) {
                this.customerSuggest.innerHTML = this.searchSuggestHtml(response.data);
                this.addSearchSuggestEventHandlers(response.data)
                this.show(this.customerSuggest);
            } else {
                // No results returned. Reset and ide the suggestion dropdown
                this.customerSuggest.innerHTML = '';
                this.hide(this.customerSuggest);
            }
        }
    }

    /**
     * Attaches event handler to trigger results display to search suggestions
     */
    addSearchSuggestEventHandlers(data) {
        const elArray = Array.from(document.querySelectorAll('[data-search-link]'));
        for (let i = 0; i < elArray.length; i++) {
            elArray[i].addEventListener('click', () => {
                this.displaySearchResult(data[i]);
            });
        }
    }

    /**
     * Injects the customer details and complaints HTML 
     * into the DOM and displays it
     */
    displaySearchResult(data) {
        this.customerDetail.innerHTML = this.customerDetailHtml(data);
        this.show(this.customerDetail);

        this.customerFound.querySelector('tbody').innerHTML = this.complaintRowsHtml(data);
        this.show(this.customerFound);

        this.hide(this.complaintsTable);
    }

    /**
     * Creates HTML for the search suggestions dropdown
     */
    searchSuggestHtml(data) {
        return  data.map(customer => {
            return `
                <a href="#" class="dropdown-item" data-search-link="${customer.account_number}">${customer.account_number} | ${customer.email} | ${customer.first_name} ${customer.last_name}</a>
            `;
        }).join('');
    }

    /**
     * Creates HTML displaying customer details
     */
    customerDetailHtml(data) {
        return `
            <hr>
            <div class="row">
                <div class="col-md-10">
                    <h5 class="pb-2"><strong>Customer:</strong> ${data.account_number}<br>
                    ${data.first_name} ${data.last_name}<br>
                    ${data.email}</h5>
                </div>
                <div class="col-md-2">
                    <a href="/complaints/${data.id}/create" class="btn btn-primary float-right">Create New Complaint</a>
                </div> 
            </div>
        `;
    }

    /**
     * Creates HTML for the default complaints dislpay table rows
     */
    complaintRowsHtml({ complaints }) {
        return complaints.map(complaint => {
            return `
                <tr>
                    <td>${complaint.created_at_diff}</td>
                    <td class="text-center"><i class="fas fa-${ complaint.reward ? 'check' : 'times' }"></i></td>
                    <td>${complaint.description}</td>
                    <td><a href="complaints/${complaint.id}" class="btn btn-primary btn-sm">Show</a></td>
                </tr>
        `;
        }).join('');
    }

    hide(el) {
        el.classList.add(this.cssClasses.hidden);
    }

    show(el) {
        el.classList.remove(this.cssClasses.hidden);
    }

};

export default CustomerSearch;