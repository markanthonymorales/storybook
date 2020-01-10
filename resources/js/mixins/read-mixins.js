import pdf from 'vue-pdf'

export default {
	props: ['readStory', 'typeData', 'isPreview'],
	components: {
        pdf,
    },
	data(){
		return {
			mobile: false,
			noArrow: 'never',
			story: {},
			currentPage: 1,
			totalPage: 0,
			event: "close",
			readNext: null,
			readPrev: null,
			is_preview: this.isPreview?true:false,
		}
	},
	created(){
		let self = this

		self.mobile = self.isMobile()
		self.noArrow = self.isMobile()?'always':'never'
		self.story = self.readStory

		if(!self.story.is_file_type)
			self.totalPage = self.story.pages.length

		if(self.typeData == 'books'){
			self.story.pages.forEach((page, index)=>{
				if(index < 2 && !page.id && !page.is_cover){
					let nodeElem = document.createElement('div')
					nodeElem.innerHTML = page.content
					// console.log(nodeElem)

					let div = nodeElem.children[0].children
					let header = div[0].cloneNode(true)
					let body = div[1].cloneNode(true)
					// console.log(header)
					// console.log(body)

					let tbodies = []
					let maxRow = 16
					let totalMaxRow = 0
					for(let i = 0; i < body.rows.length; i++){
						if(i < 1)
							tbodies[totalMaxRow] = document.createElement('tbody')

						// if( i > 0 && i % maxRow - 1 === 0){ // test only
						if( i > 0 && i % maxRow === 0){
							totalMaxRow++
							tbodies[totalMaxRow] = document.createElement('tbody')
						}

						tbodies[totalMaxRow].appendChild(body.rows[i].cloneNode(true))
					}

					let tblContent = document.createElement('div')
					tblContent.classList.add('table-of-content')
					tblContent.appendChild(header.cloneNode(true))
					// console.log(tbodies)

					tbodies.forEach((tbody, index2) => {
						if(index2 > 0)
							tblContent.innerHTML = ''

						let table = document.createElement('table')
						table.appendChild(tbody.cloneNode(true))
						tblContent.appendChild(table)

						let newIndex = index

						if(index2 > 0){
							newIndex = newIndex + index2
							self.story.pages.splice(newIndex, 0, {
								id: 0,
								is_cover: false,
								content: tblContent.outerHTML
							})
						}else{
							self.story.pages[newIndex] = {
								id: 0,
								is_cover: false,
								content: tblContent.outerHTML
							}
						}
					})
				}
			})
		}

		if(!this.is_preview)
			self.getReadNextAndPrev()

		window.addEventListener('resize', function() {
			self.mobile = self.isMobile()
		})
	},
	methods: {
		getReadNextAndPrev(){
			let self = this
			axios.get('/read/pagination/'+self.story.id+'/'+self.typeData).then(res => {
				self.readNext = res.data.next
				self.readPrev = res.data.prev
			}).catch(err => {
				console.log(err)
			})
		},
		onChangePage(data){
			this.currentPage = data
		},
		setActiveItem(index) {
			if(this.story.is_file_type)
				if(index > 0 && index <= this.totalPage)
					this.currentPage = index

			if(index < this.story.pages.length && index > -1){
				if(!this.story.is_file_type)
					this.$refs.carousel.setActiveItem(index);
			}
		},
		showAdditionalInfo(){
			if(this.event == 'close'){
				this.event = 'open'
			}else
				this.event = 'close'
		}
	}
}