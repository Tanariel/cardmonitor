<template>
    <tr>
        <td class="align-middle text-center"><i class="fas fa-grip-lines pointer sort"></i></td>
        <td class="align-middle text-center">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle text-center">
            <i class="fas fa-play pointer text-success" @click="deactivate" v-if="item.active == 1"></i>
            <i class="fas fa-pause pointer text-danger" @click="activate" v-if="item.active == 0"></i>
        </td>
        <td class="align-middle pointer" @click="link">{{ item.name }}</td>
        <td class="align-middle pointer" @click="link">
            <expansion-icon :expansion="item.expansion" v-if="item.expansion_id"></expansion-icon>
            <span v-else>Alle</span>
        </td>
        <td class="align-middle text-center pointer" @click="link">
            <rarity :value="item.rarity" v-if="item.rarity"></rarity>
            <span v-else>Alle</span>
        </td>
        <td class="align-middle pointer" @click="link">{{ item.base_price_formatted }} * {{ item.multiplier_formatted }}</td>
        <td class="align-middle text-right pointer" @click="link">{{ item.articleStats.count }}</td>
        <td class="align-middle text-right pointer" @click="link">{{ item.articleStats.price_formatted }} €</td>
        <td class="align-middle text-right pointer" @click="link">{{ item.articleStats.price_rule_formatted }} €</td>
        <td class="align-middle text-right pointer" @click="link" v-html="item.articleStats.difference_icon + ' ' + item.articleStats.difference_percent_formatted + '%'"></td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <a :href="item.editPath" type="button" class="btn btn-secondary" title="Bearbeiten" @click="link"><i class="fas fa-edit"></i></a>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    import expansionIcon from '../expansion/icon.vue';
    import rarity from '../partials/emoji/rarity.vue';

    export default {

        components: {
            expansionIcon,
            rarity,
        },

        props: [ 'item', 'uri', 'selected' ],

        data () {
            return {
                id: this.item.id,
            };
        },

        methods: {
            activate() {
                var component = this;
                axios.post(component.item.path + '/activate')
                    .then(function (response) {
                        component.$emit("updated", response.data);
                        Vue.success('Regel <b>' + response.data.name + '</b> wurde aktiviert.');
                    });
            },
            deactivate() {
                var component = this;
                axios.delete(component.item.path + '/activate')
                    .then(function (response) {
                        component.$emit("updated", response.data);
                        Vue.success('Regel <b>' + response.data.name + '</b> wurde deaktiviert.');
                    });
            },
            destroy() {
                var component = this;
                axios.delete(component.item.path)
                    .then(function (response) {
                        if (response.data.deleted) {
                            component.$emit("deleted", component.id);
                            Vue.success('Regel wurde gelöscht.');
                        }
                        else {
                            Vue.error('Regel konnte nicht gelöscht werden.');
                        }
                    });
            },
            link () {
                location.href = this.item.path;
            },
        },
    };
</script>