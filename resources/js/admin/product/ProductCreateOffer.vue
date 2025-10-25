<template>
  <div class="page-margin-20-40 page-product-offer">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Product Offers</h2>
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openAddDialog">Add Offers
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table v-model="selectedtag" item-key="product_offer_id" :show-select="true" :items="productOffers"
            :headers="offerheaders" :search="ssearch"
            :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
            <template v-slot:top>
              <v-row dense class="mx-1 pb-1">
                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details
                  prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Product Offers" />
              </v-row>
            </template>
            <template #item.variant_label="{ item }">
              <span v-html="item.variant_label_html || item.variant_label || '—'"></span>
            </template>
            <template #item.product_type="{ item }">
              <span v-if="item.product_type === 'buy_x_get_y'">
                Buy {{ item.buy_qty }} Get {{ item.get_qty }}
              </span>
              <span v-else-if="item.product_type === 'volume_discount'">
                Any {{ item.min_qty }} for £{{ item.discount_amount }}
              </span>
            </template>

            <template #header.actions1>
              <div class="text-center">Action</div>
            </template>
            <template #item.actions1="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small @click="openEditDialog(item)"
                  style="cursor: pointer;">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
            <template #header.actions2>
              <div class="text-center">Action</div>
            </template>
            <template #item.actions2="{ item }">
              <div class="text-center">
                <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)"
                  style="cursor: pointer;">
                  <v-icon small left>mdi-delete</v-icon>Delete
                </v-chip>
              </div>
            </template>
            <template #header.delete>
              <div class="d-flex justify-end align-center">
                <span v-if="!selectedtag.length"></span>
                <v-menu v-if="selectedtag.length" offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <div class="d-flex align-center">
                      <span class="mr-2 font-weight-medium text-caption">{{ selectedtag.length }} selected</span>
                      <v-icon color="primary" v-bind="attrs" v-on="on"
                        style="cursor: pointer;">mdi-dots-vertical</v-icon>
                    </div>
                  </template>

                  <v-list dense>
                    <v-list-item @click="confirmBulkDelete">
                      <v-list-item-title>Delete</v-list-item-title>
                    </v-list-item>

                    <v-list-item @click="confirmBulkAdd">
                      <v-list-item-title>Add Tag</v-list-item-title>
                    </v-list-item>

                    <v-list-item @click="confirmBulkRemove">
                      <v-list-item-title>Remove Tag</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <!-- ADD dialog -->
    <v-dialog v-model="addDialog" max-width="620" @update:model-value="resetAdd">
      <v-card elevation="5">
        <v-card-title class="d-flex align-center">
          <span>Add Offer</span>
          <v-spacer></v-spacer>
          <v-icon @click="addDialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="addValid" @submit.prevent="saveAdd">
          <v-card-text>
            <v-autocomplete class="dialog-variants" v-model="addForm.product_ids" :items="products"
              item-text="mproduct_title" item-value="mproduct_id" label="Products" multiple small-chips deletable-chips
              :rules="[required]" @change="loadAddVariants" />
            <v-autocomplete class="dialog-variants" ref="variantSelect" v-model="addForm.variant_ids"
              :items="addVariants" item-text="option_label" item-value="mvariant_id" label="Variants" multiple
              small-chips deletable-chips :rules="[required]"
              :menu-props="{ offsetY: true, contentClass: 'variant-menu' }">
              <template v-slot:append-item>
                <div v-if="(addForm.variant_ids || []).length" class="pa-2 text-right">
                  <v-btn small color="primary" class="px-4" @click.stop="applyVariantSelection">
                    Apply
                  </v-btn>
                </div>
              </template>
            </v-autocomplete>

            <v-select v-model="addForm.product_deal_tag" :items="dealTagOptions" item-text="label" item-value="value"
              label="Product Deal Tag" dense :menu-props="{ offsetY: true }" />
            <v-select v-model="addForm.product_type" :items="offerOptions" item-text="label" item-value="value"
              label="Product Offer Type" dense></v-select>
            <!-- Buy X Get Y -->
            <div v-if="addForm.product_type === 'buy_x_get_y'" class="mt-4">
              <v-row>
                <v-col cols="6">
                  <v-text-field v-model="rule.buy_qty" label="Buy Quantity" type="number" dense outlined
                    min="1" />
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="rule.get_qty" label="Get Free Quantity" type="number" dense outlined
                    min="1" />
                </v-col>
              </v-row>
            </div>

            <!-- Volume Discount -->
            <div v-if="addForm.product_type === 'volume_discount'" class="mt-4">
              <v-row>
                <v-col cols="6">
                  <v-text-field v-model="rule.min_qty" label="Minimum Quantity" type="number" dense outlined
                    min="1" />
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="rule.discount_amount" label="Fixed Price (£)" dense outlined
                    @input="validateDecimal" :error-messages="discountError" placeholder="e.g. 19.99" />
                </v-col>
              </v-row>
            </div>

          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit"
              style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
              :disabled="isAddDisabled">Add Offer</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- EDIT dialog -->
    <v-dialog v-model="editDialog" max-width="620" @update:model-value="resetEdit">
      <v-card elevation="5">
        <v-card-title class="d-flex align-center">
          <span>Edit Offer</span>
          <v-spacer></v-spacer>
          <v-icon @click="editDialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="editValid" @submit.prevent="saveEdit">
          <v-card-text>
            <v-autocomplete v-model="editForm.product_id" :items="products" item-text="mproduct_title"
              item-value="mproduct_id" label="Product" :rules="[required]" @change="loadEditVariants" />
            <v-autocomplete v-model="editForm.variant_id" :items="editVariants" item-text="option_label"
              item-value="mvariant_id" label="Variant" :rules="[required]" clearable />

            <v-select v-model="editForm.product_deal_tag" :items="dealTagOptions" item-text="label" item-value="value"
              label="Product Deal Tag" dense :menu-props="{ offsetY: true }" />
            <v-select v-model="editForm.product_type" :items="offerOptions" item-text="label" item-value="value"
              label="Product Offer Type" dense></v-select>

            <!-- BUY X GET Y -->
            <div v-if="editForm.product_type === 'buy_x_get_y'" class="d-flex">
              <v-text-field v-model="editForm.buy_qty" label="Buy Qty" type="number" min="1" dense outlined 
                class="mr-2"></v-text-field>
              <v-text-field v-model="editForm.get_qty" label="Get Qty" type="number" min="1" dense 
                outlined></v-text-field>
            </div>

            <!-- VOLUME DISCOUNT -->
            <div v-if="editForm.product_type === 'volume_discount'" class="d-flex">
              <v-text-field v-model="editForm.min_qty" label="Minimum Qty" type="number" min="1" dense outlined
                class="mr-2"></v-text-field>
              <v-text-field v-model="editForm.discount_amount" label="Fixed Price (£)" type="number" min="0" step="0.01"
                dense outlined></v-text-field>
            </div>

          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit"
              style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
              :disabled="isEditDisabled">
              Update
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- Delete dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete this offer?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading"
            @click="performDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Bulk-delete confirmation -->
    <v-dialog v-model="bulkDeleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete <strong>{{ selectedtag.length }}</strong> offers?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="bulkDeleteLoading" :disabled="bulkDeleteLoading"
            @click="performBulkDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Bulk-add confirmation -->
    <v-dialog v-model="bulkAddDialog" max-width="500">
      <v-card elevation="5">
        <v-card-title class="text-h6">Add Product Deal Tags</v-card-title>
        <v-card-text>
          <v-select v-model="bulk_product_tag" :items="dealTagOptions" item-text="label" item-value="value"
            label="Product deal tag" dense :menu-props="{ offsetY: true }" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn class="btn-32-text-12" text @click="bulkAddDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="bulkAddLoading"
            :disabled="bulkAddLoading || !bulk_product_tag" @click="performBulkAdd">Add</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Bulk-remove confirmation -->
    <v-dialog v-model="bulkRemoveDialog" max-width="500">
      <v-card elevation="5">
        <v-card-title class="text-h6">Remove Product Deal Tag</v-card-title>
        <v-card-text>Are you sure you want to remove <strong>{{ selectedtag.length }}</strong> product deal
          tag?</v-card-text>
        <v-spacer></v-spacer>
        <v-card-actions>
          <v-spacer />
          <v-btn class="btn-32-text-12" text @click="bulkRemoveDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="bulkRemoveLoading" :disabled="bulkRemoveLoading"
            @click="performBulkRemove">Remove</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ProductCreateOffer",

  data() {
    return {
      ssearch: '',
      offerheaders: [
        { text: '', value: 'data-table-select' },
        { text: "Product", value: "mproduct_title" },
        { text: "Variant", value: "variant_label" },
        { text: "Product deal tag", value: "product_deal_tag" },
        { text: "Product offer", value: "product_type" },
        { text: 'Action', value: 'actions1', sortable: false },
        { text: 'Action', value: 'actions2', sortable: false },
        { text: '', value: 'delete', sortable: false, width: '130px' }
      ],
      products: [],
      productOffers: [],

      addDialog: false,
      addValid: false,
      addVariants: [],
      addForm: {
        product_ids: [],
        variant_ids: [],
        product_deal_tag: "",
        product_type: null,
      },

      editDialog: false,
      editValid: false,
      editVariants: [],
      editForm: {
        product_offer_id: null,
        product_id: null,
        variant_id: null,
        product_deal_tag: null,
        product_type: null,
        buy_qty: null,
        get_qty: null,
        min_qty: null,
        discount_amount: null
      },

      dealTagOptions: [
        { label: 'No tag', value: null },
        { label: 'Flash Deal', value: 'Flash Deal' },
        { label: 'Sale', value: 'Sale' },
      ],
      discountError: '',
      rule: {
        buy_qty: null,
        get_qty: null,
        min_qty: null,
        discount_amount: null,
      },
      offerOptions: [
        { label: 'No offer', value: null },
        { label: "Volume Discount", value: 'volume_discount' },
        { label: "Buy X Get Y", value: 'buy_x_get_y' },
      ],

      deleteDialog: false,
      offerToDelete: null,
      deleteLoading: false,
      saving: false,
      required: (v) => !!v || "Required",

      bulk_product_tag: null,

      selectedtag: [],
      bulkDeleteDialog: false,
      bulkDeleteLoading: false,

      bulkAddDialog: false,
      bulkAddLoading: false,

      bulkRemoveDialog: false,
      bulkRemoveLoading: false,
    };
  },

  created() {
    this.loadAll();
  },
  watch: {
    editDialog(val) {
      if (val) {
        const v = this.editForm?.product_deal_tag
        if (v === '' || v === undefined || v === 'none' || v === 'No tag') {
          this.$set(this.editForm, 'product_deal_tag', null)
        }
      }
    },
    'addForm.product_type'(val) {
      this.rule = { buy_qty: null, get_qty: null, min_qty: null, discount_amount: null };
    }
  },
  computed: {
    isAddDisabled() {
      if (!this.addValid || !this.addForm.product_ids.length || !this.addForm.variant_ids.length) {
        return true;
      }

      if (this.addForm.product_type === 'buy_x_get_y') {
        return !(this.rule.buy_qty > 0 && this.rule.get_qty > 0);
      }

      if (this.addForm.product_type === 'volume_discount') {
        return !(this.rule.min_qty > 0 && this.rule.discount_amount > 0);
      }

      return false;
    },
    isEditDisabled() {
      if (!this.editValid || this.saving) return true;
      if (!this.editForm.product_id || !this.editForm.variant_id) return true;

      if (this.editForm.product_type === 'buy_x_get_y') {
        return !(this.editForm.buy_qty > 0 && this.editForm.get_qty > 0);
      }

      if (this.editForm.product_type === 'volume_discount') {
        return !(this.editForm.min_qty > 0 && this.editForm.discount_amount > 0);
      }

      return false;
    }
  },

  methods: {
    async loadAll() {
      const { data } = await axios.get("/admin/product-offers/vlist");
      this.products = data.products;

      const vIndex = new Map();

      for (const p of this.products) {
        for (const v of (p.mvariants || [])) {
          vIndex.set(v.mvariant_id, {
            mproduct_title: p.mproduct_title,
            variant_label: this.formatVariantValues(v.option_value) || '',
            variant_label_html: this.formatVariantValuesHtml(v.option_value) || '',
          });
        }
      }

      this.productOffers = (data.productoffers || []).map(offer => {
        const extra = vIndex.get(offer.mvariant_id) || { mproduct_title: '', variant_label: '', variant_label_html: '' };
        return { ...offer, ...extra };
      });
    },
    validateDecimal(value) {
      const regex = /^\d+(\.\d{1,2})?$/;
      if (!regex.test(this.rule.discount_amount)) {
        this.discountError = "Only decimal allowed up to 2 places";
      } else {
        this.discountError = "";
      }
    },
    formatVariantValues(optionValue) {
      if (!optionValue) return '';

      if (typeof optionValue === 'string') {
        if (/[:=]/.test(optionValue) && !optionValue.trim().startsWith('{') && !optionValue.trim().startsWith('[')) {
          return optionValue;
        }
        try {
          optionValue = JSON.parse(optionValue);
        } catch (_) {
          if (optionValue.includes('|') || optionValue.includes('=')) {
            return optionValue
              .split('|')
              .map(p => p.replace('=', ': ').trim())
              .join(', ');
          }
          return optionValue;
        }
      }

      if (Array.isArray(optionValue)) {
        return optionValue
          .map(o => {
            const k = o.key || o.name || o.label || o.option || Object.keys(o || {})[0];
            const v = o.value ?? (k && o[k]);
            return (k && v != null) ? `${k}: ${v}` : '';
          })
          .filter(Boolean)
          .join(', ');
      }

      if (typeof optionValue === 'object') {
        return Object.entries(optionValue)
          .filter(([, v]) => v != null && v !== '')
          .map(([k, v]) => `${k}: ${v}`)
          .join(', ');
      }

      return String(optionValue);
    },
    formatVariantValuesHtml(optionValue) {
      const pairs = this.parseVariantPairs(optionValue);
      return pairs
        .filter(({ k, v }) => k && v != null && v !== '')
        .map(({ k, v }) => `<strong>${this.escapeHtml(String(k))}</strong>: ${this.escapeHtml(String(v))}`)
        .join(', ');
    },

    parseVariantPairs(optionValue) {
      if (!optionValue) return [];

      if (typeof optionValue === 'string') {
        const s = optionValue.trim();
        if (/^[^{}\[\]]+:\s*.+/.test(s)) {
          return s.split(',').map(part => {
            const [k, ...rest] = part.split(':');
            return { k: k?.trim(), v: rest.join(':').trim() };
          });
        }
        if (s.startsWith('{') || s.startsWith('[')) {
          try { optionValue = JSON.parse(s); } catch { }
        } else if (s.includes('|') || s.includes('=')) {
          return s.split('|').map(pair => {
            const [k, ...rest] = pair.split('=');
            return { k: k?.trim(), v: rest.join('=').trim() };
          });
        }
      }

      if (Array.isArray(optionValue)) {
        const out = [];
        for (const o of optionValue) {
          if (!o || typeof o !== 'object') continue;
          const k = o.key || o.name || o.label || o.option || Object.keys(o)[0];
          const v = o.value ?? (k && o[k]);
          out.push({ k, v });
        }
        return out;
      }

      if (typeof optionValue === 'object') {
        return Object.entries(optionValue).map(([k, v]) => ({ k, v }));
      }

      return [];
    },

    escapeHtml(s) {
      return s
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    },
    applyVariantSelection() {
      if (this.$refs.variantSelect && this.$refs.variantSelect.isMenuActive !== undefined) {
        this.$refs.variantSelect.isMenuActive = false;
      } else if (this.$refs.variantSelect && this.$refs.variantSelect.blur) {
        this.$refs.variantSelect.blur();
      }
    },
    openAddDialog() {
      this.resetAdd(false);
      this.addDialog = true;
    },
    async loadAddVariants() {
      this.addVariants = [];

      if (!this.addForm.product_ids || this.addForm.product_ids.length === 0) return;

      try {
        const { data } = await axios.get('/admin/product-offers/vlist');
        const usedVariants = data.used_variant_ids || [];
        const allVariants = [];

        for (const product of data.products) {
          if (!this.addForm.product_ids.includes(product.mproduct_id)) continue;

          for (const variant of product.mvariants) {
            if (usedVariants.includes(variant.mvariant_id)) continue;

            let optionVal = variant.option_value;
            if (typeof optionVal === 'string') {
              try {
                optionVal = JSON.parse(optionVal);
              } catch (e) {
                optionVal = {};
              }
            }

            const isEmpty = !optionVal || Object.keys(optionVal).length === 0;

            const label = isEmpty
              ? product.mproduct_title
              : `${product.mproduct_title} - ${Object.entries(optionVal).map(([key, val]) => `${key}: ${val}`).join(' / ')}`;

            allVariants.push({
              mvariant_id: variant.mvariant_id,
              option_value: optionVal,
              option_label: label
            });
          }
        }

        this.addVariants = allVariants;
      } catch (e) {
        console.error('❌ Failed to load variants:', e);
      }
    },
    resetAdd() {
      this.addForm = {
        product_ids: [],
        variant_ids: [],
        product_deal_tag: '',
      };
      this.addVariants = [];
    },
    async saveAdd() {
      if (!this.addValid || this.saving) return;
      this.saving = true;

      try {
        await axios.post("/admin/product-offers/add", {
          product_ids: this.addForm.product_ids,
          variant_ids: this.addForm.variant_ids,
          product_deal_tag: this.addForm.product_deal_tag,
          product_type: this.addForm.product_type,

          buy_qty: this.rule.buy_qty ?? null,
          get_qty: this.rule.get_qty ?? null,

          min_qty: this.rule.min_qty ?? null,
          discount_amount: this.rule.discount_amount ?? null,
        });

        this.$toast.success("Offers added successfully!", {
          timeout: 500
        })
        this.addDialog = false;
        this.loadAll();
      } catch (err) {
        this.$toast.error("Failed to add offers.", {
          timeout: 500
        })
      } finally {
        this.saving = false;
      }
    },
    openEditDialog(item) {
      this.editForm = {
        product_offer_id: item.product_offer_id,
        product_id: this.findProductIdByVariant(item.mvariant_id),
        variant_id: item.mvariant_id,
        product_deal_tag: item.product_deal_tag,
        product_type: item.product_type,
        buy_qty: item.buy_qty,
        get_qty: item.get_qty,
        min_qty: item.min_qty,
        discount_amount: item.discount_amount,
      };
      this.loadEditVariants();
      this.editDialog = true;
    },
    loadEditVariants() {
      const selectedProduct = this.products.find(
        (p) => p.mproduct_id === this.editForm.product_id
      );

      if (!selectedProduct) {
        this.editVariants = [];
        return;
      }

      const usedVariantIds = this.productOffers
        .filter(o => o.product_offer_id !== this.editForm.product_offer_id)
        .map(o => o.mvariant_id);

      this.editVariants = selectedProduct.mvariants
        .filter(v => !usedVariantIds.includes(v.mvariant_id) || v.mvariant_id === this.editForm.variant_id)
        .map(variant => {
          let optionVal = variant.option_value;
          if (typeof optionVal === 'string') {
            try {
              optionVal = JSON.parse(optionVal);
            } catch (e) {
              optionVal = {};
            }
          }

          const isEmpty = !optionVal || Object.keys(optionVal).length === 0;

          const option_label = isEmpty
            ? selectedProduct.mproduct_title
            : `${selectedProduct.mproduct_title} - ${Object.entries(optionVal)
              .map(([key, val]) => `${key}: ${val}`)
              .join(' / ')}`;

          return {
            ...variant,
            option_value: optionVal,
            option_label
          };
        });

      if (
        this.editForm.variant_id &&
        !this.editVariants.some(v => v.mvariant_id === this.editForm.variant_id)
      ) {
        this.editForm.variant_id = null;
      }
    },
    resetEdit(close = true) {
      this.editForm = {
        product_offer_id: null,
        product_id: null,
        variant_id: null,
        product_deal_tag: "",
        product_type: null,
        buy_qty: null,
        get_qty: null,
        min_qty: null,
        discount_amount: null,
      };
      this.editVariants = [];
      this.editValid = false;
      if (close) this.editDialog = false;
    },
    async saveEdit() {
      if (!this.editValid || this.saving) return;
      this.saving = true;

      try {
        await axios.post("/admin/product-offers/update", {
          product_offer_id: this.editForm.product_offer_id,
          mvariant_id: this.editForm.variant_id,
          product_deal_tag: this.editForm.product_deal_tag,
          product_type: this.editForm.product_type,
          buy_qty: this.editForm.product_type === 'buy_x_get_y' ? this.editForm.buy_qty : null,
          get_qty: this.editForm.product_type === 'buy_x_get_y' ? this.editForm.get_qty : null,
          min_qty: this.editForm.product_type === 'volume_discount' ? this.editForm.min_qty : null,
          discount_amount: this.editForm.product_type === 'volume_discount' ? this.editForm.discount_amount : null,
        });

        this.$toast?.success('Offer updated successfully!', { timeout: 500 });
        await this.loadAll();
        this.resetEdit();
      } catch (err) {
        console.error(err);
        this.$toast?.error('Failed to update offer', { timeout: 500 });
      } finally {
        this.saving = false;
      }
    },
    findProductIdByVariant(variantId) {
      const prod = this.products.find((p) =>
        p.mvariants.some((v) => v.mvariant_id === variantId)
      );
      return prod ? prod.mproduct_id : null;
    },
    confirmDelete(item) {
      this.offerToDelete = item;
      this.deleteDialog = true;
    },
    async performDelete(id) {
      if (!this.offerToDelete) return;
      this.deleteLoading = true;

      try {
        await axios.post('/admin/product-offers/delete', {
          product_offer_id: this.offerToDelete.product_offer_id
        });

        this.$toast?.success('Offer deleted successfully!', {
          timeout: 500
        })
        this.loadAll();
      } catch (err) {
        this.$toast?.error('Failed to delete offer', {
          timeout: 500
        })
      } finally {
        this.deleteLoading = false;
        this.deleteDialog = false;
        this.offerToDelete = null;
      }
    },
    confirmBulkDelete() {
      this.bulkDeleteDialog = true;
    },
    async performBulkDelete() {
      if (!this.selectedtag.length) return;
      this.bulkDeleteLoading = true;

      try {
        await axios.post('/admin/product-offers/bulk-delete', {
          product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
        });

        this.$toast?.success('Selected product offers deleted!', {
          timeout: 500
        })
        this.selectedtag = [];
        this.loadAll();
      } catch (err) {
        console.error(err);
        this.$toast?.error('Failed to delete selected product offers.', {
          timeout: 500
        })
      } finally {
        this.bulkDeleteLoading = false;
        this.bulkDeleteDialog = false;
      }
    },
    confirmBulkAdd() {
      this.bulkAddDialog = true;
    },
    async performBulkAdd() {
      if (!this.selectedtag.length) return;
      this.bulkAddLoading = true;

      try {
        await axios.post('/admin/product-offers/bulk-add', {
          product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
          bulk_product_tag: this.bulk_product_tag,
        });

        this.$toast?.success('Selected product offers added!', {
          timeout: 500
        })
        this.selectedtag = [];
        this.loadAll();
      } catch (err) {
        console.error(err);
        this.$toast?.error('Failed to add selected product offers.', {
          timeout: 500
        })
      } finally {
        this.bulkAddLoading = false;
        this.bulkAddDialog = false;
        this.bulk_product_tag = null;
      }
    },
    confirmBulkRemove() {
      this.bulkRemoveDialog = true;
    },
    async performBulkRemove() {
      if (!this.selectedtag.length) return;
      this.bulkRemoveLoading = true;

      try {
        await axios.post('/admin/product-offers/bulk-remove', {
          product_offer_ids: this.selectedtag.map((c) => c.product_offer_id),
          bulk_product_tag: this.bulk_product_tag,
        });

        this.$toast?.success('Selected product offers removed!', {
          timeout: 500
        })
        this.selectedtag = [];
        this.loadAll();
      } catch (err) {
        console.error(err);
        this.$toast?.error('Failed to remove selected product offers.', {
          timeout: 500
        })
      } finally {
        this.bulkRemoveLoading = false;
        this.bulkRemoveDialog = false;
      }
    },
  },
};

</script>

<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
<style>
.page-product-offer .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px !important;
}
</style>
<style>
.variant-menu .v-list {
  max-height: 144px;
  overflow-y: auto !important;
}

.dialog-variants label {
  height: 32px !important;
}
</style>