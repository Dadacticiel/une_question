<template>
    <v-container fluid>
        <div id="messages">
            <div class="d-flex mt-8 animate__animated animate__pulse animate__infinite"
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
            <div>
                <Message v-for="(message, key) in messages" :key="key" :message="message" :new="false"></Message>
                <Message v-for="(message, key) in newMessages" :key="key" :message="message" :new="true"></Message>
                <div v-if="!loading && messages.length === 0 && newMessages.length === 0" class="mt-4 animate__animated animate__fadeInDown" style="font-size: 1.5em; text-align: center;">
                    Aucune question pour le moment :'(
                </div>
            </div>
        </div>
    </v-container>
</template>

<style lang="scss" scoped>
    #messages {
        height: calc(100vh - 93px);
        overflow-y: scroll;
        overflow-x: hidden;
    }
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
            messageEnCours: '',
            sending: false,
            messages: [],
            newMessages: []
        }
    },
    methods: {
        envoyerMessage() {
            if(this.messageEnCours.length > 0) {
                this.sending = true;
                this.$http.post('/publish', {message: this.messageEnCours}).then(response => {
                    this.sending = false;
                    this.messageEnCours = '';
                }, response => {
                    this.sending = false;
                });
            }
        },
        getNouveauMessage() {
            let that = this;

            // Récupération des nouveaux messages
            this.$http.get('/get-new-messages').then(response => {
                // On récupère le scroll maximum
                let scrollMax = document.getElementById('messages').scrollMaxY || (document.getElementById('messages').scrollHeight - document.getElementById('messages').clientHeight);
                let currentScroll = document.getElementById('messages').scrollTop;
                let isAtMaxScroll = currentScroll === scrollMax;

                debugger

                // Ajout des nouveaux messages aux messages déjà récupérés
                this.newMessages = [...this.newMessages, ...response.body];
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();

                if (isAtMaxScroll) {
                    this.scrollToLastMessage();
                }
            }, response => {
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();
            });
        },
        scrollToLastMessage() {
            // On scroll dans 1ms afin que l'élément soit inséré et que le scroll max recalculé
            setTimeout(function () {
                let scrollMax = document.getElementById('messages').scrollMaxY || (document.getElementById('messages').scrollHeight - document.getElementById('messages').clientHeight);
                document.getElementById('messages').scrollTo({top: scrollMax, behavior: 'smooth'});
            }, 1);
        }
    },
    created() {
        // Récupération des messages
        this.$http.get('/get-old-messages').then(response => {
            // get body data
            this.messages = response.body;
            this.loading = false;
            this.scrollToLastMessage();
            // On écoute les nouveaux messages
            this.getNouveauMessage();
        }, response => {
            this.loading = false;
            alert('Erreur lors de la récupération des anciens messages');
        });
    }
}
</script>