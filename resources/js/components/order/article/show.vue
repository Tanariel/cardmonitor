<template>
    <div class="container" v-if="item != null">
        <div class="alert alert-dark" role="alert" v-show="counts.open == 0">
            Alle Karten bearbeitet <span v-show="counts.problem > 0">({{ counts.problem }} {{ counts.problem == 1 ? 'Problem' : 'Probleme' }})</span>
        </div>
        <div class="row mb-3">
            <div class="col text-center p-3">
                <img class="img-fluid" :src="item.card.imagePath">
            </div>
            <div class="col d-flex flex-column">
                <div class="mb-3">
                    <div><b>{{ (index + 1) }}: {{ item.localName }} (#{{ item.card.number }}) <span class="flag-icon" :class="'flag-icon-' + item.language.code" :title="item.language.name"></span> </b></div>
                    <div><expansion-icon :expansion="item.card.expansion"></expansion-icon></div>
                    <div><rarity :value="item.card.rarity"></rarity> ({{ item.card.rarity }})</div>
                    <div><condition :value="item.condition"></condition> ({{ item.condition }})</div>
                    <div><i class="fas fa-star text-warning" v-if="item.is_foil"></i></div>
                    <div class="mt-2" v-if="item.storage_id" title="Lagerplatz"><i class="fas fa-boxes"></i> {{ item.storage.full_name }}</div>
                </div>
                <div class="col mb-3">
                    <div class="form-group">
                        <label for="state_comment_boilerplate">Probleme?</label>
                        <select class="form-control" id="state_comment_boilerplate" placeholder="Problem auswählen" @change="form.state_comments += $event.target.value">
                            <option>Probleme?</option>
                            <option>ist nicht vorhanden</option>
                            <option>in schlechterem Zustand als angegeben</option>
                            <option>in falscher Sprache</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="state_comments">Status Kommentar</label>
                        <input type="text" class="form-control" id="state_comments" v-model="form.state_comments" placeholder="Kommentar für Nachricht">
                    </div>
                </div>
                <div>
                    <i class="fas fa-fw mb-3" :class="item.state_icon" :title="item.state_comments"></i> {{ item.state_comments }}
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-danger" @click="next(true, 1)">Nächste Karte (Status Problem)</button>
                    <button class="btn btn-light" @click="next(false)">Weiter</button>
                    <button class="btn btn-primary" @click="next(true, 0)">Nächste Karte (Status OK)</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import condition from '../../partials/emoji/condition.vue';
    import rarity from '../../partials/emoji/rarity.vue';
    import expansionIcon from '../../expansion/icon.vue';

    export default {

        components: {
            condition,
            expansionIcon,
            rarity,
        },

        props: {
            item: {
                required: false,
                type: Object,
                default: null,
            },
            index: {
                required: false,
                type: Number,
                default: 0,
            },
            counts: {
                required: true,
                type: Object,
            },
        },

        watch: {
            item(newValue) {
                this.form.state = newValue ? newValue.state : null;
                this.form.state_comments = newValue ? newValue.state_comments || '' : '';
            },
        },

        data() {
            return {
                form: {
                    state: this.item ? this.item.state : null,
                    state_comments: this.item ? this.item.state_comments || '' : '',
                },
            };
        },

        methods: {
            next(shouldUpdate, state) {
                var component = this;
                if (shouldUpdate == false) {
                    component.$emit('next', component.item);
                    return;
                }
                component.form.state = state;
                axios.put('/article/' + component.item.id, component.form)
                    .then( function (response) {
                        component.errors = {};
                        component.$emit('next', response.data);
                        Vue.success('Artikel gespeichert.');
                    })
                    .catch(function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Artikel konnte nicht gespeichert werden.');
                });
            },
        },
    };
</script>