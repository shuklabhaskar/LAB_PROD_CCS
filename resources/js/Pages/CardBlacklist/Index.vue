<template>

    <div class="container-fluid p-0">

        <!--HEADING AND BUTTON-->
        <div class="row mb-2 mb-xl-3">

            <!--MAIN HEADING-->
            <div class="col-auto d-none d-sm-block">
                <h3><strong>CARD</strong> BLACKLIST</h3>
            </div>

        </div>

        <div class="row">

            <!--CARD WITH FORM AND BUTTONS-->
            <div class="card col-md-7" style="margin-right: 5px;">

                <div class="card-body">

                    <!--FORM FOR STATION INPUTS-->
                    <form @submit.prevent="cardBlacklist">

                        <!--FORM INPUTS-->
                        <div class="row">

                            <!--MEDIA TYPE-->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Media Type <span class="text-danger"> *</span></label>
                                <select class="form-control form-select" v-model="form.media_type_id">
                                    <option value="null">Select Media Type</option>
                                    <option v-for="MediaType in MediaTypes" :value="MediaType.media_type_id">
                                        {{ MediaType.media_name }}
                                    </option>
                                </select>
                                <div class="text-danger" v-if="errors.media_type_id">{{ errors.media_type_id }}</div>
                            </div>

                            <!--BLACKLIST REASON-->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Blacklist Reason <span class="text-danger"> *</span></label>
                                <select class="form-control form-select" v-model="form.ms_blk_reason_id">
                                    <option value="null">Select Blacklist Reason</option>
                                    <option v-for="BlacklistReason in BlacklistReasons"
                                            :value="BlacklistReason.ms_blk_reason_id">{{ BlacklistReason.reason }}
                                    </option>
                                </select>
                                <div class="text-danger" v-if="errors.ms_blk_reason_id">
                                    {{ errors.ms_blk_reason_id }}
                                </div>
                            </div>


                            <!--START SERIAL NUMBER USING AS ENGRAVED ID-->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Start Serial <span class="text-danger"> *</span></label>
                                <input type="number" class="form-control" v-model="form.engraved_id"
                                       placeholder="Enter Start Serial Number"/>
                                <div class="text-danger" v-if="errors.engraved_id">{{ errors.engraved_id }}</div>
                            </div>

                            <!--END SERIAL NUMBER-->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">End Serial</label>
                                <input type="number" class="form-control" v-model="form.end_serial"
                                       placeholder="Enter End Serial Number"/>
                                <div class="text-danger" v-if="errors.end_serial">{{ errors.end_serial }}</div>
                            </div>

                        </div>

                        <!-- SAVING DATA -->
                        <!--BUTTONS-->
                        <div class="row mb-2 mb-xl-3">

                            <!--SAVE BUTTON-->
                            <div class="col-auto ms-auto text-end mt-n1">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                    <font-awesome-icon icon="fa-solid fa-save"/>&nbsp;
                                    Save
                                </button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-light">
                                        <div class="modal-body m-3 text-center">
                                            <i class="fas fa-question-circle display-5 text-center text-primary"></i>
                                            <h3 class="m-2"><span>Are you sure! Do you want to add new cards for card Blacklist ?</span>
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

            <!-- SEARCH ENGRAVED ID AND GET DETAILS ID IT IS BLACKLISTED OR NOT -->
            <div class="card col-md-4 ">

                <div class="card-body">

                    <!--FORM FOR STATION INPUTS-->
                    <form @submit.prevent="searchCard">

                        <!--FORM INPUTS-->
                        <div class="col">

                            <!--ENGRAVED ID FOR SEARCH-->
                            <div class="mb-3 col-md-12 mt-4">
                                <label class="form-label">Search Engraved ID <span class="text-danger"> *</span></label>
                                <input required type="number" class="form-control" v-model="form.search_engraved_id"
                                       placeholder="Enter Engraved ID here for search"/>
                            </div>

                            <!--SAVE BUTTON-->
                            <div class="d-flex justify-content-center mt-2">
                                <button type="submit" class="btn btn-outline-success">
                                    <font-awesome-icon icon="fa-solid fa-magnifying-glass"/>&nbsp;
                                    Search Engraved ID
                                </button>
                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div class="container mt-5"  v-if="searchResult">
            <form @submit.prevent="deleteCard">
                <div class="card shadow-sm">
                    <div class="row no-gutters">
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <img :src="bl_user" class="card-img-left" alt="Image">
                        </div>
                        <div class="col-md-6 card-body-right">
                            <div class="card-body">
                                <div class="card-details">
                                    <h3 class="mb-4">BLACKLISTED CARD DETAIL</h3>
                                    <table>
                                        <tr>
                                            <td><strong>S.NO:</strong></td>
                                            <td>1</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Media Type:</strong></td>
                                            <td>Close Loop</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Serial Number:</strong></td>
                                            <td>{{ searchResult.engraved_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Blacklist Reason:</strong></td>
                                            <td>{{ searchResult.reason }}</td>
                                        </tr>
                                    </table>
                                    <div class="action-buttons">
                                        <button v-on:click="getDeleteItemId(searchResult.cl_blk_id)"
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                title="Delete">
                                            <i class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>
                                    </div>
                                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-light">
                                                <div class="modal-body text-center py-5">
                                                    <i class="fas fa-question-circle text-primary"></i>
                                                    <h3 class="my-4">Are you sure you want to delete this card from the blacklist?</h3>
                                                    <a @click="reloadPage" data-bs-dismiss="modal" class="btn btn-outline-primary m-2 btn-lg">NO</a>
                                                    <button type="submit" class="btn btn-danger btn-lg" data-bs-dismiss="modal">YES</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Show message if no result found -->
        <div class="row" v-if="searchMessage && !searchResult">
            <div class="col-12 text-center">
                <div class="d-flex align-items-center justify-content-center" role="alert">
                    <img :src="not_found" alt="No results" id="not_found_image" class="img-fluid me-3">
                </div>
            </div>
        </div>



    </div>

</template>

<script>

import Layout from "../Base/Layout";
import {Link, useForm} from "@inertiajs/inertia-vue3";
import axios from "axios";

export default {
    name: "Index",
    layout: Layout,

    components: {
        Link
    },
    props: {
        MediaTypes: Array,
        BlacklistReasons: Array,
        errors: Object,
    },
    data() {
        return {
            form: useForm({
                media_type_id: null,
                ms_blk_reason_id: null,
                engraved_id: null,
                end_serial: null,
                search_engraved_id: null,
                deleteItemId: Number,
            }),
            searchResult: null,
            searchMessage: null,
            bl_user: '/Img/bl_user.png',
            not_found: '/Img/cl_blacklist_nf.png'
        }
    },
    mounted() {

        $("#cardBlacklist").DataTable({
            responsive: true,
            "paging": true
        });
    },

    methods: {

        reloadPage() {
            window.location.reload();
        },

        cardBlacklist() {
            this.$inertia.post('/card/blacklist', this.form)
        },

        deleteCard: function () {
            this.form.post('/card/blacklist/delete/' + this.deleteItemId)
            window.alert('Card Deleted successfully');
            window.location.reload();
        },

        getDeleteItemId: function (id) {
            this.deleteItemId = id
        },

        async searchCard() {
            try {
                const response = await axios.get(`/api/get/blacklisted/cardDetail/${this.form.search_engraved_id}`);
                if (response.data.status) {
                    this.searchResult = response.data.data;
                    this.searchMessage = null;
                } else {
                    this.searchResult = null;
                    this.searchMessage = response.data.message;
                }
            } catch (error) {
                this.searchResult = null;
                this.searchMessage = 'An error occurred while Searching for the card.';
            }
        }

    },

}

</script>

<style>
.card-img-left {
    width: 250px;
    height: 250px;
    object-fit: cover;
}
.card-body-right {
    padding: 20px;
}
.card-details table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
.card-details table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}
.action-buttons {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}
#not_found_image{
    width: 500px;
    height: 400px;
    object-fit: cover;
}
.modal-content {
    border-radius: 10px;
}
.modal-body i {
    font-size: 3rem;
}
</style>
