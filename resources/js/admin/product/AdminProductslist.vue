<template>
    <div class="page-margin-20-40">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="10" class="p-0">
                    <h2 class="text-h6 mb-1">Products</h2> 
                </v-col>

                <v-col cols="12" md="2" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small :to="'/admin/product/addview'" router class="text-none fw-bold btn-32-text-12" style="color: #1976d2; 
                        background-color: white !important; border: 1px solid #1976d2 !important;">
                        Add Product
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <v-row class="mt-0 pt-0">
            <v-col cols="12" class="mt-2">
                <v-card elevation="5" style="background-color: transparent;">
                    <v-row class="align-center">
                        <v-col class="pt-0">
                            <v-tabs v-model="activeTab" active-class="grey lighten-3" height="30" class="text-none">
                            <v-tab class="text-none" style="font-size: 12px;">All</v-tab>
                            <v-tab class="text-none" style="font-size: 12px;">Active</v-tab>
                            <v-tab class="text-none" style="font-size: 12px;">Draft</v-tab>
                            </v-tabs>
                        </v-col>
                        <v-col class="d-flex justify-end pt-0" cols="auto" v-if="selected.length > 0">
                            <v-menu offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <span class="mr-2 font-weight-medium text-caption">{{ selected.length }} selected</span>
                                    <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer; margin-right: 5px;">
                                        mdi-dots-vertical
                                    </v-icon>
                                </template>
                                <v-list dense>
                                    <v-list-item @click="openConfirmDialog('delete')">
                                        <v-list-item-title>Delete</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="openAddTagDialog">
                                        <v-list-item-title>Add Tags</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="openremoveTagDialog">
                                        <v-list-item-title>Remove Tags</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="openConfirmDialog('markActive')">
                                        <v-list-item-title>Mark as Active</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="openConfirmDialog('markDraft')">
                                        <v-list-item-title>Mark as Draft</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </v-col>
                    </v-row>

                    <v-data-table dense v-model="selected" :show-select="true" item-key="mproduct_id" :headers="mprosHeaders"  :items="filteredMpros"  :search="msearch" 
                        :footer-props="{'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-col cols="12" md="6" class="pa-0 pr-3">
                                    <v-text-field class="px-2 py-1" v-model="msearch" dense outlined clearable hide-details prepend-inner-icon="mdi-magnify mb-2" 
                                        placeholder="Search Products"/>
                                </v-col>
                                <v-col cols="6" md="2" class="pa-0 pr-3">
                                    <v-autocomplete class="px-2 py-1" v-model="selectedType" :items="mptypes" item-text="mproduct_type_name" item-value="mproduct_type_id" 
                                        dense outlined hide-details label="Type"/>
                                </v-col>
                                <v-col cols="6" md="2" class="pa-0 pr-3">
                                    <v-autocomplete class="px-2 py-1" v-model="selectedBrand" :items="mbrands" item-text="mbrand_name" item-value="mbrand_id" 
                                        dense hide-details outlined label="Brand" clearable/>
                                </v-col>
                                <v-col cols="6" md="2" class="pa-0 pr-3">
                                    <v-autocomplete class="px-2 py-1 tag-text-area" v-model="selectedTags" :items="mtags" item-text="mtag_name" item-value="mtag_id" dense hide-details outlined 
                                        label="Tags" clearable deletable-chips multiple small-chips/>
                                </v-col>
                            </v-row>
                        </template>
                        <template v-slot:item.mproduct_image="{ item }">
                            <v-img :src="item.mproduct_image ? cdn + item.mproduct_image : ''" cover width="50" height="50" class="ma-1" style="border: 1px solid #e0e0e0; 
                                border-radius: 10px;">  
                                <template #placeholder>
                                    <div class="d-flex align-center justify-center fill-height">
                                        <v-icon color="grey">mdi-image</v-icon>
                                    </div>
                                </template>
                            </v-img>
                        </template>
                        <template v-slot:item.mproduct_title="{ item }">
                            <router-link :to="{ name: 'edit-product', params: { mproid: item.mproduct_id } }" class="link-dark">
                                {{ item.mproduct_title }}
                            </router-link>
                        </template>
                        <template #header.status>
                            <div class="text-center">Status</div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <div class="text-center">
                                <v-chip :color="item.status === 'Active' ? 'green' : 'blue'" class="ma-1" outlined pill small>
                                    {{ item.status === 'Active' ? 'Active' : 'Draft' }}
                                </v-chip>
                            </div>
                        </template>
                        <template v-slot:item.minventory="{ item }">
                            <span>
                                <span :class="{ 'red--text': item.inventoryQty <= 0 }">{{ item.inventoryQty }} in stock</span>
                                <span v-if="item.hasOptions"> for {{ item.vCount }} variant{{ item.vCount > 1 ? 's' : '' }}</span>
                            </span>
                        </template>
                        <template #header.type_name>
                            <div class="text-center">Type</div>
                        </template>
                        <template v-slot:item.type_name="{ item}">
                            <div class="text-center">
                                {{ item.type_name }}
                            </div>
                        </template>
                        <template #header.brand_name>
                            <div class="text-center">Brand</div>
                        </template>
                        <template v-slot:item.brand_name="{ item}">
                            <div class="text-center">
                                {{ item.brand_name }}
                            </div>
                        </template>
                        <template #header.actions>
                            <div class="text-center">Actions</div>
                        </template>
                        <template #item.actions="{ item }">
                            <div class="text-center">
                                <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;">
                                    <v-icon small left>mdi-delete</v-icon>
                                    Delete
                                </v-chip>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>

        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this Product?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text color="grey" class="btn-32-text-12" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn text color="red" class="btn-32-text-12" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Bulk-delete confirmation -->
        <v-dialog v-model="confirmDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm {{ actionLabel }}</v-card-title>
                <v-card-text>
                    Are you sure you want to <strong>{{ actionLabel.toLowerCase() }}</strong> 
                    <strong>{{ selected.length }}</strong> selected products?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text color="grey" class="btn-32-text-12" @click="confirmDialog = false">Cancel</v-btn>
                    <v-btn text color="red" class="btn-32-text-12" @click="executeBulkAction">Yes</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Add tag -->
        <v-dialog v-model="addTagDialog" max-width="500">
            <v-card elevation="5" class="d-flex flex-column justify-space-between" style="min-height: 340px;">
                <v-card-title class="text-h6">Add Tags</v-card-title>
                <v-card-text>
                    <v-autocomplete ref="tagsAutocomplete" v-model="tagsToAdd" :items="mtags" item-text="mtag_name" item-value="mtag_id" label="Select Tags" multiple 
                        small-chips deletable-chips clearable outlined dense :search-input.sync="typedTag" :filter="tagFilter" style="height: 32px; font-size: 12px !important;">
                        <template v-slot:no-data>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-btn text small class="btn-32-text-12" @click="addNewTag" :disabled="!typedTag?.trim()">
                                        Add “{{ typedTag }}”
                                    </v-btn>
                                </v-list-item-content>
                            </v-list-item>
                        </template>
                    </v-autocomplete>
                </v-card-text>
                <v-spacer></v-spacer>
                <v-card-actions>
                    <v-spacer />
                    <v-btn text @click="addTagDialog = false" class="btn-32-text-12">Cancel</v-btn>
                    <v-btn color="primary" @click="submitAddTags" class="btn-32-text-12">Add</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Remove tag -->
        <v-dialog v-model="removeTagDialog" max-width="500">
            <v-card elevation="5" class="d-flex flex-column justify-space-between" style="min-height: 340px;">
                <v-card-title class="text-h6">Remove Tags</v-card-title>
                <v-card-text>
                    <v-autocomplete v-model="tagsToRemove" :items="mtags" item-text="mtag_name" item-value="mtag_id" label="Select Tags" multiple small-chips 
                        deletable-chips clearable outlined dense style="height: 38px;">
                    </v-autocomplete>
                </v-card-text>
                <v-spacer></v-spacer>
                <v-card-actions>
                    <v-spacer />
                    <v-btn text class="btn-32-text-12" @click="removeTagDialog = false">Cancel</v-btn>
                    <v-btn color="primary" class="btn-32-text-12" @click="submitRemoveTags">Remove</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
                { text: 'Image', value: 'mproduct_image', sortable: false },
                { text: 'Product', value: 'mproduct_title'},
                { text: 'Status', value: 'status', sortable: false },
                { text: 'Inventory', value: 'minventory', sortable: false },
                { text: 'Type', value: 'type_name', sortable: false },
                { text: 'Brand', value: 'brand_name', sortable: false },
                { text: 'Actions', value: 'actions', sortable: false }
            ],
            deleteDialog: false,
            productToDelete: null,
            deleteLoading: false,

            bulkDeleteDialog: false,
            bulkDeleteLoading: false,

            selected: [],
            actionToConfirm: '',
            confirmDialog: false,
            addTagDialog: false,
            removeTagDialog: false,
            tagsToAdd: [],
            tagsToRemove: [],
            mtags: [],
            typedTag: "",
            actionLabel: '',
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
                        return false;
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
        },
        confirmDelete(item) {
            this.productToDelete = item;
            this.deleteDialog = true;
        },

        async performDelete() {
            if (!this.productToDelete) return;
            this.deleteLoading = true;

            try {
            await axios.post('/admin/product-delete', {
                mproduct_id: this.productToDelete.mproduct_id
            });

            this.$toast?.success('Product deleted successfully!', {
                        timeout: 500
                    })
            this.getAllMpros();
            } catch (err) {
                console.error(err);
            this.$toast?.error('Failed to delete product', {
                        timeout: 500
                    })
            } finally {
                this.deleteLoading = false;
                this.deleteDialog = false;
                this.productToDelete = null;
            }
        },
        openAddTagDialog() {
            this.addTagDialog = true;
            this.tagsToAdd = [];
            this.fetchTags();
        },
        tagFilter(item, queryText, itemText) {
            this.typedTag = queryText;
            return itemText.toLowerCase().includes(queryText.toLowerCase());
        },
        async addNewTag() {
            const newName = this.typedTag?.trim();
            if (!newName) return;

            const alreadyExists = this.mtags.some(
                (tag) => tag.mtag_name.toLowerCase() === newName.toLowerCase()
            );
            if (alreadyExists) {
                this.typedTag = "";
                if (this.$refs.tagsAutocomplete) {
                this.$refs.tagsAutocomplete.internalSearch = "";
                }
                return;
            }

            try {
                const response = await axios.post("/admin/mtags", {
                mtag_name: newName,
                });

                const newId = response.data.mtag_id;

                this.mtags.push({
                mtag_id: newId,
                mtag_name: newName,
                });

                this.tagsToAdd.push(newId);
                this.fetchTags();
                this.typedTag = "";
                if (this.$refs.tagsAutocomplete) {
                this.$refs.tagsAutocomplete.internalSearch = "";
                }
            } catch (err) {
                console.error("Error adding tag:", err);
                this.$toast?.error("Failed to add tag");
            }
        },
        openremoveTagDialog() {
            this.removeTagDialog = true;
            this.tagsToRemove = [];
            this.fetchTags();
        },
        fetchTags() {
            axios.get('/admin/mtags/vlist').then(res => {
            this.mtags = res.data?.mtags || [];
            }).catch(err => {
            this.$toast?.error("Failed to load tags", {
                        timeout: 500
                    })
            });
        },
        openConfirmDialog(action) {
            this.actionToConfirm = action;
            this.actionLabel = {
            delete: 'Delete',
            markActive: 'Mark as Active',
            markDraft: 'Mark as Draft'
            }[action] || '';
            this.confirmDialog = true;
        },
        async openAddTagDialog() {
            this.actionToConfirm = 'addTag';
            this.addTagDialog = true;
            this.tagsToAdd = [];

            const res = await axios.get('/admin/mtags/vlist');
            this.mtags = res.data?.mtags || [];
        },
        async openremoveTagDialog() {
            this.actionToConfirm = 'removeTag';
            this.removeTagDialog = true;
            this.tagsToRemove = [];

            const res = await axios.get('/admin/mtags/vlist');
            this.mtags = res.data?.mtags || [];
        },
        async executeBulkAction() {
            const ids = this.selected.map(p => p.mproduct_id);
            let url = '';
            let payload = {};

            switch (this.actionToConfirm) {
            case 'delete':
                url = '/admin/products-bulk/delete';
                payload = { product_ids: ids };
                break;
            case 'markActive':
            case 'markDraft':
                url = '/admin/products-bulk/mark-status';
                payload = {
                product_ids: ids,
                bulkstatus: this.actionToConfirm === 'markActive' ? 'Active' : 'Draft'
                };
                break;
            }

            try {
            await axios.post(url, payload);
            this.$toast?.success(`${this.actionLabel} successful`, {
                        timeout: 500
                    })
            this.getAllMpros();
            } catch (err) {
            this.$toast?.error(`Failed to ${this.actionLabel.toLowerCase()}`, {
                        timeout: 500
                    })
            } finally {
            this.confirmDialog = false;
            this.selected = [];
            }
        },
        async submitAddTags() {
            if (!this.tagsToAdd.length) return;

            try {
            await axios.post('/admin/products-bulk/add-tags', {
                product_ids: this.selected.map(p => p.mproduct_id),
                tag_ids: this.tagsToAdd
            });
            this.$toast?.success('Tags added successfully!', {
                        timeout: 500
                    })
            this.addTagDialog = false;
            this.selected = [];         
            this.tagsToAdd = [];
            this.getAllMpros();
            } catch (err) {
            this.$toast?.error('Failed to add tags', {
                        timeout: 500
                    })
            }
        },
        async submitRemoveTags() {
            if (!this.tagsToRemove.length) return;

            try {
                await axios.post('/admin/products-bulk/remove-tags', {
                product_ids: this.selected.map(p => p.mproduct_id),
                tag_ids: this.tagsToRemove
                });
                this.$toast?.success('Tags removed successfully!', { timeout: 500 });
                this.removeTagDialog = false;
                this.selected = [];
                this.tagsToRemove = [];
                this.getAllMpros();
            } catch (err) {
                this.$toast?.error('Failed to remove tags', { timeout: 500 });
            }
        }
    }
}
</script>
<style>
.v-input {
    font-size: 12px !important;
}

