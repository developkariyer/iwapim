pimcore.registerNS("pimcore.layout.portlets.BlackbitIframePortletBundle");
pimcore.layout.portlets.BlackbitIframePortletBundle = Class.create(pimcore.layout.portlets.abstract, {

    getType: function () {
        return "pimcore.layout.portlets.BlackbitIframePortletBundle";
    },

    setConfig: function (config) {
        var parsed = {
            urlField: null
        };

        try {
            if (config) {
                parsed = JSON.parse(config);
            }
        } catch (e) {
            console.error('Failed to parse IframePortlet widget config: ', e);
        }

        this.config = parsed;
    },

    getName: function () {
        return t('BlackbitIframePortletBundle_portlet_name');
    },

    getIcon: function () {
        return "pimcore_icon_iframe";
    },

    getLayout: function (portletId) {
        var that = this;
        var defaultConf = this.getDefaultConfig();
        defaultConf.tools = [
            {
                type: 'gear',
                handler: this.editSettings.bind(this)
            },
            {
                type: 'close',
                handler: this.remove.bind(this)
            }
        ];


        this.layout = Ext.create('Portal.view.Portlet', Object.assign(defaultConf, {
            title: this.getName(),
            iconCls: this.getIcon(),
            layout: "fit",
            height: 650,
        }));

        if (this.config?.sourceUrl) {
            this.layout.add({
                xtype: 'box',
                autoEl: {
                    tag: 'iframe',
                    src: this.config.sourceUrl,
                    width: 640,
                    height: 480
                }
            });
        }

        this.layout.portletId = portletId;
        return this.layout;
    },

    editSettings: function () {
        var config = this.config || {};

        var urlField = new Ext.form.TextField({
            name: "sourceUrl",
            fieldLabel: t('BlackbitIframePortletBundle_label_for_url'),
            width: 500,
            value: this.config?.sourceUrl || ''
        });

        var win = new Ext.Window({
            width: 550,
            height: 280,
            modal: true,
            title: t('BlackbitIframePortletBundle_title_of_settings'),
            closeAction: "destroy",
            items: [
                {
                    xtype: "form",
                    bodyStyle: "padding: 10px",
                    items: [
                        urlField,
                        {
                            xtype: "button",
                            text: t('BlackbitIframePortletBundle_button_text'),
                            handler: function (button) {
                                var form = button.up('form').getForm();
                                this.updateSettings(form.getValues());

                                win.close();
                            }.bind(this)
                        }
                    ]
                }
            ]
        });
        win.show();
    },

    updateSettings: function (data) {
        this.config = data;

        Ext.Ajax.request({
            url: Routing.generate('pimcore_admin_portal_updateportletconfig'),
            method: 'PUT',
            params: {
                key: this.portal.key,
                id: this.layout.portletId,
                config: JSON.stringify(this.config)
            },
            success: function () {
                this.renderIframe(this.config);
            }.bind(this),

            failure: function() {
                console.log('error')
            }.bind(this)
        });
    },

    renderIframe: function (config) {
        var that = this;
        console.log(config);
        var layout = this.layout;
        if (!config || !config.sourceUrl) {
            layout.removeAll();
            layout.add(new Ext.Component({
                html: t('BlackbitIframePortletBundle_empty_url_error'),
                padding: 20
            }));
            return;
        }
        var iframe = new Ext.Component({
            autoEl: {
                tag: 'iframe',
                src: config.sourceUrl,
                frameborder: 0
            }
        });
        layout.removeAll();
        layout.add(iframe);
    }
});
