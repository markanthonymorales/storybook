<template>
    <div id="readStory" :class="!is_preview ? 'wrapper-body' : 'col-12'">
        <el-row v-if="!is_preview" type="flex" justify="center">
            <el-col class="font-primary">
                <el-link :underline="false" :href="'/mystorybook'" icon="el-icon-arrow-left">Back</el-link>
            </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="no-margin font-primary">
            <el-col :span="22" class="sm:text-base md:text-3xl font-bold">{{ story.title }}</el-col>
            <el-col :span="2">
                <span class="flex justify-end sm:text-base md:text-lg font-bold">{{ totalPage > 0 ? currentPage + 1 : 0 }}/{{ totalPage }}</span>
            </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="mb-3 font-primary">
            <el-col class="sm:text-xs md:text-sm">
                <span>{{ story.story_date }}</span> <span class="font-bold"> by: </span> <span> {{ story.author }} </span>
            </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="mb-3">
            <div v-if="!is_preview" class="custom-pagination">
                <el-link v-if="readNext" :underline="false" :href="'/read/'+readNext" class="up">
                    <i class="el-icon-arrow-up sm:text-base md:text-3xl lg:text-5xl text-red-600 font-bold"></i>
                </el-link>
                <el-link v-if="readPrev" :underline="false" :href="'/read/'+readPrev" class="down">
                    <i class="el-icon-arrow-down sm:text-base md:text-3xl lg:text-5xl text-red-600 font-bold" style="display: inline-block !important;"></i>
                </el-link>
            </div>
            <el-col v-if="!mobile" :span="2" class="hide-from-mobile">
                <div class="center">
                    <i class="pointer el-icon-arrow-left sm:text-base md:text-3xl lg:text-5xl text-red-600 font-bold" @click="setActiveItem(currentPage - 1)"></i>
                </div>
            </el-col>
            <el-col v-else :sm="22" :md="22" class="mobile-only shadow">
                <div v-if="story.is_file_type">
                    <pdf 
                      :src="story.file_url" 
                      style="width:100%; height: 4.7in;"
                      :page="currentPage" 
                      @num-pages="totalPage = $event"></pdf>
                </div>
                <el-carousel 
                  v-else 
                  ref="carousel" 
                  indicator-position="none" 
                  height="4.7in" 
                  :autoplay="false" 
                  @change="onChangePage">
                    <el-carousel-item v-for="(page, index) in story.pages" :key="index">
                      <div v-if="page.is_cover" 
                      class="big-paper is_cover" 
                      :style="{ backgroundImage: page.content }"></div>

                      <div v-else 
                      class="big-paper">
                          <div id="editor" class="editor" v-html="page.content"></div>
                      </div>
                    </el-carousel-item>
                    <el-carousel-item v-if="story.pages.length < 1">
                      <div class="big-paper"></div>
                    </el-carousel-item>
                </el-carousel>
            </el-col>

             <el-col v-if="!mobile" class="hide-from-mobile shadow" style="width:6.9in">
                <div v-if="story.is_file_type">
                    <pdf 
                      :src="story.file_url" 
                      style="width:100%; height: 9.6in;"
                      :page="currentPage" 
                      @num-pages="totalPage = $event"></pdf>
                </div>
                <el-carousel 
                  v-else 
                  ref="carousel" 
                  indicator-position="none" 
                  style="margin: 0 auto; height: 9.6in;" 
                  height="100%" 
                  :autoplay="false" 
                  @change="onChangePage" arrow="never">
                    <el-carousel-item v-for="(page, index) in story.pages" :key="index">
                      <div v-if="page.is_cover" 
                      class="big-paper is_cover" 
                      :style="{ backgroundImage: page.content }"></div>

                      <div v-else 
                      class="big-paper">
                          <div id="editor" class="editor" v-html="page.content"></div>
                          <p style="bottom: 5% ;position: absolute;left: 0;width: 100%;text-align: right;padding-right: 5%;">{{ page.page_number }}</p>
                      </div>
                    </el-carousel-item>
                    <el-carousel-item v-if="story.pages.length < 1">
                      <div class="big-paper"></div>
                    </el-carousel-item>
                </el-carousel>
            </el-col>

            <el-col v-if="!mobile" :span="2" class="hide-from-mobile text-right">
                <div class="center">
                    <i class="pointer el-icon-arrow-right sm:text-base md:text-3xl lg:text-5xl text-red-600 font-bold" @click="setActiveItem(currentPage + 1)"></i>
                </div>
            </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="no-margin">
            <el-col>
                <div class="additional_information font-primary">
                    <i class="el-icon-circle-plus no-margin" v-if="event == 'close'"></i>
                    <i class="el-icon-remove no-margin" v-else></i>
                    <span class="label-text font-bold no-margin pointer" @click="showAdditionalInfo"> Additional Information </span>
                </div>
            </el-col>
        </el-row>
        <el-row type="flex" justify="center" class="no-margin font-primary" v-if="event=='open'">
            <el-col class="sm:text-base md:text-md mr-2" :xs="12" :sm="12" :md="11" :lg="15" :xl="15">
                <h6 class="sm:text-base md:text-md font-bold mb-2 text-gray-700">Keywords</h6>
                <div class="bg-gray-200 p-2" v-if="story.search_tags.length > 0">
                    <el-tag v-for="(tag, index) in story.search_tags" :key="index"
                    class="sm:text-base md:text-md lg:text-xl xl:text-xl red-tag ml-0 mr-2 mb-2">{{ tag }}</el-tag>
                </div>
                <p v-else class="alert alert-info">No Available Tags</p>
            </el-col>
            <el-col :xs="12" :sm="12" :md="11" :lg="7" :xl="7">
                <h6 class="sm:text-base md:text-md font-bold mb-2 text-gray-700">Shared With:</h6>
                <div v-if="story.shared_to.length > 0">
                    <div class="bg-gray-400 shared inline-block">
                        <span 
                            v-for="(share, index) in story.shared_to" 
                            :key="index">
                            <div class="el-image-container" v-if="share.id > -1" :title="share.email">
                                <el-image :src="'/storage/images/profile/'+share.id+'.png'" class="shared-image mr-2">
                                    <div slot="error" class="image-slot">
                                        <i class="el-icon-user-solid sm:text-base md:text-2xl"></i>
                                    </div>
                                </el-image>
                            </div>
                            <div class="el-image-container" v-else :title="share.email">
                                <div class="el-image">
                                    <div class="image-slot">
                                        <i class="el-icon-user-solid sm:text-base md:text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
                <p v-else class="alert alert-info">No Available Shared With</p>
            </el-col>
        </el-row>
    </div>
</template>
<style type="text/css" scope>
    #readStory .annotationLayer{ width: fit-content !important; }
    .custom-pagination {
        position: absolute;
        height: 113%;
        z-index: 9;
        margin: -60px auto;
    }
    .custom-pagination a.up {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .custom-pagination a.down {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }
    .big-paper.is_cover{
        height: inherit;
    }
    @media only screen and (max-width: 767px) {
        .big-paper.is_cover{
            width: 100%;
        }
        .big-paper legend{
            line-height: 10 !important;
            font-size: 16px;
        }
        .big-paper div.table-of-content table td h5{
            font-size: 12px;
        }
        .custom-pagination a.up {
            top: 65px;
            font-size: 20px;
        }
        .custom-pagination a.down {
            bottom: 0;
            font-size: 20px;
        }
    }
</style>
<script src="../mixins/read-mixins.js"></script>
