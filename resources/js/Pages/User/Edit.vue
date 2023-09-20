<template>

    <!--MAIN HEADING-->
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>EDIT </strong> USER</h3>
        </div>
    </div>

    <!--CARD WITH FORM AND BUTTONS-->
    <div class="card">

        <div class="card-body">

            <!--FORM FOR User INPUTS-->
            <form @submit.prevent="editUser">

                <!--FORM INPUTS-->
                <div class="row">

                    <!--USER FIRST NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input  v-model="form.first_name" type="text" class="form-control" placeholder="Enter First Name">
                        <div class="text-danger" v-if="errors.first_name">{{ errors.first_name }}</div>
                    </div>

                    <!--USER MIDDLE NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input  v-model="form.middle_name" type="text" class="form-control" placeholder="Enter Middle Name">
                        <div class="text-danger" v-if="errors.middle_name">{{ errors.middle_name }}</div>
                    </div>


                    <!--USER LAST NAME-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input  v-model="form.last_name" type="text" class="form-control" placeholder="Enter Last Name">
                        <div class="text-danger" v-if="errors.last_name">{{ errors.last_name }}</div>
                    </div>

                    <!--USER DESIGNATION-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <input  v-model="form.designation" type="text" class="form-control" placeholder="Enter Designation Name">
                        <div class="text-danger" v-if="errors.designation">{{ errors.designation }}</div>
                    </div>

                    <!--EMP ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                        <input  v-model="form.emp_id" type="text" class="form-control" placeholder="Enter Employee ID">
                        <div class="text-danger" v-if="errors.emp_id">{{ errors.emp_id }}</div>
                    </div>

                    <!--EMP MOBILE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                        <input  v-model="form.emp_mobile" type="number" min="0" class="form-control" placeholder="Enter Mobile Number">
                        <div class="text-danger" v-if="errors.emp_mobile">{{ errors.emp_mobile }}</div>
                    </div>

                    <!--EMP EMAIL-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Email ID<span class="text-danger">*</span></label>
                        <input  v-model="form.emp_email" type="text" class="form-control" placeholder="Enter Employee Email">
                        <div class="text-danger" v-if="errors.emp_email">{{ errors.emp_email }}</div>
                    </div>

                    <!--USER GENDER-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Emp Gender <span class="text-danger">*</span></label>
                        <select  v-model="form.emp_gender" class="form-control form-select">
                            <option selected value="null">Select Gender</option>
                            <option :value="1">Male</option>
                            <option :value="2">Female</option>
                            <option :value="3">Transgender</option>
                        </select>
                        <div class="text-danger" v-if="errors.emp_gender">{{ errors.emp_gender }}</div>
                    </div>

                    <!--USER DATE OF BIRTH-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input  v-model="form.emp_dob" type="text" class="form-control emp_dob" placeholder="Enter Date of Birth">
                        <div class="text-danger" v-if="errors.emp_dob">{{ errors.emp_dob }}</div>
                    </div>


                    <!--User Description-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Description</label>
                        <input  v-model="form.description" type="text" class="form-control" placeholder="Enter Description">
                        <div class="text-danger" v-if="errors.description">{{ errors.description }}</div>
                    </div>


                    <!--USER LOGIN ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Login ID <span class="text-danger">*</span></label>
                        <input  v-model="form.user_login" type="text" class="form-control" placeholder="Enter Login ID">
                        <div class="text-danger" v-if="errors.user_login">{{ errors.user_login }}</div>
                    </div>

                    <!--USER PASSWORD-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input  v-model="form.user_pwd" type="text" class="form-control" placeholder="Enter Password">
                        <div class="text-danger" v-if="errors.user_pwd">{{ errors.user_pwd }}</div>
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

                    <!--USER ROLE ID-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select v-model="form.user_role_id" class="form-control form-select" name="line_id">
                            <option selected value="null">Select Role</option>
                            <option v-for="userRole in userRoles" :value="userRole.user_role_id">{{ userRole.user_role_name }}</option>
                        </select>
                        <div class="text-danger" v-if="errors.user_role_id">{{ errors.user_role_id }}</div>
                    </div>

                    <!--USER IS TESTER-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Is Test <span class="text-danger">*</span></label>
                        <select v-model="form.is_test" class="form-control form-select" name="test_id">
                            <option selected value="null">Select Tester</option>
                            <option :value="true">YES</option>
                            <option :value="false">NO</option>
                        </select>
                        <div class="text-danger" v-if="errors.is_test">{{ errors.is_test }}</div>
                    </div>

                    <!--USER START DATE-->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input  v-model="form.start_date" type="text" class="form-control flatpickr-minimum" placeholder="Enter Start Date">
                        <div class="text-danger" v-if="errors.start_date">{{ errors.start_date }}</div>
                    </div>

                    <!--USER END DATE-->
                    <div class="mb-3  col-md-4">
                        <label class="form-label">End Date</label>
                        <input  v-model="form.end_date" type="text" class="form-control flatpickr-end_date" placeholder="Enter End Date"/>
                    </div>

                </div>

                <!--BUTTONS-->
                <div class="row mb-2 mb-xl-3">

                    <!--BACK BUTTON-->
                    <div class="col-auto d-none d-sm-block">
                        <Link :href="'/users'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-backward" /> Back</Link>
                    </div>

                    <!--SAVE BUTTON-->
                    <div class="col-auto ms-auto text-end mt-n1">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal"><font-awesome-icon icon="fa-solid fa-refresh" /> Update</button>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-light">
                                <div class="modal-body m-3 text-center">
                                    <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                    <h3 class="m-2"><span>Are you sure! do you want to update this User ?</span></h3>
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
    import {Link,useForm} from "@inertiajs/inertia-vue3";

    export default {
        name: "Edit",
        layout:Layout,

        props: {
            User: Object,
            userRoles:null,
            errors: Object,
        },

        components:{
            Link
        },

        /* FORM DATA */
        data() {
            return {
                form: useForm({
                    first_name: this.User.first_name,
                    middle_name: this.User.middle_name,
                    last_name: this.User.last_name,
                    designation: this.User.designation,
                    emp_id: this.User.emp_id,
                    emp_mobile: this.User.emp_mobile,
                    emp_email: this.User.emp_email,
                    emp_gender: this.User.emp_gender,
                    emp_dob: this.User.emp_dob,
                    description: this.User.description,
                    user_login: this.User.user_login,
                    user_pwd: this.User.user_pwd,
                    status: this.User.status,
                    user_role_id: this.User.user_role_id,
                    start_date: this.User.start_date,
                    end_date: this.User.end_date,
                    is_test: this.User.is_test,
                })
            }
        },

        methods: {
            editUser: function () {
                this.form.post('/users/edit/'+this.User.user_id)
            }
        },

        /*FOR DATE CALENDAR*/
        mounted() {

            $('#example').DataTable();

            flatpickr(".flatpickr-minimum", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                allowInput:true,
            });

            flatpickr(".flatpickr-dob", {
                enableTime: false,
                dateFormat: "Y-m-d",
                allowInput:true,
                yearRange: "-20:+0",
            });

            flatpickr(".flatpickr-end_date", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                allowInput:true,
            });

        },
    }
</script>

<style scoped>

</style>
