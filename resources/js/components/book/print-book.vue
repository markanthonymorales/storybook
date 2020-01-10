<template>
	<div class="el-calculate-book" v-loading.fullscreen.lock="fullScreenLoading">
		<div class="card" style="width: 100%; height: auto; margin-bottom: 0;">
            <div class="card-header bg-white p-4">
                <el-row>
                    <el-col :span="24">
                        <el-steps :active="steps" align-center finish-status="success">
                            <el-step title="Calculate Book"></el-step>
                            <el-step title="Shipping Address"></el-step>
                            <el-step title="Payment Process"></el-step>
                            <el-step title="Finish"></el-step>
                        </el-steps>
                    </el-col>
                </el-row>
            </div>

            <div class="card-body">
                <el-row v-if="steps == '0'" type="flex">
                    <el-col :span="24">
                        <el-form :model="calculateForm" :rules="calculateFormRule" ref="calculateForm" :label-position="'top'" class="mb-3">
                            <el-row :gutter="20">
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Format" label-size="12px" prop="selected_format">
                                        <el-select 
                                          v-model="calculateForm.selected_format" 
                                          placeholder="Select" 
                                          class="w-full">
                                            <el-option v-for="(format, i) in options.format"
                                             :key="i"
                                             :label="format.name+' ('+format.value+')'"
                                             :value="format.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Paper" label-size="12px" prop="selected_paper">
                                        <el-select v-model="calculateForm.selected_paper" placeholder="Select" class="w-full">
                                            <el-option v-for="(paper, i) in options.paper"
                                             :key="i"
                                             :label="paper.name"
                                             :value="paper.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Cover" label-size="12px" prop="selected_cover">
                                        <el-select v-model="calculateForm.selected_cover" placeholder="Select" class="w-full">
                                            <el-option v-for="(cover, i) in options.cover"
                                             :key="i"
                                             :label="cover.name"
                                             :value="cover.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Binding" label-size="12px">
                                        <el-select v-model="calculateForm.selected_binding" placeholder="Select" class="w-full" :disabled="disabledBindingAndLamination">
                                            <el-option v-for="(binding, i) in options.binding"
                                             :key="i"
                                             :label="binding.name"
                                             :value="binding.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Lamination" label-size="12px">
                                        <el-select v-model="calculateForm.selected_lamination" placeholder="Select" class="w-full" :disabled="disabledBindingAndLamination">
                                            <el-option v-for="(lamination, i) in options.lamination"
                                             :key="i"
                                             :label="lamination.name"
                                             :value="lamination.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Quantity" label-size="12px" prop="total_book">
                                        <el-input type="number" v-model="calculateForm.total_book" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Colored pages" label-size="12px">
                                        <el-select v-model="calculateForm.has_color" placeholder="Select" class="w-full">
                                            <el-option v-for="(color, i) in options.color"
                                             :key="i"
                                             :label="color.label"
                                             :value="color.value"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item label="Delivery Option" label-size="12px">
                                        <el-select v-model="calculateForm.selected_delivery" placeholder="Select" class="w-full">
                                            <el-option v-for="(delivery, i) in options.delivery"
                                             :key="i"
                                             :label="delivery.label"
                                             :value="delivery.value"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <!-- total -->
                            <el-row :gutter="20" style="border-top: 1px solid #e2e2e2;">
                                <el-col class="float-right" :xs="24" :sm="12" :md="12" :lg="6" :xl="6">
                                    <el-form-item>
                                        <p>Delivery: {{ processData.shipping_price }}</p>
                                        <p>Book: {{ processData.book_price }}</p>
                                        <div class="underline"></div>
                                        <p>Total: {{ processData.total_price }}</p>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-button @click="calculateData('calculateForm')" class="btn-calculate button-red w-full">Calculate</el-button>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-form>
                    </el-col>
                </el-row>
                <el-row v-if="steps == '1'" type="flex" justify="center">
                    <el-col :span="24">
                        <el-form :model="shippingAddressForm" :rules="shippingAddressFormRule" ref="shippingAddressForm" :label-position="'top'" class="mb-3">
                            <el-row :gutter="20">
                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                    <el-form-item label="My Addresses" label-size="12px">
                                        <el-select 
                                        v-model="addressIndex" 
                                        placeholder="Select" 
                                        @change="handleChangeAddress">
                                            <el-option 
                                            label="-Select-"
                                            value=""></el-option>
                                            <el-option 
                                            v-for="(address, key) in addressData"
                                            :key="key"
                                            :label="address.name"
                                            :value="key"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                    <el-form-item label="Address" label-size="12px" prop="address">
                                        <el-input v-model="shippingAddressForm.address" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                                    <el-form-item label="Street" label-size="12px" prop="street">
                                        <el-input v-model="shippingAddressForm.street" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                                    <el-form-item label="City" label-size="12px" prop="city">
                                        <el-input v-model="shippingAddressForm.city" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                                    <el-form-item label="Zip Code" label-size="12px" prop="zipcode">
                                        <el-input v-model="shippingAddressForm.zipcode" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                                    <el-form-item label="Country" label-size="12px" prop="country">
                                        <el-input v-model="shippingAddressForm.country" class="w-full"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-form>
                    </el-col>
                </el-row>
                <el-row v-show="steps == '2'" type="flex" justify="center">
                    <el-col :span="24">
                        <el-form :model="paymentForm" :rules="paymentFormRule" ref="paymentForm" :label-position="'top'" class="mb-3">
                            <el-row :gutter="20">
                                <el-col :span="24">
                                    <el-form-item label="Full Name" label-size="12px" prop="fullname">
                                        <el-input v-model="paymentForm.fullname"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <el-form-item label="Email" label-size="12px" prop="email">
                                        <el-input v-model="paymentForm.email"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <el-form-item label="Phone #" label-size="12px" prop="phone">
                                        <el-input v-model="paymentForm.phone"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :span="24">
                                    <el-form-item label="Card Info." label-size="12px" prop="stripeToken">
                                        <div ref="card"></div>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-form>
                    </el-col>
                </el-row>
                <el-row v-if="steps > '2'" type="flex" justify="center" class="text-center">
                    <el-col :span="12" class="m-4">
                    	<legend style="color: #03A9F4;" class="w-full">Congratulations!</legend>
	                    <label class="w-full">Your order is now being processed.</label>
	                    <label class="w-full">Please check your email for your order receipt.</label>
                    </el-col>
                </el-row>
            </div>
            <div v-if="steps < 3 && !notCalculated" slot="footer" class="dialog-footer card-footer p-4">
                <el-row>
                    <el-col :span="24">
                        <el-button v-if="steps > 0" @click="handleBack" class="float-left" type="default">Back</el-button>
                        <el-button v-if="steps == 0" @click="handleStep('calculation')" class="float-right" type="primary">Next</el-button>
                        <el-button v-if="steps == 1" @click="handleStep('shipping')" class="float-right" type="primary">Next</el-button>
                        <el-button v-if="steps == 2" @click="handleStep('payment')" class="float-right" type="primary">Checkout</el-button>
                    </el-col>
                </el-row>
            </div>
        </div>
	</div>
</template>
<script src="../../mixins/book/print-book-mixins.js"></script>
