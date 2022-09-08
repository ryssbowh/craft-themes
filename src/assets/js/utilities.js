var ThemesUtility = {
    $select: null,
    $resultContainer: null,
    $spinner: null,

    init: function ()
    {
        this.$select = $('select#themes-select');    
        this.$resultContainer = $('#templates-results');
        this.$spinner = $('.result-container .spinner-wrapper');
        this.loadTemplates();
        this.initSelect();
    },

    initSelect: function ()
    {
        this.$select.change(() => {
            this.loadTemplates();
        });
    },

    loadTemplates: function ()
    {
        this.$resultContainer.html('');
        this.toggleSpinner(true);
        let data = {
            theme: this.$select.val(),
            action: 'themes/cp-themes/overridden-templates'
        };
        data[Craft.csrfTokenName] = Craft.csrfTokenValue;
        $.ajax({
            method: 'post',
            url: Craft.getActionUrl('/'),
            data: data
        }).done((data) => {
            this.$resultContainer.html(data);
            this.sortPadding(this.$resultContainer.find('> ul'));
        }).fail(() => {
            this.$resultContainer.html('<p>' + Craft.t('themes', "Error while fetching the theme's templates") + '</p>');
        }).always(() => {
            this.toggleSpinner(false);
        })
    },

    sortPadding: function (container, padding = 0)
    {
        $.each(container.find('> li'), (i, item) => {
            $(item).find('> div > .padded').css('padding-left', padding);
            let children = $(item).find('>ul');
            if (children.length) {
                this.sortPadding(children, padding + 15);
            }
        });
    },

    toggleSpinner: function (show)
    {
        if (show) {
            this.$spinner.show();
        } else {
            this.$spinner.hide();
        }
    }
};

ThemesUtility.init();