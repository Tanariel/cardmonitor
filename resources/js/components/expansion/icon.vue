<template>
    <div class="d-flex align-items-center justify-content-space-between" v-if="showName">
        <span class="expansion-icon mr-2" :style="{ 'background-position': bachgroundPosition }" v-if="expansion.game_id == 1"></span>
        <span class="expansion-name" :class="{'text-overflow-ellipsis': nameEllipsis}">{{ expansion.name }}</span>
    </div>
    <span class="expansion-icon" :title="expansion.name" :style="{ 'background-position': bachgroundPosition }" v-else></span>
</template>

<script>
    export default {

        props: {
            expansion: {
                required: true,
                type: Object,
            },
            showName: {
                required: false,
                default: true,
            },
            nameEllipsis: {
                required: false,
                default: false,
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
        flex-grow: 0;
        flex-shrink: 0;
        flex-basis: 21px;
        width: 21px;
        height: 21px;
        background-image: url('/storage/expansionicons/expicons.png');
    }

    .expansion-name {
        word-break: break-all;
    }
</style>