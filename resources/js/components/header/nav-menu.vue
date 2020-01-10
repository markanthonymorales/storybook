<template>
	<div v-show="showContent" id="navMenu" class="font-secondary">
		<div v-if="route_name == 'profile.index'" class="hide-from-mobile">
            <el-row type="flex" class="wrapper-body no-margin-bottom">
                <el-col :xs="6" :sm="5" :md="10" :lg="5">
                   <img :src="'/img/poetray_me-logo.png'">
               </el-col>
            </el-row>
            <el-menu :default-active="activeIndex" mode="horizontal" background-color="#D8D8D8" text-color="#000" active-text-color="#666" @select="handleSelect">
                <el-row type="flex" class="wrapper-body no-margin-bottom">
                    <el-col :xs="6" :sm="5" :md="10" :lg="5">
                       <el-menu-item index="/mystorybook" @click="dialog_visible = true" class="text-center">
                            MyStoryBook
                        </el-menu-item>
                   </el-col>
                </el-row>
            </el-menu>
        </div>

        <div v-if="route_name != 'profile.index'" class="hide-from-mobile">
			<el-row v-if="authdata" class="wrapper-body" :class="route_name" type="flex">
				<el-col :xs="5" :sm="5" :md="4" :lg="5">
					<img v-if="route_name == 'mystorybook' || route_name == 'cart.index'" :src="'/img/poetray_me-logo.png'">
				</el-col>
				<el-col :xs="5" :sm="5" :md="4" :lg="5">
					<img v-if="route_name == 'book.create' || route_name == 'book.edit'" :src="'/img/poetray_me-logo.png'" class="w-full">
				</el-col>
				<el-col :xs="5" :sm="5" :md="4" :lg="5">
					<img v-if="route_name == 'read'" :src="'/img/poetray_me-logo.png'" class="w-full">
				</el-col>
				<el-col :xs="5" :sm="5" :md="4" :lg="5">
					<img v-if="route_name == 'write.create' || route_name == 'write.edit'" :src="'/img/poetray_me-logo.png'" class="w-full">
				</el-col>
				<el-col :xs="3" :sm="3" :md="3" :lg="2">
					
				</el-col> 
			</el-row>
			<el-row v-else type="flex" class="wrapper-body">
                <el-col :xs="6" :sm="5" :md="10" :lg="5">
                   <img :src="'/img/poetray_me-logo.png'">
                </el-col>
            </el-row>
			<el-menu :default-active="activeIndex" class=" border-t-2 border-b-2 border-blue-500" mode="horizontal" background-color="#D8D8D8" text-color="#000" active-text-color="#666" @select="handleSelect">
				<el-row class="wrapper-body label-text" type="flex" >
					<el-col :xs="5" :sm="5" :md="4" :lg="5">
						<el-menu-item index="/mystorybook" class="font-secondary text-center text-xl sm:text-2xl xl:text-sm">
						    MyStoryBook
						</el-menu-item>
					</el-col>
					<el-col :sm="5" :md="5" :lg="5">
						<el-menu-item index="/book/create" class="font-secondary text-center text-xl sm:text-2xl">
						    Book
						</el-menu-item>
					</el-col>
					<el-col :sm="5" :md="5" :lg="5">
						<el-menu-item :disabled="recent == ''" :index="'/read/'+recent" class="font-secondary text-center text-xl sm:text-2xl">
						    Read
						</el-menu-item>
					</el-col>
					<el-col :sm="5" :md="5" :lg="5">
						<el-menu-item index="/write/create" class="font-secondary text-center text-xl sm:text-2xl">
						    Write
						</el-menu-item>
					</el-col>
					<el-col v-if="authData" :sm="3" :md="3" :lg="2">
					    <el-submenu index="5">
				            <template slot="title">
				            	<el-badge :value="cart.totalQty" :max="99" class="item">
                                	<el-image>
										<div slot="error">
											<div class="avatar-xs-container">
												<el-image :src="imageUrl" style="bottom: 13px; left: -7px; width: 45px;">
											      <div slot="error" class="image-slot">
											        <i class="el-icon-picture-outline" ></i>
											      </div>
											    </el-image>
											</div>
										</div>
									</el-image>
                                </el-badge>
				            </template>
				            <el-menu-item index="/cart" class="font-secondary lg:text-xs xl:text-sm">
				                Cart : {{ cart.totalQty }}
				            </el-menu-item>
				            <el-menu-item index="/profile" class="font-secondary lg:text-xs xl:text-sm">
				                My Profile
				            </el-menu-item>
				            <el-menu-item index="5-2" class="font-secondary lg:text-xs xl:text-sm">
				                Logout
				            </el-menu-item>
				        </el-submenu>
					</el-col>

					<el-col v-else :sm="3" :md="3" :lg="2">
						<el-menu-item>
                            <el-badge :value="cart.totalQty" :max="99" class="item">
                              <el-link type="text" :underline="false" href="/cart">Cart</el-link>
                            </el-badge>
                        </el-menu-item>
					</el-col>
					
				</el-row>
			</el-menu>
        </div>
        
        <!-- mobile menu -->
        <div class="mobile-only">
        	<el-row type="flex">
        		<el-col :span="5" class="bg-red-600 text-xl font-secondary" style="height: auto; line-height: 0;">
        			<el-menu :default-active="activeIndex" class="el-menu-demo" mode="horizontal" @select="handleSelect">
	                    <el-submenu index="1">
	                    	<template slot="title"><i class="el-icon-menu text-white text-3xl" ></i></template>
	                    	<el-menu-item index="/mystorybook" class="text-2xl">
							    MyStoryBook
							</el-menu-item>
	                        <el-menu-item index="/book/create" class="text-2xl">
					            Book 
					        </el-menu-item>
	                        <el-menu-item :disabled="recent == ''" :index="'/read/'+recent" class=" text-2xl">
					            Read 
					        </el-menu-item>
	                        <el-menu-item index="/write/create" class=" text-2xl">
	                        	Write
	                    	</el-menu-item>
	                    	<el-menu-item class=" text-2xl">
				                <el-link type="text" :underline="false" href="/cart">Cart : {{ cart.totalQty }}</el-link>
				            </el-menu-item>

	                    	<el-submenu index="5" v-if="authdata">
	                    		<template slot="title">
		                    		<div class="flex flex-row">
		                    			<div>
		                                	<el-image>
		                    					<div slot="error">
		                    						<div class="avatar-xs-container">
		                    							<el-image :src="imageUrl">
													    	<div slot="error" class="image-slot">
													        	<i class="el-icon-picture-outline" ></i>
													      	</div>
													    </el-image>
		                    						</div>
		                    					</div>
		                    				</el-image>
		                    			</div>
		                    			<div class="ml-3">Settings</div>
		                    		</div>
	                    		</template>
	                    		<el-menu-item index="/profile">
					                My Profile
					            </el-menu-item>
					            <el-menu-item index="5-2">
					                Logout
					            </el-menu-item>
					        </el-submenu>
	                    </el-submenu>
	                </el-menu>
        			<!-- <i  class="el-icon-menu" @click="table = true"></i> -->
        		</el-col>
        		<el-col>
        			<img :src="'/img/poetray_me-logo.png'" style="width: fit-content; float: right; margin-top: 9px;">
        		</el-col>
        	</el-row>
        </div>
	</div>
