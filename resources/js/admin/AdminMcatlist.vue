<template>
    <div>
        <v-row>
            <v-col cols="12" md="9">
                <v-text-field oulined dense prepend-inner-icon="mdi-magnify" outlined hide-details
                              placeholder="Search Category">
                </v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-select dense outlined label="Type" :items="['Manual','Smart']" hide-details
                          clearable></v-select>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="12">
                <v-card outlined>
                    <v-data-table dense :items="mcats" :headers="mcatsHeaders">
                        <template v-slot:item.mcat_image="{item}">
                            <v-btn icon outlined tile class="my-2">
                                <v-icon>mdi-image</v-icon>
                            </v-btn>

                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
<script>
export default {
    name:"AdminMcatlist",
    data(){
        return{
            mcats:[
                {mcat_image:"image",mcat_title:"Disposable Vape",products:450,condition:"Product type is equal to Disposable Pods"}
            ],
            mcatsHeaders:[
                {text:"",value:'mcat_image',class:"grey lighten-3",width:"100px",sortable:false},
                {text:"Title",value:'mcat_title', class:"grey lighten-3"},
                {text:"Products",value:'products', class:"grey lighten-3"},
                {text:"Product Condition",value:'condition', class:"grey lighten-3"},
            ]
        }
    },
    created() {
        this.getAllMcats();
    },
    methods:{
        getAllMcats(){
            axios.get('/admin/mcats/vlist')
                .then((resp)=>{
                    console.log(resp.data);
                })
        }
    }
}

</script>

<style scoped>

</style>
