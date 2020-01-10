<template>
    <div class="el-address" v-loading.fullscreen.lock="fullScreenLoading">
        <div class="card" style="width: 100%; height: auto; margin-bottom: 0;">
            <!-- <div class="card-header bg-white">
                <label style="font-size: 14px;" v-if="address.id == 0">Add New Address</label>
                <label style="font-size: 14px;" v-else>Update Address</label>
            </div> -->

            <div class="card-body">
                <el-form ref="address" :model="address" :rules="addressRule" :label-position="'top'">
                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="Name" label-size="14px" prop="name">
                                <el-input v-model="address.name"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="Phone #" label-size="14px">
                                <el-input v-model="address.phone"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :span="24">
                            <el-form-item label="Address" label-size="14px" prop="address">
                                <el-input v-model="address.address"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :span="24">
                            <el-form-item label="Street" label-size="14px" prop="street">
                                <el-input v-model="address.street"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="City" label-size="14px" prop="city">
                                <el-input v-model="address.city"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="Zip Code" label-size="14px" prop="zipcode">
                                <el-input v-model="address.zipcode"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="State" label-size="14px" prop="state">
                                <el-input v-model="address.state"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :xs="24" :sm="24" :md="12" :lg="12">
                            <el-form-item label="Country" label-size="14px" prop="country">
                                <el-input v-model="address.country"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-form>
            </div>
            <div slot="footer" class="dialog-footer card-footer bg-white p-4">
                <el-row>
                    <el-col :span="24">
                        <el-button @click="saveChanges('address')" class="float-right" type="primary">Save Changes</el-button>
                    </el-col>
                </el-row>
            </div>
        </div>
    </div>
</template>
<script>
    import { address_rule } from '../../modules/rules.js'
    export default {
        name: 'ElAddress',
        props: {
            addressData: {
                type: Object,
            }
        },
        data () {
            return {
                fullScreenLoading: false,
                address: this.addressData,
                ...address_rule,
            }
        },
        created(){
            let self = this
            Event.$on('selectedAddress', selectedAddress => {
                self.address = selectedAddress
            })
        },
        methods: {
            emittingData(addresses){
                Event.$emit('addresses', addresses)
            },
            saveChanges(formName){
                let self = this
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.$confirm('Are you sure you want to save these changes?', 'Warning', {
                          confirmButtonText: 'OK',
                          cancelButtonText: 'Cancel',
                          type: 'warning',
                          center: true
                        }).then(() => {
                            self.fullScreenLoading = true

                            let action = ''
                            if(self.address.id > 0){
                                action = axios.put('/profile/address/'+self.address.id, self.address)
                            }
                            else
                                action = axios.post('/profile/address/', self.address)

                            action
                            .then(response => {
                                self.emittingData(response.data.addresses)
                                self.fullScreenLoading = false
                                self.$notify({
                                    title: 'Success',
                                    message: 'Successfully save changes',
                                    type: 'success'
                                })
                            })
                            .catch(err => {
                                self.fullScreenLoading = false
                                self.$notify.error({
                                    type: 'Error',
                                    message: 'Something wrong during saving changes!'
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
        },
    }
</script>