<template>
    <div class="form-group" style="min-width: 200px;">
        <label for="filter-expansion" v-if="showLabel">Erweiterung</label>
        <v-select class="d-flex align-items-center" :clearable="false" :options="sortedOptions" label="name" :reduce="option => option.id" placeholder="Alle" :value="value" @input="$emit('input', $event)">
            <template v-slot:option="option">
                <expansion-icon :expansion="option" :name-ellipsis="true" v-if="option.id > 0"></expansion-icon>
                <span v-else>{{ option.name }}</span>
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

                var sorted = this.options.sort(compare);

                sorted.unshift({
                    id: 0,
                    name: 'Alle Erweiterungen',
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