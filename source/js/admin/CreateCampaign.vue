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
          <div v-show="step == 1">
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
            <button class="ac-button" @click="gotoStep(2)">weiter</button>
          </div>
          <div v-show="step == 2">
            <ul>
              <li v-for="template in template_radio">
                <label class="ac-label">
                  <input type="radio" v-model="template_name" :value="template[0]" @change="errors = []" name="template_name">{{ template[1].Name }}
                </label>
              </li>
              <button class="ac-button" @click="gotoStep(3)">weiter</button>
              <div class="ac-flex ac-justify-end">
                <a class="ac-text-xs ac-cursor-pointer" @click="step = 1">zurück</a>
              </div>
            </ul>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>

export default {
  name: "CreateCampaign",
  props: ['defaults', 'templates', 'adminurl', 'nonce', 'campaignnames'],
  data() {
    return {
      step: 1,
      campagne: {
        name: '',
        subject: '',
        from_name: '',
        from_email: '',
        reply_email: ''
      },
      errors: [],
      template_name: false,
      template_radio: Object.entries(this.templates)
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
    gotoStep(goto) {
      if (this.step == 1 && goto == 2) {
        if (this.validateCampagne() == 0) this.step = goto;
      }

      if (this.step == 2 && goto == 3) {
        if (this.template_name == false) {
          this.errors = [];
          this.errors.push('Bitte ein Template wählen');
        } else {
          this.$refs.form.submit();
        }
      }

    },
    validateCampagne() {

      this.errors = [];

      var keys = Object.keys(this.campagne);

      keys.forEach((key) => {
        if (this.campagne[key] == '') {
          this.errors.push('Bitte alle Felder ausfüllen.')
        }

        if (key.includes('email')) {
          if (!this.validateEmail(this.campagne[key])) {
            this.errors.push('Bitte nur gültige E-Mail Adressen eingeben.')
          }
        }

      });

      if(this.campaignnames.includes(this.campagne.name)){
        this.errors.push('Dieser Kampagnen Name ist schon vergeben.')
      }


      this.errors = [...new Set(this.errors)];

      return this.errors.length;

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