<template>
  <div class="page-margin-20-40">
  <v-container fluid>
    <v-row dense>
      <v-col cols="12" md="4" v-for="(item, index) in summaryCards" :key="index">
        <v-card class="pa-4" elevation="5">
          <v-row align="center">
            <v-col cols="3">
              <v-icon size="36" color="primary">{{ item.icon }}</v-icon>
            </v-col>
            <v-col>
              <div class="text-subtitle-1">{{ item.label }}</div>
              <div class="text-h5 font-weight-bold">{{ item.count }}</div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-card class="pa-4 mt-4" elevation="5">
          <h3 class="text-h6 mb-4">Line graph</h3>
          <div class="static-graph">
            <div class="graph-line dataset1">
              <div v-for="(value, index) in dataset1" :key="'d1-' + index" class="point" :style="getStyle(value, index)">
              </div>
            </div>
            <div class="graph-line dataset2">
              <div v-for="(value, index) in dataset2" :key="'d2-' + index" class="point" :style="getStyle(value, index)">
              </div>
            </div>
          </div>
          <div class="d-flex justify-space-between mt-2 text-caption px-2">
            <div v-for="(month, i) in months" :key="i" style="width: 8%; text-align: center">
              {{ month }}
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: "AdminDashboard",
  data() {
    return {
      months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      dataset1: [90, 85, 100, 95, 160, 120, 60, 90, 150, 170, 130, 100],
      dataset2: [90, 90, 90, 85, 80, 70, 40, 80, 60, 130, 90, 60],
      maxValue: 180,
      summaryCards: [
        { label: "Total Users", count: 0, icon: "mdi-account" },
        { label: "Total Orders", count: 0, icon: "mdi-cart" },
        { label: "Revenue", count: 0, icon: "mdi-currency-gbp" }
      ]
    }
  },
  mounted() {
    this.fetchCounts()
    this.getStyle()
  },
  methods: {
    getStyle(value, index) {
      const left = `${index * 8.5}%`
      const bottom = `${(value / this.maxValue) * 100}%`
      return {
        left,
        bottom
      }
    },
    async fetchCounts () {
      try {
        const { data } = await axios.get('/admin/dashboard/vlist')
        this.summaryCards[0].count = data.total_users
        this.summaryCards[1].count = data.total_orders
        this.summaryCards[2].count = data.revenue
      } catch (e) {
        console.error('Failed to load counts', e)
      }
    }
  }
}
</script>

<style scoped>
.static-graph {
  position: relative;
  height: 100px;
  width: 100%;
  border-left: 1px solid #ccc;
  border-bottom: 1px solid #ccc;
}
.graph-line {
  position: absolute;
  height: 100%;
  width: 100%;
}
.point {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  transform: translate(-50%, 50%);
}
.dataset1 .point {
  background-color: #e67e22;
}
.dataset2 .point {
  background-color: #27ae60;
}
</style>