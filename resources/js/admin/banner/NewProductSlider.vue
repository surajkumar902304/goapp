<template>
  <div class="page-margin-20-40">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">New Products Sliders</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="font-weight: bold; color: #1976d2; background-color: white !important; 
            border: 1px solid #1976d2 !important;" @click="openModal">
              Add Slider
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-card elevation="5" class="mt-4">
      <v-data-table v-model="mainSel" :items="sliderRows" :headers="sliderHdr" :search="mainSearch" item-key="slider_row_id" show-select
        :footer-props="{ 'items-per-page-options':[10,25,50] }">
        <template v-slot:top>
          <v-row dense class="mx-1 pb-1">
            <v-text-field v-model="mainSearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Product, Variant"/>
          </v-row>
        </template>
        <template #item.img="{ item }">
          <img :src="img(item.img)" width="50">
        </template>
        <template #header.actions>
          <div class="text-center">Action</div>
        </template>
        <template #item.actions="{ item }">
          <div class="text-center">
            <v-chip outlined small color="red" class="white--text" @click="rowDelConfirm(item)">
              <v-icon small left>mdi-delete</v-icon>Remove
            </v-chip>
          </div>
        </template>
        <template #header.delete>
          <div v-if="mainSel.length" class="d-flex justify-end align-center">
            <v-menu offset-y>
              <template #activator="{ on,attrs }">
                <div v-bind="attrs" v-on="on" style="cursor:pointer" class="d-flex align-center">
                  <span class="mr-2 text-caption">{{ mainSel.length }} selected</span>
                  <v-icon color="primary">mdi-dots-vertical</v-icon>
                </div>
              </template>
              <v-list dense>
                <v-list-item @click="bulkDelConfirm"><v-list-item-title>Remove</v-list-item-title></v-list-item>
              </v-list>
            </v-menu>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="modalOpen" max-width="750">
      <v-card elevation="5">
        <v-card-title>
          <span class="text-h6">Select Products</span><v-spacer/>
          <v-icon style="cursor:pointer" @click="modalOpen=false">mdi-close</v-icon>
        </v-card-title>

        <v-card-text>
          <v-text-field dense outlined prepend-inner-icon="mdi-magnify" v-model="modalSearch" placeholder="Search Product, Variant"/>
          <v-data-table v-model="modalSel" :items="allVariants" :headers="modalHdr" :search="modalSearch" item-key="mvariant_id" show-select return-object
            :footer-props="{ 'items-per-page-options':[10,25,50] }">
            <template #item.img="{ item }">
              <img :src="img(item.img)" width="50">
            </template>
          </v-data-table>
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" style="font-weight: bold; color: #1976d2; background-color: white !important;" @click="save">Add</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- confirm dialogs -->
    <v-dialog v-model="delDlg"  max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Remove</v-card-title>
        <v-card-text>Are you sure you want to Remove this new product?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="delDlg = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performRowDelete">Remove</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="bulkDlg" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Remove</v-card-title>
          <v-card-text>Are you sure you want to Remove <strong>{{ mainSel.length }}</strong> new products?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="bulkDlg = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="bulkDeleteLoading" :disabled="bulkDeleteLoading" @click="performBulkDelete">Remove</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'NewProductSlider',

  data(){
    return{
      cdn:'https://cdn.truewebpro.com/',
      mainSearch:'', 
      sliderRows:[], 
      mainSel:[],
      sliderHdr: [
        { text:'', value:'data-table-select' },
        { text:'Image', value:'img', sortable:false },
        { text:'Product', value:'product' },
        { text:'Variant', value:'variantLabel' }, 
        { text:'Action',  value:'actions', sortable:false },
        { text: '', value: 'delete', sortable: false, width: '130px' }
      ],

      modalOpen:false, 
      modalSearch:'', 
      allVariants:[], 
      modalSel:[],
      modalHdr: [
        { text:'', value:'data-table-select', width:10 },
        { text:'Image', value:'img', sortable:false },
        { text:'Product', value:'product' },
        { text:'Variant', value:'variantLabel' }, 
      ],
      delDlg:false,   
      delTarget:null,
      bulkDlg:false,
      deleteLoading: false,
      bulkDeleteLoading: false,
    }
  },
  mounted(){ 
    this.fetchRows(); 
    this.fetchVariants(); 
  },
  methods:{
    img(src){ 
      return src?this.cdn+src:'/images/no-image-available.png'; 
    },
    async fetchRows(){
      const {data}=await axios.get('/admin/new-products');
      this.sliderRows = (data.rows || []).map((r, i) => {
        const det   = r.variant.details || [];
        const label = det.length
          ? det.map(d =>
              Object.entries(d.option_value)       
                    .map(([k,v]) => `${k}: ${v}`)  
                    .join(', ')                    
            ).join(' | ')
          : '';

        return {
          slider_row_id : r.new_product_id,
          mvariant_id   : r.mvariant_id,
          img           : r.variant.mvariant_image || r.variant.product.mproduct_image,
          product       : r.variant.product.mproduct_title,
          variantLabel  : label,        
          index         : i + 1,
        };
      });
    },
    async fetchVariants(){
      const {data}=await axios.get('/admin/variants/list');
      this.allVariants = (data.variants || []).map((v, i) => {
        const det   = v.details || [];
        const label = det.length
          ? det.map(d =>
              Object.entries(d.option_value)       
                    .map(([k,v]) => `${k}: ${v}`)  
                    .join(', ')                    
            ).join(' | ')
          : '';

        return {
          mvariant_id  : v.mvariant_id,
          img          : v.mvariant_image || v.product.mproduct_image,
          product      : v.product.mproduct_title,
          variantLabel : label,        
          index        : i + 1,
        };
      });
    },
    openModal(){
      this.modalSel = this.allVariants.filter(v =>
      this.sliderRows.some(s=>s.mvariant_id===v.mvariant_id));
      this.modalOpen = true;
    },
    async save(){
      const ids = this.modalSel.map(v=>v.mvariant_id);
      if(!ids.length){ 
        this.$toast.error('Select at least one'); 
        return; }
      await axios.post('/admin/new-products',{variant_ids:ids});
      this.modalOpen=false; 
      await this.fetchRows();
      this.$toast.success('Slider updated', {
              timeout: 500
          })
    },
    rowDelConfirm(row){ 
      this.delTarget=row.slider_row_id; 
      this.delDlg=true; 
    },
    async performRowDelete(){
      this.deleteLoading = true;

      try {
          await axios.delete(`/admin/new-products/${this.delTarget}`);

          this.$toast?.success('new product removed!', {
              timeout: 500
          })
          this.delDlg=false; 
          this.fetchRows(); 
      } catch (err) {
          console.error(err);
          this.$toast?.error('Failed to remove new product.', {
              timeout: 500
          })
      } finally {
          this.deleteLoading = false;
          this.delDlg   = false;
      }
    },
    bulkDelConfirm(){ 
      if(this.mainSel.length) 
      this.bulkDlg=true; 
    },
    async performBulkDelete(){
      this.bulkDeleteLoading = true;

      try {
          const ids=this.mainSel.map(r=>r.slider_row_id);
          await axios.post('/admin/new-products/bulk-delete',{ids});

          this.$toast?.success('Selected new product removed!', {
              timeout: 500
          })
          this.bulkDlg=false; 
          this.mainSel=[]; 
          this.fetchRows(); 
      } catch (err) {
          console.error(err);
          this.$toast?.error('Failed to remove selected new product.', {
              timeout: 500
          })
      } finally {
          this.bulkDeleteLoading = false;
          this.bulkDlg   = false;
      }
    },
  }
};
</script>

<style scoped>
.v-input{font-size:12px!important}
</style>
