<template>
    <div class="form-group">
        <label for="filter-game" v-if="showLabel">Spiel</label>
        <select class="form-control" id="filter-game" v-model="value" @change="$emit('input', value)">
            <option :value="0" v-if="optionAll">Alle</option>
            <option v-for="(option, key) in options" :value="key">{{ option }}</option>
        </select>
    </div>
</template>

<script>
    export default {

        props: {
            initialValue: {
                required: false,
                default: 0,
            },
            options: {
                required: true,
            },
            showLabel: {
                required: false,
                default: true,
            },
            optionAll: {
                required: false,
                default: true,
            }
        },

        data () {
            return {
                value: this.initialValue || 0,
            };
        },
    };
</script>