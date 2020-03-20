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

        mounted() {
            this.setData(this.value);
        },

        watch: {
            value(newValue) {
                this.setData(newValue);
            },
        },

        data() {
            return {
                classes: '',
                title: '',
            };
        },

        methods: {
            setData(value) {
                switch(value) {
                    case 'MT':
                        this.classes = 'fa-grin-beam text-success';
                        this.title = 'Mint';
                        break;
                    case 'NM':
                        this.classes = 'fa-grin text-success';
                        this.title = 'Near Mint';
                        break;
                    case 'EX':
                        this.classes = 'fa-smile text-warning';
                        this.title = 'Excelent';
                        break;
                    case 'GD':
                        this.classes = 'fa-meh text-warning';
                        this.title = 'Good';
                        break;
                    case 'LP':
                        this.classes = 'fa-frown text-danger';
                        this.title = 'Light Played';
                        break;
                    case 'PL':
                        this.classes = 'fa-frown-open text-danger';
                        this.title = 'Played';
                        break;
                    case 'PO':
                        this.classes = 'fa-sad-tear text-danger';
                        this.title = 'Poor';
                        break;
                }
            },
        },

    };
</script>