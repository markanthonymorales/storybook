import plainTextToHtml from '@ckeditor/ckeditor5-clipboard/src/utils/plaintexttohtml'

import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document'
import { data } from '../data/write-data.js'
import { wordProcess2, splitContent } from '../modules/wordprocess.js'
import { DecoupEditConfig } from '../modules/global-decoupled-editor.js'

import pdfMixins from './pdf-mixins.js'

export default {
	props: ['userId', 'storyData'],
	mixins: [pdfMixins],
	data(){
		const checkFromDate = (rule, value, callback) => {
			if (!value) {
				return callback(new Error('Please pick a date'));
			}
			setTimeout(() => {
				if (value > this.story.to_date) {
					callback(new Error('Date must be lesser than or equal to date "To"'));
				} else {
					callback();
				}
			}, 500);
		};

		const checkToDate = (rule, value, callback) => {
			if (!value) {
				return callback(new Error('Please pick a date'));
			}
			setTimeout(() => {
				if (value < this.story.from_date) {
					callback(new Error('Date must be greater than or equal to date "From"'));
				} else {
					callback();
				}
			}, 500);
		};

		return {
			...data,
			colors: [],
			colored_index: '',
			gPosition: {},
			storyRules: {
				title: [
					{ required: true, message: 'Please input Story Title name', trigger: 'blur' }
				],
				from_date: [
					{ validator: checkFromDate, trigger: 'change' }
				],
				to_date: [
					{ validator: checkToDate, trigger: 'change' }
				],
			},
		}
	},
	created(){
		let self = this

		self.user_id = self.userId
		self.mobile = self.isMobile()

		self.story = self.storyData

		for(let i = 0; i < self.story.total_page; i++){
			self.colors[i] = false
		}

		if(self.story.colored_index == ''){
			let str = ''
			self.story.pages.forEach((page, i)=>{
				if(page.is_colored){
					self.colors[i] = true
					str = str.concat((i+1)+',')
				}
			})
			self.story.colored_index = str
		}else{
			if(typeof self.story.colored_index != 'undefined' && self.story.colored_index.length > 0){
				let colored = self.story.colored_index
				colored = colored.split(',')
				colored.forEach((n, i)=>{
					self.colors[n-1] = true
				})
			}
		}
			

		self.story.colored_index = self.story.colored_index > '0'?self.story.colored_index:''
		self.colored_index = self.story.colored_index > '0'?self.story.colored_index:''

		self.convertIntTypeToBool()
	},
	mounted(){
		let self = this

		if(!self.story.is_file_type)
			self.onReady()
	},
	methods: {
		handlePDFColorIndex(i){
			let self = this
			let str = self.colored_index
			let bool = false
			let data = i+','

			if(!str.includes(data)){
				self.colored_index = str.concat(data)
				bool = true
			}else{
				self.colored_index = str.replace(data,'')
				bool = false
			}
			
			self.story.colored_index = self.colored_index

			if(typeof self.story.pages[i-1] != 'undefined')
				self.story.pages[i-1].is_colored = bool
		},
		convertIntTypeToBool(){
			let self = this
			self.story.pages.forEach((v, i) => {
				v.is_colored = v.is_colored?true:false
			})
		},
		backToCustomWrite(){
			this.$confirm('Are you sure you want to use custom write story?', 'Warning', {
			  confirmButtonText: 'OK',
			  cancelButtonText: 'Cancel',
			  type: 'warning',
			  center: true
			}).then(() => {
				this.story.is_file_type = false
				this.story.file_url = ''
				this.story.pages = []
				setTimeout(()=>{
					this.onReady()
				}, 500)
			}).catch(err => {
				this.$notify.info({
					type: 'info',
					message: 'Action has been canceled'
				})
			})
		},
		addUploadButton(){
			if(this.story.id && !this.story.is_file_type)
				return false


			var spn = document.createElement('span')
			spn.innerText = 'Used PDF Upload'

			var icn = document.createElement('i')
			icn.classList.add('el-icon-upload')

			var btn = document.createElement('button')

			btn.classList.add('button-red', 'el-button', 'float-right', 'ml-auto')
			btn.setAttribute('type', 'button')
			btn.addEventListener('click', () => {
				this.$confirm('Are you sure you want to use upload pdf story?', 'Warning', {
				  confirmButtonText: 'OK',
				  cancelButtonText: 'Cancel',
				  type: 'warning',
				  center: true
				}).then(() => {
					this.story.is_file_type = true
					this.story.pages = []
					this.editorData.destroy()
				}).catch(err => {
					this.$notify.info({
						type: 'info',
						message: 'Action has been canceled'
					})
				})
			})
			btn.appendChild(icn)
			btn.appendChild(spn)

			return btn
		},
		onReady(){
			let self = this

			self.fullScreenLoading = true

			DecoupledEditor
			.create( document.querySelector( '#editor' ), DecoupEditConfig('/story/upload/'+self.story.user_id, 'write'))
			.then( editor => {

				editor.isReadOnly = true

				const toolbarContainer = document.querySelector( '#toolbar-container' )
				toolbarContainer.innerHTML = ''

				let btn = self.addUploadButton()

				if(btn)
					editor.ui.view.toolbar.element.appendChild(btn)

				toolbarContainer.appendChild( editor.ui.view.toolbar.element )

				// clipboardInput
				const clipboardPlugin = editor.plugins.get( 'Clipboard' );
				const editingView = editor.editing.view;

				editingView.document.on( 'clipboardInput', ( evt, data ) => {
					const dataTransfer = data.dataTransfer;

					let content = plainTextToHtml( dataTransfer.getData( 'text/plain' ) );

					let newElem = document.createElement('div')

					if(!content.includes('<p>'))
						content = `<p>${content}</p>`

					newElem.innerHTML = content

					splitContent(newElem).then(res => {
						content = res
						content = clipboardPlugin._htmlDataProcessor.toView( content );
						clipboardPlugin.fire( 'inputTransformation', { content, dataTransfer } );
					})

					editingView.scrollToTheSelection();
					evt.stop();
				});
				// clipboardInput

				editor.keystrokes.set('Ctrl+V', (evt, cancel) => {
					self.isTyping = true
				})

				editor.editing.view.document.on('paste', (evt, data) => {
					self.isTyping = true
				})

				editor.plugins.get('PendingActions').on( 'change:hasAny', (evt, action, inprogress, done) => {
					if(done)
						self.isTyping = true
				});

				let enterKeyCode = 13
				editor.editing.view.document.on('keydown', (evt, data) => {
					if (data.keyCode == enterKeyCode)
						self.isTyping = true
				});

				self.editorData = editor

				self.editorData.model.document.on( 'change:data', function(data){
					self.onChangeData(self.editorData.getData())
				})

				if(Object.keys(self.storyData.pages).length > 0){
					self.currentPage = 0;
					self.onOpenPage(self.currentPage)
				}
				self.fullScreenLoading = false
				
			})
			.catch( error => {
				console.error( error )
				self.fullScreenLoading = false
			})
		},

		handleSelect(item) {
			this.holdInputShare = item.link;
		},

		handleClose(tag) {
			this.story.search_tags.splice(this.story.search_tags.indexOf(tag), 1);
		},

		handleInputConfirm(e) {
			let inputValue = this.inputValue;
			
			if (inputValue) {
				let status = this.story.search_tags.find( ( value ) => value === inputValue )
				if(!status)
					this.story.search_tags.push(inputValue)
			}

			if(e.type == 'blur')
				this.inputVisible = false;

			this.inputValue = '';
		},

		querySearch(queryString, cb) {
			let self = this
			axios.get('/search/email/'+queryString)
			.then(response=>{
				var users = []

				response.data.find(({id, email}) => {
					if(email !== self.searchEmail)
						users.push({link: id, value: email})
				})
				
				var results = queryString ? users.filter(self.createFilter(queryString)) : users
				cb(results)
			})
			.catch(err=>{
				self.$notify.error({
				  title: 'Error',
				  message: err.message
				})
			})
		},
		

		onAddNewPage(){
			let self = this
			self.story.pages.push({
				'id': null,
				'is_colored': 0,
				'content': '',
				// 'page_type': 'custom',
			})
			self.currentPage = null
			self.editorData.setData('')
			self.editorData.isReadOnly = true
			if(self.story.pages.length > 0){
				self.currentPage = self.story.pages.length - 1
				self.editorData.isReadOnly = false
				self.editorData.editing.view.focus()
			}
		},

		onOpenPage(index){
			let self = this
			self.editorData.isReadOnly = false
			self.currentPage = index
			if(typeof self.story.pages[self.currentPage] != 'undefined'){
				self.editorData.setData(self.story.pages[self.currentPage].content)
			}else{
				self.onAddNewPage()
			}
			self.editorData.editing.view.focus()
		},

		onSubmitStory(formName){
			let self = this
			self.$refs[formName].validate((valid) => {
				if (valid) {
					self.$confirm('Are you sure you want to save these story?', 'Warning', {
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel',
					  type: 'warning',
					  center: true
					}).then(() => {
						self.story.total_page = self.story.is_file_type ? self.totalPDFPages : self.story.pages.length
						self.fullScreenLoading = true
						if(!self.story.id){
							axios.post('/write', self.story)
							.then(response=>{
								self.story = response.data.story
								history.pushState(null, '', '/write/'+self.story.id+'/edit')
								self.convertIntTypeToBool()
								self.getLastView()

								self.fullScreenLoading = false

								self.$notify.success({
								  title: 'Success',
								  message: 'Successfully save changes'
								})
							})
							.catch(err=>{   
								self.fullScreenLoading = false
								
								Object.keys(err.response.data.errors).forEach(function(value, index){
									self.$notify.error({
									  title: 'Error',
									  message: err.response.data.errors[value][index]
									})
								})
							})
						}else{
							axios.put('/write/'+self.story.id, self.story)
							.then(response=>{
								self.story = response.data.story
								self.convertIntTypeToBool()
								self.getLastView()
								
								self.fullScreenLoading = false
								self.$notify.success({
								  title: 'Success',
								  message: 'Successfully update changes'
								})
							})
							.catch(err=>{   
								self.fullScreenLoading = false

								Object.keys(err.response.data.errors).forEach(function(value, index){
									self.$notify.error({
									  title: 'Error',
									  message: err.response.data.errors[value][index]
									})
								})
							})
						}
					}).catch(() => {
					  self.$notify.info({
						type: 'info',
						message: 'Action has been canceled'
					  })
					})
				} else {
					self.$notify.error({
						type: 'Error',
						message: 'Please fill-up the required fields'
					})
					return false;
				}
			});
		},

		onDeletePage(index){
			let self = this
			self.$confirm('Are you sure you want to delete these page?', 'Warning', {
			  confirmButtonText: 'OK',
			  cancelButtonText: 'Cancel',
			  type: 'warning',
			  center: true
			}).then(() => {
				if(self.story.pages[index].id != null){
					axios.get('/story/page/delete/'+self.story.pages[index].id)
					.then(response=>{
						if(self.currentPage == index){
							self.editorData.setData('')
							self.currentPage = null
							self.editorData.isReadOnly = true
						}
						self.story.pages.splice(index, 1)
						self.$notify({
						  title: 'Success',
						  message: 'Successfully Delete Page',
						  type: 'success'
						})
					})
					.catch(err=>{
						self.$notify.error({
						  title: 'Error',
						  message: err.message
						})
						console.log(err)
					})
				}else{
					if(self.currentPage == index){
						self.editorData.setData('')
						self.currentPage = null
						self.editorData.isReadOnly = true
					}
					self.story.pages.splice(index, 1)
					self.$notify({
					  title: 'Success',
					  message: 'Successfully Delete Page',
					  type: 'success'
					})
				}
			}).catch(() => {
			  self.$notify.info({
				type: 'info',
				message: 'Action has been canceled'
			  })
			})
		},

		emailIsValid(email){
			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
		},

		onAddSharedTo(){
			let self = this
			if(!self.emailIsValid(self.searchEmail))
				self.$notify.error({
				  title: 'Error',
				  message: 'The email is invalid.'
				})

			if(self.emailIsValid(self.searchEmail))
				if(self.searchEmail){
					let isExists = false
					let length = self.story.shared_to.length
					for(let i = 0; i < length; i++){
						if(self.searchEmail == self.story.shared_to[i].email){
							isExists = true
							break
						}
					}

					if(!isExists){
						self.story.shared_to.push({
							id: self.holdInputShare,
							email: self.searchEmail,
							is_allow_edit: false,
						})
					}else{
						self.$notify.error({
						  title: 'Error',
						  message: 'The email is already exists.'
						})
					}
				}
			
			self.holdInputShare = ''
			self.searchEmail = ''
		},
		removeUserEmailFromSharedStory(i){
			let self = this
			if(self.userId == self.story.user_id){
				self.story.shared_to.splice(i, 1)
			}else{
				self.$notify.error({
				  title: 'Error',
				  message: 'Only the owner of this story can removed member.'
				})
			}
		},

		showInput() {
			this.inputVisible = true;
			this.$nextTick(_ => {
				this.$refs.saveTagInput.$refs.input.focus();
			});
		},

		createFilter(queryString) {
			return (users) => {
				return (users.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
			};
		},

		onChangeData(data){
			let self = this
			if(self.currentPage != null){
				self.holdEditorData = data
				self.story.pages.splice(self.currentPage, 1, {
					'id': typeof self.story.pages[self.currentPage].id != 'undefined'?self.story.pages[self.currentPage].id:null,
					'is_colored': typeof self.story.pages[self.currentPage].id != 'undefined'?self.story.pages[self.currentPage].is_colored:0,
					'content': data
				})
			}
		},

		checkFromAndTo(evt){
			this.$refs.storyForm.validateField('from_date')
			this.$refs.storyForm.validateField('to_date')
		}
	},
	watch: {
		holdEditorData: _.debounce( function() {
			this.isTyping = false
		}, 1000),
		async isTyping(value){
			let self = this
			// get current cursor position

			if(!value){
				// alert('Word Process: Start!')
				console.clear()
				console.log('%cWord Process: %cStart', 'color: #fff; font-size: 18px;', 'color: #ffed4a; font-size: 18px;', )
				
				// get position of cursor
				const position = self.editorData.model.document.selection.getFirstPosition()

				// get new position parent element of cursor to old page
				let parent = position.parent

				// run wordprocessor
				const getResult = await wordProcess2(self, self.currentPage)

				// set the new content of old page to editor
				self.editorData.setData(self.story.pages[self.currentPage].content)

				
				// get maximum child of old editor
				let maxPosition = position.root.childCount < 31 ? position.root.childCount : 31

				// get paths of old position
				let path = position.path

				if(position.root.childCount < 31)
					if(path[0] == maxPosition)
						path[0] += 1

				// get total page newly generated
				let getPageIndex = Math.round(path[0] / maxPosition)

				// get total lines
				let getNodeIndex = path[0] % maxPosition

				if(path[0] % maxPosition === 0)
					if(getNodeIndex < 1)
						getNodeIndex = path[0]

				// compare old cursor position to max position of old editor
				if(path[0] > maxPosition){

					// get new page id
					self.currentPage = self.currentPage + getPageIndex

					// set the new content of current page to editor
					self.editorData.setData(self.story.pages[self.currentPage].content)
				}

				console.log(getNodeIndex, ' != ', path[0], 'max:'+maxPosition)
				if(getNodeIndex != path[0]){

					// get new position of cursor to new page
					const position2 = self.editorData.model.document.selection.getFirstPosition()

					// get child element line of new editor
					parent = position2.root._children._nodes[getNodeIndex - 1]
					// console.log(position2.root._children._nodes)

				}else // get child element line of old editor
					parent = position.parent
				

				// move the cursor to the same position of last edit
				self.editorData.model.change( writer => {
					writer.setSelection( 
						parent, 
						path[1] 
					)
				})

				console.log('%cWord Process: %cDone', 'color: #fff; font-size: 18px;', 'color: #38c172; font-size: 18px;', )
				// alert('Word Process: Done!')
			}
		},
	},
}