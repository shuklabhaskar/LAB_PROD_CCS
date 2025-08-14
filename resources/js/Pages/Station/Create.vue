<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>CREATE </strong> STATION</h3>
        </div>
    </div>

    <!--CARD WITH FORM AND BUTTONS-->
    <div class="card">

        <div class="card-body">

            <!--FORM FOR STATION INPUTS-->
            <form @submit.prevent="storeStation">

                <!--FORM INPUTS-->
                <div class="row">

                    <!--STATION NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Station Name<span class="text-danger">*</span></label>
                        <input  v-model="form.stn_name" type="text" class="form-control" placeholder="Station Name">
                        <div class="text-danger" v-if="errors.stn_name">{{ errors.stn_name }}</div>
                    </div>

                    <!--DESCRIPTION-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Description</label>
                        <input  v-model="form.description" type="text" class="form-control" placeholder="Enter Description">
                        <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                    </div>

                    <!--STATUS-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Status<span class="text-danger">*</span></label>
                        <select  v-model="form.status" class="form-control form-select">
                            <option selected value="null">Select Status</option>
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                        <div class="text-danger" v-if="errors.status">{{ errors.status }}</div>
                    </div>

                    <!--LINE ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Line<span class="text-danger">*</span></label>
                        <select v-model="form.line_id" class="form-control form-select" name="line_id">
                            <option selected value="null">Select Line</option>
                            <option v-for="line in Lines" :value="line.line_id">{{ line.line_name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.line_id">{{ errors.line_id }}</div>
                    </div>

                    <!--COMPANY ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Company<span class="text-danger">*</span></label>
                        <select v-model="form.company_id" class="form-control form-select" name="line_id">
                            <option selected value="null">Select Company</option>
                            <option v-for="company in Companies" :value="company.company_id">{{ company.company_name }}
                            </option>
                        </select>
                        <div class="text-danger" v-if="errors.company_id">{{ errors.company_id }}</div>
                    </div>

                    <!--STATION SHORT NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Short Name<span class="text-danger">*</span></label>
                        <input  v-model="form.stn_short_name" type="text" class="form-control" placeholder="Short Name">
                        <div class="text-danger" v-if="errors.stn_short_name">{{ errors.stn_short_name }}</div>
                    </div>

                    <!--STATION NATIONAL LANGUAGE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">National Name<span class="text-danger">*</span></label>
                        <input  v-model="form.stn_national_lang" type="text" class="form-control" placeholder="National Name">
                        <div class="text-danger" v-if="errors.stn_national_lang">{{ errors.stn_national_lang }}</div>
                    </div>

                    <!--STATION REGIONAL LANGUAGE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Regional Name<span class="text-danger">*</span></label>
                        <input  v-model="form.stn_regional_lang" type="text" class="form-control" placeholder="Regional Name">
                        <div class="text-danger" v-if="errors.stn_regional_lang">{{ errors.stn_regional_lang }}</div>
                    </div>

                    <!--CORD X-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">X-Coordinate<span class="text-danger">*</span></label>
                        <input  v-model="form.cord_x" type="number" class="form-control" min="0" placeholder="Cord X">
                        <div class="text-danger" v-if="errors.cord_x">{{ errors.cord_x }}</div>
                    </div>

                    <!--CORD Y-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Y-Coordinate<span class="text-danger">*</span></label>
                        <input  v-model="form.cord_y" type="number" class="form-control" min="0" placeholder="Cord Y">
                        <div class="text-danger" v-if="errors.cord_y">{{ errors.cord_y }}</div>
                    </div>

                    <!--START DATE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Start Date<span class="text-danger">*</span></label>
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
                        <Link :href="'/station/inventory'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
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
                                    <h3 class="m-2"><span>Are you sure! do you want to create new station ?</span></h3>
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

/*IMPORTS*/
import Layout from "../Base/Layout";
import {Link} from "@inertiajs/inertia-vue3";
import {useForm} from '@inertiajs/inertia-vue3'

export default {
    props: {
        Companies: Array,
        Lines: Array,
        errors: Object,
        EndLocation:Array
    },
    layout: Layout,
    name: "create",
    components: {
        Link
    },

    /*FORM DATA*/
    data() {
        return {
            form: useForm({
                stn_name: null,
                description: null,
                status: null,
                line_id: null,
                company_id: null,
                stn_short_name: null,
                stn_national_lang: null,
                stn_regional_lang: null,
                cord_x: null,
                cord_y: null,
                start_date: null,
                end_date: null,
            })
        }
    },

    methods: {
        storeStation: function () {
            this.$inertia.post('/stations', this.form)

        }
    },

    /*FOR DATE CALENDER*/
    mounted() {
        flatpickr(".flatpickr-minimum", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            allowInput:true
        });

        $('#example').DataTable();

        flatpickr(".flatpickr-end_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            allowInput:true
        });

    },
}


</script>

