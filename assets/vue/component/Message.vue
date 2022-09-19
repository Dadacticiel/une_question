<template>
    <div>
        {{ message.body }}
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>

export default {
    name: "Message",
    components: {
    },
    created() {

    },
    props: [
        'message'
    ]
}
</script>