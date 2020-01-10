<template>
    <div id="write" v-loading.fullscreen.lock="fullScreenLoading" class="wrapper-body">
        <el-form :model="story" :rules="storyRules" ref="storyForm" :label-position="'top'">
            <el-row type="flex" justify="center" class="flex flex-col">
                <el-col>                
                    <div>
                        <el-row class="no-padding no-margin">
                            <el-form-item label="Story Title" label-size="18px" prop="title">
                                <el-input v-model="story.title" placeholder="Story Title" class="story-title"></el-input>
                            </el-form-item>
                        </el-row>
                    </div>
                </el-col>
                <el-col>
                    <div>
                        <el-row type="flex" class="no-margin">
                            <el-col  class="mr-4">
                                <el-form-item label="From Date" label-size="18px" prop="from_date">
                                    <el-date-picker
                                        v-model="story.from_date"
                                        type="date"
                                        placeholder="Pick a date"
                                        format="yyyy/MM/dd"
                                        @change="checkFromAndTo"
                                        value-format="yyyy-MM-dd"
                                        class="w-full">
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                            <el-col >
                                <el-form-item label="To Date" label-size="18px" prop="to_date" >
                                    <el-date-picker
                                        v-model="story.to_date"
                                        type="date"
                                        placeholder="Pick a date"
                                        format="yyyy/MM/dd"
                                        @change="checkFromAndTo"
                                        value-format="yyyy-MM-dd"
                                        >
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
            <el-row v-if="!story.is_file_type" type="flex" justify="center" class="mb-4">
                <el-col class="no-border-radius bg-gray-200 p-3">
                    <!-- The toolbar will be rendered in this container. -->
                    <div id="toolbar-container"></div>
                </el-col>
            </el-row>

            <el-row type="flex" justify="center" class="mb-4">
                <el-col class="bg-gray-200 p-3">
                    <el-row class="no-margin">
                        <el-col><span class="text-sm font-secondary"> Search Keywords </span></el-col>
                        <el-col class="bg-white p-2">
                            <el-tag
                              class="mb-1"
                              :key="tag"
                              v-for="tag in story.search_tags"
                              v-if="tag != ''"
                              closable
                              :disable-transitions="false"
                              @close="handleClose(tag)">
                              {{tag}}
                            </el-tag>
                            <el-input
                              class="input-new-tag"
                              v-if="inputVisible"
                              v-model="inputValue"
                              ref="saveTagInput"
                              size="mini"
                              @keyup.enter.native="handleInputConfirm"
                              @blur="handleInputConfirm"
                            >
                            </el-input>
                            <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Tag</el-button>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>


            <!-- FOR MOBILE -->
            <div v-if="mobile" class="mobile-only">
                <el-row class="no-margin" v-if="!story.is_file_type">
                    <el-button 
                    class="button-red-other w-full no-border-radius" 
                    icon="el-icon-document-add"
                    @click="onAddNewPage">Add Page</el-button>
                </el-row>
                <el-row type="inline" class="bg-gray-200 p-2 flex flex-col mb-3">
                    <el-col :span="24" class="mb-2 add-new-page-container" v-if="!story.is_file_type">
                        <div class="mr-1 flex flex-row mb2">
                            <div  v-for="(page, index) in story.pages" class="pages-container mr-2">
                                <el-tooltip placement="left">
                                    <div slot="content">
                                        <!-- Is Colored Page? <el-checkbox v-model="page.is_colored" >{{ page.is_colored?'Ja':'Nein' }}</el-checkbox> -->
                                        Is Colored Page? <el-checkbox v-model="colors[index]" @change="handlePDFColorIndex(index+=1)">{{ colors[index]?'Ja':'Nein' }}</el-checkbox>
                                    </div>
                                    <div class="inline">
                                        <div class="w-8">
                                            <span class="page-number"> {{ index + 1 }} </span>
                                        </div>
                                        <div class="flex flex-col" style="position: relative" :class="{ 'is-active': index == currentPage }" >
                                            <span v-if="colors[index]" class="is_colored"></span>
                                            <div class="card-docs display-border padding-sm font-xs" v-html="page.content"></div>
                                            <el-row type="flex" class="no-margin">
                                                <el-col>
                                                    <div class="bg-green-300 p-1 text-center"> 
                                                        <i class="pointer el-icon-edit" @click="onOpenPage(index)"></i> 
                                                    </div>
                                                </el-col>
                                                <el-col>
                                                    <div class="bg-red-300 p-1  text-center"> 
                                                        <i class="pointer el-icon-delete" @click="onDeletePage(index)"></i> 
                                                    </div>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </div>
                                </el-tooltip>
                            </div>
                        </div>
                    </el-col>

                    <el-col :span="24" >
                        <div v-if="story.is_file_type">
                            <el-upload-pdf v-model="story.file_url"></el-upload-pdf>
                        </div>
                        <div v-else class="big-paper mb-2" :class="{ disabled : editorData.isReadOnly }">
                            <!-- This container will become the editable. -->
                            <div id="editor" name="editor" contenteditable="true" maxlength="5" @input="isTyping = true">
                                
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <el-button 
                            class="button-red" 
                            icon="el-icon-document-checked"
                            @click="onSubmitStory('storyForm')">Save Story</el-button>
                        </div>
                    </el-col>
                </el-row>

                <el-row type="flex" justify="end">
                    <el-col :xs="10" :sm="6" :md="6" class="mr-2">
                        <div class="no-">
                            <el-autocomplete
                              :disabled="story.user_id != userId" 
                              class="col-md-12 mb-2 no-padding "
                              v-model="searchEmail"
                              :fetch-suggestions="querySearch"
                              placeholder="Please Input"
                              :trigger-on-focus="false"
                              @select="handleSelect"
                            ></el-autocomplete>
                        </div>
                        <div>
                            <el-button 
                            :disabled="story.user_id != userId" 
                            class=" w-full" 
                            icon="el-icon-share"
                            @click="onAddSharedTo">Share Story</el-button>
                        </div>
                    </el-col>
                    <el-col :xs="12" :sm="6" :md="6" class="mr-2 mb-2">
                        <div class="shared inline-block bg-gray-200">
                            <span 
                            v-for="(shared, index) in story.shared_to" 
                            :key="index">

                                <el-tooltip placement="top">
                                    <div v-if="shared.id" slot="content">
                                        Allowed to edit ? <el-checkbox v-if="story.user_id == userId"  v-model="shared.is_allow_edit">{{ shared.is_allow_edit?'Ja':'Nein' }}</el-checkbox>
                                        <span v-else>{{ shared.is_allow_edit?'Ja':'Nein' }}</span>
                                    </div>
                                    <div v-else slot="content">
                                        This user must register first, before you<br />assign to have access to edit the story.
                                    </div>
                                    <div class="el-image-container" v-if="shared.id > -1" :title="shared.email" @click="removeUserEmailFromSharedStory(index)">
                                        <el-image :src="'/storage/images/profile/'+shared.id+'.png'" class="shared-image mr-2">
                                            <div slot="error" class="shared-image">
                                                <i class="el-icon-user-solid text-2xl"></i>
                                            </div>
                                        </el-image>
                                    </div>
                                    <div class="el-image-container" v-else :title="email" @click="removeUserEmailFromSharedStory(index)">
                                        <div class="shared-image mr-2">
                                            <div class="image-slot">
                                                <i class="el-icon-user-solid text-2xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                </el-tooltip>

                            </span>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <!-- FOR MOBILE -->

            <div v-else class="hide-from-mobile">
                <el-row type="flex">
                    <el-col :sm="11" :lg="9" class="mr-2">
                        <el-button 
                        class="w-full mb-2 button-red" 
                        @click="onAddNewPage" 
                        icon="el-icon-document-add"
                        v-if="!story.is_file_type">Add Page</el-button>
                        <div class="bg-gray-200 p-2 listing-page mb-2">

                            <div v-for="(page, index) in story.pages" class="pages-container flex flex-row mb-2" v-if="!story.is_file_type">
                                <el-tooltip placement="left">
                                    <div slot="content">
                                        <!-- Is Colored Page? <el-checkbox v-model="page.is_colored" >{{ page.is_colored?'Ja':'Nein' }}</el-checkbox> -->
                                        Is Colored Page? <el-checkbox v-model="colors[index]" @change="handlePDFColorIndex(index+=1)">{{ colors[index]?'Ja':'Nein' }}</el-checkbox>
                                    </div>
                                    <div class="flex">
                                        <div class="w-8">
                                            <span class="page-number"> {{ index + 1 }} </span>
                                        </div>
                                        <div class="flex flex-col" style="position: relative" :class="{ 'is-active': index == currentPage }">
                                            <span v-if="colors[index]" class="is_colored"></span>
                                            <div class="card-docs display-border padding-sm font-xs" v-html="page.content"></div>
                                            <el-row type="flex" class="no-margin">
                                                <el-col>
                                                    <div class="bg-green-300 p-1 text-center"> 
                                                        <i class="pointer el-icon-edit" @click="onOpenPage(index)"></i> 
                                                    </div>
                                                </el-col>
                                                <el-col>
                                                    <div class="bg-red-300 p-1  text-center"> 
                                                        <i class="pointer el-icon-delete" @click="onDeletePage(index)"></i> 
                                                    </div>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </div>
                                </el-tooltip>
                            </div>
                            <div v-if="story.is_file_type" class="thumbnail">
                                <div 
                                class="pages-container flex flex-row mb-2"
                                v-for="index in totalPDFPages"
                                :key="index"
                                @click="handlePDFThumbnail(index)">
                                    <el-tooltip placement="left">
                                        <div slot="content">
                                            Is Colored Page? <el-checkbox v-model="colors[index-1]" @change="handlePDFColorIndex(index)">{{ colors[index-1]?'Ja':'Nein' }}</el-checkbox>
                                        </div>
                                        <div class="flex">
                                            <div class="w-8">
                                                <span class="page-number"> {{ index }} </span>
                                            </div>
                                            <div class="flex flex-col" style="position: relative" :class="{ 'is-active': index == currentPage }">
                                                <span v-if="colors[index-1]" class="is_colored"></span>
                                                <pdf
                                                    :key="index"
                                                    :src="story.file_url"
                                                    :page="index"
                                                    class="flex flex-col pointer"
                                                    :class="{ 'is-active': currentPDFPage == index }"
                                                    style="display: block; width: 1.3in;"
                                                ></pdf>
                                            </div>
                                            
                                        </div>
                                    </el-tooltip>
                                </div>
                            </div>
                        </div>
                        <el-row class="flex justify-end">
                            <el-col class="no-border-radius">
                                <div class="display-center mb-2">
                                    <div class="inline shared bg-gray-400">
                                        <span 
                                        v-for="(shared, index) in story.shared_to" 
                                        :key="index">

                                        <el-tooltip style="position: relative;" placement="top">
                                            <div v-if="shared.id" slot="content">
                                                Allowed to edit ? <el-checkbox v-if="story.user_id == userId"  v-model="shared.is_allow_edit">{{ shared.is_allow_edit?'Ja':'Nein' }}</el-checkbox>
                                                <span v-else>{{ shared.is_allow_edit?'Ja':'Nein' }}</span>
                                            </div>
                                            <div v-else slot="content">
                                                This user must register first, before you<br />assign to have access to edit the story.
                                            </div>
                                          
                                            <div class="el-image-container" v-if="shared.id > -1" :title="shared.email" @click="removeUserEmailFromSharedStory(index)">
                                                <el-image :src="'/storage/images/profile/'+shared.id+'.png'" class="shared-image mr-2">
                                                    <div slot="error" class="image-slot">
                                                        <i class="el-icon-user-solid text-2xl"></i>
                                                    </div>
                                                </el-image>
                                            </div>
                                            <div class="el-image-container" v-else :title="email" @click="removeUserEmailFromSharedStory(index)">
                                                <div class="el-image">
                                                    <div class="image-slot">
                                                        <i class="el-icon-user-solid text-2xl"></i>
                                                    </div>
                                                </div>
                                            </div>

                                        </el-tooltip>

                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <el-autocomplete
                                      v-if="story.user_id == userId" 
                                      class="w-full"
                                      v-model="searchEmail"
                                      :fetch-suggestions="querySearch"
                                      placeholder="Please Input"
                                      :trigger-on-focus="false"
                                      @select="handleSelect"
                                    ></el-autocomplete>
                                </div>
                                <div>
                                    <el-button 
                                    v-if="story.user_id == userId" 
                                    class="w-full button-red" 
                                    icon="el-icon-share"
                                    @click="onAddSharedTo">Share Story</el-button>
                                </div>
                            </el-col>
                        </el-row>
                    </el-col>
                    <el-col >
                        <div v-if="story.is_file_type">
                            <el-upload-pdf 
                            v-model="story.file_url" 
                            :selected-page="currentPDFPage" 
                            @change="handlePDFChange"></el-upload-pdf>
                        </div>
                        <div v-else class="big-paper mb-2" :class="{ disabled : editorData.isReadOnly }">
                            <!-- This container will become the editable. -->
                            <div id="editor" name="editor" contenteditable="true" maxlength="15" @input="isTyping = true">
                            
                            </div> 
                        </div>
                        <div class="flex justify-end">
                            <el-button 
                            v-if="story.is_file_type" 
                            type="warning" 
                            @click="handleResetPDF" 
                            icon="el-icon-refresh"
                            class="float-right">Reset</el-button>
                            <el-button 
                              v-if="!story.id && story.is_file_type" 
                              type="default"
                              icon="el-icon-back"
                              @click="backToCustomWrite">Back Custom Write Story</el-button>
                            <el-button 
                            class="button-red" 
                            icon="el-icon-document-checked"
                            @click="onSubmitStory('storyForm')">Save Story</el-button>
                        </div>
                    </el-col>
                </el-row>
            </div>
        </el-form>
    </div>
</template>
<style type="text/css" scope>
    #write .ck.ck-reset_all button.button-red, .ck.ck-reset_all button.button-red * {
        cursor: pointer;
        color: inherit;
    }
    #write div.is-active {
        box-shadow: 5px 4px 10px #3a3838ad;
    }
    span.is_colored{
        position: absolute;
        background: rgb(255, 152, 0) none repeat scroll 0% 0%;
        width: 10px;
        height: 10px;
        border-top: 5px solid red;
        border-right: 5px solid blue;
        border-left: 5px solid green;
        border-bottom: 5px solid white;
        right: 5px;
        z-index: 9;
    }
</style>
<script src="../mixins/write-mixins.js"></script>

