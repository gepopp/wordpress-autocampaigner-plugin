<template>
  <div class="ac-my-5 ac-py-5 ac-border-b ac-border-plugin">
    <h3 class="ac-text-xl ac-font-semibold">Geplanter Newsletter</h3>
    <div class="ac-mb-5 ac-pb-5 ac-border-b ac-border-plugin">
      <ul>
        <li class="ac-flex ac-justify-between">
          <span>Betreff:</span>
          <span v-text="statusinfo.Subject"></span>
        </li>
        <li class="ac-flex ac-justify-between">
          <span>Geplant für:</span>
          <span v-text="statusinfo.DateScheduled"></span>
        </li>
      </ul>
    </div>

    <button class="ac-button ac-flex ac-justify-center" @click="unschedule">
      <span v-if="status == false">Auf Entwurf umstellen</span>
      <svg v-if="status == 'sending'" class="ac-animate-spin ac-h-5 ac-w-5 ac-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </button>

  </div>
</template>

<script>
import Axios from "axios";
import Qs from "qs";

export default {
  name: "Unscheduler",
  props: ['statusinfo'],
  data(){
    return{
      status: false
    }
  },
  methods:{
    unschedule(){

      this.status = 'sending';

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: "autocampaigner_unschedule_campaign",
        nonce: xhr.nonce,
        cm_id: this.statusinfo.CampaignID
      })).then((rsp) => {
        this.$emit('unscheduled', { cm_id: this.statusinfo.CampaignID })
      });
    }
  }
}
</script>

<style scoped>

</style>