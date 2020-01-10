import draggable from 'vuedraggable'
import moment from 'moment'

import '../../components/book/custom-calendar/calendar.css'

import CalculateBook from '../../components/book/print-book.vue';
import ReadBook from '../../components/read-page.vue';
import CustomCalendar from '../../components/book/custom-calendar/calendar.vue'
import { data } from '../../data/book-data.js'
import pdfMixins from '../pdf-mixins.js'

export default {
	props: ['bookData', 'authId', 'authorData', 'markupPrice', 'ebookMarkupPrice'],
	components: {
		draggable,
		'el-custom-calendar': CustomCalendar,
		'el-print-book': CalculateBook,
		'el-read-book': ReadBook,
	},
	mixins: [pdfMixins],
	data(){
		let checkPrice = (rule, value, callback) => {
			if (value < 0) {
				return callback(new Error('Please input the price'));
			}
			setTimeout(() => {
				if (!Number.isFinite(value)) {
					callback(new Error('Please input digits only'));
				} else {
					if (value < 0) {
						callback(new Error('Price must be greater than 0'));
					} else {
						callback();
					}
				}
			}, 500);
		};

		let checkFromDate = (rule, value, callback) => {
			if (!value) {
				return callback(new Error('Please pick a date'));
			}
			setTimeout(() => {
				if (value > this.book.to_date) {
					callback(new Error('Date must be lesser than or equal to date "To"'));
				} else {
					callback();
				}
			}, 500);
		};

		let checkToDate = (rule, value, callback) => {
			if (!value) {
				return callback(new Error('Please pick a date'));
			}
			setTimeout(() => {
				if (value < this.book.from_date) {
					callback(new Error('Date must be greater than or equal to date "From"'));
				} else {
					callback();
				}
			}, 500);
		};

		return {
			...data,
			total_page: 0,
			spineCalculatedWidth: 0.67,
			is_spine_allowed: false,
			bookRules: {
				title: [
					{ 
						required: true, 
						message: 'Please input Title name', 
						trigger: 'blur' 
					}
				],
				description: [
					{ 
						required: true, 
						message: 'Please input Description', 
						trigger: 'blur' 
					}
				],
				price: [
					{ validator: checkPrice, trigger: 'blur' }
				],
				from_date: [
					{ validator: checkFromDate, trigger: 'change' }
				],
				to_date: [
					{ validator: checkToDate, trigger: 'change' }
				],
				book_date: [
					{ 
						required: true, 
						message: 'Please pick a date', 
						trigger: 'change' 
					}
				],
			},
		}
	},
	
	created(){
		let self = this

		self.mobile = self.isMobile()
		self.book.author = self.authorData
		self.book.original_price = 0.00

		self.auth_id = self.authId

		self.headerInfo['X-CSRF-TOKEN'] = window.axios.defaults.headers.common['X-CSRF-TOKEN']

		window.addEventListener('resize', function() {
			self.mobile = self.isMobile()
		})

		if(Object.keys(self.bookData).length > 0){
			self.book = self.bookData

			if(self.book.has_front_cover)
				self.fileFrontCover = [{ name: 'front_cover', url: self.book.front_cover }]

			if(self.book.has_spine_cover)
				self.fileSpineCover = [{ name: 'spine_cover', url: self.book.spine_cover }]
			
			if(self.book.has_back_cover)
				self.fileBackCover = [{ name: 'back_cover', url: self.book.back_cover }]

			if(!self.book.book_date)
				self.book.book_date = moment(new Date()).format('YYYY-M-DD')

			
			self.total_page = 0
			self.book.chapters.forEach(function(i){
				self.total_page+=1

				if(i.is_file_type)
					self.total_page = self.total_page + i.total_page

				i.pages.forEach(function(a, n){						
					self.total_page+=1
				})
			})
			console.log(self.total_page)

			self.is_spine_allowed = false
			if(self.total_page > 24)
				self.is_spine_allowed = true

			self.total_page += 2

			self.spineCalculatedWidth = parseInt(self.total_page) / 2 * 0.0144 + 0.5
			self.spineCalculatedWidth += 0.1

			console.log(self.spineCalculatedWidth)
		}

		self.book.markup_price = self.markupPrice
		self.book.ebook_markup_price = self.ebookMarkupPrice

		self.getStories()
		Event.$on('selectedDate', date => {
			self.selectedRange = date
		})

	},
	methods:{
		handleFrontCoverSuccess(res){
			this.book.front_cover = res.url
			this.book.has_front_cover = res.success
			if(this.book.has_front_cover)
				this.fileFrontCover = [{ name: 'front_cover', url: this.book.front_cover }]

			this.handleCoverSuccess('Front')
		},
		handleSpineCoverSuccess(res){
			this.book.spine_cover = res.url
			this.book.has_spine_cover = res.success
			if(this.book.has_spine_cover)
				this.fileSpineCover = [{ name: 'spine_cover', url: this.book.spine_cover }]

			this.handleCoverSuccess('Spine')
		},
		handleBackCoverSuccess(res){
			this.book.back_cover = res.url
			this.book.has_back_cover = res.success
			if(this.book.has_back_cover)
				this.fileBackCover = [{ name: 'back_cover', url: this.book.back_cover }]

			this.handleCoverSuccess('Back')
		},
		handleCoverSuccess(name){
			this.$notify({
				title: 'Success',
				message: 'Successfully upload '+name+' Cover image.',
				type: 'success'
			})
		},
		handleFrontCoverError(err){
			let data = JSON.parse(err.message);
			this.handleCoverError(data)
		},
		handleSpineCoverError(err){
			let data = JSON.parse(err.message);
			this.handleCoverError(data)
		},
		handleBackCoverError(err){
			let data = JSON.parse(err.message);
			this.handleCoverError(data)
		},
		handleCoverError(data){
			this.fullScreenLoading = false

			this.$notify.error({
			  title: 'Error',
			  message: data.message
			})
		},

		handleOpenCoverDialog(){
			let self = this
			self.isPreviewBook = false
			self.openCoverDialog = true
			self.handleHideAndShowUpload()
		},
		handleHideAndShowUpload(){
			let self = this
			self.book.front_cover = ''
			self.book.has_front_cover = false
			self.book.spine_cover = ''
			self.book.has_spine_cover = false
			self.book.back_cover = ''
			self.book.has_back_cover = false

			setTimeout(function(){

				let classes = ['uploadFrontCover', 'uploadSpineCover', 'uploadBackCover']
				for(let a = 0; a < classes.length; a++){
					var div = document.getElementById(classes[a])
					var checkLength = div.children[0].childElementCount
					if(checkLength > 0){

						if(classes[a] == 'uploadFrontCover' && self.$refs[classes[a]].uploadFiles.length > 0){
							self.book.front_cover = self.$refs[classes[a]].uploadFiles[0].url
							self.book.has_front_cover = true
						}
						
						if(classes[a] == 'uploadSpineCover' && self.$refs[classes[a]].uploadFiles.length > 0){
							self.book.spine_cover = self.$refs[classes[a]].uploadFiles[0].url
							self.book.has_spine_cover = true
						}
						
						if(classes[a] == 'uploadBackCover' && self.$refs[classes[a]].uploadFiles.length > 0){
							self.book.back_cover = self.$refs[classes[a]].uploadFiles[0].url
							self.book.has_back_cover = true
						}
			
						document.getElementById(classes[a]).children[1].style.display = 'none'
					}else
						document.getElementById(classes[a]).children[1].style.display = 'block'

				}
			}, 100)
		},
		handlePreview(i){
			let self = this

			self.handleHideAndShowUpload()
		},
		handleRemove(i){
			let self = this

			setTimeout(function(){
				self.handleHideAndShowUpload()
			}, 1000)
		},
		handleMountedRange(){
			this.selectedRange = moment(new Date()).format('YYYY-M-DD')
			let mm = this.selectedRange.split('-')
			this.setActiveItem(mm[1] - 1);
		},
		checkFromAndTo(evt){
			this.$refs.bookForm.validateField('from_date')
			this.$refs.bookForm.validateField('to_date')
		},
		addTableOfContent(chapters){
			let tableOfContent = `<h1>Table of Content</h1>
				<table>
					<tbody>`

			let tr = ``
			let pageNumber = 0
			chapters.forEach(function (i) {
				if(typeof i != 'undefined'){
					let ttl_page = i.pages.length
					if(i.is_file_type)
						ttl_page = i.total_page

					tr += `<tr>
							<td>
								<h5>
									<strong>${i.title}</strong>
								</h5>
								<i></i>
								<font>${pageNumber+(ttl_page>0?1:0)}-${pageNumber+ttl_page}</font>
							</td>
						</tr>`
					pageNumber+=ttl_page
				}
			})

			tableOfContent += `${tr}</tbody>
				</table>`
			this.book.content = tableOfContent
		},
		handleFileUploads(fileName){
			let self = this
			let file = self.$refs[fileName].files[0]
			if(typeof self.book.files === 'undefined')
				self.book.files = new FormData()
			self.book.files.set(fileName, file)
			let url = URL.createObjectURL(file);

			if(fileName == 'front_cover')
				self.book.has_front_cover = true
			
			if(fileName == 'back_cover')
				self.book.has_back_cover = true

			self.book[fileName] = url
			self.handlePreviewBook()
		},
		onPreviewBook(book){
			let self = this
			self.$refs.bookForm.validate((valid) => {
				if (valid) {
					if(self.isPreviewBook){
						self.isPreviewBook = false
					}else{
						self.handlePreviewBook()
						self.isPreviewBook = true
					}
				} else {
					self.$notify.error({
					  title: 'Error',
					  message: 'Please fill-up the required fields before previewing'
					})
					return false;
				}
			})
		},
		handlePreviewBook(){
			let self = this
			let { author, id, book_date, title, tags, shared_to } = self.book
			self.previewBook = {
				author, id, book_date, title, search_tags: tags, shared_to
			}

			let pages = []

			if(self.book.has_front_cover)
				pages.push({
					id: null,
					is_cover: true,
					content: `url("${self.book.front_cover}")`
				})

			pages.push({
				id: null,
				is_cover: false,
				content: `<div class="table-of-content">${self.book.content}</div>`
			})

			self.total_page = 0
			self.book.chapters.forEach(function(i){
				self.total_page += 1

				pages.push({
						id: null,
						is_cover: false,
						content: `<legend 
						class="book-preview-title"
						>${i.title}</legend>`
				})

				if(i.is_file_type){
					self.total_page = self.total_page + i.total_page
					pages.push({
						id: null,
						book_chapter_id: null,
						content: `<div class="pdf-watermark">
							<span>PDF File</span>
						</div>`
					})
				}
					

				i.pages.forEach(function(a, n){						
					self.total_page+=1
					pages.push({
							id: a.id,
							book_chapter_id: a.book_chapter_id,
							content: a.content
					})
				})
			})

			self.is_spine_allowed = false
			if(self.total_page > 24)
				self.is_spine_allowed = true

			self.total_page += 2

			self.spineCalculatedWidth = parseInt(self.total_page) / 2 * 0.0144 + 0.5
			self.spineCalculatedWidth += 0.1

			if(self.book.has_back_cover)
				pages.push({
					id: null,
					is_cover: true,
					content: `url("${self.book.back_cover}")`
			})

			self.book.total_page = self.total_page

			self.previewBook.pages = pages
		},

		callPrintDialog(){
			let self = this
			const h = self.$createElement;
			self.$msgbox({
			  showCancelButton: true,
			  confirmButtonText: 'OK',
			  cancelButtonText: 'Cancel',
			  type: 'warning',
			  center: true,
			  title: 'Warning',
			  message: h('p', null, [
				h('span', null, 'Are you sure you want to print this book?'),
				h('br'),
				h('span', { style: 'font-size: 10px' }, [
					h('font', { style: 'color: red' }, 'Notes: '),
					h('font', null,'After printing you will redirect to homepage.')
				])
			  ]),
			}).then(() => {
				self.printBook.id = self.book.id
				self.openPrintBookCalculation = true
			}).catch(() =>{
				self.$notify.info({
					type: 'info',
					message: 'Action has been canceled'
				})
			})
		},
		
		onSaveAndPrint(formName){
			let self = this
			const h = self.$createElement;
			let confirmMsg = h('p', null, [
				h('span', null, 'Are you sure you want to save these changes?'),
				h('br'),
				h('span', { style: 'font-size: 10px' }, [
					h('font', { style: 'color: red' }, 'Note: '),
					h('font', null,'after publishing, you won\'t be able to edit this book')
				])
			])

			let successMsg = 'Successfully save changes and open print book dialog'
			
			if(self.book.id && !self.book.is_save_as_draft){
				self.callPrintDialog()
				return false
			}

			self.save(formName, confirmMsg, successMsg, 'saveandprint')
		},

		onSaveAsDraft(formName){
			let self = this
			let confirmMsg = 'Are you sure you want to save these changes as draft?'
			let successMsg = 'Successfully save changes as draft'
			
			if(self.book.id && !self.book.is_save_as_draft)
				return false
			
			self.save(formName, confirmMsg, successMsg, 'saveasdraft')
		},

		onSave(formName){
			let self = this

			const h = self.$createElement;
			let confirmMsg = h('p', null, [
				h('span', null, 'Are you sure you want to save these changes?'),
				h('br'),
				h('span', { style: 'font-size: 10px' }, [
					h('font', { style: 'color: red' }, 'Note: '),
					h('font', null,'after publishing, you won\'t be able to edit this book')
				])
			])

			let successMsg = 'Successfully save changes'
			
			if(self.book.id && !self.book.is_save_as_draft)
				return false

			self.save(formName, confirmMsg, successMsg, 'finish')
		},

		handleClose(tag) {
			this.book.tags.splice(this.book.tags.indexOf(tag), 1);
		},

		showInput() {
			this.inputVisible = true;
			this.$nextTick(_ => {
				this.$refs.saveTagInput.$refs.input.focus();
			});
		},

		handleInputConfirm(e) {
			let inputValue = this.inputTag;
			if (inputValue) {
				let status = this.book.tags.find( ( value ) => value === inputValue )
				if(!status)
					this.book.tags.push(inputValue)
			}

			if(e.type == 'blur'){
				this.inputVisible = false;
			}
			
			this.inputTag = '';
		},

		handlePreviewStory(data){
			let self = this

			if(data.is_file_type){
				self.selectedPreviewStory = data
				self.previewStory = true
				return false
			}

			if(typeof data.pages[0] == 'undefined'){
				self.selectedPreviewStory = {}
				self.previewStoryContent = ''
				self.previewStory = false
				return false
			}

			self.selectedPreviewStory = data
			self.previewStoryContent = data.pages[0].content
			self.previewStory = true
		},

		getStoryByDate(param){
			let self = this
			axios.post('/story/list', param)
			.then(response=>{
				self.availableStories = response.data.story
			})
			.catch(err=>{
				self.$notify.error({
					title: 'Error',
					message: err.message
				})
			})
		},

		getFilterCalendar(dd){
			// console.log('getFilterCalendar')

			let self = this
			if(!dd)
				return;

			this.selectedRange = ''

			Object.keys(self.filterByMonths).forEach(function(i){
				let mm = parseInt(i) + 1
				let getLastDay = new Date(dd, mm, 0)
				self.filterByMonths[i].range = []
				self.filterByMonths[i].range.push([
					dd+'-'+mm+'-1',
					dd+'-'+mm+'-'+getLastDay.getDate()
				])
			})
		},

		onChangePage(data){
			this.currentCalendar = data
		},

		setActiveItem(index) {
			this.$refs.carousel.setActiveItem(index);
		},

		// onChangePagePreview(data){
		// 		this.currentPage = data
		// },

		// setActiveItemPreview(index) {
		//     if(index < this.previewBook.pages.length && index > -1){
		//         this.$refs.carousel_preview.setActiveItem(index);
		//     }
		// },

		// showAdditionalInfo(){
		// 	if(this.event == 'close'){
		// 		this.event = 'open'
		// 		return false;
		// 	}

		// 	this.event = 'close'
		// },

		getStories(param = ''){
			let self = this
			axios.post('/story/list', param)
			.then(response=>{
				self.stories = response.data.story
				let dates = []

				Object.keys(self.stories).forEach(function(i){
					let newDate = self.stories[i].story_date
					if(!newDate)
						newDate = self.stories[i].from_date
					newDate = newDate.split('-')
					if(!dates.includes(newDate[0]))
						dates.push(newDate[0])
				})

				dates.sort()
				for(let i = 0; i < dates.length; i++){
					self.availableYear.push(dates[i])
				}
				
				// self.availableYear.push(new Date().getFullYear().toString())
				self.availableYear.reverse()
				// console.log(self.availableYear)
				self.selectedYear = self.availableYear[0]
				setTimeout(()=>{
					self.handleMountedRange()
				}, 1000)

			})
			.catch(err=>{
				self.$notify.error({
					title: 'Error',
					message: err.message
				})
			})
		},

		save(formName, confirmMsg, successMsg, action){
			let self = this

			self.$refs[formName].validate((valid) => {
				if (valid) {
					self.$msgbox({
					  showCancelButton: true,
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel',
					  type: 'warning',
					  center: true,
					  title: 'Warning',
					  message: confirmMsg,
					}).then(() => {
						self.fullScreenLoading = true
						let actionAxios = ''
						if(typeof self.book.id != 'undefined'){
							actionAxios = axios.put('/book/'+self.book.id, self.book)
						}else
							actionAxios = axios.post('/book', self.book)

						// console.log(action != 'saveasdraft')
						if(action != 'saveasdraft')
							self.book.is_save_as_draft = false

						actionAxios
						.then(response =>{
							self.book = response.data.book

							setTimeout(() => {

								if(typeof self.$refs.uploadFrontCover != 'undefined')
									self.$refs.uploadFrontCover.submit()
								if(typeof self.$refs.uploadSpineCover != 'undefined')
									self.$refs.uploadSpineCover.submit()
								if(typeof self.$refs.uploadBackCover != 'undefined')
									self.$refs.uploadBackCover.submit()

								self.$notify({
								  title: 'Success',
								  message: successMsg,
								  type: 'success'
								})

								self.fullScreenLoading = false

								if(action == 'finish'){
									setTimeout(function(){
										window.location.href = '/'
									}, 5000)
								}else
									history.pushState(null, '', '/book/'+self.book.id+'/edit')

								if(action == 'saveandprint'){
									self.callPrintDialog()
								}

							}, 2000)
						})
						.catch(err =>{
							self.book.is_save_as_draft = true
							self.fullScreenLoading = false
							Object.keys(err.response.data.errors).forEach(function(value, index){
								self.$notify.error({
								  title: 'Error',
								  message: err.response.data.errors[value][index]
								})
							})
						})
					}).catch(() => {
						self.fullScreenLoading = false
						self.$notify.info({
							type: 'info',
							message: 'Action has been canceled'
						})
					})
				} else {
					self.$notify.error({
						title: 'Error',
						message: 'Please fill-up the required fields!'
					})
					return false;
				}
			})
		},
		handleBookChaptersContainer(evt){
			let self = this
			let from = ''
			let to = ''

			self.book.tags = []
			self.book.shared_to = []

			self.book.chapters.forEach(function (i) {
				if(typeof i != 'undefined'){

					if(!self.book.from_date && typeof i.from_date != 'undefined' 
						&& i.from_date < self.book.from_date 
						|| !self.book.from_date)
						self.book.from_date = i.from_date

					if(!self.book.to_date && typeof i.to_date != 'undefined' 
						&& i.to_date > self.book.to_date 
						|| !self.book.to_date)
						self.book.to_date = i.to_date

					let tags = typeof i.tags != 'undefined'?i.tags.split(','):[]
					tags.forEach( (tag) => {
						let status = self.book.tags.find( ( value ) => value === tag )
						if(tag != '' && !status)
							self.book.tags.push(tag)
					})

					let shared_to = typeof i.shared_to != 'undefined'?i.shared_to:[]
					shared_to.forEach( (data) => {
						let status = self.book.shared_to.find( ({ email }) => email === data.email )
						if(data != '' && !status)
							self.book.shared_to.push({
								id: data.id,
								email: data.email,
								is_allow_edit: data.is_allow_edit,
							})
					})
				}
			})

			this.addTableOfContent(this.book.chapters)
			this.handlePreviewBook()
		}
	},
	watch:{
		availableStories(){
			let self = this
			self.isPreviewBook = false
			self.availableStories.forEach((story, index)=>{

				let status = self.book.chapters.find((chapter)=>{
					if(chapter.object_type == 'stories' && story.id == chapter.id)
						return true

					if(typeof chapter.object_type == 'undefined' && story.id == chapter.story_id)
						return true

					return false
				})

				if(status)
					self.availableStories.splice(index, 1)
			})
		},
		'book.original_price'(){
			let times = this.book.original_price * this.book.markup_price
			let devide = times / 100
			let plus = this.book.original_price + devide

			this.book.price = plus
			this.book.ebook_price = plus + this.book.ebook_markup_price
		},
		selectedYear(){
			this.getFilterCalendar(this.selectedYear.toString())
		},
		selectedRange(){
			this.availableStories = []
			this.selectedPreviewStory = {}
			this.previewStoryContent = ''
			this.previewStory = false
			this.getStoryByDate({ date: this.selectedRange })
		},
		'book.status_type'() {
			// this.book.original_price = 0.00
		},
	},
}