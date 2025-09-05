<template>
  <div v-if="order" class="page-margin-20-40 page-order-detail" style="margin: 20px 60px !important;">
    <v-row class="mb-2 align-center justify-space-between">
      <v-col cols="12" md="6">
        <div class="d-flex align-center">
          <v-btn class="btn-32-text-12 mr-2" style="color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" :loading="backLoading" :disabled="backLoading" small elevation="0" @click="navigateBack">
            <template #loader>
              <v-progress-circular indeterminate size="16" color="white" />
            </template>
            <v-icon v-if="!backLoading" small>mdi-arrow-left</v-icon>
            <span v-if="!backLoading">Back</span>
          </v-btn>

          <h3 class="text-h6 font-weight-bold mb-0 mr-2">#TR00{{ order.order_id }}</h3>

          <v-chip small class="ma-1" color="grey lighten-2" text-color="black" outlined>
            <v-icon left small>mdi-currency-usd</v-icon>
            {{ order.payment_status }}
          </v-chip>

          <v-chip small class="ma-1" :color="order.fulfillment_status.toLowerCase() === 'fulfilled' ? 'green lighten-4' : 'orange lighten-4'" text-color="black" outlined>
            <v-icon left small>mdi-checkbox-blank-circle</v-icon>
            {{ order.fulfillment_status }}
          </v-chip>
        </div>
      </v-col>

      <v-col cols="12" md="6" class="text-end">
        <!-- <v-btn class="btn-32-text-12 ml-1" small outlined color="success" @click="sendRefund">
          Refund
        </v-btn> -->

        <v-btn v-if="order.payment_status.toLowerCase() !== 'cancelled'" class="btn-32-text-12 ml-1" small outlined style="color: red; background-color: white !important; border: 1px solid red !important;" @click="dialogCancel = true">
          Cancel
        </v-btn>

        <v-btn class="btn-32-text-12 ml-1" style="color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small outlined @click="printPackingSlip">
          Print Packing Slip
        </v-btn>
      </v-col>
    </v-row>

    <div class="d-flex align-center mb-4">
      <span class="text-caption grey--text">{{ order.order_date | niceDate }}</span>
    </div>

    <v-row>
      <v-col cols="12" md="8">
        <v-card v-if="unfulfilledItems.length" elevation="5" class="rounded-3">
          <v-row class="m-0">
            <v-col cols="12" md="6">
            <v-card-title class="py-2 pb-0">
              <div class="subtitle-2 font-weight-bold">Unfulfilled ({{ unfulfilledItems.length }})</div>
            </v-card-title>
            </v-col>
            <v-col cols="12" md="6">
              <v-card-actions class="justify-end">
                <v-btn class="btn-32-text-12" style="color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small @click="openFulfilDialog">
                  Fulfill Item
                </v-btn>
              </v-card-actions>
            </v-col>
          </v-row>
          <v-list one-line>
            <v-list-item v-for="item in unfulfilledItems" :key="item.order_item_id" class="mt-2">
              <div class="order-wrap">
              <v-list-item-avatar size="50">
                <v-img :src="imgSrc(item.variant?.image, item.product?.mproduct_image)" contain/>
              </v-list-item-avatar>

              <v-row no-gutters align="center" class="w-100">
                <v-col cols="4">
                  <strong>{{ item.product.mproduct_title }}</strong>
                  <div class="caption grey--text">SKU: {{ item.variant.sku }}</div>
                </v-col>

                <v-col cols="4">
                  <div class="caption grey--text" v-if="item.variant?.option_value">
                    <div v-for="(val,key) in item.variant.option_value" :key="key">{{ key }}: {{ val }}</div>
                  </div>
                </v-col>

                <v-col cols="2" class="text-right">
                  £{{ item.variant.price | money }} × {{ item.quantity - (item.fulfilled_quantity || 0) }}
                </v-col>

                <v-col cols="2" class="text-right font-weight-medium">
                  £{{ (item.variant.price * (item.quantity - (item.fulfilled_quantity || 0))).toFixed(2) }}
                </v-col>
              </v-row>
            </div>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card v-for="f in (order.fulfillments || [])" :key="f.order_fulfillment_id" elevation="5" class="mb-4 mt-4 rounded-3">
          <v-row class="m-0">
            <v-col cols="12" md="6">
              <v-card-title class="py-2 pb-0">
                <div class="subtitle-2 font-weight-bold">Fulfilled ({{ f.items.length }})</div>
              </v-card-title>
            </v-col>
            <v-col v-if="!f.tracking_id" cols="12" md="6">
              <v-card-actions class="justify-end">
                <v-btn class="btn-32-text-12" style="color: #0cc827; background-color: white !important; border: 1px solid #0cc827 !important;" small @click="openTrackingDialog(f)">
                  + Add Tracking
                </v-btn>
              </v-card-actions>
            </v-col>
            <v-col v-if="f.tracking_id" cols="12" md="6">
              <div class="py-2 pb-0 me-2">
                <div class="subtitle-2 text-end">
                  {{ f.shipping_courier || 'Royal Mail' }} tracking: {{ f.tracking_id }}
                  <!-- <a
                    :href="`https://www.royalmail.com/track-your-item#/tracking-results/${f.tracking_id}`"
                    target="_blank"
                    class="blue--text text-decoration-underline"
                  >
                    {{ f.tracking_id }}
                  </a> -->
                </div>
              </div>
            </v-col>
          </v-row>
          
          

          <v-list dense>
            <v-list-item v-for="itm in f.items" :key="f.order_fulfillment_id + '-' + itm.order_item_id" class="mt-2">
              <div class="order-wrap">
              <v-list-item-avatar size="50">
                <v-img :src="imgSrc(itm.variant?.image, itm.product?.mproduct_image)" contain/>
              </v-list-item-avatar>

              <v-row no-gutters align="center" class="w-100">
                <v-col cols="4">
                  <strong>{{ itm.product.mproduct_title }}</strong>
                  <div class="caption grey--text">SKU: {{ itm.variant.sku }}</div>
                </v-col>

                <v-col cols="4">
                  <div class="caption grey--text" v-if="itm.variant?.option_value">
                    <div v-for="(val, key) in itm.variant.option_value" :key="key">
                      {{ key }}: {{ val }}
                    </div>
                  </div>
                </v-col>

                <v-col cols="2" class="text-right">
                  £{{ itm.variant.price | money }} × {{ itm.quantity }}
                </v-col>

                <v-col cols="2" class="text-right font-weight-medium">
                  £{{ (itm.variant.price * itm.quantity).toFixed(2) }}
                </v-col>
              </v-row>
            </div>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card elevation="5" class="mt-4 rounded-3 px-4">
          <v-card-title class="py-2">
            <div class="subtitle-1 font-weight-bold">
              {{ order.payment_status.toLowerCase() === 'paid' ? 'Paid' : 'Pending' }}
            </div>
          </v-card-title>
          <div class="pp-table-wrap border border-1 rounded-3 overflow-hidden p-2">
          <v-simple-table dense>
            <template #default>
              <tbody>
                <tr>
                  <td>Subtotal</td>
                  <td class="text-right">{{ order.units }} item</td>
                  <td class="text-right">£{{ order.summary.subtotal | money }}</td>
                </tr>
                <tr>
                  <td>Vat</td>
                  <td></td>
                  <td class="text-right">£{{ order.summary.vat | money }}</td>
                </tr>
                <tr>
                  <td>Shipping</td>
                  <td class="text-right">{{ order.delivery.method }}</td>
                  <td class="text-right">£{{ order.summary.delivery_cost | money }}</td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td></td>
                  <td class="text-right">-£{{ order.summary.coupon_discount | money }}</td>
                </tr>
                <tr class="font-weight-bold">
                  <td>Total</td>
                  <td></td>
                  <td class="text-right">£{{ order.summary.total_paid | money }}</td>
                </tr>

                <tr v-if="order.payment_status.toLowerCase() === 'pending'">
                  <td>Wallet</td>
                  <td></td>
                  <td class="text-right">-£{{ order.summary.wallet_discount | money }}</td>
                </tr>
                <tr v-if="order.payment_status.toLowerCase() === 'paid'">
                  <td>Paid</td>
                  <td></td>
                  <td class="text-right">£{{ order.summary.total_paid | money }}</td>
                </tr>
                <tr v-if="order.payment_status.toLowerCase() === 'pending'">
                  <td>Balance</td>
                  <td></td>
                  <td class="text-right">£{{ order.summary.payment_total | money }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
          </div>

          <v-card-actions class="justify-end">
            <v-btn class="btn-32-text-12 mr-2" style="color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small outlined :loading="loadingInvoice" :disabled="loadingInvoice" @click="sendInvoice">
              Send Invoice
            </v-btn>

            <v-btn class="btn-32-text-12" v-if="order.payment_status.toLowerCase() !== 'paid'" small style="color: #0cc827; background-color: white !important; border: 1px solid #0cc827 !important;" :loading="loadingPaid" :disabled="loadingPaid" 
              @click="markAsPaid">
              <template #loader>
                <v-progress-circular indeterminate size="16" color="white" />
              </template>
              Mark as Paid
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>





      <v-col cols="12" md="4">
        <v-card elevation="5" class="mb-4 p-3 rounded-3">
          <v-card-title class="subtitle-2 font-weight-bold p-0 mb-2">Notes</v-card-title>
          <v-card-text class="body-2 p-3 rounded-2 text-center d-flex align-items-center justify-content-center" style="background-color: #eee; min-height: 100px;">
            {{ order.delivery_instructions || 'No notes from customer' }}
          </v-card-text>
        </v-card>

        <v-card elevation="5" class="rounded-3 p-3">
          <v-card-title class="py-2 subtitle-2 font-weight-bold p-0 mb-2">Customer</v-card-title>
          <v-card-text class="body-2 p-3 rounded-2" style="background-color: #eee;">
            <div class="mb-3 font-weight-medium pb-3" style="border-bottom: 1px solid #c4c4c4;">{{ order.user.name }}</div>
            <div class="subtitle-2 font-weight-bold mb-1 text-dark">Customer Information</div>
            <div class="mb-3 pb-3" style="border-bottom: 1px solid #c4c4c4;">
              <div class="d-flex justify-content-between">E-mail Id <span>{{ order.user.email }}</span></div>
              <div class="d-flex justify-content-between">Phone Number <span>{{ order.user.mobile || '—' }}</span></div>
            </div>

            <div class="subtitle-2 font-weight-bold mb-1 text-dark">Shipping Address</div>
            <div class="mb-2 font-weight-medium">{{ order.user.name }}</div>
            <div style="max-width: 50%;">{{ order.delivery.address }}</div>
            <div>{{ order.user.mobile }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="fulfilDialog" max-width="700px">
      <v-card elevation="5">
        <v-card-title class="headline grey lighten-2">
          Fulfil&nbsp;Item&nbsp;Details
        </v-card-title>
        <v-divider/>

        <v-card-text>
          <v-container>
            <v-row v-for="itm in selectableItems" :key="itm.order_item_id" class="align-center mb-2">
              <v-col cols="1" class="text-center">
                <v-checkbox v-model="itm.fulfil" dense hide-details/>
              </v-col>

              <v-col cols="2">
                <v-img height="46" :src="itm.thumb" contain class="rounded"/>
              </v-col>

              <v-col cols="5">
                <div class="subtitle-2 font-weight-medium">
                  {{ itm.product.mproduct_title }}
                </div>
                <div class="caption grey--text text--medium">
                  SKU:&nbsp;{{ itm.variant.sku }}
                </div>
              </v-col>

              <v-col cols="2" class="text-center">
                <v-chip x-small outlined>{{ itm.variant.weight }} {{ itm.variant.weightunit }}</v-chip>
              </v-col>

              <v-col cols="2" class="text-right">
                <v-text-field v-model.number="itm.qtyToFulfil" type="number" dense outlined hide-details class="mr-0" :min="1" :max="itm.remaining" style="width: 70px"/>
                <span class="caption grey--text">of&nbsp;{{ itm.remaining }}</span>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider/>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" text @click="fulfilDialog = false">Close</v-btn>
          <v-btn class="btn-32-text-12" :loading="loadingFulfil" :disabled="loadingFulfil || fulfilCount === 0" color="primary" @click="saveFulfil">
            <template #loader>
              <v-progress-circular indeterminate size="16" color="white" />
            </template>
            Fulfil&nbsp;{{ fulfilCount }} item<span v-if="fulfilCount>1">s</span>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="trackingDialog" max-width="500px">
      <v-card>
        <v-card-title class="headline">Add Tracking</v-card-title>
        <v-card-text>
          <v-text-field v-model="trackingId" label="Tracking Number" />
          <v-text-field v-model="courier" label="Shipping Courier" />
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" text @click="trackingDialog = false">Close</v-btn>
          <v-btn class="btn-32-text-12" color="primary" :loading="loadingTracking" :disabled="loadingTracking" @click="saveTracking">
            <template #loader>
              <v-progress-circular indeterminate size="16" color="white" />
            </template>
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogCancel" max-width="500px">
      <v-card>
        <v-card-title class="headline">Confirm Cancellation</v-card-title>
        <v-card-text>Are you sure you want to cancel this order?</v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" color="blue darken-1" text @click="dialogCancel = false">Close</v-btn>
          <v-btn class="btn-32-text-12" color="red darken-1" text :loading="loadingCancel" :disabled="loadingCancel" @click="cancelOrder">
            Yes, Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>

  <div v-else class="pa-10 text-center">
    <v-progress-circular indeterminate/>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'OrderDetail',
  props: {
    orderid: { type: Number, required: true }
  },

  data () {
    return {
      cdn: 'https://cdn.truewebpro.com/',
      order: null,

      backLoading: false,

      fulfilDialog   : false,
      loadingFulfil  : false,
      selectableItems: [],

      isEditing: false,
      loadingReceipt: false,

      trackingDialog: false,
      currentFulfillment: null,
      trackingId: '',
      courier: '',
      loadingTracking: false,

      loadingPaid: false,
      loadingInvoice: false,

      dialogCancel: false,
      loadingCancel: false,
      dialogRefund: false, 
    }
  },

  computed: {
    unfulfilledItems () {
      if (!this.order) return []
      return this.order.items.filter(i =>
        (i.fulfilled_quantity ?? 0) < (i.quantity ?? 0)
      )
    },
    totalDiscount () {
      const s = this.order?.summary || {}
      return (parseFloat(s.wallet_discount) || 0) + (parseFloat(s.coupon_discount) || 0)
    },
    fulfilCount () {
      return this.selectableItems.filter(itm => itm.fulfil).length
    }
  },

  filters: {
    money (v) {
      const n = parseFloat(v || 0)
      return isNaN(n) ? '0.00' : n.toFixed(2)
    },
    niceDate (v) {
      if (!v) return ''
      const d = new Date(String(v).replace(' ', 'T'))
      const datePart = d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
      const timePart = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })
      return `${datePart} at ${timePart}`
    }
  },

  mounted () {
    this.loadOrder()
  },

  methods: {
    async loadOrder () {
      const { data } = await axios.get(`/admin/vorder/editdata/${this.orderid}`)
      this.order = data.order
    },

    navigateBack () {
      if (this.backLoading) return
      this.backLoading = true
      setTimeout(() => this.$router.push({ name: 'order-list' }), 400)
    },

    imgSrc (variantImg, productImg) {
      if (variantImg) return this.cdn + variantImg
      if (productImg) return this.cdn + productImg
      return '/images/no-image-available.png'
    },

    openFulfilDialog () {
      this.selectableItems = this.unfulfilledItems.map(itm => {
        const remaining = (itm.quantity || 0) - (itm.fulfilled_quantity || 0)
        return {
          ...itm,
          remaining,
          fulfil     : true,
          qtyToFulfil: remaining,
          weight     : itm.variant?.weight ?? 0,
          thumb      : this.imgSrc(itm.variant?.image, itm.product?.mproduct_image),
        }
      })
      this.fulfilDialog = true
    },

    async saveFulfil () {
      if (this.fulfilCount === 0) return;

      this.loadingFulfil = true;
      try {
        const lines = this.selectableItems
          .filter(itm => itm.fulfil)                                  
          .map(itm => {
            const remaining = (itm.quantity || 0) - (itm.fulfilled_quantity || 0);
            let q = parseInt(itm.qtyToFulfil, 10);
            if (isNaN(q)) q = 0;
            q = Math.max(1, Math.min(remaining, q));
            return {
              order_item_id: Number(itm.order_item_id),
              quantity: q,                                              
            };
          })
          .filter(l => l.quantity > 0);

        if (!lines.length) {
          this.$toast.error('Select at least one line to fulfil');
          this.loadingFulfil = false;
          return;
        }

        await axios.post('/admin/orders/fulfill', {
          order_id: this.order.order_id,
          lines
        });

        this.$toast.success('Line(s) fulfilled', { timeout: 600 });
        this.fulfilDialog = false;
        await this.loadOrder();
      } catch (e) {
        console.error(e);
        this.$toast.error('Unable to fulfil item(s)');
      } finally {
        this.loadingFulfil = false;
      }
    },

    openTrackingDialog (f) {
      this.currentFulfillment = f
      this.trackingId = f.tracking_id || ''
      this.courier = f.shipping_courier || ''
      this.trackingDialog = true
    },

    async saveTracking () {
      if (!this.currentFulfillment) return
      this.loadingTracking = true
      try {
        await axios.post('/admin/orders/fulfillments/add-tracking', {
          order_fulfillment_id: this.currentFulfillment.order_fulfillment_id,
          tracking_id: this.trackingId,
          shipping_courier: this.courier
        })
        this.$toast.success('Tracking saved', { timeout: 600 })
        this.trackingDialog = false
        await this.loadOrder()
      } catch (e) {
        console.error(e)
        this.$toast.error('Failed to save tracking')
      } finally {
        this.loadingTracking = false
      }
    },

    async markAsPaid () {
      this.loadingPaid = true
      try {
        const res = await axios.post('/admin/orders/mark-as-paid', {
          order_id: this.order.order_id,
        })
        if (res.data.status) {
          this.$toast.success('Order marked as paid.', { timeout: 500 })
          this.loadOrder()
        } else {
          this.$toast.error(res.data.message || 'Unable to mark as paid.')
        }
      } catch (e) {
        console.error(e)
        this.$toast.error('Failed to mark as paid.')
      } finally {
        this.loadingPaid = false
      }
    },

    async cancelOrder () {
      this.loadingCancel = true
      try {
        const { data } = await axios.post('/admin/order/cancel', {
          order_id: this.order.order_id
        })
        if (data.status) {
          this.$toast.success('Order cancelled successfully.')
          this.dialogCancel = false
          this.loadOrder()
        } else {
          this.$toast.error(data.message || 'Failed to cancel order.')
        }
      } catch (e) {
        console.error(e)
        this.$toast.error('Something went wrong while cancelling the order.')
      } finally {
        this.loadingCancel = false
      }
    },

    async sendInvoice () {
      this.loadingInvoice = true
      try {
        const { data } = await axios.post('/admin/order/send-invoice', {
          order_id: this.order.order_id,
        })
        if (data.status) {
          this.$toast.success('Invoice sent successfully.', { timeout: 1500 })
          this.loadOrder()
        } else {
          this.$toast.error(data.message || 'Failed to send invoice.')
        }
      } catch (e) {
        console.error(e)
        this.$toast.error('Failed to send invoice.')
      } finally {
        this.loadingInvoice = false
      }
    },

    printPackingSlip () {
      const url = `/admin/order/packing-slip/${this.order.order_id}`
      const w = window.open(url, '_blank')
      if (w) w.focus()
    },
    sendRefund () { this.$toast.info('Not implemented yet', { timeout: 500 }) }
  }
}
</script>

<style>
.subtitle-2 { font-size: 14px; }
.order-wrap {
    width: 100%;
    display: flex;
    background-color: #eee;
    border-radius: 10px;
    padding-right: 15px;
}
</style>
