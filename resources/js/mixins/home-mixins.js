import axios from 'axios'
import ElProducts from '../components/book/products.vue';
import CalculateBook from '../components/book/print-book.vue';
import StoryBookActions from '../components/options/story-book-actions.vue';
import RecentViewList from '../components/book/recent-view-list.vue';
import StoryList from '../components/book/story-list.vue';
import BookList from '../components/book/book-list.vue';
import { data } from '../data/home-data.js';
import { resize_window } from '../modules/resize-window.js';

export default {
	props:['userData'],
    components: {
        'el-products': ElProducts,
        'el-print-book': CalculateBook,
        'el-story-book-actions': StoryBookActions,
        'el-recent-view-list': RecentViewList,
        'el-story-list': StoryList,
        'el-book-list': BookList,
    },
	data(){
        return {
          ...data,
          param: {
            search_title: '',
            search_keyword: '',
            selectedMonth: [1, 12],
            selectedYear: [new Date().getFullYear() - 7, new Date().getFullYear()],
          },
          onRefresh: '',
        }
    },
    created(){
        let self = this
        self.mobile = self.isMobile()
        self.auth_id = self.userData.id
        self.getSelectedMonth = self.isMobile()?'5':5,

        resize_window(self)

        Event.$on('cart', cart => {
            self.cart = cart
        })

        Event.$on('on-refresh', status => {
            self.callRefresh()
        })
    },

    watch: {
        search_title: _.debounce( function() {
            this.isTyping = false
        }, 1000),
        search_keyword: _.debounce( function() {
            this.isTyping = false
        }, 1000),
        isTyping(value){
            if(!value){
                this.callRefresh()
            }
        },
    },

	methods: {
        handleFilterData(){
            this.param = {
                search_title: this.search_title,
                search_keyword: this.search_keyword,
                selectedMonth: !this.allMonth?this.getSelectedMonth:[1, 12],
                selectedYear: !this.allYear?this.getSelectedYear:[this.minYear, this.maxYear],
            }
        },

        handleChangeData(min, max){
            let self = this
            if(min > 0 && self.minYear > min)
                self.minYear = min

            if(self.maxYear < max)
                self.maxYear = max

            self.getYearSlider()
        },

		handleStoryBookOptionChange(value){
            let self = this
            if(value == 'remove action' || value == 'on refresh'){
                self.callRefresh()
                return false
            }
            self.printBook.id = value
            self.openPrintBookCalculation = true
        },

        callRefresh(){
            this.handleFilterData()
            this.onRefresh = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
        },
		getYearSlider(){
			let self = this
			self.filterByYears = {}
		    for(var i = self.minYear; i <= self.maxYear; i++){
		        self.filterByYears[i] = i.toString()
		    }
		},
	}
}