<template>
    <tr v-if="isEditing">
        <td class="align-middle pointer">{{ (index + 1) }}</td>
        <td class="align-middle pointer">
            <input class="form-control" :class="'start' in errors ? 'is-invalid' : ''" type="number" v-model.number="form.start" @keydown.enter="create">
            <div class="invalid-feedback" v-text="'start' in errors ? errors.start[0] : ''"></div>
        </td>
        <td class="align-middle pointer">
            <input class="form-control" :class="'end' in errors ? 'is-invalid' : ''" type="number" v-model.number="form.end" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'end' in errors ? errors.end[0] : ''"></div>
        </td>
        <td class="align-middle pointer">
            <input class="form-control" :class="'quantity_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.quantity_formatted" @keydown.enter="create">
            <div class="invalid-feedback" v-text="'quantity_formatted' in errors ? errors.quantity_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-primary" title="Speichern" @click="update"><i class="fas fa-fw fa-save"></i></button>
                <button type="button" class="btn btn-secondary" title="Abbrechen" @click="isEditing = false"><i class="fas fa-fw fa-times"></i></button>
            </div>
        </td>
    </tr>
    <tr v-else>
        <td class="align-middle pointer" @click="isEditing = true">{{ (index + 1) }}</td>
        <td class="align-middle text-right pointer" @click="isEditing = true">{{ item.start }}</td>
        <td class="align-middle text-right pointer" @click="isEditing = true">{{ item.end }}</td>
        <td class="align-middle text-right pointer" @click="isEditing = true">{{ item.quantity_formatted }}</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Bearbeiten" @click="isEditing = true"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {

        props: ['item', 'uri', 'index'],

        data () {
            return {
                id: this.item.id,
                isEditing: false,
                form: {
                    start: Number(this.item.start),
                    end: Number(this.item.end),
                    effective_from_formatted: this.item.effective_from_formatted,
                    quantity_formatted: this.item.quantity_formatted,
                },
                errors: {},
            };
        },

        methods: {
            destroy() {
                var component = this;
                axios.delete('/quantity/' + component.id)
                    .then(function (response) {
                        if (response.data.deleted) {
                            component.$emit("deleted", component.id);
                            Vue.success('Kosten wurden gelöscht.');
                        }
                        else {
                            Vue.error('Kosten konnten nicht gelöscht werden.');
                        }
                });
            },
            update() {
                var component = this;
                axios.put('/quantity/' + component.id, component.form)
                    .then( function (response) {
                        component.errors = {};
                        component.isEditing = false;
                        component.$emit('updated', response.data);
                        Vue.success('Staffel gespeichert.');
                    })
                    .catch(function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Staffel konnten nicht gespeichert werden.');
                });
            },
        },
    };
</script>