.v-input__control {
    display: flex;
    flex-wrap: wrap !important;
    align-items: flex-start !important;
    padding-top: 4px;
    padding-bottom: 4px;
    min-height: 40px !important;
    height: auto !important;
}

.v-select__selections {
    display: flex;
    flex-wrap: wrap !important; 
    align-items: flex-start !important;
    min-height: 32px !important;
    height: auto !important;
}

.v-chip {
    margin: 4px 6px 0 0 !important;
    font-size: 12px !important;
}

.v-chip__close {
    font-size: 14px !important;
    margin-left: 4px;
    color: #555;
}

.v-input input {
    font-size: 12px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    height: 32px !important;
    line-height: 32px !important;
    margin: 0 !important;
    align-self: center !important;
}

.v-label {
    display: flex !important;
    align-items: center !important;
    font-size: 12px !important;
    height: 100% !important;
    padding-bottom: 20px !important;
}

.v-input__append-inner {
    margin-top: 4px !important;
}

/* Custom css for all page */
thead tr th {
    background-color: #e8e8e8;
}
.page-margin-20-40{
    /* margin: 20px 40px !important; */
}
.btn-32-text-12{
    height: 26px !important;
    font-size: 12px !important;
}
.v-tabs .v-item-group {
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    overflow: hidden;
}
.v-tabs .v-tabs-bar__content {
    border-bottom: 1px solid #dbdbdb;
}
.mdi-checkbox-blank-outline::before,
.v-data-table .v-icon.v-icon {
    font-size: 20px;
}
.v-input--selection-controls__ripple {
    height: 12px;
    width: 12px;
    left: 50%;
    top: 50%;
    margin: 0;
    transform: translate(-50%, -50%);
    border-radius: 0;
}
.mdi-checkbox-marked::before {
    font-size: 20px;
}
</style>
