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
              <v-list-item-title class="text-h6">
                TrueWeb App
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-list dense nav shaped>
          <v-list-item-group>
            <router-link tag="v-list-item" to="/admin/dashboard" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-view-dashboard-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Dashboard</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/orders" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-cart-outline</v-icon></v-list-item-icon>
              <v-list-item-title>
                Order <v-chip x-small color="grey" class="ml-4 mb-1 white--text">{{ orderCount }}</v-chip>
              </v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/products" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-package-variant</v-icon></v-list-item-icon>
              <v-list-item-title>Products</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/customers" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-account-group-outline</v-icon></v-list-item-icon>
              <v-list-item-title>Customers</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/custom-price" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-currency-gbp</v-icon></v-list-item-icon>
              <v-list-item-title>Custom Price</v-list-item-title>
            </router-link>

            <router-link tag="v-list-item" to="/admin/reps" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-account</v-icon></v-list-item-icon>
              <v-list-item-title>Rep</v-list-item-title>
            </router-link>



            <router-link tag="v-list-item" to="/admin/coupons" active-class="active-link">
              <v-list-item-icon><v-icon>mdi-percent</v-icon></v-list-item-icon>
              <v-list-item-title>Discount</v-list-item-title>
            </router-link>


            <v-list-group prepend-icon="mdi-tune-variant" :value="isProductGroupOpen" no-action>
              <template v-slot:activator>
                <v-list-item-title>Product Setting</v-list-item-title>
              </template>

              <router-link tag="v-list-item" to="/admin/main-categories" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-view-list</v-icon></v-list-item-icon>
                <v-list-item-title>Main Categories</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/categories" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-shape-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Categories</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/sub-categories" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-shape-plus-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Sub-Categories</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/product-offers" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-tag-multiple-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Product Offers</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/product-options" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-format-list-bulleted</v-icon></v-list-item-icon>
                <v-list-item-title>Product Options</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/brands" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-domain</v-icon></v-list-item-icon>
                <v-list-item-title>Brands</v-list-item-title>
              </router-link>

            </v-list-group>

            <v-list-group prepend-icon="mdi-palette-swatch-variant" :value="isThemeGroupOpen" no-action>
              <template v-slot:activator>
                <v-list-item-title>Theme Setting</v-list-item-title>
              </template>

              <router-link tag="v-list-item" to="/admin/round-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-circle-slice-8</v-icon></v-list-item-icon>
                <v-list-item-title>Round Banners</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/big-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-view-carousel</v-icon></v-list-item-icon>
                <v-list-item-title>Big Sliders</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/small-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-view-parallel</v-icon></v-list-item-icon>
                <v-list-item-title>Small Sliders</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/deals-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-sale</v-icon></v-list-item-icon>
                <v-list-item-title>Deals Sliders</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/new-product-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-package-variant-plus</v-icon></v-list-item-icon>
                <v-list-item-title>New Products</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/fruit-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-shape</v-icon></v-list-item-icon>
                <v-list-item-title>Fruit Sliders</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/top-seller-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-star-circle</v-icon></v-list-item-icon>
                <v-list-item-title>Top-Seller</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/slider-headers" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-monitor-dashboard</v-icon></v-list-item-icon>
                <v-list-item-title>Slider Header</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/browse-sliders" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-content-duplicate</v-icon></v-list-item-icon>
                <v-list-item-title>Browse Sliders</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/loyalty-reward-banner" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-star-check-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Loyalty Reward Banner</v-list-item-title>
              </router-link>

            </v-list-group>

            <v-list-group prepend-icon="mdi-store-cog-outline" :value="isShopGroupOpen" no-action>
              <template v-slot:activator>
                <v-list-item-title>Shop Setting</v-list-item-title>
              </template>

              <router-link tag="v-list-item" to="/admin/delivery-method" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-truck</v-icon></v-list-item-icon>
                <v-list-item-title>Delivery Method</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/customer-tags" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-file-document-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Customer Tags</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/services" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-new-box</v-icon></v-list-item-icon>
                <v-list-item-title>Service</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="#">
                <v-list-item-icon><v-icon>mdi-truck-delivery-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Shipping</v-list-item-title>
              </router-link>

              <router-link tag="v-list-item" to="/admin/payment-method" active-class="active-link">
                <v-list-item-icon><v-icon>mdi-credit-card-outline</v-icon></v-list-item-icon>
                <v-list-item-title>Payment Method</v-list-item-title>
              </router-link>

            </v-list-group>
          </v-list-item-group>
        </v-list>

        <template v-slot:append>
          <v-list>
            <v-list-item :href="'/admin/logout'" active-class="active-link">
              <v-list-item-icon>
                <v-icon>mdi-logout</v-icon>
              </v-list-item-icon>
              <v-list-item-title style="font-size: 13px !important;">Log Out</v-list-item-title>
            </v-list-item>
          </v-list>
        </template>
      </v-navigation-drawer>

      <v-main style="padding-left: 66px; background-color: #eeeeee;" class="py-5 pe-3">
        <router-view />
      </v-main>
    </template>
    <template v-else>
      <router-view />
    </template>
  </v-app>
</template>

<script>
export default {
  name: 'App',

  data() {
    return {
      orderCount: 0,
      refreshInterval: null,
    }
  },
  mounted() {
    this.fetchOrderCount();

    // Auto-refresh every 10 seconds (adjust as needed)
    this.refreshInterval = setInterval(() => {
      this.fetchOrderCount();
    }, 10000);

    // Optional: listen for a custom event from child components
    this.$root.$on('order-updated', this.fetchOrderCount);
  },

  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    fetchOrderCount() {
      axios.get('/admin/orders/count')
        .then(res => {
          this.orderCount = res.data.count || 0;
        })
        .catch(() => {
          this.orderCount = 0;
        });
    }
  },

  computed: {
    isProductGroupOpen() {
      return this.$route.path.startsWith('/admin/main-categories')
        || this.$route.path.startsWith('/admin/categories')
        || this.$route.path.startsWith('/admin/sub-categories')
        || this.$route.path.startsWith('/admin/product-offers')
        || this.$route.path.startsWith('/admin/product-options')
        || this.$route.path.startsWith('/admin/brands')
        || this.$route.path.startsWith('/admin/coupons');
    },
    isThemeGroupOpen() {
      return this.$route.path.startsWith('/admin/round-sliders')
        || this.$route.path.startsWith('/admin/big-sliders')
        || this.$route.path.startsWith('/admin/small-sliders')
        || this.$route.path.startsWith('/admin/deals-sliders')
        || this.$route.path.startsWith('/admin/new-product-sliders')
        || this.$route.path.startsWith('/admin/fruit-sliders')
        || this.$route.path.startsWith('/admin/top-seller-sliders')
        || this.$route.path.startsWith('/admin/slider-headers')
        || this.$route.path.startsWith('/admin/browse-sliders')
        || this.$route.path.startsWith('/admin/loyalty-reward-banner')
    },
    isShopGroupOpen() {
      return this.$route.path.startsWith('/admin/delivery-method')
        || this.$route.path.startsWith('/admin/customer-tags')
        || this.$route.path.startsWith('/admin/services')
        || this.$route.path.startsWith('#')
        || this.$route.path.startsWith('/admin/payment-method')
    }
  }

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

.v-navigation-drawer .v-list-item-group>.v-list-item:last-child {
  position: absolute;
  bottom: 0;
  left: 8px;
  right: 8px;

  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.v-navigation-drawer .v-list-item-group {
  position: static;
  margin: 0 !important;
}
</style>