<template>
  <div class="ac-relative">
    <div v-if="!edit" v-html="textEditable"></div>
    <div v-else>
      <quill-editor v-model="textEditable"/>
    </div>
    <div class="ac-absolute ac-w-10 ac-h-10 ac-bg-plugin ac-rounded-full ac-bottom-0 ac-right-0 ac-text-white ac-flex ac-items-center ac-justify-center" @click="edit = !edit">
      <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
      </svg>
    </div>
  </div>

</template>

<script>
export default {
  name: "Multiline",
  props: ['text'],
  data() {
    return {
      textEditable: this.text.trim(),
      edit: false
    }
  },
  methods:{
    saveData(){
      return {
        Content: this.textEditable
      }
    }
  }
}
</script>

<style scoped>

</style>