<template>
    <div class="container-fluid p-0">

        <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CREATE</strong> USER PRIVILEGE</h3>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="card col-md-6">

                    <div class="card-body">

                        <form @submit.prevent="editUserRole">


                            <div class="row">

                                <!--USER ROLE NAME-->
                                <div class="mb-3">
                                    <label class="form-label">User Role Name<span class="text-danger">*</span></label>
                                    <input  v-model="form.user_role_name" class="form-control" name="user_role_name" placeholder="User Role Name">
                                    <div class="text-danger" v-if="errors.user_role_name">{{ errors.user_role_name }}</div>
                                </div>

                                <!--USER ROLE DESCRIPTION-->
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input  v-model="form.description" class="form-control" name="description" placeholder="Enter Description"/>
                                    <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                                </div>

                            </div>

                            <div class="row mb-2 mb-xl-3">

                                <div class="col-auto d-none d-sm-block">
                                    <Link :href="'/userRole'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back
                                    </Link>
                                </div>

                                <div class="col-auto ms-auto text-end mt-n1">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-refresh" /> Update
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-light">
                                            <div class="modal-body m-3 text-center">
                                                <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                                <h3 class="m-2">
                                                    <span>Are you sure! do you want to Update this User Role ?</span></h3>
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
        layout:Layout,
        components:{
            Link
        },
        props:{
            userRole : Object,
            errors:Object
        },
        /*FORM DATA*/
        data() {
            return {
                form: useForm({
                    user_role_name: this.userRole.user_role_name,
                    description: this.userRole.description,
                })
            }
        },
        methods: {
            editUserRole: function () {
                this.form.post('/userRole/edit/'+this.userRole.user_role_id)
            }
        },
    }
</script>

<style scoped>

</style>
