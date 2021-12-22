<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "EditorLayout",
  props: ['label'],
  data(){
    return {
      multilines: [],
      images: [],
    }
  },
  mounted() {

    this.multilines = this.$children.filter(child => {
      return child.$options.name === "Multiline";
    })
    this.images = this.$children.filter(child => {
      return child.$options.name === "ImageEditable";
    })
  },
  methods:{
    saveData(){
      var multilines = [];
      var images = [];

      this.images.forEach((image) => {
        images.push(image.saveData())
      })

      this.multilines.forEach((multiline) => {
        multilines.push(multiline.saveData())
      })

      return {
        Layout: this.label,
        Multilines: multilines,
        Images : images
      }


    }
  }
}
</script>

<style scoped>

</style>