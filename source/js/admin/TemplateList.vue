<template>
  <div>
    <div class="ac-relative">
      <h3 class="ac-text-xl ac-font-semibold ac-mb-5">Lokale Templates</h3>
      <div class="ac-mb-10">
        <div class="ac-grid ac-grid-cols-5 ac-gap-5">
          <div v-for="template in templates">
            <div class="ac-my-5 ac-pb-5 ac-border-b ac-border-plugin">
              <div class="ac-w-full">
                <h3 v-text="template.name"></h3>
                <p v-text="template.description"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import TemplateUpdater from "./TemplateUpdater.vue";

export default {
  name: "TemplateList",
  components: {TemplateUpdater},
  props: ['templates'],
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