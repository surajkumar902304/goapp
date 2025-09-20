<template>
<div class="page-margin-20-40">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Brands</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
            <v-btn small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; border: 1px solid #1976d2 !important;" @click="openDialog">
                Add Brand
            </v-btn>
        </v-col>
      </v-row>
    </v-container>
  
    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table v-model="selected" item-key="mbrand_id" :show-select="true" :items="mbrands" :headers="mbrandsHeaders" :search="ssearch" :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                    <template v-slot:top>
                        <v-row dense class="mx-1 pb-1">
                            <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Brands"/>
                        </v-row>
                    </template>
                    <template #item.mbrand_image="{ item }">
                        <img :src="cdn + item.mbrand_image || 'https://via.placeholder.com/50'" width="50" height="50" class="m-1"/>
                    </template>
                    <template #item.mbrand_name="{ item }">
                        <span>{{ item.mbrand_name }}</span>
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
                                        <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer;">
                                            mdi-dots-vertical
                                        </v-icon>
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
                <span>{{ editedIndex === -1 ? 'Add Brand' : 'Edit Brand' }}</span>
                <v-spacer></v-spacer>
                <v-icon @click="addSdialog = false">mdi-close</v-icon>
            </v-card-title>
            <v-form v-model="fsvalid" @submit.prevent="saveBrand">
                <v-card-text>
                    <v-text-field v-model="defaultItem.mbrand_name" :rules="mbrandnameRule" label="Brand Name"/>
                    <div class="d-flex flex-column align-center">
                        <v-card-actions class="pb-0 pt-0">
                            <span class="body-2 fw-semibold">{{ isImageSelected ? 'Selected Image' : 'Select Image' }}</span>
                        </v-card-actions>
                        <input ref="imageInput" type="file" accept="image/*" style="display:none" @change="handleImageUpload"/>
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
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :disabled="!fsvalid || submitting">
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
            <v-card-text>
                Are you sure you want to delete this Brand?<br />
                Deleting this brand will also permanently remove all products associated with it.
            </v-card-text>
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
            <v-card-text>
                Are you sure you want to delete <strong>{{ selected.length }}</strong> brands?<br />
                Deleting this brands will also permanently remove all products associated with it.
            </v-card-text>
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
    name: 'AdminBrandlist',
    data() {
        return {
            cdn: 'https://cdn.truewebpro.com/',
            ssearch: '',
            mbrands: [],
            mbrandsHeaders: [
                { text: '', value: 'data-table-select' },
                { text: 'Image', value: 'mbrand_image', sortable: false },
                { text: 'Name', value: 'mbrand_name', width: '375px' },
                { text: 'Action', value: 'actions1', sortable: false },
                { text: 'Action', value: 'actions2', sortable: false },
                { text: '', value: 'delete', sortable: false, width: '130px' }
            ],
            addSdialog: false,
            editedIndex: -1,
            fsvalid: false,
            submitting: false,
            defaultItem: {
                mbrand_id: null,
                mbrand_name: '',
                mbrand_image: ''
            },
            imagePreview: null,
            imageName: '',
            mbrandnameRule: [
                v => !!v || 'Brand Name is required',
                v => (v && v.length >= 3) || 'Name must be at least 3 characters',
                (v) =>
                    !this.mbrands.some(
                        (brand) =>
                        brand.mbrand_name === v &&
                        brand.mbrand_id !== this.defaultItem.mbrand_id
                    ) || "Brand already exists"
            ],
            deleteDialog: false,
            brandToDelete: null,
            deleteLoading: false,

            selected: [],         
            bulkDeleteDialog: false,
            bulkDeleteLoading: false,
        };
    },
    created() {
        this.getAllBrands();
    },
    watch: {
        addSdialog(val) {
            if (!val) this.submitting = false;
        }
    },
    computed: {
        isImageSelected () {
            return !!this.imageName;
        }
    },
    methods: {
        getAllBrands() {
            axios.get('/admin/mbrands/vlist').then(res => {
            this.mbrands = res.data.mbrands;
            });
        },
        openDialog() {
            this.defaultItem = { mbrand_id: null, mbrand_name: '', mbrand_image: '' };
            this.imagePreview = 'https://via.placeholder.com/150';
            this.imageName = '';
            this.editedIndex = -1;
            this.fsvalid = false;
            this.addSdialog = true;
        },
        editItem(item) {
            this.defaultItem = {
            mbrand_id: item.mbrand_id,
            mbrand_name: item.mbrand_name,
            mbrand_image: ''
            };
            this.imagePreview = item.image_url || (this.cdn + item.mbrand_image);
            this.imageName = item.mbrand_image ? item.mbrand_image.split('/').pop() : '';
            this.editedIndex = item.mbrand_id;
            this.fsvalid = true;
            this.addSdialog = true;
        },
        triggerFileInput() {
            this.$refs.imageInput.click();
        },
        handleImageUpload (e) {
            const file = e.target.files[0];
            if (file) {
                this.defaultItem.mbrand_image = file;
                this.imagePreview = URL.createObjectURL(file);
                this.imageName    = file.name;
            }
        },
        onDialogToggle(open) {
            if (!open) {
            this.defaultItem = { mbrand_id: null, mbrand_name: '', mbrand_image: '' };
            this.imagePreview = null;
            this.imageName = '';
            this.fsvalid = false;
            this.submitting = false;
            this.editedIndex = -1;
            }
        },
        saveBrand() {
            this.submitting = true;
    
            const fd = new FormData();
            fd.append('mbrand_name', this.defaultItem.mbrand_name);
    
            if (this.defaultItem.mbrand_image instanceof File) {
            fd.append('mbrand_image', this.defaultItem.mbrand_image);
            }
    
            if (this.editedIndex !== -1) fd.append('mbrand_id', this.editedIndex);
    
            const isNew = this.editedIndex === -1;
            const url = isNew ? '/admin/mbrands/add' : '/admin/mbrands/update';

            axios.post(url, fd, { 
                headers: { 'Content-Type': 'multipart/form-data' } 
            })
            .then(() => {
                this.getAllBrands();
                this.addSdialog = false;

                this.$toast.success(isNew ? 'Banner added successfully!' : 'Banner updated successfully!', {
                        timeout: 500
                    })
            })
            .catch(() => {
            this.$toast.error('Something went wrong while saving the brand.', {
                        timeout: 500
                    })
            })
            .finally(() => {
            this.submitting = false;
            });
        },
        confirmDelete(item) {
            this.brandToDelete = item;
            this.deleteDialog = true;
        },
        async performDelete() {
            if (!this.brandToDelete) return;
            this.deleteLoading = true;
            try {
            await axios.post('/admin/mbrand-delete', {
                mbrand_id: this.brandToDelete.mbrand_id
            });
            this.$toast?.success('Brand deleted successfully!', {
                        timeout: 500
                    })
            this.getAllBrands(); 
            } catch (err) {
                console.error(err);
            this.$toast?.error('Failed to delete product', {
                        timeout: 500
                    })
            } finally {
                this.deleteLoading = false;
                this.deleteDialog = false;
                this.brandToDelete = null;
            }
        },
        confirmBulkDelete() {
            this.bulkDeleteDialog = true;
        },

        async performBulkDelete() {
            if (!this.selected.length) return;
            this.bulkDeleteLoading = true;

            try {
                await axios.post('/admin/mbrands/bulk-delete', {
                mbrand_ids: this.selected.map((c) => c.mbrand_id),
                });

                this.$toast?.success('Selected categories deleted!', {
                    timeout: 500
                })
                this.selected = [];        
                this.getAllBrands();  
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
  
<style>
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