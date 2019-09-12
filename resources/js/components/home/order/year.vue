<template>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            <div class="col">Bestellungen pro Monat</div>
            <div class="form-group mb-0">
                <select class="form-control" v-model="form.year" @change="update">
                    <option value="0">letzte 12 Monate</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                </select>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-8">
                <highcharts :options="chartOptions"></highcharts>
            </div>
            <div class="col-md-4">
                Statistik
            </div>
        </div>
    </div>
</template>

<script>
    import {Chart} from 'highcharts-vue'

    export default {

        components: {
            highcharts: Chart
        },

        data() {
            var date = new Date();

            return {
                form: {
                    year: 0,
                },
                series: [],
                chartOptions: {
                    chart: {
                        type: 'spline',
                    },
                    title: {
                        text: 'Sin chart',
                    },
                    series: [{
                        data: [10, 0, 8, 2, 6, 4, 5, 5],
                        color: '#6fcd98'
                    }],
                },
            };
        },

        methods: {
            update() {
                console.log('updating..');
                this.chartOptions.series[0].data = _.shuffle([10, 0, 8, 2, 6, 4, 5, 5]);
            },
        },
    };
</script>