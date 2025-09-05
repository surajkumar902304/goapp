<template>
    <div class="page-margin-20-40">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="10" class="p-0">
                    <h2 class="text-h6 mb-1">Sub-Categories</h2>
                </v-col>

                <v-col cols="12" md="2" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small :to="'/admin/sub-categories/addview'" router
                        class="text-none btn-32-text-12" style="font-weight: bold; color: #1976d2; 
            background-color: white !important; border: 1px solid #1976d2 !important;">
                        Add Sub-Category
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <v-row class="mt-0">
            <v-col cols="12">
                <v-card elevation="5">
                    <v-data-table v-model="selected" :show-select="true" item-key="msubcat_id" :items="subcats"
                        :headers="msubcatsHeaders" :search="ssearch" :footer-props="{
                            'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'
                        }">
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-col cols="12" md="8" class="pa-0 pr-3">
                                    <v-text-field class="m-2" v-model="ssearch" dense outlined clearable hide-details
                                        prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Sub-Category" />
                                </v-col>
                                <v-col cols="6" md="2" class="pa-0 pr-3">
                                    <v-autocomplete class="m-2" v-model="publishFilter"
                                        :items="[{ text: 'All', value: null }, { text: 'Online Store', value: 'Online Store' }, { text: 'Other', value: 'Other' }]"
                                        item-text="text" item-value="value" outlined dense label="Publishing"
                                        hide-details @change="publishByStatus" />
                                </v-col>
                                <v-col cols="6" md="2" class="pa-0">
                                    <v-autocomplete class="m-2" v-model="typeFilter"
                                        :items="[{ text: 'All', value: null }, { text: 'Smart', value: 'smart' }, { text: 'Manual', value: 'manual' }]"
                                        item-text="text" item-value="value" outlined dense label="Type" hide-details
                                        @change="typeByStatus" />
                                </v-col>
                            </v-row>
                        </template>
                        <template #item.msubcat_image="{ item }">
                            <img :src="cdn + item.msubcat_image || 'https://via.placeholder.com/50'" width="50"
                                height="50" class="m-1" />
                        </template>
                        <template v-slot:item.msubcat_name="{ item }">
                            <router-link :to="{ name: 'edit-subcat', params: { msubcatid: item.msubcat_id } }"
                                class="link-dark">
                                {{ item.msubcat_name }}
                            </router-link>
                        </template>
                        <template #item.mcat_name="{ item }">
                            {{ item.category?.mcat_name || '—' }}
                        </template>
                        <template #header.msubcat_type>
                            <div class="text-center">Collection type</div>
                        </template>
                        <template v-slot:item.msubcat_type="{ item }">
                            <div class="text-center">
                                {{ item.msubcat_type }}
                            </div>
                        </template>
                        <template #item.status="{ item }">
                            <v-switch v-model="item.status" :input-value="item.status === 1"
                                @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
                        </template>
                        <template #header.actions>
                            <div class="text-center">Action</div>
                        </template>
                        <template #item.actions="{ item }">
                            <div class="text-center">
                                <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)"
                                    style="cursor: pointer;">
                                    <v-icon small left>mdi-delete</v-icon>Delete</v-chip>
                            </div>
                        </template>
                        <template #header.delete>
                            <div class="d-flex justify-end align-center">
                                <span v-if="!selected.length"></span>
                                <v-menu v-if="selected.length" offset-y>
                                    <template v-slot:activator="{ on, attrs }">
                                        <div class="d-flex align-center">
                                            <span class="mr-2 font-weight-medium text-caption">{{ selected.length }}
                                                selected</span>
                                            <v-icon color="primary" v-bind="attrs" v-on="on"
                                                style="cursor: pointer;">mdi-dots-vertical</v-icon>
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

        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this Sub-Category?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading"
                        @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Bulk-delete confirmation -->
        <v-dialog v-model="bulkDeleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete <strong>{{ selected.length }}</strong>
                    sub-categories?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="bulkDeleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="bulkDeleteLoading"
                        :disabled="bulkDeleteLoading" @click="performBulkDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Msubcatlist',
    data() {
        return {
            cdn: 'https://cdn.truewebpro.com/',
            ssearch: '',
            msubcats: [],
            subcats: [],
            mcats: [],
            publishFilter: null,
            typeFilter: null,

            msubcatsHeaders: [
                { text: '', value: 'data-table-select' },
                { text: 'Image', value: 'msubcat_image', sortable: false },
                { text: 'Sub-category name', value: 'msubcat_name' },
                { text: 'Category name', value: 'mcat_name', sortable: false },
                { text: 'Collection type', value: 'msubcat_type', sortable: false },
                { text: 'Status', value: 'status', sortable: false },
                { text: 'Actions', value: 'actions', sortable: false },
                { text: '', value: 'delete', sortable: false, width: '130px' }
            ],
            deleteDialog: false,
            subcategoryToDelete: null,
            deleteLoading: false,

            selected: [],
            bulkDeleteDialog: false,
            bulkDeleteLoading: false,
        };
    },

    created() {
        this.getAllSubCategories();
        this.getAllCategories();
    },
    methods: {
        getAllSubCategories() {
            axios.get('/admin/msub-categories/vlist').then(res => {
                this.msubcats = res.data.msubcats;
                this.subcats = res.data.msubcats;
            });
        },
        getAllCategories() {
            axios.get('/admin/mcategories/vlist').then(res => {
                this.mcats = res.data.mcats;
            });
        },
        publishByStatus() {
            if (this.publishFilter === null) {
                this.subcats = this.msubcats;
            } else {
                this.subcats = this.msubcats.filter(subcat =>
                    Array.isArray(subcat.msubcat_publish) &&
                    subcat.msubcat_publish.includes(this.publishFilter)
                );
            }
        },
        typeByStatus() {
            if (this.typeFilter === null) {
                this.subcats = this.msubcats;
            } else {
                this.subcats = this.msubcats.filter(subcat => subcat.msubcat_type === this.typeFilter);
            }
        },
        confirmDelete(item) {
            this.subcategoryToDelete = item;
            this.deleteDialog = true;
        },

        async performDelete() {
            if (!this.subcategoryToDelete) return;
            this.deleteLoading = true;
            try {
                await axios.post('/admin/msub-category-delete', {
                    msubcat_id: this.subcategoryToDelete.msubcat_id
                });
                this.$toast?.success('Sub-Category deleted successfully!', {
                    timeout: 500
                })
                this.getAllSubCategories();
            } catch (err) {
                console.error(err);
                this.$toast?.error('Failed to delete product', {
                    timeout: 500
                })
            } finally {
                this.deleteLoading = false;
                this.deleteDialog = false;
                this.subcategoryToDelete = null;
            }
        },
        confirmBulkDelete() {
            this.bulkDeleteDialog = true;
        },
        async performBulkDelete() {
            if (!this.selected.length) return;
            this.bulkDeleteLoading = true;
            try {
                await axios.post('/admin/msub-categories/bulk-delete', {
                    msubcat_ids: this.selected.map((c) => c.msubcat_id),
                });
                this.$toast?.success('Selected sub-categories deleted!', {
                    timeout: 500
                })
                this.selected = [];
                this.getAllSubCategories();
            } catch (err) {
                console.error(err);
                this.$toast?.error('Failed to delete selected sub-categories.', {
                    timeout: 500
                })
            } finally {
                this.bulkDeleteLoading = false;
                this.bulkDeleteDialog = false;
            }
        },
        async toggleStatus(item) {
            try {
                await axios.post(`/admin/msub-category/status-toggle/${item.msubcat_id}`, {
                    status: item.status
                });
                this.$toast?.success('Sub Category Status updated', { timeout: 500 });
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

.v-data-table .v-simple-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    transform: scale(0.7);
}
</style>
<style>
.v-input--switch {
    transform: none !important;
    margin: 0!important;
    padding: 0!important;
}

.v-input--switch .v-input__control {
    min-height: auto!important;
}

.v-input--switch .v-input__slot {
    justify-content: flex-start;
    width: auto;
    min-height: auto!important;
    margin-bottom: 0!important;
    height: 23px!important;
}

.v-input--switch input {
    height: 26px!important;
}

.v-input--switch .v-input--selection-controls__input {
    transform: scale(0.75);
    margin-right: 0;
}

.v-input--switch .v-input--selection-controls__ripple,
.v-input--switch .v-messages {
    display: none;
}
</style>