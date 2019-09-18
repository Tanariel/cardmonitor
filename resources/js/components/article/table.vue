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
                        <th>
                            <label class="form-checkbox" for="checkall"></label>
                            <input id="checkall" type="checkbox" v-model="selectAll">
                        </th>
                        <th class="text-center">Status</th>
                        <th class="text-right"></th>
                        <th class="">Name</th>
                        <th class="text-right">#</th>
                        <th class="">Erweiterung</th>
                        <th class="text-center">Seltenheit</th>
                        <th class="text-center">Sprache</th>
                        <th class="text-center">Zustand</th>
                        <th class="text-center">Foil</th>
                        <th class="text-center">Signiert</th>
                        <th class="text-center">Playset</th>
                        <th class="">Hinweise</th>
                        <th class="text-right">Verkaufspreis</th>
                        <th class="text-right">Einkaufspreis</th>
                        <th class="text-right">Provision</th>
                        <th class="text-right" title="Voraussichtlicher Gewinn ohne allgemeine Kosten">Gewinn</th>
                        <th class="text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :item="item" :key="item.id" :uri="uri" :conditions="conditions" :languages="languages" :selected="(selected.indexOf(item.id) == -1) ? false : true" @input="toggleSelected" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()"></row>
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

                <li class="page-item" v-for="n in paginate.lastPage" v-bind:class="{ active: (n == filter.page) }"><a class="page-link" href="#" @click.prevent="filter.page = n">{{ n }}</a></li>

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

    export default {

        components: {
            row,
            filterSearch,
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
                    show: false,
                    page: 1,
                    searchtext: '',
                },
                selected: [],
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
        },
    };
</script>