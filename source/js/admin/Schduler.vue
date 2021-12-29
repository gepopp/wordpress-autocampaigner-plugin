<template>
  <div>
    <div class="ac-my-5 ac-py-5 ac-border-b ac-border-plugin">
      <h3 class="ac-text-xl ac-font-semibold">Newsletter senden</h3>
      <div class="ac-mb-4">
        <label class="ac-label">Sendetermin</label>
        <input class="ac-admin-input ac-w-full" type="datetime-local" v-model="send_at">
        <p class="ac-text-xs ac-text-plugin">Leer lassen zum sofortigen Versand!</p>
      </div>
      <button class="ac-button ac-flex ac-justify-center" @click="scheduleCampaign">
        <span v-if="schedulestatus == false">senden</span>
        <svg v-if="schedulestatus == 'sending'" class="ac-animate-spin ac-h-5 ac-w-5 ac-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-if="schedulestatus == 'error'" class="ac-h-5 ac-w-5 ac-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
  name: "Schduler",
  props: ['draftId'],
  data() {
    return {
      schedulestatus: false,
      send_at: '',
    }
  },
  methods: {
    scheduleCampaign() {

      this.schedulestatus = 'sending';

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: "autocampaigner_cm_schedule_campaign",
        nonce: xhr.nonce,
        draft_id: this.draftId,
        schedule: this.send_at
      }))
          .then((rsp) => {
            this.send_at = '';
            this.$emit('scheduled', rsp.data);
          })
          .catch((rsp) => {

            this.schedulestatus = 'error';
            setTimeout(() => this.schedulestatus = false, 3000)

          });
    },
  }
}
</script>

<style scoped>

</style>