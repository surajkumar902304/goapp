<template>
  <v-app>
    <v-navigation-drawer app permanent expand-on-hover fixed class="grey lighten-3" elevation="16">
      
      <v-list dense nav shaped>
        <v-list-item-group>
          <!-- Dashboard -->
          <router-link tag="v-list-item" to="/admin/dashboard" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-view-dashboard-outline</v-icon></v-list-item-icon>
            <v-list-item-title>Dashboard</v-list-item-title>
          </router-link>

          <!-- Order -->
          <router-link tag="v-list-item" to="#">
            <v-list-item-icon><v-icon>mdi-cart-outline</v-icon></v-list-item-icon>
            <v-list-item-title>Order</v-list-item-title>
          </router-link>

          <!-- Products -->
          <router-link tag="v-list-item" to="/admin/products/list" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-package-variant</v-icon></v-list-item-icon>
            <v-list-item-title>Products</v-list-item-title>
          </router-link>

          <!-- Customers -->
          <router-link tag="v-list-item" to="/admin/customers" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-account-group-outline</v-icon></v-list-item-icon>
            <v-list-item-title>Customers</v-list-item-title>
          </router-link>

          <!-- Product Setting Group -->
          <v-list-group prepend-icon="mdi-tune-variant" :value="isProductGroupOpen" no-action>
            <template v-slot:activator>
              <v-list-item-title>Product Setting</v-list-item-title>
            </template>

            <router-link tag="v-list-item" to="/admin/main-categories/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-view-list</v-icon></v-list-item-icon>
              <v-list-item-title>Main Categories</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/categories/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-shape-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Categories</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/sub-categories/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-shape-plus-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Sub-Categories</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/product-offers/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-tag-multiple-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Product Offers</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/product-options/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-format-list-bulleted</v-icon></v-list-item-icon>
              <v-list-item-title>Product Options</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/brands/list" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-domain</v-icon></v-list-item-icon>
              <v-list-item-title>Brands</v-list-item-title>
            </router-link>
          </v-list-group>

          <!-- Theme Setting Group -->
          <v-list-group prepend-icon="mdi-palette-swatch-variant" :value="isThemeGroupOpen" no-action>
            <template v-slot:activator>
              <v-list-item-title>Theme Setting</v-list-item-title>
            </template>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-circle-slice-8</v-icon></v-list-item-icon>
              <v-list-item-title>Round Banners</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-view-carousel</v-icon></v-list-item-icon>
              <v-list-item-title>Big Sliders</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-view-parallel</v-icon></v-list-item-icon>
              <v-list-item-title>Small Sliders</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-sale</v-icon></v-list-item-icon>
              <v-list-item-title>Deals Sliders</v-list-item-title>
            </router-link>

            <!-- <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-new-box</v-icon></v-list-item-icon>
              <v-list-item-title>New Products Sliders</v-list-item-title>
            </router-link> -->

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-shape</v-icon></v-list-item-icon>
              <v-list-item-title>Fruit Sliders</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/browse-sliders" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-star-check-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Browse Sliders</v-list-item-title>
            </router-link>

            <!-- <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-star-check-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Top Sallers</v-list-item-title>
            </router-link> -->

          </v-list-group>

          <v-list-group prepend-icon="mdi-store-cog-outline" :value="isShopGroupOpen" no-action>
            <template v-slot:activator>
              <v-list-item-title>Shop Setting</v-list-item-title>
            </template>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-truck-delivery-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Shipping</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-file-document-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Pages</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="#">
              <v-list-item-icon><v-icon>mdi-credit-card-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Payment Method</v-list-item-title>
            </router-link>

          </v-list-group>

          <!-- Logout (still handled by Laravel) -->
          <v-list-item :href="'/admin/logout'" active-class="active-link">
            <v-list-item-icon><v-icon>mdi-logout</v-icon></v-list-item-icon>
            <v-list-item-title>Log Out</v-list-item-title>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-main style="padding-left: 66px" class="py-5 pe-3">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'App',

  computed: {
    isProductGroupOpen() {
      return this.$route.path.startsWith('/admin/main-category')
          || this.$route.path.startsWith('/admin/category')
          || this.$route.path.startsWith('/admin/sub-category')
          || this.$route.path.startsWith('/admin/product-offers')
          || this.$route.path.startsWith('/admin/product-option')
          || this.$route.path.startsWith('/admin/brand');
    },
    isThemeGroupOpen() {
      return this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
          || this.$route.path.startsWith('/admin/browse-sliders')
    },
    isShopGroupOpen() {
      return this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
          || this.$route.path.startsWith('#')
    }
  }
  // computed: {
  //   isProductGroupOpen() {
  //     return this.$route.path.startsWith('/admin/main-category')
  //         || this.$route.path.startsWith('/admin/category')
  //         || this.$route.path.startsWith('/admin/sub-category')
  //         || this.$route.path.startsWith('/admin/product-offers')
  //         || this.$route.path.startsWith('/admin/product-option')
  //         || this.$route.path.startsWith('/admin/brand');
  //   },
  //   isThemeGroupOpen() {
  //     return this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //   },
  //   isShopGroupOpen() {
  //     return this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //         || this.$route.path.startsWith('#')
  //   }
  // }


}
</script>

<style scoped>
.v-list-group__items .v-list-item.v-list-item--link {
    padding-left: 10px !important;
}
.active-link {
  background-color: #1976d2 !important;
  color: white !important;
}
</style>