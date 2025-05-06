<template>
<div>
    <v-row>
        <v-col cols="12" md="6">
          <h2 class="text-h6 fw-semibold">Brands</h2>
        </v-col>
        <v-col cols="12" md="5" class="text-end">
            <v-text-field v-model="ssearch" dense hide-details outlined prepend-inner-icon="mdi-magnify" placeholder="Search name"/>
        </v-col>
        <v-col cols="12" md="1" class="text-end">
            <v-btn color="secondary" small class="text-none font-weight-bold" @click="openDialog">
                Add Brand
            </v-btn>
        </v-col>
    </v-row>
  
    <v-row>
        <v-col cols="12">
            <v-card outlined>
                <v-data-table :items="mbrands" :headers="mbrandsHeaders" :search="ssearch">
                    <template #item.mbrand_image="{ item }">
                        <img :src="cdn + item.mbrand_image || 'https://via.placeholder.com/50'" width="50" />
                    </template>
                    <template #item.mbrand_name="{ item }">
                        <span>{{ item.mbrand_name }}</span>
                    </template>
                    <template #item.actions="{ item }">
                        <v-btn icon color="primary" @click="editItem(item)">
                            <v-icon small>mdi-pencil</v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
  
    <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
        <v-card>
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
                        <span class="body-2 fw-semibold">
                            {{ isImageSelected ? 'Selected Image' : 'Select Image' }}
                        </span>
                        </v-card-actions>
                        <input ref="imageInput" type="file" accept="image/*" style="display:none" @change="handleImageUpload"/>
                        <div class="uploader-box mb-2" @click="triggerFileInput">
                            <v-img v-if="isImageSelected" :src="imagePreview" class="rounded" max-width="150" max-height="150" cover/>

                            <v-icon v-else size="48" class="grey--text text--lighten-1">
                                mdi-image-area
                            </v-icon>
                        </div>
                        <div v-if="imageName" class="text-caption">{{ imageName }}</div>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <template v-if="editedIndex !== -1 || isImageSelected">
                        <v-btn type="submit" color="success" small :disabled="!fsvalid || submitting">
                            {{ editedIndex === -1 ? 'Add' : 'Update' }}
                        </v-btn>
                    </template>
                </v-card-actions>
            </v-form>
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
            { text: 'ID', value: 'mbrand_id' },
            { text: 'Image', value: 'mbrand_image' },
            { text: 'Name', value: 'mbrand_name' },
            { text: 'Actions', value: 'actions' }
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
            ]
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
                this.imagePreview = URL.createObjectURL(file);   // नया प्रीव्यू
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

                this.$toast.success(isNew ? 'Banner added successfully!' : 'Banner updated successfully!');
            })
            .catch(() => {
            this.$toast.error('Something went wrong while saving the brand.');
            })
            .finally(() => {
            this.submitting = false;
            });
        }
    }
};
</script>
  
<style scoped>
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
</style>