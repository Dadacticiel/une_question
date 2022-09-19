<template>
    <v-app>
        <v-app-bar app>
            Une question ?
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main>

            <!-- Provides the application the proper gutter -->
            <v-container fluid>
                <Messages></Messages>
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </v-app>
</template>

<style lang="scss" scoped>

</style>

<script>
    import Messages from "./Messages";
    export default {
        name: "App",
        components: {Messages},
        created() {

        }
    }
</script>