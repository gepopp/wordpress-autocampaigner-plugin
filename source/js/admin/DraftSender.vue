<template>
  <div class="ac-border-plugin ac-border">
    <div class="ac-grid ac-grid-cols-6 ac-gap-10 ac-py-5">
      <div class="ac-col-span-4 ac-border-r ac-border-plugin">
        <div class="ac-w-full ac-h-full ac-flex ac-justify-center ac-items-center ac-bg-plugin" v-if="iframe_src == ''">
          <svg class="ac-animate-spin ac-h-16 ac-w-16 ac-text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <iframe :src="iframe_src" width="100%" height="800"></iframe>
      </div>
      <div class="ac-col-span-2 ac-pr-5">


        <div v-if="status == 'drafts' || status == 'new'">
          <template-updater :template-folder="campaign.template" ref="templateupdater" @templateUpdated="createDraft"></template-updater>
          <create-draft-on-cm :campaign="campaign" ref="draftCreator" @draftCreated="reloadInfo"></create-draft-on-cm>

          <div v-if="cm_id">
            <send-preview :cm_id="cm_id"></send-preview>
            <schduler :cm_id="cm_id" @scheduled="reloadInfo"></schduler>
          </div>

        </div>

        <div v-if="status == 'sent'">
          <sent-campaign-info :statusinfo="statusinfo"></sent-campaign-info>
        </div>

        <div v-if="status == 'scheduled'">
          <unscheduler :statusinfo="statusinfo" @unscheduled="reloadInfo"></unscheduler>
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
  props: ['campaign', 'info'],
  data() {
    return {
      cm_id: this.campaign.cm_id,
      iframe_src: '',
      draft_details: false,
      scheduled: false,
      statusinfo: this.info.info,
      status: this.info.status
    }
  },
  mounted() {
    if (this.status == 'drafts' || this.status == 'new') {
      if (this.campaign.cm_id == null) {
        this.$refs.templateupdater.updateTemplate()
      } else {
        this.$refs.templateupdater.waiting = this.$refs.draftCreator.waiting = false;
        this.$refs.templateupdater.updated = this.$refs.draftCreator.updated = true;
        this.loadPreview();
      }
    }

    if (this.status == 'sent') {
      this.iframe_src = this.statusinfo.WebVersionURL.replace('http', 'https');
    }

    if (this.status == 'scheduled') {
      this.iframe_src = this.statusinfo.PreviewURL.replace('http', 'https');
    }


  },
  methods: {
    reloadInfo(e) {

      var tries = 1;
      var interval = setInterval(() => {

        Axios.post(xhr.ajaxurl, Qs.stringify({
          action: "autocampaigner_reload_cm_info",
          nonce: xhr.nonce,
          cm_id: e.cm_id
        }))
            .then((rsp) => {
              this.statusinfo = rsp.data.info;
              this.status = rsp.data.status;
              this.cm_id = rsp.data.info.CampaignID;
              this.iframe_src = rsp.data.info.PreviewURL == undefined
                  ? rsp.data.info.WebVersionURL.replace('http', 'https')
                  : rsp.data.info.PreviewURL.replace('http', 'https');
            });

        tries++;

        if(tries> 4) clearInterval(interval);

      }, 3000);
    },
    createDraft() {
      this.$refs.draftCreator.createDraft();
    },
    loadPreview() {
      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: "autocampaigner_load_cm_draft_details",
        nonce: xhr.nonce,
        cm_id: this.cm_id
      }))
          .then((rsp) => {
            this.iframe_src = rsp.data.PreviewURL.replace('http', 'https')
            this.draft_details = rsp.data;
          });
    },

  }
}
</script>

<style scoped>

</style>