<template>
    <div>
        <div class="row">
            <div class="col">
                <button class="btn btn-primary" @click="create"><i class="fas fa-plus-square"></i></button>
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
                        <th>#</th>
                        <th class="text-right">Von Karten</th>
                        <th class="text-right">Bis Karten</th>
                        <th class="text-right">Einheiten</th>
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
        <div class="alert alert-dark mt-3" v-else><center>Keine Staffelung vorhanden</center></div>
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
                uri: this.model.path + '/quantity',
                isLoading: false,
                items: this.model.quantities,
                filter: {

                },
                form: {
                    start: 1,
                    end: 9999,
                    quantity_formatted: '1',
                    effective_from_formatted: '01.01.1970 00:00',
                },
                errors: {},
            };
        },

        computed: {
            maxEnd() {
                return (this.items.length == 0 ? 0 : Math.max.apply(Math, this.items.map(function(item) { return item.end; })));
            },
            differenceLastQuantity() {
                var length = this.items.length;
                if (length == 0) {
                    return 4;
                }
                var lastQuantity = this.items[(length - 1)];

                return (lastQuantity.end - lastQuantity.start);
            }
        },

        methods: {
            create() {
                var component = this;
                component.form.start = (component.maxEnd + 1);
                component.form.end = (component.form.start + component.differenceLastQuantity);
                axios.post(component.uri, component.form)
                    .then(function (response) {
                        component.items.push(response.data);
                    })
                    .catch( function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Staffel konnte nicht erstellt werden!');
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
                        Vue.error('Staffeln konnten nicht geladen werden!');
                        console.log(error);
                    });
            },
            updated(index, item) {
                Vue.set(this.items, index, item);
            },
            remove(index) {
                this.items.splice(index, 1);
                Vue.success('Staffel gelöscht.');
            },
        },
    };
</script>