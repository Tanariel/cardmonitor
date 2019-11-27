<template>
    <tr v-if="isEditing">
        <td class="align-middle">{{ item.item.name }}</td>
        <td class="align-middle text-right">
            <input class="form-control" :class="'quantity_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.quantity_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'quantity_formatted' in errors ? errors.quantity_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">{{ Number(calculated_price).format( 2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-primary" title="Speichern" @click="update"><i class="fas fa-fw fa-save"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="isEditing = false"><i class="fas fa-fw fa-times"></i></button>
            </div>
        </td>
    </tr>
    <tr v-else>
        <td class="align-middle pointer"><a :href="item.item.path">{{ item.item.name }}</a></td>
        <td class="align-middle text-right pointer" @click="isEditing = true">{{ Number(item.quantity).format(2, ',', '.') }} Stück</td>
        <td class="align-middle text-right pointer">{{ Number(item.unit_cost).format(2, ',', '.') }} €/Stück</td>
        <td class="align-middle text-right pointer" @click="isEditing = true">{{ Number(item.quantity * item.unit_cost).format( 2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Speichern" @click="isEditing = true"><i class="fas fa-fw fa-edit"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy"><i class="fas fa-fw fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {

        props : {
            item: {
                required: true,
                type: Object,
            },
            uri: {
                required: true,
                type: String,
            },
        },

        watch: {
            item(newValue) {
                this.form.quantity_formatted = newValue.quantity_formatted;
                this.form.unit_cost_formatted = newValue.unit_cost_formatted;
            },
        },

        computed: {
            calculated_price() {
                return Number(this.form.unit_cost_formatted.replace(',', '.')) * Number(this.form.quantity_formatted.replace(',', '.'));
            }
        },

        data() {
            return {
                errors: {},
                form: {
                    quantity_formatted: this.item.quantity_formatted,
                    unit_cost_formatted: this.item.unit_cost_formatted,
                },
                isEditing: false,
            };
        },

        methods: {
            update() {
                var component = this;
                axios.put(component.uri + '/' + component.item.id, component.form)
                    .then( function (response) {
                        component.isEditing = false;
                        component.errors = {};
                        component.$emit('updated', response.data);
                        Vue.success('Kosten gespeichert.');
                    })
                    .catch(function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Kosten konnte nicht gespeichert werden.');
                });
            },
            destroy() {
                var component = this;
                axios.delete(component.uri + '/' + component.item.id)
                    .then( function (response) {
                        if (response.data.deleted) {
                            Vue.success('Kosten gelöscht')
                            component.$emit("deleted", component.id);
                            return;
                        }

                        Vue.error('Kosten konnten nicht gelöscht werden.');
                    })
            },
        },

    };
</script>