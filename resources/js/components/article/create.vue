<template>
    <div class="row align-items-stretch">
        <div class="col-md-4 d-flex flex-column">
            <div class="form-group">
                <select class="form-control" v-model="filter.language_id" @change="fetch()">
                    <option :value="id" v-for="(name, id) in languages">{{ name }}</option>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" v-model="filter.expansion_id" @change="fetch()">
                    <option value="0">Alle Erweiterungen</option>
                    <option :value="id" v-for="(name, id) in expansions">{{ name }}</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <filter-search :should-focus="filter.shouldFocus" v-model="filter.searchtext" @input="fetch()" @focused="shouldFocus = false"></filter-search>
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
                        <tr v-for="(card, index) in cards" @click="item = card">
                            <td class="align-middle text-center pointer" width="50"><i class="fas fa-image" @mouseover="showImgbox(card.imagePath, ($event.layerY + 100) + 'px')" @mouseout="hideImgbox"></i></td>
                            <td class="align-middle pointer">{{ card.expansion.name }}</td>
                            <td class="align-middle text-center" width="50"><rarity :value="card.rarity"></rarity></td>
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
                            <tr>
                                <td class="align-middle">Artikel</td>
                                <td class="align-middle" colspan="2">
                                    <div>{{ item.local_name }}</div>
                                <div class="text-muted" v-if="filter.language_id != 1">{{ item.name }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle">Anzahl</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control" type="number" v-model="form.count"@keydown="keydown">
                                        <div class="invalid-feedback" v-text="'count' in errors ? errors.count[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="align-middle">
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
                                <td></td>
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
                                <td class="align-middle">
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
                                <td class="align-middle"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Signiert?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_signed" v-model="form.is_signed">
                                        <label class="form-check-label" for="is_signed"></label>
                                    </div>
                                </td>
                                <td class="align-middle"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Playset?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_playset" v-model="form.is_playset">
                                        <label class="form-check-label" for="is_playset"></label>
                                    </div>
                                </td>
                                <td class="align-middle"></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Altered?</td>
                                <td class="align-middle">
                                    <div class="form-group form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_altered" v-model="form.is_altered">
                                        <label class="form-check-label" for="is_altered"></label>
                                    </div>
                                </td>
                                <td class="align-middle"></td>
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
                                <td></td>
                            </tr>
                            <tr>
                                <td class="align-middle">Preis</td>
                                <td class="align-middle">
                                    <div class="form-group mb-0">
                                        <input class="form-control text-right" :class="'unit_price_formatted' in errors ? 'is-invalid' : ''" type="text" v-model="form.unit_price_formatted" @keydown.enter="create(false)">
                                        <div class="invalid-feedback" v-text="'unit_price_formatted' in errors ? errors.unit_price_formatted[0] : ''"></div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-secondary" @click="form.count = 1">LOW</button>
                                        <button class="btn btn-secondary" @click="form.count = 2">SELL</button>
                                        <button class="btn btn-secondary" @click="form.count = 3">TREND</button>
                                        <button class="btn btn-secondary" @click="form.count = 4">AVG</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-primary" title="Anlegen" @click="create(false)"><i class="fas fa-fw fa-save"></i></button>
                    <button class="btn btn-primary" title="Anlegen & Exportieren" @click="create(true)"><i class="fas fa-fw fa-sync"></i></button>
                </div>
                <div class="col">
                    <img :src="item.imagePath">
                </div>
            </div>
        </div>
        <div id="imgbox" style="position: absolute; left: 100px;" :style="{ top: imgbox.top }">
            <img :src="imgbox.src" v-show="imgbox.show">
        </div>
    </div>
</template>

<script>
    import filterSearch from "../filter/search.vue";
    import rarity from '../partials/emoji/rarity.vue';

    export default {

        components: {
            filterSearch,
            rarity,
        },

        computed: {
            conditionIndex() {
                return this.conditionKeys.indexOf(this.form.condition);
            },
            conditionKeys() {
                return Object.keys(this.conditions);
            }
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
                type: Object,
                required: true,
            },
            languages: {
                type: Object,
                required: true,
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
                    this.form.unit_cost_formatted = '0,00';
                    this.form.unit_price_formatted = '0,00'; // aus user settings (TREND|LOW|...)
                    this.form.is_foil = false;
                    this.form.is_signed = false;
                    this.form.is_playset = false;
                    this.form.sync = false;

                    this.filter.shouldFocus = true;
                }
                else {
                    this.form.card_id = newValue.id;
                    this.form.language_id = this.filter.language_id;
                    this.form.unit_cost_formatted = Number(this.defaultCardCosts[newValue.rarity] || 0).format(2, ',', '');
                    this.form.unit_price_formatted = '0,00';
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
                imgbox: {
                    src: null,
                    show: true,
                },
                isLoading: false,
                errors: {},
                filter: {
                    expansion_id: 250,
                    language_id: 3,
                    searchtext: 'kundschafter',
                    shouldFocus: false,
                },
                form: {
                    card_id: 0,
                    cardmarket_comments: '',
                    condition: 'NM',
                    count: 1,
                    language_id: 0,
                    unit_cost_formatted: '0,00',
                    unit_price_formatted: '2,34', // aus user settings (TREND|LOW|...)
                    is_foil: false,
                    is_signed: false,
                    is_playset: false,
                    sync: false,
                },
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
                // axios
                component.filter.searchtext = '';
                component.item = null;
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
                            component.item = component.cards[0];
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
            keydown(event) {
                console.log(event);
            },
            showImgbox(src, top) {
                this.imgbox.src = src;
                this.imgbox.top = top;
                this.imgbox.show = true;
            },
            hideImgbox() {
                this.imgbox.show = false;
            },
        },
    };
</script>