<template>
	<div class="col-md-12" v-loading="loading">
        <el-row v-if="book.list.length > 0" type="inline">
            <el-col :xs="11" :sm="6" :md="6" :lg="6" :xl="4" v-for="(book, index) in book.list" :key="index" class="info-center xs-mr-1">
                <el-card shadow="hover" class="card no-border-radius" :body-style="{ padding: '0px' }">

                    <img src="/img/draft-extended.png" v-if="book.is_save_as_draft" class="is_draft" title="Save As Draft"/>
                    <img src="/img/sold-extended.png" v-if="!book.is_save_as_draft && book.user_id != authId" class="is_publish" title="Sold"/>
                    <img src="/img/publish-extended.png" v-if="!book.is_save_as_draft && book.user_id == authId" class="is_publish" title="Publish"/>
                    
                    <div v-if="book.has_front_cover" 
                     class="card-content"
                     :style="{ padding: '0px' }">
                        <el-image
                         :src="book.front_cover"
                         :fit="'cover'"></el-image>
                    </div>
                    <div v-else
                     class="card-content" 
                     v-html="book.chapters.length > 0 && book.chapters[0].pages.length > 0 ? book.chapters[0].pages[0].content : '<strong>No Available Page</strong>'">
                    </div>
                    <div class="p-2">
                        <el-row>
                            <el-col :xs="20">
                                <div class="flex flex-col font-primary">
                                    <span class="text-xs" style="max-width: 100%; overflow: hidden; text-overflow: '...'; white-space: nowrap; text-overflow: ellipsis;">{{ book.title }}</span>
                                    <span>{{ book.book_date }}</span>
                                </div>
                            </el-col>
                            <el-col :xs="2"  class="flex justify-end content-center">
                                <el-story-book-actions 
                                  :story-book-data="book" 
                                  :option-type="'books'" 
                                  :auth-id="authId" 
                                  @change="handleStoryBookOptionChange"></el-story-book-actions>
                            </el-col>
                        </el-row>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <el-row v-else>
          <p class="alert alert-warning text-center">No Available Book</p>
        </el-row>
        <el-row>
            <el-pagination
              small
              :hide-on-single-page="hideOnSinglePage"
              @current-change="handleBookListCurrentChange"
              :current-page="book.current_page"
              :page-size="book.page_size"
              layout="prev, pager, next"
              :total="book.total">
            </el-pagination>
        </el-row>
    </div>
</template>
<script>
	// import CalculateBook from './print-book.vue';
	import StoryBookActions from '../options/story-book-actions.vue';

	export default {
        name: 'ElBookList',
		props: {
			onRefresh: String,
	        authId: Number,
	        filterData: Object,
		},
		components: {
	        // 'el-print-book': CalculateBook,
	        'el-story-book-actions': StoryBookActions,
	    },
		data(){
			return {
				hideOnSinglePage: true,
				loading: false,

				book: {
					list: [],
			        current_page: 1,
			        page_size: 12,
			        total: 0,
				},

				param: {
					title: '',
	                keyword: '',
	                current_page: 1,
	                page_size: 12,
				}
			}
		},
		created(){
			this.setParam()
			this.getBookList(this.param)
		},
		watch:{
			onRefresh(){
				let self = this
				if(self.onRefresh){
	                self.book.current_page = 1
					self.setParam()
					self.getBookList(self.param)
				}
			},
		},
		methods: {
			setParam(){
	            let self = this

	            // if visible book is equal to zero
	            if(self.book.list.length == 0 && self.book.current_page > 0)
	                self.book.current_page = 1

				self.param.title = self.filterData.search_title
	            self.param.keyword = self.filterData.search_keyword
	            self.param.month = self.filterData.selectedMonth
	            self.param.year = self.filterData.selectedYear
	            self.param.current_page = self.book.current_page
	            self.param.page_size = self.book.page_size
			},
			// paginnate book list
	        handleBookListCurrentChange(page_no){
	            let self = this
	            self.book.current_page = page_no

	            self.setParam()

	            self.getBookList(self.param)
	        },

	        // Get book list
			getBookList(param){
				let self = this
			    self.loading = true

			    axios.post('/books', param)
			    .then(response=>{
			        self.book.list = response.data.book
			        self.book.total = response.data.total

			        self.$emit('change', parseInt(response.data.min), parseInt(response.data.max))

			        self.loading = false
			    })
			    .catch(err=>{
			        self.loading = false
			        console.log(err.message)
			    })
			},

			handleStoryBookOptionChange(value){
		        this.$emit('on-refresh', false)
		        this.$emit('action', value)
	        },
		}
    }
</script>