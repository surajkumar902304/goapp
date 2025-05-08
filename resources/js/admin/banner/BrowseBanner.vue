<template>
    <div>
      <v-row>
        <v-col cols="12" md="10">
          <v-text-field
            v-model="ssearch"
            clearable
            dense
            hide-details
            outlined
            prepend-inner-icon="mdi-magnify"
            placeholder="Search name"
          />
        </v-col>
        <v-col cols="12" md="2" class="text-end">
          <v-btn color="secondary" small class="text-none font-weight-bold" @click="openDialog">
            Add Banner
          </v-btn>
        </v-col>
      </v-row>
  
      <v-row>
        <v-col cols="12">
          <v-card outlined>
            <v-simple-table>
              <thead>
                <tr>
                  <th style="width:30%">Imagedefwsdf</th>
                  <th style="width:40%">Name</th>
                  <th style="width:20%">Actions</th>
                  <th style="width:10%">Position Drag</th> <!-- drag column -->
                </tr>
              </thead>
              <draggable tag="tbody" :list="browsebanners" handle=".drag-handle" @end="onDragEnd">
                <tr v-for="item in filteredBanners" :key="item.browsebanner_id">
                  <td class="p-2">
                    <img :src="cdn + item.browsebanner_image || 'https://via.placeholder.com/50'" width="150" height="100" />
                  </td>
                  <td class="align-middle">
                    {{ item.browsebanner_name }}
                  </td>
                  <td>
                    <v-btn icon color="primary" @click="editItem(item)">
                      <v-icon small>mdi-pencil</v-icon>
                    </v-btn>
                  </td>
                  <!-- drag handle column -->
                  <td class="text-center drag-handle" style="cursor: grab">
                    <v-icon small>mdi-drag</v-icon>
                  </td>
                </tr>
              </draggable>
            </v-simple-table>
          </v-card>
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
        browsebanners: [],
        categoriesAll: [],
        categories  : [],
        subcategories : [],
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
        imagePreview: null,
        imageName: '',
        bannernameRule: [
          v => !!v || 'Banner Name is required',
          v => (v && v.length >= 3) || 'Name must be at least 3 characters',
          (v) => !this.browsebanners.some(
            (banner) =>
              banner.browsebanner_name === v &&
              banner.browsebanner_id !== this.defaultItem.browsebanner_id
          ) || 'Banner already exists'
        ],
        bannerimageRule: [
            v => !!v || 'Banner Name is required',
        ]
      };
    },
    created() {
      this.getAllBanners();
      this.loadCategories();
    },
    watch: {
        addSdialog(val) {
            if (!val) this.submitting = false;
        },
        /* ==== category change ==== */
      'defaultItem.mcat_id'(val){
        if (this.fillLock) return        // 🚫  fill के वक़्त block कर दो
        if (!this.categoriesAll.length) return
        const cat = this.categoriesAll.find(c=>c.mcat_id===val)
        this.subcategories           = cat ? cat.subcategories : []
        this.products                = []
        this.defaultItem.msubcat_id  = null
        this.defaultItem.mproduct_id = null
      },

      /* ==== sub‑category change ==== */
      'defaultItem.msubcat_id'(val){
        if (this.fillLock) return
        if (!this.subcategories.length) return
        const sub = this.subcategories.find(s=>s.msubcat_id===val)
        this.products             = sub ? sub.products : []
        this.defaultItem.mproduct_id = null
      }
    },
    computed: {
      isImageSelected() {
        return !!this.imageName;
      },
      filteredBanners() {
  const term = (this.ssearch || '').toLowerCase();

  return this.browsebanners.filter(b =>
    (b.browsebanner_name || '').toLowerCase().includes(term)
  );
}
    },
    methods: {
      getAllBanners() {
        axios.get('/admin/browsebanners/vlist').then(res => {
          this.browsebanners = res.data.browsebanner;
        });
      },
      async loadCategories () {
        if (this.categoriesAll.length) return          // already cached
        const { data } = await axios.get('/admin/main/categories')
        this.categoriesAll = data.categories           // ⬅️ nested lists यहीं रहेंगे
        this.categories    = data.categories.map(c => ({
          mcat_id  : c.mcat_id,
          mcat_name: c.mcat_name
        }))
      },

      /* 2) ensure helper – promise जिसे editItem await करेगा */
      ensureCategoriesLoaded () {
  return this.categoriesAll.length
    ? Promise.resolve()
    : this.loadCategories()
},
      openDialog() {
        this.defaultItem = { mcat_id:null, msubcat_id:null, mproduct_id:null, browsebanner_id: null, browsebanner_name: '', browsebanner_image: '' };
        this.subcategories=[]; 
        this.products=[];
        this.imagePreview = 'https://via.placeholder.com/150';
        this.imageName = '';
        this.editedIndex = -1;
        this.fsvalid = false;
        this.addSdialog = true;
      },
      /* ------------ editItem ------------- */
  async editItem(item){
    await this.ensureCategoriesLoaded()

    /* 1) पहले drop‑downs तैयार करो  */
    const cat = this.categoriesAll.find(c => c.mcat_id === item.mcat_id)
    const subs = cat ? cat.subcategories : []
    const sub  = subs.find(s => s.msubcat_id === item.msubcat_id)
    const prods  = sub ? sub.products : []

    /* lock watcher → fill everything → unlock */
    this.fillLock = true
    this.subcategories = subs
    this.products      = prods

    this.defaultItem = {
      browsebanner_id   : item.browsebanner_id,
      browsebanner_name : item.browsebanner_name,
      browsebanner_image: '',
      mcat_id           : item.mcat_id,
      msubcat_id        : item.msubcat_id,
      mproduct_id       : item.mproduct_id
    }
    this.$nextTick(() => {     // unlock बाद के टिक पर
      this.fillLock = false
    })

    this.imagePreview = item.image_url || (this.cdn + item.browsebanner_image)
    this.imageName    = item.browsebanner_image
                        ? item.browsebanner_image.split('/').pop()
                        : ''

    this.editedIndex  = item.browsebanner_id
    this.fsvalid      = true
    this.addSdialog   = true
  },
      triggerFileInput() {
        this.$refs.imageInput.click();
      },
      handleImageUpload(e) {
        const file = e.target.files[0];
        if (file) {
          this.defaultItem.browsebanner_image = file;
          this.imagePreview = URL.createObjectURL(file);
          this.imageName = file.name;
        }
      },
      onDialogToggle(open) {
        if (!open) {
          this.defaultItem = { mcat_id:null, msubcat_id:null, mproduct_id:null, browsebanner_id: null, browsebanner_name: '', browsebanner_image: '' };
          this.subcategories=[]; 
          this.products=[];
          this.imagePreview = null;
          this.imageName = '';
          this.fsvalid = false;
          this.submitting = false;
          this.editedIndex = -1;
        }
      },
      saveBanner() {
        this.submitting = true;
        const fd = new FormData();
        if (this.defaultItem.mcat_id    != null) fd.append('mcat_id',    this.defaultItem.mcat_id)
        if (this.defaultItem.msubcat_id != null) fd.append('msubcat_id', this.defaultItem.msubcat_id)
        if (this.defaultItem.mproduct_id!= null) fd.append('mproduct_id',this.defaultItem.mproduct_id)
        fd.append('browsebanner_name', this.defaultItem.browsebanner_name);
        if (this.defaultItem.browsebanner_image instanceof File) {
          fd.append('browsebanner_image', this.defaultItem.browsebanner_image);
        }
        if (this.editedIndex !== -1) {
          fd.append('browsebanner_id', this.editedIndex);
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
  