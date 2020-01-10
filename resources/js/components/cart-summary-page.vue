<template>
    <div id="summaryPage" class="container" v-loading.fullscreen.lock="fullScreenLoading">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card" style="width: 100%; height: auto;">
                    <div class="card-header bg-white">
                        <a class="btn-link" href="/">
                            <i class="el-icon-arrow-left"></i>Back
                        </a>
                    </div>

                    <div class="card-body">
                        <el-row>
                            <el-col :span="24">
                                <el-steps :active="step" align-center>
                                    <el-step title="Cart Summary"></el-step>
                                    <el-step title="Payment Process"></el-step>
                                    <el-step title="Finish"></el-step>
                                </el-steps>
                            </el-col>
                        </el-row>
                        <el-row v-if="step == '1'" type="flex" justify="center">
                            <el-col :span="20">
                                <el-table
                                 :data="cart.items"
                                 :summary-method="getSummaries"
                                 show-summary
                                 style="width: 100%; font-size: 12px;">
                                    <el-table-column
                                    prop="title"
                                    sortable
                                    label="Title">
                                    </el-table-column>
                                    <el-table-column
                                    prop="author"
                                    sortable
                                    label="Author">
                                    </el-table-column>
                                    <el-table-column
                                    prop="description"
                                    class-name="description"
                                    label="Description">
                                    </el-table-column>
                                    <el-table-column
                                    prop="quantity"
                                    sortable
                                    label="Quantity">
                                    </el-table-column>
                                    <el-table-column
                                    prop="unit_price"
                                    sortable
                                    label="Unit Price">
                                    </el-table-column>
                                    <el-table-column
                                    label="Action">
                                        <template slot-scope="scope">
                                            <el-button
                                            @click.native.prevent="deleteRow(scope.$index, cart.items)"
                                            type="text"
                                            size="small">
                                            Remove
                                            </el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </el-col>
                        </el-row>
                        <el-row v-if="step == '2'" type="flex" justify="center">
                            <el-col :span="20">
                                <el-form ref="checkoutForm" :rules="checkoutRule" :model="checkout">
                                    <el-row >
                                        <el-col :span="24">
                                            <el-form-item label="Full Name" prop="fullname">
                                                <el-input v-model="checkout.fullname"></el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row >
                                        <el-col :xs="24" :sm="24" :md="12" :lg="12" class="pr-1">
                                            <el-form-item label="Email" prop="email">
                                                <el-input v-model="checkout.email"></el-input>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :xs="24" :sm="24" :md="12" :lg="12" class="pr-1">
                                            <el-form-item label="Phone #" prop="phone">
                                                <el-input v-model="checkout.phone"></el-input>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row >
                                        <el-col :span="24">
                                            <el-form-item label="Card Info." prop="stripeToken">
                                                <div ref="card"></div>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </el-form>
                            </el-col>
                        </el-row>
                        <el-row v-if="step == '3'" type="flex" justify="center">
                            <el-col :span="20" class="text-center">
                                <h1 class="text-center mt-5 alert alert-success">
                                <i class="el-icon-circle-check" style="font-size: 100px;"></i><br /><br />
                                    Successfully Checkout New Order!!!
                                </h1>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="card-footer bg-white">
                        <el-button v-if="step == 1" class="mt-3 float-right" type="primary" @click="next">Next</el-button>
                        <el-button v-if="step == 2" class="mt-3 el-col-xs-24 el-col-sm-24 el-col-md-5" type="default" @click="back">Back To Cart Summary</el-button>
                        <el-button v-if="step == 2" href="/" class="mt-3 float-right el-col-xs-24 el-col-sm-24 el-col-md-4" type="danger" @click="createToken('checkoutForm')">Finish</el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style type="text/css" scope>
    td.description div {
        overflow-wrap: normal;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    /**
     * The CSS shown here will not be introduced in the Quickstart guide, but shows
     * how you can use CSS to style your Element's container.
     */
    .StripeElement {
        position: relative;
        font-size: 14px;
        width: 100%;
        display: inline-block;
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid #dcdfe6;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
    @media only screen and (max-width: 767px) {
        .StripeElement {
            font-size: 9px !important;
        }
    }
</style>
<script src="../mixins/cart-summary-mixins.js"></script>
