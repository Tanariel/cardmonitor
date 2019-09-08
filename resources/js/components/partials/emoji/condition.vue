<template>
    <i class="fas fa-fw" :class="classes" :title="title"></i>
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
            var classes,
                title;
            switch(this.value) {
                case 'M':
                    classes = 'grin-beam text-success';
                    title = 'Mint';
                    break;
                case 'NM':
                    classes = 'fa-grin text-success';
                    title = 'Near Mint';
                    break;
                case 'EX':
                    classes = 'fa-smile text-warning';
                    title = 'Excelent';
                    break;
                case 'GD':
                    classes = 'fa-meh text-warning';
                    title = 'Good';
                    break;
                case 'LP':
                    classes = 'fa-frown text-danger';
                    title = 'Light Played';
                    break;
                case 'PL':
                    classes = 'fa-frown-open text-danger';
                    title = 'Played';
                    break;
                case 'PO':
                    classes = 'fa-sad-tear text-danger';
                    title = 'Poor';
                    break;
            }

            return {
                classes: classes,
                title: title,
            };
        },

    };
</script>