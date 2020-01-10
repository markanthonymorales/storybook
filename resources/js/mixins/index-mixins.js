import ElProducts from '../components/book/products.vue';
import { initial_data } from '../data/data.js'

export default {
    data(){
        return {
            ...initial_data,
            onRefresh: '',
        }
    },
    components: {
        'el-products': ElProducts,
    },
    watch: {
        search_title: _.debounce( function() {
            this.isTyping = false
        }, 1000),
        isTyping(value){
            if(!value){
                this.callRefresh()
            }
        },
        filterBook(){
            this.callRefresh()
        },
        event(){
            if(this.event == 'ifLogin'){
                Event.$emit('showContent', true)
                let x = document.getElementsByClassName('footer')
                x[0].style.display = 'flex'
            }
        },
    },
    created() {
        let self = this
        Event.$on('cart', cart => {
            self.cart = cart
        })

        Event.$on('dialogVisible', dialogVisible => {
            self.dialogVisible = dialogVisible
        })
        
        self.mobile = self.isMobile()
        window.addEventListener('resize', function() {
            self.mobile = self.isMobile()
        })

        self.getToCart()
    },
    methods: {
        callRefresh(){
            this.onRefresh = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
        },
    }
}