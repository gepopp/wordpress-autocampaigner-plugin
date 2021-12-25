<template>
  <div class="ac-relative">
    <a :href="editable.link" v-text="editable.text" @click.prevent="edit = true"></a>
    <div class="ac-absolute ac-bg-white ac-top-0 ac-right-0 ac-bg-white ac-z-50 ac-bg-white ac-p-3 ac-shadow-xl ac-w-80" v-show="edit">
      <div class="ac-flex ac-justify-between ac-w-full ac-cursor-pointer" @click="edit = false">
        <p class="ac-text-plugin">Link bearbeiten</p>
        <svg class="ac-w-6 ac-h-6 ac-text-plugin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      <div class="ac-mb-4">
        <label class="ac-label ac-text-xs ac-text-left">Text</label>
        <input type="text" class="ac-admin-input" v-model="editable.text" @keypress.enter="edit = false">
      </div>
      <div class="ac-mb-4">
        <label class="ac-label ac-text-xs ac-text-left">Link</label>
        <input type="text" class="ac-admin-input" v-model="editable.link" @keypress.enter="edit = false">
      </div>
      <a :href="editable.link" target="_blank">Link öffnen</a>
    </div>
  </div>
</template>

<script>
export default {
  name: "Singleline",
  props: ['text', 'link'],
  data() {
    return {
      edit: false,
      editable: {
        text: this.text,
        link: this.link
      }
    }
  },
  methods: {
    setFromPost(post) {
      this.editable.link = post.permalink
    },
    saveData() {
      return {
        Content: this.editable.text,
        Href: this.editable.link
      }
    }
  }
}
</script>

<style scoped>

</style>