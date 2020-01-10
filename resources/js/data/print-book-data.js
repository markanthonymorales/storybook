import { calculate_rule, shipping_address_rule, payment_rule } from '../modules/rules.js'

export const printBookData = {
	stripe: {},
    card: undefined,
	fullScreenLoading: false,
    addressIndex: '',
	steps: 0,
	notCalculated: true,
	disabledBindingAndLamination: true,
	options: {
		format: [],
		paper: [],
		cover: [],
		binding: [],
		lamination: [],
		color: [
			{
				label: 'Yes',
				value: true
			},
			{
				label: 'No',
				value: false
			},
		],
		delivery: [
			{
				label: 'Standard',
				value: 'standard'
			},
			{
				label: 'Express',
				value: 'express'
			}
		]
	},

	// data collected
	processData: {},

	// calculation process
  	calculateForm: {
        selected_format: '',
        selected_paper: '',
        selected_cover: '',
        selected_lamination: '',
        selected_binding: '',
    },
  	...calculate_rule,

  	// shipping address information
  	shippingAddressForm: {},
  	...shipping_address_rule,

  	// payment process
  	paymentForm: {},
  	...payment_rule,
}