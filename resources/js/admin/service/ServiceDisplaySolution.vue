<template>
<div class="page-margin-20-40">
    <v-container fluid class="pt-0">
        <v-row class="mt-0 pt-0">
            <v-col cols="12" md="11" class="p-0">
                <h2 class="text-h6 mb-1">Services</h2> 
            </v-col>

            <v-col cols="12" md="1" class="p-0 ps-2 text-end">
                <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
                    border: 1px solid #1976d2 !important;" @click="openDialog">
                    Add Service
                </v-btn>
            </v-col>
        </v-row>
    </v-container>
  
    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table v-model="selected" item-key="service_solution_id" :show-select="true" :items="services" :headers="servicesHeaders" :search="ssearch" 
                    :footer-props="{'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                    <template v-slot:top>
                        <v-row dense class="mx-1 pb-1">
                            <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Title"/>
                        </v-row>
                    </template>
                    <template v-slot:item.service_solution_image="{ item }">
                        <v-img :src="item.service_solution_image ? cdn + item.service_solution_image : ''" cover width="50" height="50" class="ma-1" style="border: 1px solid #e0e0e0; border-radius: 10px;">
                            <template #placeholder>
                            <div class="d-flex align-center justify-center fill-height">
                                <v-icon color="grey">mdi-image</v-icon>
                            </div>
                            </template>
                        </v-img>
                    </template>
                    <template #item.service_solution_title="{ item }">
                        <span>{{ item.service_solution_title }}</span>
                    </template>
                    <template #header.actions1>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions1="{ item }">
                        <div class="text-center">
                            <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" style="cursor: pointer;">
                                <v-icon small left>mdi-pencil</v-icon>Edit
                            </v-chip>
                        </div>
                    </template>
                    <template #header.actions2>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions2="{ item }">
                        <div class="text-center">
                            <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;">
                                <v-icon small left>mdi-delete</v-icon>Delete
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
  
    <v-dialog v-model="addSdialog" max-width="600px" @update:model-value="onDialogToggle">
        <v-card elevation="5">
            <v-card-title>
                <span>{{ editedIndex === -1 ? 'Add Service' : 'Edit Service' }}</span>
                <v-spacer />
                <v-btn class="btn-32-text-12" icon @click="addSdialog = false"><v-icon>mdi-close</v-icon></v-btn>
            </v-card-title>
            <v-form v-model="fsvalid" @submit.prevent="saveService">
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="defaultItem.service_solution_title" :rules="servicetitleRule" label="Service Title" outlined dense required />
                            <v-text-field v-model="defaultItem.service_solution_sub_title" :rules="servicesubtitleRule" label="Service Sub-Title" outlined dense required />
                        </v-col>

                        <v-col cols="12" md="6" class="d-flex flex-column align-center">
                            <div class="text-center mb-2 uploader-label">
                                {{ isImageSelected ? 'Selected Image' : 'Select Image' }}
                            </div>
                            <input ref="imageInput" type="file" accept="image/*" style="display: none" @change="handleImageUpload" />
                            <div class="uploader-box mb-2" @click="$refs.imageInput.click()">
                                <v-img v-if="isImageSelected" :src="imagePreview" max-width="150" max-height="150" contain/>
                                <v-icon v-else size="48" class="grey--text text--lighten-1">mdi-image-area</v-icon>
                            </div>

                            <div v-if="imageName" class="text-caption">{{ imageName }}</div>
                        </v-col>

                        <div class="text-left uploader-label">Description </div>
                        <v-col cols="12">
                            <v-textarea v-model="defaultItem.service_solution_desc" placeholder="Enter Description" auto-grow outlined dense rows="4" 
                                class="service-desc-textarea" :rules="servicedescRule"/>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions>
                    <v-spacer />
                    <template v-if="editedIndex !== -1 || isImageSelected">
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :disabled="!fsvalid || submitting" >
                            {{ editedIndex === -1 ? 'Add Service' : 'Update Service' }}
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
            <v-card-text>Are you sure you want to delete this Service?<br /></v-card-text>
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
            <v-card-text>Are you sure you want to delete <strong>{{ selected.length }}</strong> services?<br /></v-card-text>
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
    name: 'ServiceDisplaySolution',
    data() {
        return {
            cdn: 'https://cdn.truewebpro.com/',
            ssearch: '',
            services: [],
            servicesHeaders: [
                { text: '', value: 'data-table-select', width: '10px' },
                { text: 'Image', value: 'service_solution_image', sortable: false },
                { text: 'Title', value: 'service_solution_title' },
                { text: 'Action', value: 'actions1', sortable: false },
                { text: 'Action', value: 'actions2', sortable: false },
                { text: '', value: 'delete', sortable: false, width: '130px' }
            ],
            addSdialog: false,
            editedIndex: -1,
            fsvalid: false,
            submitting: false,
            defaultItem: {
                service_solution_id: null,
                service_solution_title: '',
                service_solution_sub_title: '',
                service_solution_image: '',
                service_solution_desc: ''
            },
            imagePreview: null,
            imageName: '',
            servicetitleRule: [
                v => !!v || 'Title is required',
                v => (v && v.length >= 3) || 'Title must be at least 3 characters',
                v => (v && v.length <= 55) || 'Title max 55 characters',
                (v) =>
                    !this.services.some(
                        (service) =>
                        service.service_solution_title === v &&
                        service.service_solution_id !== this.defaultItem.service_solution_id
                    ) || "service already exists"
            ],
            servicesubtitleRule: [
                v => !!v || 'Sub Title is required',
                v => (v && v.length >= 3) || 'Sub Title must be at least 3 characters',
                v => (v && v.length <= 55) || 'Sub Title max 55 characters',
            ],
            servicedescRule: [
                v => !!v || 'Service Description is required',
                v => (v && v.length >= 3) || 'Description must be at least 3 characters',
                v => (v && v.length <= 255) || 'Description max 255 characters',
            ],
            deleteDialog: false,
            serviceToDelete: null,
            deleteLoading: false,

            selected: [],         
            bulkDeleteDialog: false,
            bulkDeleteLoading: false,
        };
    },
    created() {
        this.getAllServices();
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
        getAllServices() {
            axios.get('/admin/services/vlist').then(res => {
            this.services = res.data.services;
            });
        },
        openDialog() {
            this.defaultItem = { service_solution_id: null, service_solution_title: '', service_solution_sub_title:'', service_solution_image: '', service_solution_desc: '' };
            this.imagePreview = 'https://via.placeholder.com/150';
            this.imageName = '';
            this.editedIndex = -1;
            this.fsvalid = false;
            this.addSdialog = true;
        },
        editItem(item) {
            this.defaultItem = {
            service_solution_id: item.service_solution_id,
            service_solution_title: item.service_solution_title,
            service_solution_sub_title: item.service_solution_sub_title,
            service_solution_desc: item.service_solution_desc,
            service_solution_image: ''
            };
            this.imagePreview = item.image_url || (this.cdn + item.service_solution_image);
            this.imageName = item.service_solution_image ? item.service_solution_image.split('/').pop() : '';
            this.editedIndex = item.service_solution_id;
            this.fsvalid = true;
            this.addSdialog = true;
        },
        triggerFileInput() {
            this.$refs.imageInput.click();
        },
        handleImageUpload (e) {
            const file = e.target.files[0];
            if (file) {
                this.defaultItem.service_solution_image = file;
                this.imagePreview = URL.createObjectURL(file); 
                this.imageName    = file.name;
            }
        },
        onDialogToggle(open) {
            if (!open) {
            this.defaultItem = { service_solution_id: null, service_solution_title: '', service_solution_sub_title:'', service_solution_image: '', service_solution_desc: '' };
            this.imagePreview = null;
            this.imageName = '';
            this.fsvalid = false;
            this.submitting = false;
            this.editedIndex = -1;
            }
        },
        saveService() {
            this.submitting = true;
    
            const fd = new FormData();
            fd.append('service_solution_title', this.defaultItem.service_solution_title);
            fd.append('service_solution_sub_title', this.defaultItem.service_solution_sub_title);
            fd.append('service_solution_desc', this.defaultItem.service_solution_desc);
    
            if (this.defaultItem.service_solution_image instanceof File) {
            fd.append('service_solution_image', this.defaultItem.service_solution_image);
            }
    
            if (this.editedIndex !== -1) fd.append('service_solution_id', this.editedIndex);
    
            const isNew = this.editedIndex === -1;
            const url = isNew ? '/admin/services/add' : '/admin/services/update';

            axios.post(url, fd, { 
                headers: { 'Content-Type': 'multipart/form-data' } 
            })
            .then(() => {
                this.getAllServices();
                this.addSdialog = false;

                this.$toast.success(isNew ? 'Banner added successfully!' : 'Banner updated successfully!', {
                        timeout: 500
                    })
            })
            .catch(() => {
            this.$toast.error('Something went wrong while saving the service.', {
                        timeout: 500
                    })
            })
            .finally(() => {
            this.submitting = false;
            });
        },
        confirmDelete(item) {
            this.serviceToDelete = item;
            this.deleteDialog = true;
        },
        async performDelete() {
            if (!this.serviceToDelete) return;
            this.deleteLoading = true;
            try {
            await axios.post('/admin/service-delete', {
                service_solution_id: this.serviceToDelete.service_solution_id
            });
            this.$toast?.success('Service deleted successfully!', {
                        timeout: 500
                    })
            this.getAllServices(); 
            } catch (err) {
                console.error(err);
            this.$toast?.error('Failed to delete product', {
                        timeout: 500
                    })
            } finally {
                this.deleteLoading = false;
                this.deleteDialog = false;
                this.serviceToDelete = null;
            }
        },
        confirmBulkDelete() {
            this.bulkDeleteDialog = true;
        },
        async performBulkDelete() {
            if (!this.selected.length) return;
            this.bulkDeleteLoading = true;

            try {
                await axios.post('/admin/services/bulk-delete', {
                service_solution_ids: this.selected.map((c) => c.service_solution_id),
                });

                this.$toast?.success('Selected categories deleted!', {
                    timeout: 500
                })
                this.selected = [];        
                this.getAllServices();  
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