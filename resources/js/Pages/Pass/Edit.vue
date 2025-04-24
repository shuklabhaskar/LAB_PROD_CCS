<template>

    <div class="container-fluid p-0">

        <!--MAIN HEADING-->
        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>EDIT</strong> PASS</h3>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <form @submit.prevent="editPass">

                    <div class="accordion mb-4" id="accordionPass">

                        <!--BASIC DETAILS-->
                        <div class="accordion-item">

                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    PASS BASIC DETAILS
                                </button>
                            </h2>

                            <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionPass">
                                <div class="accordion-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">

                                                <!--MEDIA TYPE-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Media Type <span class="text-danger"> *</span></label>
                                                    <select disabled class="form-control form-select"   v-model="form.media_type_id">
                                                        <option value="-1">Select Media Type</option>
                                                        <option v-for="MediaType in MediaTypes" :value="MediaType.media_type_id">
                                                            {{MediaType.media_name}}
                                                        </option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.media_type_id">{{ errors.media_type_id }}</div>
                                                </div>

                                                <template v-if="form.media_type_id == 1 || form.media_type_id == 2">
                                                    <!--CARD TYPE-->
                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Card Type <span class="text-danger"> *</span></label>
                                                        <select class="form-control form-select"  v-model="form.card_type_id">
                                                            <option value="-1">Select Card Type</option>
                                                            <option v-for="cardType in cardTypes" :value="cardType.card_type_id">{{cardType.card_name}}</option>
                                                        </select>
                                                        <div class="text-danger" v-if="errors.card_type_id">{{errors.card_type_id }}</div>
                                                    </div>
                                                </template>

                                                <!--PASS ID-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Pass ID <span class="text-danger"> *</span></label>
                                                    <input type="number"  class="form-control" v-model="form.pass_id" placeholder="Pass ID"/>
                                                    <div class="text-danger" v-if="errors.pass_id">{{ errors.pass_id }}</div>
                                                </div>

                                                <!--PRODUCT TYPE-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Product Type <span class="text-danger"> *</span></label>
                                                    <select class="form-control form-select"   v-model="form.product_type_id">
                                                        <option value="-1">Select Product Type</option>
                                                        <option v-for="ProductType in ProductTypes" :value="ProductType.product_type_id">
                                                            {{ProductType.product_name.toUpperCase()}}
                                                        </option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.product_type_id">{{ errors.product_type_id }}</div>
                                                </div>

                                                <!--PASS NAME-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Pass Name <span class="text-danger"> *</span></label>
                                                    <input type="text"  class="form-control" v-model="form.pass_name" placeholder="Pass Name"/>
                                                    <div class="text-danger" v-if="errors.pass_name">{{ errors.pass_name }}</div>
                                                </div>

                                                <!--DESCRIPTION-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Description</label>
                                                    <input class="form-control"  placeholder="Description" v-model="form.description" />
                                                    <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                                                </div>

                                                <!--COMPANY ID-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Company <span class="text-danger">*</span></label>
                                                    <select v-model="form.company_id" class="form-control" >
                                                        <option selected value="-1">Select Company</option>
                                                        <option v-for="company in Companies" :value="company.company_id">{{ company.company_name }}
                                                        </option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.company_id">{{ errors.company_id }}</div>
                                                </div>

                                                <!--STATUS-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Status <span class="text-danger"> *</span></label>
                                                    <select class="form-control form-select" v-model="form.status" >
                                                        <option value="null">Select Status</option>
                                                        <option :value="true">ACTIVE</option>
                                                        <option :value="false">INACTIVE</option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.status">{{ errors.status }}</div>
                                                </div>

                                                <!--FARE TABLE ID-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Select Fare <span class="text-danger"> *</span></label>
                                                    <select class="form-control form-select" v-model="form.fare_table_id" >
                                                        <option value="-1">Select Fare</option>
                                                        <option v-for="fare in Fares" :value="fare.fare_table_id">{{fare.fare_name.toUpperCase()}}</option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.fare_table_id">{{ errors.fare_table_id }}</div>
                                                </div>

                                                <!--START DATE-->
                                                <div class="mb-3 col-md-4">
                                                    <label class="form-label">Start Date<span class="text-danger">*</span></label>
                                                    <input v-model="form.start_date"  type="text" class="form-control flatpickr-minimum" placeholder="Enter Start Date">
                                                    <div class="text-danger" v-if="errors.start_date">{{ errors.start_date }}</div>
                                                </div>

                                                <!--END DATE-->
                                                <div class="mb-3  col-md-4">
                                                    <label class="form-label">End Date</label>
                                                    <input v-model="form.end_date" type="text" class="form-control flatpickr-minimum" placeholder="Enter End Date"/>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!--PENALTY DETAILS-->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">PASS PENALTY DETAILS</button>
                            </h2>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionPass">
                                <div class="accordion-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">

                                                <!--SAME_stn_OVERTIME_LIMIT-->
                                                <div class="mb-3 col-md-3" id="same_stn_over_time_limit">
                                                    <label class="form-label">Same Station Over Time Limit</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" v-model="form.same_stn_over_time_limit">
                                                        <span class="input-group-text">Min</span>
                                                    </div>
                                                </div>

                                                <!--SAME_stn_OVERTIME_PENALTY-->
                                                <div class="mb-3 col-md-3" id="same_stn_over_time_pen">
                                                    <label class="form-label">Same Station Over Time Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.same_stn_over_time_pen">
                                                    </div>
                                                </div>

                                                <!--SAME_stn_OVERTIME_MAX_PENALTY-->
                                                <div class="mb-3 col-md-3" id="same_stn_over_time_max_pen">
                                                    <label class="form-label">Same Station Over Time Max Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.same_stn_over_time_max_pen">
                                                    </div>
                                                </div>

                                                <!--OTHER_stn_OVERTIME_LIMIT-->
                                                <div class="mb-3 col-md-3" id="other_stn_over_time_limit">
                                                    <label class="form-label">Other Station Over Time Limit</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" v-model="form.other_stn_over_time_limit">
                                                        <span class="input-group-text">Min</span>
                                                    </div>
                                                </div>

                                                <!--OTHER_stn_OVERTIME_PENALTY-->
                                                <div class="mb-3 col-md-3" id="other_stn_over_time_pen">
                                                    <label class="form-label">Other Station Over Time Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.other_stn_over_time_pen">
                                                    </div>
                                                </div>

                                                <!--OTHER_stn_OVERTIME MAX PENALTY-->
                                                <div class="mb-3 col-md-3" id="other_stn_over_time_max_pen">
                                                    <label class="form-label">Other Station Over Time Max Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.other_stn_over_time_max_pen" name="other_stn_overtime_max_penalty" id="other_stn_overtime_max_penalty">
                                                    </div>
                                                </div>

                                                <!--OVER TRAVELLING PENALTY-->
                                                <div class="mb-3 col-md-3" id="over_travel_pen">
                                                    <label class="form-label">Over Travel Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.over_travel_pen">
                                                    </div>
                                                </div>

                                                <!--ENTRY MISMATCH LIMIT-->
                                                <div class="mb-3 col-md-3" id="entry_mismatch_limit">
                                                    <label class="form-label">Entry Mismatch Limit</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" v-model="form.entry_mismatch_limit" class="form-control"/>
                                                        <span class="input-group-text">Min</span>
                                                    </div>
                                                </div>

                                                <!--ENTRY MISMATCH SAME TIME PENALTY-->
                                                <div class="mb-3 col-md-3" id="entry_mismatch_same_time_pen">
                                                    <label class="form-label">Entry Mismatch Same Time Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.entry_mismatch_same_time_pen">
                                                    </div>
                                                </div>

                                                <!--ENTRY MISMATCH NO EXIT PENALTY-->
                                                <div class="mb-3 col-md-3" id="entry_mismatch_no_exit_pen">
                                                    <label class="form-label">Entry Mismatch No Exit Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.entry_mismatch_no_exit_pen">
                                                    </div>
                                                </div>

                                                <!--ENTRY MISMATCH PENALTY-->
                                                <div class="mb-3 col-md-3" id="exit_mismatch_pen">
                                                    <label class="form-label">Entry Mismatch Penalty</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control" v-model="form.exit_mismatch_pen">
                                                    </div>
                                                </div>

                                                <!--ENTRY EXIT CONTROL-->
                                                <div class="mb-3 col-md-3" id="entry_exit_control">
                                                    <label class="form-label">Entry Exit Control</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input type="checkbox" v-model="form.entry_exit_control">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--PASS PARAMETER SECTION-->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                    PASS PARAMETER SECTION
                                </button>
                            </h2>
                            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionPass">
                                <div class="accordion-body">
                                    <div class="card">
                                        <div class="card-body">

                                            <div class="row">

                                                <!--PASS IS TEST OR NOT-->
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Is Test <span class="text-danger">*</span></label>
                                                    <select v-model="form.is_test" class="form-control form-select" name="test_id">
                                                        <option selected value="null">Select Tester</option>
                                                        <option :value="true">YES</option>
                                                        <option :value="false">NO</option>
                                                    </select>
                                                    <div class="text-danger" v-if="errors.is_test">{{ errors.is_test }}</div>
                                                </div>

                                                <!--ENTRY VALIDITY PERIOD-->
                                                <div class="mb-3 col-md-3" id="entry_validity_period">
                                                    <label class="form-label">Entry Validity Period</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" min="0" v-model="form.entry_validity_period" class="form-control"/>
                                                        <span class="input-group-text">Min</span>
                                                    </div>
                                                </div>


                                                <!--RETURN VALIDITY PERIOD-->
                                                <div class="mb-3 col-md-3" id="return_validity_period">
                                                    <label class="form-label">Return Validity Period</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" min="0" v-model="form.return_validity_period" class="form-control"/>
                                                        <span class="input-group-text">Min</span>
                                                    </div>
                                                </div>

                                                <!--PASS VALIDITY PERIOD-->
                                                <div class="mb-3 col-md-3" id="pass_validity_period">
                                                    <label class="form-label">Pass Validity Period</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" min="0" v-model="form.pass_validity_period" class="form-control"/>
                                                        <span class="input-group-text">Days</span>
                                                    </div>
                                                </div>

                                                <!--GRACE PERIOD-->
                                                <div class="mb-3 col-md-3" id="grace_period">
                                                    <label class="form-label">Grace Period</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" min="0" v-model="form.grace_period" class="form-control"/>
                                                        <span class="input-group-text">Days</span>
                                                    </div>
                                                </div>

                                                <!--TRIP COUNT-->
                                                <div class="mb-3 col-md-3" id="trip_count">
                                                    <label class="form-label">Trip Count</label>
                                                    <input type="number" min="0" v-model="form.trip_count" class="form-control"/>
                                                </div>

                                                <!--DAILY TRIP LIMIT-->
                                                <div class="mb-3 col-md-3" id="daily_trip_limit" >
                                                    <label class="form-label">Daily Trip Limit</label>
                                                    <input  disabled type="number" min="0" v-model="form.daily_trip_limit" class="form-control"/>
                                                </div>

                                                <!--ISSUE PERMIT-->
                                                <div class="mb-3 col-md-3" id="issue_permit">
                                                    <label class="form-label">Issue Permit</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input  type="checkbox" v-model="form.issue_permit">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--RELOAD PERMIT-->
                                                <div class="mb-3 col-md-3" id="reload_permit">
                                                    <label class="form-label">Reload Permit</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input  type="checkbox" v-model="form.reload_permit">
                                                        </div>
                                                    </div>
                                                </div>


                                                <!--CARD REFUND PERMIT-->
                                                <div class="mb-3 col-md-3" id="refund_permit">
                                                    <label class="form-label">Refund Permit</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input type="checkbox" v-model="form.refund_permit">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--CARD REFUND CHARGES-->
                                                <div class="mb-3 col-md-3" id="refund_charges">
                                                    <label class="form-label">Card Refund Charges</label>
                                                    <div class="input-group mb-3">
                                                        <input v-if="form.refund_permit == 1" type="text" class="form-control" v-model="form.refund_charges">
                                                        <input v-else disabled type="text" class="form-control" v-model="form.refund_charges">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!--SV BALANCE SECTION-->
                        <div class="accordion-item" v-if="form.product_type_id === 3">
                            <h2 class="accordion-header" id="flush-headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">SV BALANCE SECTION</button>
                            </h2>
                            <div id="flush-collapseFive" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingFive" data-bs-parent="#accordionPass">
                                <div class="accordion-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">

                                                <!--Min SV Topup Amount-->
                                                <div class="mb-3 col-md-4" id="min_sv_topup_amt">
                                                    <label class="form-label">Min SV Topup Amount</label>
                                                    <div class="input-group mb-3"><span class="input-group-text">₹</span>
                                                        <input  disabled type="text" class="form-control" v-model="form.min_sv_topup_amt">
                                                    </div>
                                                </div>

                                                <!--SV Step Topup Amount-->
                                                <div class="mb-3 col-md-4" id="sv_step_topup_amt">
                                                    <label class="form-label">SV Step Topup Amount</label>
                                                    <div class="input-group mb-3"><span class="input-group-text">₹</span>
                                                        <input  disabled type="text" class="form-control" v-model="form.sv_step_topup_amt">
                                                    </div>
                                                </div>

                                                <!--MIN SV ENTRY AMOUNT-->
                                                <div class="mb-3 col-md-4" id="min_sv_entry_bal">
                                                    <label class="form-label">Min SV Entry Balance</label>
                                                    <div class="input-group mb-3"><span class="input-group-text">₹</span>
                                                        <input  disabled type="text" class="form-control" v-model="form.min_sv_entry_bal">
                                                    </div>
                                                </div>

                                                <!--MAX SV BALANCE-->
                                                <div class="mb-3 col-md-4" id="max_sv_bal">
                                                    <label class="form-label">Max SV Balance</label>
                                                    <div class="input-group mb-3"><span class="input-group-text">₹</span>
                                                        <input disabled type="text" class="form-control" v-model="form.max_sv_bal">
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mb-xl-3">
                        <!--BACK BUTTON-->
                        <div class="col-auto d-none d-sm-block">
                            <Link :href="'/pass'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward"/> Back</Link>
                        </div>

                        <!--UPDATE BUTTON-->
                        <div class="col-auto ms-auto text-end mt-n1">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-refresh" />&nbsp;Update</button>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-light">
                                    <div class="modal-body m-3 text-center">
                                        <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                        <h3 class="m-2">
                                            <span>Are you sure! do you want to Update this Pass ?</span></h3>
                                        <a data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                                        <button type="submit" class="btn btn-primary m-2 btn-lg" data-bs-dismiss="modal">YES</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</template>

