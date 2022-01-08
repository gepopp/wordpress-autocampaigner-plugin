<template>
  <div class="ac-relative">
    <slot></slot>
    <div class="ac-absolute ac-bottom-0 ac-top-0" v-if="holdsPost > 0">
      <div class="ac-flex ac-justify-start ac-w-full ac-cursor-pointer">
        <p>Post tauschen</p>
      </div>
      <input type="text" class="ac-admin-input" v-model="search" @keyup="searchPosts" placeholder="search">
      <hr>
      <div class="ac-flex ac-flex-col ac-w-72 ac-leding-none ac-z-50 ac-cursor-pointer ac-bg-white ac-p-3 ac-shadow-2xl">
        <div v-for="postdata in searchresluts" class="ac-border-b ac-border-plugin ac-py-2">
          <p v-html="postdata.title" class="ac-leading-none ac-line-clamp-1"></p>
          <div class="ac-flex ac-justify-between ac-mt-3">
              <span class="ac-text-white ac-bg-plugin ac-px-3"
                    @click="setPost(postdata, post)"
                    v-for="post in holdsPost"
                    v-text="'Post ' + post"
              ></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
import Qs from "qs";
import debounce from "lodash.debounce";

export default {
  name: "EditorLayout",
  props: {
    label: String,
    type: String,
    holdsPost: {
      type: Number,
      default: 0
    },
    edit: false,
    posts: [],

  },
  data() {
    return {
      multilines: [],
      images: [],
      singlelines: [],
      relationships: [],
      search: '',
      searchresluts: []
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
    this.singlelines = this.$children.filter(child => {
      return child.$options.name === "Relationship";
    })
  },
  methods: {
    setPost(postdata, postnumber) {


      var children = this.$children;
      var chunklength = this.$children.length / this.holdsPost;


      var sliced = [];
      for (let i = 0; i < children.length; i += chunklength) {
        const chunk = children.slice(i, i + chunklength);
        sliced.push(chunk);
      }

      sliced[postnumber - 1].forEach((child, index) => {
          child.setFromPost(postdata)
      })

      this.searchresluts = [];
      this.search = '';
    },
    searchPosts() {

      if (this.search.length < 2) {
        this.searchresluts = [];
        return;
      }

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampaigner_search_posts',
        nonce: xhr.nonce,
        type: this.type,
        search: this.search
      })).then((rsp) => this.searchresluts = rsp.data);
    },
    saveData() {
      var multilines = [];
      var singlelines = [];
      var images = [];
      var relationships = [];

      this.images.forEach((image) => {
        images.push(image.saveData())
      })

      this.multilines.forEach((multiline) => {
        multilines.push(multiline.saveData())
      })

      this.singlelines.forEach((singleline) => {
        singlelines.push(singleline.saveData())
      })

      this.relationships.forEach((relation) => {
        relation.push(relation.saveData())
      })




      return {
        Layout: this.label,
        Multilines: multilines,
        Images: images,
        Singlelines: singlelines,
        Relationships: relationships
      }


    }
  }
}
</script>

<style scoped>

</style>