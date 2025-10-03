<template>
  <div class="page-margin-20-40">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="12" class="p-0">
          <h2 class="text-h6 mb-1">Tag Price<span v-if="prettyTagName"> - {{ prettyTagName }}</span></h2>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :items="allVariants" :headers="headers" :search="mainSearch" item-key="mvariant_id"
            :footer-props="{
              'items-per-page-options': [10, 25, 50], 'items-per-page-text': 'Rows per page:'
            }">
            <template v-slot:top>
              <v-row dense class="mx-1 pb-1">
                <v-text-field v-model="mainSearch" class="m-2" clearable dense outlined hide-details
                  prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Product, Variants" />
              </v-row>
            </template>

            <template #item.img="{ item }">
              <img :src="img(item.img)" width="50" height="50" style="object-fit:contain" />
            </template>

            <template #item.tag_price="{ item }">
              <div style="cursor:pointer" @click="openTagDialog(item)">
                {{ item.tag_price }}
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="showTagDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title><span>Update Tag Price</span>
          <v-spacer></v-spacer>
          <v-icon @click="showTagDialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="priceValid" @submit.prevent="updateTagValue">
          <v-card-text>
            <v-text-field v-model="tagValueInput" label="Tag Price" type="number" step="0.01" :rules="priceRules"
              autofocus />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :loading="isUpdating" 
              :disabled="isUpdating || !priceValid">
              Update
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserTagPrice',
  props: {
    usertagid: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      cdn: 'https://cdn.truewebpro.com/',
      tagName: '',
      mainSearch: '',
      allVariants: [],
      headers: [
        { text: 'Image', value: 'img', sortable: false },
        { text: 'Product', value: 'product' },
        { text: 'Variants', value: 'variantLabel' },
        { text: 'Price', value: 'price' },
        { text: 'Tag Price', value: 'tag_price' },
      ],
      showTagDialog: false,
      priceValid: false,
      tagValueInput: '',
      currentVariantId: null,
      isUpdating: false,
      user_tag_id: this.$route.params?.usertagid,
      priceRules: [
        v => (v !== '' && v !== null) || 'Tag price is required.',
        v => !isNaN(v) || 'Value must be a number.',
        v => parseFloat(v) >= 0 || 'Must be a positive number.',
        v => /^\d+(\.\d{1,2})?$/.test(String(v)) || 'Max 2 decimal places allowed.',
      ],
    };
  },
  mounted() {
    this.user_tag_id = this.$route.params?.usertagid;
    this.fetchVariants();
  },
  computed: {
    prettyTagName() {
      const s = (this.tagName || '').trim();
      return s ? s.charAt(0).toUpperCase() + s.slice(1).toLowerCase() : '';
    }
  },
  methods: {
    img(src) {
      return src ? this.cdn + String(src).replace(/^\/+/, '') : '/images/no-image-available.png';
    },
    async fetchVariants() {
      const { data } = await axios.get(`/admin/user-tag-price/list`, {
        params: { UserTagPrice: this.user_tag_id },
      });

      this.tagName = data.tagName || '';

      this.allVariants = (data.variants || []).map((v, i) => {
        let label = '';
        const det = v.details || [];
        if (det.length) {
          label = det
            .map(d =>
              Object.entries(d.option_value || {}).map(([k, val]) => `${k}: ${val}`).join(', ')
            )
            .join(' | ');
        }

        return {
          mvariant_id: v.mvariant_id,
          img: v.mvariant_image || (v.product && v.product.mproduct_image),
          product: v.product ? v.product.mproduct_title : '',
          variantLabel: label,
          price: v.price,
          tag_price: v.tag_price ?? '',
        };
      });
    },
    openTagDialog(item) {
      this.currentVariantId = item.mvariant_id;
      this.tagValueInput = item.tag_price === '' || item.tag_price == null ? '' : String(item.tag_price);
      this.priceValid = true;
      this.showTagDialog = true;
    },
    closeDialog() {
      this.showTagDialog = false;
      this.currentVariantId = null;
      this.tagValueInput = '';
      this.priceValid = false;
    },
    async updateTagValue() {
      if (this.isUpdating || !this.priceValid) return;

      this.isUpdating = true;
      try {
        await axios.post('/admin/user-tag-price/update', {
          user_tag_id: this.user_tag_id,
          mvariant_id: this.currentVariantId,
          tag_price: this.tagValueInput === '' ? null : parseFloat(this.tagValueInput),
        });

        const idx = this.allVariants.findIndex(v => v.mvariant_id === this.currentVariantId);
        if (idx !== -1) {
          this.allVariants[idx].tag_price =
            this.tagValueInput === '' ? '' : parseFloat(this.tagValueInput);
        }

        this.$toast?.success('Tag price updated!', { timeout: 600 });
        this.closeDialog();
      } catch (e) {
        console.error(e);
        this.$toast?.error('Failed to update tag price', { timeout: 900 });
      } finally {
        this.isUpdating = false;
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
