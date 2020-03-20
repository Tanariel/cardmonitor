<template>
    <i class="fas fa-fw fa-circle" :class="classes" :title="value"></i>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true,
            },
        },

        computed: {
            classes() {
                return 'text-' + this.value.toLowerCase()
            }
        },
    };
</script>