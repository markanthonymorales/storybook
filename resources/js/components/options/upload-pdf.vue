<template>
	<div class="pdf-upload" v-loading="loading">
		<el-row v-if="!file_url" type="flex">
			  <!-- :show-file-list="false" -->
			<el-upload
			  class="avatar-uploader"
			  ref="uploadPDF"
			  action="/story/add/pdf"
	          :headers="headerInfo"
			  :on-success="handlePDFSuccess"
			  :before-upload="beforePDFUpload">
				<i class="el-icon-plus avatar-uploader-icon"></i>
				<div class="el-upload__tip" slot="tip">PDF file only</div>
			</el-upload>
		</el-row>
		<el-row	v-else >
			<pdf :src="file_url"
			style="width:100%; border: 1px solid #e2e2e2 !important; margin: 0 0 15px;"
			:page="page" 
			@page-loaded="handlePDFLoaded"
      		@num-pages="numPages = $event"></pdf>
		</el-row>
	</div>
</template>
<style>
	.avatar-uploader {
		width: -webkit-fill-available;
	}
	.avatar-uploader .el-upload {
		border: 1px dashed #d9d9d9;
		border-radius: 6px;
		cursor: pointer;
		position: relative;
		overflow: hidden;
		width: -webkit-fill-available;
	    height: auto;
	}
	.avatar-uploader .el-upload:hover {
		border-color: #409EFF;
	}
	.avatar-uploader-icon {
		font-size: 28px;
		color: #8c939d;
		width: -webkit-fill-available;
		height: 178px;
		line-height: 178px;
		text-align: center;
	}
	.avatar {
		width: -webkit-fill-available;
		height: 178px;
		display: block;
	}
</style>
<script>
	import pdf from 'vue-pdf'

	export default {
		name: 'ElUploadPdf',
		props: {
			selectedPage: Number,
			value: String,
		},
		components: {
			pdf,
		},
		data () {
			return {
				loading: false,
				file_url: '',
				page: 1,
				numPages: 0,
			}
		},
		created(){
			Event.$on('resetPDF', url => {
	            this.file_url = url
	            this.page = 1
	            this.numPages = 0
	            this.$emit('input', this.file_url)
	            this.$emit('change', this.numPages)
	        })
		},
		mounted(){
			this.file_url = this.value
			this.page = this.selectedPage
		},
		methods: {
			handlePDFSuccess(res, file) {
				// console.log(res, file)
				this.file_url = URL.createObjectURL(file.raw);
				this.$emit('input', res.url)
			},
			beforePDFUpload(file) {
				const isPDF = file.type === 'application/pdf'
				// const isLt2M = file.size / 1024 / 1024 < 2;

				if (!isPDF) {
					this.$message.error('File must be PDF format!')
				}

				// if (!isLt2M) {
				// 	this.$message.error('File size can not exceed 2MB!')
				// }

				// return isPDF && isLt2M
				return isPDF
			},
			handlePDFLoaded(){
				setTimeout(()=>{
					this.$emit('change', this.numPages)
				}, 1000)
			}
		},
		watch:{
			selectedPage(){
				this.page = this.selectedPage
			},
		}
	}
</script>