<template>
  <div class="page-margin-20-40">
    <v-row>
      <h2 class="text-h6 mb-4">Loyalty Reward Banner</h2>
    </v-row>

    <v-card elevation="5" class="mt-4">
      <div class="d-flex flex-column align-center pa-6">
        <strong class="mb-4">Update Image</strong>
        <input ref="imageInput" type="file" accept="image/*" class="d-none" @change="handleImageUpload"/>
        <div class="uploader-box mb-2" @click="triggerFileInput">
          <v-img v-if="imagePreview" :src="imagePreview" height="100%" width="100%" cover/>
          <v-icon v-else size="56" color="grey lighten-1">mdi-image-area</v-icon>
        </div>
        <div v-if="imageName" class="text-caption mb-2">{{ imageName }}</div>
        <v-btn class="btn-32-text-12" color="success" :loading="submitting" :disabled="submitting || !imageSelected || saved" @click="saveBanner">
          {{ isNew ? 'UPLOAD' : 'UPDATE' }}
        </v-btn>
      </div>
    </v-card>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'LoyaltyRewardBanner',

  data () {
    return {
      cdn: 'https://cdn.truewebpro.com/',
      defaultItem: { 
        loyalty_reward_banner_id: null, 
        loyalty_reward_banner_image: '' 
      },
      imagePreview: null,  
      imageName   : '',
      submitting  : false,
      isNew       : true,
      saved       : false
    }
  },
  computed: {
    imageSelected () {
      return this.defaultItem.loyalty_reward_banner_image instanceof File
    }
  },
  async created () { 
    await this.loadBanner() 
  },
  methods: {
    async loadBanner () {
      try {
        const { data } = await axios.get('/admin/loyalty-rewards/vlist')
        const banner = data.loyalty_rewards?.[0]
        if (banner) {
          this.isNew   = false
          this.defaultItem.loyalty_reward_banner_id = banner.loyalty_reward_banner_id
          this.imagePreview = this.cdn + banner.loyalty_reward_banner_image
          this.imageName    = banner.loyalty_reward_banner_image.split('/').pop()
        }
      } catch (e) {
        console.info('No banner yet – ready to upload first image')
      }
    },
    triggerFileInput () { 
      this.$refs.imageInput.click() 
    },
    handleImageUpload (e) {
      const file = e.target.files[0]
      if (!file) return
      this.defaultItem.loyalty_reward_banner_image = file
      this.imagePreview = URL.createObjectURL(file)
      this.imageName    = file.name
      this.saved        = false
    },
    async saveBanner () {
      this.submitting = true
      const fd = new FormData()

      if (!this.isNew) {
        fd.append('loyalty_reward_banner_id', this.defaultItem.loyalty_reward_banner_id)
      }
      fd.append('loyalty_reward_banner_image', this.defaultItem.loyalty_reward_banner_image)

      const url = this.isNew
        ? '/admin/loyalty-rewards/add'
        : '/admin/loyalty-rewards/update'

      try {
        await axios.post(url, fd, { headers:{ 'Content-Type':'multipart/form-data' } })
        await this.loadBanner()
        this.$toast.success(this.isNew ? 'Image uploaded!' : 'Image updated!', { timeout: 500 })
        this.isNew = false
        this.saved = true
      } catch {
        this.$toast.error('Save failed', { timeout: 800 })
      } finally { this.submitting = false }
    }
  }
}
</script>

<style scoped>
.uploader-box{
  width:100%;
  min-width: 600px !important;
  border:2px dashed #c3c3c3;
  border-radius:6px;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  overflow:hidden;
}
</style>
