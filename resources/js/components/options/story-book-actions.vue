<template>
    <div class="story-book-options" v-loading="loading">
        <el-dropdown trigger="click">
            <span class="el-dropdown-link pointer">
                <i class="el-icon-more font-small"></i>
            </span>
            <el-dropdown-menu slot="dropdown" class="font-primary">
            <!-- general option -->
                <el-dropdown-item class="clearfix">
                    <el-link 
                       :underline="false" 
                       :href="'/read/'+list.id+'/'+optionType" 
                       icon="el-icon-view">Read</el-link>
                </el-dropdown-item>

            <!-- story options -->
                <el-dropdown-item 
                   class="clearfix" 
                   v-if="(list.user_id == authId || list.has_access) && optionType == 'stories'">
                    <el-link :underline="false" :href="'/write/'+list.id+'/edit'" icon="el-icon-edit">Edit</el-link>
                </el-dropdown-item>

                <el-dropdown-item 
                  class="clearfix" 
                  v-if="list.user_id == authId && optionType == 'stories'">
                    <el-link :underline="false" @click="removeStory(list.id)" icon="el-icon-delete">Remove</el-link>
                </el-dropdown-item>

            <!-- book options -->
                <el-dropdown-item class="clearfix" v-if="optionType == 'books' && !list.is_save_as_draft">
                    <el-link :underline="false" @click="printBookCalculation(list.id)" icon="el-icon-printer">Print</el-link>
                </el-dropdown-item>

                <el-dropdown-item class="clearfix" v-if="optionType == 'books' && !list.is_save_as_draft">
                    <el-link :underline="false" :href="'/pdf/'+list.id" icon="el-icon-download" :download="list.title" :title="list.title">Download</el-link>
                </el-dropdown-item>

                <el-dropdown-item 
                  class="clearfix" 
                  v-if="list.user_id == authId && optionType == 'books' && list.is_save_as_draft">
                    <el-link :underline="false" :href="'/book/'+list.id+'/edit'" icon="el-icon-edit">Edit</el-link>
                </el-dropdown-item>

                <el-dropdown-item 
                  class="clearfix" 
                  v-if="list.is_save_as_draft && list.user_id == authId && optionType == 'books'">
                    <el-link :underline="false" @click="removeBook(list.id)" icon="el-icon-delete">Remove</el-link>
                </el-dropdown-item>

            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    export default {
        props: {
            authId: Number,
            optionType: String,
            storyBookData: {
                type: Object,
            }
        },
        watch:{
          storyBookData(){
            let self = this
            self.list = self.storyBookData
          }
        },
        methods: {
            printBookCalculation(id){
                let self = this
                self.$emit('change', id)
            },
            
            // Removing Story from list
            removeStory(id){
                var self = this
                self.$confirm('Are you sure you want to delete this Story?', 'Warning', {
                  confirmButtonText: 'OK',
                  cancelButtonText: 'Cancel',
                  type: 'warning',
                  center: true
                }).then(() => {
                    self.loading = true
                    if(id != null){
                        axios.delete('/write/'+id)
                        .then(response=>{
                            
                            self.$emit('change', 'remove action')

                            self.loading = false
                            self.$notify({
                              title: 'Success',
                              message: 'Successfully Delete Story',
                              type: 'success'
                            })
                        })
                        .catch(err=>{
                            self.loading = false
                            self.$notify.error({
                              title: 'Error',
                              message: err.message
                            })
                        })
                    }else{
                        self.loading = false
                        self.$notify({
                          title: 'Success',
                          message: 'Successfully Delete Story',
                          type: 'success'
                        })
                    }
                }).catch(() => {
                  self.loading = false
                  self.$notify.info({
                    type: 'info',
                    message: 'Action has been canceled'
                  })
                })
            },
            // Removing Books from List
            removeBook(id){
                var self = this
                self.$confirm('Are you sure you want to delete this Book?', 'Warning', {
                  confirmButtonText: 'OK',
                  cancelButtonText: 'Cancel',
                  type: 'warning',
                  center: true
                }).then(() => {
                    self.loading = true
                    if(id != null){
                        axios.delete('/book/'+id)
                        .then(response=>{
                            
                            self.$emit('change', 'remove action')

                            self.loading = false
                            self.$notify({
                              title: 'Success',
                              message: 'Successfully Delete Book',
                              type: 'success'
                            })
                        })
                        .catch(err=>{
                            self.loading = false
                            self.$notify.error({
                              title: 'Error',
                              message: err.message
                            })
                        })
                    }else{
                        self.loading = false
                        self.$notify({
                          title: 'Success',
                          message: 'Successfully Delete Book',
                          type: 'success'
                        })
                    }
                }).catch(() => {
                  self.loading = false
                  self.$notify.info({
                    type: 'info',
                    message: 'Action has been canceled'
                  })
                })
            },
        },
        data () {
            return {
                loading: false,
                list: this.storyBookData,
            }
        },
    }
</script>