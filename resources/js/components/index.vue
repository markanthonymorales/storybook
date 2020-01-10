<template>
    <div  id="landingPage" v-loading.fullscreen.lock="fullScreenLoading">
        <div class="pointer display-center" v-if="event=='toLogin'" v-on:click="event='ifLogin'">
            <img src="img/poetrayme_logo-splash.png" class="img-logo m-auto">
        </div>

        <div v-if="event=='ifLogin'">

            <!-- BANNER -->
            <el-row>
                <el-col>
                    <img :src="'../img/Banner_booklist.png'" class="img-banner w-full">
                    <span class="banner-content">
                        <h1>Lots of book to read. <br />100% authentic</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis</p>
                    </span>
                </el-col>
            </el-row>
            <!-- END BANNER -->

            <!-- BODY -->
            <div class="wrapper-body">
                <el-row  class="mt-5 mb-5">
                    <el-col>
                        <img :src="'../img/steps_banner.png'" class="img-banner w-full">
                    </el-col>
                </el-row>
                <el-row class="font-primary">
                    <el-col :xs="24" :sm="12" :md="12" class="mb-3 sm:mb-0 sm:mr-3">
                        <el-input placeholder="Please input" 
                         suffix-icon="el-icon-search" 
                         v-model="search_title"
                         @input="isTyping = true"></el-input>
                    </el-col>
                    <el-col :xs="8" :sm="4" :md="4">
                        <el-radio label="all" v-model="filterBook" class="color-black font-md">All</el-radio>
                    </el-col>
                    <el-col :xs="8" :sm="4" :md="4">
                        <el-radio label="free" v-model="filterBook" class="color-black font-md">Public</el-radio>
                    </el-col>
                    <el-col :xs="8" :sm="3" :md="3">
                        <el-radio label="for sale" v-model="filterBook" class="color-black font-md">Paid</el-radio>
                    </el-col>
                </el-row>
 
                <el-row type="flex" justify="center" class="mt-3 mb-3">
                    <el-col>
                        <span class="title-line"></span>
                        <label class="title">Storybooks List</label>
                    </el-col>
                </el-row>

                <!-- LIST OF BOOKS -->
                <el-products :on-refresh="onRefresh" :auth-data="authdata" :search-data="search_title" :filter-data="filterBook"></el-products>
                <!-- END LIST OF  BOOKS -->

                <el-row type="flex" justify="center" class="mt-3 mb-3">
                    <el-col>
                        <span class="title-line"></span>
                        <label class="title">Editor's Choice</label>
                    </el-col>
                </el-row>

                <!-- LIST OF EDITOR'S CHOICE -->
                <el-row type="inline">
                    <el-col class="alert alert-warning info-center font-secondary" :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        Coming Soon!
                    </el-col>
                </el-row>
            </div>
        </div>

        <!-- login/register dialog -->
        <el-dialog title="Poetray.me" :visible.sync="dialogVisible" >
            <el-row type="flex" justify="center" class="pt-10">
                <el-col>
                    <span class="display-center mb-3 font-primary"> Already have an account? </span>
                </el-col>
            </el-row>
            <el-row class="no-padding" type="flex" justify="center">
                <el-col>
                    <div class="display-center mb-3">
                        <el-link :underline="false" href="/login" class="color-white"><el-button class="button-red" >Login</el-button></el-link>
                    </div>
                </el-col>
            </el-row>
            <el-row class="no-padding" type="flex" justify="center">
                <el-col>
                    <span class="display-center mb-3 font-primary"> Don't have an account? </span>
                </el-col>
            </el-row>
            <el-row class="no-margin pb-10" type="flex" justify="center">
                    <el-col>
                        <div class="display-center">
                            <el-link :underline="false" href="/register" class="color-white"><el-button class="button-red">Register </el-button></el-link>
                        </div>
                    </el-col>
                </el-row>
        </el-dialog>
        <!-- end login/register dialog -->
    </div>
</template>
<style type="text/css" scoped>
    span.banner-content {
        position: absolute;
        top: 30%;
        left: 0;
        right: 0;
        text-align: center;
        width: 50%;
        margin: 0 auto;
        color: #fff;
        text-transform: uppercase;
    }
    span.banner-content h1 {
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 25px;
        word-spacing: 10px;
    }
</style>
<script src="../mixins/index-mixins.js"></script>
