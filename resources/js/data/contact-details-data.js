import { address } from './data.js'

export const data = {
	fullScreenLoading: false,
    showAddressDialog: false,

    showAddressTable: true,
    showContactTable: false,
    showBookTable: false,
    showStoryTable: false,
    stories: [],
    contacts: [],
    books: [],
    addresses: [],
    selectedAddress: {
        ...address,
    },
}