<template>
    <div>
        <v-form @submit.prevent="saveProductData" v-model="fvalid">
            <div class="row">
                <div class="col-md-6">
                    <v-btn :loading="backLoading" :disabled="backLoading" small @click="navigateBack">
                        <template v-slot:loader>
                            <v-progress-circular indeterminate size="20" color="white"></v-progress-circular>
                        </template>
                        <v-icon v-if="!backLoading">mdi-arrow-left</v-icon>
                        <span v-if="!backLoading">Back</span>
                    </v-btn>
                </div>
                <div class="col-md-6 text-right">
                    <v-btn color="success" :loading="saveLoading" :disabled="saveLoading || !fvalid" @click="saveProductData">
                        <template v-slot:loader>
                            <v-progress-circular indeterminate size="20" color="green"></v-progress-circular>
                        </template>
                    <span v-if="!saveLoading">Save</span>
                    </v-btn>
                </div>
            </div>
            <v-row>
                <v-col cols="12" md="9">
                    <v-card>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="productname" placeholder="Enter product name" dense outlined label="Product Name" :rules="nameRules"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-textarea v-model="productdesc" rows="3" placeholder="Enter product description" dense outlined counter="160" label="Description"></v-textarea>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-card class="d-flex align-center justify-center" height="90" outlined @click="triggerFileInput">
                                        <v-icon v-if="!featuredImage" size="40" color="grey darken-1">mdi-image-area</v-icon>
                                        <v-img v-else :src="featuredImage" contain height="100%"></v-img>
                                        <v-file-input ref="fileInput" v-model="productImage" accept="image/*" hide-input @change="handleFileUpload" style="display: none"></v-file-input>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card v-if="!Object.keys(variants).length > 0" class="mt-3">
                        <v-card-title>Pricing</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="price" placeholder="Enter price" type="number" dense outlined label="Price" :rules="priceRules"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="compareprice" placeholder="Enter compare price" type="number" dense outlined label="Compare Price" :rules="comparepriceRules"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="costprice" placeholder="Enter cost price" type="number" dense outlined label="Cost Price" :rules="costpriceRules"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4" class="py-0">
                                    <v-checkbox v-model="taxable" label="Charge tax on this product"></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card v-if="!Object.keys(variants).length > 0" class="mt-3">
                        <v-card-title>Inventory</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="4">
                                    <v-text-field v-model="barcode" placeholder="Barcode" dense outlined label="Barcode" :rules="barcodeRules"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field v-model="sku" dense outlined label="SKU" append-icon="mdi-plus-circle-outline" :rules="skuRules" @click:append="regenerateSKU"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field v-model.number="stockQuantity" placeholder="Stock" type="number" dense outlined label="Stock Quantity" :rules="stockRules"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card v-if="!Object.keys(variants).length > 0" class="mt-3">
                        <v-card-title>Shipping</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="4">
                                    <v-input dense outlined>
                                        <v-text-field v-model.number="weight" type="number" dense outlined label="Weight" :rules="weightRules"></v-text-field>
                                        <template v-slot:append>
                                            <v-select v-model="weightUnit" dense solo :items="['kg', 'g']"></v-select>
                                        </template>
                                    </v-input>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card class="mt-3 pa-3">
                        <v-card-title>Variants</v-card-title>
                        <v-row v-if="Object.keys(variants).length > 0">
                            <v-col cols="12">
                                <div v-for="(variant, key) in variants" :key="key">
                                    <v-sheet class="pa-2 rounded border mb-3" style="background-color: rgb(249, 249, 249);">
                                        <v-row class="align-center">
                                            <v-col cols="8" class="d-flex align-center">
                                                <span class="font-weight-bold mr-2">{{ key }}:</span>
                                                <v-chip v-for="(opvalue, index) in variant" :key="index" outlined close 
                                                    @click:close="removeValue(key, opvalue)" close-icon="mdi-close-circle" color="primary" class="me-1">
                                                    {{ opvalue }}
                                                </v-chip>
                                            </v-col>
                                            <v-col cols="4" class="d-flex justify-end">
                                                <v-btn icon color="primary">
                                                    <v-icon @click="editVariant(key)">mdi-pencil</v-icon>
                                                </v-btn>
                                                <v-btn icon color="error">
                                                    <v-icon @click="removeOption(key)">mdi-delete</v-icon>
                                                </v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-sheet>
                                </div>
                            </v-col>
                        </v-row>   
                        <v-card-text>
                            <v-btn v-if="!showVariantForm && Object.keys(variants).length < 1" color="primary" @click="showVariantForm = true" class="mb-3">
                                <v-icon left>mdi-plus</v-icon> Add Variation
                            </v-btn>
                            <v-btn v-if="Object.keys(variants).length > 0 && Object.keys(variants).length < 3 && !showVariantForm" color="secondary" @click="showVariantForm = true" class="mt-3">
                                <v-icon left>mdi-plus</v-icon> Add Another Variation
                            </v-btn> 
                            <v-row v-if="showVariantForm" class="align-center">
                                <v-col cols="12" md="4">
                                    <v-select v-model="selectedOption" :items="filteredOptions" item-text="moption_name" item-value="moption_name" label="Option Name" 
                                    dense outlined hide-details></v-select>
                                </v-col>
                                <v-col cols="12" md="5">
                                    <v-text-field v-model="optionValues" label="Option Values (comma-separated)" dense outlined hide-details></v-text-field>
                                </v-col>
                                <v-col cols="12" md="3">
                                    <v-btn small color="success" @click="saveVariant">{{ isEditingVariant ? 'UPDATE' : 'DONE' }}</v-btn>
                                    <v-btn icon color="red" @click="removeVariant">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </v-col>
                            </v-row>                    
                        </v-card-text>
                    </v-card>
                    <v-card v-if="Object.keys(variants).length > 0" class="mt-3 pa-3">
                        <v-card-title style="justify-content: center;">Variants Details</v-card-title>
                        <v-data-table :items="variantItems" :headers="variantHeaders" class="elevation-1">
                            <template v-slot:item.variantImage="{ item, index }">
                                <v-card class="d-flex align-center justify-center m-1" height="50" outlined @click="triggerVariantFileInput(index)">
                                    <v-icon v-if="!item.variantImage" size="50" color="grey darken-1">mdi-image-area</v-icon>
                                    <v-img v-else :src="item.preview" contain height="100%" max-width="50px"></v-img>
                                    <input type="file" :ref="'variantImage' + index" accept="image/*" @change="handleVariantFileUpload($event, index)" style="display: none" />
                                </v-card>
                            </template>
                            <template v-slot:item.variant="{ item }">
                                <div class="d-flex flex-row">
                                    <div v-for="(part, i) in parseVariant(item.variant)" :key="i" class="py-1">
                                        <span  class="font-weight-bold text-capitalize">{{ part.name }}: </span>
                                        <span>{{ part.value }}</span>&nbsp;
                                    </div>
                                </div>
                            </template>
                            <template v-slot:item.price="{ item }">
                                <v-text-field v-model="item.price" small dense outlined hide-details></v-text-field>
                            </template>
                            <template v-slot:item.stock="{ item }">
                                <v-text-field v-model="item.stock" small dense outlined type="number" hide-details></v-text-field>
                            </template>
                            <template v-slot:item.sku="{ item }">
                                <v-text-field v-model="item.sku" small dense outlined hide-details></v-text-field>
                            </template>
                            <template v-slot:item.barcode="{ item }">
                                <v-text-field v-model="item.barcode" small dense outlined hide-details></v-text-field>
                            </template>
                            <template v-slot:item.actions="{ item, index }">
                                <v-btn icon color="red" @click="removeVariantCombination(index)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card>
                        <v-card-title>Status</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <v-select v-model="pro.pstatus" :items="['Active', 'Draft']" dense outlined label="Product Status"></v-select>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card class="mt-3">
                        <v-card-title>Publishing</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <div class="fw-bold">Sales Channels</div>
                                    <v-checkbox v-model="pro.selectedSalesChannel" label="Online Store" value="Online Store" />
                                    <v-checkbox v-model="pro.selectedSalesChannel" label="Other" value="Other" />
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                    <v-card class="mt-3">
                        <v-card-title>Product Organization</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <v-autocomplete v-model="pro.ptype" :items="mptypes" item-text="mproduct_type_name" item-value="mproduct_type_id" 
                                        :filter="ptypeFilter" label="Type" outlined dense clearable :search-input.sync="typedText">
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewProductType" :disabled="!typedText?.trim()" >
                                                Add "{{ typedText }}"
                                            </v-btn>
                                        </template>
                                    </v-autocomplete>
                                    <v-autocomplete v-model="pro.brand" :items="mbrands" item-text="mbrand_name" item-value="mbrand_id" label="Brand" 
                                         outlined dense clearable>
                                    </v-autocomplete>
                                    <!-- <v-autocomplete v-model="pro.brand" :items="mbrands" item-text="mbrand_name" item-value="mbrand_id" label="Brand" 
                                        :filter="brandFilter" outlined dense clearable :search-input.sync="typedBrand">
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewBrand" :disabled="!typedBrand?.trim()" >
                                                Add "{{ typedBrand }}"
                                            </v-btn>
                                        </template>
                                    </v-autocomplete> -->
                                    <v-autocomplete ref="tagsAutocomplete" multiple v-model="pro.tags" :items="mtags" item-text="mtag_name" 
                                        item-value="mtag_id" label="Tags" :filter="tagFilter" outlined dense small-chips deletable-chips :search-input.sync="typedTag">
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewTag" :disabled="!typedTag?.trim()" >
                                                Add “{{ typedTag }}”
                                            </v-btn>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-form>
    </div>
