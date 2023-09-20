<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>USER</strong> INVENTORY</h3>
            </div>

            <!--CREATE BUTTON-->
            <div class="col-auto ms-auto text-end mt-n1">
                <i class="fa-thin fa-00"></i>
                <Link :href="'users/create'" class="btn btn-outline-primary"><font-awesome-icon icon="fa-solid fa-plus" /> Create New User</Link>
            </div>

        </div>

        <!--CARD AND TABLE DATA-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="user_table" class="table table-striped" style="width:100%">

                            <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>FIRST NAME</th>
                                <th>EMPLOYEE ID</th>
                                <th>ROLE</th>
                                <th>LOGIN ID</th>
                                <th>STATUS</th>
                                <th>EMAIL ID</th>
                                <th>ACTIONS</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(user,user_id) in Users" :key="user_id">
                                <td>{{user_id+1}}</td>
                                <td>{{user.first_name}}</td>
                                <td>{{user.emp_id}}</td>
                                <td>{{user.user_role_name}}</td>
                                <td>{{user.user_login}}</td>
                                <td v-if="user.status === true ">
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td v-else>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td>{{user.emp_email}}</td>
                                <td>
                                    <Link type="button" :href="'/users/edit/' + user.user_id" class="btn btn-sm btn-icon btn-primary rounded" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                        <font-awesome-icon icon="fa-solid fa-edit" />
                                    </Link>
                                </td>
                            </tr>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    import Layout from "../Base/Layout";
    import {Link} from "@inertiajs/inertia-vue3";

    export default {
        name: "Index",
        layout:Layout,
        components:{
            Link
        },
        props:{
            Users:Array,
        },
        mounted() {

            $("#user_table").DataTable({
                responsive  : true,
                "paging"    : true,
            });
        }
    }
</script>

<style scoped>

</style>
