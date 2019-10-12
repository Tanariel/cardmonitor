<template>
    <div class="form-group">
        <label for="filter-rarity">Erweiterung</label>
        <select class="form-control" id="filter-rarity" v-model="value" @change="$emit('input', value)">
            <option value="0">Alle</option>
            <option v-for="(option, key) in options" :value="key">{{ option }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        props: [
            'initialValue',
            'options',
        ],

        data () {
            return {
                value: this.initialValue || 0,
            };
        },
    };
</script>