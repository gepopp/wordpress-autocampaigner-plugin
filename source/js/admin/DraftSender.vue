<template>
  <div>
    <div class="ac-flex ac-justify-start ac-mb-5">
      <a :href="edit" class="ac-bg-plugin ac-text-white ac-px-10 ac-py-3" v-if="current_campaign.status !== 'sent'">bearbeiten</a>
    </div>
    <div class="ac-border-plugin ac-border">

      <div class="ac-grid ac-grid-cols-5 ac-gap-10 ac-py-5">
        <div class="ac-col-span-4 ac-border-r ac-border-plugin">
          <iframe :src="previewUrl" width="100%" height="800"></iframe>
        </div>
        <div class="ac-pr-5">

          <h3 class="ac-text-xl" v-text="current_campaign.header_data.campaign_name"></h3>


          <div class="ac-w-full ac-h-full ac-flex ac-justify-center ac-items-center" v-if="initialLoading || error">

            <div class="ac-flex ac-flex-col ac-items-center" v-if="error">
              <svg class="ac-h-10 ac-w-10 ac-text-plugin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
              <p v-text="error.Message"></p>
            </div>

            <div v-else>
              <svg class="ac-animate-spin ac-h-10 ac-w-10 ac-text-plugin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

          </div>

          <div v-if="current_campaign.status == 'created'">
            <send-preview :cm_id="current_campaign.cm_id"></send-preview>
            <schduler :draftId="campaignId" @scheduled="reloadInfo"></schduler>
          </div>

          <div v-if="current_campaign.status == 'sent'">
            <sent-campaign-info :draftId="campaignId" :draft="current_campaign"></sent-campaign-info>
          </div>

          <div v-if="current_campaign.status == 'scheduled'">
            <unscheduler :draftId="campaignId" :draft="current_campaign" @unscheduled="reloadInfo"></unscheduler>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TemplateUpdater from "./TemplateUpdater.vue";
import CreateDraftOnCm from "./CreateDraftOnCm.vue";
import SendPreview from "./SendPreview.vue";
import Schduler from "./Schduler.vue";
import Axios from "axios";
import Qs from "qs";
import SentCampaignInfo from "./SentCampaignInfo.vue";
import Unscheduler from "./Unscheduler.vue";

export default {
  name: "DraftSender",
  components: {Unscheduler, SentCampaignInfo, Schduler, SendPreview, CreateDraftOnCm, TemplateUpdater},
  props: ['campaignId', 'campaign', 'previewUrl', 'edit'],
  data() {
    return {
      current_campaign: this.campaign,
      initialLoading: false,
      error: false
    }
  },
  mounted() {

    if (this.current_campaign.status == 'new') {
      this.initialLoading = true;

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampaigner_create_draft_on_api',
        nonce: xhr.nonce,
        draft_id: this.campaignId
      }))
          .then((rsp) => this.current_campaign = rsp.data)
          .catch((error) => this.error = error.response.data)
          .then(() => this.initialLoading = false)
    }


  },
  methods: {
    reloadInfo(e) {
        this.current_campaign = e;
    }
  }
}
</script>
