<template>
    <div>
        <v-row>
            <h2 class="text-h6 mb-1">Categories</h2>
        </v-row>
        <v-row class="mt-0 pt-0">
            <v-col cols="12" md="10">
                <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Category name"/>
            </v-col>
            <v-col cols="12" md="1">
                <v-btn color="secondary" small class="text-none" style="height: 32px;" @click="openDialog" > 
                    Add Category
                </v-btn>
            </v-col>
            <v-col cols="12" md="1" v-if="selected.length" class="text-end">
                <v-icon color="red" class="text-none" style="height: 32px" :loading="bulkDeleteLoading" :disabled="bulkDeleteLoading" @click="confirmBulkDelete" >
                    mdi-delete
                </v-icon>
            </v-col>
        </v-row>
      
        <v-row class="mt-0">
            <v-col cols="12">
                <v-card outlined>
                    <v-data-table
                        v-model="selected"         
                        :show-select="true"       
                        item-key="mcat_id"
                        :items="mcats"
                        :headers="mcatsHeaders"
                        :search="ssearch"
                        
                        :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100],
                        'items-per-page-text': 'Rows per page:'
                        }"
                    >
                        <template v-slot:item.index="{ item }">
                            {{ item.index }}
                        </template>
                        <template #item.mcat_name="{ item }">
                            <span>{{ item.mcat_name }}</span>
                        </template>
                        <template #item.actions="{ item }">
                            <v-btn icon color="primary" @click="editItem(item)">
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-icon small color="red"  @click="confirmDelete(item)">
                                mdi-delete
                            </v-icon>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
      
        <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
            <v-card>
                <v-card-title>
                    <span>{{ editedIndex === -1 ? 'Add Category' : 'Edit Category' }}</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="fsvalid" @submit.prevent="saveCategory">
                    <v-card-text>
                        <v-autocomplete
                            v-model="defaultItem.main_mcat_id"
                            :items="mainCategories"
                            item-text="main_mcat_name"
                            item-value="main_mcat_id"
                            label="Main Category"
                            :rules="[v => !!v || 'Main category is required']"
                            />
                    
                        <v-text-field v-model="defaultItem.mcat_name" :rules="mcategorynameRule" label="Category Name"/>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn type="submit" color="success" small :disabled="!fsvalid || submitting">
                            {{ editedIndex === -1 ? 'Add' : 'Update' }}
                        </v-btn>
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

        <!-- Bulk-delete confirmation -->
        <v-dialog v-model="bulkDeleteDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>
                Are you sure you want to delete <strong>{{ selected.length }}</strong> categories?
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
                <v-btn
                    text
                    color="red"
                    :loading="bulkDeleteLoading"
                    :disabled="bulkDeleteLoading"
                    @click="performBulkDelete"
                >
                    Delete
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
    </template>
      
    <script>
    import axios from 'axios';
      
    export default {
        name: 'Mcatlist',
        data() {
            return {
                ssearch: '',
                mcats: [],
                mainCategories: [],
                mcatsHeaders: [
                    { text: 'Index', value: 'index', sortable: true },
                    { text: 'Category Name', value: 'mcat_name' },
                    { text: 'Main Category Name', value: 'mainmcat_name' },
                    { text: 'Actions', value: 'actions', sortable: false }
                ],
                addSdialog: false,
                editedIndex: -1,
                fsvalid: false,
                submitting: false,
                defaultItem: {
                    mcat_id: null,
                    mcat_name: '',
                    main_mcat_id: null,
                },
                mcategorynameRule: [
                    v => !!v || 'Category Name is required',
                    v => (v && v.length >= 3) || 'Name must be at least 3 characters',
                    (v) =>
                        !this.mcats.some(
                            (category) =>
                            category.mcat_name === v &&
                            category.mcat_id !== this.defaultItem.mcat_id
                        ) || "Category already exists"
                ],
                deleteDialog: false,
                categoryToDelete: null,
                deleteLoading: false,

                selected: [],         
                bulkDeleteDialog: false,
                bulkDeleteLoading: false,
            };
        },
        created() {
            this.getAllCategories();
            this.getMainCategories();
        },
        watch: {
            addSdialog(val) {
                if (!val) this.submitting = false;
            }
        },
        methods: {
            getAllCategories() {
                axios.get('/admin/mcategories/vlist').then(res => {
                this.mcats = res.data.mcats.map((cat, i) => ({
                    ...cat,
                    index: i + 1
                    }));
                });
            },
            getMainCategories() {
                axios.get('/admin/main-mcategories/vlist').then(res => {
                this.mainCategories = res.data.mainmcats;
                });
            },
            openDialog() {
                this.defaultItem = { mcat_id: null, mcat_name: '' };
                this.editedIndex = -1;
                this.fsvalid = false;
                this.addSdialog = true;
            },
            editItem(item) {
                this.defaultItem = {
                    mcat_id: item.mcat_id,
                    mcat_name: item.mcat_name,
                    main_mcat_id: item.main_mcat_id,
                };
                
                this.editedIndex = item.mcat_id;
                this.fsvalid = true;
                this.addSdialog = true;
            },
            
            onDialogToggle(open) {
                if (!open) {
                this.defaultItem = { mcat_id: null, mcat_name: '' };
                this.fsvalid = false;
                this.submitting = false;
                this.editedIndex = -1;
                }
            },
            saveCategory() {
                this.submitting = true;
        
                const fd = new FormData();
                fd.append('mcat_name', this.defaultItem.mcat_name);
                fd.append('main_mcat_id', this.defaultItem.main_mcat_id);
        
                if (this.editedIndex !== -1) fd.append('mcat_id', this.editedIndex);
                const url = this.editedIndex === -1 ? '/admin/mcategory/add' : '/admin/mcategory/update';
        
                axios
                .post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    this.$toast.success(
                        this.editedIndex === -1 
                            ? 'Category added successfully!' 
                            : 'Category updated successfully!'
                    , {
                        timeout: 500
                    })
                    this.getAllCategories();
                    this.addSdialog = false;
                })
                .catch((error) => {
                    console.error(error);
                    this.$toast.error('Failed to save category. Please try again.', {
                        timeout: 500
                    })
                })
                .finally(() => {
                    this.submitting = false;
                });
            },
            confirmDelete(item) {
                this.categoryToDelete = item;
                this.deleteDialog = true;
            },

            async performDelete() {
                if (!this.categoryToDelete) return;
                this.deleteLoading = true;

                try {
                await axios.post('/admin/mcategory-delete', {
                    mcat_id: this.categoryToDelete.mcat_id
                });

                this.$toast?.success('Category deleted successfully!', {
                        timeout: 500
                    })
                this.getAllCategories(); 
                } catch (err) {
                    console.error(err);
                this.$toast?.error('Failed to delete product', {
                        timeout: 500
                    })
                } finally {
                    this.deleteLoading = false;
                    this.deleteDialog = false;
                    this.categoryToDelete = null;
                }
            },
            confirmBulkDelete() {
                this.bulkDeleteDialog = true;
            },

            async performBulkDelete() {
                if (!this.selected.length) return;
                this.bulkDeleteLoading = true;

                try {
                    await axios.post('/admin/mcategories/bulk-delete', {
                    mcat_ids: this.selected.map((c) => c.mcat_id),
                    });

                    this.$toast?.success('Selected categories deleted!', {
                        timeout: 500
                    })
                    this.selected = [];        
                    this.getAllCategories();  
                } catch (err) {
                    console.error(err);
                    this.$toast?.error('Failed to delete selected categories.', {
                        timeout: 500
                    })
                } finally {
                    this.bulkDeleteLoading = false;
                    this.bulkDeleteDialog   = false;
                }
            },
        }
    };
    </script>
      
    <style scoped>
.v-input {
  font-size: 12px !important;
}
    </style>