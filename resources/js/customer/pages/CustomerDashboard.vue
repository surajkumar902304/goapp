<template>
  <div class="page-margin-20-40">
  <v-container fluid>
    <v-row dense>
      <v-col v-for="(item, idx) in summaryCards" :key="idx" cols="12" md="4">
        <v-card class="pa-4" elevation="5">
          <v-row align="center">
            <v-col cols="3">
              <v-icon size="36" color="primary">{{ item.icon }}</v-icon>
            </v-col>
            <v-col>
              <div class="text-subtitle-1">{{ item.label }}</div>
              <div class="text-h5 font-weight-bold">
                {{ item.count }}
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CustomerDashboard',

  data () {
    return {
      summaryCards: [
        { label: 'Total Customers', count: 0,   icon: 'mdi-account' },
        { label: 'Total Orders', count: 0, icon: 'mdi-cart'    },
        { label: 'Total Commission',count: 0, icon: 'mdi-currency-gbp' }
      ]
    }
  },
  mounted () {
    this.fetchCounts()
  },
  methods: {
    async fetchCounts () {
      try {
        const { data } = await axios.get('/rep/dashboard/vlist')
        this.summaryCards[0].count = data.total_users
        this.summaryCards[1].count = data.total_orders
        this.summaryCards[2].count = data.total_commission
      } catch (e) {
        console.error('Failed to load counts', e)
      }
    }
  }
}
</script>

<style>
thead tr th {
    background-color: #dbdbdb;
}
</style>
