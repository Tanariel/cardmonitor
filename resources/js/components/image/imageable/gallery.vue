<template>
    <div v-if="items.length">
        <div class="row mb-1">
            <div class="col">
                <a :href="item.url">
                    <img :src="item.url" class="img-fluid" alt="">
                </a>
            </div>

        </div>

        <div class="row" v-if="(items.length > 1)">
            <div class="col-md-2" v-for="(image, index) in items">
                <img :src="image.url" class="img-fluid pointer" alt="" @click="item = image">
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

        data() {
            return {
                item: this.model.images[0],
                items: this.model.images,
            };
        },
    };
</script>