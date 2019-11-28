<template>
    <tr>
        <td class="align-middle text-left">{{ item.storagable.name }}</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
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
                errors: {},
            };
        },

        methods: {
            destroy() {
                var component = this;
                axios.delete('/content/' + component.id)
                    .then(function (response) {
                        if (response.data.deleted) {
                            component.$emit("deleted", component.id);
                            Vue.success('Erweiterung wurde gelöscht.');
                        }
                        else {
                            Vue.error('Erweiterung konnte nicht gelöscht werden.');
                        }
                });
            },
        },
    };
</script>