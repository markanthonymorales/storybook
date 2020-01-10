<template>
	<div id="printPage" class="wrapper-body" v-loading.fullscreen.lock="fullScreenLoading">
		<el-row type="inline" class="mb-3">
			<el-col :xs="24" :sm="24" :md="12" :lg="12">
				<div class="label-text font-primary">My Story Content</div>
			</el-col>
			<el-col :xs="24" :sm="24" :md="12" :lg="12" class="end">
				<el-select v-model="selectedYear" placeholder="Select">
					<el-option
					   v-for="(year, index) in availableYear" 
					   :key="index" 
					   :label="year" 
					   :value="year">
					</el-option>
				</el-select>
			</el-col>
		</el-row>

		<div v-if="!mobile" class="mb-3 font-primary">
			<el-row type="flex" justify="center">
				<el-col :span="1" class="center" >
					<i class="display-right  el-icon-arrow-left font-xx-l font-weight cursor"  @click="setActiveItem(currentCalendar - 1)"></i>
				</el-col>
				<el-col>
					<el-carousel 
						type="card"
						ref="carousel" indicator-position="none" :autoplay="false" @change="onChangePage" arrow="never">
						<el-carousel-item
						 v-for="(month, index) in filterByMonths"
						 :key="index">

							<el-custom-calendar 
								v-if="month.range.length > 0"
								v-model="month.range[0][0]"
								:selected="selectedRange" 
								:title="month.title" 
								:range="month.range[0]"
								:story-list="stories"
								>
							</el-custom-calendar>

						</el-carousel-item>
					</el-carousel> 
				</el-col>
				<el-col :span="1" class="center" >
					<i class=" el-icon-arrow-right font-xx-l font-weight cursor" @click="setActiveItem(currentCalendar + 1)"></i>
				</el-col>
			</el-row>
		</div>

		<!-- CALENDAR- mobile -->
		<el-row v-else class="mb-3 font-primary">
			<el-col class="shadow custom-calendar">
				<el-carousel ref="carousel" indicator-position="none" @change="onChangePage" :autoplay="false" arrow="always">
					<el-carousel-item
					 v-for="(month, index) in filterByMonths"
					 :key="index">
						<el-custom-calendar 
							v-if="month.range.length > 0"
							v-model="month.range[0][0]"
							:selected="selectedRange" 
							:title="month.title" 
							:range="month.range[0]"
							:story-list="stories"
							>
						</el-custom-calendar>
					</el-carousel-item>
				  </el-carousel>
			</el-col>
		</el-row>
		
		<!-- ADD STORIES TO BOOK -->
		<el-row type="flex" class="label-text font-primary mb-2">
			<el-col :span="11" :lg="10"><div >Stories</div></el-col>
			<el-col :span="2" :lg="4"></el-col>
			<el-col :span="11" :lg="10"><div>My Storybooks</div></el-col>
		</el-row>

		<el-form :model="book" :rules="bookRules" ref="bookForm" :label-position="'top'" class="mb-3">
			<el-row type="flex" class="mb-3">
				<el-col :span="11" :lg="11">
					<div class="draggable-container">
						<draggable 
						:list="availableStories"
						class="list-group draggable-card draggable-card-left inline" 
						@start="drag=true" @end="drag=false"
						draggable=".item"
						group="story" 
						>
							<div
								class="pointer float-left list-group-item card-docs font-xs mr-2 mb-3 item no-border-radius" style="word-wrap: break-word; font-size: 8px; text-align: center; line-height: 16;"
								v-for="(story, index) in availableStories"
								:key="index"
								@click="handlePreviewStory(story)"
							>
								<label class="pointer">{{ story.title }}</label>
							</div>
						</draggable>
					</div>
				</el-col>

				<el-col :span="2" :lg="2" class="info-center sm:text-base md:text-3xl">
					<el-row>
						<i class=" el-icon-caret-left"></i>
					</el-row>
					<el-row>
						<i class="el-icon-caret-right"></i>
					</el-row>
				</el-col>

				<el-dialog
				  :visible.sync="dialogVisible"
				  width="30%"
				  center>
				  <div style="padding: 23% 5%;position: relative;" class="table-of-content" v-html="book.content"></div>
				</el-dialog>

				<el-col :span="11" :lg="11">
					<div class="draggable-container">
						<div 
						   id="table_of_content"
						   style="word-wrap: break-word; font-size: 8px; text-align: center; line-height: 16;"
						   class="card-docs card-table-of-content pointer float-left list-group-item display-border mr-2 mb-2"
						   v-html="book.content"
						   @click="dialogVisible = true"
						></div>
						<draggable 
							:disabled="!book.is_save_as_draft"
							:list="book.chapters" 
							class="list-group draggable-card draggable-card-left inline" 
							@start="drag=true" @end="drag=false"
							draggable=".item"
							group="story" 
							@change="handleBookChaptersContainer"
							>

							  <div
								class="pointer float-left list-group-item card-docs font-xs mr-2 mb-3 item no-border-radius" style="word-wrap: break-word; font-size: 8px; text-align: center; line-height: 16;"
								v-for="(story, index) in book.chapters"
								v-if="story"
								:key="index"
								@click="handlePreviewStory(story)">
									<label class="pointer">{{ story.title }}</label>
								</div>
						</draggable>
					</div>
				</el-col>
			</el-row>

			<!-- Preview Story -->
			<el-dialog
				  title="Preview Story"
				  :visible.sync="previewStory"
				  :width="!mobile?'820px !important':'90% !important'"
				  center>
					<div class="mb-3 font-primary">
						<div class="text-center bg-gray-200 p-2">
							<h1 class="sm:text-base md:text-3xl font-bold">{{ selectedPreviewStory.title }}</h1>
							<h6>{{ selectedPreviewStory.story_date }} <font class="font-bold"> by: </font> {{ selectedPreviewStory.author }} </h6>
						</div>

						<!-- for mobile -->
						<div v-if="mobile" class="mobile-only">
							<el-row class="bg-gray-300 p-2 overflow-x-auto mb-3">
								<el-col style="padding: 10px 9px;">
									<div class="flex flex-row">
										<div 
											v-for="(page, index) in selectedPreviewStory.pages"
											:key="index"
											@click="previewStoryContent = page.content"
											class="display-row mr-2">
											<span class="mr-2"> 
												{{ index + 1 }} 
											</span>
											<div 
												v-html="page.content"
												class="card-docs">
											</div>
										</div>
									</div>
								</el-col>
							</el-row>
							
							<el-row>
								<el-col>
									<div class="big-paper mb-1 border-gray-400 bg-white">
										<div v-html="previewStoryContent"></div>
									</div>
								</el-col>
							</el-row>
						</div>

						<!-- for web -->
						<div v-else class="bg-gray-200 hide-from-mobile hide-from-other">
							<el-row type="flex">
								<el-col :span="6">
									<!-- is_file_type -->
									<div v-if="selectedPreviewStory.is_file_type"  class="bg-gray-200 p-2 listing-page mb-2 mr-2">
										<div 
										class="pages-container flex flex-row mb-2"
										v-for="i in totalPDFPages"
										:key="i"
										@click="handlePDFThumbnail(i)">
											<div class="flex">
												<div class="w-8">
													<span class="page-number"> {{ i }} </span>
												</div>
												<pdf
													:key="i"
													:src="selectedPreviewStory.file_url"
													:page="i"
													class="flex flex-col pointer thumbnail-pdf"
													:class="{  'is-active': currentPDFPage == i }"
													style="display: block; width: 1.3in;"
												></pdf>
											</div>
										</div>
									</div>
									<div v-else>
										<el-row class="no-margin">
											<el-col class="bg-gray-200 pt-2 pb-2 pl-2">
												<div class="listing-page">
													<div 
													v-for="(page, index) in selectedPreviewStory.pages"
													:key="index"
													@click="previewStoryContent = page.content"
													class="flex flex-row mb-3">
														<span class="mr-2"> 
															{{ index + 1 }} 
														</span>
														<div 
														v-html="page.content"
														class="pointer card-docs display-border padding-sm font-xs" style="word-wrap: break-word;"></div>
													</div>
												</div>
												
											</el-col>
										</el-row>
									</div>
								</el-col>
								<el-col :span="20">
									<div v-if="selectedPreviewStory.is_file_type">
										<el-upload-pdf 
										v-model="selectedPreviewStory.file_url" 
										:selected-page="currentPDFPage" 
										@change="handlePDFChange"></el-upload-pdf>
									</div>
									<div v-else class="big-paper bg-white mb-1 border-gray-400">
										<div v-html="previewStoryContent"></div>
									</div>
								</el-col>
							</el-row>
						</div>
					</div>
			</el-dialog>
			<!-- End of Preview Story -->

			<div class="bg-gray-200 p-3">
				<el-row :gutter="20">
					<el-col>
						<el-form-item label="Title" label-size="18px" prop="title">
							<el-input :disabled="!book.is_save_as_draft" v-model="book.title" placeholder="Enter Book Title Here..." ></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row type="flex" :gutter="20">
					<el-col>
						<el-form-item label="Description" size="medium" prop="description">
							<el-input :disabled="!book.is_save_as_draft" type="textarea" v-model="book.description"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row :gutter="20">
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
						<el-form-item label="From" label-size="18px" prop="from_date">
							<el-date-picker
							  :disabled="!book.is_save_as_draft"
							  v-model="book.from_date"
							  type="date"
							  format="yyyy/MM/dd"
							  value-format="yyyy-MM-dd"
							  @change="checkFromAndTo"
							  placeholder="Pick a day" class="w-full">
							</el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
						<el-form-item label="To" label-size="18px" prop="to_date">
							<el-date-picker
							  :disabled="!book.is_save_as_draft"
							  v-model="book.to_date"
							  type="date"
							  format="yyyy/MM/dd"
							  value-format="yyyy-MM-dd"
							  @change="checkFromAndTo"
							  placeholder="Pick a day" class="w-full">
							</el-date-picker>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
						<el-form-item label="Book Date" label-size="18px" prop="book_date">
							<el-date-picker
							  :disabled="!book.is_save_as_draft"
							  v-model="book.book_date"
							  type="date"
							  format="yyyy/MM/dd"
							  value-format="yyyy-MM-dd"
							  placeholder="Pick a day" class="w-full">
							</el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>


				<el-row type="flex" :gutter="20">
					<el-col :span="12">
						<el-form-item>
							<el-button 
							:disabled="!book.is_save_as_draft" 
							type="primary" 
							icon="el-icon-reading"
							@click="handleOpenCoverDialog">Add Cover</el-button>
						</el-form-item>
					</el-col>
				</el-row>

				<el-dialog
				  title="Cover Page"
				  :visible.sync="openCoverDialog"
				  width="650px !important"
				  class="update-cover"
				  center>
					<div class="dialog-body">
						<el-row type="flex" slot="body" class="dialog-body justify-center">
							
							<el-col title="Back Cover" style="width: 565px; height: 842px; zoom: 37.3%; transform: perspective(1200px) rotateY(-16deg); margin-top: 32px; margin-right: 10px;">
								
								<el-upload
								  :action="'/book/upload/'+book.id+'/backCover'"
								  :headers="headerInfo"
								  ref="uploadBackCover"
								  id="uploadBackCover"
								  list-type="picture-card"
								  :auto-upload="false"
								  :file-list="fileBackCover"
								  :on-change="handlePreview"
								  :on-remove="handleRemove"

								  :on-success="handleBackCoverSuccess"
								  :on-error="handleBackCoverError"

								  :limit="1">
									<i slot="default" class="el-icon-plus"></i>
									<div slot="file" slot-scope="{file}">
									  <img
										class="el-upload-list__item-thumbnail"
										:src="file.url" alt=""
									  >
									</div>
								</el-upload>

							</el-col>

							<el-col v-show="is_spine_allowed" title="Spine Cover" :style="'width: '+spineCalculatedWidth+'cm; height: 842px; zoom: 40%; overflow: hidden;'">
								<el-upload
								  :action="'/book/upload/'+book.id+'/spineCover'"
								  :headers="headerInfo"
								  ref="uploadSpineCover"
								  id="uploadSpineCover"
								  list-type="picture-card"
								  :auto-upload="false"
								  :file-list="fileSpineCover"
								  :on-change="handlePreview"
								  :on-remove="handleRemove"

								  :on-success="handleSpineCoverSuccess"
								  :on-error="handleSpineCoverError"

								  :limit="1">
									<i slot="default" class="el-icon-plus"></i>
									<div slot="file" slot-scope="{file}">
									  <img
										class="el-upload-list__item-thumbnail"
										:src="file.url" alt=""
									  >
									</div>
								</el-upload>
							</el-col>

							<el-col title="Front Cover" style="width: 565px; height: 842px; zoom: 37.3%; transform: perspective(1200px) rotateY(16deg); margin-top: 32px; margin-left: 10px;">
								
								<el-upload
								  :action="'/book/upload/'+book.id+'/frontCover'"
								  :headers="headerInfo"
								  ref="uploadFrontCover"
								  id="uploadFrontCover"
								  list-type="picture-card"
								  :auto-upload="false"
								  :file-list="fileFrontCover"
								  :on-change="handlePreview"
								  :on-remove="handleRemove"

								  :on-success="handleFrontCoverSuccess"
								  :on-error="handleFrontCoverError"

								  :limit="1">
									<i slot="default" class="el-icon-plus"></i>
									<div slot="file" slot-scope="{file}">
									  <img
										class="el-upload-list__item-thumbnail"
										:src="file.url" alt=""
									  >
									</div>
								</el-upload>

							</el-col>

						</el-row>
					</div>
					<span slot="footer" class="dialog-footer mt-2 block text-left">
						<h1><font style="font-weight: 700; color: red;">Notes: </font></h1>
						<ul class="pl-3 m-2 list-decimal">
							<li>Recommended size(s) to upload 565(width) x 842(height) pixels.</li>
							<li>To have a "Spine Cover" the total pages must be greater or equal to 24 page.</li>
						</ul>
						<h1><strong>Total Page:</strong> ({{ total_page - 2 }})page{{ total_page > 1 ? '\'s': '' }}</h1>
					</span>
				</el-dialog>

				<el-row type="inline" :gutter="20">
					<el-col :xs="12" :sm="12" :md="4" :lg="4">
						<el-form-item>
							<el-radio :disabled="!book.is_save_as_draft" v-model="book.status_type" label="free">Public</el-radio>
						</el-form-item>
					</el-col>
					<el-col :xs="12" :sm="12" :md="4" :lg="4">
						<el-form-item>
								<el-radio :disabled="!book.is_save_as_draft" v-model="book.status_type" label="for sale">For Sale</el-radio>
						</el-form-item>
					</el-col>
					<el-col :xs="12" :sm="12" :md="4" :lg="4">
						<el-form-item>
							<el-radio :disabled="!book.is_save_as_draft" v-model="book.status_type" label="private">Private</el-radio>
						</el-form-item>
					</el-col>
					<el-col :xs="24" :sm="24" :md="12" :lg="12">
						<el-form-item prop="price">
							<el-input :readOnly="book.status_type != 'for sale' || !book.is_save_as_draft" v-model.number="book.original_price" placeholder="Price..">
								<template slot="append">+ Markup Price({{ markupPrice }}%) = {{ book.price }}</template>
							</el-input>
							<label></label>
						</el-form-item>
					</el-col>
				</el-row>

				<el-row type="flex" class="mb-3" :gutter="20">
					<el-col class="bg-white p-2 font-primary">
						<span v-if="!book.is_save_as_draft">
							<el-tag
							  v-for="tag in book.tags"
							  :key="tag"
							  closable
							  :disabled-transitions="false"class="mb-2">
							  {{tag}}
							</el-tag>
						</span>
						
						<span v-else>
							<el-tag
							  v-for="tag in book.tags"
							  :key="tag"
							  closable
							  :disabled-transitions="false" 
							  @close="handleClose(tag)" class="mb-2">
							  {{tag}}
							</el-tag>
						</span>

						<el-input
						  class="input-new-tag"
						  v-if="inputVisible && book.is_save_as_draft"
						  v-model="inputTag"
						  ref="saveTagInput"
						  size="mini"
						  @keyup.enter.native="handleInputConfirm"
						  @blur="handleInputConfirm"
						>
						</el-input>
						<el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Tag</el-button>
					</el-col>
				</el-row>

				<el-row class="end" :gutter="20">
					<el-col :xs="24" :sm="8" :md="8" :lg="6" :xl="6" class="mb-2 sm:mb-0 lg:mr-3">
						<el-button 
						@click="onPreviewBook(book)" 
						class="w-full" 
						:icon="'el-icon-'+(isPreviewBook ? 'close' : 'view')"
						:type="isPreviewBook ? 'danger' : 'default'">{{ isPreviewBook ? 'Close Preview Storybook' : 'Preview Storybook' }}</el-button>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="6" :xl="6" class="mb-2 sm:mb-0 lg:mr-3">
						<el-button 
						@click="onSaveAsDraft('bookForm')" 
						class="button-red w-full" 
						icon="el-icon-check"
						:disabled="book.is_save_as_draft?false:true">Save As Draft</el-button>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="6" :xl="6">
						<el-button 
						@click="onSave('bookForm')" 
						class="button-red w-full" 
						icon="el-icon-collection-tag"
						:disabled="book.is_save_as_draft?false:true">Publish</el-button>
					</el-col>
					<el-col :xs="24" :sm="8" :md="8" :lg="6" :xl="6">
						<el-button 
						@click="onSaveAndPrint('bookForm')" 
						icon="el-icon-printer"
						class="button-red w-full">{{ book.is_save_as_draft?'Publish & Print':'Print' }}</el-button>
					</el-col>
				</el-row>
			</div>
		</el-form>

		<!-- BOOK PREVIEW -->
		<el-row type="flex" class="bg-gray-200 p-3"  v-if="isPreviewBook">
			<el-read-book :read-story="previewBook" :type-data="'books'" :is-preview="'true'"></el-read-book>
		</el-row>
		<el-dialog 
		  title="Print Book"
		  v-if="openPrintBookCalculation"
		  :visible.sync="openPrintBookCalculation" 
		  class="print-book-form">
			<el-row type="flex">
				<el-col :span="24">
					<el-print-book 
					  :id-data="printBook.id" 
					  ></el-print-book>
				</el-col>
			</el-row>
		</el-dialog>
	</div>
