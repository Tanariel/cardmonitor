<template>
    <i class="fas fa-fw" :class="classes" :title="title" v-if="title"></i>
    <span v-else>-</span>
</template>

<script>
    export default {
        props: {
            value: {
                type: Number,
                required: true,
            },
        },

        data() {
            var classes,
                title;
            switch(this.value) {
                case 1:
                    classes = 'fa-grin-beam text-success';
                    title = this.$t('order.evaluations.1');
                    break;
                case 2:
                    classes = 'fa-grin text-success';
                    title = this.$t('order.evaluations.2');
                    break;
                case 3:
                    classes = 'fa-meh text-warning';
                    title = this.$t('order.evaluations.3');
                    break;
                case 4:
                    classes = 'fa-frown-open text-danger';
                    title = this.$t('order.evaluations.4');
                    break;
            }

            return {
                classes: classes,
                title: title,
            };
        },

    };
</script>