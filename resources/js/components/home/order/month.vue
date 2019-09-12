<template>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            <div class="col">Bestellungen pro Tag</div>
            <div class="form-group mr-1 mb-0">
                <select class="form-control" v-model="form.month">
                    <option value="1">Januar</option>
                    <option value="2">Februar</option>
                    <option value="3">März</option>
                    <option value="4">April</option>
                    <option value="5">Mai</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Dezember</option>
                </select>
            </div>
            <div class="form-group mb-0">
                <select class="form-control" v-model="form.year" @input="update">
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
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
                    month: date.getMonth() + 1,
                    year: date.getFullYear(),
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