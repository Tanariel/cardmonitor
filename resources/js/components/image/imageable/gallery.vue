<template>
    <div v-if="items.length">

        <div class="col row">
            <div class="col text-center" ref="wrapper">
                <a :href="item.url" class="">
                    <img :src="item.url" class="img-fluid" alt="" :style="{ height: height + 'px'}">
                </a>
            </div>
        </div>

        <div class="row align-items-center justify-content-center mt-1" v-if="(items.length > 1)">
            <div class="col-md-2" v-for="(image, index) in items">
                <img :src="image.url" class="img-fluid pointer" alt="" @click="item = image" style="height: 100px;">
            </div>
        </div>

    </div>
    <div v-else>
        <div class="alert alert-dark" role="alert">
            Keine Bilder vorhanden.
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

            this.height = Math.max(this.$refs.wrapper.clientHeight, 300);

        },

        data() {
            return {
                item: this.model.images[0],
                items: this.model.images,
                height: 0,
            };
        },
    };
</script>