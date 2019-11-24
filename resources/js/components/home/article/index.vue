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
                <div v-else-if="articles.count > 0">
                    <table class="table">
                        <tr>
                            <td>Artikel</td>
                            <td class="text-right">{{ articles.count }}</td>
                        </tr>
                        <tr>
                            <td>Wert Verkaufspreis</td>
                            <td class="text-right">{{ Number(articles.unit_price_sum).format(2, ',', '.') }} €</td>
                        </tr>
                        <tr>
                            <td>Wert Regelpreis</td>
                            <td class="text-right">{{ Number(articles.rule_price_sum).format(2, ',', '.') }} €</td>
                        </tr>
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
                articles: {
                    count: 0,
                    unit_price_sum: 0,
                    rule_price_sum: 0,
                },

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
                        Vue.error('Artikel konnten nicht synchronisiert werden!');
                        console.log(error);
                    })
                    .finally ( function () {

                    });
            },
        },

    };
</script>