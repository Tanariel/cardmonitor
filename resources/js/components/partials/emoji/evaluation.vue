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
                    title = 'Sehr gut';
                    break;
                case 2:
                    classes = 'fa-grin text-success';
                    title = 'Gut';
                    break;
                case 3:
                    classes = 'fa-meh text-warning';
                    title = 'Neutral';
                    break;
                case 4:
                    classes = 'fa-frown-open text-danger';
                    title = 'Schlecht';
                    break;
            }

            return {
                classes: classes,
                title: title,
            };
        },

    };
</script>