<script>
    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";
    import axios from 'axios'
    export default {
        props:{
            Pass:Object,
            MediaTypes:Array,
            Companies:Array,
            ProductTypes:Array,
            Units:Array,
            Fares:Array,
            cardTypes:Array,
            errors:Object,
        },
        name: "Edit",
        layout: Layout,
        components:{
            Link
        },
        data() {
            return {
                form: useForm({
                    media_type_id: this.Pass.media_type_id,
                    card_type_id: this.Pass.card_type_id,
                    product_type_id: this.Pass.product_id,
                    pass_id: this.Pass.pass_id,
                    pass_name: this.Pass.pass_name,
                    description: this.Pass.description,
                    company_id: this.Pass.company_id,
                    status: this.Pass.status,
                    fare_table_id: this.Pass.fare_table_id,
                    start_date: this.Pass.start_date,
                    end_date: this.Pass.end_date,
                    same_stn_over_time_limit: this.Pass.same_stn_over_time_limit,
                    same_stn_over_time_pen: this.Pass.same_stn_over_time_pen,
                    same_stn_over_time_max_pen: this.Pass.same_stn_over_time_max_pen,
                    other_stn_over_time_limit: this.Pass.other_stn_over_time_limit,
                    other_stn_over_time_pen: this.Pass.other_stn_over_time_pen,
                    other_stn_over_time_max_pen: this.Pass.other_stn_over_time_max_pen,
                    over_travel_pen: this.Pass.over_travel_pen,
                    entry_mismatch_limit: this.Pass.entry_mismatch_limit,
                    entry_mismatch_same_time_pen: this.Pass.entry_mismatch_same_time_pen,
                    entry_mismatch_no_exit_pen: this.Pass.entry_mismatch_no_exit_pen,
                    exit_mismatch_pen: this.Pass.exit_mismatch_pen,
                    entry_exit_control: this.Pass.entry_exit_control,
                    entry_validity_period: this.Pass.entry_validity_period,
                    return_validity_period: this.Pass.return_validity_period,
                    pass_validity_period: this.Pass.pass_validity_period,
                    grace_period: this.Pass.grace_period,
                    trip_count: this.Pass.trip_count,
                    daily_trip_limit: this.Pass.daily_trip_limit,
                    issue_permit: this.Pass.issue_permit,
                    reload_permit: this.Pass.reload_permit,
                    refund_permit: this.Pass.refund_permit,
                    refund_charges: this.Pass.refund_charges,
                    min_sv_topup_amt: this.Pass.min_sv_topup_amt,
                    sv_step_topup_amt: this.Pass.sv_step_topup_amt,
                    min_sv_entry_bal: this.Pass.min_sv_entry_bal,
                    max_sv_bal: this.Pass.max_sv_bal,
                    is_test: this.Pass.is_test,
                })
            }
        },

        methods: {
            updateText(){
                this.form.pass_id =this.form.media_type_id +''+ this.form.product_type_id;
                this.setParam(this.form.product_type_id)
            },
            editPass: function () {
                this.form.post('/pass/edit/'+this.Pass.pass_inv_id)
            },

            setParam: async function (product_id) {

                const res = await axios.post('/api/get/params', {
                    'product_type_id': product_id
                })
                const result = await res.data

                const Parameters = result
                Parameters.disabled.forEach(param => {
                    try { document.getElementById(param.params).hidden = param.params !== 'product_id' }
                    catch(err) { console.log(param.params) }
                })
                Parameters.enabled.forEach(param => {
                    try { document.getElementById(param.params).hidden = false }
                    catch(err) { console.log(param.params) }
                })

            }

        },
        mounted() {

            console.log(this.Pass)
            this.setParam(this.Pass.product_id)

            console.log("permit"+this.Pass.reload_permit)

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput:true
            });
        }

    }
</script>

<style scoped>

</style>
