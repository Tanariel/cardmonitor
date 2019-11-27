<template>
    <div>
        <div class="row">
            <div class="col d-flex align-items-start">
                <div class="form-group mb-0 mr-1">
                    <select class="form-control" v-model="form.expansion_id" @change="create">
                        <option :value="0">Erweiterung hinzufügen</option>
                        <option :value="expansion.id" v-for="expansion in availableExpansions">{{ expansion.name }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div v-if="isLoading" class="mt-3 p-5">
            <center>
                <span style="font-size: 48px;">
                    <i class="fas fa-spinner fa-spin"></i><br />
                </span>
                Lade Daten..
            </center>
        </div>
        <div class="table-responsive mt-3" v-else-if="items.length">
            <table class="table table-hover table-striped bg-white">
                <thead>
                    <tr>
                        <th>Erweiterung</th>
                        <th class="text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :model="model" :item="item" :index="index" :key="item.id" :uri="uri" @deleted="remove(index)" @updated="updated(index, $event)"></row>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>Keine Sets vorhanden</center></div>
    </div>
</template>

<script>
    import row from "./row.vue";

    export default {

        components: {
            row,
        },

        props: {
            model: {
                required: true,
                type: Object,
            },
        },

        data () {
            return {
                uri: this.model.path + '/content',
                isLoading: false,
                items: [],
                expansions: [],
                filter: {

                },
                form: {
                    expansion_id: 0,
                },
                errors: {},
            };
        },

        mounted() {

            var component = this;
            axios.get('/expansion', {
                params: component.filter
            })
                .then(function (response) {
                    component.expansions = response.data;
                })
                .catch(function (error) {
                    Vue.error('Sets konnten nicht geladen werden!');
                    console.log(error);
                });

            this.fetch();

        },

        computed: {
            availableExpansions() {
                const expansion_ids = this.items.reduce( function (total, content) {
                    total.push(content.storagable.id);
                    return total;
                }, []);

                var available = this.expansions.filter(function (expansion) {
                    return (expansion_ids.indexOf(expansion.id) == -1);
                });

                function compare(a, b) {
                    if (a.name < b.name) {
                        return -1;
                    }

                    if (a.name > b.name) {
                        return 1;
                    }

                    return 0;
                }

                return available.sort(compare);
            },
        },

        methods: {
            create() {
                var component = this;
                axios.post(component.uri, component.form)
                    .then(function (response) {
                        component.items.push(response.data);
                        component.form.expansion_id = 0;
                        Vue.success('Zuordnung hinzugefügt.');
                    })
                    .catch( function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Set konnte nicht erstellt werden!');
                });
            },
            fetch() {
                var component = this;
                component.isLoading = true;
                axios.get(component.uri, {
                    params: component.filter
                })
                    .then(function (response) {
                        component.items = response.data;
                        component.isLoading = false;
                    })
                    .catch(function (error) {
                        Vue.error('Sets konnten nicht geladen werden!');
                        console.log(error);
                    });
            },
            updated(index, item) {
                Vue.set(this.items, index, item);
            },
            remove(index) {
                this.items.splice(index, 1);
                Vue.success('Set gelöscht.');
            },
        },
    };
</script>