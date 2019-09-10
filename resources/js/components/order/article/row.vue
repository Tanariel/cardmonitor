<template>
    <tr>
        <td class="align-middle pointer" @click="toShow">{{ (index + 1) }}</td>
        <td class="align-middle text-center pointer" @click="toShow"><i class="fas fa-fw" :class="item.state_icon" :title="item.state_comments"></i></td>
        <td class="align-middle"><i class="fas fa-image pointer" @mouseover="show($event)" @mouseout="$emit('hide')"></i></td>
        <td class="align-middle pointer" @click="toShow">{{ item.localName }}</td>
        <td class="align-middle text-right pointer" @click="toShow">{{ item.card.number }}</td>
        <td class="align-middle pointer" @click="toShow">{{ item.card.expansion.name }}</td>
        <td class="align-middle text-center pointer" @click="toShow"><rarity :value="item.card.rarity"></rarity></td>
        <td class="align-middle text-center pointer" @click="toShow"><condition :value="item.condition"></condition></td>
        <td class="align-middle pointer" @click="toShow">
            <i class="fas fa-star text-warning" v-if="item.is_foil"></i>
        </td>
        <td class="align-middle pointer" @click="toShow">{{ item.comments || '' }}</td>
        <td class="align-middle text-right pointer" @click="toShow">{{ Number(item.unit_price).toFixed(2) }} €</td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'provision_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.provision_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'provision_formatted' in errors ? errors.provision_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right pointer" @click="toShow">{{ Number(item.unit_price - item.unit_cost - item.provision).toFixed(2) }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Anzeigen" @click="toShow"><i class="fas fa-fw fa-eye"></i></button>
                <button type="button" class="btn btn-secondary" title="Speichern" @click="update"><i class="fas fa-fw fa-save"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    import condition from '../../partials/emoji/condition.vue';
    import rarity from '../../partials/emoji/rarity.vue';

    export default {

        components: {
            condition,
            rarity,
        },

        props: ['item', 'uri', 'index'],

        data () {
            return {
                id: this.item.id,
                isEditing: false,
                form: {
                    provision_formatted: this.item.provision_formatted,
                    unit_cost_formatted: this.item.unit_cost_formatted,
                },
                errors: {},
            };
        },

        methods: {
            show(event) {
                this.$emit('show', {
                    src: this.item.card.imagePath,
                    top: (event.layerY + 325) + 'px',
                });
            },
            toShow() {
                this.$emit('toshow');
            },
            update() {
                var component = this;
                axios.put('/article/' + component.id, component.form)
                    .then( function (response) {
                        component.errors = {};
                        component.isEditing = false;
                        component.$emit('updated', response.data);
                        Vue.success('Artikel gespeichert.');
                    })
                    .catch(function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Artikel konnte nicht gespeichert werden.');
                });
            },
        },
    };
</script>