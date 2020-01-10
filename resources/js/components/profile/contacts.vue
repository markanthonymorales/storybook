<template>
    <div class="col-10" style="margin: 0 auto;" v-loading.fullscreen.lock="fullScreenLoading">

        <el-dialog 
        :title="selectedAddress.id > 0?'Edit Address':'Add New Address'"
        :visible.sync="showAddressDialog" 
        :before-close="handleCloseAddressDialog">
            <el-address :address-data="selectedAddress"></el-address>
        </el-dialog>

        <div class="clearfix mt-4">
            <div class="mb-2 w-full">
                <label class="pointer" @click="handleShowTable('showAddressTable')">Address List : {{ addresses?addresses.length:0 }} 
                    <i v-if="!showAddressTable" 
                    class="el-icon-arrow-right" 
                    style="display: inline-block !important;"></i>
                    <i v-else 
                    class="el-icon-arrow-down" 
                    style="display: inline-block !important;"></i>
                </label>
                <el-button 
                    class="el-icon-plus float-right" 
                    size="small" 
                    @click="handleShowAddressDialog" 
                    style="display: inline-block !important;">Add New Address</el-button>
            </div>
            <el-table
            v-if="showAddressTable"
            :data="addresses"
            border
            height="300"
            style="width: 100%; font-size: 12px;">
                <el-table-column
                  prop="name"
                  label="Alias">
                </el-table-column>
                <el-table-column
                  prop="address"
                  label="Address">
                </el-table-column>
                <el-table-column
                  prop="city"
                  label="City">
                </el-table-column>
                <el-table-column
                  prop="state"
                  label="State">
                </el-table-column>
                <el-table-column
                  prop="country"
                  label="Country">
                </el-table-column>
                <el-table-column
                label="Action" width="180">
                    <template slot-scope="scope">
                        <el-button
                        size="mini"
                        @click="handleEditAddress(scope.$index, scope.row)">Edit</el-button>
                        <el-button
                        size="mini"
                        type="danger"
                        @click="handleDeleteAddress(scope.$index, scope.row)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="clearfix mt-4">
            <label class="pointer mb-2 w-full" @click="handleShowTable('showContactTable')">Contact List : {{ contacts?contacts.length:0 }} 
                <i v-if="!showContactTable" class="el-icon-arrow-right" style="display: inline-block !important;"></i>
                <i v-else class="el-icon-arrow-down" style="display: inline-block !important;"></i>
            </label>
            
            <el-table
            v-if="showContactTable"
            :data="contacts"
            border
            height="300"
            style="width: 100%; font-size: 12px;">
                <el-table-column type="expand">
                  <template slot-scope="props">
                    <p>Firstname : {{ props.row.firstname }}</p>
                    <p>Lastname : {{ props.row.lastname }}</p>
                    <p>Nickname : {{ props.row.nickname }}</p>
                    <p>Email Address: {{ props.row.email }}</p>
                    <p>Birthday: {{ props.row.birthday }}</p>
                    <p>Total Stories: {{ props.row.total_stories }}</p>
                    <p>Total Books: {{ props.row.total_books }}</p>
                    <p>Total stories shared to you: {{ props.row.total_stories_shared_to_you }}</p>
                    <p>Total stories you shared: {{ props.row.total_stories_you_shared }}</p>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="is_user"
                  label="Is User">
                </el-table-column>
                <el-table-column
                  prop="email"
                  label="Email Address">
                </el-table-column>
            </el-table>
        </div>
        <div class="clearfix mt-4">
            <label class="pointer mb-2 w-full" @click="handleShowTable('showBookTable')">Book List : {{ books.length }} 
                <i v-if="!showBookTable" class="el-icon-arrow-right" style="display: inline-block !important;"></i>
                <i v-else class="el-icon-arrow-down" style="display: inline-block !important;"></i>
            </label>
            <el-table
            v-if="showBookTable"
            :data="books"
            border
            height="300"
            style="width: 100%; font-size: 12px;">
                <el-table-column type="expand">
                  <template slot-scope="props">
                    <p>Description: {{ props.row.description }}</p>
                    <p>Price: {{ props.row.price }}</p>
                    <p>Type: {{ props.row.status_type }}</p>
                    <p>Tags: {{ props.row.tags }}</p>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="title"
                  label="Title">
                </el-table-column>
                <el-table-column
                  prop="from_date"
                  label="From">
                </el-table-column>
                <el-table-column
                  prop="to_date"
                  label="To">
                </el-table-column>
                <el-table-column
                  prop="created_at"
                  label="Created Date">
                </el-table-column>
                <el-table-column
                  prop="updated_at"
                  label="Last Update">
                </el-table-column>
            </el-table>
        </div>
        <div class="clearfix mt-4">
            <label class="pointer mb-2 w-full" @click="handleShowTable('showStoryTable')">Story List : {{ stories.length }} 
                <i v-if="!showStoryTable" class="el-icon-arrow-right" style="display: inline-block !important;"></i>
                <i v-else class="el-icon-arrow-down" style="display: inline-block !important;"></i>
            </label>
            <el-table
            v-if="showStoryTable"
            :data="stories"
            border
            height="300"
            style="width: 100%; font-size: 12px;">
                <el-table-column type="expand">
                  <template slot-scope="props">
                    <p>Tags: {{ props.row.tags }}</p>
                    <p>Shares: {{ props.row.shared_to }}</p>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="title"
                  label="Title">
                </el-table-column>
                <el-table-column
                  prop="from_date"
                  label="From">
                </el-table-column>
                <el-table-column
                  prop="to_date"
                  label="To">
                </el-table-column>
                <el-table-column
                  prop="created_at"
                  label="Created Date">
                </el-table-column>
                <el-table-column
                  prop="updated_at"
                  label="Last Update">
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script>
    import Address from './address.vue';
    import { data } from '../../data/contact-details-data.js';

    export default {
        name: 'ElContactDetails',
        props: {
            authId: Number,
            storyData: {
                type: Array,
            },
            bookData: {
                type: Array,
            },
            contactData: {
                type: Array,
            },
            addressData: {
                type: Array,
            }
        },
        components: {
            'el-address': Address
        },
        data(){            
            return {
                ...data,
            }
        },
        created(){
            let self = this

            self.stories = this.storyData
            self.contacts = this.contactData
            self.books = this.bookData
            self.addresses = this.addressData
            self.selectedAddress.user_id = this.authId

            Event.$on('addresses', addresses => {
                self.addresses = addresses
            })

            self.emittingSelectedAddressData(self.selectedAddress)
        },
        methods: {

            emittingSelectedAddressData(selectedAddress){
                Event.$emit('selectedAddress', selectedAddress)
            },

            handleShowTable(showTable){
                if(this[showTable]){
                    this[showTable] = false
                }else
                    this[showTable] = true
            },

            handleCloseAddressDialog(){
                this.selectedAddress = {
                    id: 0,
                    user_id: this.authId,
                    name: '',
                    address: '',
                    city: '',
                    zipcode: '',
                    state: '',
                    country: '',
                    phone: ''
                }
                this.emittingSelectedAddressData(this.selectedAddress)
                this.showAddressDialog = false
            },

            handleShowAddressDialog(){
                this.showAddressDialog = true
            },

            handleEditAddress(index, row) {
                this.selectedAddress = row
                this.emittingSelectedAddressData(this.selectedAddress)
                this.showAddressDialog = true
            },
              
            handleDeleteAddress(index, row) {
                let self = this
                self.$confirm('Are you sure you want to remove these address?', 'Warning', {
                  confirmButtonText: 'OK',
                  cancelButtonText: 'Cancel',
                  type: 'warning',
                  center: true
                }).then(() => {
                    self.fullScreenLoading = true
                    axios.delete('/profile/address/'+row.id)
                    .then(response => {
                        self.addresses = response.data.addresses
                        self.fullScreenLoading = false
                        self.$notify({
                            title: 'Success',
                            message: 'Selected address has been successfully removed',
                            type: 'success'
                        })
                    })
                    .catch(err => {
                        self.fullScreenLoading = false
                        self.$notify.error({
                            type: 'Error',
                            message: 'Something wrong during removing address!'
                        })
                    })
                }).catch(() => {
                    self.$notify.info({
                        type: 'info',
                        message: 'Action has been canceled'
                    })
                })
            }
        }
    }
</script>
