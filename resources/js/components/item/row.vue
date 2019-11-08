<template>
    <tr>
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle pointer" @click="link">{{ item.name }}</td>
        <td class="align-middle pointer" @click="link">{{ Number(item.unit_cost).toFixed(2) }} €</td>

        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Bearbeiten" @click="link" v-if="item.isEditable"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy" v-if="item.isDeletable"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {

        props: ['item', 'uri', 'selected'],

        data () {
            return {
                id: this.item.id,
            };
        },

        methods: {
            destroy() {
                var component = this;
                axios.delete(component.item.path)
                    .then(function (response) {
                        if (response.data.deleted) {
                            component.$emit("deleted", component.id);
                            // Vue.success('Kosten wurden gelöscht.');
                        }
                        else {
                            // Vue.error('Kosten konnten nicht gelöscht werden.');
                        }
                    });
            },
            link () {
                location.href = this.item.path + '/edit';
            }
        },
    };
</script>