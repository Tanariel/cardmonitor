<template>
    <div>
        <div class="card h-100">
            <div class="card-header">Artikel</div>
            <div class="card-body">
                <div v-if="isLoading" class="mt-3 p-5">
                    <center>
                        <span style="font-size: 48px;">
                            <i class="fas fa-spinner fa-spin"></i><br />
                        </span>
                        <span v-if="syncing.status == 1">{{ $t('article.is_syncing') }}</span>
                        <span v-else>{{ $t('app.loading') }}</span>
                    </center>
                </div>
                <div v-else-if="articles.offers.count > 0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-right text-overflow-ellipsis">{{ $t('app.amount') }}</th>
                                <th class="text-right d-none d-xl-table-cell text-overflow-ellipsis">{{ $t('app.purchase') }}</th>
                                <th class="text-right text-overflow-ellipsis">{{ $t('app.sale') }}</th>
                                <th class="text-right d-none d-lg-table-cell text-overflow-ellipsis">{{ $t('app.difference') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-overflow-ellipsis">{{ $t('app.offers') }}</td>
                                <td class="text-right">{{ Number(articles.offers.count).format(0, '', '.') }}</td>
                                <td class="text-right d-none d-xl-table-cell">{{ Number(articles.offers.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.offers.price).format(2, ',', '.') }} €</td>
                                <td class="text-right d-none d-lg-table-cell">{{ Number(articles.offers.price - articles.offers.cost).format(2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td class="text-overflow-ellipsis">{{ $t('rule.plural') }}</td>
                                <td class="text-right">{{ Number(articles.rules.count).format(0, '', '.') }}</td>
                                <td class="text-right d-none d-xl-table-cell">{{ Number(articles.rules.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.rules.price).format(2, ',', '.') }} €</td>
                                <td class="text-right d-none d-lg-table-cell">{{ Number(articles.rules.price - articles.rules.cost).format(2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td class="text-overflow-ellipsis">{{ $t('app.sales') }}</td>
                                <td class="text-right">{{ Number(articles.sold.count).format(0, '', '.') }}</td>
                                <td class="text-right d-none d-xl-table-cell">{{ Number(articles.sold.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.sold.price).format(2, ',', '.') }} €</td>
                                <td class="text-right d-none d-lg-table-cell">{{ Number(articles.sold.price - articles.sold.cost).format(2, ',', '.') }} €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex align-items-center justify-content-center" v-else>
                    <button class="btn btn-secondary ml-1" @click="sync" :disabled="syncing.status == 1">
                        <i class="fas fa-spinner fa-spin mr-1" v-show="syncing.status == 1"></i> {{ $t('article.sync') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: {
            isSyncingArticles: {
                required: true,
                type: Number,
            },
        },

        data() {
            return {
                uri: '/home/article',
                isLoading: true,
                syncing: {
                    status: this.isSyncingArticles,
                    interval: null,
                },
                articles: {},
            };
        },

        mounted() {
            if (this.isSyncingArticles) {
                this.checkIsSyncingArticles();
            }
            else {
                this.fetch();
            }
        },

        methods: {
            fetch() {
                var component = this;
                component.isLoading = true;
                axios.get(component.uri)
                    .then(function (response) {
                        component.articles = response.data;
                        component.isLoading = false;
                    })
                    .catch(function (error) {
                        Vue.error(component.$t('app.errors.loading'));
                        console.log(error);
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
                axios.get('/article/sync')
                    .then(function (response) {
                        component.syncing.status = response.data.is_syncing_articles;
                        if (component.syncing.status == 0) {
                            clearInterval(component.syncing.interval)
                            component.syncing.interval = null;
                            component.fetch();
                            Vue.success(component.$t('article.synced'));
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
            sync() {
                var component = this;
                axios.put('/article/sync')
                    .then(function (response) {
                        component.syncing.status = 1;
                        component.checkIsSyncingArticles();
                        Vue.success(component.$t('article.syncing'));
                    })
                    .catch(function (error) {
                        Vue.error(component.$t('article.syncing_error'));
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
        },

    };
</script>