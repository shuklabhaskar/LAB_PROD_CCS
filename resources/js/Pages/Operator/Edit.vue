<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>EDIT </strong> OPERATOR</h3>
        </div>
    </div>

    <!--MAIN CARD LAYOUT FOR EDITING ALL VALUES EXCEPT PASSWORD-->
    <div class="card col-md-6">

        <div class="card-body">

            <form @submit.prevent="editOperator">

                <!--FORM INPUTS-->
                <div class="row">

                    <!--OPERATOR ID-->
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Operator ID<span class="text-danger">*</span></label>
                        <input v-model="form.operator_id" type="number" class="form-control" min="1" placeholder="Enter Operator ID">
                        <div class="text-danger" v-if="errors.operator_id">{{ errors.operator_id }}</div>
                    </div>

                    <!--OPERATOR NAME-->
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Operator Name<span class="text-danger">*</span></label>
                        <input v-model="form.operator_name" type="text" class="form-control" placeholder="Enter Operator Name">
                        <div class="text-danger" v-if="errors.operator_name">{{ errors.operator_name }}</div>
                    </div>

                    <!--USERNAME-->
                    <div class="mb-3 col-md-6">
                        <label class="form-label">User Name<span class="text-danger">*</span></label>
                        <input v-model="form.user_name" type="text" class="form-control" placeholder="Enter User Name">
                        <div class="text-danger" v-if="errors.user_name">{{ errors.user_name }}</div>
                    </div>

                </div>

                <!--BUTTONS-->
                <div class="row mb-2 mb-xl-3">

                    <!--BACK BUTTON-->
                    <div class="col-auto d-none d-sm-block">
                        <Link :href="'/operators'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
                    </div>

                    <!--SAVE BUTTON-->
                    <div class="col-auto ms-auto text-end mt-n1">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-refresh"/> Update</button>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-light">
                                <div class="modal-body m-3 text-center">
                                    <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                    <h3 class="m-2"><span>Are you sure! do you want to update this Operator ?</span></h3>
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


    <!-- EDIT PASSWORD ONLY -->
    <div class="card col-md-6">

        <div class="card-body">

            <form @submit.prevent="updatePassword">

                <!--FORM INPUTS-->
                <div class="row">

                    <h4 style="font-style: italic">Change Password Only</h4>

                    <!--USER PASSWORD-->
                    <div class="mb-3 col-md-12">
                        <label class="form-label">User Password<span class="text-danger">*</span></label>
                        <input v-model="form.user_password" type="password" class="form-control" placeholder="Enter Password Here">
                        <div class="text-danger" v-if="errors.user_password">{{ errors.user_password }}</div>
                    </div>

                </div>

                <!--BUTTON-->
                <div class="row mb-2 mb-xl-3">

                    <!--UPDATE PASSWORD BUTTON-->
                    <div class="col-auto ms-auto text-end mt-n1">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal1"><font-awesome-icon icon="fa-solid fa-refresh"/> Update Password</button>
                    </div>

                    <!-- MODAL -->
                    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-light">
                                <div class="modal-body m-3 text-center">
                                    <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                    <h3 class="m-2"><span>Are you sure! do you want to update the Password ?</span></h3>
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
    props:{
        Operator: Object,
        errors: Object,
    },

    name: "Edit",
    layout: Layout,
    components:{
        Link
    },

    data() {
        return {
            form: useForm({
                operator_id: this.Operator.operator_id,
                operator_name: this.Operator.operator_name,
                user_name: this.Operator.user_name,
                user_password: this.Operator.user_password,
            })
        }
    },

    methods: {
        /* FUNCTION FOR UPDATING ALL VALUES EXCEPT PASSWORD*/
        editOperator: function () {
            this.form.post('/operator/edit/'+this.Operator.operator_id)
        },

        /* FUNCTION FOR UPDATING PASSWORD */
        updatePassword: function () {
            this.form.post('/operator/edit/password/'+this.Operator.operator_id)
        }
    }
}
</script>
