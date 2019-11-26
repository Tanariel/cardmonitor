<template>
    <div class="col-12 col-md-6 col-lg-4 col-xl mb-3 mb-xl-0 d-none d-xl-block">
        <div class="card h-100">
            <div class="card-header">Artikel</div>
            <div class="card-body">
                <div v-if="isLoading" class="mt-3 p-5">
                    <center>
                        <span style="font-size: 48px;">
                            <i class="fas fa-spinner fa-spin"></i><br />
                        </span>
                        <span v-if="syncing.status == 1">Synchronisiere Artikel</span>
                        <span v-else>Lade Daten..</span>
                    </center>
                </div>
                <div v-else-if="articles.offers.count > 0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-right">Anzahl</th>
                                <th class="text-right">Einkauf</th>
                                <th class="text-right">Verkauf</th>
                                <th class="text-right">Differenz</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Angebote</td>
                                <td class="text-right">{{ Number(articles.offers.count).format(0, '', '.') }}</td>
                                <td class="text-right">{{ Number(articles.offers.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.offers.price).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.offers.price - articles.offers.cost).format(2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td>Regeln</td>
                                <td class="text-right">{{ Number(articles.rules.count).format(0, '', '.') }}</td>
                                <td class="text-right">{{ Number(articles.rules.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.rules.price).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.rules.price - articles.rules.cost).format(2, ',', '.') }} €</td>
                            </tr>
                            <tr>
                                <td>Verkäufe</td>
                                <td class="text-right">{{ Number(articles.sold.count).format(0, '', '.') }}</td>
                                <td class="text-right">{{ Number(articles.sold.cost).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.sold.price).format(2, ',', '.') }} €</td>
                                <td class="text-right">{{ Number(articles.sold.price - articles.sold.cost).format(2, ',', '.') }} €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex align-items-center justify-content-center" v-else>
                    <button class="btn btn-secondary ml-1" @click="sync" :disabled="syncing.status == 1">
                        <i class="fas fa-spinner fa-spin mr-1" v-show="syncing.status == 1"></i> Artikel synchronisieren
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
                        Vue.error('Artikel konnten nicht geladen werden!');
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
                            Vue.success('Artikel wurden synchronisiert.');
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
                        Vue.success('Artikel werden im Hintergrund aktualisiert.');
                    })
                    .catch(function (error) {
                        Vue.error('Artikel konnten nicht synchronisiert werden! Ist das Cardmarket Konto verbunden?');
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
        },

    };
</script>