<template>
  <div class="page-margin-20-40 page-customer-reps">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="12" class="p-0">
          <h2 class="text-h6 mb-1">Customers</h2> 
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :items="reps" :headers="headers" :search="ssearch" item-key="id" 
            :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
            <template v-slot:top>
              <v-row dense class="mx-1 pb-1">
                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Name, E-mail, Mobile"/>
              </v-row>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CustomerReps',

  data() {
    return {
      ssearch: '',
      reps: [],
      headers: [
        { text: 'Customer Name', value: 'name' },
        { text: 'Mobile', value: 'mobile' },
        { text: 'E-mail', value: 'email' },
        { text: 'Company Name', value: 'company_name' }
      ]
    };
  },
  created() {
    this.fetchMyReferrals();
  },
  methods: {
    async fetchMyReferrals() {
      try {
        const res = await axios.get('/rep/vlist');
        this.reps = res.data.users || [];
      } catch (err) {
        console.error('Failed to load referrals', err);
        this.$toast?.error('Unable to fetch referrals');
      }
    }
  }
}
</script>

<style>
.v-input {
  font-size: 12px !important;
}
.v-data-table>.v-data-table__wrapper>table>thead>tr>th,
.page-customer-reps .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>
