<template>
  <div>
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0 align-center justify-space-between">
        <v-col cols="12" md="6" class="p-0">
          <h2 class="text-h6 mb-1">Orders</h2>
        </v-col>
        <v-col cols="12" md="6" class="p-0 d-flex justify-end">
          <v-btn class="text-none btn-32-text-12" small
            style="color: #1976d2; font-weight: bold; background-color: white !important; border: 1px solid #1976d2 !important;"
            @click="syncOrders" :disabled="saving">
            Sync Send Cloud
          </v-btn>

          <v-overlay :value="saving" opacity="0.6" absolute>
            <div class="d-flex flex-column align-center">
              <v-progress-circular indeterminate size="64" color="primary" />
              <div class="mt-4 font-weight-medium">Processing… please wait</div>
            </div>
          </v-overlay>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0 pt-0">
      <v-col cols="12" class="mt-2">
        <v-card elevation="5" style="background-color: transparent;">
          <v-row class="align-center">
            <v-col class="pt-0">
              <v-tabs v-model="activeTab" @change="clearSelection" active-class="grey lighten-3" height="30">
                <v-tab class="text-none" style="font-size: 12px;">All</v-tab>
                <v-tab class="text-none" style="font-size: 12px;">Unfulfilled</v-tab>
                <v-tab class="text-none" style="font-size: 12px;">Unpaid</v-tab>
                <v-tab class="text-none" style="font-size: 12px;">Cancelled</v-tab>
              </v-tabs>
            </v-col>

            <v-col class="d-flex justify-end pt-0" cols="auto" v-if="selected.length > 0">
              <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                  <span class="mr-2 font-weight-medium text-caption">{{ selected.length }} selected</span>
                  <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer; margin-right: 5px;">
                    mdi-dots-vertical
                  </v-icon>
                </template>
                <v-list dense>
                  <v-list-item @click="openConfirmDialog('markPaid')">
                    <v-list-item-title>Mark as Paid</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="openConfirmDialog('markCancle')">
                    <v-list-item-title>Cancel Orders</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="openConfirmDialog('markFulfilled')">
                    <v-list-item-title>Mark as Fulfilled</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="openConfirmDialog('markUnfulfilled')">
                    <v-list-item-title>Mark as Unfulfilled</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="openPrintConfirmDialog = true">
                    <v-list-item-title>Print</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-col>
          </v-row>

          <v-data-table dense v-model="selected" :show-select="true" item-key="order_id" :items="filteredOrders"
            :headers="orderHeaders" :search="ssearch" :items-per-page="50"
            :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
            <template v-slot:top>
              <v-text-field v-model="ssearch" class="px-2 py-1" clearable dense outlined hide-details
                prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Orders" />
            </template>
            <template v-slot:item.order_id="{ item }">
              <router-link :to="{ name: 'order-detail', params: { orderid: item.order_id } }" class="link-dark">
                #TR00{{ item.order_id }}
              </router-link>
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>
            <template v-slot:item.total_amount="{ item }">
              £{{ item.total_amount }}
            </template>
            <template #item.status="{ item }">
              <v-chip :color="statusColor(item.status)" small outlined>{{ item.status }}</v-chip>
            </template>
            <template #item.fulfillment_status="{ item }">
              <v-chip :color="getFulfillmentStatusColor(item.fulfillment_status)" small outlined>
                {{ formatFulfillmentStatus(item.fulfillment_status) }}
              </v-chip>
            </template>
            <template v-slot:item.total_items="{ item }">
              {{ item.total_items }} {{ item.total_items === 1 ? 'item' : 'items' }}
            </template>
            <!-- <template #header.action1>
              <div class="text-center">Action</div>
            </template>
            <template v-slot:item.action1="{ item }">
              <div class="text-center">
                <v-chip v-if="item.status === 'Pending' || item.status === 'Cancelled'" color="green" class="ma-1" outlined pill small style="cursor: pointer;" 
                  @click="changeStatus(item, 'Paid')">Paid
                </v-chip>
              </div>
            </template> -->
            <!-- <template #header.action2>
              <div class="text-center">Action</div>
            </template>
            <template v-slot:item.action2="{ item }">
              <div class="text-center">
                <v-chip v-if="item.status === 'Pending' || item.status === 'Paid'" color="red" class="ma-1" outlined pill small style="cursor: pointer;" 
                  @click="changeStatus(item, 'Cancelled')">Cancelled
                </v-chip>
              </div>
            </template> -->
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>


    <v-dialog v-model="confirmDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm {{ actionLabel }}</v-card-title>
        <v-card-text>
          Are you sure you want to <strong>{{ actionLabel.toLowerCase() }}</strong>
          <strong>{{ selected.length }}</strong> selected orders?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="confirmDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="loadingBulk" :disabled="loadingBulk"
            @click="executeBulkAction">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="openPrintConfirmDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm {{ actionLabel }}</v-card-title>
        <v-card-text>
          Are you sure you want to <strong>Print</strong>
          <strong>{{ selected.length }}</strong> selected orders?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="openPrintConfirmDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="loadingBulk" :disabled="loadingBulk"
            @click="executePrintAction">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'OrderList',
  data() {
    return {
      cdn: 'https://cdn.truewebpro.com/',
      ssearch: '',
      orders: [],
      activeTab: 0,

      selected: [],
      actionToConfirm: '',
      confirmDialog: false,
      loadingBulk: false,
      actionLabel: '',

      openPrintConfirmDialog: false,

      saving: false
    };
  },
  computed: {
    filteredOrders() {
      return this.orders.filter(order => {
        switch (this.activeTab) {
          case 0:
            return true;
          case 1:
            return order.fulfillment_status?.toLowerCase() === 'unfulfilled';
          case 2:
            return order.status?.toLowerCase() === 'pending';
          case 3:
            return order.status?.toLowerCase() === 'cancelled';
          default:
            return true;
        }
      });
    },
    orderHeaders() {
      const headers = [
        { text: 'Order Id', value: 'order_id' },
        { text: 'Date & Time', value: 'created_at' },
        { text: 'Customer', value: 'name' },
        { text: 'Total amount', value: 'total_amount' },
        { text: 'Payment status', value: 'status' },
        { text: 'Fulfillment status', value: 'fulfillment_status' },
        { text: 'Items', value: 'total_items' },
        { text: 'Delivery status', value: '' },
        { text: 'Send Cloud', value: 'cnd_status' },
        { text: 'Delivery method', value: 'delivery_method' },
      ];

      if (this.activeTab === 0) {
        // headers.push({ text: 'Action', value: 'action2', sortable: false, width: '100px' });
      } if (this.activeTab === 1) {
        // headers.push({ text: 'Action', value: 'action1', sortable: false, width: '100px' });
      }

      if (this.activeTab === 2) {
        // headers.push({ text: 'Action', value: 'action2', sortable: false, width: '100px' });
      }

      return headers;
    },
  },
  created() {
    this.getAllOrders();
  },
  methods: {
    async getAllOrders() {
      try {
        const { data } = await axios.get('/admin/orders/vlist');
        if (data.status && Array.isArray(data.orders)) {
          this.orders = data.orders.map((order) => ({
            ...order,
            status: order.status.charAt(0).toUpperCase() + order.status.slice(1),
            fulfillment_status:
              order.fulfillment_status.charAt(0).toUpperCase() + order.fulfillment_status.slice(1),
          }));
        }
      } catch (err) {
        console.error('Error fetching orders:', err);
      }
    },
    formatDate(date) {
      const d = new Date(date);
      return d.toLocaleString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
    },
    clearSelection() {
      this.selected = []; 
    },
    statusColor(status) {
      switch (status.toLowerCase()) {
        case 'paid': return 'green';
        case 'shipped': return 'blue';
        case 'cancelled': return 'red';
        default: return 'orange';
      }
    },
    getFulfillmentStatusColor(fulfillment_status) {
      switch (fulfillment_status?.toLowerCase()) {
        case 'fulfilled':
          return 'green';
        case 'partiallyfulfilled':
          return 'orange';
        case 'unfulfilled':
        default:
          return 'red';
      }
    },
    formatFulfillmentStatus(fulfillment_status) {
      if (!fulfillment_status) return '';

      const map = {
        fulfilled: 'Fulfilled',
        unfulfilled: 'Unfulfilled',
        partiallyfulfilled: 'Partially Fulfilled',
      };

      const key = fulfillment_status.toLowerCase();
      return map[key] || fulfillment_status.charAt(0).toUpperCase() + fulfillment_status.slice(1);
    },
    changeStatus(order, newStatus) {
      order.status = newStatus;
      axios.post('/admin/orders/update-status', {
        order_ids: [order.order_id],
        status: newStatus
      })
        .then(() => {
          this.$toast?.success(`Status updated to ${newStatus}.`, {
            timeout: 500,
            hideProgressBar: true,
            icon: false,
          });
          this.getAllOrders();
        })
        .catch(() => {
          this.$toast?.error('Failed to update status.', { timeout: 500 });
        });
    },
    openConfirmDialog(action) {
      this.actionToConfirm = action;
      this.actionLabel = {
        markPaid: 'Mark as Paid',
        markCancle: 'Mark as Cancle',
        markFulfilled: 'Mark as Fulfilled',
        markUnfulfilled: 'Mark as Unfulfilled',
        print: 'print'
      }[action] || '';
      this.confirmDialog = true;
    },
    async executeBulkAction() {
      this.loadingBulk = true;
      const ids = this.selected.map(p => p.order_id);
      let url = '';
      let payload = {};

      switch (this.actionToConfirm) {
        case 'markPaid':
        case 'markCancle':
          url = '/admin/orders-bulk/mark-status';
          payload = {
            order_ids: ids,
            bulkstatus: this.actionToConfirm === 'markPaid' ? 'paid' : 'cancelled'
          };
          break;
        case 'markFulfilled':
        case 'markUnfulfilled':
          url = '/admin/orders-bulk/mark-fulfillment';
          payload = {
            order_ids: ids,
            bulkfulfilled: this.actionToConfirm === 'markFulfilled' ? 'fulfilled' : 'unfulfilled'
          };
          break;
      }

      try {
        await axios.post(url, payload);
        this.$toast?.success(`${this.actionLabel} successful`, {
          timeout: 500
        })
        this.getAllOrders();
      } catch (err) {
        this.$toast?.error(`Failed to ${this.actionLabel.toLowerCase()}`, {
          timeout: 500
        })
      } finally {
        this.confirmDialog = false;
        this.selected = [];
        this.loadingBulk = false;
      }
    },
    async executePrintAction() {
      this.loadingBulk = true;
      try {
        const base = '/admin/orders-bulk/packing-slips';
        const ids = this.selected.map(r => r.order_id);

        const qs = new URLSearchParams();
        ids.forEach(id => qs.append('order_ids[]', id));

        window.open(`${base}?${qs.toString()}`, '_blank');
        this.$toast?.success('Opening print view...', { timeout: 800 });
      } catch (err) {
        this.$toast?.error('Failed to open print view', { timeout: 800 });
      } finally {
        this.openPrintConfirmDialog = false;
        this.selected = [];
        this.loadingBulk = false;
      }
    },
    async syncOrders() {
      try {
        this.saving = true
        const res = await axios.post('/admin/sendcloud/sync');
        this.$toast.success(`Royal Mail sync: (pushed ${res.data.pushed_now} order)`, { timeout: 800 });
        this.getAllOrders();
      } catch (e) {
        this.$toast.error('Sendcloud keys check.')
      } finally {
        this.saving = false
      }
    }

  },
};
</script>

<style>
.v-input {
  font-size: 12px !important;
}

td.text-start {
  font-size: 12px !important;
}

.uploader-box {
  max-width: 200px;
  max-height: 200px;
  border: 1px dashed #ccc;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
</style>
