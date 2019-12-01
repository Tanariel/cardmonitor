<template>
    <div class="row align-items-stretch">
        <div class="col-md-4 d-flex flex-column">
            <div class="form-group">
                <select class="form-control" v-model="filter.language_id" @change="fetch()">
                    <option :value="id" v-for="(name, id) in languages">{{ name }}</option>
                </select>
            </div>

            <filter-expansion :initial-value="filter.expansion_id" :options="expansions" :show-label="false" v-model="filter.expansion_id" @input="fetch()"></filter-expansion>

            <div class="form-group" style="margin-bottom: 0;">
                <filter-search :should-focus="filter.shouldFocus" v-model="filter.searchtext" @input="fetch()" @focused="filter.shouldFocus = false"></filter-search>
            </div>
            <div class="col mt-3 p-0" style="min-height: 500px; overflow: auto;">
                <div v-if="isLoading" class="mt-3 p-5">
                    <center>
                        <span style="font-size: 48px;">
                            <i class="fas fa-spinner fa-spin"></i><br />
                        </span>
                        Lade Daten..
                    </center>
                </div>
                <table
                <table class="table table-hover table-striped" v-else-if="cards.length">
                    <tbody>
                        <tr v-for="(card, index) in cards" @click="setItem(card, index)">
                            <td class="align-middle d-none d-lg-table-cell text-center pointer w-icon"><i class="fas fa-image" @mouseover="showImgbox({src: card.imagePath, top: ($event.layerY + 100) + 'px', left: ($event.layerX + 50) + 'px'})" @mouseout="hideImgbox"></i></td>
                            <td class="align-middle pointer w-icon"><expansion-icon :expansion="card.expansion" :show-name="false"></expansion-icon></td>
                            <td class="align-middle text-center w-icon"><rarity :value="card.rarity"></rarity></td>
                            <td class="align-middle pointer">
                                <div>{{ card.local_name }}</div>
                                <div class="text-muted" v-if="filter.language_id != 1">{{ card.name }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-dark mt-3" v-else><center>Keine Karten gefunden</center></div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="d-flex" v-if="item != null">
                <div class="col">
                    <table class="table table-striped">
                        <tbody>
                            <tr class="d-sm-none d-table-row">
                                <td class="align-middle" width="150">Artikel</td>
                                <td class="align-middle" width="100%">
                                    <div>{{ item.local_name }}</div>
                                    <div class="text-muted" v-if="filter.language_id != 1">{{ item.name }}</div>
                                </td>
                            </tr>
                            <tr class="d-none d-sm-table-row">
                                <td class="align-middle" width="150">Artikel</td>
                                <td class="align-middle" colspan="2" width="100%">
                                    <div>{{ item.local_name }}</div>
                                    <div class="text-muted" v-if="filter.language_id != 1">{{ item.name }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle">Lagerplatz</td>
                                <td class="align-middle" width="30%">
                                    <div class="form-group mb-0">
                                        <select class="form-control" v-model="form.storage_id">
                                            <option :value="null">Kein Lagerplatz</option>
                                            <option :value="storage.id" v-for="(storage, id) in storages" v-html="storage.indentedName"></option>
                                        </select>
                                        <div class="invalid-feedback" v-text="'storage' in errors ? errors.storage[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell" width="70%"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Anzahl</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control" type="number" ref="count" v-model="form.count" @keydown.enter="create(true)">
                                        <div class="invalid-feedback" v-text="'count' in errors ? errors.count[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-secondary" @click="form.count = 1">1</button>
                                        <button class="btn btn-secondary" @click="form.count = 2">2</button>
                                        <button class="btn btn-secondary" @click="form.count = 3">3</button>
                                        <button class="btn btn-secondary" @click="form.count = 4">4</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle">Sprache</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <select class="form-control" v-model="form.language_id">
                                            <option :value="id" v-for="(name, id) in languages">{{ name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-text="'language_id' in errors ? errors.language_id[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Zustand</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <select class="form-control" v-model="form.condition">
                                            <option :value="id" v-for="(name, id) in conditions">{{ name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-text="'condition' in errors ? errors.condition[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-secondary" @click="form.condition = conditionKeys[conditionIndex - 1]" :disabled="conditionIndex == 0">+</button>
                                        <button class="btn btn-secondary" @click="form.condition = conditionKeys[conditionIndex + 1]" :disabled="conditionIndex == 6">-</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle">Foil?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_foil" v-model="form.is_foil">
                                        <label class="form-check-label" for="is_foil"></label>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Signiert?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_signed" v-model="form.is_signed">
                                        <label class="form-check-label" for="is_signed"></label>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Playset?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_playset" v-model="form.is_playset">
                                        <label class="form-check-label" for="is_playset"></label>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Altered?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_altered" v-model="form.is_altered">
                                        <label class="form-check-label" for="is_altered"></label>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Hinweise</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control" :class="'cardmarket_comments' in errors ? 'is-invalid' : ''" type="text" v-model="form.cardmarket_comments" @keydown.enter="create(false)">
                                        <div class="invalid-feedback" v-text="'cardmarket_comments' in errors ? errors.cardmarket_comments[0] : ''"></div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Kosten</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control text-right" :class="'unit_cost_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_cost_formatted" @keydown.enter="create(false)">
                                        <div class="invalid-feedback" v-text="'unit_cost_formatted' in errors ? errors.unit_cost_formatted[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="align-middle d-none d-sm-table-cell"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Preis</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control text-right" :class="'unit_price_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_price_formatted" @keydown.enter="create(false)">
                                        <div class="invalid-feedback" v-text="'unit_price_formatted' in errors ? errors.unit_price_formatted[0] : ''"></div>
                                    </div>
                                </td class="align-middle d-none d-sm-table-cell">
                                <td class="align-middle d-none d-sm-table-cell">
                                    <div class="btn-group btn-group-sm" role="group" v-show="isLoadingPrices == false">
                                        <button class="btn btn-secondary" @click="setPrice('low')">LOW</button>
                                        <button class="btn btn-secondary" @click="setPrice('sell')">SELL</button>
                                        <button class="btn btn-secondary" @click="setPrice('trend')">TREND</button>
                                        <button class="btn btn-secondary" @click="setPrice('avg')" :disabled="form.is_foil">AVG</button>
                                        <button class="btn btn-secondary" @click="setPrice('rule')" :title="item.rule.name" v-if="item.rule.id">REGEL</button>
                                    </div>
                                    <center v-show="isLoadingPrices"><i class="fas fa-fw fa-spin fa-spinner"></i> Lade aktuelle Preise</center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-primary" title="Anlegen" @click="create(false)"><i class="fas fa-fw fa-save"></i></button>
                    <button class="btn btn-primary" title="Anlegen & Exportieren" @click="create(true)"><i class="fas fa-fw fa-cloud-upload-alt"></i></button>
                    <button class="btn btn-secondary" title="Abbrechen" @click="item = null"><i class="fas fa-fw fa-times"></i></button>
                </div>
                <div class="col-xl d-none d-xl-block">
                    <img :src="item.imagePath">
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="table-responsive" v-show="items.length">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center d-none d-lg-table-cell w-icon">Sync</th>
                            <th class="text-right d-none d-xl-table-cell w-icon"></th>
                            <th class="">Name</th>
                            <th class="w-icon"></th>
                            <th class="text-center d-none d-xl-table-cell w-icon"></th>
                            <th class="text-center d-none d-lg-table-cell">Sprache</th>
                            <th class="text-center d-none d-lg-table-cell">Zustand</th>
                            <th class="d-none d-xl-table-cell" style="width: 100px;"></th>
                            <th class="d-none d-xl-table-cell">Lagerplatz</th>
                            <th class="text-right d-none d-sm-table-cell">Verkaufspreis</th>
                            <th class="text-right d-none d-xl-table-cell">Einkaufspreis</th>
                            <th class="text-right d-none d-xl-table-cell w-formatted-number">Provision</th>
                            <th class="text-right d-none d-xl-table-cell w-formatted-number" title="Voraussichtlicher Gewinn ohne allgemeine Kosten" width="100">Gewinn</th>
                            <th class="text-right d-none d-sm-table-cell w-action">Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <row :item="item" :key="item.id" :uri="uri" :conditions="conditions" :languages="languages" :storages="storages" :selected="(selected.indexOf(item.id) == -1) ? false : true" v-for="(item, index) in items" @input="toggleSelected" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()" @deleted="remove(index)"></row>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="imgbox" style="position: absolute;" :style="{ top: imgbox.top, left: imgbox.left, }">
            <img :src="imgbox.src" v-show="imgbox.show">
        </div>
    </div>
</template>

<script>
    import filterExpansion from "../filter/expansion.vue";
    import filterSearch from "../filter/search.vue";
    import rarity from '../partials/emoji/rarity.vue';
    import row from "./row.vue";
    import expansionIcon from '../expansion/icon.vue';

    export default {

        components: {
            expansionIcon,
            filterExpansion,
            filterSearch,
            rarity,
            row,
        },

        props: {
            conditions: {
                type: Object,
                required: true,
            },
            defaultCardCosts: {
                type: Object,
                required: true,
            },
            expansions: {
                type: Array,
                required: true,
            },
            languages: {
                type: Object,
                required: true,
            },
            storages: {
                type: Array,
                required: true,
            },
        },

        computed: {
            conditionIndex() {
                return this.conditionKeys.indexOf(this.form.condition);
            },
            conditionKeys() {
                return Object.keys(this.conditions);
            },
            selectAll: {
                get: function () {
                    return this.items.length ? this.items.length == this.selected.length : false;
                },
                set: function (value) {
                    this.selected = [];
                    if (value) {
                        for (let i in this.items) {
                            this.selected.push(this.items[i].id);
                        }
                    }
                },
            },
            sortedExpansions: function() {
                function compare(a, b) {
                    if (a.name < b.name) {
                        return -1;
                    }

                    if (a.name > b.name) {
                        return 1;
                    }

                    return 0;
                }

                return this.expansions.sort(compare);
            },
        },

        watch: {
            item(newValue) {
                if (newValue == null) {
                    this.form.card_id = 0;
                    this.form.cardmarket_comments = '';
                    this.form.condition = 'NM';
                    this.form.count = 1;
                    this.form.language_id = 0;
                    this.form.storage_id = null;
                    this.form.unit_cost_formatted = '0,00';
                    this.form.unit_price_formatted = '0,00'; // aus user settings (TREND|LOW|...)
                    this.form.is_foil = false;
                    this.form.is_signed = false;
                    this.form.is_playset = false;
                    this.form.sync = false;

                    this.filter.shouldFocus = true;
                }
                else {
                    if (newValue.has_latest_prices == false) {
                        var component = this;
                        component.isLoadingPrices = true;
                        axios.put('/cardmarket/product/' + newValue.id)
                            .then( function (response) {
                                response.data.index = newValue.index;
                                response.data.local_name = newValue.local_name;
                                response.data.expansion = newValue.expansion;
                                component.item = response.data;
                                component.isLoadingPrices = false;
                                Vue.set(component.cards, newValue.index, component.item);
                            })
                    }
                    newValue.price_rule = (newValue.rule.id ? newValue[newValue.rule.base_price] * newValue.rule.multiplier : null);
                    this.form.card_id = newValue.id;
                    this.form.language_id = this.filter.language_id;
                    this.form.storage_id = newValue.storage_id || null;
                    this.form.unit_cost_formatted = Number(this.defaultCardCosts[newValue.rarity] || 0).format(2, ',', '');
                    this.form.unit_price_formatted = Number(newValue.price_rule || newValue.price_trend).format(2, ',', '');
                    this.form.is_foil = false;
                    this.form.is_signed = false;
                    this.form.is_playset = false;
                    this.form.is_altered = false;
                    this.form.cardmarket_comments = '';
                }
            },
        },

        data() {
            return {
                cards: {},
                item: null,
                items: [],
                imgbox: {
                    src: null,
                    show: true,
                },
                isLoading: false,
                isLoadingPrices: false,
                errors: {},
                filter: {
                    expansion_id: 0,
                    language_id: 3,
                    searchtext: '',
                    shouldFocus: false,
                },
                form: {
                    card_id: 0,
                    cardmarket_comments: '',
                    condition: 'NM',
                    count: 1,
                    language_id: 0,
                    storage_id: null,
                    unit_cost_formatted: '0,00',
                    unit_price_formatted: '0,00',
                    is_foil: false,
                    is_signed: false,
                    is_playset: false,
                    sync: false,
                },
                selected: [],
                uri: '/card',
            };
        },

        mounted() {
            this.fetch();
        },

        methods: {
            create(sync) {
                var component = this;
                component.form.sync = sync;
                axios.post('/article', component.form)
                    .then(function (response) {
                        component.items = response.data.concat(component.items);
                        component.filter.searchtext = '';
                        component.item = null;
                        component.filter.shouldFocus = true;
                        Vue.success('Artikel erstellt' + (sync ? ' und hochgeladen' : '') +'.');
                    })
                     .catch(function (error) {
                        Vue.error('Karten konnten nicht angelegt werden!');
                        console.log(error);
                    });
            },
            fetch() {
                var component = this;
                if (component.filter.searchtext.length < 3 && component.filter.expansion_id == 0) {
                    component.cards = {};
                    return;
                }
                component.isLoading = true;
                axios.get(component.uri, {
                    params: component.filter
                })
                    .then(function (response) {
                        component.cards = response.data;
                        component.isLoading = false;
                        if (component.cards.length == 1) {
                            component.setItem(component.cards[0], 0);
                        }
                        else {
                            component.item = null;
                        }
                    })
                    .catch(function (error) {
                        Vue.error('Karten konnten nicht geladen werden!');
                        console.log(error);
                    });
            },
            setItem(card, index) {
                this.item = card;
                if (card != null) {
                    this.item.index = index;
                    this.$nextTick(function () {
                        this.$refs.count.focus();
                    });
                }
            },
            updated(index, item) {
                Vue.set(this.items, index, item);
            },
            remove(index) {
                this.items.splice(index, 1);
            },
            toggleSelected (id) {
                var index = this.selected.indexOf(id);
                if (index == -1) {
                    this.selected.push(id);
                }
                else {
                    this.selected.splice(index, 1);
                }
            },
            keydown(event) {
                // console.log(event);
            },
            showImgbox({src, top, left}) {
                this.imgbox.src = src;
                this.imgbox.top = top;
                this.imgbox.left = left;
                this.imgbox.show = true;
            },
            hideImgbox() {
                this.imgbox.show = false;
            },
            setPrice(type) {
                console.log(this.item['price_' + type]);
                this.form.unit_price_formatted = Number(this.item['price_' + (this.form.is_foil ? 'foil_' : '') + type]).format(2, ',', '');
            },
        },
    };
</script>