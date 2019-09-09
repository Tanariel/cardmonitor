<template>
    <div>
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
                        <th></th>
                        <th class="text-right"></th>
                        <th class="">Name</th>
                        <th class="text-right">#</th>
                        <th class="">Erweiterung</th>
                        <th class="text-center">Seltenheit</th>
                        <th class="text-center">Zustand</th>
                        <th class="">Extra</th>
                        <th class="">Hinweise</th>
                        <th class="text-right">Verkaufspreis</th>
                        <th class="text-right">Einkaufspreis</th>
                        <th class="text-right">Provision</th>
                        <th class="text-right" title="Gewinn ohne allgemeine Kosten">Gewinn</th>
                        <th class="text-right" width="10%">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in items">
                        <row :model="model" :item="item" :index="index" :key="item.id" :uri="uri" @deleted="remove(index)" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()"></row>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>Keine Artikel vorhanden</center></div>
        <div id="imgbox" style="position: absolute; left: 125px;" :style="{ top: imgbox.top }">
            <img :src="imgbox.src" v-show="imgbox.show">
        </div>
    </div>
</template>

<script>
    import row from "./row.vue";

    export default {

        components: {
            row,
        },

        props: {
            model: {
                required: true,
                type: Object,
            },
        },

        data () {
            return {
                uri: this.model.path + '/quantity',
                isLoading: false,
                items: this.model.articles,
                filter: {

                },
                form: {

                },
                imgbox: {
                    src: null,
                    show: true,
                },
                errors: {},
            };
        },

        methods: {
            updated(index, item) {
                Vue.set(this.items, index, item);
            },
            remove(index) {
                this.items.splice(index, 1);
                Vue.success('Interaktion gelöscht.');
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