<template>
  <div class="ac-relative">
    <quill-editor v-model="textEditable" theme="bubble"></quill-editor>
  </div>

</template>

<script>
export default {
  name: "Multiline",
  props: {'text':{ type: String, default: ''}, 'meta': { type: String, default: ''}},
  data() {
    return {
      textEditable: this.text.trim(),
      edit: false,
    }
  },
  methods:{
    setFromPost(post){
      this.textEditable = post[this.meta];
    },
    saveData(){
      return {
        Content: this.textEditable
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>