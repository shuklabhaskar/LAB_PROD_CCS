<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>CREATE </strong>CARD TYPE</h3>
        </div>
    </div>

    <!--CARD WITH FORM AND BUTTONS-->
    <div class="card">

        <div class="card-body">

            <!--FORM FOR CARD TYPE  INPUTS-->
            <form @submit.prevent="storeCardType">

                <!--FORM INPUTS-->
                <div class="row">

                    <!--MEDIA TYPE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Media Type <span class="text-danger"> *</span></label>
                        <select class="form-control form-select"  v-model="form.media_type_id">
                            <option value="null">Select Media Type</option>
                            <option v-for="MediaType in MediaTypes" :value="MediaType.media_type_id">{{MediaType.media_name}}</option>
                        </select>
                        <div class="text-danger" v-if="errors.media_type_id">{{errors.media_type_id }}</div>
                    </div>

                    <!--PAYMENT SCHEME TYPE-->
                    <template v-if="form.media_type_id == 1 || form.media_type_id == 2">
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Payment Scheme Type <span class="text-danger"> *</span></label>
                            <select class="form-control form-select"  v-model="form.ps_type_id">
                                <option value="null">Select Payment Scheme Type</option>
                                <option v-for="PsType in PsTypes" :value="PsType.ps_type_id">{{PsType.ps_name}}</option>
                            </select>
                            <div class="text-danger" v-if="errors.ps_type_id">{{errors.ps_type_id }}</div>
                        </div>
                    </template>

                    <!-- CARD BIN NUMBER -->
                    <template v-if="form.media_type_id == 1">
                        <!--CARD BIN NUMBER -->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Card BIN Number </label>
                            <input  v-model="form.card_bin_number" type="number" class="form-control" placeholder="Card BIN Number">
                            <div class="text-danger" v-if="errors.card_bin_number">{{ errors.card_bin_number }}</div>
                        </div>
                    </template>

                    <!--CARD PRODUCT ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Card Product ID <span class="text-danger">*</span></label>
                        <input  v-model="form.card_pro_id" type="text" class="form-control" placeholder="Card Product ID">
                        <div class="text-danger" v-if="errors.card_pro_id">{{ errors.card_pro_id }}</div>
                    </div>

                    <!--CARD NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Card Name <span class="text-danger">*</span></label>
                        <input  v-model="form.card_name" type="text" class="form-control" placeholder="Card Name">
                        <div class="text-danger" v-if="errors.card_name">{{ errors.card_name }}</div>
                    </div>

                    <!--DESCRIPTION-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Description</label>
                        <input  v-model="form.description" type="text" class="form-control" placeholder="Enter Description">
                        <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                    </div>

                    <!--STATUS-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select  v-model="form.status" class="form-control form-select">
                            <option selected value="null">Select Status</option>
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                        <div class="text-danger" v-if="errors.status">{{ errors.status }}</div>
                    </div>

                    <!--CARD FEE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Card Fee <span class="text-danger">*</span><span class="text-danger"> &nbsp;&nbsp;&nbsp;&nbsp;Non Refundable </span></label>
                        <input  v-model="form.card_fee" type="number" min="0" class="form-control" placeholder="Card Fee">
                        <div class="text-danger" v-if="errors.card_fee">{{ errors.card_fee }}</div>
                    </div>

                    <!--CARD SECURITY AMOUNT-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Card Security Amount <span class="text-danger">*</span> <span class="text-danger">&nbsp;&nbsp;&nbsp;&nbsp;Refundable / Non Refundable</span></label>
                        <input  v-model="form.card_sec" type="number" min="0" class="form-control" placeholder="Card Security Amount">
                        <div class="text-danger" v-if="errors.card_sec">{{ errors.card_sec }}</div>
                    </div>

                    <!--CARD SECURITY REFUND PERMIT-->
                    <div class="mb-3 col-md-4" id="reload_permit">
                        <label class="form-label">Card Security Refund Permit</label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox" v-model="form.card_sec_refund_permit">
                            </div>
                        </div>
                    </div>

                    <!--CARD SECURITY REFUND CHARGES-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Card Security Refund Charges</label>
                        <div class="input-group mb-3">
                            <input v-if="form.card_sec_refund_permit == true" type="text" class="form-control" v-model="form.card_sec_refund_charges">
                            <input  v-else disabled type="text" class="form-control">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <!--START DATE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input  v-model="form.start_date" type="text" class="form-control flatpickr-minimum" placeholder="Enter Start Date">
                        <div class="text-danger" v-if="errors.start_date">{{ errors.start_date }}</div>
                    </div>

                    <!--END DATE-->
                    <div class="mb-3  col-md-4">
                        <label class="form-label">End Date</label>
                        <input  v-model="form.end_date" type="text" class="form-control flatpickr-end_date" placeholder="Enter End Date"/>
                    </div>

                </div>

                <!--BUTTONS-->
                <div class="row mb-2 mb-xl-3">

                    <!--BACK BUTTON-->
                    <div class="col-auto d-none d-sm-block">
                        <Link :href="'/cardType'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
                    </div>

                    <!--SAVE BUTTON-->
                    <div class="col-auto ms-auto text-end mt-n1">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-save" /> Save</button>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-light">
                                <div class="modal-body m-3 text-center">
                                    <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                    <h3 class="m-2"><span>Are you sure! do you want to create New Card Type ?</span></h3>
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

</template>

<script>
    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";
    export default {
        props: {
            errors:Object,
            svPass:Array,
            tpPass:Array,
            MediaTypes:Array,
            PsTypes:Array
        },
        name: "Create",
        layout: Layout,
        components: {
            Link
        },

        data (){
            return {
                form: useForm({
                    ps_type_id:null,
                    media_type_id: null,
                    card_bin_number: null,
                    card_name: null,
                    description: null,
                    status: null,
                    card_pro_id: null,
                    card_fee: null,
                    card_sec: null,
                    card_sec_refund_permit: null,
                    card_sec_refund_charges: null,
                    sv_pass_id:null,
                    tp_pass_id:null,
                    start_date: null,
                    end_date: null,
                })
            }
        },

        methods: {
            storeCardType: function () {
                this.$inertia.post('/cardType', this.form)
            }
        },

        /* FOR DATE CALENDAR */
        mounted() {

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput:true
            });

            flatpickr(".flatpickr-end_date", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput:true
            });

        },

    }
</script>

<style scoped>

</style>
