<template>
  <div class="ac-relative">
    <quill-editor v-model="textEditable" theme="bubble"></quill-editor>
  </div>

</template>

<script>
export default {
  name: "Multiline",
  props: ['text'],
  data() {
    return {
      textEditable: this.text.trim(),
      edit: false,
    }
  },
  methods:{
    setFromPost(post){
      this.textEditable = post;
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