</template>
<style type="text/css">
	.thumbnail-pdf:hover {
	    box-shadow: 4px 5px 10px #000;
	}
	.thumbnail-pdf.is-active{
	    box-shadow: 4px 5px 10px #000;
	}
	div.card-table-of-content table td p{
		line-height: 0;
	}
	.update-cover .el-dialog__body .el-upload.el-upload--picture-card{
		min-height: 842px;
	}
	.update-cover li.el-upload-list__item img{ 
		min-height: 842px;
		max-height: 842px;
	}
	#uploadSpineCover li.el-upload-list__item img {
		object-fit: cover !important;
		height: min-content;
	}
	li.el-upload-list__item.is-ready img {
		object-fit: contain !important;
	}
	.el-dialog__body .el-upload.el-upload--picture-card {
		line-height: 385px;
	}
	.el-dialog__body .el-upload.el-upload--picture-card, 
	.el-dialog__body .el-upload-list--picture-card, 
	.el-dialog__body .el-upload-list--picture-card .el-upload-list__item {
		width: 100%;
		height: 100%;
		border-radius: 0;
	}
	.el-dialog__body .el-upload-list--picture-card .el-upload-list__item{
		position: relative;
		display: flex;
		margin: 0;
	}
	#printPage .listing-page{
		max-height: 890px;
		overflow: auto;
	}
	@media only screen and (max-width: 767px) {
		#printPage .listing-page{
			max-height: initial;
			overflow: auto;
		}
		/*.big-paper .image-style-align-right{
			margin-left: 30px !important;
		}*/
	}
</style>
<script src="../../mixins/book/mixins.js"></script>
