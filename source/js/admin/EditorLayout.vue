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
      singlelines: []
    }
  },
  mounted() {

    this.multilines = this.$children.filter(child => {
      return child.$options.name === "Multiline";
    })
    this.images = this.$children.filter(child => {
      return child.$options.name === "ImageEditable";
    })
    this.singlelines = this.$children.filter(child => {
      return child.$options.name === "Singleline";
    })
  },
  methods:{
    saveData(){
      var multilines = [];
      var singlelines = [];
      var images = [];

      this.images.forEach((image) => {
        images.push(image.saveData())
      })

      this.multilines.forEach((multiline) => {
        multilines.push(multiline.saveData())
      })

      this.singlelines.forEach((singleline) => {
        singlelines.push(singleline.saveData())
      })

      return {
        Layout: this.label,
        Multilines: multilines,
        Images : images,
        Singlelines: singlelines
      }


    }
  }
}
</script>

<style scoped>

</style>