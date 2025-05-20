<template>
  <v-container fluid>
    <v-row><h2>Add New Sub-Category</h2></v-row>
    <v-row>
      <v-col cols="6" class="d-flex">
        <v-btn :loading="backLoading" :disabled="backLoading" small @click="navigateBack">
            <template v-slot:loader>
                <v-progress-circular indeterminate size="20" color="white"></v-progress-circular>
            </template>
            <v-icon v-if="!backLoading">mdi-arrow-left</v-icon>
            <span v-if="!backLoading">Back</span>
        </v-btn>
      </v-col>

      <v-col cols="6" class="text-end">
        <v-btn small outlined @click="discard" class="mr-2">Discard</v-btn>
        <v-btn small color="success" :loading="saveLoading" :disabled="saveDisabled" @click="saveSubCategory">
          <template v-slot:loader>
                <v-progress-circular indeterminate size="20" color="white"></v-progress-circular>
            </template>
          <span v-if="!saveLoading">Save</span>
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <!-- Category and Sub-Category -->
      <v-col cols="12" md="9">
        <v-card outlined>
          <v-card-text>
            <v-card-subtitle class="black--text">Category *</v-card-subtitle>
            <v-select dense outlined v-model="form.mcat_id" :items="mcats" item-text="mcat_name" item-value="mcat_id" 
              label="Select Category" :rules="[v=>!!v||'Required']" @change="resetSubcat"/>

            <v-card-subtitle class="black--text">Sub-Category *</v-card-subtitle>
            <v-text-field dense outlined v-model="form.subcatname" label="Sub-Category Name" 
              :rules="msubcatnameRule" @blur="checkDuplicate"/>
          </v-card-text>
        </v-card>

        <!-- Collection Type -->
        <v-card outlined class="my-3">
          <v-card-subtitle>Collection Type</v-card-subtitle>
          <v-card-text>
            <v-radio-group v-model="mcattype" column dense>
              <v-radio label="Manual" value="manual"/>
              <v-radio label="Smart"  value="smart"/>
            </v-radio-group>
          </v-card-text>
        </v-card>

        <!-- Products Browse -->
        <v-card v-if="mcattype==='manual'" outlined class="my-3">
          <v-card-subtitle>Products</v-card-subtitle>
          <v-card-text>
            <v-row>
              <v-col cols="6">
                <v-text-field dense outlined prepend-inner-icon="mdi-magnify" v-model="productSearch" 
                  placeholder="Search Product"/>
              </v-col>
              <v-col cols="2">
                <v-btn outlined @click="productDialog=true">Browse</v-btn>
              </v-col>
              <v-col cols="4">
                <v-select dense outlined prefix="Sort:" :items="sorts" v-model="sortMethod" @change="sortSelected"/>
              </v-col>
            </v-row>

            <v-divider class="my-2"/>

            <div v-if="selectedProducts.length" class="px-4 pb-4">
              <h4 class="mb-2">Selected Products</h4>
              <v-list dense>
                <v-list-item v-for="p in selectedProducts" :key="p.mproduct_id">
                  <v-list-item-avatar>
                    <img :src="p.mproduct_image ? cdn+p.mproduct_image : '/images/no-image-available.png'"/>
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>{{ p.mproduct_title }}</v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon @click="removeProduct(p.mproduct_id)">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
            </div>
          </v-card-text>
        </v-card>

        <!-- Conditions All / Any -->
        <v-card v-else outlined class="my-3">
          <v-card-subtitle>Conditions</v-card-subtitle>
          <v-card-text>
            <div class="d-flex align-items-end mb-3">
              Product must match:
              <v-radio-group v-model="acondition" row dense hide-details class="ms-2">
                <v-radio label="All conditions" value="all"/>
                <v-radio label="Any condition" value="any"/>
              </v-radio-group>
            </div>

            <div v-for="(row, idx) in conditions" :key="idx" class="row mb-2">
              <div class="col-md-4">
                <v-autocomplete
                  dense
                  outlined
                  v-model="row.tag"
                  :items="ruleColumns"
                  item-text="field_name"
                  item-value="field_id"
                  label="Field"
                  @change="updateRelations(idx)"
                />
              </div>

              <div class="col-md-4">
                <v-autocomplete
                  dense
                  outlined
                  v-model="row.condition"
                  :items="row.relations"
                  item-text="query_name"
                  item-value="query_id"
                  label="Condition"
                />
              </div>

              <div class="col-md-4 d-flex">
                <div v-if="['Inventory stock'].includes(getFieldNameById(row.tag))">
                  <v-text-field
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    type="number"
                    class="flex-grow-1"
                    :rules="[(v) => v === '' || /^\d+$/.test(v) || 'Must be a whole number']"
                  />
                </div>

                <div v-else-if="['Title'].includes(getFieldNameById(row.tag))">
                  <v-text-field
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    type="text"
                    class="flex-grow-1"
                  />
                </div>

                <div v-else-if="['Price'].includes(getFieldNameById(row.tag))">
                  <v-text-field
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    type="number"
                    class="flex-grow-1"
                    :rules="[(v) => v === '' || !isNaN(v) || 'Must be a number']"
                  />
                </div>
                <div v-else-if="['Type'].includes(getFieldNameById(row.tag))">
                  <v-combobox
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    class="flex-grow-1"
                    :items="getDynamicSuggestions(row.tag)"
                    item-text="mproduct_type_name"
                    item-value="mproduct_type_id"
                    :return-object="true"
                  />
                </div>
                <div v-else-if="['Brand'].includes(getFieldNameById(row.tag))">
                  <v-combobox
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    class="flex-grow-1"
                    :items="getDynamicSuggestions(row.tag)"
                    item-text="mbrand_name"
                    item-value="mbrand_id"
                    :return-object="true"
                  />
                </div>
                <!-- Default → combobox with dynamic items -->
                <div v-else>
                  <v-combobox
                    dense
                    outlined
                    v-model="row.value"
                    label="Value"
                    class="flex-grow-1"
                    :items="getDynamicSuggestions(row.tag)"
                    item-text="mtag_name"
                    item-value="mtag_id"
                    :return-object="true"
                  />
                </div>

                <v-btn icon v-if="conditions.length > 1" @click="removeCondition(idx)">
                  <v-icon color="red">mdi-trash-can</v-icon>
                </v-btn>
              </div>
            </div>


            <v-btn outlined small @click="addCondition">
              <v-icon small>mdi-plus</v-icon> Add another condition
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Publishing -->
      <v-col cols="12" md="3">
        <v-card outlined class="mb-3">
          <v-card-actions><span class="body-2 fw-semibold">Publishing</span></v-card-actions>
          <v-card-text>
            <v-row>
                <v-checkbox v-model="msubcat_publish" label="Online Store" value="Online Store" />
                <v-checkbox v-model="msubcat_publish" label="Other" value="Other" />
            </v-row>
          </v-card-text>
        </v-card>

        <!-- Image -->
        <v-card outlined>
          <v-card-actions><span class="body-2 fw-semibold">Image *</span></v-card-actions>
          <v-card-text>
            <div v-if="!imagePreview">
              <v-file-input dense hide-input accept="image/*" prepend-icon="mdi-camera-outline" label="Upload image" 
                @change="onImageSelect"/>
            </div>
            <div v-else class="d-flex align-center">
              <v-img :src="imagePreview" max-width="80" max-height="80" contain class="uploader-box"/>
              <v-btn icon small class="ml-2" @click="clearImage">
                <v-icon color="red">mdi-trash-can</v-icon>
              </v-btn>
            </div>
          </v-card-text>
        </v-card>

        <!-- Create offer -->
        <v-card outlined class="mt-3">
          <v-card-actions><span class="body-2 fw-semibold">Create Offer</span></v-card-actions>
          <v-text-field class="px-4 mt-6" v-model="form.offer_name" label="Offer Name" dense outlined/>
          <v-text-field class="px-4 mt-3" v-model="form.start_time" label="Start Time" type="datetime-local" dense outlined :rules="startTimeRules"/>
          <v-text-field class="px-4 mt-3" v-model="form.end_time" label="End Time" type="datetime-local" dense outlined :rules="endTimeRules"/>
        </v-card>

        <!-- Sub-Category Tag -->
        <v-card outlined class="mt-3">
          <v-card-actions><span class="body-2 fw-semibold">Sub-Category Tag</span></v-card-actions>
          <v-card-text>
            <v-text-field dense outlined v-model="form.subcattag" label="Sub-Category Tag Name"/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="productDialog" max-width="650">
      <v-card>
        <v-card-title>
          <span class="text-h6">Select Products</span>
          <v-spacer/>
          <v-icon @click="productDialog=false">mdi-close</v-icon>
        </v-card-title>

        <v-card-text>
          <v-text-field dense outlined prepend-inner-icon="mdi-magnify" v-model="productSearch" placeholder="Search Product"/>
        </v-card-text>

        <v-data-table v-model="productSelection" :items="allProducts" :headers="productHeaders" :search="productSearch" show-select 
          item-key="mproduct_id" return-object :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100],
                        'items-per-page-text': 'Rows per page:'
                        }">
          <template #item.mproduct_image="{ item }">
            <img :src="item.mproduct_image ? cdn+item.mproduct_image : '/images/no-image-available.png'" width="50"/>
          </template>
        </v-data-table>

        <v-card-actions>
          <v-spacer/><v-btn color="primary" @click="confirmProducts">Done</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AddSubCategory',

  data () {
    return {
      cdn : 'https://cdn.truewebpro.com/',

      mcats : [],
      msubcats: [],
      allProducts: [], 
      allTypes: [], 
      allTags: [], 
      allBrands: [], 
      productSearch:'', 
      productSelection:[],
      selectedProducts:[], 
      sortMethod:null,
      productHeaders:[
        { text:'Image', value:'mproduct_image', sortable: false },
        { text:'Name',  value:'mproduct_title' }
      ],
      sorts:[
        'Product Title A-Z','Product Title Z-A'
      ],

      ruleColumns:[], 
      allQueries:[], 
      fieldQueryMap:{},
      conditions:[
        { tag:'', condition:'', value:'', relations:[] }
      ],
      acondition:'all',
      msubcat_publish: [],
      form:{
        mcat_id:null, 
        subcatname:'', 
        subcattag:'', 
        image:null,
        offer_name: '',
        start_time: null,
        end_time: null,
      },

      mcattype:'manual',
      imagePreview:null, 
      nameError:'',

      productDialog:false,
      backLoading: false,
      saveLoading: false,
      msubcatnameRule: [
          v => !!v || 'Sub-Category is required',
          v => (v && v.length >= 3) || 'Name must be at least 3 characters',
          (v) =>
              !this.msubcats.some(
                  (msubcat) =>
                  msubcat.msubcat_name.toLowerCase().trim() === v.toLowerCase().trim() &&
                  msubcat.msubcat_id !== this.msubcat_id
            ) || "Sub-Category already exists"
      ],
      existingOffers: [],
      startTimeRules: [
        v => v >= this.now || 'Start time cannot be in the past'
      ],
      endTimeRules: [
        v => v > this.form.start_time || 'End time must be after start time'
      ],
    }
  },

  computed:{
    now() {
      return new Date().toISOString().slice(0, 16);
    },
    saveDisabled () {
      const base = !this.form.mcat_id ||
        !this.form.subcatname.trim() ||
        !this.form.image ||
        !!this.nameError;

      const startValid = !this.form.start_time || this.form.start_time >= this.now;
      const endValid = !this.form.end_time || this.form.end_time > this.form.start_time;

      if (this.mcattype === 'smart') {
        return base || !startValid || !endValid || !this.hasValidConditionRow;
      }

      return base || !startValid || !endValid;
    },

    hasValidConditionRow () {
      return this.conditions.some(r =>
        r.tag && r.condition && (r.value !== '' && r.value !== null)
      )
    },
  },

  created () {
    this.fetchCats()
    this.fetchProducts()
    this.fetchRuleMeta()
  },

  methods:{
    async fetchCats () {
      const [catR, subR] = await Promise.all([
        axios.get('/admin/mcategories/vlist'),
        axios.get('/admin/msub-categories/vlist')
      ])
      this.mcats   = catR.data.mcats
      this.msubcats = subR.data.msubcats
    },
    resetSubcat () {
      this.form.subcatname=''
      this.nameError=''
    },
    getFieldNameById(fieldId) {
      const field = this.ruleColumns.find(f => f.field_id === fieldId);
      return field ? field.field_name : '';
    },
    getDynamicSuggestions(fieldId) {
      const name = this.getFieldNameById(fieldId);

      switch (name) {
        case 'Type':
          return this.allTypes ?? [];
        case 'Brand':
          return this.allBrands ?? [];
        case 'Tag':
          return this.allTags ?? [];
        default:
          return [];
      }
    },
    checkDuplicate () {
      if(!this.form.mcat_id||!this.form.subcatname.trim()) return
      axios.get('/admin/msub-categories/vlist')
        .then(r=>{
          const exists = r.data.msubcats.some(s =>
            s.mcat_id===this.form.mcat_id &&
            s.msubcat_name.toLowerCase()===this.form.subcatname.trim().toLowerCase())
          this.nameError = exists ? 'Sub-category already exists' : ''
        })
    },

    async fetchProducts () {
      const { data } = await axios.get('/admin/mcollproducts/vlist')
      this.allProducts = data.products
      this.allTypes  = data.types;
      this.allBrands = data.brands;
      this.allTags   = data.tags;
    },
    confirmProducts () {
      this.selectedProducts=[...this.productSelection]
      this.productDialog=false
    },
    removeProduct(id){
      this.selectedProducts = this.selectedProducts.filter(p=>p.mproduct_id!==id)
      this.productSelection = this.productSelection.filter(o=>
        (o.mproduct_id||o)!==id)
    },
    sortSelected () {
      const k=this.sortMethod
      const p=this.selectedProducts
      switch(k){
        case 'Product Title A-Z': p.sort((a,b)=>a.mproduct_title.localeCompare(b.mproduct_title)); break
        case 'Product Title Z-A': p.sort((a,b)=>b.mproduct_title.localeCompare(a.mproduct_title)); break
      }
    },

    async fetchRuleMeta () {
      const { data } = await axios.get('/admin/querys/vlist')
      this.ruleColumns = data.fields
      this.allQueries  = data.querys
      this.fieldQueryMap = data.relations.reduce((m,r)=>{
        (m[r.field_id] ||= []).push(r.query_id)
        return m
      },{})
    },
    updateRelations(idx){
      const allowed=this.fieldQueryMap[this.conditions[idx].tag]||[]
      this.conditions[idx].relations =
        this.allQueries.filter(q=>allowed.includes(q.query_id))
      this.conditions[idx].condition=''
    },
    addCondition(){ this.conditions.push({ tag:'',condition:'',value:'',relations:[] }) },
    removeCondition(idx){ this.conditions.splice(idx,1) },

    onImageSelect(file){
      if(file){ this.form.image=file; this.imagePreview=URL.createObjectURL(file) }
      else this.clearImage()
    },
    clearImage(){ this.form.image=null; this.imagePreview=null },

    async saveSubCategory () {

      this.saveLoading = true;

      const fd = new FormData()
      fd.append("msubcat_publish", JSON.stringify(this.msubcat_publish ?? []));
      fd.append('offer_name', this.form.offer_name ?? '');
      fd.append('start_time', this.form.start_time ?? '');
      fd.append('end_time', this.form.end_time ?? '');
      Object.entries(this.form).forEach(([k, v]) => fd.append(k, v ?? ''))
      fd.append('mcattype', this.mcattype)
      if (this.mcattype === 'smart') {
        const trimmed = this.conditions.map(c => {
          let value = c.value;

          // Extract only the ID if it's an object (used in tag, brand, type)
          if (value && typeof value === 'object') {
            value = value.mtag_id || value.mbrand_id || value.mproduct_type_id || value.id || '';
          }

          return {
            field_id: c.tag,
            query_id: c.condition,
            value: value ?? ''
          };
        });

        fd.append('condition_logic', this.acondition);
        fd.append('conditions', JSON.stringify(trimmed));
      } else {                           
        const ids = this.selectedProducts.map(p => p.mproduct_id)
        fd.append('product_ids', JSON.stringify(ids))
      }
      await axios.post('/admin/msub-category/add', fd)
      .then((resp)=>{
          console.log(resp.data);
          window.location.href = '/admin/msub-categories/list';
          this.$toast.success('Sub-Category added successfully!');
      })
    },
    navigateBack() {
          if (this.backLoading) return;
          this.backLoading = true;
          setTimeout(() => {
              window.location.href = '/admin/msub-categories/list';
          }, 500); 
    },
    discard () {
      this.form={
        mcat_id:null,
        subcatname:'',
        subcattag:'',
        image:null,
        offer_name: '',
        start_time: null,
        end_time: null,
      }
      msubcat_publish:[],
      this.imagePreview=null; 
      this.nameError=''; 
      this.mcattype='manual'
      this.selectedProducts=[]; 
      this.productSelection=[]
      this.conditions=[{
        tag:'',
        condition:'',
        value:'',
        relations:[]}
      ]
    }
  }
}
</script>
<style scoped>
/* unchanged styles */
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
