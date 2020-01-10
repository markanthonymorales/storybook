import ElUploadPDF from '../components/options/upload-pdf.vue'
import pdf from 'vue-pdf'

export default {
	components: {
		pdf,
		'el-upload-pdf': ElUploadPDF,
	},
	data(){
		return {
			currentPDFPage: 1,
			totalPDFPages: 0,
		}
	},
	methods: {
		handlePDFThumbnail(index){
			this.currentPDFPage = index
		},
		handlePDFChange(totalPage){
			this.totalPDFPages = totalPage
		},
		handleResetPDF(){
			Event.$emit('resetPDF', '')
		},
	},
}