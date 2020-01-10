<template>
	<div class="col-md-12" v-loading="loading">
		<el-row type="inline">
			<el-col v-if="!isMobile()" :xs="11" :sm="6" :md="6" :lg="6" :xl="4" class="hide-from-mobile info-center" style="padding-left: 33px;">
				<el-link href="/write/create" :underline="false">
					<el-card shadow="hover" class="card info-center no-border-radius no-padding" >
						<i class="el-icon-plus text-red-700 text-3xl font-weight transparent"></i>
					</el-card>
				</el-link>
			</el-col>
			<el-col 
			  v-if="recentViewed.length > 0" 
			  :xs="11" :sm="6" :md="6" :lg="6" :xl="4" 
			  v-for="(story, index) in recentViewed" 
			  :key="index" 
			  class="info-center xs-mr-1">
				<el-card shadow="hover" class="card no-border-radius"
					:body-style="{ padding: '0px' }">
					<img src="/img/shared-extended.png" v-if="story.is_shared && story.object_type == 'stories'" class="is_shared" title="Shared Story"/>

					<img src="/img/draft-extended.png" v-if="story.is_save_as_draft && story.object_type == 'books'" class="is_draft" title="Save As Draft"/>
					<img src="/img/sold-extended.png" v-if="!story.is_save_as_draft && story.user_id != authId && story.object_type == 'books'" class="is_publish" title="Sold"/>
					<img src="/img/publish-extended.png" v-if="!story.is_save_as_draft && story.user_id == authId && story.object_type == 'books'" class="is_publish" title="Publish"/>
					
					<div v-if="story.is_file_type" class="card-content-pdf">
					  	<pdf :src="story.file_url"style="width:100%;"></pdf>
					</div>
					<div v-else-if="story.cover" 
					 class="card-content font-small"
					 :style="{ padding: '0px' }">
						<el-image
						 :src="story.cover"
						 :fit="'cover'"></el-image>
					</div>
					<div v-else
						class="card-content font-small" 
						v-html="story.pages.length > 0 ? story.pages[0].content : ''"></div>
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
								  :option-type="story.object_type" 
								  :auth-id="authId" 
								  @change="handleOptionChange"></el-story-book-actions>
							</el-col>
						</el-row>
					</div>
				</el-card>
			</el-col>
			<el-col 
			  v-if="recentViewed.length < 1" 
			  :xs="24" :sm="18" :md="18" :lg="18" :xl="12">
				<p class="alert alert-warning text-center">No Recent Viewed Book or Story</p>
			</el-col>

		</el-row>
	</div>
</template>
<script>
  import pdf from 'vue-pdf'
	import StoryBookActions from '../options/story-book-actions.vue';

	export default {
		name: 'ElRecentViewList',
		props: {
			onRefresh: String,
			authId: Number,
		},
		components: {
		pdf,
		'el-story-book-actions': StoryBookActions,
		},
		data(){
			return {
				hideOnSinglePage: true,
				loading: false,
				recentViewed: {},
			}
		},
		created(){
			this.getRecentlyViewedStory()
		},
		watch:{
			onRefresh(){
				if(this.onRefresh){
					this.getRecentlyViewedStory()
				}
			}
		},
		methods: {
			// Get Recent Story list
			getRecentlyViewedStory(){
				let self = this
				self.loading = true
				axios.get('/recently/viewed/story')
				.then(response=>{
					self.loading = false
					self.recentViewed = response.data.story
				})
				.catch(err=>{
					self.loading = false
					console.log(err.message)
				})
			},
			handleOptionChange(value){
				this.$emit('on-refresh', false)
				this.$emit('action', value)
			}
		}
	}
</script>