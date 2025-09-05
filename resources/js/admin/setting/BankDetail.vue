<template>
<div class="page-margin-20-40 page-bank-detail">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Bank Details</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openDialog">
              Add Bank
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table :headers="bankdetailsHeaders" :items="bankdetails" :search="ssearch" 
                    :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
                    <template v-slot:top>
                      <v-row dense class="mx-1 pb-1">
                        <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search all"/>
                      </v-row>
                    </template>
                    <template #item.company_name="{ item }">
                        <span>{{ item.company_name }}</span>
                    </template>
                    <template #item.bank_name="{ item }">
                        <span>{{ item.bank_name }}</span>
                    </template>
                    <template #item.code="{ item }">
                        <span>{{ item.account_number }}</span>
                    </template>
                    <template #item.sort_code="{ item }">
                        <span>{{ item.sort_code }}</span>
                    </template>
                    <template #item.is_active="{ item }">
                        <v-switch v-model="item.is_active" :input-value="item.is_active === 1" @change="toggleStatus(item)" dense inset style="transform: scale(0.75);"></v-switch>
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
                            <v-chip color="red" class="white--text" outlined pill small @click="confirmDelete(item)" style="cursor: pointer;" >
                                <v-icon small left>mdi-delete</v-icon>Delete
                            </v-chip>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>

    <v-dialog v-model="addSdialog" max-width="600" @update:model-value="onDialogToggle">
      <v-card elevation="5">
        <v-card-title>
          <span>{{ editedIndex === -1 ? 'Add Bank Detail' : 'Edit Bank Detail' }}</span>
          <v-spacer></v-spacer>
          <v-icon @click="addSdialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="fsvalid" @submit.prevent="saveBankDetail">
          <v-card-text>
            <v-text-field v-model="defaultItem.company_name" @input="defaultItem.company_name = defaultItem.company_name.toUpperCase()" 
              :rules="companynameRules" label="Company Name"/>
            <v-text-field v-model="defaultItem.bank_name" @input="defaultItem.bank_name = defaultItem.bank_name.toUpperCase()" 
              :rules="banknameRules" label="Bank Name"/>
            <v-text-field v-model="defaultItem.account_number" :rules="accountnumberRules" label="Account Number"/>
            <v-text-field v-model="defaultItem.sort_code" :rules="sortcodeRules" label="Sort Code"/>
            <v-text-field v-model="defaultItem.note" :rules="noteRules" label="Note"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fsvalid || submitting">
              {{ editedIndex === -1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">
          Confirm Delete
        </v-card-title>
        <v-card-text>
          Are you sure you want to delete this Bank Detail?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialog = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoading" :disabled="deleteLoading" @click="performDelete">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'BankDetail',
  data() {
    return {
      ssearch: '',
      bankdetails: [],
      bankdetailsHeaders: [
        { text: 'Company name', value: 'company_name' },
        { text: 'Bank name', value: 'bank_name' },
        { text: 'Account no', value: 'account_number' },
        { text: 'Sort code', value: 'sort_code' },
        { text: 'Status', value: 'is_active' },
        { text: 'Action', value: 'actions1', sortable: false },
        { text: 'Action', value: 'actions2', sortable: false },
      ],

      addSdialog: false,
      editedIndex: -1,
      fsvalid: false,
      submitting: false,

      defaultItem: {
        bank_detail_id: null,
        company_name: '',
        bank_name: '',
        account_number: '',
        sort_code: '',
        note: '',
      },
      companynameRules: [
        v => !!v || 'Company Name is required',
        v => (v && v.length <=255) || 'Company Name must be less than 255 characters',
      ],
      banknameRules: [
        v => !!v || 'Bank Name is required',
        v => (v && v.length <=255) || 'Bank Name must be less than 255 characters',
      ],
      accountnumberRules: [
        v => !!v || 'Account Number is required',
        v => (v && v.length <=255) || 'Account Number must be less than 255 characters',
      ],
      sortcodeRules: [
        v => !!v || 'Sort Code is required',
        v => (v && v.length <=255) || 'Sort Code must be less than 255 characters',
      ],
      noteRules: [
        v => !!v || 'Note is required',
        v => (v && v.length <=255) || 'Note must be less than 255 characters',
      ],
      deleteDialog: false,
      bankdetailToDelete: null,
      deleteLoading: false,
    }
  },
  created() {
    this.getAllbankdetails()
  },
  watch: {
    addSdialog(val) {
      if (!val) this.submitting = false
    }
  },
  methods: {
    getAllbankdetails() {
      axios.get('/admin/bank-detail/vlist').then(res => {
        this.bankdetails = res.data.bankdetails;
      })
      .catch(err => {
        console.error(err)
      })
    },
    onDialogToggle(open) {
      if (!open) {
      this.defaultItem = 
      { 
        bank_detail_id: null, 
        company_name: '', 
        bank_name: '', 
        account_number: '', 
        sort_code: '', 
        note: '', 
      };
      this.fsvalid = false;
      this.submitting = false;
      this.editedIndex = -1;
      }
    },

    openDialog() {
      this.defaultItem = {
        bank_detail_id: null,
        company_name: '',
        bank_name: '',
        account_number: '',
        sort_code: '',
        note: '',
      }
      this.editedIndex = -1
      this.fsvalid = false
      this.addSdialog = true
    },

    editItem(item) {
      this.defaultItem = {
        bank_detail_id: item.bank_detail_id,
        company_name: item.company_name,
        bank_name: item.bank_name,
        account_number: item.account_number,
        sort_code: item.sort_code,
        note: item.note,
      }
      this.editedIndex = item.bank_detail_id
      this.fsvalid = true
      this.addSdialog = true
    },

    async saveBankDetail() {
      this.submitting = true;

      let expiresAtValue = this.defaultItem.expires_at;

      if (expiresAtValue) {
        expiresAtValue = `${expiresAtValue} 00:00:00`;
      } else {
        expiresAtValue = null;
      }

      const payload = {
        company_name: this.defaultItem.company_name.toUpperCase(),
        bank_name: this.defaultItem.bank_name.toUpperCase(),
        account_number: this.defaultItem.account_number,
        sort_code: this.defaultItem.sort_code,
        note: this.defaultItem.note,
      };

      if (this.editedIndex !== -1) {
        payload.bank_detail_id = this.editedIndex;
      }

      const url = this.editedIndex === -1 ? '/admin/bank-detail/add' : '/admin/bank-detail/update';

      try {
        await axios.post(url, payload, {
          headers: { 'Content-Type': 'application/json' }
        });
        this.$toast.success(
          this.editedIndex === -1 ? 'Bank Detail added successfully!' : 'Bank Detail updated successfully!',
          { timeout: 500 }
        );
        this.getAllbankdetails();
        this.addSdialog = false;
      } catch (error) {
      } finally {
        this.submitting = false;
      }
    },
    async toggleStatus(item) {
      try {
          await axios.post(`/admin/bank-detail/status-toggle/${item.bank_detail_id}`, {
              is_active: item.is_active
          });
          this.$toast?.success('Bank Detail Status updated', { timeout: 500 });
      } catch (error) {
          console.error("Failed to toggle status", error);
          this.$toast?.error('Failed to update status', { timeout: 500 });
      }
    },
    confirmDelete(item) {
      this.bankdetailToDelete = item
      this.deleteDialog = true
    },
    async performDelete() {
      if (!this.bankdetailToDelete) return
      this.deleteLoading = true
      try {
        await axios.post('/admin/bank-detail-delete', { bank_detail_id: this.bankdetailToDelete.bank_detail_id })
        this.$toast.success('Bank Detail deleted successfully!', { timeout: 500 })
        this.getAllbankdetails()
      } catch (err) {
        console.error(err)
        this.$toast.error('Failed to delete Bank Detail.', { timeout: 2000 })
      } finally {
        this.deleteLoading = false
        this.deleteDialog = false
        this.bankdetailToDelete = null
      }
    },

  }
}
</script>

<style scoped>
.v-input {
  font-size: 12px !important;
}
</style>
<style>
.page-bank-detail .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>