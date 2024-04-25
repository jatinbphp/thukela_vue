<template>
  <div class="py-4 container-fluid">
    <div v-if="!isLoading" class="row">
      <div class="col-2"></div>
      <div class="col-8">
        <div class="row">
          <div class="col-4">
            <label class="form-label">Start Date</label>
            <flat-pickr
                v-model="fromDate"
                class="form-control datetimepicker"
                placeholder="Please select start date"
                :config="config"
            ></flat-pickr>
          </div>
          <div class="col-4">
            <label class="form-label">End Date</label>
            <flat-pickr
                v-model="toDate"
                class="form-control datetimepicker"
                placeholder="Please select end date"
                :config="config"
            ></flat-pickr>
          </div>
          <div class="col-4">
            <button
                type="button"
                name="button"
                class="btn bg-gradient-success ms-2"
                style="margin-top: 29px;"
                @click="submitDateRange"
            >
              Submit
            </button>
          </div>
        </div>
      </div>
      <div class="col-2"></div>
    </div>
    <div class="mt-4 row">
      <div class="col-md-12">
        <div v-if="isLoading" class="loader-container d-flex justify-content-center">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div v-else>
          <div v-if="Object.keys(this.provisionbill).length !== 0" class="col-md-12 text-center">
            <label class="h6">AccountId: {{this.provisionbill.key1}}</label>
          </div>
          <div v-if="chartData.labels.length > 0 && Object.keys(chartData.datasets).length > 0">
            <bar-chart :chart="chartData" />
          </div>
          <div v-else>No data available</div>
          <div v-if="Object.keys(this.provisionbill).length !== 0" class="col-md-12">
            <h4 class="card-title text-center mt-3" style="color: #FFFFFF;">Provisional Bill</h4>
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-3 font-weight-bold text-heading">
                    Customer :
                  </div>
                  <div class="col-md-3 text-data">
                    {{this.provisionbill.key2}}
                  </div>
                  <div class="col-md-3 font-weight-bold text-heading">
                    Document Date :
                  </div>
                  <div class="col-md-3 text-data">
                    {{this.provisionbill.documentdate}}
                  </div>

                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Meter Account :
                  </div>
                  <div class="col-md-9 mt-2 text-data">
                    {{this.provisionbill.key2}}
                  </div>

                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Period :
                  </div>
                  <div class="col-md-9 mt-2 text-data">
                    From {{this.provisionbill.startdate}} to {{this.provisionbill.enddate}}
                  </div>

                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Tariff :
                  </div>
                  <div class="col-md-9 mt-2 text-data">
                    {{this.provisionbill.tariff}}
                  </div>

                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Meter Totals :
                  </div>
                  <div class="col-md-3 mt-2 text-data">
                    {{this.provisionbill.readings.mr.snumber}}
                    ({{this.provisionbill.readings.mr.mtype}})
                  </div>
                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Consumption :
                  </div>
                  <div class="col-md-3 mt-2 text-data">
                    {{totalNewConsumption()}}{{unit()}}
                  </div>

                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    Start reading :
                  </div>
                  <div class="col-md-3 mt-2 text-data">
                    {{this.provisionbill.readings.mr.start.E1}}
                  </div>
                  <div class="col-md-3 font-weight-bold mt-2 text-heading">
                    End reading :
                  </div>
                  <div class="col-md-3 mt-2 text-data">
                    {{this.provisionbill.readings.mr.end.E1}}
                  </div>
                </div>
              </div>
              <div class="col-md-12 mt-4">
                <span class="font-weight-bold text-data">{{this.provisionbill.key1}} {{this.provisionbill.util}} : Consumption : {{totalNewConsumption()}}{{unit()}}</span>
                <div class="table-responsive">
                  <table class="table">
                    <thead class="text-primary">
                    <tr>
                      <th>Tariff</th>
                      <th>Description</th>
                      <th>Units</th>
                      <th>Rate[R]</th>
                      <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody v-if="this.provisionbill.bill.li && this.provisionbill.bill.li.length > 0">
                      <tr v-for="(list, key) in this.provisionbill.bill.li" :key="key">
                        <td>{{ list.tname }}</td>
                        <td>{{ (Array.isArray(list.desc2)) ? '' : list.desc2 }}</td>
                        <td>{{ numberFormat(list.units, 2) + list.unitsunit }}</td>
                        <td>R{{ numberFormatThousand(list.rate, 2)}}</td>
                        <td>R{{ numberFormatThousand(list.amount, 2)}}</td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font-weight-bold">Sub Total</td>
                        <td>R{{ numberFormatThousand(this.provisionbill.total_taxed, 2) }}</td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font-weight-bold">
                          <div>Total before {{ this.provisionbill.taxtype }}</div>
                          <div class="mt-2">{{ this.provisionbill.taxtype }} ({{ numberFormat(this.provisionbill.taxperc, 1) + '%' }})</div>
                          <div class="mt-2">Total</div>
                        </td>
                        <td>
                          <div>R{{ numberFormatThousand(this.provisionbill.total_taxed, 2) }}</div>
                          <div class="mt-2">R{{ numberFormatThousand(this.provisionbill.tax, 2) }}</div>
                          <div class="mt-2">R{{ numberFormatThousand(this.provisionbill.total, 2) }}</div>
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-else>
                      <tr>
                        <td colspan="5">Data Not Found</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div v-else>No data available</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BarChart from "./pages/components/BarChart.vue";
