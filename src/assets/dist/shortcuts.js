var ThemesShortcuts = {
    init: function (data) {
        var _this = this;
        document.querySelectorAll('[data-layout-shortcut]').forEach(function(elem){
            var id = elem.dataset.layoutShortcut;
            var firstChild = elem.firstChild;
            if (data.hasOwnProperty(id)) {
                var layoutData = data[id];
                var tag = document.createElement('div');
                var cog = document.createElement('div');
                var listWrapper = document.createElement('div');
                var list = document.createElement('ul');
                tag.classList.add('themes-shortcuts');
                cog.classList.add('cog');
                listWrapper.classList.add('list');
                listWrapper.appendChild(list);
                tag.appendChild(cog);
                tag.appendChild(listWrapper);
                layoutData.forEach(function(link) {
                    var a = document.createElement('a');
                    var li = document.createElement('li');
                    a.href = link.url;
                    a.innerHTML = link.label;
                    a.target = '_blank';
                    li.appendChild(a);
                    list.appendChild(li);
                });
                elem.insertBefore(tag, elem.firstChild);
                tag.addEventListener("click", function (event) {
                    var opened = this.classList.contains('open');
                    _this.closeAll();
                    if (!opened) {
                        this.classList.add('open');
                        event.stopPropagation();
                    }
                });
            }
        });
        document.body.addEventListener("click", function () {
            _this.closeAll();
        });
    },

    closeAll: function () {
        document.querySelectorAll('.themes-shortcuts').forEach(function(elem){
            elem.classList.remove('open');
        });
    }
};

ThemesShortcuts.init(shortcutData);