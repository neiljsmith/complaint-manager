/**
 * Useful, common functions 
 */

export function elVal(selector) {
    return document.querySelector(selector).value;
}  

export function isEmail(value) {
    return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value);
}

