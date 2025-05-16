<template>
  <v-container fluid>
    <!-- ===== header / toolbar ===== -->
    <v-row><h2>Edit Sub-Category</h2></v-row>

    <v-row>
      <v-col cols="6" class="d-flex">
        <v-btn :loading="backLoading" :disabled="backLoading" small @click="navigateBack">
          <template #loader>
            <v-progress-circular indeterminate size="20" color="white"/>
          </template>
          <v-icon v-if="!backLoading">mdi-arrow-left</v-icon>
          <span  v-if="!backLoading">Back</span>
        </v-btn>
      </v-col>

      <v-col cols="6" class="text-end">
        <v-btn small outlined class="mr-2" @click="discard">Discard</v-btn>
        <v-btn small color="success" :loading="saveLoading"
               :disabled="saveDisabled" @click="updateSubCategory">
          <template #loader>
            <v-progress-circular indeterminate size="20" color="white"/>
          </template>
          <span v-if="!saveLoading">Update</span>
        </v-btn>
      </v-col>
    </v-row>

    <!-- ===== main form ===== -->
    <v-row>
      <!-- ---- left column ---- -->
      <v-col cols="12" md="9">
        <!-- category + name -->
        <v-card outlined>
          <v-card-text>
            <v-card-subtitle class="black--text">Category *</v-card-subtitle>

            <v-select dense outlined v-model="form.mcat_id"
                      :items="mcats" item-text="mcat_name" item-value="mcat_id"
                      label="Select Category"
                      :rules="[v=>!!v||'Required']"
                      @change="resetSubcat"/>

            <v-card-subtitle class="black--text">Sub-Category *</v-card-subtitle>

            <v-text-field dense outlined class="mt-4"
                          v-model="form.subcatname"
                          label="Sub-Category Name"
                          :rules="msubcatnameRule"
                          />
          </v-card-text>
        </v-card>

        <!-- collection-type -->
        <!-- <v-card outlined class="my-3">
          <v-card-subtitle>Collection Type</v-card-subtitle>
          <v-card-text>
            <v-radio-group v-model="mcattype" column dense>
              <v-radio label="Manual" value="manual"/>
              <v-radio label="Smart"  value="smart"/>
            </v-radio-group>
          </v-card-text>
        </v-card> -->

        <!-- manual products -->
        <v-card v-if="mcattype==='manual'" outlined class="my-3">
          <v-card-subtitle>Products</v-card-subtitle>
          <v-card-text>
            <v-row>
              <v-col cols="6">
                <v-text-field dense outlined prepend-inner-icon="mdi-magnify"
                              v-model="productSearch" placeholder="Search Product"/>
              </v-col>
              <v-col cols="2"><v-btn outlined @click="productDialog=true">Browse</v-btn></v-col>
              <v-col cols="4">
                <v-select dense outlined prefix="Sort:"
                          :items="sorts" v-model="sortMethod" @change="sortSelected"/>
              </v-col>
            </v-row>

            <v-divider class="my-2"/>
            <div v-if="selectedProducts.length" class="px-4 pb-4">
              <h4 class="mb-2">Selected Products</h4>
              <v-list dense>
                <v-list-item v-for="p in selectedProducts" :key="p.mproduct_id">
                  <v-list-item-avatar>
                    <img :src="p.mproduct_image ? cdn+p.mproduct_image
                                                : '/images/no-image-available.png'"/>
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

        <!-- smart conditions -->
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

        <v-card v-if="mcattype==='smart'" class="my-5" outlined>
        <v-card-title class="font-weight-bold">Matched Products</v-card-title>
        <v-card-text>
          <v-data-table
            :headers="[
              { text: 'Index', value: 'index', width: '10%', sortable: true },
              { text: 'Image', value: 'image', width: '20%', sortable: false },
              { text: 'Title', value: 'mproduct_title', width: '40%' },
              { text: 'Variants', value: 'option_value', width: '40%', sortable: false }
            ]"
            :items="indexedMatchedProducts"
            :items-per-page="10"
            class="elevation-1"
          >
            <template v-slot:item.index="{ item }">
              {{ item.index }}
            </template>

            <template v-slot:item.image="{ item }">
              <v-img
                :src="item.image ? cdn + item.image : cdn + item.mproduct_image"
                max-width="60"
                max-height="60"
                class="rounded my-2"
                contain
              />
            </template>

            <template v-slot:item.option_value="{ item }">
              <div v-if="typeof item.option_value === 'object' && item.option_value !== null">
                <div v-for="(val, key) in item.option_value" :key="key">
                  <strong>{{ key }}:</strong> {{ val }}
                </div>
              </div>
            </template>
          </v-data-table>

        </v-card-text>
      </v-card>

      </v-col>

      <!-- ---- right column ---- -->
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

        <!-- image -->
        <v-card outlined>
          <v-card-actions><span class="body-2 fw-semibold">Image *</span></v-card-actions>
          <v-card-text>
            <div v-if="!imagePreview">
              <v-file-input dense hide-input accept="image/*"
                            prepend-icon="mdi-camera-outline"
                            label="Upload image" @change="onImageSelect"/>
            </div>
            <div v-else class="d-flex align-center">
              <v-img :src="imagePreview" max-width="80" max-height="80" contain/>
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
          <v-text-field class="px-4 mt-3" v-model="form.start_time" label="Start Time" type="datetime-local" dense outlined/>
          <v-text-field class="px-4 mt-3" v-model="form.end_time" label="End Time" type="datetime-local" dense outlined :rules="endTimeRules"/>
        </v-card>

        <v-card outlined class="mt-3">
          <v-card-actions><span class="body-2 fw-semibold">Sub-Category Tag</span></v-card-actions>
          <v-card-text>
            <v-text-field dense outlined v-model="form.subcattag"
                          label="Sub-Category Tag Name"/>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- product dialog -->
    <v-dialog v-model="productDialog" max-width="650">
      <v-card>
        <v-card-title>
          <span class="text-h6">Select Products</span>
          <v-spacer/><v-icon @click="productDialog=false">mdi-close</v-icon>
        </v-card-title>

        <v-card-text>
          <v-text-field dense outlined prepend-inner-icon="mdi-magnify"
                        v-model="productSearch" placeholder="Search Product"/>
        </v-card-text>

        <v-data-table v-model="productSelection"
                      :items="allProducts"
                      :headers="productHeaders"
                      :search="productSearch"
                      show-select item-key="mproduct_id" return-object>
          <template #item.mproduct_image="{ item }">
            <img :src="item.mproduct_image ? cdn+item.mproduct_image
                                           : '/images/no-image-available.png'"
                 width="50"/>
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
name: 'EditSubCategory',
props: { msubcatid: { type:Number, required:true } },

