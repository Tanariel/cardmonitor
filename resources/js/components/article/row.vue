<template>
    <tr>
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle text-center pointer" @click="toShow"><i class="fas fa-fw" :class="item.state_icon" :title="item.state_comments"></i></td>
        <td class="align-middle"><i class="fas fa-image pointer" @mouseover="show($event)" @mouseout="$emit('hide')"></i></td>
        <td class="align-middle pointer" @click="toShow"><span class="flag-icon" :class="'flag-icon-' + item.language.code" :title="item.language.name"></span> {{ item.localName }}</td>
        <td class="align-middle text-right pointer" @click="toShow">{{ item.card.number }}</td>
        <td class="align-middle pointer" @click="toShow">{{ item.card.expansion.name }}</td>
        <td class="align-middle text-center pointer" @click="toShow"><rarity :value="item.card.rarity"></rarity></td>
        <td class="align-middle text-center pointer" @click="toShow"><condition :value="item.condition"></condition></td>
        <td class="align-middle pointer" @click="toShow">
            <i class="fas fa-star text-warning" v-if="item.is_foil"></i>
        </td>
        <td class="align-middle pointer" @click="toShow">{{ item.comments || '' }}</td>
        <td class="align-middle text-right pointer">
            <input class="form-control text-right" :class="'unit_price_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_price_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'unit_price_formatted' in errors ? errors.unit_price_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">{{ Number(item.provision).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right pointer" @click="toShow">{{ Number(item.unit_price - item.unit_cost - item.provision).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Speichern" @click="update"><i class="fas fa-fw fa-save"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    import condition from '../partials/emoji/condition.vue';
    import rarity from '../partials/emoji/rarity.vue';

    export default {

        components: {
            condition,
            rarity,
        },

        props: ['item', 'uri', 'selected'],

        data () {
            return {
                id: this.item.id,
                isEditing: false,
                form: {
                    unit_cost_formatted: this.item.unit_cost_formatted,
                    unit_price_formatted: this.item.unit_price_formatted,
                },
                errors: {},
            };
        },

        methods: {
            show(event) {
                this.$emit('show', {
                    src: this.item.card.imagePath,
                    top: (event.layerY + 50) + 'px',
                });
            },
            toShow() {
                this.$emit('toshow');
            },
            update() {
                var component = this;
                axios.put(component.item.path, component.form)
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