<template>
  <div>
    <div class="ac-relative">
      <h3 class="ac-text-xl ac-font-semibold ac-mb-5">Lokale Templates</h3>
      <div class="ac-mb-10">
        <div class="ac-grid ac-grid-cols-5 ac-gap-5">
          <div v-for="template in exisitng">
            <div class="ac-my-5 ac-pb-5 ac-border-b ac-border-plugin">
              <template-updater :template-folder="template" :ref="template"></template-updater>
              <div class="ac-w-full">
                <button
                    class="ac-py-2 ac-px-5 ac-text-white ac-text-center ac-cursor-pointer ac-bg-plugin ac-w-full"
                    @click="upload(template)"
                >Upload</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <h3 class="ac-text-xl ac-font-semibold ac-mb-5">Templates auf dem E-Mail Server</h3>
      <div class="ac-flex ac-space-x-5">
        <div v-for="template in templates" :key="template.TemplateID" class="ac-p-5 ac-border ac-border-plugin">
          <a :href="template.PreviewURL" target="_blank">
            <h3 class="ac-font-semibold" v-text="template.Name"></h3>
            <img :src="template.ScreenshotURL" width="200" class="sc-border-none">
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import Axios from "axios";
import Qs from "qs";
import TemplateUpdater from "./TemplateUpdater.vue";

export default {
  name: "TemplateList",
  components: {TemplateUpdater},
  props: ['exisitng', 'templates'],
  data() {
    return {
      listDetails: [],
      isLoading: false,
      setLists: this.usedListsPreload,
    }
  },
  mounted() {
    this.getTemplates();

  },
  methods: {
    upload(template) {

      this.$refs[template][0].updateTemplate();
    },
    getTemplates() {

    }
  }
}
</script>

<style scoped>

</style>