"use strict";null==Craft.Themes&&(Craft.Themes={}),Craft.Themes.RulesTable=Craft.EditableTable.extend({init:function(t,s,i,a){var e=this;a.onAddRow=function(t){e.onAddRow(t)},Craft.EditableTable.prototype.init.call(this,t,s,i,a),this.checkAllRows()},checkAllRows:function(){var t=this;$.each(this.$tbody.find("tr"),function(){t.onAddRow($(this))})},disableRow:function(t,s){s?t.removeClass("disabled"):t.addClass("disabled")},onAddRow:function(t){var s=t.find("td.enabled .lightswitch");this.disableRow(t,s.hasClass("on"));var i=this;s.change(function(){i.disableRow(t,s.hasClass("on"))})}});
//# sourceMappingURL=rules.js.map
