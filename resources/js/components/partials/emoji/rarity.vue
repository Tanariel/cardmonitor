<template>
    <i class="fas fa-fw fa-circle" :class="classes" :title="title"></i>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true,
            },
        },

        data() {
            var text = 'text-' + this.value.toLowerCase(),
                title = this.value;

            return {
                classes: text,
                title: title,
            };
        },

    };
</script>