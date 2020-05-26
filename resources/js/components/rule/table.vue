<template>
    <div>
        <div class="row">
            <div class="col d-flex align-items-start mb-1 mb-sm-0">
                <div class="form-group mb-0 mr-1">
                    <div>
                        <input type="text" class="form-control" :class="'name' in errors ? 'is-invalid' : ''" v-model="form.name" placeholder="Name" @keydown.enter="create">
                        <div class="invalid-feedback" v-text="'name' in errors ? errors.name[0] : ''"></div>
                    </div>
                </div>
                <button class="btn btn-primary" @click="create"><i class="fas fa-plus-square"></i></button>
            </div>
            <div class="col-auto d-flex">
                <div class="form-group" style="margin-bottom: 0;">
                    <filter-search v-model="filter.searchtext" @input="fetch()"></filter-search>
                </div>
                <button class="btn btn-secondary ml-1" @click="filter.show = !filter.show" v-if="false"><i class="fas fa-filter"></i></button>
                <button class="btn btn-secondary text-overflow-ellipsis ml-1" @click="apply" title="Regeln simulieren" :disabled="applying.status == 1"><i class="fas fa-spinner fa-spin mr-1" v-show="applying.status == 1"></i>{{ $t('rule.simulate') }}</button>
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
                {{ $t('app.loading') }}
            </center>
        </div>
        <div class="table-responsive mt-3" v-else-if="items.length">
            <table class="table table-hover table-striped bg-white">
                <thead>
                    <tr>
                        <th class="w-icon"></th>
                        <th class="text-center w-checkbox">
                            <label class="form-checkbox" for="checkall"></label>
                            <input id="checkall" type="checkbox" v-model="selectAll">
                        </th>
                        <th class="text-center w-icon"></th>
                        <th width="20%">{{ $t('app.name') }}</th>
                        <th class="d-none d-sm-table-cell text-overflow-ellipsis" width="15%">{{ $t('app.expansion') }}</th>
                        <th class="text-center d-none d-sm-table-cell text-overflow-ellipsis" width="10%">{{ $t('app.rarity') }}</th>
                        <th class="d-none d-sm-table-cell" width="10%">{{ $t('rule.price_base') }}</th>
                        <th class="text-right d-none d-xl-table-cell" width="10%">{{ $t('app.article') }}</th>
                        <th class="text-right d-none d-xl-table-cell text-overflow-ellipsis" width="10%">{{ $t('app.price') }}</th>
                        <th class="text-right d-none d-lg-table-cell text-overflow-ellipsis" width="10%">{{ $t('app.price_rule') }}</th>
                        <th class="text-right d-none d-md-table-cell text-overflow-ellipsis" width="5%">{{ $t('app.difference') }}</th>
                        <th class="text-right" width="5%">{{ $t('app.actions.action') }}</th>
                    </tr>
                </thead>
                <draggable v-model="items" tag="tbody" handle=".sort" @end="sort">
                    <row :item="item" :key="item.id" :uri="uri" :selected="(selected.indexOf(item.id) == -1) ? false : true" v-for="(item, index) in items" @input="toggleSelected" @deleted="remove(index)" @updated="updated(index, $event)"></row>
                </draggable>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>{{ $t('rule.alerts.no_data') }}</center></div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center" v-show="paginate.lastPage > 1">
                <li class="page-item" v-show="paginate.prevPageUrl">
                    <a class="page-link" href="#" @click.prevent="filter.page--">{{ $t('app.paginate.previous') }}</a>
                </li>

                <li class="page-item" v-for="(n, i) in pages" v-bind:class="{ active: (n == filter.page) }"><a class="page-link" href="#" @click.prevent="filter.page = n">{{ n }}</a></li>

                <li class="page-item" v-show="paginate.nextPageUrl">
                    <a class="page-link" href="#" @click.prevent="filter.page++">{{ $t('app.paginate.next') }}</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    import draggable from "vuedraggable";

    import row from "./row.vue";
    import filterSearch from "../filter/search.vue";

    export default {

        components: {
            draggable,
            filterSearch,
            row,
        },

        props: {
            isApplyingRules: {
                required: true,
                type: Number,
            },
        },

        data () {
            return {
                uri: '/rule',
                items: [],
                isLoading: true,
                applying: {
                    status: this.isApplyingRules,
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
                form: {
                    name: '',
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
            apply() {
                var component = this;
                axios.post(component.uri + '/apply')
                    .then(function (response) {
                        component.applying.status = 1;
                        component.checkIsApplyingRules();
                        Vue.success(component.$t('rule.successes.simulate_background'));
                    })
                    .catch(function (error) {
                        Vue.error(component.$t('rule.errors.simulated'));
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            create() {
                var component = this;
                axios.post(component.uri, component.form)
                    .then(function (response) {
                        location.href = response.data.path;
                    })
                    .catch( function (error) {
                        component.errors = error.response.data.errors;
                        // Vue.error('Interaktion konnte nicht erstellt werden!');
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
                        Vue.error(component.$t('rule.errors.loaded'));
                        console.log(error);
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
                axios.get(component.uri + '/apply')
                    .then(function (response) {
                        component.applying.status = response.data.is_applying_rules;
                        if (component.applying.status == 0) {
                            clearInterval(component.applying.interval)
                            component.applying.interval = null;
                            component.fetch();
                            Vue.success(component.$t('rule.successes.simulated'));
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            sort() {
                const ranks = this.items.reduce( function (total, item, index) {
                    total[index] = item.id;
                    return total;
                }, []);

                var component = this;
                axios.put('/rule/sort', {
                    rules: ranks,
                })
                    .then(function (response) {
                        Vue.success(component.$t('app.successes.sorted'))
                    })
                    .catch( function (error) {
                        Vue.error(component.$t('app.errors.sorted'));
                });
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
            showPageButton(page) {
                if (page == 1 || page == this.paginate.lastPage) {
                    return true;
                }

                if (page <= this.filter.page + 2 && page >= this.filter.page - 2) {
                    return true;
                }

                return false;
            },
            remove(index) {
                this.items.splice(index, 1);
            },
        },
    };
</script>