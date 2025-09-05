<template>
    <div class="page-margin-20-40">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="10" class="p-0">
                    <h2 class="text-h6 mb-1">Main Categories</h2> 
                </v-col>

                <v-col cols="12" md="2" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
                        border: 1px solid #1976d2 !important;" @click="openDialog">
                        Add Main Category
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <v-row class="mt-0">
            <v-col cols="12">
                <v-card elevation="5">
                    <v-simple-table style="border-radius: 5px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th :colspan="5" class="pa-2" style="background-color: white !important;">
                                    <v-text-field v-model="ssearch" class="ma-0" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" 
                                        placeholder="Search Main category name"/>
                                </th> 
                            </tr>
                            <tr style="background:#b6b6b6;">
                                <th style="text-align: center;">Position drag</th>
                                <th>Main category name</th>
                                <th>Status</th>
                                <th style="text-align: center;">Action</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>

                        <draggable tag="tbody" :list="mainmcats" handle=".drag-handle" @end="onDragEnd">
                            <tr v-for="item in mainmcatsFilter" :key="item.main_mcat_id">
                                <td class="drag-handle" style="cursor: grab; text-align: center; ">
                                    <v-icon small>mdi-drag</v-icon>
                                </td>
                                <td class="align-middle" style="font-size: 12px;">
                                    {{ item.main_mcat_name.toUpperCase() }}
                                </td>
                                <td>
                                    <v-switch v-model="item.status" :input-value="item.status === 1" @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
                                </td>
                                <td style="text-align: center;">
                                    <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" 
                                        style="cursor: pointer;">
                                        <v-icon small left>mdi-pencil</v-icon>
                                        Edit
                                    </v-chip>
                                </td>
                                <td style="text-align: center;">
                                    <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" 
                                        style="cursor: pointer;">
                                        <v-icon small left>mdi-delete</v-icon>
                                        Delete
                                    </v-chip>
                                </td>
                            </tr>
                        </draggable>
                    </v-simple-table>
                </v-card>
            </v-col>
        </v-row>
      
        <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
            <v-card elevation="5"> 
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
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fsvalid || submitting">
                            {{ editedIndex === -1 ? 'Add' : 'Update' }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this Main Category?</v-card-text>
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
import axios from 'axios';
import draggable  from 'vuedraggable'
  
export default {
    name: 'MainMcatlist',
    components : { draggable },
    data() {
        return {
            ssearch: '',
            mainmcats: [],
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
    computed : {
        mainmcatsFilter () {
            const term = (this.ssearch || '').toLowerCase()
            return this.mainmcats.filter(b =>
                (b.main_mcat_name || '').toLowerCase().includes(term)
            )
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
        async onDragEnd() {
            const payload = this.mainmcats.map((it, i) => ({
            id: it.main_mcat_id,
            position: i + 1,
            }));
            try {
            await axios.post('/admin/main-mcategories/reorder', payload);
            this.$toast.success('Order saved', {
                    timeout: 500
                })
            } catch (err) {
            console.error(err);
            this.$toast.error('Failed to save order', {
                    timeout: 500
                })
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
                , {
                    timeout: 500
                })
                this.getAllMainCategories();
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
            await axios.post('/admin/main-mcategory-delete', {
                main_mcat_id: this.categoryToDelete.main_mcat_id
            });
            this.$toast?.success('Main Category deleted successfully!', {
                    timeout: 500
                })
            this.getAllMainCategories(); 
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
        async toggleStatus(item) {
            try {
                await axios.post(`/admin/main-mcategories/status-toggle/${item.main_mcat_id}`, {
                    status: item.status
                });
                this.$toast?.success('Main Category Status updated', { timeout: 500 });
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