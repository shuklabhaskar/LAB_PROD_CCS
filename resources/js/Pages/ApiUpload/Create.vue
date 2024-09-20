<template>

    <div class="container-fluid p-0">

        <!--HEADING-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>API</strong> UPLOAD</h3>
            </div>

        </div>

        <!--CARD WITH FORM AND BUTTONS-->
        <div class="card col-9 d-flex">

            <div class="card-body">

                <!--FORM FOR API INPUTS-->
                <form @submit.prevent="storeApiRoute">

                    <!--FORM INPUTS-->
                    <div class="row">

                        <!--API NAME-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="api_name">API Name <span
                                class="text-danger"> *</span></label>
                            <input id="api_name" v-model="form.api_name" name="api_name" class="form-control"
                                   placeholder="Enter Api Name"/>
                            <div class="text-danger" v-if="errors.api_name">{{ errors.api_name }}</div>
                        </div>

                        <!--API ROUTE-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="api_route">Api Route <span
                                class="text-danger"> *</span></label>
                            <input id="api_route" v-model="form.api_route" name="api_route" class="form-control"
                                   placeholder="Enter Api Route"/>
                            <div class="text-danger" v-if="errors.api_route">{{ errors.api_route }}</div>
                        </div>

                        <!--API DESCRIPTION-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label" for="api_description">Api Description</label>
                            <input id="api_description" v-model="form.api_description" name="api_route"
                                   class="form-control" placeholder="Enter Api Description"/>
                            <div class="text-danger" v-if="errors.api_description">{{ errors.api_description }}</div>
                        </div>

                        <!--API REQUEST TYPE-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">API Request Type <span class="text-danger"> *</span></label>
                            <select class="form-control form-select" v-on:change="updateText"
                                    v-model="form.api_request_type">
                                <option value="null">Select API Request Type</option>
                                <option value="1">GET</option>
                                <option value="2">POST</option>
                                <option value="3">DELETE</option>
                            </select>
                            <div class="text-danger" v-if="errors.api_request_type">{{ errors.api_request_type }}</div>
                        </div>

                        <!--PRODUCT TYPE-->
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Product Type <span class="text-danger"> *</span></label>
                            <select class="form-control form-select" v-on:change="updateText"
                                    v-model="form.product_type_id">
                                <option value="null">Select Product Type</option>
                                <option v-for="ProductType in ProductTypes" :value="ProductType.product_type_id">
                                    {{ ProductType.product_name.toUpperCase() }}
                                </option>
                            </select>
                            <div class="text-danger" v-if="errors.product_type_id">{{ errors.product_type_id }}</div>
                        </div>

                    </div>

                    <!-- SAVING DATA -->
                    <!--BUTTONS-->
                    <div class="row mb-2 mb-xl-3">


                        <!--BACK BUTTON-->
                        <div class="col-auto d-none d-sm-block">
                            <Link :href="'/api/endPoint'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
                        </div>

                        <!--SAVE BUTTON-->
                        <div class="col-auto ms-auto text-end mt-n1">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">
                                <font-awesome-icon icon="fa-solid fa-save"/>&nbsp;Save
                            </button>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-light">
                                    <div class="modal-body m-3 text-center">
                                        <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                        <h3 class="m-2"><span>Are you sure! Do you want to add new Api Route ?</span>
                                        </h3>
                                        <a data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                                        <button type="submit" class="btn btn-primary m-2 btn-lg"
                                                data-bs-dismiss="modal">YES
                                        </button>
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

export default {
    name: "Create",
    layout: Layout,

    components: {
        Link
    },
    props: {
        ProductTypes: Array,
        errors: Object,
    },
    data() {
        return {
            form: useForm({
                api_name: null,
                api_route: null,
                api_description: null,
                api_request_type: null,
                product_type_id: null
            })
        }
    },
    methods: {
        storeApiRoute() {
            this.$inertia.post('/api/endPoint/store', this.form)
        }
    }
}
</script>
