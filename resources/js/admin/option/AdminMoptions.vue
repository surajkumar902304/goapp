<template>
    <div>
        <v-row>
            <v-col cols="12" md="10">
                <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify" placeholder="Search name"/>
            </v-col>
            <v-col cols="12" md="2" class="text-end mt-1">
                <v-btn color="secondary" small @click="addDialog = true" class="text-none">
                    Add Option Name</v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="12">
                <v-card outlined>
                    <v-data-table :items="moptions" :headers="moptionHeaders" :search="ssearch">
                        <template v-slot:item.actions="{item}">
                            <v-btn color="primary" small  @click="editItem(item)"> <v-icon small>mdi-pencil-outline</v-icon>Edit</v-btn>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
        <v-dialog max-width="400px" v-model="addDialog">
            <v-card>
                <v-card-actions>
                    <span>Add Option Name</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addDialog = false">mdi-close</v-icon>
                </v-card-actions>
                <v-form v-model="fvalid" @submit.prevent="addMoption">
                    <v-card-text>
                        <v-text-field v-model="defaultItem.moption_name" dense :rules="nameRule"
                                      placeholder="Colour, Size etc"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-btn type="submit" color="success" small :disabled="!fvalid">Add Option</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog max-width="400px" v-model="editDialog">
            <v-card>
                <v-card-actions>
                    <span>Edit Option Name</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="editDialog = false">mdi-close</v-icon>
                </v-card-actions>
                <v-form v-model="evalid" @submit.prevent="editMoption">
                    <v-card-text>
                        <v-text-field v-model="editedItem.moption_name" dense :rules="nameRule"
                                      placeholder="Colour, Size etc"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-btn type="submit" color="success" small :disabled="!evalid">Update Option</v-btn>
                    </v-card-actions>
                </v-form>
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
                {text:'ID',value:'moption_id'},
                {text:'Name',value:'moption_name'},
                {text:'Actions',value:'actions', sortable: false },
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
            ]
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
                    this.$toast?.success('Option added successfully!');
                })
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
                    this.$toast?.success('Option updated successfully!');
                })
        },
        editItem(item){
            this.editedIndex = this.moptions.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.editDialog = true;
        }
    }
}

</script>

<style scoped>

</style>
