import ElProfileDetails from '../components/profile/details.vue';
import ElChangePassword from '../components/profile/change-password.vue';
import ElContactDetails from '../components/profile/contacts.vue';
import ElConfiguration from '../components/profile/configuration.vue';

export default {
    props: ['userData', 'contactData', 'storiesData', 'booksData', 'configData', 'addressData'],
    components: {
        'el-profile-details': ElProfileDetails,
        'el-change-password': ElChangePassword,
        'el-contact-details': ElContactDetails,
        'el-configuration': ElConfiguration,
    },
    data(){
        
        return {
            dialogVisible: false,
            fullScreenLoading: false,
            
            imageUrl: '',            
            info: "details",
        }
    },
    created(){
        let self = this
        
        self.imageUrl = `/storage/images/profile/${self.userData.id}/thumbnail/${self.userData.id}.png`
        self.UrlExists(self.imageUrl, (err, res) => {
            if (err) {
                self.imageUrl = `/storage/images/profile/${self.userData.id}.png`
            }
        })

        Event.$on('imageUrl', imageUrl => {
            self.imageUrl = imageUrl
        })
    },
    methods: {       
        handleAvatarError(err){
            let data = JSON.parse(err.message);
            let errorString = data.message

            this.fullScreenLoading = false

            this.$notify.error({
              title: 'Error',
              message: errorString
            })
        },     
        handleAvatarSuccess(res, file) {
            this.fullScreenLoading = false
            Event.$emit('imageUrl', URL.createObjectURL(file.raw))
        },
        beforeAvatarUpload(file) {
            let self = this
            self.fullScreenLoading = true
            const isJPG = file.type === 'image/jpeg'
            const isLt2M = file.size / 1024 / 1024 < 2

            if (!isJPG) {
                self.fullScreenLoading = false

                self.$notify.error({
                  title: 'Error',
                  message: 'Avatar picture must be JPG format!'
                })
            }
            if (!isLt2M) {
                self.fullScreenLoading = false

                self.$notify.error({
                  title: 'Error',
                  message: 'Avatar picture size can not exceed 2MB!'
                })
            }
            return isJPG && isLt2M
        }
    }
}