<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>FIRMWARE</strong>&nbsp;UPLOAD</h3>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <div>

                    <div class="card">

                        <div class="card-body">

                            <form @submit.prevent="storeProducts">

                                <div class="row">

                                    <!--SELECT FIRMWARE-->
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Select Firmware<span class="text-danger">*</span></label>
                                        <select v-model="form.firmware_id" class="form-control form-select">
                                            <option selected value="null">Select Firmware For</option>
                                            <option :value="1">AG</option>
                                            <option :value="2">TOM</option>
                                            <option :value="6">EDC</option>
                                        </select>
                                        <div class="text-danger" v-if="errors.firmware_id">{{ errors.firmware_id }}</div>
                                    </div>

                                    <!--&lt;!&ndash;ENTER FIRMWARE VERSION&ndash;&gt;
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Enter Firmware Version</label>
                                        <input v-model="form.firmware_version" class="form-control" placeholder="Enter Firmware Version">
                                        <div class="text-danger" v-if="errors.firmware_version">{{ errors.firmware_version }}</div>
                                    </div>-->

                                    <!--DESCRIPTION FOR FIRMWARE-->
                                    <div class="mb-3 col-8">
                                        <label class="form-label">Description</label>
                                        <input  v-model="form.description" class="form-control" name="description" placeholder="Enter Description of Firmware"/>
                                        <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                                    </div>

                                    <!--UPLOAD FIRMWARE FILE-->
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <div class="form-group files">
                                                <label class="mb-3">Upload Your Firmware File </label>
                                                <input @change="onFileChange" type="file" class="mb-3 form-control">
                                                <div class="text-danger" v-if="errors.file">{{ errors.file }}</div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!--BUTTONS-->
                                <div class="row mb-2 mb-xl-3">

                                    <!--BACK BUTTON-->
                                    <div class="col-auto d-none d-sm-block">
                                        <Link :href="'/dashboard'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back
                                        </Link>
                                    </div>

                                    <!--SAVE BUTTON-->
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
                                                    <h3 class="m-2"><span>Are you sure! do you want to Upload this Firmware ?</span></h3>
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

    </div>
</template>

<script>
    import Layout from "../Base/Layout";
    import {Link, useForm} from "@inertiajs/inertia-vue3";

    export default {
        layout: Layout,
        components: {
            Link
        },
        props: {
            errors: Object,
        },

        data() {
            return {
                form: useForm({
                    firmware_id: null,
                    firmware_version: null,
                    description: null,
                    file: null,
                })
            }
        },

        methods: {

            /*FUNCTION FOR FILE UPLOAD OF FIRMWARE*/
           onFileChange: function (e) {
                this.form.file = e.target.files[0]
            },

            storeProducts() {
                this.$inertia.post('/saveFile', this.form)
            },

        },
    }
</script>

<style scoped>

    .files input {
        outline: 2px dashed #0036ff;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 120px 0px 85px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }

    .files input:focus {
        outline: 2px dashed #0036ff;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        border: 1px solid #0036ff;
    }

    .files {
        position: relative
    }

    .files:after {
        pointer-events: none;
        position: absolute;
        top: 60px;
        left: 0;
        width: 50px;
        right: 0;
        height: 56px;
        content: "";
        background-image: url(https://www.flaticon.com/free-icon/file_1091223?term=upload&page=1&position=12&origin=search&related_id=1091223);
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
    }

    .color input {
        background-color: #0036ff;
    }

    .files:before {
        position: absolute;
        bottom: 10px;
        left: 0;
        pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #0036ff;
        font-weight: 600;
        text-transform: capitalize;
        text-align: center;
    }

</style>
