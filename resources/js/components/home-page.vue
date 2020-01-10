<template>
    <div class="wrapper-body">
        <el-row v-if="mobile" class="mobile-only mb-3">
              <el-link href="/write/create" :underline="false" class="text-white button-red w-full" icon="text-white el-icon-plus">Add New Story</el-link>
        </el-row>
        <el-row type="flex" class="mt-3 mb-3 font-primary">
            <el-col v-if="!mobile" :xs="12" :sm="6" :md="6" :lg="5" :xl="5" class="label-text hide-from-mobile md:ml-5">Add New Story</el-col>
            <el-col  :xs="24" :sm="10" :md="10" :lg="19" :xl="19">
              <span class="title-line"></span>
              <label class="title">Recent Books and Stories</label>
            </el-col>
        </el-row>
        <el-recent-view-list 
        :on-refresh="onRefresh" 
        :auth-id="auth_id"
        @action="handleStoryBookOptionChange"></el-recent-view-list>

        <!-- YEAR FILTER -->
        <el-row type="flex" justify="center" class="mb-3 font-primary">
            <el-col :xs="19" :sm="21" :md="18" :lg="23" :xl="23" class="mr-2">
                <el-slider v-if="!mobile" class="hide-from-mobile w-full year-filter-slider"
                  v-model="getSelectedYear"
                  :marks="filterByYears"
                  :min="minYear"
                  range
                  :max="maxYear"
                  :disabled="allYear"
                  @change="handleStoryBookOptionChange('on refresh')">
                </el-slider>

                <!-- `checked` should be true or false -->
                <el-select v-else class="hide-from-web" v-model="getSelectedYear" placeholder="Select" :disabled="allYear" @change="handleStoryBookOptionChange('on refresh')">
                    <el-option
                      v-for="(item, index) in filterByYears"
                      :key="index"
                      :label="item"
                      :value="index">
                    </el-option>
                </el-select>
            </el-col>
            <el-col :xs="5" :sm="3" :md="4" :lg="1" :xl="1" class="pl-2 pt-2">
                 <el-checkbox v-model="allYear" class="font-primary" @change="handleStoryBookOptionChange('on refresh')">All</el-checkbox>
            </el-col>
        </el-row>

        <!-- MONTH FILTER -->
        <el-row type="flex" justify="center" class="mb-5 font-primary">
            <el-col  :xs="19" :sm="21" :md="18" :lg="23" :xl="23" class="mr-2">
                <el-slider v-if="!mobile" class="hide-from-mobile w-full"
                  v-model="getSelectedMonth"
                  :marks="filterByMonths"
                  :show-tooltip="false"
                  :min="1"
                  range
                  :max="12"
                  :disabled="allMonth"
                  @change="handleStoryBookOptionChange('on refresh')">
                </el-slider>

                <el-select v-else class="hide-from-web" v-model="getSelectedMonth" placeholder="Select" :disabled="allMonth" @change="handleStoryBookOptionChange('on refresh')">
                    <el-option
                      v-for="(item, index) in filterByMonths"
                      :key="index"
                      :label="item"
                      :value="index">
                    </el-option>
                </el-select>
            </el-col>
            <el-col :xs="5" :sm="3" :md="4" :lg="1" :xl="1" class="font-primary pl-2 pt-2">
                <el-checkbox v-model="allMonth" @change="handleStoryBookOptionChange('on refresh')">All</el-checkbox>
            </el-col>
        </el-row>

        <!-- SEARCH -->
        <el-row type="flex" class="font-primary">
            <el-col :xs="12" class="mr-2">
                <span class="label-text">Title: </span>
            </el-col>
            <el-col :xs="12">
                <span class="label-text">Keyword: </span>
            </el-col>
        </el-row>
        <el-row type="flex" class="mb-4 font-primary">
            <el-col :xs="12" class="mr-2">
                <el-input placeholder="Search Title" v-model="search_title" @input="isTyping = true"></el-input>
            </el-col>
            <el-col :xs="12">
                <el-input placeholder="Search Keyword" v-model="search_keyword" @input="isTyping = true"></el-input>
            </el-col>
        </el-row>

        <el-row type="flex" justify="center" class="mt-3 mb-3">
            <el-col>
              <span class="title-line"></span>
              <label class="title">Story List</label>
            </el-col>
        </el-row>

        <!-- STORY LIST -->
        <el-story-list 
        :on-refresh="onRefresh"
        :auth-id="auth_id"
        :filter-data="param" @change="handleChangeData" @action="handleStoryBookOptionChange"></el-story-list>

        <el-row type="flex" justify="center" class="mt-3 mb-3">
            <el-col>
              <span class="title-line"></span>
              <label class="title">Book List</label>
            </el-col>
        </el-row>

        <!-- BOOK LIST -->
        <el-book-list 
        :on-refresh="onRefresh"
        :auth-id="auth_id"
        :filter-data="param" @change="handleChangeData" @action="handleStoryBookOptionChange"></el-book-list>
        
        <!-- Product List Section -->
        <el-row type="flex" justify="center" class="mt-3 mb-3">
            <el-col>
              <span class="title-line"></span>
              <label class="title">Storybooks List</label>
            </el-col>
        </el-row>

        <div class="col-md-12">
            <el-products :auth-data="auth_id" :on-refresh="onRefresh" :search-data="param.search_title" :filter-data="'all'"></el-products>
        </div>

        <el-dialog 
          title="Print Book"
          v-if="openPrintBookCalculation"
          :visible.sync="openPrintBookCalculation" 
          class="print-book-form">
            <el-row type="flex">
                <el-col :span="24">
                    <el-print-book 
                      :id-data="printBook.id" 
                      ></el-print-book>
                </el-col>
            </el-row>
        </el-dialog>
    </div>
</template>
<style type="text/css" scope>
    .year-filter-slider .el-slider__marks div { display: none; }
    .year-filter-slider .el-slider__marks div:first-child { display: block; }
    .year-filter-slider .el-slider__marks div:last-child { display: block; }
    .el-slider__stop{ background-color: transparent; }
    .underline { border-width: 1px; }
    .print-book-form .el-dialog { width: 80% !important; }
    .print-book-form .el-dialog .el-form .el-form-item__label { font-size: 14px !important; }
    .el-slider__runway.disabled .el-slider__bar { background-color: #C0C4CC !important; }
</style>
<script src="../mixins/home-mixins.js"></script>
