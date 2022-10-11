<template>
    <div>
        <div id="qr_fixed" v-if="$route.path == '/questions'">
            <qrcode-vue class="ma-auto" :value="value" :size="size" level="H" />
        </div>

        <v-dialog
            v-model="dialog"
            width="500"
        >
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="red lighten-2"
                    dark
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon>
                        mdi-qrcode-scan
                    </v-icon>
                </v-btn>
            </template>

            <v-card>
                <v-card-title class="text-h5 grey lighten-2">
                    QR Code
                </v-card-title>

                <v-card-text class="mt-4" style="font-size: 2em;">
                    Vous avez une question ?
                </v-card-text>

                <v-divider></v-divider>

                <div class="d-flex mt-4">
                    <qrcode-vue class="ma-auto" :value="value" :size="size" level="H" />
                </div>



                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary"
                        text
                        @click="dialog = false"
                    >
                        Fermer
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>

</template>

<style lang="scss" scoped>
    #qr_fixed {
        position: fixed;
        right: 15px;
        top: 80px;
    }
</style>

<script>

import QrcodeVue from 'qrcode.vue';
export default {
    name: "QR",
    components: {
        QrcodeVue
    },
    data() {
        return {
            dialog: false,
            value: '',
            size: 400,
        }
    },
    created() {
        this.value = window.location.origin;
        window.addEventListener('resize', this.checkWindowSize)
        this.checkWindowSize();
    },
    methods: {
        checkWindowSize() {
            let viewport_width = window.innerWidth;
            if(viewport_width < 400+48) {
                this.size = viewport_width-48;
            } else {
                this.size = 400;
            }
        }
    }
}
</script>