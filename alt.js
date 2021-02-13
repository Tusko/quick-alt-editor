/*jslint browser: true, white: true, vars: true, plusplus: true, regexp: true, indent: 4, maxerr: 50 */
/*global $, jQuery, qTranslateConfig, ajaxurl*/

jQuery(document).ready(function ($) {
    "use strict";
    if($(".wp-list-table.media").length > 0 && location.search.indexOf('list') === -1) {
        location.search = '?mode=list'
    }
    function wpa_update_alt_field($attachment){
        var altID = $("#wpa_mc_" + $attachment);
        altID.next().show();
        var altText = altID.val();

        if(altID.parents(".tt-m-alt").find("input").length > 1) {
            var langs = qTranslateConfig.qtx.getLanguages();
            altText = "";
            $.each(langs, function(lang){
                altText += "[:" + lang + "]" + $("input[name=\"qtranslate-fields[wpa_mc_qtx][" + lang + "]\"").val();
            });
            altText += "[:]";
        }

        $.post(ajaxurl, {
            action: "wpa_media_alt_update",
            "_wpnonce": $("[name=\"_wpnonce-" + $attachment + "\"]").val(),
            "post_id": $attachment,
            "alt_text": altText
        }, function (alt) {
            if (alt) {
                altID.next().hide();
            }
        });
    }

    $(this).on("keydown", ".tt-m-alt input.wpa_mc_qtx", function(e) {
        var key = e.which || e.keyCode || 0;
        if(key === 13) {
            $(this).blur();
            return false;
        }
    })
      .on("blur", ".tt-m-alt input.wpa_mc_qtx", function() {
          if($(this).parents(".tt-m-alt").find("input").length > 1) {
              $("input[name=\"qtranslate-fields[wpa_mc_qtx][" + qTranslateConfig.activeLanguage + "]\"]").val($(this).val());
          }
          wpa_update_alt_field($(this).attr("id").replace("wpa_mc_", ""));
          return false;
      });

});
