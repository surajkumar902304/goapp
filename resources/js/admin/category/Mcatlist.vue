<template>
    <div>
        <v-row>
            <v-col cols="12" md="10">
                <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify" placeholder="Search Category name"/>
            </v-col>
            <v-col cols="12" md="2" class="text-end">
                <v-btn color="secondary" small class="text-none font-weight-bold" @click="openDialog">
                    Add Category
                </v-btn>
            </v-col>
        </v-row>
      
        <v-row>
            <v-col cols="12">
                <v-card outlined>
                    <v-data-table :items="mcats" :headers="mcatsHeaders" :search="ssearch">
                        <template #item.mcat_name="{ item }">
                            <span>{{ item.mcat_name }}</span>
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
                    <span>{{ editedIndex === -1 ? 'Add Category' : 'Edit Category' }}</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="fsvalid" @submit.prevent="saveCategory">
                    <v-card-text>
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
                mcatsHeaders: [
                { text: 'ID', value: 'mcat_id' },
                { text: 'Category Name', value: 'mcat_name' },
                { text: 'Actions', value: 'actions', sortable: false }
                ],
                addSdialog: false,
                editedIndex: -1,
                fsvalid: false,
                submitting: false,
                defaultItem: {
                mcat_id: null,
                mcat_name: '',
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
                ]
            };
        },
        created() {
            this.getAllCategories();
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
        
        
                if (this.editedIndex !== -1) fd.append('mcat_id', this.editedIndex);
        
                const url = this.editedIndex === -1 ? '/admin/mcategory/add' : '/admin/mcategory/update';
        
                axios
                .post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    this.getAllCategories();
                    this.addSdialog = false;
                });
            }
        }
    };
    </script>
      
    <style scoped>

    </style>