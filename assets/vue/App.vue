<template>
    <div>
        <h1>COUCOU</h1>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    // var amqp = require('amqplib/callback_api');

    export default {
        name: "App",
        created() {
            alert('GOGO');
            amqp.connect('amqp://localhost', function(error0, connection) {
                if (error0) {
                    throw error0;
                }
                connection.createChannel(function(error1, channel) {
                    if (error1) {
                        throw error1;
                    }
                    var queue = 'hello';
                    var msg = 'Hello world';

                    channel.assertQueue(queue, {
                        durable: false
                    });

                    channel.sendToQueue(queue, Buffer.from(msg));
                    console.log(" [x] Sent %s", msg);
                });
            });
        }
    }
</script>