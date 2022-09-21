<template>
    <v-container fluid>
        <v-card v-if="!unlocked" class="mx-auto my-12"  max-width="374">
            <v-card-title>
              Espace protégé
            </v-card-title>
          <div class="pa-5">
            <v-text-field
                type="password"
                label="Mot de passe"
                v-model="password"
                v-on:keyup.enter="checkPassword"
            ></v-text-field>
            <div class="d-flex">
              <v-btn class="ml-auto" color="primary" @click="checkPassword">Login</v-btn>
            </div>
          </div>
        </v-card>
        <div id="messages" v-if="unlocked">
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
       font-size: 2em;
    }
</style>

<script>

import Message from "./component/Message";
const pagePassword = 7376222959731466;

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
            password: '',
            messages: [],
            newMessages: [],
            unlocked: false
        }
    },
    methods: {
        checkPassword() {
            if(this.hash(this.password) === pagePassword) {
                this.getMessages();
                this.unlocked = true;
            }
        },
        hash(str, seed = 0) {
            let h1 = 0xdeadbeef ^ seed,
                h2 = 0x41c6ce57 ^ seed;
            for (let i = 0, ch; i < str.length; i++) {
              ch = str.charCodeAt(i);
              h1 = Math.imul(h1 ^ ch, 2654435761);
              h2 = Math.imul(h2 ^ ch, 1597334677);
            }

            h1 = Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^ Math.imul(h2 ^ (h2 >>> 13), 3266489909);
            h2 = Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^ Math.imul(h1 ^ (h1 >>> 13), 3266489909);

            return 4294967296 * (2097151 & h2) + (h1 >>> 0);
        },
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
        },
        getMessages() {
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
}
</script>