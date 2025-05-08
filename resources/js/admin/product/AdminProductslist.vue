<template>
    <div>
        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="msearch" clearable dense outlined prepend-inner-icon="mdi-magnify" placeholder="Search all products" hide-details></v-text-field>
            </v-col>
            <v-col cols="12" md="2">
                <v-autocomplete v-model="selectedType" :items="mptypes" item-text="mproduct_type_name" item-value="mproduct_type_id" 
                    dense hide-details outlined label="Type" clearable>
                </v-autocomplete>
            </v-col>
            <v-col cols="12" md="2">
                <v-autocomplete v-model="selectedBrand" :items="mbrands" item-text="mbrand_name" item-value="mbrand_id" dense hide-details outlined 
                    label="Brand" clearable>
                </v-autocomplete>
            </v-col>
            <v-col cols="12" md="2">
                <v-autocomplete v-model="selectedTags" :items="mtags" item-text="mtag_name" item-value="mtag_id" dense hide-details outlined label="Tags" 
                    clearable multiple small-chips deletable-chips>
                </v-autocomplete>
            </v-col>
            <v-col cols="12">
                <v-card outlined>
                    <v-tabs v-model="activeTab" class="mb-2" active-class="grey lighten-3" height="30">
                        <v-tab class="text-none">All</v-tab>
                        <v-tab class="text-none">Active</v-tab>
                        <v-tab class="text-none">Draft</v-tab>
                    </v-tabs>
                    <v-data-table  dense  :headers="mprosHeaders"  :items="filteredMpros"  :search="msearch">
                        <template v-slot:item.mproduct_image="{ item }">
                            <v-img :src="item.mproduct_image ? cdn + item.mproduct_image : ''" cover width="50" height="50" class="ma-1" 
                                style="border: 1px solid #e0e0e0; border-radius: 10px;">  
                                <template #placeholder>
                                    <div class="d-flex align-center justify-center fill-height">
                                        <v-icon color="grey">mdi-image</v-icon>
                                    </div>
                                </template>
                            </v-img>
                        </template>
                        <template v-slot:item.mproduct_title="{ item }">
                            <a :href="'/admin/product/' + item.mproduct_id" class="link-dark"> {{ item.mproduct_title }} </a>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <v-btn :color="item.status === 'Active' ? 'green lighten-2' : 'cyan lighten-5'" rounded x-small elevation="0" class="text-none">
                                {{ item.status }} 
                            </v-btn>
                        </template>
                        <template v-slot:item.minventory="{ item }">
                            <span>
                                <span :class="{ 'red--text': item.inventoryQty <= 0 }">{{ item.inventoryQty }} in stock</span>
                                <span v-if="item.hasOptions"> for {{ item.vCount }} variant{{ item.vCount > 1 ? 's' : '' }}</span>
                            </span>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
  
<script>
import axios from 'axios'
  
export default {
    name: 'AdminProductslist',
    data () {
        return {
            cdn: 'https://cdn.truewebpro.com/',
            activeTab: 0,
            msearch: '',
            mpros: [],
            mptypes: [],
            mbrands: [],
            mtags: [],
            selectedType: null,
            selectedBrand: null,
            selectedTags: [],
            mprosHeaders: [
                { text: 'Image', value: 'mproduct_image', class: 'grey lighten-3', width: '100px', sortable: false },
                { text: 'Product', value: 'mproduct_title', class: 'grey lighten-3', width: '40%'},
                { text: 'Status', value: 'status', class: 'grey lighten-3', width: '150px' },
                { text: 'Inventory', value: 'minventory', class: 'grey lighten-3', width: '150px', sortable: false },
                { text: 'Type', value: 'type_name', class: 'grey lighten-3', width: '150px' },
                { text: 'Brand', value: 'brand_name', class: 'grey lighten-3', width: '150px' }
            ]
        }
    },
    computed: {
        filteredMpros () {
            return this.mpros.filter(p => {
                if (this.selectedType && p.mproduct_type_id !== this.selectedType) {
                    return false
                }
                if (this.selectedBrand && p.mbrand_id !== this.selectedBrand) {
                    return false
                }
                if (this.selectedTags.length) {
                    for (const tg of this.selectedTags) {
                        if (!p.mtags.includes(tg)) return false
                    }
                }
                switch(this.activeTab) {
                    case 0:
                        return true
                    case 1:
                        return p.status === 'Active'
                    case 2:
                        return p.status === 'Draft'
                    default:
                        return true
                }
            })
        }
    },
    created () {
        this.getAllMpros()
    },
    methods: {
        getAllMpros () {
            axios.get('/admin/products/vlist')
            .then(resp => {
                const data = resp.data

                const usedTypeIds = new Set();
                const usedBrandIds = new Set();
                const usedTagIds = new Set();
    
                this.mpros = (data.mproducts || []).map(prod => {
                    const type = data.mptypes.find(t => t.mproduct_type_id === prod.mproduct_type_id)
                    const brand = data.mbrands.find(b => b.mbrand_id === prod.mbrand_id)
                    
                    prod.type_name = type ? type.mproduct_type_name : ''
                    prod.brand_name = brand ? brand.mbrand_name : ''

                    if(prod.mproduct_type_id){
                        usedTypeIds.add(prod.mproduct_type_id);
                    }
                    if(prod.mbrand_id){
                        usedBrandIds.add(prod.mbrand_id);
                    }
                    prod.tag_names = (prod.mtags || []).map(tagId => {
                        const foundTag = data.mtags.find(t => t.mtag_id === tagId);
                        if (foundTag) {
                            usedTagIds.add(foundTag.mtag_id);
                            return foundTag.mtag_name;
                        }
                        return null;
                    }).filter(Boolean).join(', ');
                    
                    const variants = prod.mvariants || [];

                    const hasOptions = variants.some(v => {
                    try {
                        const opts = JSON.parse(v.options);
                        return Array.isArray(opts) && opts.length > 0;
                    } catch (e) {
                        return false; // in case options is not valid JSON
                    }
                    });

                    const totalQty = variants.reduce((sum, v) => sum + (v.quantity || 0), 0);
                    const variantCount = variants.length;

                    prod.inventoryQty = totalQty;
                    prod.vCount = variantCount;
                    prod.hasOptions = hasOptions;

                    if (!prod.mproduct_image) {
                        prod.mproduct_image = ''
                    }
    
                    return prod
                });
                this.mptypes = (data.mptypes || []).filter(type => usedTypeIds.has(type.mproduct_type_id));
                this.mbrands = (data.mbrands || []).filter(brand => usedBrandIds.has(brand.mbrand_id));
                this.mtags = (data.mtags || []).filter(tag => usedTagIds.has(tag.mtag_id));
            })
            .catch(err => {
                console.error('Error loading products:', err)
            })
        }
    }
}
</script>