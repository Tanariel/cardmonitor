<template>
    <div>
        <div class="row align-items-stretch mb-3">
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">{{ form.name }}</div>
                    <div class="card-body d-flex">
                        <div class="col">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" placeholder="Name" v-model="form.name">
                            </div>
                            <div class="form-group">
                                <label for="name">Beschreibung</label>
                                <textarea class="form-control" placeholder="Beschreibung" v-model="form.description"></textarea>
                            </div>
                        </div>
                        <div class="col">

                        </div>
                        <div class="col">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">Preis</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="base_price">Basis Preis</label>
                            <select class="form-control" id="base_price" v-model="form.base_price">
                                <option :value="id" v-for="(name, id) in basePrices">{{ name }}</option>
                            </select>
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="multiplier_formatted">Multiplikator</label>
                            <input type="text" class="form-control" v-model="form.multiplier_formatted">
                            <small>Hinweis</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="card mb-3">
                <div class="card-header">Karten</div>
                <div class="card-body d-flex">
                    <div class="col">
                        <div class="form-group">
                            <label for="expansion_id">Erweiterung</label>
                            <select id="expansion_id" class="form-control" v-model="form.expansion_id">
                                <option :value="null">Alle Erweiterungen</option>
                                <option :value="id" v-for="(name, id) in expansions">{{ name }}</option>
                            </select>
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="rarity">Seltenheit</label>
                            <select class="form-control" id="rarity" v-model="form.rarity">
                                <option :value="null">Alle Seltenheiten</option>
                                <option :value="name" v-for="(name, id) in rarities">{{ name }}</option>
                            </select>
                            <small>Hinweis</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="price_above_formatted">Preis von</label>
                            <input type="text" class="form-control" v-model="form.price_above_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="price_below_formatted">Preis bis</label>
                            <input type="text" class="form-control" v-model="form.price_below_formatted">
                            <small>Hinweis</small>
                        </div>
                    </div>
                    <div class="col d-flex flex-column">
                        <div class="col d-flex justify-content-center align-items-center">
                                <div class="col form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="is_foil" v-model="form.is_foil">
                                    <label class="form-check-label" for="is_foil">Foil</label>
                                </div>
                                <div class="col form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="is_signed" v-model="form.is_signed">
                                    <label class="form-check-label" for="is_signed">Signed</label>
                                </div>
                        </div>
                        <div class="col d-flex justify-content-center align-items-center">
                                <div class="col form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="is_altered" v-model="form.is_altered">
                                    <label class="form-check-label" for="is_altered">Altered</label>
                                </div>
                                <div class="col form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="is_playset" v-model="form.is_playset">
                                    <label class="form-check-label" for="is_playset">Playset</label>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="false">
            <div class="card mb-3">
                <div class="card-header">Mindestpreise</div>
                <div class="card-body d-flex">
                    <div class="col">
                        <div class="form-group">
                            <label for="min_price_masterpiece_formatted">Masterpiece</label>
                            <input type="text" class="form-control" id="min_price_masterpiece_formatted" v-model="form.min_price_masterpiece_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_mythic_formatted">Mythic</label>
                            <input type="text" class="form-control" id="min_price_mythic_formatted" v-model="form.min_price_mythic_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_rare_formatted">Rare</label>
                            <input type="text" class="form-control" id="min_price_rare_formatted" v-model="form.min_price_rare_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_special_formatted">Special</label>
                            <input type="text" class="form-control" id="min_price_special_formatted" v-model="form.min_price_special_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_time_shifted_formatted">Time Shifted</label>
                            <input type="text" class="form-control" id="min_price_time_shifted_formatted" v-model="form.min_price_time_shifted_formatted">
                            <small>Hinweis</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="min_price_uncommon_formatted">Uncommon</label>
                            <input type="text" class="form-control" id="min_price_uncommon_formatted" v-model="form.min_price_uncommon_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_common_formatted">Commmon</label>
                            <input type="text" class="form-control" id="min_price_common_formatted" v-model="form.min_price_common_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_land_formatted">Land</label>
                            <input type="text" class="form-control" id="min_price_land_formatted" v-model="form.min_price_land_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_token_formatted">Token</label>
                            <input type="text" class="form-control" id="min_price_token_formatted" v-model="form.min_price_token_formatted">
                            <small>Hinweis</small>
                        </div>
                        <div class="form-group">
                            <label for="min_price_tip_card_formatted">Tip Card</label>
                            <input type="text" class="form-control" id="min_price_tip_card_formatted" v-model="form.min_price_tip_card_formatted">
                            <small>Hinweis</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <button type="submit" class="btn btn-primary" @click="update">Speichern</button>
            </div>
        </div>

    </div>
</template>

<script type="text/javascript">
    export default {

        props: {
            basePrices: {
                type: Object,
                required: true,
            },
            expansions: {
                type: Object,
                required: true,
            },
            model: {
                type: Object,
                required: true,
            },
            rarities: {
                type: Array,
                required: true,
            },
        },

        data () {
            return {
                form: {
                    base_price: this.model.base_price,
                    description: this.model.description,
                    expansion_id: this.model.expansion_id,
                    is_altered: this.model.is_altered,
                    is_foil: this.model.is_foil,
                    is_playset: this.model.is_playset,
                    is_signed: this.model.is_signed,
                    min_price_common_formatted: this.model.min_price_common_formatted,
                    min_price_land_formatted: this.model.min_price_land_formatted,
                    min_price_masterpiece_formatted: this.model.min_price_masterpiece_formatted,
                    min_price_mythic_formatted: this.model.min_price_mythic_formatted,
                    min_price_rare_formatted: this.model.min_price_rare_formatted,
                    min_price_special_formatted: this.model.min_price_special_formatted,
                    min_price_time_shifted_formatted: this.model.min_price_time_shifted_formatted,
                    min_price_tip_card_formatted: this.model.min_price_tip_card_formatted,
                    min_price_token_formatted: this.model.min_price_token_formatted,
                    min_price_uncommon_formatted: this.model.min_price_uncommon_formatted,
                    multiplier_formatted: this.model.multiplier_formatted,
                    name: this.model.name,
                    price_above_formatted: this.model.price_above_formatted,
                    price_below_formatted: this.model.price_below_formatted,
                    rarity: this.model.rarity,
                },
                errors: {},
            };
        },

        methods: {
            update() {
                var component = this;
                axios.put(component.model.path, component.form)
                    .then( function (response) {
                        component.errors = {};
                        Vue.success('Regel gespeichert.');
                    })
                    .catch(function (error) {
                        component.errors = error.response.data.errors;
                        Vue.error('Regel konnte nicht gespeichert werden.');
                });
            },
        },
    };
</script>