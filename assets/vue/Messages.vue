<template>
    <v-container fluid>
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
            <Message v-for="message in messages" :message="message" :new="false"></Message>
            <Message v-for="message in newMessages" :message="message" :new="true"></Message>
        </div>
    </v-container>
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
            messages: [],
            newMessages: []
        }
    },
    methods: {
        getNouveauMessage() {
            let that = this;

            // Récupération des nouveaux messages
            this.$http.get('/get-new-messages').then(response => {
                // On récupère le scroll maximum
                let scrollMax = window.scrollMaxY || (document.documentElement.scrollHeight - document.documentElement.clientHeight);
                let currentScroll = document.documentElement.scrollTop || document.body.scrollTop;
                let isAtMaxScroll = currentScroll === scrollMax;

                // Ajout des nouveaux messages aux messages déjà récupérés
                this.newMessages = [...this.newMessages, ...response.body];
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();

                if(isAtMaxScroll) {
                    this.scrollToLastMessage();
                }
            }, response => {
                // On écoute à nouveau l'arrivée de nouveaux messages
                that.getNouveauMessage();
            });
        },
        scrollToLastMessage() {
            // On scroll dans 1ms afin que l'élément soit inséré et que le scroll max recalculé
            setTimeout(function() {
                let scrollMax = window.scrollMaxY || (document.documentElement.scrollHeight - document.documentElement.clientHeight);
                window.scrollTo({top: scrollMax, behavior: 'smooth'});
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