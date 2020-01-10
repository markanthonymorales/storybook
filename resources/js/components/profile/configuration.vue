<template>
    <div v-loading.fullscreen.lock="fullScreenLoading">
        <div class="col-8 display-column margin-bottom-lg" style="margin: 0 auto;">
            <el-form ref="config" :model="config" :rules="configRules" :label-position="'top'">
                <el-row :gutter="20">
                    <el-col :xl="24" :sm="24" :md="12" :lg="12">
                        <el-form-item label="Book Markup Price:" prop="markup_price">
                            <el-input v-model="config.markup_price">
                                <template slot="append">%</template>
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :xl="24" :sm="24" :md="12" :lg="12">
                        <el-form-item label="EBook Markup Price:" prop="ebook_markup_price">
                            <el-input v-model="config.ebook_markup_price">
                                <template slot="prepend">$</template>
                            </el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="Footer Text:" prop="footer">
                            <el-input
                              placeholder="Please input"
                              v-model="config.footer">
                            </el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="Terms and Policies:" prop="terms_policies">
                            <ckeditor
                                style="border: 1px solid #e2e2e2;"
                                :editor="editor"
                                v-model="config.terms_policies"
                                :config="editorConfig"
                                @ready="onReady"
                            ></ckeditor>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24" class="text-center">
                        <el-form-item >
                            <el-button @click="onSubmitConfig" class="button-red">Update</el-button>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import CKEditor from '@ckeditor/ckeditor5-vue';
    import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';
    import { DecoupEditConfig } from '../../modules/global-decoupled-editor.js'
    
    export default {
        name: 'ElConfiguration',
        props: {
            configData: {
                type: Array,
            },
        },
        components: {
            // Use the <ckeditor> component in this view.
            ckeditor: CKEditor.component
        },
        data(){
            return {
                fullScreenLoading: false,
                editor: DecoupledEditor,
                editorConfig: DecoupEditConfig('/profile/upload/terms-policies/tmp'),
                config: {
                    markup_price: 0,
                    ebook_markup_price: 0,
                    footer: '',
                    terms_policies: '',
                },
                configRules: {},
            }
        },
        created(){
            let self = this
            
            if(self.configData.length > 0){
                for(let i = 0; i < self.configData.length; i++){
                    self.config[self.configData[i].keycode] = self.configData[i].value
                    self.configRules[self.configData[i].keycode] = [
                        { required: true, message: 'Please input '+self.configData[i].name, trigger: 'blur' },
                    ]
                }
            }
        },
        methods: {
            onReady( editor )  {
                // Insert the toolbar before the editable area.
                editor.ui.getEditableElement().parentElement.insertBefore(
                    editor.ui.view.toolbar.element,
                    editor.ui.getEditableElement()
                );
            },
            onSubmitConfig(){
                let self = this
                self.$refs.config.validate((valid) => {
                    if (valid) {
                        self.$confirm('Are you sure you want to save these changes?', 'Warning', {
                          confirmButtonText: 'OK',
                          cancelButtonText: 'Cancel',
                          type: 'warning',
                          center: true
                        }).then(() => {
                            self.fullScreenLoading = true
                            axios.post('/profile/config/update', self.config)
                            .then(response => {
                                self.fullScreenLoading = false
                                self.$notify({
                                    title: 'Success',
                                    message: 'Successfully save changes',
                                    type: 'success'
                                })
                            })
                            .catch(() => {
                                self.fullScreenLoading = false
                                self.$notify.error({
                                    type: 'Error',
                                    message: 'Something wrong during saving changes!'
                                })
                            })
                        }).catch(() => {
                            self.fullScreenLoading = false
                            self.$notify.info({
                                type: 'info',
                                message: 'Action has been canceled!'
                            })
                        })
                    } else {
                        self.fullScreenLoading = false
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
