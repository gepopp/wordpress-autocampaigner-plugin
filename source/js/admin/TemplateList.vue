<template>
  <div>
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
  </div>
</template>

<script>

import Axios from "axios";
import Qs from "qs";

export default {
  name: "TemplateList",
  props: ['templates', 'exisitng'],
  data() {
    return {
      listDetails: [],
      isLoading: true,
      setLists: this.usedListsPreload
    }
  },
  methods: {
    upload(template) {
      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampainger_upload_template',
        nonce: xhr.nonce,
        template_name: template
      }))
          .then((rsp) => {console.log(rsp.data)})
          .catch((rsp) => console.log(rsp.response));
    }
  }
}
</script>

<style scoped>

</style>