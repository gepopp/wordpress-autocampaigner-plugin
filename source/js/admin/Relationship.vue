<template>
  <div>
    {{ meta }} werden beim erstellen der Aussendung geladen
  </div>
</template>

<script>
export default {
  name: "Relationship",
  props: ['meta', 'post'],
  data() {
    return {
      posts: []
    }
  },
  mounted() {

  },
  methods: {
    saveData() {
      return {
        meta: this.meta,
        parent: this.post
      }
    }
  }
}
</script>

<style scoped>

</style>