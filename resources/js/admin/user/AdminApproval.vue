<template>
  <div class="page-margin-20-40">
    <v-row>
      <h2 class="text-h6 mb-1">Customers</h2>
    </v-row>

    <v-row>
      <v-col cols="12" class="mb-4">
        <v-card elevation="5">
          <v-row class="align-center">
            <v-col class="pt-0">
              <v-tabs v-model="activeTab" active-class="grey lighten-3" height="30">
                <v-tab class="text-none" style="font-size:12px">Approved</v-tab>
                <v-tab class="text-none" style="font-size:12px">Pending</v-tab>
                <v-tab class="text-none" style="font-size:12px">Declined</v-tab>
              </v-tabs>
            </v-col>

            <v-col v-if="selected.length" cols="auto" class="d-flex justify-end pt-0">
              <v-menu offset-y>
                <template #activator="{ on, attrs }">
                  <span class="mr-2 font-weight-medium text-caption">{{ selected.length }} selected</span>
                  <v-icon color="primary" v-bind="attrs" v-on="on" style="cursor:pointer;margin-right:5px">
                    mdi-dots-vertical
                  </v-icon>
                </template>
                <v-list dense>
                  <v-list-item v-if="activeTab===0" @click="openConfirmDialog('markDeclined')">
                    <v-list-item-title>Mark as Declined</v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="activeTab===1" @click="openConfirmDialog('markApproved')">
                    <v-list-item-title>Mark as Approved</v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="activeTab===1" @click="openConfirmDialog('markDeclined')">
                    <v-list-item-title>Mark as Declined</v-list-item-title>
                  </v-list-item>
                  <v-list-item v-if="activeTab===2" @click="openConfirmDialog('markApproved')">
                    <v-list-item-title>Mark as Approved</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-col>
          </v-row>

          <v-data-table dense v-model="selected" show-select item-key="id" :items="filteredUsers" :headers="userHeaders" :search="ssearch"
            :footer-props="{ 'items-per-page-options':[10,25,50,100], 'items-per-page-text':'Rows per page:'}">
            <template v-slot:top>
              <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" 
                placeholder="Search Name, Email, Spend"/>
            </template>
            <template #item.wallet="{ item }">
              <div style="cursor: pointer;" @click="openWalletDialog(item)">
                {{ parseFloat(item.wallet?.balance ?? 0).toFixed(2) }}
              </div>
            </template>
            <template #header.rep_code><div class="text-center">Rep&nbsp;code</div></template>
            <template #item.rep_code="{ item }">
              <div class="text-center">
                <span v-if="item.repcustomer?.rep_code">{{ item.repcustomer.rep_code }}</span>
                <v-chip v-else color="blue" outlined small @click="openRepDialog(item)">Add</v-chip>
              </div>
            </template>
            <template #header.user_tag_name><div class="text-center">Tag</div></template>
            <template #item.user_tag_name="{ item }">
              <div class="text-center">
                <span v-if="item.tagcustomer?.user_tag_name">{{ item.tagcustomer.user_tag_name }}</span>
                <v-chip v-else color="blue" outlined small @click="openTagDialog(item)">Add</v-chip>
              </div>
            </template>
            <template v-slot:item.total_spend="{ item }">
                £{{ item.total_spend }}
            </template>
            <!-- <template #header.admin_approval><div class="text-center">Status</div></template>
            <template #item.admin_approval="{ item }">
              <div class="text-center">
                <v-chip :color=" item.admin_approval==='Approved' ? 'green' : item.admin_approval==='Declined' ? 'red darken-1' : 'orange' " outlined pill small>
                  {{ item.admin_approval }}
                </v-chip>
              </div>
            </template> -->
            <!-- <template #header.user_details><div class="text-center">User Details</div></template>
            <template #item.user_details="{ item }">
              <div class="text-center">
                <v-chip color="blue" outlined pill small @click="openUserDialog(item)">View</v-chip>
                
              </div>
            </template> -->
            <template #header.action1><div class="text-center">Action</div></template>
            <template #item.action1="{ item }">
              <div class="text-center">
                <v-chip v-if="item.admin_approval==='Pending' || item.admin_approval==='Declined'" color="green" outlined pill small style="cursor:pointer" 
                  @click="changeStatus(item,'Approved')">Approve</v-chip>
              </div>
            </template>
            <template #header.action2><div class="text-center">Action</div></template>
            <template #item.action2="{ item }">
              <div class="text-center">
                <v-chip v-if="item.admin_approval==='Pending' || item.admin_approval==='Approved'" color="red" outlined pill small style="cursor:pointer"
                  @click="changeStatus(item,'Declined')">Decline</v-chip>
              </div>
            </template>
            <template #header.action3><div class="text-center">Action</div></template>
            <template #item.action3="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small style="cursor:pointer"@click="editItem(item)">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-if="selectedUser" v-model="editDialog" max-width="600px">
      <v-card elevation="5">
        <v-card-title class="headline grey lighten-2">
          Edit Profile
          <v-spacer/><v-icon @click="editDialog=false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="fsvalid" @submit.prevent="updateUser">
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.name" label="Name" :rules="[rules.required, rules.max255]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.email" label="Email" :error-messages="emailError" @blur="checkEmailUnique"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.mobile" label="Mobile" :rules="[rules.required, rules.max20]" type="tel" inputmode="tel"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.company_name" label="Company Name" :rules="[rules.required, rules.max255]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.address1" label="Address 1" :rules="[rules.required, rules.max255]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.address2" label="Address 2" :rules="[rules.max255]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.city" label="City" :rules="[rules.required, rules.max100]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.country" label="Country" :rules="[rules.required, rules.max100]"/></v-col>
                <v-col cols="12" sm="6"><v-text-field v-model="selectedUser.postcode" label="Postcode" :rules="[rules.required, rules.max20]"/></v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer/>
            <v-btn class="btn-32-text-12" type="submit" color="success" :loading="submitting" :disabled="!fsvalid || submitting || !emailUnique">Update</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="confirmDialog" max-width="400px">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm {{ actionLabel }}</v-card-title>
        <v-card-text>
          Are you sure you want to <strong>{{ actionLabel.toLowerCase() }}</strong>
          <strong>{{ selected.length }}</strong> selected users?
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" text @click="confirmDialog=false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" @click="executeBulkAction">Yes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- <v-dialog v-model="userDialog" max-width="500px">
      <v-card elevation="5">
        <v-card-title>
          <span class="text-h6">User Details</span>
          <v-spacer/><v-icon @click="userDialog=false" class="cursor-pointer">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-list dense>
            <v-list-item><v-list-item-content><strong>Name:</strong> {{ selectedUser.name }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Email:</strong> {{ selectedUser.email }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Phone:</strong> {{ selectedUser.mobile }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Company:</strong> {{ selectedUser.company_name }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Address:</strong> {{ selectedUser.address1 }} {{ selectedUser.address2 }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>City:</strong> {{ selectedUser.city }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Country:</strong> {{ selectedUser.country }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Postcode:</strong> {{ selectedUser.postcode }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Rep Code:</strong> {{ selectedUser.repcustomer?.rep_code || '—' }}</v-list-item-content></v-list-item>
            <v-list-item><v-list-item-content><strong>Status:</strong> {{ selectedUser.admin_approval }}</v-list-item-content></v-list-item>
          </v-list>
        </v-card-text>
      </v-card>
    </v-dialog> -->

    <v-dialog v-model="tagDialog" max-width="400px">
      <v-card elevation="5">
        <v-card-title>
          Assign Tag
          <v-spacer/>
          <v-icon @click="tagDialog=false">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-select v-model="selectedTagId" :items="userTags" item-value="user_tag_id" item-text="user_tag_name" label="Select Tag" dense outlined/>
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" color="primary" @click="assignTag">Assign</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="repDialog" max-width="400px">
      <v-card elevation="5">
        <v-card-title>
          Assign Rep
          <v-spacer/>
          <v-icon @click="repDialog=false">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-select v-model="selectedRepId" :items="reps" item-value="rep_id" item-text="rep_code" label="Select Rep Code" dense outlined/>
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" color="primary" @click="assignRep">Assign</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="walletDialog" max-width="500px">
      <v-card elevation="5">
        <v-card-title class="headline grey lighten-3">
          Update Wallet Balance
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" icon @click="walletDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-form v-model="walletFormValid" @submit.prevent="submitWalletUpdate">
          <v-card-text>
            <v-text-field v-model="walletForm.balance" label="Current Wallet Balance" readonly/>
            <v-radio-group v-model="walletForm.balancekey" row hide-details class="d-flex align-center mt-2">
              <v-radio label="Add Balance" value="add" dense class="mr-4"></v-radio>
              <v-radio label="Remove Balance" value="remove" dense></v-radio>
            </v-radio-group>
            <v-text-field v-model="walletForm.balancevalue" :label="walletForm.balancekey === 'add' ? 'Amount to Add' : 'Amount to Remove'" type="number" step="0.01"
              :rules="[
                v => v === '' || parseFloat(v) >= 0 || 'Only positive numbers allowed',
                v => v === '' || /^\d+(\.\d{1,2})?$/.test(v) || 'Up to 2 decimal places allowed'
              ]"/>
            <v-text-field v-model="walletForm.reference" dense placeholder="Enter Reference" label="Reference" :rules="[ v => !v || v.length <= 255 || 'Max 255 chars' ]"></v-text-field>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn class="btn-32-text-12" type="submit" color="success" :disabled="!walletFormValid || loading" :loading="loading">
              Update
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'AdminApproval',

  data () {
    return {
      users: [],
      activeTab: 0,

      editDialog: false,
      userDialog: false,
      repDialog: false,
      tagDialog: false,
      confirmDialog: false,

      fsvalid: false,
      submitting: false,
      rules: {
        required : v => !!v || 'Required',
        max255   : v => !v || v.length <= 255 || 'Max 255 chars',
        max100   : v => !v || v.length <= 100 || 'Max 100 chars',
        max20    : v => !v || v.length <= 20  || 'Max 20 chars',
      },

      selected: [],
      selectedUser: {
        id: null, 
        name: '', 
        email: '', 
        mobile: '',
        company_name: '', 
        address1: '', 
        address2: '',
        city: '', 
        country: '', 
        postcode: '',
        admin_approval: '', 
        repcustomer: {},
        tagcustomer: {},
      },
      emailError: '',
      emailUnique: true,
      selectedUserForRep: null,
      selectedUserForTag: null,
      selectedRepId: null,
      selectedTagId: null,

      ssearch: '',
      actionToConfirm: '',
      actionLabel: '',
      reps: [],
      userTags: [],

      walletDialog: false,
      walletFormValid: false,
      walletForm: {
        user_id: null,
        balance: 0,
        balancevalue: 0,
        balancekey: 'add',
        reference: '',
      },
      loading: false
    }
  },

  created () {
    this.loadUsers()
    this.loadReps()
    this.loadTags()
  },

  computed: {
    filteredUsers () {
      return this.users.filter(u => {
        if (this.activeTab === 0) return u.admin_approval === 'Approved'
        if (this.activeTab === 1) return u.admin_approval === 'Pending'
        if (this.activeTab === 2) return u.admin_approval === 'Declined'
        return true
      })
    },

    userHeaders () {
      const base = [
        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Wallet', value: 'wallet' },
        { text: 'Rep code', value: 'rep_code', sortable: false },
        
        // { text: 'Status', value: 'admin_approval', sortable: false, width: '140px' },
        // { text: 'User Details', value: 'user_details', sortable: false, width: '140px' },
      ]

      if (this.activeTab === 0) {
        // base.push({ text: 'Tag', value: 'user_tag_name', sortable: false })
        base.push({ text: 'Order', value: 'total_order' })
        base.push({ text: 'Spend', value: 'total_spend' })
        base.push({ text: 'Action', value: 'action2', sortable: false })
        base.push({ text: 'Edit', value: 'action3', sortable: false })
      } else {
        // base.push({ text: 'Tag', value: 'user_tag_name', sortable: false })
        base.push({ text: 'Action', value: 'action1', sortable: false })
        if (this.activeTab === 1) 
        base.push({ text: 'Action', value: 'action2', sortable: false })
        base.push({ text: 'Edit', value: 'action3', sortable: false })
      }

      return base
    }
  },

  watch: {
    activeTab () { this.selected = [] }
  },

  methods: {
    async loadUsers () {
      const { data } = await axios.get('/admin/users/vlist')
      this.users = data.users.map(u => ({
        ...u,
        repcustomer: u.repcustomer || {},
        tagcustomer: u.tagcustomer || {}
      }))
    },
    async loadReps () {
      const { data } = await axios.get('/admin/reps/vlist')
      this.reps = data.reps
    },
    // async loadTags () {
    //   const { data } = await axios.get('/admin/user-tags/vlist')
    //   this.userTags = data.userTags
    // },
    async checkEmailUnique() {
      if (!this.selectedUser.email) {
        this.emailError = 'Email is required';
        this.emailUnique = false;
        return;
      }

      try {
        const res = await axios.post('/admin/check-email', {
          email: this.selectedUser.email,
          id: this.selectedUser.id 
        });

        if (res.data.exists) {
          this.emailError = 'Email already exists';
          this.emailUnique = false;
        } else {
          this.emailError = '';
          this.emailUnique = true;
        }
      } catch (err) {
        this.emailError = 'Error checking email';
        this.emailUnique = false;
      }
    },
    editItem (item) {
      this.selectedUser = {
        ...item,
        repcustomer: item.repcustomer || {},
        tagcustomer: item.tagcustomer || {}
      }
      this.fsvalid = true
      this.editDialog = true
      this.emailUnique = true
      this.emailError = ''
    },

    async updateUser () {
      this.submitting = true
      try {
        await axios.put(`/admin/users/${this.selectedUser.id}`, this.selectedUser)
        this.$toast.success('Profile updated', { timeout: 500, });
        this.editDialog = false
        this.loadUsers()
      } catch {
        this.$toast.error('Failed to update profile')
      } finally {
        this.submitting = false
        this.emailUnique = true
      }
    },

    openUserDialog (item) {
      this.selectedUser = {
        ...item,
        repcustomer: item.repcustomer || {},
        tagcustomer: item.tagcustomer || {}
      }
      this.userDialog = true
    },

    async changeStatus (user, status) {
      try {
        await axios.post('/admin/users/update-approval', { user_ids: [user.id], bulkstatus: status })
        this.$toast.success(`Status updated to ${status}`, { timeout: 500, });
        this.loadUsers()
      } catch {
        this.$toast.error('Failed to update status')
      }
    },

    openConfirmDialog (action) {
      this.actionToConfirm = action
      this.actionLabel = action === 'markApproved' ? 'Mark as Approved' : 'Mark as Declined'
      this.confirmDialog = true
    },

    async executeBulkAction () {
      const ids = this.selected.map(u => u.id)
      const bulkstatus = this.actionToConfirm === 'markApproved' ? 'Approved' : 'Declined'
      try {
        await axios.post('/admin/users/update-approval', { user_ids: ids, bulkstatus })
        this.$toast.success(`${this.actionLabel} successful`, { timeout: 500, });
        this.loadUsers()
      } catch {
        this.$toast.error(`Failed to ${this.actionLabel.toLowerCase()}`)
      } finally {
        this.confirmDialog = false
        this.selected = []
      }
    },

    openRepDialog (user) {
      this.selectedUserForRep = user
      this.selectedRepId = null
      this.repDialog = true
    },
    async assignRep () {
      try {
        await axios.post('/admin/users/assign-rep', {
          user_id: this.selectedUserForRep.id,
          rep_id: this.selectedRepId,
        })
        this.$toast.success('Rep assigned successfully', { timeout: 500, });
        this.repDialog = false
        this.loadUsers()
      } catch {
        this.$toast.error('Failed to assign rep')
      }
    },
    openTagDialog (user) {
      this.selectedUserForTag = user
      this.selectedTagId = null
      this.tagDialog = true
    },
    async assignTag () {
      try {
        await axios.post('/admin/users/assign-tag', {
          user_id: this.selectedUserForTag.id,
          user_tag_id: this.selectedTagId,
        })
        this.$toast.success('Tag assigned successfully', { timeout: 500, });
        this.tagDialog = false
        this.loadUsers()
      } catch {
        this.$toast.error('Failed to assign tag')
      }
    },
    openWalletDialog (item) {
      this.walletForm = {
        user_id: item.id,
        balance: item.wallet?.balance ?? 0,
        balancevalue: 0,
        balancekey: 'add',
        reference: ''
      }
      this.walletFormValid = true
      this.walletDialog = true
    },
    async submitWalletUpdate () {
      this.loading = true
      try {
        await axios.post('/admin/users/update-wallet', this.walletForm)
        this.$toast.success('Wallet updated successfully', { timeout: 500, });
        this.walletDialog = false
        this.loadUsers() 
      } catch (error) {
        this.$toast.error('Failed to update wallet')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style>
.v-input__slot {
    min-height: 30px !important;
}
.v-input {
  font-size: 12px !important;
}
td.text-start {
    font-size: 13px !important;
}
.Vue-Toastification__toast {
  border-radius: 5px;
  padding: 10px 17px;
  font-size: 10px !important;
  min-width: 260px;
  min-height: 40px;
}
</style>
