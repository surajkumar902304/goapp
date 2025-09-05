<template>
  <v-app>
    <template v-if="$route.meta.layout !== 'none'">
    <v-navigation-drawer app permanent expand-on-hover fixed class="grey lighten-3" elevation="16">
      <v-list class="border border-bottom">
        <v-list-item class="px-2">
          <v-list-item-avatar>
            <v-img src="/images/icon.png"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="text-h6">TrueWeb App</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-list dense nav shaped>
        <v-list-item-group>
          <router-link tag="v-list-item" :to="{ name: 'customer.dashboard' }" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-view-dashboard-outline</v-icon></v-list-item-icon>
            <v-list-item-title>Dashboard</v-list-item-title>
          </router-link>

          <router-link tag="v-list-item" :to="{ name: 'customer.reps' }" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-account-group-outline</v-icon></v-list-item-icon>
            <v-list-item-title>Costomer List</v-list-item-title>
          </router-link>

          <router-link tag="v-list-item" :to="{ name: 'customer.commission' }" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-cash-multiple</v-icon></v-list-item-icon>
            <v-list-item-title>Commission</v-list-item-title>
          </router-link>

          <v-list-item :href="'/rep/logout'" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-logout</v-icon></v-list-item-icon>
            <v-list-item-title>Log Out</v-list-item-title>
          </v-list-item>
          <!-- <v-list-item @click="logout">
            <v-list-item-icon><v-icon>mdi-logout</v-icon></v-list-item-icon>
            <v-list-item-title>Log Out</v-list-item-title>
          </v-list-item> -->
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-main style="padding-left:66px; background-color: #eeeeee;" class="py-5 pe-3">
      <router-view />
    </v-main>
    </template>
    <template v-else>
      <router-view />
    </template>
  </v-app>
</template>

<script>
import axios from 'axios'
export default {
  name: 'CustomerApp',
  methods: {
    async logout () {
      try {
        await axios.post('/rep/logout', {}, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        window.location = '/rep/login'
      } catch (e) {
        console.error('Logout failed', e)
      }
    }
  }

}
</script>

<style scoped>
.v-list-group__items .v-list-item.v-list-item--link {
  padding-left: 10px !important;
}
.active-link {
  background-color:#1976d2!important;
  color:#fff!important;
}
</style>
