import SimpleuploadPlugin from 'ckeditor5-simple-upload/src/simpleupload';
// import { CustomButtonPlugin } from './custombutton';


export const DecoupEditConfig = (url, page = 'profile') => {
	try{
		let toolbar = [
			'fontfamily',
	        '|',
	        'fontsize',
	        'bold',
	        'italic',
	        'underline',
	        '|',
	        'highlight',
	        'alignment',
	        '|',
	        'imageUpload',
	        // 'customButton'
		]

		// let index = toolbar.indexOf('customButton');
		// if(page != 'write')
		// 	toolbar.splice(index, 1)

		return {
			allowedContent: 'p',
			autoGrow_maxHeight: 400,
		    toolbar: toolbar,
		    language: 'en',
		    extraPlugins: [
		        SimpleuploadPlugin,
		        // CustomButtonPlugin,
		    ],
		    removePlugins: [ 'ButtonView' ],
		    simpleUpload: {
		        uploadUrl: {
		            url: url, 
		            headers:{ 
		                'Accept': 'application/json',
		                'X-CSRF-TOKEN': window.axios.defaults.headers.common['X-CSRF-TOKEN'],
		            } 
		        }
		    },
		}
	}catch(err){
		console.log(err)
		return {};
	}
}