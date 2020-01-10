const user_rules =  {
	userRule: {
		firstname: [
	        { required: true, message: 'Please input Firstname', trigger: 'blur' },
	        { min: 3, message: 'Length should be 3  and above', trigger: 'blur' }
	    ],
	    lastname: [
	        { required: true, message: 'Please input Lastname', trigger: 'blur' },
	        { min: 3, message: 'Length should be 3 and above', trigger: 'blur' }
	    ],
	    nickname: [
	        { required: true, message: 'Please input Nickname', trigger: 'blur' },
	        { min: 3, message: 'Length should be 3 and above', trigger: 'blur' }
	    ],
	    gender: [
	        { 
	            required: true, 
	            message: 'Please select Activity gender', 
	            trigger: 'change' 
	        }
	    ],
	}
};

const cart_rule = {
	checkoutRule: {
        fullname: [
            { 
                required: true, 
                message: 'Please input full name', 
                trigger: 'blur' 
            }
        ],
        email: [
            { 
                required: true, 
                message: 'Please input email', 
                trigger: 'blur' 
            }
        ],
        phone: [
            { 
                required: true, 
                message: 'Please input phone number', 
                trigger: 'blur' 
            }
        ],
        stripeToken: [
            { 
                required: true, 
                message: 'Please input card info.', 
                trigger: 'blur' 
            }
        ]
    }
}

const address_rule = {
	addressRule: {
        name: [
            { required: true, message: 'Please input Alias', trigger: 'blur' },
        ],
        ...common_address_rule,
    },
}

const common_address_rule = {
    address: [
        { required: true, message: 'Please input Address', trigger: 'blur' },
    ],
    street: [
        { required: true, message: 'Please input Street', trigger: 'blur' },
    ],
    city: [
        { required: true, message: 'Please input City', trigger: 'blur' },
    ],
    zipcode: [
        { required: true, message: 'Please input Zip Code', trigger: 'blur' },
    ],
    state: [
        { required: true, message: 'Please input State', trigger: 'blur' },
    ],
    country: [
        { required: true, message: 'Please input Country', trigger: 'blur' },
    ],
}

const calculate_rule = {
    calculateFormRule: {
        selected_format: [
            { 
                required: true, 
                message: 'Please select format type', 
                trigger: 'change' 
            }
        ],
        selected_paper: [
            { 
                required: true, 
                message: 'Please select paper type', 
                trigger: 'change' 
            }
        ],
        selected_cover: [
            { 
                required: true, 
                message: 'Please select cover type', 
                trigger: 'change' 
            }
        ],
        total_book: [
            { 
                required: true, 
                message: 'Please input form total book', 
                trigger: 'change' 
            },
            { min: 0, message: 'Length should be greater than 0', trigger: 'blur' }
        ]
    }
}

const shipping_address_rule = {
    shippingAddressFormRule: {
        ...common_address_rule,
    }
}

const payment_rule = {
    paymentFormRule: {
        fullname: [
            { 
                required: true, 
                message: 'Please input form fullname', 
                trigger: 'blur' 
            }
        ],
        email: [
            { 
                required: true, 
                message: 'Please input form email', 
                trigger: 'blur' 
            }
        ],
        phone: [
            { 
                required: true, 
                message: 'Please input form phone', 
                trigger: 'blur' 
            }
        ],
        stripeToken: [
            { 
                required: true, 
                message: 'Please input card info.', 
                trigger: 'blur'
            }
        ]
    },
}

export { user_rules, cart_rule, address_rule, calculate_rule, shipping_address_rule, payment_rule }