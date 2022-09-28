<template>
    <v-container fluid>
        <div class="new_message">
            <v-container fluid>
            <v-row>
                <v-col sm="12" lg="8" class="d-flex ma-auto">
                    <v-text-field
                        class="ma-auto"
                        label="J'ai une question !"
                        hide-details="auto"
                        placeholder="Exemple : Comment faites-vous pour être aussi charismatique ?"
                        v-model="messageEnCours"
                        v-on:keyup.enter="envoyerMessage"
                        solo
                    ></v-text-field>
                    <v-btn class="ml-2 my-auto" depressed
                           color="primary"
                           :loading="sending"
                           :disabled="messageEnCours.length === 0 || sending" @click="envoyerMessage">Envoyer</v-btn>
                </v-col>
            </v-row>
            <v-row>
                <v-col xs="12" class="text-center mt-4 pb-0">
                    <b>Réactions live :</b>
                </v-col>
                <v-col sm="12" lg="8" class="d-flex ma-auto">
                    <div class="ma-auto">
                        <v-btn depressed
                               color="primary" @click="react('applause')">
                            <v-icon color="yellow">
                                mdi-hand-clap
                            </v-icon>
                        </v-btn>
                        <v-btn depressed
                               color="primary" @click="react('heart')">
                            <v-icon color="pink">
                                mdi-heart
                            </v-icon>
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
            </v-container>
        </div>
    </v-container>
</template>

<style lang="scss" scoped>
    .new_message {
        margin: 50px;
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
            this.sending = true;
            this.$http.post('/'+type).then(response => {
                this.sending = false;
            }, response => {
                this.sending = false;
            });
        },
    },
    created() {
    }
}
</script>