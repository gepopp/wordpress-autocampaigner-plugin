import '../scss/admin.scss';

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

const app = new Vue({
    el: '#automailer-page',
    components: {
        SubscriberLists,
        TemplateList
    }
})