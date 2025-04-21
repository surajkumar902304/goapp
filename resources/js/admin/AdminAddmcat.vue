<template>
    <div class="row">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <a href="/admin/mcats/list">
                    <v-icon small >mdi-arrow-left</v-icon>
                </a>
                <span class="ms-2 text-h6">Create Category</span>
            </div>
        </div>
        <div class="col-md-6">
            <v-btn color="secondary" small outlined>Discard</v-btn>
            <v-btn color="success" small>Save</v-btn>
        </div>
        <div class="col-md-9">
            <v-card outlined class="shadow-sm">
                <v-card-text>
                    <v-text-field label="Title" dense outlined></v-text-field>
                    <v-textarea label="Description" outlined>
                    </v-textarea>
                </v-card-text>
            </v-card>
            <v-card outlined class="my-3 shadow-sm">
                <v-card-subtitle class="black--text pb-0">Category Type</v-card-subtitle>
                <v-card-text>
                    <v-radio-group v-model="mcattype" column dense hide-details>
                        <v-radio label="Manual" value="manual"></v-radio>
                        <v-radio label="Smart" value="smart"></v-radio>
                    </v-radio-group>
                </v-card-text>
            </v-card>
            <v-card v-if="mcattype == 'manual'" outlined class="my-3 shadow-sm">
                <v-card-subtitle class="black--text">Products</v-card-subtitle>
                <v-card-text>
                    <div class="row">
                        <div class="col-md-6">
                            <v-text-field dense outlined prepend-inner-icon="mdi-magnify"></v-text-field>
                        </div>
                        <div class="col-md-2">
                            <v-btn class="text-none" outlined>Browse</v-btn>
                        </div>
                        <div class="col-md-4">
                            <v-select prefix="Sort:" dense outlined :items="sorts"></v-select>
                        </div>
                    </div>
                </v-card-text>
            </v-card>
            <v-card v-if="mcattype == 'smart'" outlined class="my-3 shadow-sm">
                <v-card-subtitle class="black--text pb-0">Conditions</v-card-subtitle>
                <v-card-text>
                    <div class="d-flex align-items-end mb-3">Product Must Match:
                        <v-radio-group v-model="acondition" row dense hide-details class="ms-2">
                            <v-radio label="All Conditions" value="all"></v-radio>
                            <v-radio label="Any Condition" value="any"></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <v-autocomplete dense outlined></v-autocomplete>
                        </div>
                        <div class="col-md-4">
                            <v-autocomplete dense outlined></v-autocomplete>
                        </div>
                        <div class="col-md-4">
                            <v-combobox dense outlined></v-combobox>
                        </div>
                    </div>
                    <v-btn outlined x-small class="text-none">
                        <v-icon small>mdi-plus</v-icon>
                        Add another condition
                    </v-btn>
                </v-card-text>
            </v-card>
        </div>
        <div class="col-md-3">
            <v-card outlined class="mb-3 shadow-sm">
                <v-card-actions>
                    <span class="body-2 fw-semibold">Publishing</span>
                    <v-spacer></v-spacer>
                    <v-btn small class="text-none" text color="primary">Manage</v-btn>
                </v-card-actions>
                <v-card-text class="pt-0">
                    <v-radio-group v-model="publish" column dense hide-details>
                        <v-radio label="Online Store" value="onlinestore"></v-radio>
                        <v-radio label="App Store" value="appstore"></v-radio>
                    </v-radio-group>
                </v-card-text>
            </v-card>
            <v-card outlined class="mb-3 shadow-sm">
                <v-card-actions>
                    <span class="body-2 fw-semibold">Image</span>
                    <v-spacer></v-spacer>
                    <span></span>
                </v-card-actions>
                <v-card-text class="pt-0">
                    <v-file-input hide-input prepend-icon="mdi-camera-outline" label="Add Photo">
                    </v-file-input>
                </v-card-text>
            </v-card>
            <v-card outlined class="mb-3 shadow-sm">
                <v-card-actions>
                    <span class="body-2 fw-semibold">Theme Template</span>
                    <v-spacer></v-spacer>
                    <span></span>
                </v-card-actions>
                <v-card-text class="pt-0">
                    <v-autocomplete v-model="cattemplate" dense :items="cattemplates" hide-details outlined>
                    </v-autocomplete>
                </v-card-text>
            </v-card>
        </div>
    </div>
</template>
<script>
export default {
    name:"AdminAddcat",
    data() {
        return{
            mcats:[],
            mcattype:'manual',
            acondition:'all',
            cattemplate:'Default Template',
            cattemplates:['Default Template'],
            sorts:['Best Selling','Product Title A-Z','Product Title Z-A','Highest Price','Lowest Price','Newest','Oldest','Manually']
        }
    }
}
</script>

<style scoped>

</style>
