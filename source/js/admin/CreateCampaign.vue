<template xmlns="http://www.w3.org/1999/html">
  <div>
    <form @keydown="errors = []" @submit.prevent method="post" :action="adminurl" ref="form">
      <input type="hidden" name="action" value="autocampaigner_create_campaign">
      <input type="hidden" name="nonce" :value="nonce">
      <div class="ac-my-48 ac-flex ac-justify-center ac-items-center">
        <div class="ac-w-3xl ac-p-10 ac-border ac-border-plugin">
          <h3 class="ac-text-3xl ac-mb-10">Neuer Newsletter</h3>
          <div>
            <ul>
              <li v-for="error in errors" v-text="error" class="ac-text-red-500"></li>
            </ul>
          </div>
          <div class="ac-grid ac-grid-cols-2 ac-gap-5">
            <div>
              <div class="ac-mb-4">
                <label class="ac-label">Campagnen Name</label>
                <input class="ac-admin-input" type="text" v-model="campagne.name" name="cmpaign_name">
              </div>
              <div class="ac-mb-4">
                <label class="ac-label">Betreff</label>
                <input class="ac-admin-input" type="text" v-model="campagne.subject" name="subject">
              </div>
              <div class="ac-mb-4">
                <label class="ac-label">Sender Name</label>
                <input class="ac-admin-input" type="text" v-model="campagne.from_name" name="from_name">
              </div>
              <div class="ac-mb-4">
                <label class="ac-label">Sender E-Mail</label>
                <input class="ac-admin-input" type="text" v-model="campagne.from_email" name="from_email">
              </div>
              <div class="ac-mb-4">
                <label class="ac-label">Antwort E-Mail</label>
                <input class="ac-admin-input" type="text" v-model="campagne.reply_email" name="reply_email">
              </div>
              <div class="ac-mb-4">
                <label class="ac-label">Bestätigung senden an:</label>
                <input class="ac-admin-input" type="text" v-model="campagne.confirm_email" name="confirm_email">
              </div>
            </div>
            <div>
              <ul>
                <li v-for="template in template_radio" class="ac-pb-3 ac-mb-3 ac-border-b ac-border-plugin">
                  <div class="ac-flex ac-space-x-4 ac-items-center">
                    <input type="radio" v-model="template_name" :value="template[0]" @change="errors = []" name="template_name">
                    <div>
                      <label class="ac-label">
                        {{ template[1].Name }}
                      </label>
                      <p v-text="template[1].Description"></p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <button class="ac-button" @click="validateCampagne">weiter</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>

export default {
  name: "CreateCampaign",
  props: ['defaults', 'templates', 'adminurl', 'campaignnames'],
  data() {
    return {
      step: 1,
      campagne: {
        name: '',
        subject: '',
        from_name: '',
        from_email: '',
        reply_email: '',
        confirm_email: ''
      },
      errors: [],
      template_name: false,
      template_radio: Object.entries(this.templates),
      nonce: xhr.nonce
    }
  },
  mounted() {

    this.campagne = {...this.campagne, ...this.defaults}

  },
  computed: {
    template_folder() {
      if (!this.template_name) return false;
      var folder = '';
      for (const [key, value] of Object.entries(this.templates)) {
        if (value.Name = this.template_name) {
          folder = key;
        }
      }
      return folder;
    }
  },
  methods: {
    validateCampagne() {

      this.errors = [];

      var keys = Object.keys(this.campagne);

      keys.forEach((key) => {
        if (this.campagne[key] == '') {
          this.errors.push('Bitte alle Felder ausfüllen.')
        }

        if (key.includes('email')) {

          var emails = this.campagne[key].split(',');

          emails.forEach((email) => {
            if (!this.validateEmail(email)) {
              this.errors.push('Bitte nur gültige E-Mail Adressen eingeben.')
            }
          })

        }
      });

      if (this.campaignnames.includes(this.campagne.name)) {
        this.errors.push('Dieser Kampagnen Name ist schon vergeben.')
      }

      if (this.template_name == false) {
        this.errors.push('Bitte ein Template wählen');
      }

      this.errors = [...new Set(this.errors)];

      if (!this.errors.length) {
        this.$refs.form.submit();
      }

    },
    validateEmail(email) {
      return String(email)
          .toLowerCase()
          .match(
              /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          );
    }
  }
}
</script>

<style scoped>

</style>