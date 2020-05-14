<template>
    <div class="form-group" style="min-width: 200px;">
        <label for="filter_skryfall_expansion" v-if="showLabel"> {{ 'Skryfall ' + $t('app.expansion') }}</label>
        <v-select class="d-flex align-items-center" name="skryfall_expansion_id" :clearable="false" :options="sortedOptions" label="name" :reduce="option => option.code" placeholder="Alle" :value="value" @input="$emit('input', $event)">
            <template v-slot:option="option">
                <span>{{ option.name }}</span>
            </template>
        </v-select>
    </div>

</template>

<script>
    import expansionIcon from '../expansion/icon.vue';
    import vSelect from 'vue-select';

    export default {

        components: {
            expansionIcon,
            vSelect,
        },

        props: {
            initialValue: {
                required: false,
            },
            options: {
                required: true,
            },
            showLabel: {
                required: false,
                default: true,
            },
        },

        watch: {
            initialValue(newValue) {
                this.value = newValue;
            },
        },

        computed: {
            sortedOptions: function() {
                function compare(a, b) {
                    if (a.name < b.name) {
                        return -1;
                    }

                    if (a.name > b.name) {
                        return 1;
                    }

                    return 0;
                }

                var component = this;

                var sorted = this.options.filter(function (option) {
                    return true;
                }).sort(compare);

                sorted.unshift({
                    code: 0,
                    name: 'Wie Magic Erweiterung',
                });

                return sorted;
            },
        },

        data () {
            return {
                value: this.initialValue || 0,
            };
        },
    };
</script>