<template>

    <div class="wrapper">

        <Sidebar/>

        <div class="main">

            <Topbar/>

            <div v-if="$page.props.flash.message" style="position:absolute;top:0;right:0">
                <Notification :message="$page.props.flash.message"/>
            </div>

            <main class="content">

                <slot/>

            </main>

            <Footer/>

        </div>

    </div>

</template>

<script>

    import Footer from "../Component/Footer";
    import Sidebar from "../Component/Sidebar";
    import Topbar from "../Component/Topbar";
    import Notification from "../Component/Notification"

    export default {
        name: "Layout",
        components: {Topbar, Sidebar, Footer, Notification}
    }

</script>

<style scoped>


</style>
