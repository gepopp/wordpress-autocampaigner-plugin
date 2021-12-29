<template>
  <div class="ac-my-5 ac-py-5 ac-border-b ac-border-plugin">
    <h3 class="ac-text-xl ac-font-semibold">Gesendeter Newsletter</h3>
    <div class="ac-mb-5 ac-pb-5 ac-border-b ac-border-plugin">
      <ul>
        <li class="ac-flex ac-justify-between" v-for="data in campaigndata">
          <span v-text="data[0]"></span>
          <span v-text="data[1]"></span>
        </li>
      </ul>
    </div>
    <div class="ac-mb-5 ac-pb-5">
      <ul>
        <li v-for="(campaigndat, label) in campaigndata" v-show="!label.includes('URL')" class="ac-flex ac-justify-between ac-py-3 ac-border-b ac-border-plugin">
          <strong v-text="label"></strong>
          <span v-text="campaigndat"></span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
import Qs from "qs";

export default {
  name: "SentCampaignInfo",
  props: ['draftId', 'draft'],
  data() {
    return {
      campaigndata: []
    }
  },
  mounted() {
    Axios.post(xhr.ajaxurl, Qs.stringify({
      action: 'autocampaigner_get_cm_campaigninfo',
      nonce: xhr.nonce,
      draft_id: this.draftId
    })).then((rsp) => {

      this.campaigndata = Object.keys(rsp.data)
          .filter(key => !key.includes('URL'))
          .reduce((obj, key) => {
            obj[key] = rsp.data[key];
            return obj;
          }, {});

    });
  }
}
</script>

<style scoped>

</style>