</template>
<style type="text/css">
	.el-link.el-link--default{
		z-index: 9;
	}
	.avatar-xs-container{
		width: 32px;
		height: 32px;
		border-radius: 50%;
		overflow: hidden;
	}
	.avatar-xs-container img{}
</style>

<script>
	import { nav } from '../../data/data.js'

	export default {
		props: ['authData','routeName'],
		data(){
			return {
		        ...nav,
		        dialogVisible: false,
		        showContent: true,
			}
		},
		created(){
			let self = this

			self.mobile = self.isMobile
			self.route_name = self.routeName
			
			if(self.authData){
				self.authdata = self.authData
				self.imageUrl = `/storage/images/profile/${self.authdata.id}/thumbnail/${self.authdata.id}.png`
				self.UrlExists(self.imageUrl, (err, res) => {
					if (err) {
						self.imageUrl = `/storage/images/profile/${self.authdata.id}.png`
			        }
				})
			}else
				self.showContent = false

			Event.$on('cart', cart => {
                self.cart = cart
            })

            Event.$on('showContent', showContent => {
                self.showContent = showContent
            })

            Event.$on('recent', recent => {
                self.recent = recent
            })

            Event.$on('imageUrl', imageUrl => {
	            self.imageUrl = imageUrl
	        })
            
            self.getToCart()

			if(self.authData)
	            self.getLastView()

            window.addEventListener('resize', function() {
                self.mobile = self.isMobile()
            })
		},
		methods:{
			handleSelect(key, keyPath) {
				if(!this.authdata){
					setTimeout(function(){
						if(key){
							let menu = document.querySelector('ul[role="menu"]')
			                menu.parentNode.style.display = 'none'
			                Event.$emit('dialogVisible', true)
						}
		            }, 100)
				}

				if(this.authdata){
					if(typeof keyPath[keyPath.length - 1] != 'undefined'){
			        	if(keyPath[keyPath.length - 1] == '5-2'){
				        	var lg = document.getElementById('logout-form')
				        	lg.submit()
				        	return false;
				        }
			        }
			        window.location.href = key
				}
		    }
		}
	}
</script>