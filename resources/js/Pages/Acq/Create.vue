<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>CREATE </strong> ACQUIRER PARAM</h3>
        </div>
    </div>

    <!--CARD WITH FORM AND BUTTONS-->
    <div class="card">

        <div class="card-body">

            <!--FORM FOR ACQUIRER INPUTS-->
            <form @submit.prevent="storeAcquire">

                <!--FORM INPUTS-->
                <div class="row">

                    <!--OPERATOR NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Operator ID <span class="text-danger">*</span></label>
                        <input v-model="form.operator_id" type="text" class="form-control" placeholder="Enter Operator ID">
                        <div class="text-danger" v-if="errors.operator_id">{{ errors.operator_id }}</div>
                    </div>

                    <!--ACQUIRER ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Acquirer ID <span class="text-danger">*</span></label>
                        <input v-model="form.acq_id" type="text" class="form-control" placeholder="Enter Acquirer ID">
                        <div class="text-danger" v-if="errors.acq_id">{{ errors.acq_id }}</div>
                    </div>

                    <!--ACQUIRER NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Acquirer Name <span class="text-danger">*</span></label>
                        <input v-model="form.acq_name" type="text" class="form-control" placeholder="Enter Acquirer Name">
                        <div class="text-danger" v-if="errors.acq_name">{{ errors.acq_name }}</div>
                    </div>

                    <!--ACQUIRER MERCHANT ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Acquirer MID <span class="text-danger">*</span></label>
                        <input v-model="form.acq_mid" type="text" class="form-control" placeholder="Enter Acquirer Merchant ID">
                        <div class="text-danger" v-if="errors.acq_mid">{{ errors.acq_mid }}</div>
                    </div>

                    <!--ACQUIRER CLIENT ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Client ID <span class="text-danger">*</span></label>
                        <input v-model="form.client_id" type="text" class="form-control" placeholder="Enter Client ID">
                        <div class="text-danger" v-if="errors.client_id">{{ errors.client_id }}</div>
                    </div>


                    <!--LINE ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Line ID <span class="text-danger">*</span></label>
                        <select v-model="form.line_id" class="form-control form-select" name="line_id">
                            <option selected :value=null>Select Line</option>
                            <option v-for="line in Lines" :value="line.line_id">{{ line.line_name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.acq_mid">{{ errors.line_id }}</div>
                    </div>

                    <!-- EMV TYPE NAME -->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">EMV Type <span class="text-danger">*</span></label>
                        <select v-model="form.emv_type_id" class="form-control form-select" name="emv_type">
                            <option selected :value="null">Select EMV Type</option>
                            <option v-for="EmvType in EmvTypes" :value="EmvType.emv_type_id">{{ EmvType.emv_reader_name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.emv_type_id">{{ errors.emv_type_id }}</div>
                    </div>


                    <!-- Description -->
                    <div class="mb-3 col-md-4">
                        <label class="form-label"> Description</label>
                        <input  v-model="form.description" type="text" class="form-control" placeholder="Enter Description ">
                        <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                    </div>

                </div>

                <!--BUTTONS-->
                <div class="row mb-2 mb-xl-3">

                    <!--BACK BUTTON-->
                    <div class="col-auto d-none d-sm-block">
                        <Link :href="'/acq'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
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
                                    <h3 class="m-2"><span>Are you sure! do you want to create new Acquirer Param ?</span></h3>
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
        name: "Create",
        layout: Layout,
        props:{
            errors:Object,
            Lines:Array,
            EmvTypes:Array,
        },
        components: {
            Link
        },

        /*FORM DATA*/
        data() {
            return {
                form: useForm({
                    operator_id: null,
                    acq_id: null,
                    acq_name: null,
                    acq_mid: null,
                    client_id: null,
                    line_id: null,
                    description: null,
                    emv_type_id: null,

                })
            }
        },

        methods: {
            storeAcquire: function () {
                this.$inertia.post('/acq', this.form)
            }
        },

    }
</script>

<style scoped>

</style>
