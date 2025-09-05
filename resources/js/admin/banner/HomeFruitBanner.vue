<template>
    <div class="page-margin-20-40">
      <v-container fluid class="pt-0">
        <v-row class="mt-0 pt-0 mb-2">
          <v-col cols="12" md="11" class="p-0">
              <h2 class="text-h6 mb-1">Fruit Sliders</h2> 
          </v-col>

          <v-col cols="12" md="1" class="p-0 ps-2 text-end">
            <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="font-weight: bold; color: #1976d2; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openDialog">
                Add Slider
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
      
      <v-row>
        <v-col cols="12" class="pt-0">
          <v-card elevation="5">
            <v-simple-table style="border-radius: 5px; overflow: hidden;">
              <thead>
                <tr>
                  <th :colspan="5" class="pa-2" style="background-color: white !important;">
                    <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Slider name"/>
                  </th> 
                </tr>
                <tr style="height: 20px; background:#b6b6b6;">
                  <th>Image</th>
                  <th>Slider name</th>
                  <th style="text-align: center;">Action</th>
                  <th style="text-align: center;">Action</th>
                  <th style="text-align: center;">Position drag</th> 
                </tr>
              </thead>
              <draggable tag="tbody" :list="dealbanners" handle=".drag-handle" @end="onDragEnd">
                <tr v-for="item in filteredBanners" :key="item.home_fruit_banner_id">
                  <td class="p-2">
                    <img :src="cdn + item.home_fruit_banner_image || 'https://via.placeholder.com/50'" width="100" height="75" />
                  </td>
                  <td class="align-middle" style="font-size: 12px;">
                    {{ item.home_fruit_banner_name }}
                  </td>
                  <td style="text-align: center;">
                      <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" style="cursor: pointer;">
                          <v-icon small left>mdi-pencil</v-icon>Edit
                      </v-chip>
                  </td>
                  <td style="text-align: center;">
                      <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;">
                          <v-icon small left>mdi-delete</v-icon>Delete
                      </v-chip>
                  </td>
                  <td class="text-center drag-handle" style="cursor: grab">
                    <v-icon small>mdi-drag</v-icon>
                  </td>
                </tr>
              </draggable>
            </v-simple-table>
          </v-card>
        </v-col>
      </v-row>
  
      <v-dialog v-model="addSdialog" max-width="400">
        <v-card elevation="5">
          <v-card-title>
            <span>{{ editedIndex === -1 ? 'Add Slider' : 'Edit Slider' }}</span>
            <v-spacer></v-spacer>
            <v-icon @click="addSdialog = false">mdi-close</v-icon>
          </v-card-title>
          <v-form v-model="fsvalid" @submit.prevent="saveBanner">
            <v-card-text>
              <v-select dense outlined v-model="defaultItem.main_mcat_id" :items="mainCats" item-value="main_mcat_id" item-text="main_mcat_name" label="Main Category" clearable/>
              <v-select dense outlined v-if="categories.length" v-model="defaultItem.mcat_id" :items="categories" item-value="mcat_id" item-text="mcat_name" label="Category"  clearable/> 
              <v-select dense outlined class="mt-3" v-if="subcategories.length" v-model="defaultItem.msubcat_id" :items="subcategories" item-value="msubcat_id" item-text="msubcat_name" label="Sub‑Category"  clearable/>              
              <v-select dense outlined class="mt-3" v-if="products.length" v-model="defaultItem.mproduct_id" :items="products" item-value="mproduct_id" item-text="mproduct_title" label="Product"  clearable/>
              <v-text-field v-model="defaultItem.home_fruit_banner_name" :rules="bannernameRule" label="Slider Name"/>
              <div class="d-flex flex-column align-center">
                <v-card-actions class="pb-0 pt-0">
                  <span class="body-2 fw-semibold">
                    {{ isImageSelected ? 'Selected Image' : 'Select Image' }}
                  </span>
                </v-card-actions>
                <input ref="imageInput" type="file" accept="image/*" style="display:none" @change="handleImageUpload" />
                <div class="uploader-box mb-2" @click="triggerFileInput">
                  <v-img v-if="isImageSelected" :src="imagePreview" class="rounded" max-width="150" max-height="150" cover/>
                  <v-icon v-else size="48" class="grey--text text--lighten-1">mdi-image-area</v-icon>
                </div>
                <div v-if="imageName" class="text-caption">{{ imageName }}</div>
              </div>
            </v-card-text>
            <v-card-actions>
              <template v-if="editedIndex !== -1 || isImageSelected">
                <v-spacer></v-spacer>
                <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fsvalid || submitting">
                  {{ editedIndex === -1 ? 'Add' : 'Update' }}
                </v-btn>
              </template>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>

      <!-- Delete dialog -->
      <v-dialog v-model="deleteDialog" max-width="400">
          <v-card elevation="5">
              <v-card-title class="text-h6">Confirm Delete</v-card-title>
              <v-card-text>Are you sure you want to delete this Slider?</v-card-text>
              <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                  <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
              </v-card-actions>
          </v-card>
      </v-dialog>
    </div>
