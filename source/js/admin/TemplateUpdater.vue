<template>
  <div class="ac-flex ac-space-x-5 ac-pb-5 ac-mb-5 ac-border-b ac-border-plugin">
    <div>
      <div class="ac-flex ac-space-x-5 ac-w-full">
        <div class="ac-bg-plugin ac-rounded-full ac-p-2 ac-text-white ac-flex-none">
          <svg v-if="isLoading" class="ac-animate-spin ac-h-8 ac-w-8 ac-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-if="waiting" class="ac-animate-pulse ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <svg v-if="updated" class="ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <svg v-if="error" class="ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="ac-flex ac-flex-col ac-justify-center">
          <h3 class="ac-text-xl ac-font-semibold">Template: {{ templateFolder }}</h3>
          <p v-if="isLoading" class="ac-animate-pulse">Update Template am E-Mail Server</p>
          <p v-if="error" class="ac-animate-pulse ac-text-red-600" v-text="error"></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
import Qs from "qs";

export default {
  name: "TemplateUpdater",
  props: ['templateFolder'],
  data() {
    return {
      waiting: true,
      isLoading: false,
      updated: false,
      error: ''
    }
  },
  methods: {
    updateTemplate() {

      this.waiting = false;
      this.isLoading = true;

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: "autocampaigner_update_template_on_cm",
        nonce: xhr.nonce,
        template_name: this.templateFolder
      }))
          .then((rsp) => this.updated = rsp.data)
          .catch((rsp) => this.error = rsp.response.data)
          .then(() => { this.isLoading = false; this.$emit('templateUpdated') });
    }
  }
}
</script>

<style scoped>

</style>