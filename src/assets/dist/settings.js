"use strict";function install(t){t.attr("disabled",!0),t.next().show(),$.ajax({method:"post",url:t.attr("href"),dataType:"json",headers:{"X-CSRF-Token":Craft.csrfTokenValue}}).done(function(t){Craft.cp.displayNotice(t.message)}).fail(function(t){Craft.cp.displayError(t.responseJSON.error)}).always(function(){t.next().hide(),t.attr("disabled",!1)})}$(function(){$(".install").click(function(t){t.preventDefault(),install($(this))})});
//# sourceMappingURL=settings.js.map
