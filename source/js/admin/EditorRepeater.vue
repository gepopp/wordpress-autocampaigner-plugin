<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "EditorRepeater",
  data(){
    return {
      layouts: false
    }
  },
  mounted() {
    this.layouts = this.$children.filter(child => {
      return child.$options.name === "EditorLayout";
    })
  },
  methods:{
    saveData(){
      var data = [];
      this.layouts.forEach((layout) => {
        data.push(layout.saveData())
      })
      return {
        Items: data
      }
    }
  }
}
</script>

<style scoped>

</style>