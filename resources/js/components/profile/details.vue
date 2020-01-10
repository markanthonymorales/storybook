<template>
    <div class="col-10 mt-4" style="margin: 0 auto;" v-loading.fullscreen.lock="fullScreenLoading">
        <div class="font-primary">
            <el-form ref="user" :model="user" :rules="userRule" label-width="120px" :label-position="'top'">
                <el-row :gutter="20">
                    <el-col :xl="24" :sm="24" :md="12" :lg="12">
                        <el-form-item label="First Name:" label-size="18px" prop="firstname">
                            <el-input placeholder="Enter your firstname here..." v-model="user.firstname"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :xl="24" :sm="24" :md="12" :lg="12">
                        <el-form-item label="Last Name:" prop="lastname">
                            <el-input placeholder="Enter your surename here..." v-model="user.lastname"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :xl="24" :sm="24" :md="8" :lg="8">
                        <el-form-item label="Nickname:" prop="nickname">
                            <el-input placeholder="Enter your nickname here..." v-model="user.nickname"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :xl="24" :sm="24" :md="8" :lg="8">
                        <el-form-item label="Gender:" prop="gender">
                            <el-select class="w-full" v-model="user.gender" placeholder="Select">
                                <el-option
                                    v-for="gender in genders"
                                    :key="gender.value"
                                    :label="gender.label"
                                    :value="gender.value" class="text-sm">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :xl="24" :sm="24" :md="8" :lg="8">
                        <el-form-item label="Birthday:" style="margin-bottom: 15px !important">
                            <el-date-picker
                                v-model="user.birthdate"
                                type="date"
                                placeholder="Pick a date"
                                format="yyyy/MM/dd"
                                value-format="yyyy-MM-dd">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item class="margin-top">
                            <el-button class="float-right button-red" @click="onSubmitInfoChanges">Save Changes</el-button>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import { data } from '../../data/profile-details-data.js'

    export default {
        name: 'ElProfileDetails',
        props: {
            userData: {
                type: Object,
            },
        },
        provide() {
            return {
                elProfileDetails: this
            };
        },
        data(){
            return {
                ...data,
            }
        },
        created() {
            this.user = this.userData

        },
        methods: {
            onSubmitInfoChanges(formName){
                let self = this
                self.$refs.user.validate((valid) => {
                    if (valid) {
                        self.$confirm('Are you sure you want to save these changes?', 'Warning', {
                          confirmButtonText: 'OK',
                          cancelButtonText: 'Cancel',
                          type: 'warning',
                          center: true
                        }).then(() => {
                            self.fullScreenLoading = true
                            axios.put('/profile/'+self.user.id, self.user)
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
