<template>
    <div class="page-margin-20-40 page-cat">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="11" class="p-0">
                    <h2 class="text-h6 mb-1">Categories</h2> 
                </v-col>

                <v-col cols="12" md="1" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
                        border: 1px solid #1976d2 !important;" @click="openDialog" > 
                        Add Category
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
        <v-row class="mt-0">
            <v-col cols="12">
                <v-card elevation="5">
                    <v-data-table v-model="selected" :headers="mcatsHeaders" :items="mcats" item-key="mcat_id" :show-select="true" :search="ssearch" 
                        :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}" >
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" 
                                    placeholder="Search Category"/>
                            </v-row>
                        </template>
                        <template #item.mcat_name="{ item }">
                            <span>{{ item.mcat_name }}</span>
                        </template>
                        <template #item.status="{ item }">
                            <v-switch v-model="item.status" :input-value="item.status === 1" @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
                        </template>
                        <template #header.actions1>
                            <div class="text-center">Action</div>
                        </template>
                        <template #item.actions1="{ item }">
                            <div class="text-center">
                                <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" style="cursor: pointer;">
                                    <v-icon small left>mdi-pencil</v-icon>
                                    Edit
                                </v-chip>
                            </div>
                        </template>
                        <template #header.actions2>
                            <div class="text-center">Action</div>
                        </template>
                        <template #item.actions2="{ item }">
                            <div class="text-center">
                                <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;">
                                    <v-icon small left>mdi-delete</v-icon>
                                    Delete
                                </v-chip>
                            </div>
                        </template>
                        <template #header.delete>
                            <div class="d-flex justify-end align-center">
                                <span v-if="!selected.length"></span>
                                <v-menu v-if="selected.length" offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <div class="d-flex align-center">
                                        <span class="mr-2 font-weight-medium text-caption">{{ selected.length }} selected</span>
                                        <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer;">mdi-dots-vertical</v-icon>
                                    </div>
                                </template>
                                <v-list dense>
                                    <v-list-item @click="confirmBulkDelete">
                                    <v-list-item-title>Delete</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                                </v-menu>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
      
        <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
            <v-card elevation="5">
                <v-card-title>
                    <span>{{ editedIndex === -1 ? 'Add Category' : 'Edit Category' }}</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="fsvalid" @submit.prevent="saveCategory">
                    <v-card-text>
                        <v-autocomplete v-model="defaultItem.main_mcat_id" :items="mainCategories" item-text="main_mcat_name" item-value="main_mcat_id" label="Main Category" :rules="[v => !!v || 'Main category is required']"/>
                        <v-text-field v-model="defaultItem.mcat_name" :rules="mcategorynameRule" label="Category Name"/>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :disabled="!fsvalid || submitting">
                            {{ editedIndex === -1 ? 'Add' : 'Update' }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this Category?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Bulk-delete confirmation -->
        <v-dialog v-model="bulkDeleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete <strong>{{ selected.length }}</strong> categories?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="bulkDeleteLoading" :disabled="bulkDeleteLoading" @click="performBulkDelete">Delete</v-btn>
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
                    { text: '', value: 'data-table-select' },
                    { text: 'Category name', value: 'mcat_name' },
                    { text: 'Main category name', value: 'mainmcat_name' },
                    { text: 'Status', value: 'status', sortable: false },
                    { text: 'Action', value: 'actions1', sortable: false },
                    { text: 'Action', value: 'actions2', sortable: false },
                    { text: '', value: 'delete', sortable: false, width: '130px' }
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
                this.mcats = res.data.mcats;
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
            async toggleStatus(item) {
                try {
                    await axios.post(`/admin/mcategories/status-toggle/${item.mcat_id}`, {
                        status: item.status
                    });
                    this.$toast?.success('Category Status updated', { timeout: 500 });
                } catch (error) {
                    console.error("Failed to toggle status", error);
                    this.$toast?.error('Failed to update status', { timeout: 500 });
                }
            }
        }
    };
</script>
      
<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
<style>
.page-cat .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>