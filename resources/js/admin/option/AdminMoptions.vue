<template>
    <div class="page-margin-20-40 page-product-option">
        <v-container fluid class="pt-0">
            <v-row class="mt-0 pt-0">
                <v-col cols="12" md="11" class="p-0">
                <h2 class="text-h6 mb-1">Product-Options</h2> 
                </v-col>

                <v-col cols="12" md="1" class="p-0 ps-2 text-end">
                    <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; border: 1px solid #1976d2 !important;" @click="addDialog = true">
                        Add Options
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
        
        <v-row class="mt-0">
            <v-col cols="12" md="12">
                <v-card elevation="5">
                    <v-data-table :items="moptions" :headers="moptionHeaders" :search="ssearch" :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
                        <template v-slot:top>
                            <v-row dense class="mx-1 pb-1">
                                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search name"/>
                            </v-row>
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
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
        <v-dialog max-width="400px" v-model="addDialog">
            <v-card elevation="5">
                <v-card-actions>
                    <span>Add Option </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addDialog = false">mdi-close</v-icon>
                </v-card-actions>
                <v-form v-model="fvalid" @submit.prevent="addMoption">
                    <v-card-text>
                        <v-text-field v-model="defaultItem.moption_name" dense :rules="nameRule" placeholder="Colour, Size etc"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fvalid">Add Option</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog max-width="400px" v-model="editDialog">
            <v-card elevation="5">
                <v-card-actions>
                    <span>Edit Option Name</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="editDialog = false">mdi-close</v-icon>
                </v-card-actions>
                <v-form v-model="evalid" @submit.prevent="editMoption">
                    <v-card-text>
                        <v-text-field v-model="editedItem.moption_name" dense :rules="nameRule" placeholder="Colour, Size etc"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-spacer></v-spacer>
                        <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!evalid">Update Option</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <!-- Delete dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card elevation="5">
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this Option?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
export default {
    name:"AdminMoptions",
    data(){
        return{
            ssearch: '',
            fvalid:false,
            evalid:false,
            addDialog:false,
            editDialog:false,
            moptions:[],
            moptionHeaders:[
                { text: 'Name', value:'moption_name'},
                { text: 'Action', value: 'actions1', sortable: false },
                { text: 'Action', value: 'actions2', sortable: false },
            ],
            editedIndex:-1,
            defaultItem:{
                moption_name:''
            },
            editedItem:{
                moption_id:'',
                moption_name:''
            },
            nameRule:[
                (v) => !!v || "Name is required",
                (v) => (v && v.length >= 3) || "Name must be at least 3 characters",
                (v) => /^[a-zA-Z\s]+$/.test(v) || "Name can only contain letters and spaces",
                v => !this.moptions.some(s =>
                    s.moption_name.toLowerCase().trim() === (v || '').toLowerCase().trim() &&
                    s.moption_id !== this.moption_id
                ) || 'Option already exists'
            ],
            deleteDialog: false,
            optionToDelete: null,
            deleteLoading: false,
        }
    },
    created() {
        this.getMoptions();
    },
    methods:{
        getMoptions(){
            axios.get('/admin/moptions/vlist')
                .then((resp)=>{
                    this.moptions = resp.data.moptions;
                })
        },
        addMoption(){
            const mop = {
                'moption_name':this.defaultItem.moption_name
            }
            axios.post('/admin/moption/add',mop)
                .then((resp)=>{
                    this.getMoptions();
                    this.addDialog = false;
                    this.defaultItem.moption_name = '';
                    this.$toast?.success('Option added successfully!', {
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
        editMoption(){
            const emop = {
                'moption_id':this.editedItem.moption_id,
                'moption_name':this.editedItem.moption_name
            }
            axios.post('/admin/moption/update',emop)
                .then((resp)=>{
                    this.getMoptions();
                    this.editDialog = false;
                    this.$toast?.success('Option updated successfully!', { timeout: 500 })
                })
                .catch(() => {
                    this.$toast.error('Something went wrong while saving the brand.', { timeout: 500 })
                })
                .finally(() => {
                    this.submitting = false;
                });
        },
        editItem(item){
            this.editedIndex = this.moptions.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.editDialog = true;
        },
        confirmDelete(item) {
            this.optionToDelete = item;
            this.deleteDialog = true;
        },
        async performDelete() {
            if (!this.optionToDelete) return;
            this.deleteLoading = true;
            try {
            await axios.post('/admin/moption-delete', {
                moption_id: this.optionToDelete.moption_id
            });
            this.$toast?.success('Option deleted successfully!', { timeout: 500 })
            this.getMoptions(); 
            } catch (err) {
                console.error(err);
            this.$toast?.error('Failed to delete product');
            } finally {
                this.deleteLoading = false;
                this.deleteDialog = false;
                this.optionToDelete = null;
            }
        }
    }
}

</script>

<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
<style>
.page-product-option .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>