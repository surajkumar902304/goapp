<template>
<div class="page-margin-20-40 page-coupons">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Coupons</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openDialog">Add Coupon
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table v-model="selected" :headers="couponsHeaders" :items="coupons" item-key="coupon_id" :show-select="true" :search="ssearch" 
                    :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
                    <template v-slot:top>
                      <v-row dense class="mx-1 pb-1">
                        <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search all"/>
                      </v-row>
                    </template>
                    <template #item.code="{ item }">
                        <span>{{ item.code }}</span>
                    </template>
                    <template #item.discount_type="{ item }">
                        <span>{{ item.discount_type }}</span>
                    </template>
                    <template #item.discount_value="{ item }">
                        <span>{{ item.discount_value }}</span>
                    </template>
                    <template #item.expires_at="{ item }">
                        <span>{{ item.expires_at || '-' }}</span>
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
                    <template #header.delete>
                        <div class="d-flex justify-end align-center">
                            <span v-if="!selected.length"></span>
                            <v-menu v-if="selected.length" offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <div class="d-flex align-center">
                                    <span class="mr-2 font-weight-medium text-caption">{{ selected.length }} selected</span>
                                    <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer;">
                                        mdi-dots-vertical
                                    </v-icon>
                                </div>
                            </template>
                            <v-list dense>
                                <v-list-item @click="confirmBulkDelete">
                                    <v-list-item-title>Delete</v-list-item-title>
                                </v-list-item>
                            </v-list>
                            </v-menu>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>

    <v-dialog v-model="addSdialog" max-width="600" @update:model-value="onDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          <span>{{ editedIndex === -1 ? 'Add Coupon' : 'Edit Coupon' }}</span>
          <v-spacer></v-spacer>
          <v-icon @click="addSdialog = false">mdi-close</v-icon>
        </v-card-title>

        <v-form v-model="fsvalid" @submit.prevent="saveCoupon">
          <v-card-text>
            <v-text-field v-model="defaultItem.code" @input="defaultItem.code = defaultItem.code.toUpperCase()" :rules="codeRules" label="Coupon Code" required/>
            <v-select v-model="defaultItem.main_mcat_id" :items="mainCategoryOptions" label="Select Main Category (optional)" item-title="text" item-value="value" :return-object="false" dense required />
            <v-row>
              <v-col cols="12" md="6">
                <v-select v-model="defaultItem.discount_type" :items="discountTypeOptions" :rules="[v => !!v || 'Discount type is required']" label="Discount Type" required/>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="defaultItem.discount_value" :rules="[v => v !== null && v !== '' || 'Discount value is required', v => parseFloat(v) >= 0 || 'Must be ≥ 0']" 
                  label="Discount Value" type="number" required/>
              </v-col>
              <v-col cols="12" md="6">
                <v-menu v-model="menuExpires" :close-on-content-click="false" transition="scale-transition" offset-y max-width="290px" min-width="auto">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field v-model="defaultItem.expires_at" readonly label="Expires At (optional)" v-bind="attrs" v-on="on" placeholder="YYYY-MM-DD" />
                  </template>
                  <v-date-picker v-model="defaultItem.expires_at" no-title scrollable @input="menuExpires = false"/>
                </v-menu>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="defaultItem.usage_limit" :rules="[v => v === null || v === '' || (Number.isInteger(+v) && +v >= 1) || 'Must be integer ≥1']" 
                  label="Usage Limit (optional)" type="number" placeholder="e.g. 100"/>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="defaultItem.per_user_limit" :rules="[v => v === null || v === '' || (Number.isInteger(+v) && +v >= 1) || 'Must be integer ≥1']" 
                  label="Per-User Limit (optional)" type="number" placeholder="e.g. 1"/>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="defaultItem.min_cart_value" :rules="[v => v === null || v === '' || parseFloat(v) >= 0 || 'Must be ≥ 0']" label="Min Cart Value (optional)" 
                  type="number" placeholder="e.g. 500.00"/>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fsvalid || submitting">
              {{ editedIndex === -1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete this Coupon?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="bulkDeleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete <strong>{{ selected.length }}</strong> coupons?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="bulkDeleteLoading" :disabled="bulkDeleteLoading" @click="performBulkDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios'
import { ref, reactive, onMounted } from 'vue'

export default {
  name: 'AdminCoupons',
  data() {
    return {
      ssearch: '',
      coupons: [],
      couponsHeaders: [
        { text: '', value: 'data-table-select' },
        { text: 'Coupon code', value: 'code' },
        { text: 'Discount type', value: 'discount_type' },
        { text: 'Discount value', value: 'discount_value' },
        { text: 'Min Cart value', value: 'min_cart_value' },
        { text: 'Expires at', value: 'expires_at' },
        { text: 'Status', value: 'is_active' },
        { text: 'Action', value: 'actions1', sortable: false },
        { text: 'Action', value: 'actions2', sortable: false },
        { text: '', value: 'delete', sortable: false, width: '130px' }
      ],

      addSdialog: false,
      editedIndex: -1,
      fsvalid: false,
      submitting: false,

      defaultItem: {
        coupon_id: null,
        code: '',
        discount_type: '',
        discount_value: null,
        expires_at: null,
        usage_limit: null,
        per_user_limit: null,
        min_cart_value: null,
        main_mcat_id: null 
        
      },

      codeRules: [
        v => !!v || 'Coupon Code is required',
        v => (v && v.length >= 3) || 'Code at least 3 characters',
        v => (v && v.length <= 50) || 'Code less than 50 characters',
        v => /^\S+$/.test(v) || 'Spaces are not allowed',
        v => !this.coupons.some(c => c.code === v && c.coupon_id !== this.defaultItem.coupon_id) || 'Coupon already exists'
      ],
      discountTypeOptions: ['fixed', 'percent'],

      deleteDialog: false,
      couponToDelete: null,
      deleteLoading: false,
      mainCategoryOptions: [],
      selected: [],
      bulkDeleteDialog: false,
      bulkDeleteLoading: false,

      menuExpires: false
    }
  },
  created() {
    this.getAllCoupons();
    this.getMainCategories();
  },
  watch: {
    addSdialog(val) {
      if (!val) this.submitting = false
    }
  },
  methods: {
    async getMainCategories() {
      try {
        const res = await axios.get('/admin/maincategories/vlist');

      this.mainCategoryOptions = res.data.mcats.map(cat => ({
      text:  cat.main_mcat_name,
      value: cat.main_mcat_id
    }));
      } catch (err) {
        console.error('Failed to load categories', err);
      }
    },
    getAllCoupons() {
      axios
        .get('/admin/coupons/vlist')
        .then(res => {
          this.coupons = res.data.coupons;
        })
        .catch(err => {
          console.error(err)
        })
    },
    onDialogToggle(open) {
        if (!open) {
        this.defaultItem = 
        { 
          coupon_id: null, 
          code: '', 
          discount_type: '', 
          discount_value: null, 
          expires_at: null, 
          usage_limit: null, 
          per_user_limit: null, 
          min_cart_value: null, 
        };
        this.fsvalid = false;
        this.submitting = false;
        this.editedIndex = -1;
        }
    },
    openDialog() {
      this.defaultItem = {
        coupon_id: null,
        code: '',
        discount_type: '',
        discount_value: null,
        expires_at: null,
        usage_limit: null,
        per_user_limit: null,
        min_cart_value: null,
      }
      this.editedIndex = -1
      this.fsvalid = false
      this.addSdialog = true
    },
    editItem(item) {
      const selectedCategory = this.mainCategoryOptions.find(cat => cat.value === item.main_mcat_id);
      this.defaultItem = {
        coupon_id: item.coupon_id,
        code: item.code,
        discount_type: item.discount_type,
        discount_value: item.discount_value,
        expires_at: item.expires_at,
        usage_limit: item.usage_limit,
        per_user_limit: item.per_user_limit,
        min_cart_value: item.min_cart_value,
        main_mcat_id: selectedCategory ? selectedCategory.value : null
      }
      this.editedIndex = item.coupon_id
      this.fsvalid = true
      this.addSdialog = true
    },
    async saveCoupon() {
      this.submitting = true;

      let expiresAtValue = this.defaultItem.expires_at;

      if (expiresAtValue) {
        expiresAtValue = `${expiresAtValue} 00:00:00`;
      } else {
        expiresAtValue = null;
      }

      const payload = {
        code: this.defaultItem.code.toUpperCase(),
        discount_type: this.defaultItem.discount_type,
        discount_value: this.defaultItem.discount_value,
        expires_at: expiresAtValue,
        usage_limit: this.defaultItem.usage_limit,
        per_user_limit: this.defaultItem.per_user_limit,
        min_cart_value: this.defaultItem.min_cart_value,
      main_mcat_id: this.defaultItem.main_mcat_id?.value ?? this.defaultItem.main_mcat_id,

      };

      if (this.editedIndex !== -1) {
        payload.coupon_id = this.editedIndex;
      }

      const url = this.editedIndex === -1 ? '/admin/coupons/add' : '/admin/coupons/update';

      try {
        await axios.post(url, payload, {
          headers: { 'Content-Type': 'application/json' }
        });
        this.$toast.success(
          this.editedIndex === -1 ? 'Coupon added successfully!' : 'Coupon updated successfully!',
          { timeout: 500 }
        );
        this.getAllCoupons();
        this.addSdialog = false;
      } catch (error) {
      } finally {
        this.submitting = false;
      }
    },
    async toggleStatus(item) {
      try {
          await axios.post(`/admin/coupons/status-toggle/${item.coupon_id}`, {
              is_active: item.is_active
          });
          this.$toast?.success('Coupon Status updated', { timeout: 500 });
      } catch (error) {
          console.error("Failed to toggle status", error);
          this.$toast?.error('Failed to update status', { timeout: 500 });
      }
    },
    confirmDelete(item) {
      this.couponToDelete = item
      this.deleteDialog = true
    },
    async performDelete() {
      if (!this.couponToDelete) return
      this.deleteLoading = true
      try {
        await axios.post('/admin/coupon-delete', { coupon_id: this.couponToDelete.coupon_id })
        this.$toast.success('Coupon deleted successfully!', { timeout: 500 })
        this.getAllCoupons()
      } catch (err) {
        console.error(err)
        this.$toast.error('Failed to delete coupon.', { timeout: 2000 })
      } finally {
        this.deleteLoading = false
        this.deleteDialog = false
        this.couponToDelete = null
      }
    },
    confirmBulkDelete() {
      this.bulkDeleteDialog = true
    },
    async performBulkDelete() {
      if (!this.selected.length) return
      this.bulkDeleteLoading = true
      try {
        const ids = this.selected.map(c => c.coupon_id)
        await axios.post('/admin/coupons/bulk-delete', { coupon_ids: ids })
        this.$toast.success('Selected coupons deleted!', { timeout: 500 })
        this.selected = []
        this.getAllCoupons()
      } catch (err) {
        console.error(err)
        this.$toast.error('Failed to delete selected coupons.', { timeout: 2000 })
      } finally {
        this.bulkDeleteLoading = false
        this.bulkDeleteDialog = false
      }
    }
  }
}
</script>

<style>
.v-input {
  font-size: 12px !important;
}
.page-coupons .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>
