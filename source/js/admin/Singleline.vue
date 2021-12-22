<template>
<div>
  <a :href="editable.link" v-text="editable.text"></a>
</div>
</template>

<script>
export default {
  name: "Singleline",
  props: ['text', 'link'],
  data(){
    return{
      editable:{
        text: this.text,
        link: this.link
      }
    }
  },
  methods:{
    saveData(){
      return{
        Content: this.editable.text,
        Href: this.editable.link
      }
    }
  }
}
</script>

<style scoped>

</style>