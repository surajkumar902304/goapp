<template>
  <div class="page-margin-20-40">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Reps</h2> 
        </v-col>

        <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="font-weight: bold; color: #1976d2; background-color: white !important; 
            border: 1px solid #1976d2 !important;" @click="openDialog()">Add Rep
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table :items="reps" :headers="headers" :search="ssearch" item-key="rep_id" :footer-props="{
              'items-per-page-options':[10,25,50,100], 'items-per-page-text':'Rows per page:'}">
            <template v-slot:top>
              <v-row dense class="mx-1 pb-1">
                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search all"/>
              </v-row>
            </template>
            <template v-slot:item.total_commission="{ item }">
                £{{ item.total_commission }}
            </template>
            <template #header.actions1>
              <div class="text-center">Action</div>
            </template>
            <template #item.actions1="{ item }">
              <div class="text-center">
                <v-chip outlined pill small color="primary"
                        @click="openDialog(item)">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
            <template #header.actions2>
              <div class="text-center">Action</div>
            </template>
            <template #item.actions2="{ item }">
              <div class="text-center">
                <v-chip outlined pill small color="red"
                        @click="confirmDelete(item)">
                  <v-icon small left>mdi-delete</v-icon>Delete
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="500">
      <v-card elevation="5">
        <v-card-title>
          {{ editMode ? 'Edit Rep' : 'Add Rep' }}
          <v-spacer/><v-icon @click="dialog=false">mdi-close</v-icon>
        </v-card-title>

        <v-form ref="repForm" v-model="valid" @submit.prevent="saveRep">
          <v-card-text>
            <v-text-field v-model="form.name" label="Name" :rules="nameRules" :error-messages="backendErrors.name"/>
            <v-text-field v-model="form.email" label="E-mail" :rules="emailRules" :error-messages="backendErrors.email"/>
            <v-text-field v-model="form.mobile" label="Mobile" :rules="mobileRules" :error-messages="backendErrors.mobile"/>
            <v-text-field v-model="form.rep_code" label="Rep Code" :rules="repCodeRules" :error-messages="backendErrors.rep_code"/>
            <v-text-field v-model="form.commission_percent" label="Commission (%)" type="number" :rules="commissionRules" :error-messages="backendErrors.commission_percent"/>
            <v-text-field v-model="form.password" label="Password" type="password" autocomplete="new-password" :rules="passwordRules" :error-messages="backendErrors.password"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer/>
            <v-btn class="btn-32-text-12" type="submit" color="success" :disabled="!valid">
              {{ editMode ? 'Update' : 'Save' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">Confirm Delete</v-card-title>
        <v-card-text>
          Deleting this rep will permanently remove all references.<br>
          Are you sure?
        </v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn class="btn-32-text-12" text @click="deleteDialog=false">Cancel</v-btn>
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
  data () {
    return {
      ssearch : '',
      reps    : [],
      headers : [
        { text:'Customer name',  value:'name' },
        { text:'Email',         value:'email' },
        { text:'Rep code',       value:'rep_code' },
        { text:'Commission (%)', value:'commission_percent' },
        { text:'Total commission', value:'total_commission' },
        { text:'', value:'actions1', sortable:false ,width:90 },
        { text:'', value:'actions2', sortable:false ,width:90 },
      ],

      dialog   : false,
      editMode : false,
      valid    : false,

      form : {
        rep_id            : null,
        name              : '',
        email             : '',
        mobile            : '',
        rep_code          : '',
        commission_percent: '',
        password          : ''
      },
      backendErrors : {},      
      deleteDialog : false,
      deleteLoading: false,
      repToDelete  : null
    }
  },
  mounted () { 
    this.fetchReps() 
  },
  computed : {
    nameRules () { 
      return [v => !!v || 'Name is required'] 
    },
    emailRules () {
      return [
        v => !!v || 'E-mail is required',
        v => /.+@.+\..+/.test(v) || 'E-mail must be valid'
      ]
    },
    mobileRules () {
       return [
        v => !!v || 'Mobile number is required',
        v => !isNaN(v) || 'Must be numeric',
        v => (v + '').length <= 15 || 'Maximum 15 digits'
      ]
    },
    repCodeRules () { 
      return [v => !!v || 'Rep Code is required'] 
    },
    commissionRules () {
      return [
        v => !!v || 'Commission is required',
        v => !isNaN(v) || 'Must be a number',
        v => (v >= 0 && v <= 100) || '0 – 100 only'
      ]
    },
    passwordRules () {
      return this.editMode
        ? [
            v => (v === '' || v.length >= 6) || 'Minimum 6 characters'
          ]
        : [
            v => !!v || 'Password is required',
            v => v.length >= 6 || 'Minimum 6 characters'
          ]
    }
  },
  methods : {
    fetchReps () {
      axios.get('/admin/reps/vlist')
        .then(res => { this.reps = res.data.reps ?? [] })
        .catch(()  => this.$toast?.error('Failed to load reps'))
    },
    openDialog (item = null) {
      this.backendErrors = {}        

      if (item) {
        this.editMode = true
        this.form = { ...item, password:'' }
      } else {
        this.editMode = false
        this.form = {
          rep_id:null, name:'', email:'', mobile:'',
          rep_code:'', commission_percent:'', password:''
        }
      }
      this.$nextTick(() => { this.dialog = true })
    },
    saveRep () {
      if (!this.$refs.repForm.validate()) return

      const url  = this.editMode
         ? `/admin/reps/${this.form.rep_id}/update`
         : '/admin/reps/store'

      const payload = { ...this.form }
      if (this.editMode && !payload.password) delete payload.password

      axios.post(url, payload)
        .then(() => {
          this.dialog = false
          this.fetchReps()
          this.$toast.success(
            this.editMode ? 'Rep updated' : 'Rep added',
            { timeout: 500 }
          )
        })
        .catch(err => {
          if (err.response?.status === 422) {
            this.backendErrors = err.response.data.errors || {}
          } else {
            this.$toast.error('Save failed')
          }
        })
    },
    confirmDelete (item) {
      this.repToDelete  = item
      this.deleteDialog = true
    },
    performDelete () {
      if (!this.repToDelete) return
      this.deleteLoading = true

      axios.post('/admin/rep-delete', { rep_id: this.repToDelete.rep_id })
        .then(() => {
           this.$toast.success('Rep deleted', { timeout:500 })
           this.fetchReps()
        })
        .catch(() => this.$toast.error('Delete failed'))
        .finally(() => {
          this.deleteLoading = false
          this.deleteDialog  = false
          this.repToDelete   = null
        })
    }
  }
}
</script>

<style>
</style>