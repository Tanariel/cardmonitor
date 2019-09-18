<template>
    <tr v-if="isEditing">
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle text-center"><i class="fas fa-fw" :class="item.sync_icon" :title="item.sync_error || 'Karte synchronisiert'"></i></td>
        <td class="align-middle pointer"><i class="fas fa-image" @mouseover="show($event)" @mouseout="$emit('hide')"></i></td>
        <td class="align-middle">
            <span class="flag-icon" :class="'flag-icon-' + item.language.code" :title="item.language.name"></span> {{ item.localName }}
            <div class="text-muted" v-if="item.language_id != 1">{{ item.card.name }}</div>
        </td>
        <td class="align-middle text-right">{{ item.card.number }}</td>
        <td class="align-middle">{{ item.card.expansion.name }}</td>
        <td class="align-middle text-center"><rarity :value="item.card.rarity"></rarity></td>
        <td class="align-middle text-center">
            <select class="form-control" v-model="form.language_id">
                <option :value="language_id" v-for="(name, language_id) in languages">{{ name }}</option>
            </select>
            <div class="invalid-feedback" v-text="'unit_price_formatted' in errors ? errors.unit_price_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-center">
            <select class="form-control" v-model="form.condition">
                <option :value="id" v-for="(name, id) in conditions">{{ name }}</option>
            </select>
            <div class="invalid-feedback" v-text="'condition' in errors ? errors.condition[0] : ''"></div>
        </td>
        <td class="align-middle text-center">
            <div class="form-group form-check mb-0">
                <input class="form-check-input" type="checkbox" id="is_foil" v-model="form.is_foil">
                <label class="form-check-label" for="is_foil"></label>
            </div>
        </td>
        <td class="align-middle text-center">
            <div class="form-group form-check mb-0">
                <input class="form-check-input" type="checkbox" id="is_signed" v-model="form.is_signed">
                <label class="form-check-label" for="is_signed"></label>
            </div>
        </td>
        <td class="align-middle text-center">
            <div class="form-group form-check mb-0">
                <input class="form-check-input" type="checkbox" id="is_playset" v-model="form.is_playset">
                <label class="form-check-label" for="is_playset"></label>
            </div>
        </td>
        <td class="align-middle">
            <input class="form-control" :class="'cardmarket_comments' in errors ? 'is-invalid' : ''" type="text" v-model="form.cardmarket_comments" @keydown.enter="update(false)">
            <div class="invalid-feedback" v-text="'cardmarket_comments' in errors ? errors.cardmarket_comments[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'unit_price_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_price_formatted" @keydown.enter="update(false)">
            <div class="invalid-feedback" v-text="'unit_price_formatted' in errors ? errors.unit_price_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="update(false)">
            <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">{{ Number(item.provision).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right pointer">{{ Number(item.unit_price - item.unit_cost - item.provision).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Speichern" @click="update(false)"><i class="fas fa-fw fa-save"></i></button>
                <button type="button" class="btn btn-secondary" title="Speichern & Exportieren" @click="update(true)"><i class="fas fa-fw fa-sync"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy"><i class="fas fa-fw fa-trash"></i></button>
            </div>
        </td>
    </tr>
    <tr v-else>
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle text-center"><i class="fas fa-fw fa-euro-sign text-success" title="Verkauft"></i></td>
        <td class="align-middle pointer"><i class="fas fa-image" @mouseover="show($event)" @mouseout="$emit('hide')"></i></td>
        <td class="align-middle">
            <span class="flag-icon" :class="'flag-icon-' + item.language.code" :title="item.language.name"></span> {{ item.localName }}
            <div class="text-muted" v-if="item.language_id != 1">{{ item.card.name }}</div></td>
        <td class="align-middle text-right">{{ item.card.number }}</td>
        <td class="align-middle">{{ item.card.expansion.name }}</td>
        <td class="align-middle text-center"><rarity :value="item.card.rarity"></rarity></td>
        <td class="align-middle text-center"><span class="flag-icon" :class="'flag-icon-' + item.language.code" :title="item.language.name"></span></td>
        <td class="align-middle text-center"><condition :value="item.condition"></condition></td>
        <td class="align-middle text-center"><i class="fas fa-star text-warning" v-if="item.is_foil"></i></td>
        <td class="align-middle text-center"></td>
        <td class="align-middle text-center"></td>
        <td class="align-middle">{{ item.cardmarket_comments }}</td>
        <td class="align-middle text-right">{{ Number(item.unit_price).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="update(false)">
            <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
        </td>
        <td class="align-middle text-right">
            <input class="form-control text-right" :class="'provision_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.provision_formatted" @keydown.enter="update">
            <div class="invalid-feedback" v-text="'provision_formatted' in errors ? errors.provision_formatted[0] : ''"></div></td>
        <td class="align-middle text-right pointer">{{ Number(item.unit_price - item.unit_cost - item.provision).format(2, ',', '.') }} €</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <a class="btn btn-secondary" :href="item.order.path" :title="'Bestellung ' + item.order.cardmarket_order_id"><i class="fas fa-box"></i></a>
                <button type="button" class="btn btn-secondary" title="Speichern" @click="update(false)"><i class="fas fa-fw fa-save"></i></button>
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

        props: ['item', 'uri', 'selected', 'conditions', 'languages'],

        data () {
            return {
                id: this.item.id,
                isEditing: (this.item.order_id ? false : true),
                form: {
                    cardmarket_comments: this.item.cardmarket_comments,
                    condition: this.item.condition,
                    language_id: this.item.language_id,
                    unit_cost_formatted: this.item.unit_cost_formatted,
                    unit_price_formatted: this.item.unit_price_formatted,
                    provision_formatted: this.item.provision_formatted,
                    is_foil: this.item.is_foil,
                    is_signed: this.item.is_signed,
                    is_playset: this.item.is_playset,
                    sync: false,
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
            destroy() {
                var component = this;
                axios.delete(component.item.path)
                    .then( function (response) {
                        if (response.data.deleted) {
                            Vue.success('Karte gelöscht')
                            component.$emit("deleted", component.id);
                            return;
                        }

                        Vue.error('Karte konnte nicht gelöscht werden.');
                    })
            },
            update(sync) {
                var component = this;
                component.form.sync = sync;
                axios.put(component.item.path, component.form)
                    .then( function (response) {
                        component.errors = {};
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