</template>
    
<script>
import axios from "axios";

export default {
    name: "AdminAddProduct",
    data() {
        return {
            method:"add",
            backLoading: false,
            saveLoading: false,
            optname:{},
            optvalue:{},
            fvalid:false,
            vitems:[],
            productname: "",
            productdesc: "",
            productImage: null,
            price: "",
            compareprice: "",
            costprice: "",
            taxable: 0,
            barcode: "",
            sku: "",
            stockQuantity: "",
            weight: "",
            weightUnit: "kg",
            showVariantForm: false,
            selectedOption: "",
            optionValues: "",
            availableOptions: [],
            variants: {},
            isEditingVariant: false,
            pro:{
                selectedSalesChannel: [],
                pstatus:'Draft',
                ptype: null,
                brand: null,
                tags:[]
            },
            mptypes: [],
            typedText: "",
            mbrands:[],
            typedBrand: "",
            mtags: [],
            typedTag: "",
            featuredImage: null,
            nameRules: [
                (v) => !!v || "Product name is required",
                (v) => (v && v.length <= 255) || "Product name must be less than 255 characters",
            ],
            priceRules: [
                (v) => v === "" || (!isNaN(v) && v >= 0) || "Price must be a positive number",
                v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Price up to 2 decimal places"
            ],
            comparepriceRules: [
                (v) => v === "" || (!isNaN(v) && v >= 0) || "Compare price must be a positive number",
                v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Compare price up to 2 decimal places"
            ],
            costpriceRules: [
                (v) => v === "" || (!isNaN(v) && v >= 0) || "Cost price must be a postive number",
                v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Cost price up to 2 decimal places"
            ],
            skuRules: [(v) => !!v || "SKU is required"],
            barcodeRules: [(v) => !v || v.length >= 10 || "Barcode must be at least 10 characters"],
            stockRules: [
                (v) => v === null || v === '' || /^\d+$/.test(v) || "Stock must be a whole number",
                (v) => v === null || v === '' || v >= 0 || "Stock must be a positive number",
            ],
            weightRules: [
                (v) => v >= 0 || "Weight must be greater than 0",
                v => v === "" || /^\d+(\.\d{1,3})?$/.test(v) || "Weight up to 3 decimal places"
            ],
        };
    },
    computed: {
        parsedVariantData() {
            return this.variantItems.map(item => this.parseVariant(item.variant));
        },
        variantItems() {
            let items = [];
            const optionKeys = Object.keys(this.variants);
            const optionValues = Object.values(this.variants);

            if (optionValues.length === 0) return items;

            let baseSKU = this.sku || Math.random().toString(36).substring(2, 7).toUpperCase();
            let vprice = this.price;
            let vstockQuantity = this.stockQuantity;
            let vbarcode = '';
            let variantCount = 0;

            const generateCombinations = (arrays, prefix = []) => {
                if (!arrays.length) {
                    let skuSuffix = variantCount === 0 ? "" : `-${variantCount}`;
                    items.push({
                        variantImage: null,
                        variant: optionKeys.map((key, i) => `${key}: ${prefix[i]}`).join(" / "),
                        price: `${vprice}`,
                        stock: variantCount === 0 && Number(vstockQuantity) > 0 ? `${vstockQuantity}` : 0,
                        sku: `${baseSKU}${skuSuffix}`,
                        barcode: `${vbarcode}`,
                        optname: optionKeys,
                        optvalue: optionKeys.reduce((acc, key, i) => 
                                    {
                                        acc[key] = prefix[i]; 
                                        return acc;
                                    }, {})
                    });
                    variantCount++;
                    return;
                }

                const firstArray = arrays[0];
                const remainingArrays = arrays.slice(1);

                firstArray.forEach(value => {
                    generateCombinations(remainingArrays, [...prefix, value]);
                });
            };

            generateCombinations(optionValues);

            return items;
        },
        variantHeaders() {
            return [
                { text: "Image", value: "variantImage", editable: true },
                { text: "Variant", value: "variant" },
                { text: "Price", value: "price", editable: true },
                { text: "Available Stock", value: "stock", editable: true },
                { text: "SKU", value: "sku" },
                { text: "Barcode", value: "barcode", editable: true },
                { text: "Actions", value: "actions" },
            ];
        },
        filteredOptions() {
            return this.availableOptions.filter(option => {
                return option.moption_name === this.selectedOption || !this.variants.hasOwnProperty(option.moption_name);
            });
        },
    },
    watch: {
        variantItems: {
            handler(newValue) {
                this.vitems = newValue;
            },
            deep: true,
            immediate: true
        },
    },
    created() {
        this.getProductData();
        this.generateSKU();
    },
    methods: {
        getProductData() {
            axios.get("/admin/product/pdata")
            .then((resp) => {
                this.mbrands = resp.data.brands;
                this.mptypes = resp.data.ptypes;
                this.mtags = resp.data.tags;
                this.availableOptions = resp.data.selectedOption;
            })
            .catch((error) => {
                console.error("Error fetching product data:", error);
            });
        }, 
        parseVariant(variantString) {
            return variantString.split(" / ").map(part => {
                const [name, value] = part.split(": ");
                return { name, value };
            });
        },
        generateSKU() {
            if (!this.sku) {
                this.sku = Math.random().toString(36).substring(2, 7).toUpperCase();
            }
        },
        regenerateSKU() {
                this.sku = Math.random().toString(36).substring(2, 7).toUpperCase();
        },       
        triggerFileInput() {
                this.$refs.fileInput.$refs.input.click();
        },
        handleFileUpload() {
                if (this.productImage) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.featuredImage = e.target.result;
                    };
                    reader.readAsDataURL(this.productImage);
                }
        },
        triggerVariantFileInput(index) {
            const fileInput = this.$refs[`variantImage${index}`];

            if (fileInput) {
                fileInput.click();
            } else {
                console.error(`File input for index ${index} is undefined`);
            }
        },
        handleVariantFileUpload(event, index) {
            const file = event.target.files[0];
            const previewURL = URL.createObjectURL(file);
            if (file) {
                this.$set(this.variantItems, index, {
                    ...this.variantItems[index],
                    variantImage: file,
                    preview: previewURL
                });
            }
        },
        addVariant() {
                this.showVariantForm = true;
        },
        saveVariant() {
            if (!this.selectedOption || !this.optionValues.trim()) return;

            const optionKey = this.selectedOption;
            const newValues = this.optionValues
                .split(",")
                .map(v => v.trim().toLowerCase())
                .filter((v, index, self) => self.indexOf(v) === index);

            if (this.isEditingVariant && this.previousOptionKey && this.previousOptionKey !== optionKey) {
                this.$delete(this.variants, this.previousOptionKey);
            }

            this.$set(this.variants, optionKey, newValues);

            this.selectedOption = "";
            this.optionValues = "";
            this.showVariantForm = false;
            this.isEditingVariant = false;
            this.previousOptionKey = null;
        },
        removeVariant() {
            this.showVariantForm = false;
            this.selectedOption = null;
            this.optionValues = "";
            this.isEditingVariant = false;
        },
        removeOption(key) {
            if (this.variants[key]) {
                this.$delete(this.variants, key);
            }
        },
        removeValue(key, value) {
            if (this.variants[key]) {
                this.variants[key] = this.variants[key].filter(v => v !== value);
                if (this.variants[key].length === 0) {
                    this.$delete(this.variants, key);
                }
            }
        },
        removeVariantRow(item) {
            const [optionKey, optionVal] = item.variant.split(":").map(str => str.trim());
            this.removeValue(optionKey, optionVal);
        },
        editVariant(key) {
            if (this.variants[key]) {
                this.previousOptionKey = key;
                this.selectedOption = key;
                this.optionValues = this.variants[key].join(", ");
                this.showVariantForm = true;
                this.isEditingVariant = true;
            }
        },
        removeVariantCombination(index) {
            if (index >= 0 && index < this.variantItems.length) {
                this.variantItems.splice(index, 1);
            }
        },
        ptypeFilter(item, queryText, itemText) {
            this.typedText = queryText;
            return itemText.toLowerCase().includes(queryText.toLowerCase());
        },
        brandFilter(item, queryText, itemText) {
            this.typedBrand = queryText;
            return itemText.toLowerCase().includes(queryText.toLowerCase());
        },
        tagFilter(item, queryText, itemText) {
            this.typedTag = queryText;
            return itemText.toLowerCase().includes(queryText.toLowerCase());
        },
        async addNewProductType() {
            const name = this.typedText?.trim();
            if (!name) return;

            try {
                const formData = new FormData();
                formData.append("mproduct_type_name", name);

                const res = await axios.post("/admin/mproduct-types", formData, {
                headers: { "Content-Type": "multipart/form-data" },
                });

                const newId = res.data.mproduct_type_id;

                this.mptypes.push({
                mproduct_type_id: newId,
                mproduct_type_name: name,
                });

                this.pro.ptype = newId;

                this.typedText = "";
            } catch (err) {
                console.error("Failed to add product type:", err);
            }
        },
        async addNewBrand() {
            const newName = this.typedBrand?.trim();
            if (!newName) return;

            try {
                const formData = new FormData();
                formData.append("mbrand_name", newName);

                const res = await axios.post("/admin/mbrands", formData, {
                headers: { "Content-Type": "multipart/form-data" },
                });
                
                const newId = res.data.mbrand_id;

                this.mbrands.push({
                mbrand_id: newId,
                mbrand_name: newName,
                });

                this.pro.brand = newId;

                this.typedBrand = "";
            } catch (err) {
                console.error("Error adding brand:", err);
            }
        },
        async addNewTag() {
            const newName = this.typedTag?.trim();
            if (!newName) return;

            const alreadyExists = this.mtags.some(
                (tag) => tag.mtag_name.toLowerCase() === newName.toLowerCase()
            );
            if (alreadyExists) {
                console.warn("That tag already exists!");
                this.typedTag = "";
                if (this.$refs.tagsAutocomplete) {
                this.$refs.tagsAutocomplete.internalSearch = "";
                }
                return;
            }

            try {
                const response = await axios.post("/admin/mtags", {
                mtag_name: newName,
                });

                const newId = response.data.mtag_id;

                this.mtags.push({
                mtag_id: newId,
                mtag_name: newName,
                });

                this.pro.tags.push(newId);

                this.typedTag = "";
                if (this.$refs.tagsAutocomplete) {
                this.$refs.tagsAutocomplete.internalSearch = "";
                }
            } catch (err) {
                console.error("Error adding tag:", err);
            }
        },
        async saveProductData() {
            if (this.saveLoading || !this.fvalid) return;

            this.saveLoading = true;
            const pdata = new FormData();

            pdata.append("ptype", this.pro.ptype ?? "");
            pdata.append("pbrand", this.pro.brand ?? "");
            pdata.append("ptags", JSON.stringify(this.pro.tags ?? [])); // Convert tags to JSON
            pdata.append("pstatus", this.pro.pstatus ?? "Draft");
            pdata.append("pchannel", JSON.stringify(this.pro.selectedSalesChannel ?? []));
            pdata.append("ptitle", this.productname ?? "");
            pdata.append("pdesc", this.productdesc ?? "");
            
            if (this.productImage instanceof File) {
                pdata.append("pimage", this.productImage);
            }

            pdata.append("pprice", this.price ?? "");
            pdata.append("pcompareprice", this.compareprice ?? "");
            pdata.append("pcostprice", this.costprice ?? "");
            pdata.append("taxable", this.taxable ? "1" : "0"); // Convert to string for backend
            pdata.append("pbarcode", this.barcode ?? "");
            pdata.append("psku", this.sku ?? "");
            pdata.append("pstock", this.stockQuantity ?? "");
            pdata.append("pweight", this.weight ?? "0");
            pdata.append("pweightunit", this.weightUnit ?? "kg");

            if (this.vitems.length > 0) {
                this.vitems.forEach((variant, index) => {
                    pdata.append(`variants[${index}][sku]`, variant.sku ?? "");
                    pdata.append(`variants[${index}][price]`, variant.price ?? "");
                    pdata.append(`variants[${index}][stock]`, variant.stock ?? "");
                    pdata.append(`variants[${index}][barcode]`, variant.barcode ?? "");
                    pdata.append(`variants[${index}][optname]`, JSON.stringify(variant.optname ?? [])); // Ensure JSON
                    pdata.append(`variants[${index}][optvalue]`, JSON.stringify(variant.optvalue ?? [])); // Ensure JSON
                    
                    if (variant.variantImage instanceof File) {
                        pdata.append(`variants[${index}][variantImage]`, variant.variantImage);
                    }
                });
            }

            await axios.post("/admin/save-product",pdata,{
                headers: { "Content-Type": "multipart/form-data" }
            })
            .then((resp)=>{
                console.log(resp.data);
                window.location.href = '/admin/products/list';
                this.$toast.success('Product added successfully!');
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        navigateBack() {
            if (this.backLoading) return;

            this.backLoading = true;

            setTimeout(() => {
                window.location.href = '/admin/products/list';
            }, 500); 
        },
    } 
};
</script>
    