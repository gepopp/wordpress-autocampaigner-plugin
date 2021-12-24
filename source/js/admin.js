import '../scss/admin.scss';
import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css'
import Vue from "vue";
import SubscriberLists from "./admin/SubscriberLists.vue";
import TemplateList from "./admin/TemplateList.vue";
import CreateCampaign from "./admin/CreateCampaign.vue";
import Editor from "./admin/Editor.vue";

import MultilineEditor from "./admin/MultilineEditor.vue";
import Singleline from "./admin/Singleline.vue";
import ImageEditable from "./admin/ImageEditable.vue";
import EditorRepeater from "./admin/EditorRepeater.vue";
import EditorLayout from "./admin/EditorLayout.vue";
import VueQuillEditor from 'vue-quill-editor'

(function ($) {


    $(function () {
        $('.color-picker').wpColorPicker();
    })

    var active = 'ac-main';

    function setActive(target) {
        $('.ac-tab').each(function (index, element) {
            if ($(element).data('tab') == target) {
                $(element).show();
            } else {
                $(element).hide();
            }
        });
        $('.ac-tab-button').each(function (index, element) {
            $(element).removeClass('ac-border-b-2 ac-border-plugin ac-border-plugin ac-font-bold');
        });
        $('#' + target).addClass('ac-border-b-2 ac-border-plugin ac-border-plugin ac-font-bold')
    }

    setActive(active);

    $('.ac-tab-button').on('click', function (event) {
        setActive($(event.target).attr('id'));


    });

})(jQuery)

Vue.component('multiline', MultilineEditor);

Vue.component('singleline', Singleline);

Vue.component('image-editable', ImageEditable);

Vue.component('repeater', EditorRepeater);

Vue.component('layout', EditorLayout);

Vue.use(VueQuillEditor, {
    theme: 'bubble', modules: {
        toolbar: [
            [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
            [{'header': [1, 2, 3, 4, 5, 6, false]}],
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            [{ 'align': [] }],
            [{'header': 3}, {'header': 4}], // custom button values
            [{'list': 'ordered'}, {'list': 'bullet'}],
            ['link']
        ]
    }
})

import DraftSender from "./admin/DraftSender.vue";

Vue.config.ignoredElements = ['unsubscribe']

const app = new Vue({
    el: '#automailer-page',
    components: {
        SubscriberLists,
        TemplateList,
        CreateCampaign,
        Editor,
        DraftSender
    }
})