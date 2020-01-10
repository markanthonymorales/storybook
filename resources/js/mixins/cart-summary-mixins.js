import { data } from '../data/checkout-data.js'
import { cart_rule } from '../modules/rules.js'

export default {
    props:['fullnameData', 'emailData'],
    data(){
        return {
            ...data,
            ...cart_rule,
        }
    },
    created() {
        let self = this

        self.checkout.fullname = self.fullnameData
        self.checkout.email = self.emailData

        Event.$on('cart', cart => {
            self.cart = cart
        })
        
        self.getToCart()
    },
    watch: {
        step(){
            let self = this
            if(self.step == 2){
                let strip_key = document.head.querySelector('meta[name="stripe-key"]')
                self.stripe = Stripe(strip_key.content)
                
                setTimeout(function(){
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
                    };

                    let elements = self.stripe.elements()

                    self.card = elements.create('card', {style: style})
                    self.card.mount(self.$refs.card)
                }, 500)
            }
        }
    },
    methods: {
        handleSubmit(formName){
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
                        axios.post('/checkout', self.checkout)
                        .then(response=>{
                            self.fullScreenLoading = false
                            if(response.data.success){
                                
                                self.$notify({
                                  title: 'Success',
                                  message: 'Successfully Checkout New Order',
                                  type: 'success'
                                })

                                setTimeout(function(){
                                    window.location.reload()
                                }, 2000)

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
                        title: 'Error',
                        message: 'Please fill-up all required fields'
                    })
                    return false
                }
            })
        },
        createToken(formName){
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
                    self.checkout.stripeToken = result.token.id
                    self.handleSubmit(formName)
                }
            })
        },
        next() {
            if (this.step++ > 2) this.step = 1
        },
        back() {
            if (this.step++ == 2) this.step = 1
        },
        getSummaries(param) {
            const { columns, data } = param;
            const sums = [];
            columns.forEach((column, index) => {
              if (index === 0) {
                sums[index] = 'Total Cost';
                return;
              }
              const values = data.map(item => Number(item[column.property]));
              if (!values.every(value => isNaN(value))) {
                sums[index] = (index == 4 ?'$ ':' ') + values.reduce((prev, curr) => {
                  const value = Number(curr);
                  if (!isNaN(value)) {
                    return prev + curr;
                  } else {
                    return prev;
                  }
                }, 0);
              } else {
                sums[index] = '';
              }
            });

            return sums;
        },
        
        deleteRow(index, rows) {
            let self = this
            
            self.$confirm('Are you sure you want to remove the item?', 'Warning', {
              confirmButtonText: 'OK',
              cancelButtonText: 'Cancel',
              type: 'warning',
              center: true
            }).then(() => {

                self.fullScreenLoading = true
                axios.delete('/cart/'+rows[index].id)
                .then(response=>{
                    rows.splice(index, 1);
                    
                    self.fullScreenLoading = false
                    self.$notify({
                      title: 'Success',
                      message: 'Successfully remove item',
                      type: 'success'
                    })

                    if(response.data.refresh){
                        setTimeout(function(){
                            window.location.reload()
                        }, 2000)
                    }

                    if(!response.data.refresh){
                        self.getToCart()
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
        }
    }
}