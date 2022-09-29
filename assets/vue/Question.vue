<template>
    <v-container fluid>
        <div class="new_message">
            <v-container fluid>
            <v-row>
                <v-col sm="12" lg="8" class="ma-auto d-sm-flex d-md-flex d-lg-flex d-xl-flex text-center">
                    <v-text-field
                        class="ma-auto"
                        label="J'ai une question !"
                        hide-details="auto"
                        placeholder="Exemple : Comment faites-vous pour être aussi charismatique ?"
                        v-model="messageEnCours"
                        v-on:keyup.enter="envoyerMessage"
                        solo
                    ></v-text-field>
                    <v-btn class="ml-sm-2 ml-md-2 ml-lg-2 ml-xl-2 mt-6 my-sm-auto my-md-auto my-lg-auto my-xl-auto" depressed
                           color="primary"
                           :loading="sending"
                           :disabled="messageEnCours.length === 0 || sending" @click="envoyerMessage">Envoyer</v-btn>
                </v-col>
            </v-row>
            <div class="reactions">
                <div class="text-center mt-10 pb-2">
                    <b>Réactions live :</b>
                </div>
                <div class="d-flex ma-auto">
                    <div class="ma-auto">
                        <v-btn depressed large
                               color="primary" @click="react('applause')">
                            <v-icon color="yellow" style="font-size: 3em;">
                                mdi-hand-clap
                            </v-icon>
                        </v-btn>
                        <v-btn depressed large
                               color="primary" @click="react('heart')">
                            <v-icon color="pink" style="font-size: 3em;">
                                mdi-heart
                            </v-icon>
                        </v-btn>
                    </div>
                </div>
            </div>
            </v-container>
        </div>
    </v-container>
</template>

<style lang="scss" scoped>
    .new_message {
        margin-top: 50px;
        display: flex;
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
            messageEnCours: '',
            sending: false
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
        react(type) {
            this.$http.post('/'+type).then(response => {
            }, response => {
            });
        },
    },
    created() {
    }
}
</script>