import { cart } from './cart-data.js';

const common_data = {
	fullScreenLoading: false,
	isCollapse: true,
	mobile: false,
    dialogVisible: false,
    isTyping: false,
    activeIndex: '1',
    authdata: null,
    
};

export const app_data = {
	fullScreenLoading: false,
    ...cart,
    initial_log_messages: [
		{
			style: 'color: #e3342f; font-size: 25px;',
			msg: '%cWarning!'
		},
		{
			style: 'color: #e2e2e2; font-size: 14px;',
			msg: '%cThis is a browser feature intended for developers.'
		}
	],
};

export const initial_data = {
    event: "toLogin",
    search_title: '',
    filterBook: 'all',

	...common_data,
    ...cart,
};

export const nav = {
	route_name: '',
	recent: '',
	imageUrl: '',
	
	...common_data,
    ...cart,
};

export const story = {
	id: null,
    user_id: null,
    is_file_type: false,
    file_url: '',
    title: '',
    search_tags: [],
    shared_to: [],
    pages: [],
    colored_index: '',
    total_page: 0,
    from_date: '',
    to_date: '',
};

export const book = {
	id: null,
	title:'',
	content: '',
	description: '',
	author: '',

	price: 0.00,
	original_price: 0.00,
	markup_price: 0,
	ebook_price: 0.00,
	ebook_markup_price: 0,

	chapters: [],
	tags: [],
	shared_to: [],

	has_front_cover: false,
	has_spine_cover: false,
	has_back_cover: false,

	front_cover: '',
	spine_cover: '',
	back_cover: '',

	from_date: null,
	to_date: null,
	book_date: null,

	status_type: 'private',
	is_save_as_draft: true,

    total_page: 0,
};

export const address = {
	id: null,
    user_id: null,
    name: '',
    address: '',
    city: '',
    zipcode: '',
    state: '',
    country: '',
    phone: ''
};