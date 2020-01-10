<template>
	<div class="el-products" v-loading="loading">
		<div class="col-md-13 mb-3">
            <el-row v-if="availableBooks.list.length > 0 && filterData.length > 0" type="inline" class="booklist">
                <el-row type="inline">
                    <el-col v-for="(book, index) in availableBooks.list" :key="index" :xs="11" :sm="6" :md="6" :lg="6" :xl="4" v-if="filterData == 'all' || filterData == book.status_type.toString()" class="info-center xs-mr-1 mb-4">
                        <div class="pointer book-cover" @click="handlePreview(book)">
                            <el-image :src="book.cover" class="book-cover-img">
                              <div slot="error" class="image-slot" style="align-items: center; justify-content: center; height: -webkit-fill-available; display: flex;">
                                <img src="img/Icon-default.png" class="img-error">
                              </div>
                            </el-image>
                        </div>
                        <span style="max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" class="title info-center font-primary text-xs" :title="book.title">{{ book.title }}</span>
                        <hr class="info-center">
                        <span style="max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" class="author font-secondary info-center text-xs" :title="book.author">{{ book.author }}</span>
                    </el-col>
                </el-row>
                <el-row>
                    <el-pagination
                      small
                      :hide-on-single-page="hideOnSinglePage"
                      @current-change="handleBookListCurrentChange"
                      :current-page="availableBooks.current_page"
                      :page-size="availableBooks.page_size"
                      layout="prev, pager, next"
                      :total="availableBooks.total">
                    </el-pagination>
                </el-row>
            </el-row>
            <el-row v-else type="inline" class="storybooklist">
                <el-col v-if="filterData.length > 0" class="alert alert-warning info-center">
                    No Available Books!
                </el-col>
                <el-col v-else class="alert alert-danger info-center">
                    Please checked atleast one option Free or Paid!
                </el-col>
            </el-row>
        </div>

        <!-- FREE cover click -->
        <el-dialog v-if="openCover" class="preview-storybook" title="Story Book" :visible.sync="openCover">
            <el-row type="flex">
                <el-col :span="12" class="storybook-left">
                    <div class="no-border-radius cover-img w-full">
                        <el-upload-pdf 
                        v-model="selectedPreview.file_url" 
                        :selected-page="currentPDFPage" 
                        @change="handlePDFChange"></el-upload-pdf>
                    </div>
                </el-col>
                <el-col :span="12" class="storybook-right p-2 lg:p-3 bg-gray-100">
                    <el-row  class="w-full no-margin">
                        <h1 class="font-bold sm:text-base md:text-lg mb-2">{{ selectedPreview.title }}</h1>
                        <p class="text-xs mb-2" :style="mobile?'font-size: .55rem':''"><font class="font-bold">By: </font>{{ selectedPreview.author }}</p>
                        <p class="text-xs mb-2" v-if="!selectedPreview.has_access && selectedPreview.status_type == 'for sale'"><font class="font-bold">Price:</font> {{ selectedPreview.ebook_price }}$</p>

                        <p class="paragraph-form font-primary book-desc">
                            <font class="font-bold">Description:</font><br />{{ selectedPreview.description != '' ? selectedPreview.description : 'No Available Description' }}
                        </p>
                    </el-row>
                </el-col>
            </el-row>
            <div slot="footer" class="dialog-footer">
                <el-row>
                    <el-col :span="12">
                        <div class="preview-container-sm flex flex-row">
                            <div 
                            class="preview-sm hide-from-mobile pointer"
                            v-for="i in maxPreviewThumbnail"
                            :key="i"
                            @click="handlePDFThumbnail(i)">
                                <div class="w-full" :class="i">
                                    <pdf
                                        :key="i"
                                        :src="selectedPreview.file_url"
                                        :page="i"
                                    ></pdf>
                                </div>
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="12" class="mt-3">

                        <!-- Free -->
                        <a :href="'/pdf/'+selectedPreview.id" 
                        class="ml-1 float-right" 
                        v-if="selectedPreview.has_access || selectedPreview.status_type != 'for sale'"
                        target="_blank">
                            <el-button style="font-size: 12px;" class="red-button">Download PDF</el-button>
                        </a>
                        <!-- <a :href="'/epub/'+selectedPreview.id" 
                        class="ml-1 float-right" 
                        v-if="selectedPreview.has_access || selectedPreview.status_type != 'for sale'"
                        target="_blank">
                            <el-button style="font-size: 12px;" class="red-button">Download Ebook</el-button>
                        </a> -->
                        
                        <!-- For Sale -->
                        <el-button 
                        class="red-button ml-1" 
                        v-if="!selectedPreview.has_access && selectedPreview.status_type == 'for sale'"
                        @click="addToCart(selectedPreview.id)">Buy</el-button>
                    </el-col>
                </el-row>
                
            </div>
        </el-dialog>
	</div>
</template>
<style type="text/css" scoped>
    .pdf-watermark span{
        font-size: 0.3rem !important;
    }
    .pointer.book-cover {
        width: auto;
        height: auto;
        position: relative;
        outline: 1px solid transparent;
        -moz-perspective: 100px;
        -moz-transform: rotateY(-3deg);
        -webkit-transform: perspective(100) rotateY(-3deg);
        box-shadow: none;
        margin: 0;
        margin-bottom: 10px;
        background: none !important;
    }
    .book-cover-img{
        background-color: #4e342e;
    }
    .pointer.book-cover:before {
        width: 100%;
        left: 7.5%;
        background-color: #615e58;
        box-shadow: 5px 5px 20px #333;
    }
    .book-cover:after {
        width: 5%;
        left: 100%;
        background-color: #fdfdfd;
        box-shadow: inset 0px 0px 5px #aaa;
        -moz-transform: rotateY(20deg);
        -webkit-transform: perspective(100) rotateY(20deg);
    }
    .book-cover:before, .book-cover:after {
        position: absolute;
        top: 2%;
        height: 96%;
        content: ' ';
        z-index: -1;
    }
    .el-image.book-cover-img {
        position: relative;
        max-width: 100%;
    }
</style>
<script src="../../mixins/book/products-mixins.js"></script>
