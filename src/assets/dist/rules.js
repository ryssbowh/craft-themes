"use strict";function showHideConsoleThemeField(e){e?$(".console-themes").slideDown():$(".console-themes").slideUp()}null==Craft.Themes&&(Craft.Themes={}),Craft.Themes.RulesTable=Craft.EditableTable.extend({init:function(e,s,o,i){var n=this;i.onAddRow=function(e){n.onAddRow(e)},Craft.EditableTable.prototype.init.call(this,e,s,o,i),this.checkAllRows()},checkAllRows:function(){var e=this;$.each(this.$tbody.find("tr"),function(){e.onAddRow($(this))})},disableRow:function(e,s){s?e.removeClass("disabled"):e.addClass("disabled")},onAddRow:function(e){var s=e.find("td.enabled .lightswitch");this.disableRow(e,s.hasClass("on"));var o=this;s.change(function(){o.disableRow(e,s.hasClass("on"))})}}),$(function(){showHideConsoleThemeField($(".console-lightswitch").hasClass("on")),$(".console-lightswitch").on("change",function(){showHideConsoleThemeField($(".console-lightswitch").hasClass("on"))})});
//# sourceMappingURL=rules.js.map
