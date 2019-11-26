<template>
    <div>
        <div class="row">
            <div class="col"></div>
            <div class="col-auto d-flex">
                <div class="form-group" style="margin-bottom: 0;">
                    <filter-search v-model="filter.searchtext" @input="fetch()"></filter-search>
                </div>
                <button class="btn btn-secondary ml-1" @click="filter.show = !filter.show"><i class="fas fa-filter"></i></button>
                <button class="btn btn-secondary ml-1" @click="sync" :disabled="syncing.status == 1"><i class="fas fa-sync" :class="{'fa-spin': syncing.status == 1}"></i></button>
            </div>
        </div>

        <form v-if="filter.show" id="filter" class="mt-1">
            <div  class="form-row">



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
                        <th width="5%">
                            <label class="form-checkbox" for="checkall"></label>
                            <input id="checkall" type="checkbox" v-model="selectAll">
                        </th>
                        <th width="5%">Datum</th>
                        <th width="5%">#</th>
                        <th width="10%">Käufer</th>
                        <th class="text-right" width="10%">Karten</th>
                        <th class="text-right" width="10%">Umsatz</th>
                        <th class="text-right" width="10%">Kosten</th>
                        <th class="text-right" width="10%">Gewinn</th>
                        <th width="15%">Status</th>
                        <th class="text-center" colspan="3">Bewertung</th>
                        <th class="text-right" width="10%">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :item="item" :key="item.id" :uri="uri" :selected="(selected.indexOf(item.id) == -1) ? false : true" @input="toggleSelected"></row>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>Keine Bestellungen vorhanden</center></div>
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
    </div>
</template>

<script>
    import row from "./row.vue";
    import filterSearch from "../filter/search.vue";

    export default {

        components: {
            row,
            filterSearch,
        },

        props: {
            isSyncingOrders: {
                required: true,
                type: Number,
            },
        },

        data () {
            return {
                uri: '/order',
                items: [],
                isLoading: true,
                syncing: {
                    status: this.isSyncingOrders,
                    interval: null,
                },
                paginate: {
                    nextPageUrl: null,
                    prevPageUrl: null,
                    lastPage: 0,
                },
                filter: {
                    page: 1,
                    searchtext: '',
                },
                selected: [],
            };
        },

        mounted() {

            this.fetch();
            if (this.isSyncingOrders) {
                this.checkIsSyncingOrders();
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
            checkIsSyncingOrders() {
                var component = this;
                this.syncing.interval = setInterval(function () {
                    component.getIsSyncingOrders()
                }, 3000);
            },
            getIsSyncingOrders() {
                var component = this;
                axios.get(component.uri + '/sync')
                    .then(function (response) {
                        component.syncing.status = response.data.is_syncing_orders;
                        if (component.syncing.status == 0) {
                            clearInterval(component.syncing.interval)
                            component.syncing.interval = null;
                            component.fetch();
                            Vue.success('Bestellungen wurden synchronisiert.');
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
                        Vue.error('Bestellungen konnten nicht geladen werden!');
                        console.log(error);
                    });
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
            sync() {
                var component = this;
                axios.put(component.uri + '/sync')
                    .then(function (response) {
                        component.syncing.status = 1;
                        component.checkIsSyncingOrders();
                        Vue.success('Bestellungen werden im Hintergrund synchronisiert.');
                    })
                    .catch(function (error) {
                        Vue.error('Bestellungen konnten nicht synchronisiert werden! Ist das Cardmarket Konto verbunden?');
                        console.log(error);
                    })
                    .finally ( function () {

                    });
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