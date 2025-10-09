<template>
    <div class="page-margin-20-40 user-custom-price">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0 align-center">
                <v-col cols="6" class="p-0 d-flex align-center">
                    <h2 class="text-h6 mb-1">
                        Custom Price<span v-if="prettyTagName"> - {{ prettyTagName }}</span>
                    </h2>
                </v-col>

                <v-col cols="6" class="p-0 d-flex justify-end align-center">
                    <span class="mr-4" style="font-size: 16px !important;">Select Tag</span>
                    <v-select v-model="selectedTag" :items="userTags" item-text="user_tag_name" item-value="user_tag_id"
                        outlined dense hide-details class="ma-0" style="max-width: 150px" @change="onTagChange" />
                </v-col>
            </v-row>
        </v-container>


        <v-row class="mt-0">
            <v-col cols="12">
                <v-card elevation="5">
                    <v-data-table v-model="selected" :items="allVariants" :headers="headers" :search="mainSearch"
                        item-key="mvariant_id" :show-select="true"
                        :footer-props="{ 'items-per-page-options': [10, 25, 50], 'items-per-page-text': 'Rows per page:' }">
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-text-field v-model="mainSearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Custom Price" />
                            </v-row>
                        </template>

                        <template v-slot:item.img="{ item }">
                            <v-img :src="item.img ? cdn + item.img : ''" cover width="50" height="50" class="ma-1" style="border: 1px solid #e0e0e0; border-radius: 10px;">  
                                <template #placeholder>
                                    <div class="d-flex align-center justify-center fill-height">
                                        <v-icon color="grey">mdi-image</v-icon>
                                    </div>
                                </template>
                            </v-img>
                        </template>

                        <template #item.tag_price="{ item }">
                            <div style="cursor:pointer; background-color: #e0e0e0; padding: 6px; border-radius: 4px;"
                                @click="openTagDialog(item)">
                                {{ item.tag_price }}
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
                                            <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor: pointer;">
                                                mdi-dots-vertical
                                            </v-icon>
                                        </div>
                                    </template>

                                    <v-list dense>
                                        <v-list-item @click="openBulkDialog">
                                            <v-list-item-title>Bulk Update</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="showTagDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title><span>Update Custom Price</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="showTagDialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="priceValid" @submit.prevent="updateTagValue">
                    <v-card-text>
                        <v-text-field v-model="tagValueInput" label="Custom Price" type="number" step="0.01"
                            :rules="priceRules" autofocus />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn class="btn-32-text-12" type="submit"
                            style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
                            small :loading="isUpdating" :disabled="isUpdating || !priceValid">
                            Update
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="bulkUpdateDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title><span>Update Multi Custom Price</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="bulkUpdateDialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="priceValid" @submit.prevent="updateMultiTagValue">
                    <v-card-text>
                        <v-text-field v-model="tagValueInput" label="Custom Price" type="number" step="0.01"
                            :rules="priceRules" autofocus />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn class="btn-32-text-12" type="submit"
                            style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;"
                            small :loading="bulkUpdateLoading" :disabled="bulkUpdateLoading || !priceValid">
                            Update
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
    name: 'UserCustomPrice',
    props: {
        usertagid: {
            type: [String, Number],
            default: null,
        },
    },
    data() {
        return {
            cdn: 'https://cdn.truewebpro.com/',
            tagName: '',
            mainSearch: '',
            allVariants: [],
            headers: [
                { text: '', value: 'data-table-select', width: '10px' },
                { text: 'Image', value: 'img', sortable: false },
                { text: 'Product', value: 'product' },
                { text: 'Variants', value: 'variantLabel' },
                { text: 'Base Price', value: 'price' },
                { text: 'Custom Price', value: 'tag_price', width: '150px' },
                { text: '', value: 'delete', sortable: false, width: '130px' }
            ],
            showTagDialog: false,
            priceValid: false,
            tagValueInput: '',
            currentVariantId: null,
            isUpdating: false,

            userTags: [],
            selectedTag: null,
            prettyTagName: '',

            user_tag_id: this.$route.params?.usertagid || null,
            priceRules: [
                v => (v !== '' && v !== null) || 'Custom Price is required.',
                v => !isNaN(v) || 'Value must be a number.',
                v => parseFloat(v) >= 0 || 'Must be a positive number.',
                v => /^\d+(\.\d{1,2})?$/.test(String(v)) || 'Max 2 decimal places allowed.',
            ],

            selected: [],
            bulkUpdateDialog: false,
            bulkUpdateLoading: false,
        };
    },
    computed: {
        initialTagId() {
            const fromProp = this.usertagid != null ? Number(this.usertagid) : null;
            const fromRoute = this.$route?.params?.usertagid != null ? Number(this.$route.params.usertagid) : null;
            return fromProp ?? fromRoute ?? null;
        },
    },
    mounted() {
        axios.get('/admin/user-tags/vlist')
            .then(({ data }) => {
                const all = data.userTags || [];
                this.userTags = all.filter(t => t.type === 'custom');

                const candidate = this.initialTagId;
                const isValid = candidate && this.userTags.some(t => t.user_tag_id === candidate);

                this.selectedTag = isValid
                    ? candidate
                    : (this.userTags[0]?.user_tag_id || null);

                if (this.selectedTag) {
                    this.onTagChange(this.selectedTag);
                }
            })
            .catch(err => {
                console.error('Error fetching customer tags:', err);
            });
    },
    watch: {
        '$route.params.usertagid'(nv) {
            const newId = nv != null ? Number(nv) : null;
            if (newId && this.userTags.some(t => t.user_tag_id === newId)) {
                this.onTagChange(newId);
            }
        },
    },
    methods: {
        img(src) {
            return src ? this.cdn + String(src).replace(/^\/+/, '') : '/images/no-image-available.png';
        },
        async fetchVariants(tagId) {
            const { data } = await axios.get(`/admin/user-tag-price/list`, {
                params: { user_tag_id: tagId },
            });

            this.prettyTagName = (data.tagName || '');

            this.allVariants = (data.variants || []).map((v, i) => {
                let label = '';
                const det = v.details || [];
                if (det.length) {
                    label = det
                        .map(d =>
                            Object.entries(d.option_value || {}).map(([k, val]) => `${k}: ${val}`).join(', ')
                        )
                        .join(' | ');
                }

                return {
                    mvariant_id: v.mvariant_id,
                    img: v.mvariant_image || (v.product && v.product.mproduct_image),
                    product: v.product ? v.product.mproduct_title : '',
                    variantLabel: label,
                    price: v.price,
                    tag_price: v.tag_price ?? '',
                };
            });
        },
        onTagChange(tagId) {
            this.selectedTag = tagId;
            const tag = this.userTags.find(t => t.user_tag_id === tagId);
            this.prettyTagName = tag?.user_tag_name || '';
            this.fetchVariants(tagId);
        },
        openTagDialog(item) {
            this.currentVariantId = item.mvariant_id;
            this.tagValueInput = item.tag_price === '' || item.tag_price == null ? '' : String(item.tag_price);
            this.priceValid = true;
            this.showTagDialog = true;
        },
        closeDialog() {
            this.showTagDialog = false;
            this.currentVariantId = null;
            this.tagValueInput = '';
            this.priceValid = false;
        },
        async updateTagValue() {
            if (this.isUpdating || !this.priceValid) return;

            this.isUpdating = true;
            try {
                await axios.post('/admin/user-tag-price/update', {
                    user_tag_id: this.selectedTag,
                    mvariant_id: this.currentVariantId,
                    tag_price: this.tagValueInput === '' ? null : parseFloat(this.tagValueInput),
                });

                const idx = this.allVariants.findIndex(v => v.mvariant_id === this.currentVariantId);
                if (idx !== -1) {
                    this.allVariants[idx].tag_price =
                        this.tagValueInput === '' ? '' : parseFloat(this.tagValueInput);
                }

                this.$toast?.success('Custom price updated!', { timeout: 500 });
                this.closeDialog();
            } catch (e) {
                console.error(e);
                this.$toast?.error('Failed to update custom price', { timeout: 700 });
            } finally {
                this.isUpdating = false;
            }
        },
        openBulkDialog() {
            if (!this.selectedTag) {
                this.$toast?.error('Please select a tag first.', { timeout: 700 });
                return;
            }
            if (!this.selected || this.selected.length === 0) {
                this.$toast?.error('Select at least one variant.', { timeout: 700 });
                return;
            }
            this.tagValueInput = '';
            this.priceValid = false;
            this.bulkUpdateDialog = true;
        },
        async updateMultiTagValue() {
            if (this.bulkUpdateLoading || !this.priceValid) return;

            const commonPrice = this.tagValueInput === '' ? null : parseFloat(this.tagValueInput);
            const items = (this.selected || []).map(row => ({
                mvariant_id: row.mvariant_id,
                tag_price: commonPrice,
            }));

            if (!this.selectedTag || items.length === 0) {
                this.$toast?.error('Invalid tag or no items selected.', { timeout: 700 });
                return;
            }

            this.bulkUpdateLoading = true;
            try {
                await axios.post('/admin/user-tag-price/update', {
                    user_tag_id: this.selectedTag,
                    items,
                });

                const setIds = new Set(items.map(i => i.mvariant_id));
                this.allVariants = this.allVariants.map(v =>
                    setIds.has(v.mvariant_id)
                        ? { ...v, tag_price: commonPrice === null ? '' : commonPrice }
                        : v
                );

                this.$toast?.success('Bulk custom prices updated!', { timeout: 500 });
                this.bulkUpdateDialog = false;
                this.selected = [];
            } catch (e) {
                console.error(e);
                this.$toast?.error('Bulk update failed', { timeout: 700 });
            } finally {
                this.bulkUpdateLoading = false;
            }
        },


    },
};
</script>

<style>
.v-input {
    font-size: 12px !important;
}

.user-custom-price .v-input__control {
    height: 24px !important;
}

.user-custom-price .v-select.v-input--dense .v-select__selection--comma {
    margin: 7px 0 7px 0;
}
</style>
