<template>
    <div class="form-group">
        <label for="filter-game" v-if="showLabel">Spiel</label>
        <select class="form-control" id="filter-game" v-model="value" @change="$emit('input', value)">
            <option v-for="(option, key) in options" :value="key">{{ option }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        props: [
            'initialValue',
            'options',
            'showLabel',
        ],

        data () {
            return {
                value: this.initialValue || 1,
            };
        },
    };
</script>