<template>
  <div>
    <div class="ac-flex ac-justify-start ac-mb-5">
      <a :href="back" class="ac-bg-plugin ac-text-white ac-px-10 ac-py-3">zurück</a>
    </div>
    <div class="ac-border-plugin ac-border">

      <div class="ac-grid ac-grid-cols-6 ac-gap-10 ac-py-5">
        <div class="ac-col-span-4 ac-border-r ac-border-plugin">
          <iframe :src="templatePreview" width="100%" height="800" class="ac-blur ac-animate-pulse" v-if="status == 'new'"></iframe>
          <iframe :src="iframe_src" width="100%" height="800" v-else></iframe>
        </div>
        <div class="ac-col-span-2 ac-pr-5">


          <div v-if="status == 'new'">
            <template-updater :template-folder="campaign.template" ref="templateupdater" @templateUpdated="createDraft"></template-updater>
            <create-draft-on-cm :campaign="campaign" ref="draftCreator" @draftCreated="reloadInfo"></create-draft-on-cm>
          </div>

          <div v-if="status == 'drafts'">
            <send-preview :cm_id="cm_id"></send-preview>
            <schduler :cm_id="cm_id" @scheduled="reloadInfo"></schduler>
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
  props: ['campaign', 'info', 'templatePreview', 'back'],
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

    if (this.status == 'new') {
      this.$refs.templateupdater.updateTemplate();
    }

    if (this.status == 'drafts') {
      this.loadPreview();
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

        if (tries > 4) clearInterval(interval);

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
            this.resetUpdateStatus();
          });
    },
    resetUpdateStatus() {
      if (this.status == 'new') {
        this.$refs.templateupdater.waiting = this.$refs.draftCreator.waiting = false;
        this.$refs.templateupdater.updated = this.$refs.draftCreator.updated = true;
      }
    }

  }
}
</script>
