<template>
  <div>
    <div class="ac-realtive ac-min-h-screen">
      <div class="ac-flex ac-pl-1 ac-space-x-5 ac-border-b ac-border-plugin">
        <svg class="ac-w-6 ac-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
        </svg>
        <p class="ac-font-semibold">Diese Liste für Ihre Aussendungen verwenden</p>
      </div>
      <ul>
        <li v-for="list in listDetails" :key="list.ListID" class="ac-p-2 ac-mb-2 ac-border-b ac-border-plugin">
          <label class="ac-flex ac-items-center ac-space-x-4 ac-w-full">
            <input type="checkbox" v-model="setLists" :value="list.ListID" @change="save">
            <div class="ac-w-full ac-flex ac-w-full ac-justify-between">
              <p class="ac-font-bold ac-w-full" v-text="list.Name"></p>
              <div class="ac-flex ac-flex-col ac-px-5" :class="{ 'ac-animate-pulse ac-blur-sm' : list.TotalActiveSubscribers == undefined }">
                <p class="ac-whitespace-nowrap ac-flex ac-justify-between">
                  <span class="ac-mr-10">Aktive Empänger gesamt:</span>
                  <strong class="ac-w-10 ac-text-right" v-text="list.TotalActiveSubscribers"></strong></p>
                <p class="ac-whitespace-nowrap ac-flex ac-justify-between">
                  <span class="ac-mr-10">Aktive Empänger diesen Monat:</span>
                  <strong class="ac-w-10 ac-text-right" v-text="list.NewActiveSubscribersThisMonth"></strong></p>
                <p class="ac-whitespace-nowrap ac-flex ac-justify-between">
                  <span class="ac-mr-10">Aktive Empänger diese Woche:</span>
                  <strong class="ac-w-10 ac-text-right" v-text="list.NewActiveSubscribersThisWeek"></strong></p>
              </div>
            </div>
          </label>
        </li>
      </ul>
      <div v-show="isLoading" class="ac-absolute ac-top-0 ac-left-0 ac-w-full ac-h-full ac-bg-white ac-bg-opacity-25 ac-flex ac-justify-center ac-items-center">
        <svg class="ac-animate-spin ac--ml-1 ac-mr-3 ac-h-10 ac-w-10 ac-text-plugin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="ac-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="ac-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>
  </div>
</template>

<script>

import Axios from "axios";
import Qs from "qs"

export default {
  name: "SubscriberLists",
  props: ['lists', 'usedListsPreload'],
  data() {
    return {
      listDetails: this.lists,
      isLoading: false,
      setLists: this.usedListsPreload,
      retry: []
    }
  },
  mounted() {
    this.listDetails.forEach((list, index) => this.loadListDetails(list, index));
  },
  methods: {
    save() {

      this.isLoading = true;

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampainger_update_used_lists',
        nonce: xhr.nonce,
        lists: this.setLists
      })).then((rsp) => this.isLoading = false)
    },
    loadListDetails(list, index) {

      Axios.post(xhr.ajaxurl, Qs.stringify({
        action: 'autocampainger_load_list_details',
        nonce: xhr.nonce,
        list_id: list.ListID
      })).then((rsp) => {
        this.listDetails[index] = {...this.listDetails[index], ...rsp.data};
        this.listDetails.sort((a, b) => b.TotalActiveSubscribers - a.TotalActiveSubscribers);
      }).catch(() => {
        this.loadListDetails(list, index)
      });
    }
  }
}

</script>

<style scoped>

</style>