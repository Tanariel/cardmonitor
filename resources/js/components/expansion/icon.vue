<template>
    <div class="d-flex align-items-center justify-content-space-between">
        <span class="expansion-icon mr-2" :style="{ 'background-position': bachgroundPosition }"></span>
        <span class="expansion-name">{{ expansion.name }}</span>
    </div>
</template>

<script>
    export default {

        props: {
            expansion: {
                required: true,
                type: Object,
            },
        },

        computed: {
            bachgroundPosition() {
                return '-' + ((this.expansion.icon % 10) * 21) +'px -' + (Math.floor(this.expansion.icon / 10) * 21) + 'px';
            }
        },

    };
</script>

<style>
    .expansion-icon {
        display: inline-block;
        width: 21px;
        height: 21px;
        background-image: url('/storage/expansionicons/expicons.png');
    }

    .expansion-name {
        word-break: break-all;
    }
</style>