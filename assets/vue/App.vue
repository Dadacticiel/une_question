<template>
    <v-app>
        <v-app-bar app>
            Une question ?
            <v-spacer></v-spacer>
            <QR></QR>
        </v-app-bar>


        <!-- Sizes your content based upon application components -->
        <v-main>
          <router-view></router-view>

          <!-- Provides the application the proper gutter -->

        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </v-app>
</template>

<style lang="scss">
    html {
        overflow: hidden;
    }

    header {
        background-color: #ffe0b6 !important;
        font-weight: bold;
        font-size: 1.5em;
    }

    .v-main__wrap {
        background-color: antiquewhite;
    }

</style>

<script>
    import Messages from "./Messages";
    import QR from "./QR";
    export default {
        name: "App",
        components: {QR, Messages},
        created() {

        }
    }
</script>