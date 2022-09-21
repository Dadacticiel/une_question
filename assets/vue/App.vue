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
        background-color: #b1dbff;
        font-weight: bold;
        font-size: 1.5em;
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