/* ---------- data ---------- */
data () {
  return {
    cdn:'https://cdn.truewebpro.com/',

    /* lists */
    mcats:[], 
    msubcats:[],
    allProducts:[], 
    allTypes: [], 
    allTags: [], 
    allBrands: [], 
    matchedProducts: [], 
    productHeaders:[
      {text:'Image', value:'mproduct_image', sortable: false },
      {text:'Name',  value:'mproduct_title'}
    ],

    /* manual products */
    productDialog:false, 
    productSearch:'', 
    productSelection:[],
    selectedProducts:[], 
    smartprodisplay:[], 
    sortMethod:null,
    sorts:['Product Title A-Z','Product Title Z-A'],

    /* smart */
    ruleColumns:[], 
    allQueries:[], 
    fieldQueryMap:{},
    conditions:[
      {tag:'',condition:'',value:'',relations:[]}
    ],
    acondition:'all',
    msubcat_publish: [],
    /* form basics */
    form:{
      mcat_id:null, 
      subcatname:'', 
      subcattag:'',
      image:null,
      offer_name: '',
      start_time: null,
      end_time: null,
    },

    existingOffers: [],
    now: new Date().toISOString().slice(0, 16),
    endTimeRules: [
      v => v > this.form.start_time || 'End time must be after start time'
    ],
    mcattype:'manual',
    imagePreview:null, nameError:'',

    backLoading:false, saveLoading:false
  }
},
watch: {
  conditions: {
    handler() {
      this.loadMatchedProducts();
    },
    deep: true
  },
  acondition() {
    this.loadMatchedProducts();
  }
},

computed:{
  msubcatnameRule() {
  return [
    v => !!v || 'Sub-Category is required',
    v => (v && v.length >= 3) || 'Name must be at least 3 characters',
    v => !this.msubcats.some(s =>
      s.msubcat_name.toLowerCase().trim() === (v || '').toLowerCase().trim() &&
      s.msubcat_id !== this.msubcatid
    ) || 'Sub-Category already exists'
  ]
},
  hasValidConditionRow () {
      return this.conditions.some(r =>
        r.tag && r.condition && (r.value !== '' && r.value !== null)
      )
    },
  saveDisabled() {
    const isNew = !this.msubcatid  
    let base = !this.form.mcat_id ||
               !this.form.subcatname.trim() ||
               !!this.nameError;
      
    if (isNew) {
      base = base || !this.form.image
    }

    if (this.mcattype === 'smart') {
      return base || !this.hasValidConditionRow
    }
    
  },
  indexedMatchedProducts() {
    return this.matchedProducts.map((item, idx) => ({
      ...item,
      index: idx + 1
    }));
  }
},

