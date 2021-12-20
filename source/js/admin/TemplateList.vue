<template>
  <div>
    <div class="ac-relative">
      <h3 class="ac-text-xl ac-font-semibold ac-mb-5">Lokale Templates</h3>
      <div class="ac-mb-10">
        <ul>
          <li v-for="template in exisitng" class="ac-flex ac-justify-between ac-py-2 ac-border-b ac-border-plugin">
            <p v-text="template"></p>
            <button
                class="ac-py-2 ac-px-5 ac-text-white ac-text-center ac-cursor-pointer ac-bg-plugin"
                @click="upload(template)"
            >Upload
            </button>
          </li>
        </ul>
      </div>
      <h3 class="ac-text-xl ac-font-semibold ac-mb-5">Templates auf Campaign Monitor</h3>
      <div class="ac-flex ac-space-x-5">
        <div v-for="template in templates" :key="template.TemplateID">
          <a :href="template.PreviewURL" target="_blank">
            <h3 class="ac-font-semibold" v-text="template.Name"></h3>
            <img :src="template.ScreenshotURL" width="200" class="sc-border-none">
          </a>
        </div>
      </div>
      <div v-show="isLoading" class="ac-absolute ac-top-0 ac-left-0 ac-w-full ac-h-full ac-bg-white ac-bg-opacity-25 ac-flex ac-justify-center ac-items-center">
        <svg class="ac-animate-spin ac--ml-1 ac-mr-3 ac-h-10 ac-w-10 ac-text-plugin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>
  </div>
</template>

<script>

import Axios from "axios";
import Qs from "qs";

export default {
  name: "TemplateList",
  props: ['exisitng'],
  data() {
    return {
      listDetails: [],
      isLoading: true,
      setLists: this.usedListsPreload,
      templates: []
    }
  },
  mounted() {
    this.getTemplates();
  },
  methods: {
    upload(template) {
      this.isLoading = true;
      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampainger_upload_template',
        nonce: xhr.nonce,
        template_name: template
      }))
          .then((rsp) => {
            this.isLoading = false;
            setTimeout(() =>  this.getTemplates(), 5000);
          })
          .catch((rsp) => console.log(rsp.response));
    },
    getTemplates() {
      this.isLoading = true;
      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampainger_get_templates',
        nonce: xhr.nonce,
      }))
          .then((rsp) => {
            this.isLoading = false;
            this.templates = rsp.data;
          })

    }
  }
}
</script>

<style scoped>

</style>