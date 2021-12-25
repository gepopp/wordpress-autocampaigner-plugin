<template>
  <div>
    <div class="ac-relative">
      <div class="ac-fixed ac-top-0 ac-right-0 ac-m-40 ac-bg-plugin ac-rounded-full ac-p-2 ac-text-white ac-w-12 ac-h-12 ac-z-50" v-if="autosavestatus">
        <svg v-if="autosavestatus == 'saving'" class="ac-animate-pulse ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <svg v-if="autosavestatus == 'success'" class="ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <svg v-if="autosavestatus == 'error'" class="ac-w-8 ac-h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <slot/>
    </div>
    <form :action="adminurl" method="post" @submit.prevent="saveCampaign" ref="form">
      <input type="hidden" name="action" value="autocampaigner_save_draft_content">
      <input type="hidden" name="nonce" :value="nonce">
      <input type="hidden" name="draft" :value="draft">
      <input type="hidden" name="content" :value="createContent">
      <button class="ac-button">weiter</button>
    </form>
  </div>
</template>
<script>
import Axios from "axios";
import Qs from "qs";
import debounce from "lodash.debounce";

export default {
  name: "Editor",
  props: ['draft', 'confirm_email_setting'],
  data() {
    return {
      content: false,
      multilines: [],
      images: [],
      repeaters: [],
      singlelines: [],
      nonce: xhr.nonce,
      adminurl: xhr.posturl,
      autosavestatus: false,
      lastautosave: 0
    }
  },
  mounted() {

    this.multilines = this.$children.filter(child => {
      return child.$options.name === "Multiline";
    })
    this.images = this.$children.filter(child => {
      return child.$options.name === "ImageEditable";
    })
    this.repeaters = this.$children.filter(child => {
      return child.$options.name === "EditorRepeater";
    })
    this.singlelines = this.$children.filter(child => {
      return child.$options.name === "Singleline";
    })
  },
  watch: {
    content() {
      this.debouncedWatch();
    }
  },
  created() {
    this.debouncedWatch = debounce((newValue, oldValue) => {
      this.autosave();
    }, 2000);
  },
  beforeUnmount() {
    this.debouncedWatch.cancel();
  },
  methods: {
    autosave() {

      this.autosavestatus = 'saving';

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampaigner_autosave',
        nonce: xhr.nonce,
        draft: this.draft,
        content: this.createContent
      }))
          .then(() => {
            this.lastautosave = new Date();
            this.autosavestatus = 'success';
            setTimeout(() => this.autosavestatus = false, 2000)
          })
          .catch(() => {
            this.autosavestatus = 'error';
            setTimeout(() => this.autosavestatus = false, 2000)
          });
    },
    saveCampaign() {
      this.$refs.form.submit();
    },
  },
  computed: {
    createContent() {

      var images = [];

      this.images.forEach((image) => {
        images.push(image.saveData());
      })

      var multilines = [];

      this.multilines.forEach((multiline) => {
        multilines.push(multiline.saveData());
      })

      var repeaters = [];

      this.repeaters.forEach((repeater) => {
        repeaters.push(repeater.saveData());
      })

      var singlelines = [];

      this.singlelines.forEach((singleline) => {
        singleline.push(singleline.saveData());
      })


      var content = JSON.stringify({
        Images: images,
        Multilines: multilines,
        Repeaters: repeaters,
        Singlelines: singlelines
      });

      this.content = content;

      return content;
    }
  }
}
</script>

<style scoped>

</style>