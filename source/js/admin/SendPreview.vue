<template>
  <div>
    <div class="ac-my-5 ac-py-5 ac-border-b ac-border-plugin">
      <h3 class="ac-text-xl ac-font-semibold">Vorschau E-Mail senden</h3>
      <div class="ac-mb-4">
        <label class="ac-label">Senden an</label>
        <input class="ac-admin-input ac-w-full" type="text" v-model="preview_to">
      </div>
      <button class="ac-button ac-flex ac-justify-center" @click="sendPreview">
        <span v-if="previewstatus == false">senden</span>
        <svg v-if="previewstatus == 'done'" class="ac-w-5 ac-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <svg v-if="previewstatus == 'sending'" class="ac-animate-spin ac-h-5 ac-w-5 ac-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-if="previewstatus == 'error'" class="ac-h-5 ac-w-5 ac-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
import Qs from "qs";


export default {
  name: "SendPreview",
  props: ['cm_id'],
  data() {
    return {
      previewstatus: false,
      preview_to: '',
    }
  },
  methods: {
    sendPreview() {


      if (this.preview_to == '') {
        this.previewstatus = 'error';
        setTimeout(() => this.previewstatus = false, 3000)
        return;
      }

      this.previewstatus = 'sending';


      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: "autocampaigner_cm_send_preview",
        nonce: xhr.nonce,
        cm_id: this.cm_id,
        recipients: this.preview_to
      }))
          .then((rsp) => {
            this.previewstatus = 'done';
            this.preview_to = '';
            setTimeout(() => this.previewstatus = false, 3000)
          })
          .catch((rsp) => {
            this.previewstatus = 'error';
            setTimeout(() => this.previewstatus = false, 3000)

          });
    }
  }
}
</script>

<style scoped>

</style>