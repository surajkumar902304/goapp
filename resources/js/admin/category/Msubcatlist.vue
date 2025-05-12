<template>
<div>
    <v-row>
        <v-col cols="12" md="6">
            <v-text-field v-model="ssearch" clearable dense hide-details outlined prepend-inner-icon="mdi-magnify" placeholder="Search Sub-Categories name"
            />
        </v-col>
        <v-col cols="12" md="2">
            <v-autocomplete v-model="publishFilter" :items="[ { text: 'All', value: null }, { text: 'Online Store', value: 'Online Store' }, { text: 'App Store', value: 'App Store' } ]" 
                item-text="text" item-value="value" outlined dense label="Publishing" hide-details @change="publishByStatus"></v-autocomplete>
        </v-col>
        <v-col cols="12" md="2">
            <v-autocomplete v-model="typeFilter" :items="[ { text: 'All', value: null }, { text: 'Smart', value: 'smart' }, { text: 'Manual', value: 'manual' } ]" 
                item-text="text" item-value="value" outlined dense label="Type" hide-details @change="typeByStatus"></v-autocomplete>
        </v-col>
        <v-col cols="12" md="2" class="text-end mt-1">
            <v-btn color="secondary" small href="/admin/msub-category/add" class="text-none font-weight-bold">
                Add Sub-Category
            </v-btn>
        </v-col>
    </v-row>
    <v-row>
        <v-col cols="12">
            <v-card outlined>
                <v-data-table :items="subcats" :headers="msubcatsHeaders" :search="ssearch">
                    <template #item.msubcat_image="{ item }">
                        <img :src="cdn + item.msubcat_image || 'https://via.placeholder.com/50'" width="50" />
                    </template>
                    <template v-slot:item.msubcat_name="{ item }">
                        <a :href="'/admin/msub-category/' + item.msubcat_id" class="link-dark"> {{ item.msubcat_name }} </a>
                    </template>
                    <template #item.mcat_name="{ item }">
                        {{ item.category?.mcat_name || '—' }}
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Msubcatlist',
  data () {
    return {
        cdn: 'https://cdn.truewebpro.com/',
      ssearch: '',
      msubcats: [],
      subcats: [],
      mcats: [],
      publishFilter: null,
      typeFilter: null,

      msubcatsHeaders: [
        { text: 'Image',             value: 'msubcat_image', sortable: false },
        { text: 'Sub-Category Name', value: 'msubcat_name' },
        { text: 'Sub-Category Tag',  value: 'msubcat_tag' },
        { text: 'Category Name',     value: 'mcat_name', sortable: false },
      ],
    };
  },

  created () {
    this.getAllSubCategories();
    this.getAllCategories();
  },
  methods: {
    /* API calls */
    getAllSubCategories () {
      axios.get('/admin/msub-categories/vlist').then(res => {
        this.msubcats = res.data.msubcats;
        this.subcats = res.data.msubcats;
      });
    },
    getAllCategories () {
      axios.get('/admin/mcategories/vlist').then(res => {
        this.mcats = res.data.mcats;
      });
    },
    publishByStatus(){
      if (this.publishFilter === null) {
            this.subcats = this.msubcats;
            } else {
            this.subcats = this.msubcats.filter(subcat => subcat.msubcat_publish === this.publishFilter);
            }
    },
    typeByStatus(){
      if (this.typeFilter === null) {
            this.subcats = this.msubcats;
            } else {
            this.subcats = this.msubcats.filter(subcat => subcat.msubcat_type === this.typeFilter);
            }
    }

    
  }
};
</script>
<style scoped>
</style>
