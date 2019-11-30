<template>
    <div>
        <div class="row">
            <div class="col mb-1 mb-sm-0">
                <a href="/article/create" class="btn btn-primary"><i class="fas fa-plus-square"></i></a>
            </div>
            <div class="col-auto d-flex">
                <div class="form-group" style="margin-bottom: 0;">
                    <filter-search v-model="filter.searchtext" @input="search()"></filter-search>
                </div>
                <button class="btn btn-secondary ml-1" @click="filter.show = !filter.show"><i class="fas fa-filter"></i></button>
                <button class="btn btn-secondary ml-1" @click="sync" :disabled="syncing.status == 1"><i class="fas fa-sync" :class="{'fa-spin': syncing.status == 1}"></i></button>
                <button type="button" class="btn btn-primary text-overflow-ellipsis ml-1" title="Regeln anwenden" data-toggle="modal" data-target="#confirm-rule-apply" :disabled="applying.status == 1">
                    <i class="fas fa-spinner fa-spin mr-1" v-show="applying.status == 1"></i>Regeln anwenden
                </button>
            </div>
        </div>

        <form v-if="filter.show" id="filter" class="mt-1">
            <div  class="form-row">

                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-sync">Sync</label>
                        <select class="form-control" id="filter-sync" v-model="filter.sync" @change="search">
                            <option :value="-1">Alle</option>
                            <option :value="1">Fehler</option>
                            <option :value="0">Keine Fehler</option>
                        </select>
                    </div>
                </div>

                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-sold">Verkauft</label>
                        <select class="form-control" id="filter-sold" v-model="filter.sold" @change="search">
                            <option :value="-1">Alle</option>
                            <option :value="0">Nicht Verkauft</option>
                            <option :value="1">Verkauft</option>
                        </select>
                    </div>
                </div>

                <div class="col-auto">
                    <filter-expansion :options="expansions" v-model="filter.expansion_id" @input="search"></filter-expansion>
                </div>
                <div class="col-auto">
                    <filter-rarity :options="rarities" v-model="filter.rarity" @input="search"></filter-rarity>
                </div>
                <div class="col-auto">
                    <filter-language :options="languages" v-model="filter.language_id" @input="search"></filter-language>
                </div>
                <div class="col-auto">
                    <filter-rule :options="rules" v-model="filter.rule_id" @input="search" v-if="rules != null"></filter-rule>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-unit_price_min">Verkaufspreis min</label>
                        <input class="form-control" id="filter-unit_price_min" type="text" v-model="filter.unit_price_min" @input="search">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-unit_price_max">Verkaufspreis max</label>
                        <input class="form-control" id="filter-unit_price_max" type="text" v-model="filter.unit_price_max" @input="search">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-unit_cost_min">Einkaufspreis min</label>
                        <input class="form-control" id="filter-unit_cost_min" type="text" v-model="filter.unit_cost_min" @input="search">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="filter-unit_cost_max">Einkaufspreis max</label>
                        <input class="form-control" id="filter-unit_cost_max" type="text" v-model="filter.unit_cost_max" @input="search">
                    </div>
                </div>

                Sync, Erweiterung (nur vorhandene in Artikeln), Zustand (<=,=,>=), Foil, Signiert, Playset, Hinweise, Verfügbar (min, max), Verfügbar (Alle?|Verkauf|Angebote)
                Regel anwenden -> Filter setzen

            </div>
        </form>

        <div v-if="isLoading" class="mt-3 p-5">
            <center>
                <span style="font-size: 48px;">
                    <i class="fas fa-spinner fa-spin"></i><br />
                </span>
                Lade Daten..
            </center>
        </div>
        <div class="table-responsive mt-3" v-else-if="items.length">
            <table class="table table-hover table-striped bg-white">
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
                        <th class="text-right d-none d-sm-table-cell">VK</th>
                        <th class="text-right d-none d-lg-table-cell">EK</th>
                        <th class="text-right d-none d-lg-table-cell w-formatted-number">Provision</th>
                        <th class="text-right d-none d-xl-table-cell w-formatted-number" title="Voraussichtlicher Gewinn ohne allgemeine Kosten" width="100">Gewinn</th>
                        <th class="text-right d-none d-sm-table-cell w-action">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :item="item" :key="item.id" :uri="uri" :conditions="conditions" :languages="languages" :storages="storages" :selected="(selected.indexOf(item.id) == -1) ? false : true" @input="toggleSelected" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()" @deleted="remove(index)"></row>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>Keine Artikel vorhanden</center></div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center" v-show="paginate.lastPage > 1">
                <li class="page-item" v-show="paginate.prevPageUrl">
                    <a class="page-link" href="#" @click.prevent="filter.page--">Previous</a>
                </li>

                <li class="page-item" v-for="(n, i) in pages" v-bind:class="{ active: (n == filter.page) }"><a class="page-link" href="#" @click.prevent="filter.page = n">{{ n }}</a></li>

                <li class="page-item" v-show="paginate.nextPageUrl">
                    <a class="page-link" href="#" @click.prevent="filter.page++">Next</a>
                </li>
            </ul>
        </nav>
        <div id="imgbox" style="position: absolute; " :style="{ top: imgbox.top, left: imgbox.left, }">
            <img :src="imgbox.src" v-show="imgbox.show">
        </div>
        <div class="modal fade" id="confirm-rule-apply" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Regeln anwenden</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Möchtest du alle aktiven Regeln anweden und in deinem Cardmarket Konto speichern?</p>
                        <div class="alert alert-danger" role="alert">
                            Es werden Preise in deinem Cardmarket Konto verändert! Versichere dich vorher, ob alle Regeln angewendet werden, wie Du es möchtest!<br /><br />
                            Ausführung auf eigne Gefahr!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-secondary" @click="apply(false)">Regeln simulieren</button>
                        <button type="button" class="btn btn-primary" :disabled="canPayRuleApply == 0" @click="apply(true)">Regeln anwenden (1 €)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import row from "./row.vue";
    import filterSearch from "../filter/search.vue";
    import filterRarity from "../filter/rarity.vue";
    import filterLanguage from "../filter/language.vue";
    import filterExpansion from "../filter/expansion.vue";
    import filterRule from "../filter/rule.vue";

    export default {

        components: {
            row,
            filterRarity,
            filterSearch,
            filterLanguage,
            filterExpansion,
            filterRule,
        },

        props: {
            canPayRuleApply: {
                required: true,
                type: Number,
            },
            conditions: {
                type: Object,
                required: true,
            },
            languages: {
                required: true,
                type: Object,
            },
            expansions: {
                type: Array,
                required: true,
            },
            isApplyingRules: {
                required: true,
                type: Number,
            },
            isSyncingArticles: {
                required: true,
                type: Number,
            },
            rarities: {
                type: Array,
                required: true,
            },
            rules: {
                type: Array,
                required: true,
            },
            storages: {
                required: true,
                type: Array,
            },
        },

        data () {
            return {
                uri: '/article',
                items: [],
                isLoading: true,
                applying: {
                    status: this.isApplyingRules,
                    interval: null,
                },
                syncing: {
                    status: this.isSyncingArticles,
                    interval: null,
                },
                imgbox: {
                    src: null,
                    top: 0,
                    left: 0,
                    show: true,
                },
                paginate: {
                    nextPageUrl: null,
                    prevPageUrl: null,
                    lastPage: 0,
                },
                filter: {
                    show: false,
                    page: 1,
                    searchtext: '',
                    cardmarket_comments: '',
                    language_id: 0,
                    expansion_id: 0,
                    rule_id: 0,
                    unit_price_min: 0,
                    unit_price_max: 0,
                    unit_cost_min: 0,
                    unit_cost_max: 0,
                    sold: -1,
                    sync: -1,
                },
                selected: [],
                errors: {},
            };
        },

        mounted() {

            this.fetch();
            if (this.isApplyingRules) {
                this.checkIsApplyingRules();
            }
            if (this.isSyncingArticles) {
                this.checkIsSyncingArticles();
            }

        },

        watch: {
            page () {
                this.fetch();
            },
        },

        computed: {
            page() {
                return this.filter.page;
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
            pages() {
                var pages = [];
                for (var i = 1; i <= this.paginate.lastPage; i++) {
                    if (this.showPageButton(i)) {
                        const lastItem = pages[pages.length - 1];
                        if (lastItem < (i - 1) && lastItem != '...') {
                            pages.push('...');
                        }
                        pages.push(i);
                    }
                }

                return pages;
            },
        },

        methods: {
            apply(sync) {
                $('#confirm-rule-apply').modal('hide');
                var component = this;
                axios.post('/rule/apply', {
                    sync: sync,
                })
                    .then(function (response) {
                        component.applying.status = 1;
                        component.checkIsApplyingRules();
                        Vue.success('Regeln werden im Hintergrund ' + (sync ? 'angewendet' : 'simuliert') + '.');
                    })
                    .catch(function (error) {
                        Vue.error('Regeln konnten nicht angewendet werden!');
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            checkIsApplyingRules() {
                var component = this;
                this.applying.interval = setInterval( function () {
                    component.getIsApplyingRules()
                }, 3000);
            },
            getIsApplyingRules() {
                var component = this;
                axios.get('/rule/apply')
                    .then(function (response) {
                        component.applying.status = response.data.is_applying_rules;
                        if (component.applying.status == 0) {
                            clearInterval(component.applying.interval)
                            component.applying.interval = null;
                            component.fetch();
                            Vue.success('Regeln wurden angewendet.');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            checkIsSyncingArticles() {
                var component = this;
                this.syncing.interval = setInterval(function () {
                    component.getIsSyncingArticles()
                }, 3000);
            },
            getIsSyncingArticles() {
                var component = this;
                axios.get(component.uri + '/sync')
                    .then(function (response) {
                        component.syncing.status = response.data.is_syncing_articles;
                        if (component.syncing.status == 0) {
                            clearInterval(component.syncing.interval)
                            component.syncing.interval = null;
                            component.fetch();
                            Vue.success('Artikel wurden synchronisiert.');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            fetch() {
                var component = this;
                component.isLoading = true;
                axios.get(component.uri, {
                    params: component.filter
                })
                    .then(function (response) {
                        component.items = response.data.data;
                        component.filter.page = response.data.current_page;
                        component.paginate.nextPageUrl = response.data.next_page_url;
                        component.paginate.prevPageUrl = response.data.prev_page_url;
                        component.paginate.lastPage = response.data.last_page;
                        component.isLoading = false;
                    })
                    .catch(function (error) {
                        Vue.error('Artikel konnten nicht geladen werden!');
                        console.log(error);
                    });
            },
            sync() {
                var component = this;
                axios.put(component.uri + '/sync')
                    .then(function (response) {
                        component.syncing.status = 1;
                        component.checkIsSyncingArticles();
                        Vue.success('Artikel werden im Hintergrund aktualisiert.');
                    })
                    .catch(function (error) {
                        Vue.error('Artikel konnten nicht synchronisiert werden! Ist das Cardmarket Konto verbunden?');
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            search() {
                this.filter.page = 1;
                this.fetch();
            },
            updated(index, item) {
                Vue.set(this.items, index, item);
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
            showImgbox({src, top, left}) {
                this.imgbox.src = src;
                this.imgbox.top = top;
                this.imgbox.left = left;
                this.imgbox.show = true;
            },
            hideImgbox() {
                this.imgbox.show = false;
            },
            remove(index) {
                this.items.splice(index, 1);
            },
            showPageButton(page) {
                if (page == 1 || page == this.paginate.lastPage) {
                    return true;
                }

                if (page <= this.filter.page + 2 && page >= this.filter.page - 2) {
                    return true;
                }

                return false;
            },
        },
    };
</script>