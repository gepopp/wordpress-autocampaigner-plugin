<template>
  <div class="ac-relative">
    <div>
      <img :src="editables.src" :width="width" :height="height">
    </div>
    <div class="ac-absolute ac-bg-white ac-top-0 ac-right-0 ac-bg-white ac-z-50 ac-bg-white ac-p-3 ac-shadow-xl" v-show="edit">
      <div class="ac-flex ac-justify-end ac-w-full ac-cursor-pointer" @click="edit = false">
        <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      <input type="text" class="ac-admin-input" v-model="search" @keyup="searchImages" placeholder="search">
      <hr>
      <div class="ac-grid ac-grid-cols-3 ac-gap-2">
        <div v-for="image in searchresluts">
          <img :src="image.thumbnail" width="100" height="100" @click="editables.src = image.full; edit = false; current = image">
        </div>
      </div>
    </div>
    <div class="ac-absolute ac-bg-white ac-top-0 ac-right-0 ac-bg-white ac-z-50 ac-bg-white ac-p-3 ac-shadow-xl" v-show="link">
      <div class="ac-flex ac-justify-end ac-w-full ac-cursor-pointer" @click="link = false">
        <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      <input type="text" class="ac-admin-input" v-model="editables.href" placeholder="link">
    </div>
    <div class="ac-absolute ac-bg-white ac-top-0 ac-right-0 ac-bg-white ac-z-50 ac-bg-white ac-p-3 ac-shadow-xl" v-show="alt">
      <div class="ac-flex ac-justify-end ac-w-full ac-cursor-pointer" @click="alt = false">
        <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      <input type="text" class="ac-admin-input" v-model="editables.alt" placeholder="alt text">
    </div>
    <div class="ac-absolute  ac-bottom-0 ac-right-0 ac-text-white  ">
      <div class="ac-flex ac-flex-col ac-space-y-2">
        <div class="ac-w-10 ac-h-10 ac-bg-plugin ac-rounded-full ac-flex ac-items-center ac-justify-center" @click="edit = !edit">
          <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </div>
        <div class="ac-w-10 ac-h-10 ac-bg-plugin ac-rounded-full ac-flex ac-items-center ac-justify-center" @click="link = !link">
          <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
          </svg>
        </div>
        <div class="ac-w-10 ac-h-10 ac-bg-plugin ac-rounded-full ac-flex ac-items-center ac-justify-center" @click="alt = !alt">
          <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Axios from "axios";
import Qs from "qs";

export default {
  name: "ImageEditable",
  props: ['src', 'width', 'height'],
  data() {
    return {
      search: '',
      searchresluts: [],
      edit: false,
      link: false,
      alt: false,
      editables: {
        src: this.src,
        href: '',
        alt:''
      },
      current: {}
    }
  },
  methods: {
    searchImages() {
      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampaigner_search_image',
        nonce: xhr.nonce,
        search: this.search
      }))
          .then((rsp) => this.searchresluts = rsp.data);
    },
    saveData(){
      return {
        Content: this.editables.src,
        Href: this.editables.href,
        Alt: this.editables.alt
      }
    }
  }
}
</script>

<style scoped>

</style>