<template>
    <div>
        <v-row>
            <v-col cols="12" md="6">
                <h2 class="text-h6 fw-semibold">Users</h2>
            </v-col>
            <v-col cols="12" md="6" class="text-end">
                <v-text-field v-model="ssearch" dense hide-details outlined prepend-inner-icon="mdi-magnify" 
                    placeholder="Search name"/>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="12">
                <v-card outlined>
                    <v-data-table :items="users" :headers="userHeaders" :search="ssearch">
                        <template v-slot:item.admin_approval="{ item }">
                            <v-switch v-model="item.admin_approval" @change="updateApproval(item)" inset
                                :true-value="1" :false-value="0" ></v-switch>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
<script>
export default {
    name:"AdminApproval",
    data(){
        return{
            users:[],
            ssearch: '',
            userHeaders:[
                {text:'ID',value:'id'},
                {text:'Name',value:'name'},
                {text:'Email',value:'email'},
                {text:'Approval',value:'admin_approval'},
            ],
        }
    },
    created() {
        this.getusers();
    },
    methods:{
        getusers(){
            axios.get('/admin/users/vlist')
                .then((resp)=>{
                    this.users = resp.data.users;
                })
        },
        updateApproval(user) {
            axios.post('/admin/users/update-approval', {
            user_id: user.id,
            admin_approval: user.admin_approval
            })
            .then(() => {
            this.$toast?.success("User approval updated");
            })
            .catch(() => {
            this.$toast?.error("Failed to update approval");
            });
        }
    }
}

</script>

<style scoped>

</style>
