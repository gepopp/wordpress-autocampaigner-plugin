<template>
  <div>
    <div v-show="!saved">
      <slot/>
      <button class="ac-button save-button" @click="saveCampaign()">speichern</button>
    </div>
    <div v-show="saved">
      <form :action="adminurl" method="post">
        <input type="hidden" name="action" value="autocampaigner_schedule_campagin">
        <input type="hidden" name="nonce" :value="nonce">
        <input type="hidden" name="draft" :value="draft">
        <div class="ac-my-48 ac-flex ac-justify-center ac-items-center">
          <div class="ac-w-3xl ac-p-10 ac-border ac-border-plugin">
            <h3 class="ac-text-3xl ac-mb-10">Newsletter Versand</h3>
            <div class="ac-mb-4">
              <label class="ac-label">Bestätigung senden an:</label>
              <input class="ac-admin-input" type="text" v-model="campagne.confirm_email" name="confirm_email">
            </div>
            <button class="ac-button" @click="">weiter</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
import Axios from "axios";
import Qs from "qs";

export default {
  name: "Editor",
  props: ['draft', 'adminurl', 'nonce', 'confirm_email_setting'],
  data() {
    return {
      content: '',
      multilines: [],
      images: [],
      repeaters: [],
      saved: false,
      campagne: {
        schedule: '',
        confirm_email: this.confirm_email_setting
      },
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
  },
  methods: {
    saveCampaign() {

      var images = [];

      this.images.forEach((image) => {
       images.push( image.saveData() );
      })

      var multilines = [];

      this.multilines.forEach((multiline) => {
        multilines.push(multiline.saveData());
      })

      var repeaters = [];

      this.repeaters.forEach((repeater) => {
        repeaters.push(repeater.saveData());
      })


      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampaigner_save_content',
        nonce: xhr.nonce,
        draft: this.draft,
        content: {
          images: images,
          multilines: multilines,
          repeaters: repeaters
        }
      })).then((rsp) => this.saved = rsp.data)
    }
  }
}
</script>

<style scoped>

</style>