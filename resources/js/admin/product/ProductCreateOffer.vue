<template>
    <div>
      <v-row>
        <v-col cols="12" md="10">
          <v-text-field
            v-model="ssearch"
            dense
            hide-details
            outlined
            prepend-inner-icon="mdi-magnify"
            placeholder="Search name"
          />
        </v-col>
        <v-col cols="12" md="2" class="text-end">
          <v-btn color="secondary" small class="text-none font-weight-bold" @click="openDialog">
            Create Offer
          </v-btn>
        </v-col>
      </v-row>
  
      <!-- dialog part unchanged -->
      <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
        <v-card>
          <v-card-title>
            <span>{{ editedIndex === -1 ? 'Add Banner' : 'Edit Banner' }}</span>
            <v-spacer></v-spacer>
            <v-icon @click="addSdialog = false">mdi-close</v-icon>
          </v-card-title>
          <v-form v-model="fsvalid" @submit.prevent="saveBanner">
            <v-card-text>
              <!-- Category dropdown -->
              <v-select dense outlined
                        v-model="defaultItem.mcat_id"
                        :items="categories"
                        item-value="mcat_id"
                        item-text="mcat_name"
                        label="Category" 
                        clearable/> 
                        <!-- Sub‑Category dropdown -->
              <v-select dense outlined class="mt-3"
                        v-if="subcategories.length"
                        v-model="defaultItem.msubcat_id"
                        :items="subcategories"
                        item-value="msubcat_id"
                        item-text="msubcat_name"
                        label="Sub‑Category" 
                        clearable/>              
              <!-- Product dropdown -->
              <v-select dense outlined class="mt-3"
                        v-if="products.length"
                        v-model="defaultItem.mproduct_id"
                        :items="products"
                        item-value="mproduct_id"
                        item-text="mproduct_title"
                        label="Product" 
                        clearable/>
              <v-text-field
                v-model="defaultItem.browsebanner_name"
                :rules="bannernameRule"
                label="Banner Name"
              />
              <div class="d-flex flex-column align-center">
                <v-card-actions class="pb-0 pt-0">
                  <span class="body-2 fw-semibold">
                    {{ isImageSelected ? 'Selected Image' : 'Select Image' }}
                  </span>
                </v-card-actions>
                <input ref="imageInput" type="file" accept="image/*" style="display:none" @change="handleImageUpload" />
                <div class="uploader-box mb-2" @click="triggerFileInput">
                  <v-img
                    v-if="isImageSelected"
                    :src="imagePreview"
                    class="rounded"
                    max-width="150"
                    max-height="150"
                    cover
                  />
                  <v-icon v-else size="48" class="grey--text text--lighten-1">mdi-image-area</v-icon>
                </div>
                <div v-if="imageName" class="text-caption">{{ imageName }}</div>
              </div>
            </v-card-text>
            <v-card-actions>
              <template v-if="editedIndex !== -1 || isImageSelected">
                <v-btn type="submit" color="success" small :disabled="!fsvalid || submitting">
                  {{ editedIndex === -1 ? 'Add' : 'Update' }}
                </v-btn>
              </template>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import draggable from 'vuedraggable';
  
  export default {
    name: 'BrowseBanner',
    components: { draggable },
    data() {
      return {
        cdn: 'https://cdn.truewebpro.com/',
        ssearch: '',
        productoffers      : [],
        products      : [],
        fillLock : false,
        addSdialog: false,
        editedIndex: -1,
        fsvalid: false,
        submitting: false,
        defaultItem: {
          browsebanner_id: null,
          browsebanner_name: '',
          browsebanner_image: '',
          mcat_id : null,
          msubcat_id : null,
          mproduct_id : null
        },
      };
    },
    created() {
      this.getAllProductOffers();
      this.loadCategories();
    },

    methods: {
        getAllProductOffers() {
        axios.get('/admin/product-offers/vlist').then(res => {
          this.productoffers = res.data.productoffers;
          this.products = res.data.products;
        });
      },
      openDialog() {
        this.defaultItem = { product_offer_id:null, mvariant_id:null, product_deal_tag: '', product_offer: '' };
        this.productoffers=[]; 
        this.products=[];
        this.editedIndex = -1;
        this.fsvalid = false;
        this.addSdialog = true;
      },
      /* ------------ editItem ------------- */
  async editItem(item){
    await this.ensureCategoriesLoaded()

    /* 1) पहले drop‑downs तैयार करो  */
    const cat = this.products.find(c => c.mproduct_id === item.mproduct_id)
    const subs = cat ? cat.products : []
    const sub  = subs.find(s => s.mvariant_id === item.mvariant_id)
    const prods  = sub ? sub.productoffers : []

    /* lock watcher → fill everything → unlock */
    this.fillLock = true
    this.products = subs
    this.productoffers      = prods

    this.defaultItem = {
        product_offer_id   : item.product_offer_id,
        mvariant_id : item.mvariant_id,
        product_deal_tag: '',
        product_offer: '',
    }
    this.$nextTick(() => {     // unlock बाद के टिक पर
      this.fillLock = false
    })

    this.editedIndex  = item.product_offer_id
    this.fsvalid      = true
    this.addSdialog   = true
  },
      onDialogToggle(open) {
        if (!open) {
          this.defaultItem = { product_offer_id:null, mvariant_id:null, product_deal_tag: '', product_offer: '' };
          this.productoffers=[]; 
          this.products=[];
          this.fsvalid = false;
          this.submitting = false;
          this.editedIndex = -1;
        }
      },
      saveBanner() {
        this.submitting = true;
        const fd = new FormData();
        if (this.defaultItem.mvariant_id    != null) fd.append('mvariant_id',    this.defaultItem.mvariant_id)

        fd.append('product_deal_tag', this.defaultItem.browsebanner_name);
        fd.append('product_offer', this.defaultItem.browsebanner_name);
        if (this.editedIndex !== -1) {
          fd.append('product_offer_id', this.editedIndex);
        }
        const isNew = this.editedIndex === -1;
        const url = isNew ? '/admin/browsebanners/add' : '/admin/browsebanners/update';

        axios.post(url, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(() => {
          this.getAllBanners();
          this.addSdialog = false;

          // ✅ Show toast
          this.$toast.success(isNew ? 'Banner added successfully!' : 'Banner updated successfully!');
        })
        .catch(() => {
          this.$toast.error('Something went wrong while saving the banner.');
        })
        .finally(() => {
          this.submitting = false;
        });
      },
      onDragEnd() {
        const payload = this.browsebanners.map((item, index) => ({
          id: item.browsebanner_id,
          position: index + 1
        }));
        axios.post('/admin/browsebanners/reorder', payload).then(() => {
          this.$toast?.success('Order updated!');
        }).catch(() => {
          this.$toast?.error('Failed to update order');
        });
      }
    }
  };
  </script>
  
  <style scoped>
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
  