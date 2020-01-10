<template>
    <div v-loading.fullscreen.lock="fullScreenLoading">
        <div class="bg-gray-200 font-primary">
            <el-row type="flex" justify="center" class="mb-4 wrapper-body py-5 no-margin-bottom">
                <el-col :xs="10" class="info-center">
                    <div class=" text-center">
                        <h5 class="text-red-600 text-3xl">{{ storiesData.length }}</h5>
                        <h5 class="text-lg">Stories</h5>
                    </div>
                </el-col>
                <el-col class="flex justify-center">
                    <div class=" profile-image">
                        <el-upload
                          class="avatar-uploader"
                          :action="'/profile/upload/'+userData.id"
                          :headers="headerInfo"
                          :show-file-list="false"
                          :on-success="handleAvatarSuccess"
                          :on-error="handleAvatarError"
                          :before-upload="beforeAvatarUpload">
                          <img v-if="imageUrl" :src="imageUrl" class="avatar">
                          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                    </div>
                </el-col>
                <el-col :xs="10" class="info-center">
                    <div class=" text-center">
                        <h5 class="text-red-600 text-3xl">{{ booksData.length }}</h5>
                        <h5 class="text-lg">Story Books</h5>
                    </div>
                </el-col>
            </el-row>
        </div>
        <div class="wrapper-body">
            <el-row class="mb-2">
                <el-col class="flex justify-center font-primary">
                    <el-radio-group v-model="info">
                        <el-radio-button label="details" background-color="#666">Profile Details</el-radio-button>
                        <el-radio-button label="change-pass">Change Password</el-radio-button>
                        <el-radio-button label="contacts">Contacts</el-radio-button>
                        <el-radio-button v-if="userData.role_id == 1" label="configuration">Configuration</el-radio-button>
                    </el-radio-group>
                </el-col>
            </el-row>
            <el-row type="flex" justify="center" v-if="info == 'details'">
                <el-profile-details :user-data="userData"></el-profile-details>
            </el-row>

            <el-row type="flex" justify="center" v-if="info == 'change-pass'">
                <el-change-password :user-id="userData.id"></el-change-password>
            </el-row>

            <el-row type="flex" v-if="info == 'contacts'">
                <el-contact-details :auth-id="userData.id" :story-data="storiesData" :contact-data="contactData" :book-data="booksData" :address-data="addressData"></el-contact-details>
            </el-row>

            <el-row type="flex" justify="center" v-if="info == 'configuration'">
                <el-configuration :config-data="configData"></el-configuration>
            </el-row>

        </div>
    </div>
</template>
<style type="text/css" scope>
    .avatar{ width: 100% !important; height: 100% !important; }
</style>
<script src="../mixins/profile-mixins.js"></script>