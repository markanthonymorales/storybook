import { book } from './data.js';

export const data = {
	mobile: false,
	fullScreenLoading: false,

	openPrintBookCalculation: false,
	printBook: {},

	auth_id: null,
	
	fileFrontCover: [],
	fileSpineCover: [],
	fileBackCover: [],

	openCoverDialog: false,
	dialogVisible: false,
	selectedYear: 2019,
	availableYear: [],
	currentCalendar: 0,

	currentPage: 0,
	event: 'close',

	stories: {},

	selectedRange: '2019-01-01',
	availableStories: [],

	book:{
		...book
	},
	isPreviewBook: false,
	previewBook: {
		title: '',
		book_date: '',
		pages: [],
		tags: [],
		shared_to: [],
	},

	// custom_price: 0.00,
	selectedPreviewStory: {},
	previewStoryContent: '',
	previewStory: false,
	previewBook: false,
	
	// carousel_preview_arraw: 'never',
	inputTag: '',
	inputVisible: false,

	filterByMonths: [
		{
			title: "January",
			range: []
		},
		{
			title: "February",
			range: []
		}, 
		{
			title: "March",
			range: []
		}, 
		{
			title: "April",
			range: []
		}, 
		{
			title: "May",
			range: []
		}, 
		{
			title: "June",
			range: []
		}, 
		{
			title: "July",
			range: []
		}, 
		{
			title: "August",
			range: []
		}, 
		{
			title: "September",
			range: []
		}, 
		{
			title: "October",
			range: []
		}, 
		{
			title: "November",
			range: []
		}, 
		{
			title: "December",
			range: []
		}
	],
}