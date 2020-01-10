import { cart } from './cart-data.js';
export const data = {
	stripe: {},
    card: undefined,
    step: 1,
    activeIndex: '1',
    dialogVisible: false,
    fullScreenLoading: false,
    checkout: {
        fullname: null,
        email: null,
        phone: '',
        stripeToken: null,
        error: false
    },
    ...cart,
}