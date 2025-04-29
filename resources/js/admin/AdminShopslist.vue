<template>
    <div>
        <v-row>
            <v-col cols="12" md="6">
                <h2 class="text-h6 fw-semibold">Shops</h2>
            </v-col>
            <v-col cols="12" md="6" class="text-end">
                <v-btn color="secondary" small class="text-none font-weight-bold"
                       @click="addSdialog = true">Add Shop</v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="8">
                <v-text-field v-model="ssearch" dense hide-details outlined prepend-inner-icon="mdi-magnify"
                              placeholder="Search Shop"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-autocomplete v-model="statusFilter" :items="[ { text: 'All', value: null }, { text: 'Active', value: 1 }, { text: 'Inactive', value: 0 } ]" 
                    item-text="text" item-value="value" outlined dense label="Status" hide-details @change="filterByStatus"></v-autocomplete>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="12">
                <v-card outlined>
                    <v-data-table :items="ashops" :headers="ashopsHeaders" :search="ssearch">
                        <template v-slot:item.suser="{item}">
                            <div v-if="item.suser != null">
                                <div>{{item.suser.name}} as {{item.suser.user_role}}</div>
                                <div>{{item.suser.email}}</div>
                            </div>
                            <div v-else>
                                <v-btn color="error" text small>Not Selected</v-btn>
                            </div>
                        </template>
                        <template v-slot:item.shop_status="{item}">
                            <v-btn
                                :color="item.shop_status === 1 ? 'success' : 'error'"
                                x-small
                                @click="toggleShopStatus(item)"
                            >
                                {{ item.shop_status === 1 ? 'Active' : 'Inactive' }}
                            </v-btn>
                        </template>
                        <template v-slot:item.actions="{item}">
                            <v-btn icon color="primary" outlined>
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <span v-if="item.suser !== null"></span>
                            <span v-else>
                                <v-btn color="error" outlined small class="text-none" @click="editItem(item)">Existing Owner</v-btn>
                                <v-btn color="secondary" outlined small class="text-none" @click="addUser(item)">New Owner</v-btn>
                            </span>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
        <v-dialog v-model="addSdialog" max-width="400">
            <v-card>
                <v-card-title>
                    <span>Add Shop</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addSdialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form @submit.prevent="addNewShop" v-model="fsvalid">
                    <v-card-text>
                        <v-text-field v-model="defaultItem.shop_name" :rules="snameRule"
                                      label="Shop Name"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn type="submit" :disabled="!fsvalid" color="success" small>Add Shop</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog v-model="addEOwnerDialog" max-width="400">
            <v-card>
                <v-card-title>
                    <span>Add Existing Owner</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addEOwnerDialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="eovalid" @submit.prevent="addExistingOwner">
                    <v-card-text>
                        <v-autocomplete dense label="Shop users" v-model="auser_id" :rules="auserRule" clearable
                                        :items="ausers" item-text="name" item-value="id"></v-autocomplete>
                        <v-text-field v-model="editedItem.shop_name" readonly disabled hide-details></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-btn type="submit" color="success" small :disabled="!eovalid">Add Owner</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog v-model="addNOwnerDialog" max-width="400">
            <v-card>
                <v-card-title>
                    <span>Add New User</span>
                    <v-spacer></v-spacer>
                    <v-icon @click="addNOwnerDialog = false">mdi-close</v-icon>
                </v-card-title>
                <v-form v-model="enovalid" @submit.prevent="addNewUser">
                    <v-card-text>
                        <v-text-field v-model="nuser.name" label="Name" :rules="nnameRule" counter></v-text-field>
                        <v-text-field v-model="nuser.email" label="Email" :rules="nemailRule" counter></v-text-field>
                        <v-text-field type="password" v-model="nuser.password" label="Password" :rules="npasswordRule" counter></v-text-field>
                        <v-text-field v-model="editedItem.shop_name" readonly disabled hide-details></v-text-field>
                    </v-card-text>
                    <v-card-actions class="justify-center">
                        <v-btn type="submit" color="success" small :disabled="!enovalid">New User</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name:"AdminShopslist",
    data(){
        return{
            ssearch:'',
            fsvalid:false,
            eovalid:false,
            enovalid:false,
            addSdialog:false,
            addEOwnerDialog:false,
            addNOwnerDialog:false,
            statusFilter: null,
            ashops:[],
            allShops: [],
            ausers:[],
            auser_id:'',
            auserRule:[(v) => !!v || "Name is required"],
            ashopsHeaders:[
                {text:'ID',value:'shop_id'},
                {text:'Shop',value:'shop_name'},
                {text:'User',value:'suser'},
                {text:'Status',value:'shop_status'},
                {text:'Actions',value:'actions'},
            ],
            editedIndex:-1,
            defaultItem:{
                shop_name:'',
                shop_status:'',
            },
            editedItem:{
                shop_id:'',
                shop_name:'',
                shop_status:'',
            },
            snameRule:[
                (v) => !!v || "Shop Name is required",
                (v) => (v && v.length >= 3) || "Name must be at least 3 characters",
                (v) => !this.ashops.some((shop) => shop.shop_name === v) || "Shop already exists",
            ],
            nuser:{
                name:'',
                email:'',
                password:'',
            },
            nnameRule:[
                (v) => !!v || "Name is required",
                (v) => (v && v.length >= 3) || "Name must be at least 3 characters",
            ],
            nemailRule:[
                (v) => !!v || "email is required",
                (v) => (v && v.length >= 7) || "email must be at least 7 characters",
                (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || "Invalid email format",
                (v) => !this.ausers.some((user) => user.email === v) || "User Email already exists",
            ],
            npasswordRule:[
                (v) => !!v || "password is required",
                (v) => (v && v.length >= 5) || "password must be at least 5 characters",
            ],
        }
    },
    created() {
        this.getAllShops();
    },
    methods:{
        getAllShops(){
            axios.get('/admin/shops/vlist')
                .then((resp)=>{
                    this.ashops = resp.data.shops;
                    this.allShops = resp.data.shops;
                    this.ausers = resp.data.ausers;
                })
        },
        addNewShop(){
            const nshop = {
                shop_name:this.defaultItem.shop_name
            }
            axios.post('/admin/shop/add',nshop)
                .then((resp)=>{
                    this.getAllShops();
                    this.addSdialog = false;
                    console.log('Added',resp.data)
                })
        },
        editItem(item) {
            this.editedIndex = this.ashops.indexOf(item);
            this.editedItem = Object.assign({},item);
            this.addEOwnerDialog = true;
        },
        addUser(item) {
            this.editedIndex = this.ashops.indexOf(item);
            this.editedItem = Object.assign({},item);
            this.addNOwnerDialog = true;
        },
        addExistingOwner(){
            const eowner = {
                user_id:this.auser_id,
                shop_id:this.editedItem.shop_id,
            }
            axios.post('/admin/shop/add/owner',eowner)
                .then((resp)=>{
                    this.getAllShops();
                    this.addEOwnerDialog = false;
                    console.log(resp.data);
                })
        },
        addNewUser(){
            const anuser = {
                name:this.nuser.name,
                email:this.nuser.email,
                password:this.nuser.password,
                shop_id:this.editedItem.shop_id
            }
            axios.post('/admin/shop/add/nuser',anuser)
                .then((resp)=>{
                    this.getAllShops();
                    this.addNOwnerDialog = false;
                    console.log(resp.data);
                })
        },
        filterByStatus() {
            if (this.statusFilter === null) {
            this.ashops = this.allShops;
            } else {
            this.ashops = this.allShops.filter(shop => shop.shop_status === this.statusFilter);
            }
        },
        async toggleShopStatus(item) {
            const newStatus = item.shop_status === 1 ? 0 : 1;

            try {
            const res = await axios.post(`/admin/shop-toggle-status`, {
                shop_id: item.shop_id,
                status: newStatus
            });

            if (res.data.success) {
                item.shop_status = newStatus; // update UI immediately
            } 
            } catch (err) {
            console.error(err);
            this.$toast.error("Error occurred");
            }
        }
    }
}
</script>

<style scoped>

</style>
