<template>

    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CREATE </strong>TID TYPE</h3>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="card col-md-6">

                    <div class="card-body">

                        <form @submit.prevent="storeTids">

                            <div class="row">

                                <!--EMV BOX SERIAL NO-->
                                <div class="mb-3  col-md-6">
                                    <label class="form-label">EMV Serial NO <span class="text-danger">*</span></label>
                                    <input  v-model="form.emv_serial_no" class="form-control" name="emv_serial_no" placeholder="Enter EMV Serial Number"/>
                                    <div class="text-danger" v-if="errors.emv_serial_no">{{ errors.emv_serial_no }}</div>
                                </div>

                                <!--EMV DEVICE ID-->
                                <div class="mb-3  col-md-6">
                                    <label class="form-label">EMV Device ID </label>
                                    <input  v-model="form.device_id" class="form-control" name="emv_device_id" placeholder="Enter EMV Device ID"/>
                                    <div class="text-danger" v-if="errors.device_id">{{ errors.device_id }}</div>
                                </div>

                                <!--EMV TID-->
                                <div class="mb-3  col-md-6">
                                    <label class="form-label">EMV TID <span class="text-danger">*</span></label>
                                    <input  v-model="form.emv_tid" class="form-control" name="emv_tid" placeholder="Enter EMV TID ">
                                    <div class="text-danger" v-if="errors.emv_tid">{{ errors.emv_tid }}</div>
                                </div>


                                <!-- EMV TYPE NAME -->
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">EMV Type <span class="text-danger">*</span></label>
                                    <select v-model="form.emv_type_id" class="form-control form-select" name="emv_type">
                                        <option  selected :value="null">Select EMV Type</option>
                                        <option v-for="EmvType in EmvTypes" :value="EmvType.emv_type_id">{{ EmvType.emv_reader_name }}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.emv_type_id">{{ errors.emv_type_id }}</div>
                                </div>

                                <!-- EMV TYPE NAME -->
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Acquirer <span class="text-danger">*</span></label>
                                    <select v-model="form.acq_id" class="form-control form-select" name="Acquirer">
                                        <option selected :value="null">Select Acquirer</option>
                                        <option v-for="Acquirer in Acquirers" :value="Acquirer.acq_id">{{ Acquirer.acq_name }}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.acq_id">{{ errors.acq_id }}</div>
                                </div>

                            </div>

                            <div class="row mb-2 mb-xl-3">

                                <div class="col-auto d-none d-sm-block">
                                    <Link :href="'/tids'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back
                                    </Link>
                                </div>

                                <div class="col-auto ms-auto text-end mt-n1">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-save" /> Save
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-light">
                                            <div class="modal-body m-3 text-center">
                                                <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                                <h3 class="m-2">
                                                    <span>Are you sure! do you want to Create this TID ?</span></h3>
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
            errors: Object,
            EmvTypes: Array,
            Acquirers: Array,
        },

        data() {
            return {
                form: useForm({
                    emv_tid: null,
                    device_id: null,
                    emv_serial_no: null,
                    emv_type_id: null,
                    acq_id: null,
                })
            }
        },



        methods: {
            storeTids() {
                this.$inertia.post('/tids', this.form)
            },
        },


    }

</script>

<style scoped>

</style>
