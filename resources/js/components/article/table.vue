<template>
    <div>
        <div class="row">
            <div class="col">
                <a href="/article/create" class="btn btn-primary"><i class="fas fa-plus-square"></i></a>
            </div>
            <div class="col-auto d-flex">
                <div class="form-group" style="margin-bottom: 0;">
                    <filter-search v-model="filter.searchtext" @input="fetch()"></filter-search>
                </div>
                <button class="btn btn-secondary ml-1" @click="filter.show = !filter.show"><i class="fas fa-filter"></i></button>
                <button class="btn btn-secondary ml-1" @click="sync"><i class="fas fa-sync"></i></button>
            </div>
        </div>

        <form v-if="filter.show" id="filter" class="mt-1">
            <div  class="form-row">

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

                Sync, Erweiterung (nur vorhandene in Artikeln), Seltenheit, Sprache, Zustand (<=,=,>=), Foil, Signiert, Playset, Hinweise, Preis (min, max), Verfügbar (min, max), Verfügbar (Alle?|Verkauf|Angebote)
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
                        <th width="25">
                            <label class="form-checkbox" for="checkall"></label>
                            <input id="checkall" type="checkbox" v-model="selectAll">
                        </th>
                        <th class="text-center" width="50">Sync</th>
                        <th class="text-right" width="50"></th>
                        <th class="">Name</th>
                        <th class="text-right" width="50">#</th>
                        <th class="">Erweiterung</th>
                        <th class="text-center" width="75">Seltenheit</th>
                        <th class="text-center">Sprache</th>
                        <th class="text-center">Zustand</th>
                        <th class="text-center" width="75">Foil</th>
                        <th class="text-center" width="75">Signiert</th>
                        <th class="text-center" width="75">Playset</th>
                        <th class="">Hinweise</th>
                        <th class="text-right">Verkaufspreis</th>
                        <th class="text-right">Einkaufspreis</th>
                        <th class="text-right">Provision</th>
                        <th class="text-right" title="Voraussichtlicher Gewinn ohne allgemeine Kosten" width="100">Gewinn</th>
                        <th class="text-right" width="150">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :item="item" :key="item.id" :uri="uri" :conditions="conditions" :languages="languages" :selected="(selected.indexOf(item.id) == -1) ? false : true" @input="toggleSelected" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()" @deleted="remove(index)"></row>
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
        <div id="imgbox" style="position: absolute; left: 200px;" :style="{ top: imgbox.top }">
            <img :src="imgbox.src" v-show="imgbox.show">
        </div>
    </div>
</template>

<script>
    import row from "./row.vue";
    import filterSearch from "../filter/search.vue";
    import filterRarity from "../filter/rarity.vue";
    import filterLanguage from "../filter/language.vue";
    import filterExpansion from "../filter/expansion.vue";

    export default {

        components: {
            row,
            filterRarity,
            filterSearch,
            filterLanguage,
            filterExpansion,
        },

        props: {
            conditions: {
                type: Object,
                required: true,
            },
            languages: {
                required: true,
                type: Object,
            },
            expansions: {
                type: Object,
                required: true,
            },
            rarities: {
                type: Array,
                required: true,
            },
        },

        data () {
            return {
                uri: '/article',
                items: [],
                isLoading: true,
                imgbox: {
                    src: null,
                    show: true,
                },
                paginate: {
                    nextPageUrl: null,
                    prevPageUrl: null,
                    lastPage: 0,
                },
                filter: {
                    show: true,
                    page: 1,
                    searchtext: '',
                    cardmarket_comments: '',
                    language_id: 0,
                    unit_price_min: 0,
                    unit_price_max: 0,
                    unit_cost_min: 0,
                    unit_cost_max: 0,
                    sold: 0,
                },
                selected: [],
                errors: {},
            };
        },

        mounted() {

            this.fetch();
            // this.setInitialFilters();

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
                        Vue.success('Artikel werden im Hintergrund aktualisiert.');
                    })
                    .catch(function (error) {
                        Vue.error('Artikel konnten nicht synchronisiert werden!');
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
            showImgbox({src, top}) {
                this.imgbox.src = src;
                this.imgbox.top = top;
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