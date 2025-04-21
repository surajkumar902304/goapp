<template>
    <div>
        <h1>Admin Vue Dashboard</h1>
        <v-btn color="primary" small>Hello Vuetify!</v-btn>
        <div>
            <v-text-field v-model="fname" :rules="nameRules" dense solo counter></v-text-field>
        </div>
        <div>{{ fname }}</div>
    </div>
</template>
<script>
export default {
    name:"AdminDashboard",
    data() {
        return {
            fname:"",
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length <= 10) || 'Name must be less than 10 characters',
            ],
        }
    }
}
</script>