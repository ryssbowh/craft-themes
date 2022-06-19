"use strict";var ThemesUtility={$select:null,$resultContainer:null,$spinner:null,init:function(){this.$select=$("select#themes-select"),this.$resultContainer=$("#templates-results"),this.$spinner=$(".result-container .spinner-wrapper"),this.loadTemplates(),this.initSelect()},initSelect:function(){var t=this;this.$select.change(function(){t.loadTemplates()})},loadTemplates:function(){var e=this;this.$resultContainer.html(""),this.toggleSpinner(!0);var t={theme:this.$select.val()};t[Craft.csrfTokenName]=Craft.csrfTokenValue,$.ajax({method:"post",url:"/?action=themes/cp-themes/overridden-templates",data:t}).done(function(t){e.$resultContainer.html(t),e.sortPadding(e.$resultContainer.find("> ul"))}).fail(function(){e.$resultContainer.html("<p>"+Craft.t("themes","Error while fetching the theme's templates")+"</p>")}).always(function(){e.toggleSpinner(!1)})},sortPadding:function(t){var i=this,s=1<arguments.length&&void 0!==arguments[1]?arguments[1]:0;$.each(t.find("> li"),function(t,e){$(e).find("> div > .padded").css("padding-left",s);var n=$(e).find(">ul");n.length&&i.sortPadding(n,s+15)})},toggleSpinner:function(t){t?this.$spinner.show():this.$spinner.hide()}};ThemesUtility.init();