/**
 * Created by ulas on 12/05/2017.
 */


lknsuite = {
    init: function () {
    },

    getaccounts_filter: function () {
        var $ = jQuery.noConflict();
        $("._lknsuite_accounts_filter").keyup(function (e) {
            var search = $(this).val().toLowerCase();


            $("#_lknsuite_accounts li").each(function () {
                var li = $(this);
                var v = $(this).text().toLowerCase();

                if (search == '.') {
                    if (li.children("label").children("input:checked").length > 0) {
                        li.fadeIn();
                    } else {
                        li.hide();
                    }

                } else if (search == '') {
                    li.fadeIn();
                } else {
                    if (v.indexOf(search) > -1) {
                        //show
                        li.fadeIn();
                    } else {
                        li.hide();
                    }
                }

                li = v = null;

            });


        });

    },

    getaccounts: function (returnData) {
        var $ = jQuery.noConflict();
        var _lknsuite_meta_box = $("._lknsuite_meta_box");



        var html;
        var lkn = 0;
        var count;

        if (returnData.status == '1') {


            count = (Object.keys(returnData.data).length);
            if(count>0){

                html = '<input class="form-input-tip _lknsuite_accounts_filter" ' +
                    ' autocomplete="off" ' +
                    'value="" type="text" style="margin-top: 5px;"><ul id="_lknsuite_accounts" class="categorychecklist form-no-clear">';


                for (lkn = 0; lkn < count; lkn++) {

                    var obj = returnData.data[lkn];
                    var account_title = obj.account_title;
                    var account_id = obj.account_id;
                    var sm_site_text = obj.sm_site_text;
                    var always_select = obj.always_select;

                    html += '<li class="_lknsuite_category">';
                    html += '<label class="selectit" for="in-category-' + account_id + '" >' +
                        '<input value="' + account_id + '" name="lknsuite_accounts[]" ';
                    if (always_select == '1') {
                        html += ' checked="checked"';
                    }

                    html += 'id="in-category-' + account_id + '" type="checkbox"> ' + account_title + ' (' + sm_site_text + ')</label></li>';

                    obj = sm_site_text = account_id = account_title = null;
                }
            }else{

                html = '<ul id="_lknsuite_accounts" class="categorychecklist form-no-clear">';
                html += '<li class="_lknsuite_category">You have not authorized any account on lknsuite.com.<br />Please visit <a target="_blank" href="https://www.lknsuite.com/publisher/list_sm/">https://www.lknsuite.com/publisher/list_sm/</a> and autorize your first account on lknsuite.com </li>';
            }


            html += '</ul>';
        } else {
            html = returnData.msg;
        }

        _lknsuite_meta_box.html(html);

        lknsuite.getaccounts_filter();

        count = $ = _lknsuite_meta_box = html  = returnData = null;


    }


};

jQuery(document).ready(function () {
    if (jQuery("._lknsuite_meta_box").length > 0) {
        lknsuite.init();
    }

});
