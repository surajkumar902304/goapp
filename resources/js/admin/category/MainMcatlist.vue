<template>
    <div>
        <v-row>
            <v-col cols="12" md="10">
                <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify" placeholder="Search Main Category name"/>
            </v-col>
            <v-col cols="12" md="2" class="text-end mt-1">
                <v-btn color="secondary" small class="text-none font-weight-bold" @click="openDialog">
                    Add Main Category
                </v-btn>
            </v-col>
        </v-row>
      
        <v-row>
            <v-col cols="12">
                <v-card outlined>
                    <v-data-table :items="mainmcats" :headers="mainmcatsHeaders" :search="ssearch" :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                        <template #item.main_mcat_name="{ item }">
                            <span>{{ item.main_mcat_name }}</span>
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
                    <span>{{ editedIndex === -1 ? 'Add Main Category' : 'Edit Main Category' }}</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="fsvalid" @submit.prevent="saveCategory">
                    <v-card-text>
                        <v-text-field v-model="defaultItem.main_mcat_name" :rules="mcategorynameRule" label="Main Category Name"/>
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
                    Are you sure you want to delete this Main Category?
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
    import axios from 'axios';
      
    export default {
        name: 'MainMcatlist',
        data() {
            return {
                ssearch: '',
                mainmcats: [],
                mainmcatsHeaders: [
                { text: 'ID', value: 'main_mcat_id' },
                { text: 'Main Category Name', value: 'main_mcat_name' },
                { text: 'Actions', value: 'actions', sortable: false }
                ],
                addSdialog: false,
                editedIndex: -1,
                fsvalid: false,
                submitting: false,
                defaultItem: {
                main_mcat_id: null,
                main_mcat_name: '',
                },
                mcategorynameRule: [
                    v => !!v || 'Category Name is required',
                    v => (v && v.length >= 3) || 'Name must be at least 3 characters',
                    (v) =>
                        !this.mainmcats.some(
                            (category) =>
                            category.main_mcat_name === v &&
                            category.main_mcat_id !== this.defaultItem.main_mcat_id
                        ) || "Category already exists"
                ],
                deleteDialog: false,
                categoryToDelete: null,
                deleteLoading: false,
            };
        },
        created() {
            this.getAllMainCategories();
        },
        watch: {
            addSdialog(val) {
                if (!val) this.submitting = false;
            }
        },
        methods: {
            getAllMainCategories() {
                axios.get('/admin/main-mcategories/vlist').then(res => {
                this.mainmcats = res.data.mainmcats;
                });
            },
            openDialog() {
                this.defaultItem = { main_mcat_id: null, main_mcat_name: '' };
                this.editedIndex = -1;
                this.fsvalid = false;
                this.addSdialog = true;
            },
            editItem(item) {
                this.defaultItem = {
                    main_mcat_id: item.main_mcat_id,
                    main_mcat_name: item.main_mcat_name,
                };
                
                this.editedIndex = item.main_mcat_id;
                this.fsvalid = true;
                this.addSdialog = true;
            },
            
            onDialogToggle(open) {
                if (!open) {
                this.defaultItem = { main_mcat_id: null, main_mcat_name: '' };
                this.fsvalid = false;
                this.submitting = false;
                this.editedIndex = -1;
                }
            },
            saveCategory() {
                this.submitting = true;
        
                const fd = new FormData();
                fd.append('main_mcat_name', this.defaultItem.main_mcat_name);
        
        
                if (this.editedIndex !== -1) fd.append('main_mcat_id', this.editedIndex);
        
                const url = this.editedIndex === -1 ? '/admin/main-mcategory/add' : '/admin/main-mcategory/update';
        
                axios
                .post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    this.$toast.success(
                        this.editedIndex === -1 
                            ? 'Main category added successfully!' 
                            : 'Main category updated successfully!'
                    );
                    this.getAllMainCategories();
                    this.addSdialog = false;
                })
                .catch((error) => {
                    console.error(error);
                    this.$toast.error('Failed to save category. Please try again.');
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
                await axios.post('/admin/main-mcategory-delete', {
                    main_mcat_id: this.categoryToDelete.main_mcat_id
                });

                this.$toast?.success('Main Category deleted successfully!');
                this.getAllMainCategories(); 
                } catch (err) {
                    console.error(err);
                this.$toast?.error('Failed to delete product');
                } finally {
                    this.deleteLoading = false;
                    this.deleteDialog = false;
                    this.categoryToDelete = null;
                }
            }
        }
    };
    </script>
      
    <style scoped>

    </style>