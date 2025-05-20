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
        <v-col cols="12" md="2" class="text-end mt-1">
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
                  <th style="width:30%">Image</th>
                  <th style="width:40%">Banner Name</th>
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
                    <v-icon small color="red"  @click="confirmDelete(item)">
                        mdi-delete
                    </v-icon>
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
              <!-- Main-Category dropdown -->
              <v-select
                dense
                outlined
                v-model="defaultItem.main_mcat_id"
                :items="mainCats"
                item-value="main_mcat_id"
                item-text="main_mcat_name"
                label="Main Category"
                clearable
              />

              <!-- Category dropdown -->
              <v-select dense outlined
                        v-if="categories.length"
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

        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h6">
                    Confirm Delete
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete this Category?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
  </template>
  
  <script>
import axios      from 'axios'
import draggable  from 'vuedraggable'

export default {
  name       : 'BrowseBanner',
  components : { draggable },

  /* ───────────────────────── data ───────────────────────── */
  data () {
    return {
      /* table */
      cdn            : 'https://cdn.truewebpro.com/',
      ssearch        : '',
      browsebanners  : [],          

      /* cascading-select caches  */
      mainCats       : [],           
      categories     : [],           
      subcategories  : [],           
      products       : [],           

      /* dialog / form */
      addSdialog     : false,
      editedIndex    : -1,           
      fsvalid        : false,
      submitting     : false,
      fillLock       : false,       
      defaultItem    : {
        browsebanner_id   : null,
        browsebanner_name : '',
        browsebanner_image: '',
        main_mcat_id      : null,
        mcat_id           : null,
        msubcat_id        : null,
        mproduct_id       : null
      },

      /* image helper */
      imagePreview   : null,
      imageName      : '',

      /* validators */
      bannernameRule : [
        v => !!v || 'Banner name is required',
        v => (v && v.length >= 3) || 'Min 3 characters'
      ],

      /* delete dialog */
      deleteDialog        : false,
      browseBannerToDelete: null,
      deleteLoading       : false
    }
  },

  async created () {
    await Promise.all([ this.loadMainCats(), this.loadBanners() ])
  },

  watch : {
    /* MAIN-CATEGORY changed */
    'defaultItem.main_mcat_id' (val) {
      if (this.fillLock) return
      const mc = this.mainCats.find(m => m.main_mcat_id === val) || {}

      this.categories     = mc.categories  || []
      this.subcategories  = []
      this.products       = []

      this.defaultItem.mcat_id     = null
      this.defaultItem.msubcat_id  = null
      this.defaultItem.mproduct_id = null
    },

    /* CATEGORY changed */
    'defaultItem.mcat_id' (val) {
      if (this.fillLock) return
      const cat = (this.categories || []).find(c => c.mcat_id === val) || {}

      this.subcategories  = cat.subcategories || []
      this.products       = []

      this.defaultItem.msubcat_id  = null
      this.defaultItem.mproduct_id = null
    },

    /* SUB-CATEGORY changed */
    'defaultItem.msubcat_id' (val) {
      if (this.fillLock) return
      const sub = (this.subcategories||[]).find(s => s.msubcat_id === val) || {}
      this.products = sub.products || []
      this.defaultItem.mproduct_id = null
    }
  },

  /* ───────────────────────── computed ───────────────────────── */
  computed : {
    isImageSelected () { return !!this.imageName },

    filteredBanners () {
      const term = (this.ssearch || '').toLowerCase()
      return this.browsebanners.filter(b =>
        (b.browsebanner_name || '').toLowerCase().includes(term)
      )
    }
  },

  /* ───────────────────────── methods ───────────────────────── */
  methods : {
    /* --------------- LOADERS --------------- */
    async loadMainCats () {
      /* expects: [{
           main_mcat_id, main_mcat_name,
           categories:[{
             mcat_id, mcat_name,
             subcategories:[{
               msubcat_id, msubcat_name,
               products:[{mproduct_id,mproduct_title}]
             }]
           }]
      }] */
      const { data } = await axios.get('/admin/main/categories')
      this.mainCats = data.categories
    },

    async loadBanners () {
      const { data } = await axios.get('/admin/browsebanners/vlist')
      this.browsebanners = data.browsebanner
    },

    /* helper that guarantees mainCats are loaded before edit-fill */
    ensureCats () {
      return this.mainCats.length ? Promise.resolve() : this.loadMainCats()
    },

    /* --------------- DIALOG OPENERS --------------- */
    openDialog () {
      this.resetForm()
      this.imagePreview = 'https://via.placeholder.com/150'
      this.addSdialog   = true
    },

    async editItem (item) {
      await this.ensureCats()

      /* prepare option lists based on stored ids */
      const main   = this.mainCats.find(m => m.main_mcat_id === item.main_mcat_id) || {}
      const cat    = (main.categories     || []).find(c => c.mcat_id    === item.mcat_id)    || {}
      const sub    = (cat.subcategories   || []).find(s => s.msubcat_id === item.msubcat_id) || {}

      this.fillLock     = true            // stop watchers while filling
      this.categories    = main.categories    || []
      this.subcategories = cat.subcategories  || []
      this.products      = sub.products       || []

      this.defaultItem = {
        browsebanner_id   : item.browsebanner_id,
        browsebanner_name : item.browsebanner_name,
        browsebanner_image: '',
        main_mcat_id      : item.main_mcat_id,
        mcat_id           : item.mcat_id,
        msubcat_id        : item.msubcat_id,
        mproduct_id       : item.mproduct_id
      }
      this.$nextTick(() => (this.fillLock = false))

      this.imagePreview = this.cdn + item.browsebanner_image
      this.imageName    = item.browsebanner_image.split('/').pop()
      this.editedIndex  = item.browsebanner_id
      this.fsvalid      = true
      this.addSdialog   = true
    },

    /* --------------- IMAGE HANDLING --------------- */
    triggerFileInput () { this.$refs.imageInput.click() },

    handleImageUpload (e) {
      const file = e.target.files[0]
      if (!file) return
      this.defaultItem.browsebanner_image = file
      this.imagePreview = URL.createObjectURL(file)
      this.imageName    = file.name
    },

    /* --------------- SAVE --------------- */
    async saveBanner () {
      this.submitting = true
      const fd = new FormData()

      ;['main_mcat_id','mcat_id','msubcat_id','mproduct_id']
        .forEach(k => this.defaultItem[k]!=null && fd.append(k,this.defaultItem[k]))

      fd.append('browsebanner_name', this.defaultItem.browsebanner_name)
      if (this.defaultItem.browsebanner_image instanceof File)
        fd.append('browsebanner_image', this.defaultItem.browsebanner_image)
      if (this.editedIndex !== -1)
        fd.append('browsebanner_id', this.editedIndex)

      const isNew = this.editedIndex === -1
      const url   = isNew ? '/admin/browsebanners/add'
                          : '/admin/browsebanners/update'

      try {
        await axios.post(url, fd, { headers:{'Content-Type':'multipart/form-data'} })
        await this.loadBanners()
        this.$toast.success(isNew ? 'Banner added!' : 'Banner updated!')
        this.addSdialog = false
      } catch {
        this.$toast.error('Save failed')
      } finally {
        this.submitting = false
      }
    },

    /* --------------- DRAG REORDER --------------- */
    async onDragEnd () {
      const payload = this.browsebanners.map((it,i)=>({id:it.browsebanner_id,position:i+1}))
      try {
        await axios.post('/admin/browsebanners/reorder', payload)
        this.$toast.success('Order saved')
      } catch {
        this.$toast.error('Failed to save order')
      }
    },

    /* --------------- DELETE --------------- */
    confirmDelete (item) {
      this.browseBannerToDelete = item
      this.deleteDialog = true
    },

    async performDelete () {
      if (!this.browseBannerToDelete) return
      this.deleteLoading = true
      try {
        await axios.post('/admin/browsebanner-delete',
                         {browsebanner_id:this.browseBannerToDelete.browsebanner_id})
        this.$toast.success('Banner deleted')
        await this.loadBanners()
      } catch {
        this.$toast.error('Delete failed')
      } finally {
        this.deleteLoading = false
        this.deleteDialog  = false
        this.browseBannerToDelete = null
      }
    },

    /* --------------- UTIL --------------- */
    resetForm () {
      this.defaultItem = {
        browsebanner_id   : null,
        browsebanner_name : '',
        browsebanner_image: '',
        main_mcat_id      : null,
        mcat_id           : null,
        msubcat_id        : null,
        mproduct_id       : null
      }
      this.categories    = []
      this.subcategories = []
      this.products      = []
      this.imagePreview  = null
      this.imageName     = ''
      this.editedIndex   = -1
      this.fsvalid       = false
    }
  }
}
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
  .v-btn {
    font-size: 14px !important;
}
  </style>
  