<template>
<div class="page-margin-20-40  page-customer-commission">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="12" class="p-0">
          <h2 class="text-h6 mb-1">Commissions</h2> 
        </v-col>
      </v-row>
    </v-container>
  
    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table :items="commissions" :headers="headers" :search="ssearch" :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                    <template v-slot:top>
                      <v-row dense class="mx-1 pb-1">
                        <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Name"/>
                      </v-row>
                    </template>
                    <template v-slot:item.name="{ item }">
                        {{ item.name }}
                    </template>

                    <template v-slot:item.product_total="{ item }">
                        £{{ item.product_total }}
                    </template>

                    <template v-slot:item.commission_amount="{ item }">
                        £{{ item.commission_amount }}
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
  name:'CustomerCommission',
  data(){
    return{
        ssearch: '',
        commissions: [],
        headers: [
          { text: 'Customer Name', value: 'name' },
          { text: 'Order Amount', value: 'product_total' },
          { text: 'Commission', value: 'commission_amount' },
        ],
    }
  },
  created() {
    this.fetchRepCommission();
  },
  methods: {
    async fetchRepCommission() {
      try {
        const res = await axios.get('/rep/order-Commission/vlist');
        this.commissions = res.data.commissions || [];
      } catch (err) {
        console.error('Failed to load referrals', err);
        this.$toast?.error('Unable to fetch commission');
      }
    }
  }
  
}
</script>

<style>
.v-input {
  font-size: 12px !important;
}
.v-input__slot{
  min-height: 32px !important;
}
.page-customer-commission .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>
