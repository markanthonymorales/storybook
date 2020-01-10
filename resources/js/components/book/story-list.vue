<template>
    <div class="col-md-12" v-loading="loading">
        <el-row v-if="story.list.length > 0" type="inline" class="no-margin">
            <el-col 
             :xs="11" :sm="6" :md="6" :lg="6" :xl="4" 
             v-for="(story, index) in story.list" 
             :key="index" 
             class="info-center xs-mr-1">

                <el-card 
                 shadow="hover" 
                 class="card no-border-radius "
                 :body-style="{ padding: '0px' }">
             		<img src="/img/shared-extended.png" v-if="story.is_shared" class="is_shared" title="Shared Story"/>

             		<div v-if="story.is_file_type" class="card-content-pdf">
             			<pdf :src="story.file_url"style="width:100%;"></pdf>
             		</div>
                    <div v-else class="card-content" v-html="story.pages.length > 0 ? story.pages[0].content : ''"></div>

                    <div class="p-2">
                        <el-row>
                            <el-col :xs="20">
                                <div class="flex flex-col font-primary">
                                    <span class="text-xs" style="max-width: 100%; overflow: hidden; text-overflow: '...'; white-space: nowrap; text-overflow: ellipsis;">{{ story.title }}</span>
                                    <span>{{ story.story_date }}</span>
                                </div>
                            </el-col>
                            <el-col :xs="2" class="flex justify-end content-center">
                                <el-story-book-actions 
                                  :story-book-data="story" 
                                  :option-type="'stories'" 
                                  :auth-id="authId" 
                                  @change="handleOptionChange"></el-story-book-actions>
                            </el-col>
                        </el-row>
                    </div>
	                    

                </el-card>

            </el-col>
        </el-row>
        <el-row v-else>
          <p class="alert alert-warning text-center">No Available Story</p>
        </el-row>
        <el-row>
            <el-pagination
              small
              :hide-on-single-page="hideOnSinglePage"
              @current-change="handleStoryListCurrentChange"
              :current-page="story.current_page"
              :page-size="story.page_size"
              layout="prev, pager, next"
              :total="story.total">
            </el-pagination>
        </el-row>
    </div>
</template>
<style type="text/css">
	.card-content-pdf {
	    width: 150px;
	    height: 150px;
	    background-color: #f4f4f4 !important;
	    overflow: hidden;
	}
</style>
<script>
	import pdf from 'vue-pdf'
	import StoryBookActions from '../options/story-book-actions.vue';

	export default {
        name: 'ElStoryList',
		props: {
			onRefresh: String,
	        authId: Number,
	        filterData: Object,
		},
		components: {
			pdf,
			'el-story-book-actions': StoryBookActions,
	    },
		data(){
			return {
				hideOnSinglePage: true,
				loading: false,

				story: {
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
			this.getStoryList(this.param)
		},
		watch:{
			onRefresh(){
				let self = this
				if(self.onRefresh){
					self.story.current_page = 1
					self.setParam()
					self.getStoryList(self.param)
				}
			},
		},
		methods: {
			setParam(){
	            let self = this

	            // if visible book is equal to zero
	            if(self.story.list.length == 0 && self.story.current_page > 0)
	                self.story.current_page = 1

				self.param.title = self.filterData.search_title
	            self.param.keyword = self.filterData.search_keyword
	            self.param.month = self.filterData.selectedMonth
	            self.param.year = self.filterData.selectedYear
	            self.param.current_page = self.story.current_page
	            self.param.page_size = self.story.page_size
			},
			// paginnate book list
	        handleStoryListCurrentChange(page_no){
	            let self = this
	            self.story.current_page = page_no

	            self.setParam()

	            self.getStoryList(self.param)
	        },

	        // Get book list
			getStoryList(param){
				let self = this
			    self.loading = true

			    axios.post('/stories', param)
			    .then(response=>{
			        self.story.list = response.data.story
			        self.story.total = response.data.total

			        self.$emit('change', parseInt(response.data.min), parseInt(response.data.max))

			        self.loading = false
			    })
			    .catch(err=>{
			        self.loading = false
			        console.log(err.message)
			    })
			},

			handleOptionChange(value){
		        this.$emit('on-refresh', true)
		        this.$emit('action', value)
	        },
		}
    }
</script>