</template>
  
<script>
import axios      from 'axios'
import draggable  from 'vuedraggable'

export default {
  name       : 'HomeFruitBanner',
  components : { draggable },

  data () {
    return {
      cdn            : 'https://cdn.truewebpro.com/',
      ssearch        : '',
      dealbanners  : [],          

      mainCats       : [],           
      categories     : [],           
      subcategories  : [],           
      products       : [],           

      addSdialog     : false,
      editedIndex    : -1,           
      fsvalid        : false,
      submitting     : false,
      fillLock       : false,       
      defaultItem    : {
        home_fruit_banner_id   : null,
        home_fruit_banner_name : '',
        home_fruit_banner_image: '',
        main_mcat_id      : null,
        mcat_id           : null,
        msubcat_id        : null,
        mproduct_id       : null
      },

      imagePreview   : null,
      imageName      : '',

      bannernameRule : [
        v => !!v || 'Slider name is required',
        v => (v && v.length >= 3) || 'Min 3 characters'
      ],

      deleteDialog        : false,
      browseBannerToDelete: null,
      deleteLoading       : false
    }
  },
  async created () {
    await Promise.all([ this.loadMainCats(), this.loadBanners() ])
  },
  watch : {
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

    'defaultItem.mcat_id' (val) {
      if (this.fillLock) return
      const cat = (this.categories || []).find(c => c.mcat_id === val) || {}

      this.subcategories  = cat.subcategories || []
      this.products       = []

      this.defaultItem.msubcat_id  = null
      this.defaultItem.mproduct_id = null
    },

    'defaultItem.msubcat_id' (val) {
      if (this.fillLock) return
      const sub = (this.subcategories||[]).find(s => s.msubcat_id === val) || {}
      this.products = sub.products || []
      this.defaultItem.mproduct_id = null
    }
  },

  computed : {
    isImageSelected () { return !!this.imageName },

    filteredBanners () {
      const term = (this.ssearch || '').toLowerCase()
      return this.dealbanners.filter(b =>
        (b.home_fruit_banner_name || '').toLowerCase().includes(term)
      )
    }
  },

  methods : {
    async loadMainCats () {
      const { data } = await axios.get('/admin/main/categories')
      this.mainCats = data.categories
    },
    async loadBanners () {
      const { data } = await axios.get('/admin/fruit-banners/vlist')
      this.dealbanners = data.home_fruit_banner
    },
    ensureCats () {
      return this.mainCats.length ? Promise.resolve() : this.loadMainCats()
    },
    openDialog () {
      this.resetForm()
      this.imagePreview = 'https://via.placeholder.com/150'
      this.addSdialog   = true
    },
    async editItem (item) {
      await this.ensureCats()

      const main   = this.mainCats.find(m => m.main_mcat_id === item.main_mcat_id) || {}
      const cat    = (main.categories     || []).find(c => c.mcat_id    === item.mcat_id)    || {}
      const sub    = (cat.subcategories   || []).find(s => s.msubcat_id === item.msubcat_id) || {}

      this.fillLock     = true          
      this.categories    = main.categories    || []
      this.subcategories = cat.subcategories  || []
      this.products      = sub.products       || []

      this.defaultItem = {
        home_fruit_banner_id   : item.home_fruit_banner_id,
        home_fruit_banner_name : item.home_fruit_banner_name,
        home_fruit_banner_image: '',
        main_mcat_id      : item.main_mcat_id,
        mcat_id           : item.mcat_id,
        msubcat_id        : item.msubcat_id,
        mproduct_id       : item.mproduct_id
      }
      this.$nextTick(() => (this.fillLock = false))

      this.imagePreview = this.cdn + item.home_fruit_banner_image
      this.imageName    = item.home_fruit_banner_image.split('/').pop()
      this.editedIndex  = item.home_fruit_banner_id
      this.fsvalid      = true
      this.addSdialog   = true
    },
    triggerFileInput () { 
      this.$refs.imageInput.click() 
    },
    handleImageUpload (e) {
      const file = e.target.files[0]
      if (!file) return
      this.defaultItem.home_fruit_banner_image = file
      this.imagePreview = URL.createObjectURL(file)
      this.imageName    = file.name
    },

    async saveBanner () {
      this.submitting = true
      const fd = new FormData()

      ;['main_mcat_id','mcat_id','msubcat_id','mproduct_id']
        .forEach(k => this.defaultItem[k]!=null && fd.append(k,this.defaultItem[k]))

      fd.append('home_fruit_banner_name', this.defaultItem.home_fruit_banner_name)
      if (this.defaultItem.home_fruit_banner_image instanceof File)
        fd.append('home_fruit_banner_image', this.defaultItem.home_fruit_banner_image)
      if (this.editedIndex !== -1)
        fd.append('home_fruit_banner_id', this.editedIndex)

      const isNew = this.editedIndex === -1
      const url   = isNew ? '/admin/fruit-banners/add'
                          : '/admin/fruit-banners/update'

      try {
        await axios.post(url, fd, { headers:{'Content-Type':'multipart/form-data'} })
        await this.loadBanners()
        this.$toast.success(isNew ? 'Slider added!' : 'Slider updated!', {
                        timeout: 500
                    })
        this.addSdialog = false
      } catch {
        this.$toast.error('Save failed', {
                        timeout: 500
                    })
      } finally {
        this.submitting = false
      }
    },
    async onDragEnd () {
      const payload = this.dealbanners.map((it,i)=>({id:it.home_fruit_banner_id,position:i+1}))
      try {
        await axios.post('/admin/fruit-banners/reorder', payload)
        this.$toast.success('Order saved', {
                        timeout: 500
                    })
      } catch {
        this.$toast.error('Failed to save order', {
                        timeout: 500
                    })
      }
    },
    confirmDelete (item) {
      this.browseBannerToDelete = item
      this.deleteDialog = true
    },
    async performDelete () {
      if (!this.browseBannerToDelete) return
      this.deleteLoading = true
      try {
        await axios.post('/admin/fruit-banners-delete',
                         {home_fruit_banner_id:this.browseBannerToDelete.home_fruit_banner_id})
        this.$toast.success('Banner deleted', {
                        timeout: 500
                    })
        await this.loadBanners()
      } catch {
        this.$toast.error('Delete failed', {
                        timeout: 500
                    })
      } finally {
        this.deleteLoading = false
        this.deleteDialog  = false
        this.browseBannerToDelete = null
      }
    },
    resetForm () {
      this.defaultItem = {
        home_fruit_banner_id   : null,
        home_fruit_banner_name : '',
        home_fruit_banner_image: '',
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
.v-input {
    font-size: 12px !important;
}
</style>
  