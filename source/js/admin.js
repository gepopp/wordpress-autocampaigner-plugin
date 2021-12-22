import '../scss/admin.scss';
import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme

(function ($){


    $(function (){
        $('.color-picker').wpColorPicker();
    })

    var active = 'ac-main';

    function setActive(target){
        $('.ac-tab').each(function (index, element){
            if($(element).data('tab') == target){
                $(element).show();
            }else{
                $(element).hide();
            }
        });
        $('.ac-tab-button').each(function (index, element){
            $(element).removeClass('ac-border-b-2 ac-border-plugin ac-border-plugin ac-font-bold');
        });
        $('#' + target).addClass('ac-border-b-2 ac-border-plugin ac-border-plugin ac-font-bold')
    }

    setActive(active);

    $('.ac-tab-button').on('click', function (event){
        setActive( $(event.target).attr('id'));


    });

})(jQuery)

import Vue from "vue";
import SubscriberLists from "./admin/SubscriberLists.vue";
import TemplateList from "./admin/TemplateList.vue";
import CreateCampaign from "./admin/CreateCampaign.vue";
import Editor from "./admin/Editor.vue";

import MultilineEditor from "./admin/MultilineEditor.vue";
Vue.component('multiline', MultilineEditor);

import Singleline from "./admin/Singleline.vue";
Vue.component('singleline', Singleline);

import ImageEditable from "./admin/ImageEditable.vue";
Vue.component('image-editable', ImageEditable);

import EditorRepeater from "./admin/EditorRepeater.vue";
Vue.component('repeater', EditorRepeater);

import EditorLayout from "./admin/EditorLayout.vue";
Vue.component('layout', EditorLayout);

import VueQuillEditor from 'vue-quill-editor'
Vue.use(VueQuillEditor)

Vue.config.ignoredElements = ['unsubscribe']

const app = new Vue({
    el: '#automailer-page',
    components: {
        SubscriberLists,
        TemplateList,
        CreateCampaign,
        Editor,
    }
})