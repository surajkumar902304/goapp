<template>
    <div>
        <v-form @submit.prevent="updateProductData" v-model="fvalid">
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
                    <v-btn type="submit" color="success" :disabled="!fvalid || isSubmitting" :loading="isSubmitting">Update</v-btn>
                </div>
            </div>
    
            <v-row>
                <v-col cols="12" md="9">
                    <v-card>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="productname" placeholder="Enter product name" dense outlined label="Product Name" :rules="nameRules" />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-textarea v-model="productdesc" rows="3" placeholder="Enter product description" dense outlined counter="160" label="Description" />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-card class="d-flex align-center justify-center" height="90" outlined @click="triggerFileInput">
                                    <v-icon v-if="!featuredImage" size="40" color="grey darken-1">mdi-image-area</v-icon>
                                    <v-img v-else :src="getFeaturedImageSrc" contain height="100%" />
                                    <v-file-input ref="fileInput" v-model="productImage" accept="image/*" hide-input @change="handleFileUpload" style="display: none" />
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
        
                    <v-card v-if="!Object.keys(variants).length" class="mt-3">
                        <v-card-title>Pricing</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="price" placeholder="Enter price" dense outlined label="Price" :rules="priceRules" />
                                </v-col>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="compareprice" placeholder="Enter compare price" dense outlined label="Compare Price" :rules="comparepriceRules" />
                                </v-col>
                                <v-col cols="12" md="4" class="pb-0">
                                    <v-text-field v-model.number="costprice" placeholder="Enter cost price" dense outlined label="Cost Price" :rules="costpriceRules" />
                                </v-col>
                                <v-col cols="12" md="4" class="py-0">
                                    <v-checkbox v-model="taxable" label="Charge tax on this product" />
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
        
                    <v-card v-if="!Object.keys(variants).length" class="mt-3">
                        <v-card-title>Inventory</v-card-title>
                        <v-card-text>
                            <v-row>
                            <v-col cols="12" md="4">
                                <v-text-field v-model="barcode" placeholder="Barcode" dense outlined label="Barcode" :rules="barcodeRules" />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field v-model="sku" dense outlined label="SKU" append-icon="mdi-plus-circle-outline" :rules="skuRules" @click:append="regenerateSKU" />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field v-model.number="stockQuantity" type="number" placeholder="Stock" dense outlined label="Stock Quantity" :rules="stockRules" />
                            </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
        
                    <v-card v-if="!Object.keys(variants).length" class="mt-3">
                        <v-card-title>Shipping</v-card-title>
                        <v-card-text>
                            <v-row>
                            <v-col cols="12" md="4">
                                <v-input dense outlined>
                                <v-text-field v-model.number="weight" type="number" dense outlined label="Weight" :rules="weightRules" />
                                <template v-slot:append>
                                    <v-select v-model="weightUnit" dense solo :items="['kg','g']" />
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
                            <div v-for="(variantList, key) in variants" :key="key">
                                <v-sheet class="pa-2 rounded border mb-3" style="background-color: #f9f9f9;">
                                    <v-row class="align-center">
                                        <v-col cols="8" class="d-flex align-center">
                                        <span class="font-weight-bold mr-2">{{ key }}:</span>
                                        <v-chip v-for="(opvalue, idx) in variantList" :key="idx" outlined close @click:close="removeValue(key, opvalue)" close-icon="mdi-close-circle" color="primary" class="me-1">{{ opvalue }}</v-chip>
                                        </v-col>
                                        <v-col cols="4" class="d-flex justify-end">
                                        <v-btn icon color="primary"><v-icon @click="editVariant(key)">mdi-pencil</v-icon></v-btn>
                                        <v-btn icon color="error"><v-icon @click="removeOption(key)">mdi-delete</v-icon></v-btn>
                                        </v-col>
                                    </v-row>
                                </v-sheet>
                            </div>
                            </v-col>
                        </v-row>
                        <v-card-text>
                            <v-btn v-if="!showVariantForm && Object.keys(variants).length < 1" color="primary" @click="showVariantForm = true" class="mb-3"><v-icon left>mdi-plus</v-icon> Add Variation</v-btn>
                            <v-btn v-if="Object.keys(variants).length > 0 && Object.keys(variants).length < 3 && !showVariantForm" color="secondary" @click="showVariantForm = true" class="mt-3"><v-icon left>mdi-plus</v-icon> Add Another Variation</v-btn>
                            <v-row v-if="showVariantForm" class="align-center">
                            <v-col cols="12" md="4">
                                <v-select v-model="selectedOption" :items="filteredOptions" item-text="moption_name" item-value="moption_name" label="Option Name" dense outlined hide-details />
                            </v-col>
                            <v-col cols="12" md="5">
                                <v-text-field v-model="optionValues" label="Option Values (comma-separated)" dense outlined hide-details />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-btn small color="success" @click="saveVariant">{{ isEditingVariant ? "UPDATE" : "DONE" }}</v-btn>
                                <v-btn icon color="red" @click="removeVariant"><v-icon>mdi-delete</v-icon></v-btn>
                            </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
    
                    <v-card v-if="vitems.length > 0 && vitems.some(item => item.optname && item.optname.length > 0)" class="mt-3 pa-3">
                        <v-card-title style="justify-content: center;">Variants Details</v-card-title>
                        <v-data-table :items="vitems" :headers="variantHeaders" class="elevation-1">
                            <template v-slot:item.variantImage="{ item, index }">
                                <v-card class="d-flex align-center justify-center m-1" height="50" outlined @click="triggerVariantFileInput(index)">
                                    <v-icon v-if="!item.variantImage" size="50" color="grey darken-1" > mdi-image-area </v-icon>
                                    <v-img v-else :src="getVariantImageSrc(item)" contain height="100%" max-width="50px" ></v-img>
                                    <input type="file" :ref="`variantImage${index}`" accept="image/*" @change="handleVariantFileUpload($event, index)" style="display: none" />
                                </v-card>
                            </template>
                            <template v-slot:item.variant="{ item }">
                                <div class="d-flex flex-row">
                                    <div v-for="(part, i) in parseVariant(item.variant)" :key="i" class="py-1">
                                        <span class="font-weight-bold text-capitalize">{{ part.name }}:</span>
                                        <span>{{ part.value }}</span>&nbsp;
                                    </div>
                                </div>
                            </template>
                            <template v-slot:item.price="{ item }">
                                <v-text-field v-model="item.price" small dense outlined hide-details />
                            </template>
                            <template v-slot:item.stock="{ item }">
                                <v-text-field v-model="item.stock" small dense outlined type="number" hide-details />
                            </template>
                            <template v-slot:item.sku="{ item }">
                                <v-text-field v-model="item.sku" small dense outlined hide-details />
                            </template>
                            <template v-slot:item.barcode="{ item }">
                                <v-text-field v-model="item.barcode" small dense outlined hide-details />
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
    
                <v-col cols="12" md="3">
                    <v-card>
                        <v-card-title>Status</v-card-title>
                        <v-card-text>
                            <v-row>
                            <v-col cols="12">
                                <v-select v-model="pro.pstatus" :items="['Active','Draft']" dense outlined label="Product Status" />
                            </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
            
                    <v-card class="mt-3">
                        <v-card-title>Publishing</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <div class="fw-bold">Sales Channels</div>
                                    <v-checkbox v-model="pro.selectedSalesChannel" label="Online Store" value="Online Store" @change="handleCheckboxChange('Online Store ')" />
                                    <v-checkbox v-model="pro.selectedSalesChannel" label="Other" value="Other" @change="handleCheckboxChange('Other')" />
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
                                        :filter="ptypeFilter" label="Product Type" outlined dense clearable>
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewProductType" :disabled="!typedText" >
                                                Add "{{ typedText }}"
                                            </v-btn>
                                        </template>
                                    </v-autocomplete>
                                    <v-autocomplete v-model="pro.brand" :items="mbrands" item-text="mbrand_name" item-value="mbrand_id" label="Brand" 
                                        :filter="brandFilter" outlined dense clearable>
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewBrand" :disabled="!typedBrand" >
                                                Add "{{ typedBrand }}"
                                            </v-btn>
                                        </template>
                                    </v-autocomplete>
                                    <v-autocomplete ref="tagsAutocomplete" multiple v-model="pro.tags" :items="mtags" item-text="mtag_name" 
                                        item-value="mtag_id" label="Tags" :filter="tagFilter" outlined dense small-chips deletable-chips>
                                        <template v-slot:no-data>
                                            <v-btn @click="addNewTag" :disabled="!typedTag" >
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
  
    function isSubset(oldVal, newVal) {
        for (const [k, v] of Object.entries(oldVal)) {
        if (newVal[k] !== v) return false;
        }
        return true;
    }
    
    export default {
        name: "AdminEditProduct",
        props: {
            mproid: {
                type: Number,
                required: true
            }
        },
    data() {
        return {
            cdn: "https://cdn.truewebpro.com/",
            method: "edit",
            backLoading: false,
            fvalid: false,
            productname: "",
            productdesc: "",
            productImage: null,
            featuredImage: null,
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
            variants: {},
            vitems: [],
            isEditingVariant: false,
            isSubmitting: false,
            previousOptionKey: null,
            variantHeaders: [
            { text: "Image", value: "variantImage" },
            { text: "Variant", value: "variant" },
            { text: "Price", value: "price" },
            { text: "Stock", value: "stock" },
            { text: "SKU", value: "sku" },
            { text: "Barcode", value: "barcode" }
            ],
            pro: {
            selectedSalesChannel: "Online Store",
            pstatus: "Draft",
            ptype: "",
            brand: "",
            tags: []
            },
            mptypes: [],
            typedText: "",
            mbrands: [],
            typedBrand: "",
            mtags: [],
            typedTag: "",
            availableOptions: [],
            nameRules: [
            v => !!v || "Product name is required",
            v => (v && v.length <= 255) || "Product name must be less than 50 characters"
            ],
            priceRules: [
            v => v === "" || (!isNaN(v) && v >= 0) || "Price must be a positive number",
            v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Price up to 2 decimal places"
            ],
            comparepriceRules: [
            v => v === "" || (!isNaN(v) && v >= 0) || "Compare price must be a positive number",
            v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Compare price up to 2 decimal places"
            ],
            costpriceRules: [
            v => v === "" || (!isNaN(v) && v >= 0) || "Cost price must be a positive number",
            v => v === "" || /^\d+(\.\d{1,2})?$/.test(v) || "Cost price up to 2 decimal places"
            ],
            skuRules: [v => !!v || "SKU is required"],
            barcodeRules: [v => !v || v.length >= 10 || "Barcode must be at least 10 characters"],
            stockRules: [v => !v || v >= 0 || "Stock must be a positive number"],
            weightRules: [
            v => v >= 0 || "Weight must be greater than 0",
            v => v === "" || /^\d+(\.\d{1,3})?$/.test(v) || "Weight up to 3 decimal places"
            ]
        };
    },
    created() {
        this.getProductData();
    },
    computed: {
        filteredOptions() {
            return this.availableOptions.filter(option => {
            return (
                option.moption_name === this.selectedOption ||
                !this.variants.hasOwnProperty(option.moption_name)
            );
            });
        },
        getFeaturedImageSrc() {
            if (typeof this.featuredImage === "string" && this.featuredImage.startsWith("data:image")) {
            return this.featuredImage;
            }
            return this.cdn + this.featuredImage;
        }
    },
    methods: {
        getProductData() {
            axios.get(`/admin/vproduct/editdata?mproid=${this.mproid}`).then(resp => {
            const prod = resp.data.mproduct;
            this.productname = prod.mproduct_title;
            this.productdesc = prod.mproduct_desc;
            if (prod.mproduct_image) this.featuredImage = prod.mproduct_image;
            if (!prod.mvariants || prod.mvariants.length === 0) {
                this.price = prod.price || "";
                this.compareprice = prod.compare_price || "";
                this.costprice = prod.cost_price || "";
                this.taxable = prod.taxable || 0;
                this.barcode = prod.barcode || "";
                this.sku = prod.sku || "";
                this.stockQuantity = prod.quantity || "";
                this.weight = prod.weight || "";
                this.weightUnit = prod.weightunit || "kg";
            } else {
                this.price = prod.mvariants[0].price || "";
                this.compareprice = prod.mvariants[0].compare_price || "";
                this.costprice = prod.mvariants[0].cost_price || "";
                this.taxable = prod.mvariants[0].taxable || 0;
                this.barcode = prod.mvariants[0].barcode || "";
                this.sku = prod.mvariants[0].sku || "";
                this.stockQuantity = prod.mvariants[0].quantity || "";
                this.weight = prod.mvariants[0].weight || "";
                this.weightUnit = prod.mvariants[0].weightunit || "kg";
            }
            this.pro.pstatus = prod.status || "Draft";
            this.pro.selectedSalesChannel = prod.saleschannel || "Online Store";
            this.pro.ptype = prod.mproduct_type_id || "";
            this.pro.brand = prod.mbrand_id || "";
            this.pro.tags = prod.mtags || [];
            this.availableOptions = resp.data.moptions || [];
            if (prod.mvariants && prod.mvariants.length > 0) {
                this.vitems = prod.mvariants.map(variant => {
                return {
                    mvariant_id: variant.mvariant_id,
                    variantImage: variant.mvariant_image || null,
                    variant: Object.entries(variant.option_value || {})
                    .map(([k, v]) => `${k}: ${v}`)
                    .join(" / "),
                    price: variant.price || "",
                    stock: variant.quantity || "",
                    sku: variant.sku || "",
                    barcode: variant.barcode || "",
                    optname: Object.keys(variant.option_value || {}),
                    optvalue: variant.option_value || {}
                };
                });
                prod.mvariants.forEach(v => {
                if (v.option_value) {
                    Object.entries(v.option_value).forEach(([key, val]) => {
                    if (!this.variants[key]) this.$set(this.variants, key, []);
                    if (!this.variants[key].includes(val)) this.variants[key].push(val);
                    });
                }
                });
            }
            this.mptypes = resp.data.mptypes || [];
            this.mbrands = resp.data.mbrands || [];
            this.mtags = resp.data.mtags || [];
            }).catch(err => {
            console.error("Error fetching product data:", err);
            });
        },
        parseVariant(variantString) {
            if (!variantString) return [];
            return variantString.split(" / ").map(part => {
            const [name, value] = part.split(": ");
            return { name, value };
            });
        },
        generateVariantCombinations() {
            const oldRows = [...this.vitems];

            const optionKeys = Object.keys(this.variants);
            if (!optionKeys.length) {
                this.vitems = [];
                return;
            }

            const combos = [];
            const optionValues = Object.values(this.variants);

            const buildCombos = (arrays, prefix = []) => {
                if (!arrays.length) {
                const comboStr = optionKeys
                    .map((k, i) => `${k}: ${prefix[i]}`)
                    .join(" / ");

                combos.push({
                    variant: comboStr,
                    price: "",
                    stock: "",
                    sku: "",
                    barcode: "",
                    variantImage: null,
                    optname: optionKeys,
                    optvalue: optionKeys.reduce((acc, key, i) => {
                    acc[key] = prefix[i];
                    return acc;
                    }, {}),
                    mvariant_id: null
                });
                return;
                }

                const [firstArr, ...rest] = arrays;
                firstArr.forEach(value => {
                buildCombos(rest, [...prefix, value]);
                });
            };

            buildCombos(optionValues);

            const lastRow = oldRows.length > 0 ? oldRows[oldRows.length - 1] : null;
            let baseSKU = lastRow?.sku || "SKU";
            let skuCount = 1;

            const match = baseSKU.match(/(.*?)-(\d+)$/);
            if (match) {
                baseSKU = match[1];
                skuCount = parseInt(match[2], 10) + 1;
            }

            const usedVariantIDs = new Set();

            const finalRows = combos.map(newCombo => {
            const foundOld = oldRows.find(row => {
                for (const [k, v] of Object.entries(row.optvalue)) {
                if (newCombo.optvalue[k] !== v) return false;
                }
                return true;
            });

            if (foundOld) {
                if (!usedVariantIDs.has(foundOld.mvariant_id)) {
                usedVariantIDs.add(foundOld.mvariant_id);
                return {
                    ...newCombo,
                    price: foundOld.price,
                    stock: foundOld.stock,
                    sku: foundOld.sku,
                    barcode: foundOld.barcode,
                    variantImage: foundOld.variantImage,
                    mvariant_id: foundOld.mvariant_id,
                };
                } else {
                return {
                    ...newCombo,
                    price: lastRow?.price || "",
                    stock: lastRow?.stock || "",
                    barcode: "",
                    variantImage: null,
                    sku: `${baseSKU}-${skuCount++}`,
                    mvariant_id: null,
                };
                }

            } else {
                return {
                ...newCombo,
                price: lastRow?.price || "",
                stock: lastRow?.stock || "",
                barcode: "",
                variantImage: null,
                sku: `${baseSKU}-${skuCount++}`,
                mvariant_id: null,
                };
            }
            });

            this.vitems = finalRows;
        },
        saveVariant() {
            if (!this.selectedOption || !this.optionValues.trim()) return;
            const key = this.selectedOption;
            const vals = this.optionValues.split(",").map(s => s.trim().toLowerCase()).filter((v, i, self) => self.indexOf(v)===i);
            if (this.isEditingVariant && this.previousOptionKey && this.previousOptionKey !== key) {
            this.$delete(this.variants, this.previousOptionKey);
            }
            this.$set(this.variants, key, vals);
            this.selectedOption = "";
            this.optionValues = "";
            this.showVariantForm = false;
            this.isEditingVariant = false;
            this.previousOptionKey = null;
            this.generateVariantCombinations();
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
            this.generateVariantCombinations();
            }
        },
        removeValue(key, value) {
            if (this.variants[key]) {
            this.variants[key] = this.variants[key].filter(v => v!==value);
            if (!this.variants[key].length) {
                this.$delete(this.variants, key);
            }
            this.generateVariantCombinations();
            }
        },
        editVariant(key) {
            if (this.variants[key]) {
            this.isEditingVariant = true;
            this.previousOptionKey = key;
            this.selectedOption = key;
            this.optionValues = this.variants[key].join(", ");
            this.showVariantForm = true;
            }
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
            const input = this.$refs.fileInput?.$el?.querySelector('input[type="file"]');
            if (input) input.click();
        },
        handleFileUpload() {
            if (this.productImage instanceof File) {
            const reader = new FileReader();
            reader.onload = e => {
                this.featuredImage = e.target.result;
            };
            reader.readAsDataURL(this.productImage);
            }
        },
        getVariantImageSrc(item) {
            if (!item.variantImage) return "";
            if (typeof item.variantImage === "object" && item.variantImage.preview) {
                return item.variantImage.preview;
            }
            if (typeof item.variantImage === "string") {
                return this.cdn + item.variantImage;
            }
            return "";
        },
        triggerVariantFileInput(index) {
            const fileInput = this.$refs[`variantImage${index}`];
            if (fileInput) {
                fileInput.click();
            } else {
                console.error(`File input for index ${index} is undefined or not found.`);
            }
        },
        handleVariantFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;
            console.log("File chosen:", file);
  
            const reader = new FileReader();
            reader.onload = (e) => {
                this.$set(this.vitems, index, {
                ...this.vitems[index],
                variantImage: {
                    file: file,
                    preview: e.target.result
                }
                });
            };
            reader.readAsDataURL(file);
        },
        handleCheckboxChange(val) {
            this.pro.selectedSalesChannel = val;
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
        navigateBack() {
            if (this.backLoading) return;

            this.backLoading = true;

            setTimeout(() => {
                window.location.href = '/admin/products/list';
            }, 500); 
        },
    
        async updateProductData() {
            this.isSubmitting = true;
            const pdata = new FormData();

            pdata.append("mproduct_id", this.mproid);
            pdata.append("ptype", this.pro.ptype ?? "");
            pdata.append("pbrand", this.pro.brand ?? "");
            pdata.append("ptags", JSON.stringify(this.pro.tags ?? []));
            pdata.append("pstatus", this.pro.pstatus ?? "Draft");
            pdata.append("pchannel", this.pro.selectedSalesChannel ?? "online Store");
            pdata.append("ptitle", this.productname ?? "");
            pdata.append("pdesc", this.productdesc ?? "");

            if (this.productImage instanceof File) {
                pdata.append("pimage", this.productImage);
            }

            pdata.append("pprice", this.price ?? "");
            pdata.append("pcompareprice", this.compareprice ?? "");
            pdata.append("pcostprice", this.costprice ?? "");
            pdata.append("taxable", this.taxable ? "1" : "0");
            pdata.append("pbarcode", this.barcode ?? "");
            pdata.append("psku", this.sku ?? "");
            pdata.append("pstock", this.stockQuantity ?? "");
            pdata.append("pweight", this.weight ?? "0");
            pdata.append("pweightunit", this.weightUnit ?? "kg");

            if (this.vitems.length > 0) {
                this.vitems.forEach((variant, index) => {
                pdata.append(`variants[${index}][mvariant_id]`, variant.mvariant_id ?? "");
                pdata.append(`variants[${index}][sku]`, variant.sku ?? "");
                pdata.append(`variants[${index}][price]`, variant.price ?? "");
                pdata.append(`variants[${index}][stock]`, variant.stock ?? "");
                pdata.append(`variants[${index}][barcode]`, variant.barcode ?? "");
                pdata.append(`variants[${index}][optname]`, JSON.stringify(variant.optname ?? []));
                pdata.append(`variants[${index}][optvalue]`, JSON.stringify(variant.optvalue ?? {}));
                if (variant.variantImage && variant.variantImage.file instanceof File) {
                    pdata.append(`variants[${index}][variantImage]`, variant.variantImage.file);
                }
                });
            }

            await axios.post("/admin/update-product", pdata, {
                headers: { "Content-Type": "multipart/form-data" }
            })
            .then(resp => {
                console.log(resp.data);
                window.location.href = "/admin/products/list";
            })
            .catch ((err) => {
                console.error(err);
            })
        }
    }
  };
  </script>
  