<template>
    <div>
        <div class="d-flex mt-8"
             v-if="loading">
            <div class="ml-auto">
                <v-progress-circular
                    indeterminate
                    color="primary"
                    size="50"
                ></v-progress-circular>
            </div>
            <div class="mr-auto my-auto ml-2">
                Chargement des messages...
            </div>
        </div>
        <Message v-for="message in messages" :message="message"></Message>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>

import Message from "./component/Message";
export default {
    name: "Messages",
    components: {
        Message
    },
    data() {
        return {
            loading: true,
            messages: []
        }
    },
    methods: {
        getNouveauMessage() {
            let that = this;

            // Récupération des nouveaux messages
            this.$http.get('/get-new-messages').then(response => {
                // Ajout des nouveaux messages aux messages déjà récupérés
                this.messages = [...this.messages, ...response.body];
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();
            }, response => {
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();
            });
        }
    },
    created() {
        // Récupération des messages
        this.$http.get('/get-old-messages').then(response => {
            // get body data
            this.messages = response.body;
            this.loading = false;
            this.getNouveauMessage();
        }, response => {
            // error callback
        });
    }
}
</script>