import { cart } from './cart-data.js';

export const data = {
	// for printing data
    printBookInitialStep: 0,
    printBook: {},
    calculateBook: false,
    // end here 

    mobile: false,
    isTyping: false,
    hideOnSinglePage: true,
    openCover: false,
    auth_id: null,
    openPrintBookCalculation: false,

    // filter data
    // filter for desktop view
    allYear: true,
    allMonth: true,

    // filter for mobile view
    selectedYear: 'All',
    selectedMonth: 'All',

    search_title: "",
    search_title_product: "",
    search_keyword: "",

    getSelectedYear: 2012,
    getSelectedMonth: 5,

    minYear: new Date().getFullYear() - 7,
    maxYear: new Date().getFullYear(),

    filterByYears: {},
    filterByMonths: {
        1: "January", 
        2: "February", 
        3: "March", 
        4: "April", 
        5: "May", 
        6: "June", 
        7: "July", 
        8: "August", 
        9: "September", 
        10: "October", 
        11: "November", 
        12: "December"
    },
    // filter data
    
    ...cart,
}