import flatPickr from "vue-flatpickr-component";
import axios from "axios";

export default {
  name: "Charts",
  components: {
    BarChart,
    flatPickr,
  },
  data() {
    return {
      isLoading: true,
      chartData: {
        labels: [],
        datasets: {},
      },
      provisionbill: {},
      fromDate: "",
      toDate: "",
      config: {
        allowInput: true,
      },
    };
  },
  mounted() {
    var isLoggedIn = localStorage.getItem('is_logged_in');
    if(!isLoggedIn || isLoggedIn == 'false'){
      this.$router.push('/login/');
    }
  },
  async created() {
    try {
      const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
      const response = await axios.get(API_URL + "provisionBill", {
        params: {
          username: this.$store.getters.getUsername,
          password: this.$store.getters.getPassword,
        },
      });
      this.prepareData(response);
    } catch (error) {
      console.error("Error fetching chart data:", error);
      this.isLoading = false;
    }
  },
  methods:{
    getEndR() {
      return parseFloat(this.provisionbill.readings.mr.end.E1.replace(/[a-zA-Z]/g, ''));
    },
    getStartR() {
      return parseFloat(this.provisionbill.readings.mr.start.E1.replace(/[a-zA-Z]/g, ''));
    },
    totalTeriff() {
      return this.provisionbill.bill.li || [];
    },
    unit() {
      const totalTeriff = this.totalTeriff();
      return totalTeriff.length > 0 ? totalTeriff[0].unitsunit : '';
    },
    totalNewConsumption() {
      const endR = this.getEndR();
      const startR = this.getStartR();
      return (endR - startR).toFixed(2);
    },
    numberFormat(value, decimals) {
      return parseFloat(value).toFixed(decimals);
    },
    numberFormatThousand(value, decimals) {
      if (isNaN(value)) {
        return value;
      }
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      }).format(value);
    },
    prepareData(response){
      if (response.data && response.data.chartDates && response.data.readingData) {
        const datasets = response.data.readingData.map(item => ({
          label: item.label,
          backgroundColor: item.backgroundColor,
          borderColor: item.borderColor,
          data: item.data
        }));
        this.chartData = {
          labels: response.data.chartDates,
          datasets: datasets,
        };
        this.isLoading = false;
        this.provisionbill = response.data.provisionalBill;
      } else {
        console.error("Invalid response data:", response.data);
        this.isLoading = false;
      }
    },
    async submitDateRange() {
      try {
        this.isLoading = true;
        const API_URL = process.env.VUE_APP_API_BASE_URL + '/';
        const response = await axios.get(API_URL + "provisionBill", {
          params: {
            username: this.$store.getters.getUsername,
            password: this.$store.getters.getPassword,
            sDate: this.fromDate,
            eDate: this.toDate
          }
        });
        this.prepareData(response);
      } catch (error) {
        console.error("Error fetching chart data:", error);
        this.isLoading = false;
      }
    },
  }
};
</script>

<style>
.loader-container {
  /* Center the loader */
  display: flex;
  justify-content: center;
  align-items: center;
  height:70vh; /* Adjust as needed */
}

.container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
  width: 100%;
  padding-right: var(--bs-gutter-x, 1.5rem);
  padding-left: var(--bs-gutter-x, 1.5rem);
  margin-right: auto;
  margin-left: auto;
  background: rgb(18, 15, 38);
  background: linear-gradient(180deg, rgba(18, 15, 38, 1) 0%, rgba(45, 54, 159, 1) 100%);
  border-radius: 15px;
}

#sidenav-main {
  overflow: hidden !important;
  background-color: #0C0E2F !important;
}

.form-label, label {
  font-size: .75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #FFF !important;
  margin-left: 0.25rem;
}

.h6 {
  margin-top: 0;
  margin-bottom: 0.5rem;
  font-weight: 400;
  line-height: 1.2;
  color: #FFF !important;
}

body {
  margin: 0;
  font-family: var(--bs-body-font-family);
  font-size: var(--bs-body-font-size);
  font-weight: var(--bs-body-font-weight);
  line-height: var(--bs-body-line-height);
  color: #FFF;
  text-align: var(--bs-body-text-align);
  background-color: var(--bs-body-bg);
  -webkit-text-size-adjust: 100%;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}

.text-primary {
  color: #01b574!important;
}

.table td, .table th {
  white-space: nowrap;
  color: #FFF;
}

.opacity-4 {
  opacity: .9 !important;
}

a {
  letter-spacing: -.025rem;
  color: #FFF;
}

.navbar .sidenav-toggler-inner .sidenav-toggler-line {
  transition: all .15s ease;
  background: #FFF;
  border-radius: 0.125rem;
  position: relative;
  display: block;
  height: 2px;
}

.text-dark {
  color: #01b574 !important;
}

.navbar .nav-link, .navbar .navbar-brand {
  color: #FFF !important;
  font-size: .875rem;
}

.text-muted {
  color: #FFF !important;
}

.navbar-vertical .navbar-nav>.nav-item .nav-link.active {
  color: #0b0e34 !important;
  background-color: #fff;
}

.table thead th {
  padding: 0.75rem 1.5rem;
  letter-spacing: 0;
  border-bottom: 1px solid #e9ecef;
  text-transform: uppercase;
  color: #01b574;
}

.text-data {
  white-space: nowrap;
  color: #FFF;
}

.text-heading{
  color: #01b574;
}

@media (min-width: 992px)
.justify-content-lg-between {
  justify-content: center;
}

</style>
