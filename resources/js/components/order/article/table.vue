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
                        <th width="100"></th>
                        <th width="75" class="text-center">Status</th>
                        <th width="75" class="text-right"></th>
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
                        <row :model="model" :item="item" :index="index" :key="item.id" :uri="uri" @deleted="remove(index)" @updated="updated(index, $event)" @show="showImgbox($event)" @hide="hideImgbox()" @toshow="toshow(index, item)"></row>
                    </template>
                </tbody>
                <tfoot>
                    <tr v-show="counts.open > 0">
                        <td><b>Offen</b></td>
                        <td class="text-center"><b>{{ counts.open }}</b></td>
                        <td colspan="13"></td>
                    </tr>
                    <tr v-show="counts.problem > 0">
                        <td><b>Probleme</b></td>
                        <td class="text-center"><b>{{ counts.problem }}</b></td>
                        <td colspan="13"></td>
                    </tr>
                    <tr v-show="counts.ok > 0">
                        <td><b>OK</b></td>
                        <td class="text-center"><b>{{ counts.ok }}</b></td>
                        <td colspan="13"></td>
                    </tr>
                    <tr>
                        <td><b>Gesamt</b></td>
                        <td class="text-center"><b>{{ counts.all }}</b></td>
                        <td colspan="8"></td>
                        <td class="text-right font-weight-bold">{{ sums.unit_price.toFixed(2) }} €</td>
                        <td class="text-right font-weight-bold">{{ sums.unit_cost.toFixed(2) }} €</td>
                        <td class="text-right font-weight-bold">{{ sums.provision.toFixed(2) }} €</td>
                        <td class="text-right font-weight-bold">{{ sums.profit.toFixed(2) }} €</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="alert alert-dark mt-3" v-else><center>Keine Artikel vorhanden</center></div>
        <div id="imgbox" style="position: absolute;" :style="{ top: imgbox.top,  left: imgbox.left }">
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
            initialItems: {
                required: true,
                type: Array,
            },
            counts: {
                required: true,
                type: Object,
            },
        },

        computed: {
            sums() {
                var profit = 0,
                    unit_price = 0,
                    unit_cost = 0,
                    provision = 0;
                for (var index in this.items) {
                    profit += (Number(this.items[index]['unit_price']) - Number(this.items[index]['unit_cost']) - Number(this.items[index]['provision']));
                    unit_price += Number(this.items[index]['unit_price']);
                    unit_cost += Number(this.items[index]['unit_cost']);
                    provision += Number(this.items[index]['provision']);
                }

                return {
                    profit: profit,
                    provision: provision,
                    unit_cost: unit_cost,
                    unit_price: unit_price,
                };
            },
        },

        data () {
            return {
                uri: this.model.path + '/quantity',
                isLoading: false,
                items: this.initialItems,
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
            toshow(index, item) {
                this.$emit('toshow', {
                    index: index,
                    item: item,
                });
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
        },
    };
</script>