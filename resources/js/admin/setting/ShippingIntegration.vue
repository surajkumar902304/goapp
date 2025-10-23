<template>
<div class="page-margin-20-40 page-shipping">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="11" class="p-0">
          <h2 class="text-h6 mb-1">Shipping Settings</h2> 
        </v-col>

        <!-- <v-col cols="12" md="1" class="p-0 ps-2 text-end">
          <v-btn color="secondary" small class="text-none w-100 btn-32-text-12" style="color: #1976d2; font-weight: bold; background-color: white !important; 
              border: 1px solid #1976d2 !important;" @click="openDialogIntegrations">
              Add Integration
          </v-btn>
        </v-col> -->
      </v-row>
    </v-container>

    <v-row class="mt-0">
        <v-col cols="12">
            <v-card elevation="5">
                <v-data-table :headers="integrationsHeaders" :items="integrations" :search="ssearchintegrations" 
                    :footer-props="{ 'items-per-page-options': [10, 25, 50, 100], 'items-per-page-text': 'Rows per page:' }">
                    <template v-slot:top>
                      <v-row dense class="mx-1 pb-1">
                        <v-text-field v-model="ssearchintegrations" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" placeholder="Search Shipping"/>
                      </v-row>
                    </template>
                    <template #item.provider="{ item }">
                        <span>{{ item.provider }}</span>
                    </template>
                    <template #item.public_key="{ item }">
                        <span>{{ item.public_key }}</span>
                    </template>
                    <template #item.secret_key="{ item }">
                        <span>{{ item.secret_key }}</span>
                    </template>
                    <template #item.is_active="{ item }">
                        <v-switch v-model="item.is_active" :input-value="item.is_active === 1" @change="toggleStatusIntegrations(item)" dense inset style="transform: scale(0.75);"></v-switch>
                    </template>
                    <template #header.actions1>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions1="{ item }">
                        <div class="text-center">
                            <v-chip color="primary" class="white--text" outlined pill small @click="editItemIntegrations(item)" style="cursor: pointer;">
                              <v-icon small left>mdi-pencil</v-icon>Edit
                            </v-chip>
                        </div>
                    </template>
                    <template #header.actions2>
                        <div class="text-center">Action</div>
                    </template>
                    <template #item.actions2="{ item }">
                        <div class="text-center">
                            <v-chip color="red" class="white--text" outlined pill small @click="confirmDeleteIntegrations(item)" style="cursor: pointer;" >
                                <v-icon small left>mdi-delete</v-icon>Delete
                            </v-chip>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>

    <v-dialog v-model="addSdialogIntegrations" max-width="600" @update:model-value="onDialogToggleIntegrations">
      <v-card elevation="5">
        <v-card-title>
          <span>{{ editedIndexIntegrations === -1 ? 'Add Integration' : 'Edit Integration' }}</span>
          <v-spacer></v-spacer>
          <v-icon @click="addSdialogIntegrations = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="fsvalid" @submit.prevent="saveIntegrations">
          <v-card-text>
            <v-text-field v-model="defaultItemIntegrations.provider" disabled label="Provider Name"/>
            <v-text-field v-model="defaultItemIntegrations.public_key" label="Public Key"/>
            <v-text-field v-model="defaultItemIntegrations.secret_key" label="Secret Key"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important; border: 1px solid #1976d2 !important;" small :disabled="!fsvalidIntegrations || submittingIntegrations">
              {{ editedIndex === -1 ? 'Add' : 'Update' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialogIntegrations" max-width="400">
      <v-card elevation="5">
        <v-card-title class="text-h6">
          Confirm Delete
        </v-card-title>
        <v-card-text>
          Are you sure you want to delete this Integration?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="btn-32-text-12" text color="grey" @click="deleteDialogIntegrations = false">Cancel</v-btn>
          <v-btn class="btn-32-text-12" text color="red" :loading="deleteLoadingIntegrations" :disabled="deleteLoadingIntegrations" @click="performDeleteIntegrations">
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
  name: 'ShippingIntegration',
  data() {
    return {
      ssearchintegrations: '',
      integrations: [],
      integrationsHeaders: [
        { text: 'Provider name', value: 'provider' },
        { text: 'Public Key', value: 'public_key' },
        { text: 'Secret Key', value: 'secret_key' },
        { text: 'Status', value: 'is_active' },
        { text: 'Action', value: 'actions1', sortable: false },
        { text: 'Action', value: 'actions2', sortable: false },
      ],

      addSdialogIntegrations: false,
      editedIndexIntegrations: -1,
      fsvalidIntegrations: false,
      submittingIntegrations: false,

      defaultItemIntegrations: {
        integration_setting_id: null,
        provider: '',
        public_key: '',
        secret_key: '',
      },

      deleteDialogIntegrations: false,
      IntegrationsToDelete: null,
      deleteLoadingIntegrations: false,
    }
  },
  created() {
    this.getAllintegrations()
  },
  watch: {
    addSdialogIntegrations(val) {
      if (!val) this.submittingIntegrations = false
    }
  },
  methods: {
    getAllintegrations() {
      axios.get('/admin/integration/vlist').then(res => {
        this.integrations = res.data.integrations;
      })
      .catch(err => {
        console.error(err)
      })
    },
    onDialogToggleIntegrations(open) {
      if (!open) {
      this.defaultItemIntegrations = 
      { 
        integration_setting_id: null, 
        provider: '', 
        public_key: '', 
        secret_key: '', 
      };
      this.fsvalid = false;
      this.submittingIntegrations = false;
      this.editedIndexIntegrations = -1;
      }
    },
    openDialogIntegrations() {
      this.defaultItemIntegrations = {
        integration_setting_id: null,
        provider: '',
        public_key: '',
        secret_key: '',
      }
      this.editedIndexIntegrations = -1
      this.fsvalidIntegrations = false
      this.addSdialogIntegrations = true
    },
    editItemIntegrations(item) {
      this.defaultItemIntegrations = {
        integration_setting_id: item.integration_setting_id,
        provider: item.provider,
        public_key: item.public_key,
        secret_key: item.secret_key,
      }
      this.editedIndexIntegrations = item.integration_setting_id
      this.fsvalidIntegrations = true
      this.addSdialogIntegrations = true
    },
    async saveIntegrations() {
      this.submittingIntegrations = true;

      let expiresAtValue = this.defaultItemIntegrations.expires_at;

      if (expiresAtValue) {
        expiresAtValue = `${expiresAtValue} 00:00:00`;
      } else {
        expiresAtValue = null;
      }

      const payload = {
        provider: this.defaultItemIntegrations.provider,
        public_key: this.defaultItemIntegrations.public_key,
        secret_key: this.defaultItemIntegrations.secret_key,
      };

      if (this.editedIndexIntegrations !== -1) {
        payload.integration_setting_id = this.editedIndexIntegrations;
      }

      const url = this.editedIndexIntegrations === -1 ? '/admin/integration/add' : '/admin/integration/update';

      try {
        await axios.post(url, payload, {
          headers: { 'Content-Type': 'application/json' }
        });
        this.$toast.success(
          this.editedIndexIntegrations === -1 ? 'Integration added successfully!' : 'Integration updated successfully!',
          { timeout: 500 }
        );
        this.getAllintegrations();
        this.addSdialogIntegrations = false;
      } catch (error) {
      } finally {
        this.submittingIntegrations = false;
      }
    },
    async toggleStatusIntegrations(item) {
      try {
          await axios.post(`/admin/integration/status-toggle/${item.integration_setting_id}`, {
              is_active: item.is_active
          });
          this.$toast?.success('Integration Status updated', { timeout: 500 });
      } catch (error) {
          console.error("Failed to toggle status", error);
          this.$toast?.error('Failed to update status', { timeout: 500 });
      }
    },
    confirmDeleteIntegrations(item) {
      this.IntegrationsToDelete = item
      this.deleteDialogIntegrations = true
    },
    async performDeleteIntegrations() {
      if (!this.IntegrationsToDelete) return
      this.deleteLoadingIntegrations = true
      try {
        await axios.post('/admin/integration-delete', { integration_setting_id: this.IntegrationsToDelete.integration_setting_id })
        this.$toast.success('Integration deleted successfully!', { timeout: 500 })
        this.getAllintegrations()
      } catch (err) {
        console.error(err)
        this.$toast.error('Failed to delete Integration.', { timeout: 2000 })
      } finally {
        this.deleteLoadingIntegrations = false
        this.deleteDialogIntegrations = false
        this.IntegrationsToDelete = null
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
.page-shipping .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>