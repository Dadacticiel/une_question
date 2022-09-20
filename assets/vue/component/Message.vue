<template>
    <div :class="'message animate__animated animate__fadeInUp' + (isNew ? ' new' : '')" >
        {{ message.body }}
    </div>
</template>

<style lang="scss" scoped>
    .message {
        background-color: #7fffd44a;
        padding: 10px;
        border-radius: 10px;
        margin: 10px;
        transition: background-color 2s;
    }

    .new {
        background-color: rgba(131, 127, 255, 0.29);
    }
</style>

<script>

export default {
    name: "Message",
    components: {
    },
    created() {
        let that = this;
        if(this.new) {
            debugger
            this.isNew = true;

            setTimeout(function() {
                that.isNew = false;
            }, 200);
        }
    },
    data() {
        return {
            isNew: false
        }
    },
    props: [
        'message',
        'new'
    ]
}
</script>