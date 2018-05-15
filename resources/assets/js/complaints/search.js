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
        
        this.searchInput.addEventListener('keyup', this.search.bind(this));
    }

    search() {
        if (this.searchInput.value === '') {
            this.complaintsTable.classList.remove(this.cssClasses.hidden);
            this.searchingMessage.classList.add(this.cssClasses.hidden);
            this.customerFound.classList.add(this.cssClasses.hidden);

        } else {
            this.complaintsTable.classList.add(this.cssClasses.hidden);
            this.searchingMessage.classList.remove(this.cssClasses.hidden);
        }

        if (isEmail(this.searchInput.value) || this.isAccNoFormat(this.searchInput.value)) {
            axios.get(`/complaints/find/${this.searchInput.value}`)
                .then(response => {
                    if (response.data) {
                        this.handleSearchResult(response.data);
                    }      
                });
        }
    }

    isAccNoFormat(value) {
        const accNoLength = 8;
        return !isNaN(value) && value.length === 8;
    }

    handleSearchResult(data) {
        this.customerDetail.innerHTML = this.customerDetailHtml(data);
        this.customerFound.querySelector('tbody').innerHTML = this.complaintRowsHtml(data);
        this.customerFound.classList.remove(this.cssClasses.hidden);
        this.searchingMessage.classList.add(this.cssClasses.hidden);
    }

    customerDetailHtml(data) {
        return `
            <hr>
            <div class="row">
                <div class="col-md-10">
                    <h5 class="pb-2"><strong>Customer:</strong> ${data.account_number}<br>
                    ${data.first_name} ${data.last_name} - ${data.email}</h5>
                </div>
                <div class="col-md-2">
                    <a href="/complaints/${data.id}/create" class="btn btn-primary float-right">Create New Complaint</a>
                </div> 
            </div>
        `;
    }

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

};

export default CustomerSearch;