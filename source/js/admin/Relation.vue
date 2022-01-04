<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "Relation",
  props:['post'],
  mounted() {
    this.$children.forEach((child) => child.setFromPost(this.post));
  }
}
</script>

<style scoped>

</style>