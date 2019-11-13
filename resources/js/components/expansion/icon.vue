<template>
    <div class="d-flex align-items-center">
        <span class="mr-2" style="display: inline-block; width: 21px; height: 21px; background-image: url('/storage/expansionicons/expicons.png');" :style="{ 'background-position': bachgroundPosition }"></span>
        <span>{{ expansion.name }}</span>
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