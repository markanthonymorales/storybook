import { app_data } from '../data/data.js'

export default {
	data(){
		return {
			...app_data,
			headerInfo: {
		        'Accept': 'application/json',
		        'X-CSRF-TOKEN': window.axios.defaults.headers.common['X-CSRF-TOKEN']
		    },
		}
	},
	methods: {
		async UrlExists(url, callback)
		{
			try {
				const res = await axios.get(url);
				return callback(null, res.data);
			} catch (e) {
				return callback(e, null);
			}
		},
		isCoverExists(url){
			return axios.get(url)
		},
		isMobile() {
			if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || 
			navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) ||
			navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) ||
			navigator.userAgent.match(/Windows Phone/i))
				return true
			
			return false
		},
		getLastView(){
			axios.get('/user/recently/view')
			.then(response=>{
			if(response.data.id && response.data.name){
				Event.$emit('recent', response.data.id +`/`+response.data.name)
			}
			})
			.catch(err=>{
				console.log(err.message)
			})
		},
		// Call get to cart function
		getToCart(){
			let self = this
			
			self.fullScreenLoading = true
			axios.get('/cart/1')
			.then(response=>{
				Event.$emit('cart', response.data.cart)
				self.fullScreenLoading = false
			})
			.catch(err=>{
				console.log(err.message)
				self.fullScreenLoading = false
			})
		},

		// Call add to cart function
		addToCart(id){
			let self = this
			
			self.fullScreenLoading = true

			axios.post('/cart', { id: id})
			.then(response=>{
				if(response.data.success){
					Event.$emit('cart', response.data.cart)
					self.$notify({
					title: 'Success',
					message: 'Successfully add new item to cart',
					type: 'success'
					})
				}else{
					self.$notify.error({
					title: 'Error',
					message: response.data.error
					})
				}
				self.fullScreenLoading = false
			})
			.catch(err=>{
				self.$notify.error({
				title: 'Error',
				message: err.message
				})
				self.fullScreenLoading = false
			})
		},
	}
}
