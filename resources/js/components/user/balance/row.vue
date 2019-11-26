<template>
    <tr>
        <td class="align-middle">
            <label class="form-checkbox"></label>
            <input :checked="selected" type="checkbox" :value="id"  @change="$emit('input', id)" number>
        </td>
        <td class="align-middle">{{ item.received_at_formatted }}</td>
        <td class="align-middle">{{ item.id }}</td>
        <td class="align-middle">{{ item.description }}</td>
        <td class="align-middle">{{ item.iban }}</td>
        <td class="align-middle text-right">{{ item.amount_in_euro_formatted }} €</td>
    </tr>
</template>

<script>
    export default {
        props: ['item', 'uri', 'selected'],

        data () {
            return {
                id: this.item.id,
            };
        },
    };
</script>