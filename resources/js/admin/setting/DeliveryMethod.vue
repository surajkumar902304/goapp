<template>
<div class="page-margin-20-40 page-delivery-method">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Delivery Methods</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openDialog">
              Add Method
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table :headers="deliverymethodsHeaders" :items="deliverymethods" :search="ssearch" 
                    :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
                    <template v-slot:top>
                      <v-row dense class="mx-1 pb-1">
                        <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Delivery Methods"/>
                      </v-row>
                    </template>
                    <template #item.delivery_method_name="{ item }">
                        <span>{{ item.delivery_method_name }}</span>
                    </template>
                    <template #item.delivery_method_amount="{ item }">
                        <span>£{{ item.delivery_method_amount }}</span>
                    </template>
                    <template #item.is_active="{ item }">
                        <v-switch v-model="item.is_active" :input-value="item.is_active === 1" @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
                    </template>
                    <template #header.actions1>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions1="{ item }">
                        <div class="text-center">
                            <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" style="cursor: pointer;">
                              <v-icon small left>mdi-pencil</v-icon>Edit
                            </v-chip>
                        </div>
                    </template>
                    <template #header.actions2>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions2="{ item }">
                        <div class="text-center">
                            <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;" >
                                <v-icon small left>mdi-delete</v-icon>Delete
                            </v-chip>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>

    <v-dialog v-model="addSdialog" max-width="600" @update:model-value="onDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          <span>{{ editedIndex === -1 ? 'Add Delivery Method' : 'Edit Delivery Method' }}</span>
          <v-spacer></v-spacer>
          <v-icon @click="addSdialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="fsvalid" @submit.prevent="saveDeliveryMethod">
          <v-card-text>
            <v-text-field v-model="defaultItem.delivery_method_name" :rules="methodnameRules" label="Delivery Method Name"/>
            <v-text-field v-model="defaultItem.delivery_method_amount" type="number" :rules="amountRules" label="Delivery Method Amount"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :disabled="!fsvalid || submitting">
              {{ editedIndex === -1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">
          Confirm Delete
        </v-card-title>
        <v-card-text>
          Are you sure you want to delete this Delivery Method?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="mt-8">
      <v-col cols="12">
        <h2 class="text-h6 mb-1">Free Delivery Minimum Order</h2>
      </v-col>
    </v-row>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :headers="minHeaders" :items="minRequirements" :items-per-page="-1" hide-default-footer>
            <template #item.value="{ item }">
              £{{ item.value }}
            </template>
            <template #item.is_active="{ item }">
              <v-switch v-model="item.is_active" @change="toggleMinStatus(item)" dense inset style="transform:scale(0.75);"/>
            </template>
            <template #header.actions><div class="text-center">Action</div></template>
            <template #item.actions="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small @click="openMinDialog(item)" style="cursor:pointer;">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="addMinDialog" max-width="600" @update:model-value="onMinDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          {{ editedMinIndex === -1 ? 'Add Minimum Requirement' : 'Edit Minimum Requirement'}}
          <v-spacer/>
          <v-icon @click="addMinDialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="minFormValid" @submit.prevent="saveMinRequirement">
          <v-card-text>
            <v-text-field v-model="defaultMinItem.value" type="number" :rules="[ v => (!!v && v.toString().trim() !== '') || 'Amount is required.', v => (/^[0-9]+$/.test(v)) || 'Only whole numbers allowed.']" 
              label="Minimum Order Amount" outlined/>
          </v-card-text>
          <v-card-actions>
            <v-spacer/>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :loading="savingMin" :disabled="!minFormValid">
              {{ editedMinIndex===-1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-row class="mt-8">
      <v-col cols="12">
        <h2 class="text-h6 mb-1">Minimum Order Place</h2>
      </v-col>
    </v-row>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :headers="OrderPlaceHeaders" :items="OrderPlace" :items-per-page="-1" hide-default-footer>
            <template #item.value="{ item }">
              £{{ item.value }}
            </template>
            <template #item.is_active="{ item }">
              <v-switch v-model="item.is_active" @change="toggleMinOrderPlace(item)" dense inset style="transform:scale(0.75);"/>
            </template>
            <template #header.actions><div class="text-center">Action</div></template>
            <template #item.actions="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small @click="openMinOrderPlace(item)" style="cursor:pointer;">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="addOrderPlace" max-width="600" @update:model-value="MinOrderPlaceDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          {{ orderPlaceIndex === -1 ? 'Add Minimum Order Place' : 'Edit Minimum Order Place'}}
          <v-spacer/>
          <v-icon @click="addOrderPlace = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="orderPlaceFormValid" @submit.prevent="saveMinOrderPlace">
          <v-card-text>
            <v-text-field v-model="defaultOrderPlaceItem.value" type="number" :rules="[ v => (!!v && v.toString().trim() !== '') || 'Amount is required.', v => (/^[0-9]+$/.test(v)) || 'Only whole numbers allowed.']" 
              label="Minimum Order Amount" outlined/>
          </v-card-text>
          <v-card-actions>
            <v-spacer/>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :loading="savingOrderPlace" :disabled="!orderPlaceFormValid">
              {{ orderPlaceIndex===-1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-row class="mt-8">
      <v-col cols="12">
        <h2 class="text-h6 mb-1">Product Vat</h2>
      </v-col>
    </v-row>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :headers="vatHeaders" :items="vatRequirements" :items-per-page="-1" hide-default-footer>
            <template #item.product_vat="{ item }">
              {{ item.product_vat }}
            </template>
            <template #header.actions><div class="text-center">Action</div></template>
            <template #item.actions="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small @click="openVatDialog(item)" style="cursor:pointer;">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="addVatDialog" max-width="600" @update:model-value="onVatDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          {{ editedVatIndex === -1 ? 'Add Product Vat' : 'Edit Product Vat'}}
          <v-spacer/>
          <v-icon @click="addVatDialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="vatFormValid" @submit.prevent="saveVat">
          <v-card-text>
            <v-text-field v-model="defaultVatItem.product_vat" type="number" :rules="[ v => (!!v && v.toString().trim() !== '') || 'Vat is required.', v => (/^[0-9]+$/.test(v)) || 'Only whole numbers allowed.',
              v => (parseInt(v) >= 1 && parseInt(v) <= 99) || 'Value must be between 1 and 99.']" label="Product Vat" outlined/>
          </v-card-text>
          <v-card-actions>
            <v-spacer/>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :loading="savingVat" :disabled="!vatFormValid">
              {{ editedVatIndex===-1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'DeliveryMethod',
  data() {
    return {
      ssearch: '',
      deliverymethods: [],
      deliverymethodsHeaders: [
        { text: 'Delivery method name', value: 'delivery_method_name' },
        { text: 'Amount', value: 'delivery_method_amount' },
        { text: 'Status', value: 'is_active' },
        { text: 'Action', value: 'actions1', sortable: false },
        { text: 'Action', value: 'actions2', sortable: false },
      ],

      addSdialog: false,
      editedIndex: -1,
      fsvalid: false,
      submitting: false,

      defaultItem: {
        delivery_method_id: null,
        delivery_method_name: '',
        delivery_method_amount: null,
      },
      methodnameRules: [
        v => !!v || 'Delivery Method Name is required',
        v => (v && v.length <= 255) || 'Delivery Method Name must be less than 255 characters',
      ],
      amountRules: [
        v => !!v || 'Delivery Amount is required',
        (v) => v === "" || (!isNaN(v) && v >= 0) || "Amount must be a positive number",
        v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Amount up to 2 decimal places"
      ],
      deleteDialog: false,
      deliveryMethodToDelete: null,
      deleteLoading: false,

      minRequirements: [],
      minHeaders: [
        { text:'Amount',    value:'value' },
        { text:'Status',    value:'is_active' },
        { text:'Action',    value:'actions',  sortable:false },
      ],
      addMinDialog: false,
      editedMinIndex: -1,
      minFormValid: false,
      savingMin: false,
      defaultMinItem: { setting_id:null, value:null },

      OrderPlace: [],
      OrderPlaceHeaders: [
        { text:'Amount',    value:'value' },
        { text:'Status',    value:'is_active' },
        { text:'Action',    value:'actions',  sortable:false },
      ],
      addOrderPlace: false,
      orderPlaceIndex: -1,
      orderPlaceFormValid: false,
      savingOrderPlace: false,
      defaultOrderPlaceItem: { setting_id:null, value:null },

      vatRequirements: [],
      vatHeaders: [
        { text:'Vat (%)',  value:'product_vat' },
        { text:'Action',   value:'actions',  sortable:false },
      ],
      addVatDialog: false,
      editedVatIndex: -1,
      vatFormValid: false,
      savingVat: false,
      defaultVatItem: { product_vat_id:null, product_vat:null },
    
    }
  },
  created() {
    this.getAlldeliverymethods();
    this.getAllSettings();
    this.getAllVat();
    this.getMinOrderPlace();
  },
  watch: {
    addSdialog(val) {
      if (!val) this.submitting = false
    }
  },
  methods: {
    async getAllSettings() {
      const res = await axios.get('/admin/settings/min-order/vlist')
      this.minRequirements = res.data.minOrder
    },
    async toggleMinStatus(item) {
      try {
        await axios.post(`/admin/settings/toggle/${item.setting_id}`, {
          is_active: item.is_active
        });
        this.$toast.success('Status updated');
      } catch (e) {
        this.$toast.error('Could not update status');
      }
    },
    openMinDialog(item=null) {
      if (item) {
        this.defaultMinItem = { ...item }
        this.editedMinIndex = item.setting_id
        this.minFormValid = true
      } else {
        this.defaultMinItem = { setting_id:null, value:null }
        this.editedMinIndex = -1
        this.minFormValid = false
      }
      this.addMinDialog = true
    },
    onMinDialogToggle(open) {
      if (!open) {
        this.defaultMinItem = { setting_id:null, value:null }
        this.minFormValid = false
        this.savingMin = false
      }
    },
    async saveMinRequirement() {
      this.savingMin = true
      try {
        await axios.post('/admin/settings/min-order', {
          value: this.defaultMinItem.value
        })
        this.$toast.success('Saved!')
        await this.getAllSettings()
        this.addMinDialog = false
      } catch (e) {
        this.$toast.error('Save failed')
      } finally {
        this.savingMin = false
      }
    },
    async getMinOrderPlace() {
      const res = await axios.get('/admin/settings/min-order-place/vlist')
      this.OrderPlace = res.data.minOrderPlace
    },
    async toggleMinOrderPlace(item) {
      try {
        await axios.post(`/admin/settings/toggle/min-order-place/${item.setting_id}`, {
          is_active: item.is_active
        });
        this.$toast.success('Status updated');
      } catch (e) {
        this.$toast.error('Could not update status');
      }
    },
    openMinOrderPlace(item=null) {
      if (item) {
        this.defaultOrderPlaceItem = { ...item }
        this.orderPlaceIndex = item.setting_id
        this.orderPlaceFormValid = true
      } else {
        this.defaultOrderPlaceItem = { setting_id:null, value:null }
        this.orderPlaceIndex = -1
        this.orderPlaceFormValid = false
      }
      this.addOrderPlace = true
    },
    MinOrderPlaceDialogToggle(open) {
      if (!open) {
        this.defaultOrderPlaceItem = { setting_id:null, value:null }
        this.orderPlaceFormValid = false
        this.savingOrderPlace = false
      }
    },
    async saveMinOrderPlace() {
      this.savingOrderPlace = true
      try {
        await axios.post('/admin/settings/min-order-place', {
          value: this.defaultOrderPlaceItem.value
        })
        this.$toast.success('Saved!')
        await this.getMinOrderPlace()
        this.addOrderPlace = false
      } catch (e) {
        this.$toast.error('Save failed')
      } finally {
        this.savingOrderPlace = false
      }
    },
    async getAllVat() {
      const res = await axios.get('/admin/product-vat/vlist')
      this.vatRequirements = res.data.vat
    },
    openVatDialog(item=null) {
      if (item) {
        this.defaultVatItem = { ...item }
        this.editedVatIndex = item.product_vat_id
        this.vatFormValid = true
      } else {
        this.defaultVatItem = { product_vat_id:null, product_vat:null }
        this.editedVatIndex = -1
        this.vatFormValid = false
      }
      this.addVatDialog = true
    },
    onVatDialogToggle(open) {
      if (!open) {
        this.defaultVatItem = { product_vat_id:null, product_vat:null }
        this.vatFormValid = false
        this.savingVat = false
      }
    },
    async saveVat() {
      this.savingVat = true
      try {
        await axios.post('/admin/product-vat/update', {
          product_vat_id: this.defaultVatItem.product_vat_id,
          product_vat: this.defaultVatItem.product_vat
        })
        this.$toast.success('Saved!')
        await this.getAllVat()
        this.addVatDialog = false
      } catch (e) {
        this.$toast.error('Save failed')
      } finally {
        this.savingVat = false
      }
    },
    getAlldeliverymethods() {
      axios.get('/admin/delivery-method/vlist').then(res => {
        this.deliverymethods = res.data.deliverymethods;
      })
      .catch(err => {
        console.error(err)
      })
    },
    onDialogToggle(open) {
      if (!open) {
      this.defaultItem = 
      { 
        delivery_method_id: null, 
        delivery_method_name: '', 
        delivery_method_amount: null, 
      };
      this.fsvalid = false;
      this.submitting = false;
      this.editedIndex = -1;
      }
    },
    openDialog() {
      this.defaultItem = {
        delivery_method_id: null,
        delivery_method_name: '',
        delivery_method_amount: null,
      }
      this.editedIndex = -1
      this.fsvalid = false
      this.addSdialog = true
    },
    editItem(item) {
      this.defaultItem = {
        delivery_method_id: item.delivery_method_id,
        delivery_method_name: item.delivery_method_name,
        delivery_method_amount: item.delivery_method_amount,
      }
      this.editedIndex = item.delivery_method_id
      this.fsvalid = true
      this.addSdialog = true
    },
    async saveDeliveryMethod() {
      this.submitting = true;

      const payload = {
        delivery_method_name: this.defaultItem.delivery_method_name.toUpperCase(),
        delivery_method_amount: this.defaultItem.delivery_method_amount,
      };

      if (this.editedIndex !== -1) {
        payload.delivery_method_id = this.editedIndex;
      }

      const url = this.editedIndex === -1 ? '/admin/delivery-method/add' : '/admin/delivery-method/update';

      try {
        await axios.post(url, payload, {
          headers: { 'Content-Type': 'application/json' }
        });
        this.$toast.success(
          this.editedIndex === -1 ? 'Delivery Method added successfully!' : 'Delivery Method updated successfully!',
          { timeout: 500 }
        );
        this.getAlldeliverymethods();
        this.addSdialog = false;
      } catch (error) {
      } finally {
        this.submitting = false;
      }
    },
    async toggleStatus(item) {
      try {
          await axios.post(`/admin/delivery-method/status-toggle/${item.delivery_method_id}`, {
              is_active: item.is_active
          });
          this.$toast?.success('Delivery Method Status updated', { timeout: 500 });
      } catch (error) {
          console.error("Failed to toggle status", error);
          this.$toast?.error('Failed to update status', { timeout: 500 });
      }
    },
    confirmDelete(item) {
      this.deliveryMethodToDelete = item
      this.deleteDialog = true
    },
    async performDelete() {
      if (!this.deliveryMethodToDelete) return
      this.deleteLoading = true
      try {
        await axios.post('/admin/delivery-method-delete', { delivery_method_id: this.deliveryMethodToDelete.delivery_method_id })
        this.$toast.success('Delivery Method deleted successfully!', { timeout: 500 })
        this.getAlldeliverymethods()
      } catch (err) {
        console.error(err)
        this.$toast.error('Failed to delete Delivery Method.', { timeout: 2000 })
      } finally {
        this.deleteLoading = false
        this.deleteDialog = false
        this.deliveryMethodToDelete = null
      }
    },

  }
}
</script>

<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
<style>
.page-delivery-method .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>