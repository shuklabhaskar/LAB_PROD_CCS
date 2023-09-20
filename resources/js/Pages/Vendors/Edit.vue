<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>EDIT</strong>VENDOR</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="card col-md-6">
                    <div class="card-body">

                        <form @submit.prevent="editVendor">

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                    <input  type="text" v-model="form.vendor_name" class="form-control"  placeholder="Vendor Name" >
                                    <div class="text-danger" v-if="errors.vendor_name">{{ errors.vendor_name }}</div>

                                </div>

                                <!--PASS TYPE DESCRIPTION-->
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input   v-model="form.description" class="form-control" />
                                </div>

                            </div>

                            <div class="row mb-2 mb-xl-3">

                                <div class="col-auto d-none d-sm-block">
                                    <Link :href="'/vendors'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward"/> Back</Link>
                                </div>

                                <div class="col-auto ms-auto text-end mt-n1">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-refresh"/>&nbsp;Update</button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-light">
                                            <div class="modal-body m-3 text-center">
                                                <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                                <h3 class="m-2">
                                                    <span>Are you sure! do you want to update this Vendor ?</span></h3>
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
        name: "Edit",
        props:{
            Vendor: Object,
            errors: Object
        },
        layout: Layout,
        components:{
            Link
        },
        data() {
            return {
                form: useForm({
                    vendor_id: this.Vendor.vendor_id,
                    vendor_name: this.Vendor.vendor_name,
                    description: this.Vendor.description,
                })
            }
        },

        methods: {
            editVendor: function () {
                this.form.post('/vendors/edit/' + this.Vendor.vendor_id)
            }
        }
    }
</script>

<style scoped>

</style>
