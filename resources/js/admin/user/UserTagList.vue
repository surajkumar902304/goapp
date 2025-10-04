<template>
    <div class="page-margin-20-40 user-tag-list">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="11" class="p-0">
                    <h2 class="text-h6 mb-1">Customer Tags</h2>
                </v-col>

                <v-col cols="12" md="1" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small class="text-none w-100 btn-32-text-12"
                        style="color: #1976d2; font-weight: bold; background-color: white !important; border: 1px solid #1976d2 !important;"
                        @click="openDialog">
                        Add Tag
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <v-row class="mt-0">
            <v-col cols="12">
                <v-card elevation="5">
                    <v-data-table item-key="mbrand_id" :items="userTags" :headers="userTagsHeaders" :search="ssearch"
                        :footer-props="{ 'items-per-page-options': [10, 25, 50], 'items-per-page-text': 'Rows per page:' }">
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details
                                    prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Customer Tag" />
                            </v-row>
                        </template>
                        <!-- <template #item.user_tag_name="{ item }">
                            <span v-if="(item.type || '').toLowerCase() !== 'custom'">
                                {{ item.user_tag_name }}
                            </span>
                            <router-link v-else
                                :to="{ name: 'user-tag-price', params: { usertagid: item.user_tag_id } }"
                                class="link-dark" title="Set custom tag prices">
                                {{ item.user_tag_name }}
                            </router-link>
                        </template> -->
                        <template #item.is_active="{ item }">
                            <v-switch v-model="item.is_active" :input-value="item.is_active === 1"
                                @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
                        </template>
                        <template #header.actions1>
                            <div class="text-center">Action</div>
                        </template>
                        <template #item.actions1="{ item }">
                            <div class="text-center">
                                <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)"
                                    style="cursor: pointer;">
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
                                <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)"
                                    style="cursor: pointer;">
                                    <v-icon small left>mdi-delete</v-icon>
                                    Delete
                                </v-chip>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="addSdialog" max-width="400" @update:model-value="onDialogToggle">
            <v-card elevation="5">
                <v-card-title>
                    <span>Add Tag</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>

                <v-form v-model="fsvalid" @submit.prevent="saveUserTag">
                    <v-card-text>
                        <v-text-field v-model="defaultItem.user_tag_name"
                            @input="defaultItem.user_tag_name = defaultItem.user_tag_name.toUpperCase()"
                            :rules="tagnameRule" label="Tag Name" />
                        <v-select v-model="defaultItem.type" :items="['custom', 'percentage']" label="Type"
                            :rules="[v => !!v || 'Type is required']" />

                        <v-text-field v-if="defaultItem.type === 'percentage'" v-model="defaultItem.discount" :rules="[
                            v => defaultItem.type !== 'percentage' || (!!v && v.toString().trim() !== '') || 'Discount is required.',
                            v => defaultItem.type !== 'percentage' || (/^[0-9]+$/.test(v)) || 'Only whole numbers allowed.',
                            v => defaultItem.type !== 'percentage' || (parseInt(v) >= 1 && parseInt(v) <= 100) || 'Value must be between 1 and 100.'
                        ]" label="Discount (%)" type="number" />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit"
                            style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
                            small :disabled="!fsvalid || submitting">
                            Add
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="editDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title>
                    <span>Edit Tag</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="editDialog = false">mdi-close</v-icon>
                </v-card-title>

                <v-form v-model="editValid" @submit.prevent="submitEditTag">
                    <v-card-text>
                        <v-text-field v-model="editItemData.user_tag_name" label="Tag Name"
                            @input="editItemData.user_tag_name = editItemData.user_tag_name.toUpperCase()"
                            :rules="tagnameRule" />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit"
                            style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
                            :disabled="!editValid || submitting" small>
                            Update
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>


        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete this Tag?<br />
                    Deleting this tag will also permanently remove all products price for a tag.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading"
                        @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserTagList',
    data() {
        return {
            ssearch: '',
            userTags: [],
            userTagsHeaders: [
                { text: 'Tag name', value: 'user_tag_name' },
                { text: 'Type', value: 'type' },
                { text: 'Discount (%)', value: 'discount' },
                { text: 'Status', value: 'is_active' },
                { text: 'Action', value: 'actions1', sortable: false },
                { text: 'Action', value: 'actions2', sortable: false },
            ],
            addSdialog: false,
            editedIndex: -1,
            fsvalid: false,
            submitting: false,
            defaultItem: {
                user_tag_id: null,
                user_tag_name: '',
                type: '',
                discount: null
            },
            editDialog: false,
            editValid: false,
            submitting: false,
            editItemData: {
                user_tag_id: null,
                user_tag_name: '',
            },
            tagnameRule: [
                v => !!v || 'Tag Name is required',
                v => (v && v.length >= 3) || 'Name must be at least 3 characters',
                (v) =>
                    !this.userTags.some(
                        (tag) =>
                            tag.user_tag_name === v &&
                            tag.user_tag_id !== this.defaultItem.user_tag_id
                    ) || "Tag already exists"
            ],
            deleteDialog: false,
            brandToDelete: null,
            deleteLoading: false,
        };
    },
    created() {
        this.getAllUserTags();
    },
    watch: {
        addSdialog(val) {
            if (!val) this.submitting = false;
        }
    },
    methods: {
        getAllUserTags() {
            axios.get('/admin/user-tags/vlist').then(res => {
                this.userTags = res.data.userTags;
            });
        },
        openDialog() {
            this.defaultItem = {
                user_tag_name: '',
                type: '',
                discount: null
            };
            this.fsvalid = false;
            this.addSdialog = true;
        },

        onDialogToggle(open) {
            if (!open) {
                this.defaultItem = {
                    user_tag_name: '',
                    type: '',
                    discount: null
                };
                this.fsvalid = false;
                this.submitting = false;
            }
        },
        saveUserTag() {
            this.submitting = true;

            const fd = new FormData();
            fd.append('user_tag_name', this.defaultItem.user_tag_name);
            fd.append('type', this.defaultItem.type);
            if (this.defaultItem.type === 'percentage') {
                fd.append('discount', this.defaultItem.discount || 0);
            }

            axios.post('/admin/user-tag/add', fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then(() => {
                    this.addSdialog = false;
                    this.$toast.success('Tag added successfully!', { timeout: 500 });
                    this.getAllUserTags();
                })
                .catch(() => {
                    this.$toast.error('Something went wrong while saving the tag.', { timeout: 500 });
                })
                .finally(() => {
                    this.submitting = false;
                });
        },
        editItem(item) {
            this.editItemData = {
                user_tag_id: item.user_tag_id,
                user_tag_name: item.user_tag_name,
            };
            this.editDialog = true;
        },
        submitEditTag() {
            this.submitting = true;

            const fd = new FormData();
            fd.append('user_tag_id', this.editItemData.user_tag_id);
            fd.append('user_tag_name', this.editItemData.user_tag_name);

            axios.post('/admin/user-tag/update', fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then(() => {
                    this.getAllUserTags();
                    this.editDialog = false;
                    this.$toast.success('Tag updated successfully!', { timeout: 500 });
                })
                .catch(() => {
                    this.$toast.error('Something went wrong while updating the tag.', { timeout: 500 });
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
                await axios.post('/admin/user-tag/delete', {
                    user_tag_id: this.brandToDelete.user_tag_id
                });
                this.$toast?.success('Tag deleted successfully!', {
                    timeout: 500
                })
                this.getAllUserTags();
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
        async toggleStatus(item) {
            try {
                await axios.post(`/admin/user-tag/status-toggle/${item.user_tag_id}`, {
                    is_active: item.is_active
                });
                this.$toast?.success('Tag Status updated', { timeout: 500 });
            } catch (error) {
                console.error("Failed to toggle status", error);
                this.$toast?.error('Failed to update status', { timeout: 500 });
            }
        },

    }
};
</script>

<style>
.v-input {
    font-size: 12px !important;
}

.user-tag-list .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
    height: 32px !important;
}
</style>