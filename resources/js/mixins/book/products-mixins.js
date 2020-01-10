import { data } from '../../data/product-data.js'
import pdfMixins from '../pdf-mixins.js'

export default {
	name: 'ElProducts',
	props: {
		onRefresh: String,
        searchData: String,
		filterData: String,
        authData: Number
	},
    mixins: [pdfMixins],
	provide() {
	    return {
	    	elProducts: this
	    };
	},
    data () {
        return {
            ...data,
            maxPreviewThumbnail: 5,
        }
    },
    created(){
        let self = this
        self.handleSearch()
        self.mobile = self.isMobile()
        self.authId = self.authData
    },
	methods: {
        handleBookListCurrentChange(page_no){
            let self = this
            self.availableBooks.current_page = page_no

            // if visible story is equal to zero
            if(self.availableBooks.list.length == 0 && self.availableBooks.current_page > 0)
                self.availableBooks.current_page = 1

            self.handleSearch()
        },

        handleSearch(){
            let self = this
            self.loading = true
            axios.post('/book/publish', {
                title: self.searchData,
                filter: self.filterData,
                page_size: self.availableBooks.page_size,
                current_page: self.availableBooks.current_page,
            })
            .then(response=>{
                self.availableBooks.list = response.data.books
                self.availableBooks.total = response.data.total
                self.loading = false
            })
            .catch(err=>{
                console.log(err.message)
                self.loading = false
            })
        },

        handlePreview(book){
            let self = this
            self.selectedPreview = book
            self.selectedPreview.file_url = '/storage/pdf/'+self.selectedPreview.id+'/'+self.selectedPreview.title+'.pdf'

            let index = 0
            let data = book.cover
            if(!book.cover){
                data = ''
            }

            self.previewFrame = ''

            self.openCover = true

            self.handlePreviewFrame(data, index)
        },

        handlePreviewFrame(str, i, file_type){
            let self = this
            let frame = ''
            const container = document.querySelector('div.storybook-left')
            
            if(container)
                container.firstChild.classList.remove('big-paper')

            if(!i){
                frame = `<div class="el-image w-full"><div class="image-slot" style="align-items: center; justify-content: center; height: -webkit-fill-available; display: flex;"><img src="img/Icon-default.png" class="img-error"></div></div>`

                if(str)
                    frame = `<img src="${str}" class="cover-img" >`
            }else{
                if(container)
                    container.firstChild.classList.add('big-paper')

                if(file_type){
                    frame = `<div class="pdf-watermark"><span>PDF File</span></div>`
                }
                else
                    frame = `<div class="container" >${str}</div>`
            }
            self.previewFrame = frame
        },
	},
    watch: {
        onRefresh(){
            if(this.onRefresh){
                this.handleSearch()
            }
        },
    },
}