<template>
  <div>
    <div>
      <slot/>
    </div>
      <form :action="adminurl" method="post" @submit.prevent="saveCampaign" ref="form">
        <input type="hidden" name="action" value="autocampaigner_save_draft_content">
        <input type="hidden" name="nonce" :value="nonce">
        <input type="hidden" name="draft" :value="draft">
        <input type="hidden" name="content" :value="createContent">
        <button class="ac-button">weiter</button>
      </form>
    </div>
</template>
<script>
import Qs from "qs";


export default {
  name: "Editor",
  props: ['draft', 'confirm_email_setting'],
  data() {
    return {
      content: false,
      multilines: [],
      images: [],
      repeaters: [],
      singlelines: [],
      nonce: xhr.nonce,
      adminurl: xhr.posturl
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
    this.singlelines = this.$children.filter(child => {
      return child.$options.name === "Singleline";
    })
  },
  methods: {

    saveCampaign() {
        this.$refs.form.submit();
    },
  },
  computed: {
    createContent() {

        var images = [];

        this.images.forEach((image) => {
          images.push(image.saveData());
        })

        var multilines = [];

        this.multilines.forEach((multiline) => {
          multilines.push(multiline.saveData());
        })

        var repeaters = [];

        this.repeaters.forEach((repeater) => {
          repeaters.push(repeater.saveData());
        })

        var singlelines = [];

        this.singlelines.forEach((singleline) => {
          singleline.push(singleline.saveData());
        })


        return JSON.stringify({
          Images: images,
          Multilines: multilines,
          Repeaters: repeaters,
          Singlelines: singlelines
        });

    }
  }
}
</script>

<style scoped>

</style>