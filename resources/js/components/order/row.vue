<template>
    <tr>
        <td class="align-middle d-none d-sm-table-cell pointer" @click="link">{{ item.paid_at }}</td>
        <td class="align-middle pointer" @click="link">
            <div>{{ item.cardmarket_order_id }}</div>
            <div class="text-muted">{{ item.buyer.name }}</div>
        </td>
        <td class="align-middle d-none d-md-table-cell text-right pointer" @click="link">{{ item.articles_count }}</td>
        <td class="align-middle d-none d-md-table-cell text-right pointer" @click="link">{{ Number(item.revenue).toFixed(2) }} €</td>
        <td class="align-middle d-none d-xl-table-cell text-right pointer" @click="link">{{ Number(item.cost).toFixed(2) }} €</td>
        <td class="align-middle d-none d-xl-table-cell text-right pointer" @click="link">{{ Number(item.profit).toFixed(2) }} €</td>
        <td class="align-middle d-none d-md-table-cell pointer" @click="link">{{ item.state }}</td>
        <td class="align-middle d-none d-lg-table-cell text-center pointer" @click="link" title="Allgemeine Bewertung"><evaluation :value="item.evaluation ? item.evaluation.grade : 0"></evaluation></td>
        <td class="align-middle d-none d-lg-table-cell text-center pointer" @click="link" title="Beschreibung der Artikelzustände"><evaluation :value="item.evaluation ? item.evaluation.item_description : 0"></evaluation></td>
        <td class="align-middle d-none d-lg-table-cell text-center pointer" @click="link" title="Verpackung der Bestellung"><evaluation :value="item.evaluation ? item.evaluation.packaging : 0"></evaluation></td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Bearbeiten" @click="link"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    import evaluation from '../partials/emoji/evaluation.vue';

    export default {

        components: {
            evaluation,
        },

        props: [ 'item', 'uri', 'selected' ],

        data () {
            return {
                id: this.item.id,
            };
        },

        methods: {
            link () {
                location.href = this.item.path;
            }
        },
    };
</script>