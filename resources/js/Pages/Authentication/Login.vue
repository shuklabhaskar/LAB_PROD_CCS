<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center mt-7">

            <div class="card col-4 text-center mt-7">
                <div class="card-body">
                    <form @submit.prevent="checkUser">
                        <h1 >LOGIN</h1>

                        <div class="mb-3">
                            <input required  class="form-control" type="number"  v-model="form.login_id" placeholder="Enter Login ID">
                            <div class="text-danger" v-if="errors.login_id">{{ errors.login_id }}</div>
                        </div>

                        <div class="mb-3">
                            <input required  class="form-control" type="password" v-model="form.password" placeholder="Enter Password Here">
                            <div class="text-danger" v-if="errors.login_id">{{ errors.login_id }}</div>

                        </div>

                        <button type="submit" class="btn btn-primary m-2 btn-lg" data-bs-dismiss="modal">Login</button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import {Link, useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "Login",
    components: {
        Link
    },
    props:{
        errors:null
    },
    data() {
        return {
            form: useForm({
                login_id: null,
                password: null,
            })
        }
    },

    methods: {
        checkUser() {
            this.$inertia.post('/', this.form)
        },
    },

}

</script>

<style scoped>

</style>
