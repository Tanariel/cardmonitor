<template>
    <div v-if="hasItems">

        <div class="col row">
            <div class="col text-center" ref="wrapper">
                <a :href="item.url" class="">
                    <img :src="item.url" class="img-fluid" alt="" :style="{ height: height + 'px'}">
                </a>
            </div>
        </div>

        <div class="row align-items-center justify-content-center mt-1" v-if="(items_count > 1)">
            <div class="col-md-2" v-for="(image, index) in items">
                <img :src="image.url" class="img-fluid pointer" alt="" @click="item = image" style="height: 100px;">
            </div>
        </div>

    </div>
    <div v-else>
        <div class="alert alert-dark" role="alert">
            {{ $t('image.alerts.no_data') }}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
        },

        mounted() {

            if (this.hasItems) {
                this.height = Math.max(this.$refs.wrapper.clientHeight, 300);
            }

        },

        computed: {
            items_count() {
                return this.items.length;
            },
            hasItems() {
                return (this.items_count > 0);
            },
        },

        data() {
            return {
                item: this.model.images.length ? this.model.images[0] : null,
                items: this.model.images,
                height: 300,
            };
        },
    };
</script>