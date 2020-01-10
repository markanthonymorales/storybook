import { printBookData } from '../../data/print-book-data.js'

export default {
	name: 'ElCalculateBook',
	props: {
		idData: Number,
	},
	data () {
		return {
			...printBookData,
			bookData: {},
			userData: {},
			formatTypeData: [],
			attributeData: [],
			addressData: [],
		}
	},
	created(){
		let self = this
		self.getBook(self.idData)
		self.getData(self.idData)

		self.cleanData()
	},
	methods: {
		getBook(id){
			let self = this
			axios.get('/book/'+id)
			.then(response=>{
				if(response.data.success){
					self.bookData.id = response.data.book.id
					self.bookData.total_page = response.data.book.total_page  // total number of pages
					self.bookData.total_colored_page = 0
					self.bookData.colored_index = ''

					let index = 1 // table of content

					if(response.data.book.has_front_cover)
						index += 1

					response.data.book.chapters.forEach(function(v){
						index += 1 // page title

						console.log(v.colored_index)
						let colors = v.colored_index ? v.colored_index.split(',') : []
						let page_number = v.total_page

						for(let i = 1; i <= page_number; i++){
							index += 1 // current page
							console.log(colors.includes(i.toString()), i.toString(), index)
							if(colors.includes(i.toString())){
								self.bookData.total_colored_page += 1
								self.bookData.colored_index += (self.bookData.colored_index != ''?',':'')+index
							}
						}
					})

					if(response.data.book.has_back_cover)
						index += 1

					console.log(index)
				}
			})
			.catch(err=>{
				console.log(err.message)
			})
		},
		getData(id){
			let self = this
			axios.get('/print/book/'+id).then(res => {
				self.userData = res.data.user
				if(self.userData != ''){
					self.paymentForm.fullname = self.userData.firstname +' '+ self.userData.lastname
					self.paymentForm.email = self.userData.email
				}

				self.formatTypeData = res.data.formatType
				self.attributeData =  res.data.attribute
				self.addressData = res.data.address

				self.options.format = this.formatTypeData

				for(let i = 0; i < self.attributeData.length; i++){
					if(self.attributeData[i].keycode == 'paper_type')
						self.options.paper.push(self.attributeData[i])

					if(self.attributeData[i].keycode == 'cover_type')
						self.options.cover.push(self.attributeData[i])

					if(self.attributeData[i].keycode == 'lamination_type')
						self.options.lamination.push(self.attributeData[i])

					if(self.attributeData[i].keycode == 'binding_type')
						self.options.binding.push(self.attributeData[i])
				}
			}).catch(err => {
				console.log(err)
			})
		},
		handleChangeAddress(){
			let self = this

			self.shippingAddressForm = {}
			if(self.addressIndex === '')
				return false

			let row = self.addressData[self.addressIndex]
			self.shippingAddressForm = {
				address: row.address,
				street: row.street,
				city: row.city,
				zipcode: row.zipcode,
				state: row.state,
				country: row.country,
			}

			self.paymentForm.phone = row.phone
			
		},
		handleStep(action){
			if(action == 'calculation')
				this.steps += 1

			if(action == 'shipping')
				this.shippingAddressAction('shippingAddressForm')

			if(action == 'payment')
				this.paymentAction('paymentForm')
		},
		handleBack(){
			this.steps -= 1
		},
		calculateData(formName){
			let self = this
			self.$refs[formName].validate((valid) => {
				if (valid) {
					let params = {
						id: self.bookData.id,
						total_page: self.bookData.total_page,
						total_colored_page: self.bookData.total_colored_page,

						book_format: self.calculateForm.selected_format,
						paper: self.calculateForm.selected_paper,
						cover: self.calculateForm.selected_cover,
						binding: self.calculateForm.selected_binding,
						lamination: self.calculateForm.selected_lamination,
						total_book: self.calculateForm.total_book,
						has_color: self.calculateForm.has_color,
					}

					axios.post('/print/calculate', params)
					.then(response =>{
						self.processData = response.data.result

						let shipping_cost = response.data.shipping_cost.standard
						if(self.calculateForm.selected_delivery == 'express')
							shipping_cost = response.data.shipping_cost.express

						self.processData.book_price = response.data.retail_price.toFixed(2)
						self.processData.spine = response.data.cover_cost.spine.toFixed(2)
						self.processData.shipping_price = parseFloat(shipping_cost).toFixed(2)

						let total_price = parseFloat(self.processData.book_price) + parseFloat(self.processData.shipping_price)

						self.processData.total_price = total_price.toFixed(2)
						self.notCalculated = false
					})
					.catch(err =>{
						self.$notify.error({
						  title: 'Error',
						  message: err.message
						})
					})
				}else{
					self.$notify.error({
						type: 'Error',
						message: 'Please fill-up the required fields'
					})
					return false;
				}
			})
		},
		shippingAddressAction(formName){
			let self = this
			self.$refs[formName].validate((valid) => {
				if (valid) {
					this.steps += 1
				}else{
					self.$notify.error({
						type: 'Error',
						message: 'Please fill-up the required fields'
					})
					return false;
				}
			})
		},
		paymentAction(formName){
			let self = this
			self.stripe.createToken(self.card).then(function(result) {
				// Access the token with result.token
				if (result.error) {
					self.$notify.error({
						title: 'Error',
						message: 'Please fill-up all required fields'
					})
					return false
				}else{
					self.paymentForm.stripeToken = result.token.id
					self.handleCheckout(formName)
				}
			})
		},
		handleCheckout(formName){
			let self = this
			self.$refs[formName].validate((valid) => {
				if (valid) {
					self.$confirm('Are you sure you want to proceed the checkout?', 'Warning', {
					  confirmButtonText: 'OK',
					  cancelButtonText: 'Cancel',
					  type: 'warning',
					  center: true
					}).then(() => {

						self.fullScreenLoading = true

						let params = {
							firstname: self.processData.firstname,
							lastname: self.processData.lastname,
							email: self.processData.email,

							id: self.processData.id,
							title: self.processData.title,
							author: self.processData.author,

							spine: self.processData.spine,
							
							total_height: self.processData.total_height,
							total_width: self.processData.total_width,
							total_height_incl_bleed: self.processData.total_height_incl_bleed,
							total_width_incl_bleed: self.processData.total_width_incl_bleed,
							manufacturing_cost: self.processData.manufacturing_cost,
							retail_price: self.processData.retail_price,

							total_book: self.calculateForm.total_book,
							total_page: self.bookData.total_page,

							colored_index: self.calculateForm.has_color?self.bookData.colored_index:'',
							total_colored_page: self.calculateForm.has_color?self.bookData.total_colored_page:0,

							paper: self.processData.paper,
							binding: self.processData.binding,
							cover: self.processData.cover,
							lamination: self.processData.lamination,

							shipping_price: self.processData.shipping_price,
							book_price: self.processData.book_price,
							total_price: self.processData.total_price,
							shipping_option: self.calculateForm.selected_delivery,

							address: self.shippingAddressForm.address,
							// address2: self.shippingAddressForm.address2,
							street: self.shippingAddressForm.street,
							zipcode: self.shippingAddressForm.zipcode,
							city: self.shippingAddressForm.city,
							country: self.shippingAddressForm.country,

							fullname: self.paymentForm.fullname,
							email: self.paymentForm.email,
							phone: self.paymentForm.phone,
							stripeToken: self.paymentForm.stripeToken,
						}

						axios.post('/print/checkout', params)
						.then(response=>{
							self.fullScreenLoading = false
							if(response.data.success){
								console.log(response.data.xml)
								self.$notify({
								  title: 'Success',
								  message: 'Successfully Checkout New Order',
								  type: 'success'
								})
								self.steps += 2
								setTimeout(function(){
									window.location.href = '/'
								}, 5000)
							}else{
								self.$notify.error({
								  title: 'Error',
								  message: response.data.message
								})
							}
						})
						.catch(err=>{
							self.fullScreenLoading = false
							self.$notify.error({
							  title: 'Error',
							  message: err.message
							})
						})

					})
					.catch(() => {
						self.$notify.info({
							type: 'info',
							message: 'Action has been canceled'
						})
					})
				}else{
					self.$notify.error({
						type: 'Error',
						message: 'Please fill-up the required fields'
					})
					return false;
				}
			})
		},
		cleanData(){
			let self = this

			self.steps = 0
			self.notCalculated = true
			self.disabledBindingAndLamination = true

			self.processData = {
				shipping_price: 0.00,
				book_price: 0.00,
				total_price: 0.00,
			}

			// calculation process
			self.calculateForm = {
				selected_format: '',
				selected_paper: '',
				selected_cover: '',
				selected_binding: '',
				selected_lamination: '',
				total_book: '1',
				has_color: false,
				selected_delivery: 'standard',
			}

			// Shipping address process
			self.shippingAddressForm = {
				address: '',
				// address2: '',
				street: '',
				city: '',
				zipcode: '',
				country: '',
			}

			// Payment process
			self.paymentForm = {
				fullname: '',
				email: '',
				phone: '',
				stripeToken: ''
			}
		},
	},
	watch: {
		'calculateForm.selected_cover'(){
			this.disabledBindingAndLamination = false
			if(this.calculateForm.selected_cover < 6){
				this.disabledBindingAndLamination = true
			}
		},
		steps(){
			let self = this
			if(self.steps == '2'){

				let strip_key = document.head.querySelector('meta[name="stripe-key"]')
				self.stripe = Stripe(strip_key.content)

				let style = {
					base: {
						color: '#32325d',
						fontFamily: '"Nunito", sans-serif',
						fontSmoothing: 'antialiased',
						fontSize: '16px',
						'::placeholder': {
							color: '#aab7c4'
						}
					},
					invalid: {
						color: '#fa755a',
						iconColor: '#fa755a'
					}
				}

				let elements = self.stripe.elements()

				self.card = elements.create('card', {style: style})
				self.card.mount(self.$refs.card)
			}
		}
	},
}