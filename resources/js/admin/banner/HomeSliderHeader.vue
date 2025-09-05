<template>
  <div class="page-margin-20-40 page-slider-header">
    <v-container fluid class="pt-0">
      <v-row class="mt-0 pt-0">
        <v-col cols="12" md="12" class="p-0">
          <h2 class="text-h6 mb-1">Slider Headers</h2> 
        </v-col>
      </v-row>
    </v-container>

    <v-row class="mt-0">
      <v-col cols="12">
        <v-card elevation="5">
          <v-data-table item-key="slider_header_id" :items="sliders" :headers="sliderHeaders" :search="ssearch" :items-per-page="10000" hide-default-footer>            
            <template v-slot:top>
              <v-row dense class="mx-1 pb-1">
                <v-text-field v-model="ssearch" class="m-2" clearable dense outlined hide-details prepend-inner-icon="mdi-magnify mb-2" 
                  placeholder="Search Header key, Header name"/>
              </v-row>
            </template>
            <template #item.header_name="{ item }">
              <span class="text-capitalize">{{ item.header_name }}</span>
            </template>
            <template #item.header_value="{ item }">
              <span>{{ item.header_value }}</span>
            </template>
            <template #header.actions>
                <div class="text-center">Action</div>
            </template>
            <template #item.actions="{ item }">
              <div class="text-center">
                <v-chip color="primary" class="white--text" outlined pill small @click="editItem(item)" style="cursor: pointer;">
                  <v-icon small left>mdi-pencil</v-icon>Edit
                </v-chip>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="addSdialog" max-width="400">
      <v-card elevation="5">
        <v-card-title>
          <span>Edit Header Name</span>
          <v-spacer></v-spacer>
          <v-icon @click="addSdialog = false">mdi-close</v-icon>
        </v-card-title>
        <v-form v-model="fsvalid" @submit.prevent="saveHeader">
          <v-card-text>
            <v-text-field v-model="defaultItem.header_name" label="Header Key" disabled class="text-capitalize"/>
            <v-text-field v-model="defaultItem.header_value" :rules="[v => !!v || 'Header Value is required']" label="Header Name" 
              required @input="defaultItem.header_value = defaultItem.header_value.toUpperCase()"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-32-text-12" type="submit" style="font-weight: bold; color: #1976d2; background-color: white !important;" small :disabled="!fsvalid || submitting">
              Update
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HomeSliderHeader',
  data() {
    return {
      ssearch: '',
      sliders: [],
      sliderHeaders: [
        { text: 'Header key', value: 'header_name' },
        { text: 'Header name', value: 'header_value' },
        { text: 'Action', value: 'actions', sortable: false },
      ],
      addSdialog: false,
      editedIndex: -1,
      fsvalid: false,
      submitting: false,
      defaultItem: {
        slider_header_id: null,
        header_name: '',
        header_value: '',
      },
    };
  },
  created() {
    this.getAllSliderHeaders();
  },
  methods: {
    getAllSliderHeaders() {
      axios.get('/admin/slider-headers/vlist').then((res) => {
        this.sliders = res.data.sliders;
      });
    },
    editItem(item) {
      this.defaultItem = {
        slider_header_id: item.slider_header_id,
        header_name: item.header_name,
        header_value: item.header_value,
      };
      this.editedIndex = item.slider_header_id;
      this.fsvalid = true;
      this.addSdialog = true;
    },
    saveHeader() {
      this.submitting = true;

      axios
        .post('/admin/slider-headers/update', {
          slider_header_id: this.defaultItem.slider_header_id,
          header_value: this.defaultItem.header_value,
        })
        .then(() => {
          this.getAllSliderHeaders();
          this.addSdialog = false;
          this.$toast.success('Header updated successfully!', { timeout: 500 });
        })
        .catch(() => {
          this.$toast.error('Failed to update header.', { timeout: 500 });
        })
        .finally(() => {
          this.submitting = false;
        });
    },
  },
};
</script>
<style>
.text-capitalize input {
  text-transform: capitalize;
}
.page-slider-header .v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  height: 32px!important;
}
</style>