<template>
    <div v-loading.fullscreen.lock="fullScreenLoading">
        <div class="display-column text-center margin-bottom-lg">
            <el-form ref="changePass" :model="changePass" :rules="passRules" label-width="120px" :label-position="'top'">
                <el-form-item label="New Password:" prop="password">
                    <el-input type="password" placeholder="Enter your new password here..." v-model="changePass.password"></el-input>
                </el-form-item>
                <el-form-item label="Confirm Password:" prop="confirm_password" style="margin-bottom: 15px !important" >
                    <el-input type="password" placeholder="Re-enter new password for confirmation." v-model="changePass.confirm_password"></el-input>
                </el-form-item>
                <el-form-item >
                    <el-button class="button-red" @click="onSubmitPassword">Update Password</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ElChangePassword',
        props: {
            userId: Number,
        },
        data(){
            let validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('Please input the password'));
                } else {
                    if (this.changePass.confirm_password !== '') {
                        this.$refs.changePass.validateField('confirm_password');
                    }
                    callback();
                }
            };
            let validatePass2 = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('Please input the password again'));
                } else if (value !== this.changePass.password) {
                    callback(new Error('Two inputs don\'t match!'));
                } else {
                    callback();
                }
            };
            
            return {
                fullScreenLoading: false,
                changePass: {
                    password: '',
                    confirm_password: ''
                },
                passRules: {
                    password: [
                        { validator: validatePass, trigger: 'blur' },
                        { min: 8, message: 'Length should be 8 and above', trigger: 'blur' }
                    ],
                    confirm_password: [{ validator: validatePass2, trigger: 'blur' }],
                },
            }
        },
        watch: {
            
        },
        methods: {
            onSubmitPassword(formName){
                let self = this
                self.$refs.changePass.validate((valid) => {
                    if (valid) {
                        self.$confirm('Are you sure you want to save these changes?', 'Warning', {
                          confirmButtonText: 'OK',
                          cancelButtonText: 'Cancel',
                          type: 'warning',
                          center: true
                        }).then(() => {
                            self.fullScreenLoading = true
                            axios.post('/profile/update/password', {'id':self.userId, 'password':self.changePass.password, 'password_confirmation':self.changePass.confirm_password})
                            .then(response=>{
                                self.fullScreenLoading = false
                                self.$notify({
                                  title: 'Success',
                                  message: 'Successfully save changes',
                                  type: 'success'
                                })
                            })
                            .catch(err=>{
                                self.fullScreenLoading = false
                                self.$notify.error({
                                  title: 'Error',
                                  message: err.message
                                })
                                console.log(err)
                            })
                        }).catch(() => {
                          self.$notify.info({
                            type: 'info',
                            message: 'Action has been canceled'
                          })
                        })
                    } else {
                        self.$notify.error({
                          title: 'Error',
                          message: 'Please fill-up the required fields!'
                        })
                        return false
                    }
                })
            },
        }
    }
</script>