created(){
  Promise
    .all([ this.fetchCats(), this.fetchProducts(), this.fetchRuleMeta() ])
    .then(() => this.loadExisting())
    .catch(err => console.error(err))
    axios.get('/admin/msub-categories/vlist').then(res => {
    this.existingOffers = res.data.msubcats
      .map(s => s.offer_name?.toLowerCase().trim())
      .filter(Boolean);
  });
},

methods:{
  async fetchCats(){
    try{
      const [c,s] = await Promise.all([
        axios.get('/admin/mcategories/vlist'),
        axios.get('/admin/msub-categories/vlist')
      ])
      this.mcats=c.data.mcats
      this.msubcats=s.data.msubcats
    }catch(e){ console.error(e) }
  },
  resetSubcat(){ 
    this.form.subcatname=''; this.nameError='' 
  },
  async loadMatchedProducts() {
  try {
    const { data } = await axios.get('/admin/main/categories');

    if (!data || !data.categories) return;

    const subcatId = this.msubcatid; // msubcat_id from the route param or prop

    for (const cat of data.categories) {
      const matchSubcat = cat.subcategories.find(s => s.msubcat_id == subcatId);

      if (matchSubcat) {
        this.cdn = data.cdnURL || '';

        this.matchedProducts = (matchSubcat.products || []).map(p => {
          // Parse option_value if it's a JSON string
          if (typeof p.option_value === 'string') {
            try {
              p.option_value = JSON.parse(p.option_value);
            } catch (e) {
              p.option_value = {}; // fallback if parse fails
            }
          }
          return p;
        });

        break;
      }
    }
  } catch (e) {
    console.error('❌ Failed to load matched products:', e);
  }
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
  async fetchProducts(){
    const { data } = await axios.get('/admin/mcollproducts/vlist')
      this.allProducts = data.products
      this.allTypes  = data.types;
      this.allBrands = data.brands;
      this.allTags   = data.tags;
  },
  confirmProducts(){ 
    this.selectedProducts=[...this.productSelection]; this.productDialog=false 
  },
  removeProduct(id){
    this.selectedProducts=this.selectedProducts.filter(p=>p.mproduct_id!==id)
    this.productSelection=this.productSelection.filter(o=>(o.mproduct_id||o)!==id)
  },
  sortSelected(){
    if(this.sortMethod==='Product Title A-Z')
      this.selectedProducts.sort((a,b)=>a.mproduct_title.localeCompare(b.mproduct_title))
    if(this.sortMethod==='Product Title Z-A')
      this.selectedProducts.sort((a,b)=>b.mproduct_title.localeCompare(a.mproduct_title))
  },

  async fetchRuleMeta(){
    try{
      const {data}=await axios.get('/admin/querys/vlist')
      this.ruleColumns=data.fields; this.allQueries=data.querys
      this.fieldQueryMap=data.relations.reduce((m,r)=>{
        (m[r.field_id] ||= []).push(r.query_id); return m
      },{})
    }catch(e){ console.error(e) }
  },
  getRelations(field_id) {
  const queryIds = this.fieldQueryMap[field_id] || [];
  return this.allQueries.filter(q => queryIds.includes(q.query_id));
},
  updateRelations(idx){
    const allowed=this.fieldQueryMap[this.conditions[idx].tag]||[]
    this.conditions[idx].relations=this.allQueries.filter(q=>allowed.includes(q.query_id))
    this.conditions[idx].condition=''
  },
  addCondition(){ 
    this.conditions.push({tag:'',condition:'',value:'',relations:[]}) 
  },
  removeCondition(idx){ 
    this.conditions.splice(idx,1) 
  },

  onImageSelect(file){
    if(file){ this.form.image=file; this.imagePreview=URL.createObjectURL(file) }
    else this.clearImage()
  },
  clearImage(){ 
    this.form.image=null; this.imagePreview=null 
  },

  async loadExisting(){
    try{
      const {data}=await axios.get(`/admin/vsub-category/editdata/${this.msubcatid}`)
      const s=data.subcat

      this.form.mcat_id    = s.mcat_id
      this.form.subcatname = s.msubcat_name
      this.form.subcattag  = s.msubcat_tag
      this.form.offer_name = s.offer_name
      this.form.start_time = s.start_time
      this.form.end_time = s.end_time
      this.mcattype        = s.msubcat_type
      this.acondition        = s.condition_logic
      this.msubcat_publish = s.msubcat_publish
      if(s.msubcat_image) this.imagePreview=this.cdn+s.msubcat_image

      if(this.mcattype==='manual'){
        this.selectedProducts = (s.product_ids||[])
          .map(id => this.allProducts.find(p=>p.mproduct_id===id))
          .filter(Boolean)
        this.productSelection = [...this.selectedProducts]
      }else {
        this.conditions = (s.conditions || []).map(c => {
        const field = this.ruleColumns.find(f => f.field_id === c.field_id)?.field_name;
        let value = c.value;

        if (['Brand', 'Type', 'Tag'].includes(field)) {
          // Try to find full object from value
          if (field === 'Brand') {
            value = this.allBrands.find(b => b.mbrand_id == c.value);
          } else if (field === 'Type') {
            value = this.allTypes.find(t => t.mproduct_type_id == c.value);
          } else if (field === 'Tag') {
            value = this.allTags.find(t => t.mtag_id == c.value);
          }
        }

        return {
          tag       : c.field_id,
          condition : c.query_id,
          value     : value,
          relations : this.getRelations(c.field_id)
        };
      });
    }
  } catch (e) {
    console.error(e);
  }
},

  updateSubCategory(){
    this.saveLoading=true
    const fd=new FormData()
    fd.append("msubcat_publish", JSON.stringify(this.msubcat_publish ?? []));
    fd.append('msubcat_id', this.msubcatid);
    Object.entries(this.form).forEach(([k,v])=>fd.append(k,v??''))
    fd.append('mcattype', this.mcattype)

    if(this.mcattype==='smart'){
      fd.append('condition_logic', this.acondition)
      fd.append('conditions', JSON.stringify(
        this.conditions.map(c => {
          const field = this.ruleColumns.find(f => f.field_id === c.tag)?.field_name;
          let value = c.value;

          if (['Brand', 'Type', 'Tag'].includes(field)) {
            if (typeof value === 'object') {
              if (field === 'Brand') value = value.mbrand_id;
              if (field === 'Type') value = value.mproduct_type_id;
              if (field === 'Tag') value = value.mtag_id;
            }
          }

          return {
            field_id : c.tag,
            query_id : c.condition,
            value    : value ?? ''
          };
        })
      ));
    }else{
      const ids = this.selectedProducts.map(p => p.mproduct_id)
        fd.append('product_ids', JSON.stringify(ids))
    }
    if(this.form.image instanceof File) fd.append('image', this.form.image)

    axios.post(`/admin/msub-category/${this.msubcatid}/update`, fd)
      .then(()=>{
        this.$toast.success('Sub-Category updated successfully!');
        return this.loadMatchedProducts();
      })
      .finally(()=>this.saveLoading=false)
  },

  navigateBack(){
    if(this.backLoading) return
    this.backLoading=true
    setTimeout(()=>window.location.href='/admin/msub-categories/list',500)
  },
  async discard(){
    try{
      const {data}=await axios.get(`/admin/vsub-category/editdata/${this.msubcatid}`)
      const s=data.subcat

      this.form.mcat_id    = s.mcat_id
      this.form.subcatname = s.msubcat_name
      this.form.subcattag  = s.msubcat_tag
      this.form.offer_name = s.offer_name
      this.form.start_time = s.start_time
      this.form.end_time = s.end_time
      this.mcattype        = s.msubcat_type
      this.msubcat_publish = s.msubcat_publish
      if(s.msubcat_image) this.imagePreview=this.cdn+s.msubcat_image

      if(this.mcattype==='manual'){
        this.selectedProducts = (s.product_ids||[])
          .map(id => this.allProducts.find(p=>p.mproduct_id===id))
          .filter(Boolean)
        this.productSelection = [...this.selectedProducts]
      }else{
        this.conditions = (s.conditions||[]).map(c => ({
          tag       : c.field_id,
          condition : c.query_id,
          value     : c.value,
          relations : []
        }))
        if(!this.conditions.length)
          this.conditions = [{tag:'',condition:'',value:'',relations:[]}]
        this.acondition = s.condition_logic || 'all'
      }
    }catch(e){ console.error(e) }
  }
}
}
</script>

<style scoped>

</style>
