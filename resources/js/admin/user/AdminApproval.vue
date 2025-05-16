<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="ssearch"
          clearable
          dense
          hide-details
          outlined
          prepend-inner-icon="mdi-magnify"
          placeholder="Search name"
        />
      </v-col>
    </v-row>

    <v-row> 
      <v-col cols="12">
        <v-card outlined>
          <v-tabs v-model="activeTab" class="mb-2" active-class="grey lighten-3" height="30">
            <v-tab class="text-none">Pending</v-tab>
            <v-tab class="text-none">Approved</v-tab>
            <v-tab class="text-none">Declined</v-tab>
          </v-tabs>
            <v-data-table :items="filteredUsers" :headers="userHeaders" :search="ssearch" :footer-props="{
                        'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:'}">
          
                <template v-slot:item.admin_approval="{ item }">
                    <v-chip :color="item.admin_approval === 'Approved' ? 'green' : item.admin_approval === 'Declined' ? 'red darken-1' : 'orange'" class="ma-1" outlined pill small>
                        {{
                        item.admin_approval === 'Approved'
                            ? 'Approved'
                            : item.admin_approval === 'Declined'
                            ? 'Declined'
                            : 'Pending'
                        }}
                    </v-chip>
                </template>
                <template v-slot:item.user_details="{ item }">
                    <v-chip :color="'blue'" class="ma-1" outlined pill small @click="openUserDialog(item)">
                        View
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <v-icon v-if="item.admin_approval === 'Pending'" color="green" class="mr-2" @click="changeStatus(item, 'Approved')" >mdi-check</v-icon>
                    <v-icon v-if="item.admin_approval === 'Pending' || item.admin_approval === 'Approved'" color="red" class="mr-2" @click="changeStatus(item, 'Declined')" >mdi-close</v-icon>
                    <v-icon v-if="item.admin_approval === 'Declined'" color="green" @click="changeStatus(item, 'Approved')" >mdi-check</v-icon>
                </template>
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="userDialog" max-width="500">
        <v-card>
            <v-card-title>
            <span class="text-h6">User Details</span>
            <v-spacer></v-spacer>
            <v-icon @click="userDialog = false" class="cursor-pointer">mdi-close</v-icon>
            </v-card-title>
            <v-card-text>
            <v-list dense v-if="selectedUser">
                <v-list-item><v-list-item-content><strong>Name:</strong> {{ selectedUser.name }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Email:</strong> {{ selectedUser.email }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Phone:</strong> {{ selectedUser.mobile }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Company Name:</strong> {{ selectedUser.company_name }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Address:</strong> {{ selectedUser.address1 }} {{ selectedUser.address2 }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>City:</strong> {{ selectedUser.city }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Country:</strong> {{ selectedUser.country }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Postcode:</strong> {{ selectedUser.postcode }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Rep Code:</strong> {{ selectedUser.rep_code ?? '' }}</v-list-item-content></v-list-item>
                <v-list-item><v-list-item-content><strong>Status:</strong> {{ selectedUser.admin_approval }}</v-list-item-content></v-list-item>
            </v-list>
            </v-card-text>
        </v-card>
    </v-dialog>

  </div>
</template>

<script>
export default {
  name: "AdminApproval",
  data() {
    return {
      users: [],
      activeTab: 0,
      userDialog: false,
      selectedUser: null,
      ssearch: '',
      userHeaders: [
        { text: 'ID', value: 'id' },
        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Status', value: 'admin_approval', sortable: false },
        { text: 'User Details', value: 'user_details', sortable: false },
        { text: 'Action', value: 'actions', sortable: false },
      ],
    };
  },
  computed: {
        filteredUsers() {
            return this.users.filter(user => {
            switch (this.activeTab) {
                case 0: return user.admin_approval === 'Pending';
                case 1: return user.admin_approval === 'Approved';
                case 2: return user.admin_approval === 'Declined';
                default: return true;
            }
            });
        }
    },
  created() {
    this.getusers();
  },
  methods: {
    getusers() {
      axios.get('/admin/users/vlist')
        .then((resp) => {
          this.users = resp.data.users;
        });
    },
    openUserDialog(user) {
        this.selectedUser = user;
        this.userDialog = true;
    },
    changeStatus(user, newStatus) {
        user.admin_approval = newStatus;

        axios
            .post("/admin/users/update-approval", {
            user_id: user.id,
            admin_approval: newStatus,
            })
            .then(() => {
            this.$toast?.success(`Status updated to ${newStatus}.`);
            })
            .catch(() => {
            this.$toast?.error("Failed to update status.");
            });
    }
  }
}
</script>


<style scoped>
button.v-icon.notranslate.ml-2.v-icon--link.mdi.mdi-eye.theme--light.primary--text {
    opacity: 0;
}

tr:hover button.v-icon.notranslate.ml-2.v-icon--link.mdi.mdi-eye.theme--light.primary--text {
    opacity: 1;
}
</style>
