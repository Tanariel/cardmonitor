<template>
    <tr>
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle pointer" @click="link">{{ item.cardmarket_order_id }}</td>
        <td class="align-middle text-right pointer" @click="link">{{ item.articles_count }}</td>
        <td class="align-middle text-right pointer" @click="link">{{ Number(item.revenue).toFixed(2) }} €</td>
        <td class="align-middle text-right pointer" @click="link">{{ Number(item.cost).toFixed(2) }} €</td>
        <td class="align-middle text-right pointer" @click="link">{{ Number(item.profit).toFixed(2) }} €</td>
        <td class="align-middle pointer" @click="link">{{ item.state }}</td>
        <td class="align-middle pointer" @click="link" title="Allgemeine Bewertung">{{ item.evaluation_grade || '-' }}</td>
        <td class="align-middle pointer" @click="link" title="Beschreibung der Artikelzustände">{{ item.evaluation_item_description || '-' }}</td>
        <td class="align-middle pointer" @click="link" title="Verpackung der Bestellung">{{ item.evaluation_packaging || '-' }}</td>
        <td class="align-middle text-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary" title="Bearbeiten" @click="link"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-secondary" title="Löschen" @click="destroy"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {

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