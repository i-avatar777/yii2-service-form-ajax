/**
 * Created by ramha on 11.01.2021.
 */

var iAvatar777_ActiveForm = {
    /**
     * array
     * [{id:text,data:object}]
     */
    formArray: [],

    /**
     *
     * */
    get: function (id) {
        var i = 0;
        for(i = 0; i < iAvatar777_ActiveForm.formArray.length; i++) {
            if (iAvatar777_ActiveForm.formArray[i].id == id) {
                return iAvatar777_ActiveForm.formArray[i].data;
            }
        }

        return null;
    },


    /**
     *
     * */
    init: function (formId, formSelector, formUrl, functionSuccess, type) {
        var data = {
            onClick: null,
            url: '',
            lastStart: -1,
            thisStart: null,
            delta: 1000,
            isStart: null,
            success1: functionSuccess
        };
        if (type == 1) {
            data.url = $(formSelector).attr('action');
        }
        if (type == 2) {
            data.url = formUrl;
        }

        data.onClick = function(e) {
            var b = $(this);
            b.off('click');
            var title = b.html();
            b.html($('<i>', {class: 'fa fa-spinner fa-spin fa-fw'}));
            b.attr('disabled', 'disabled');

            ajaxJson({
                url: data.url,
                data: $(formSelector).serializeArray(),
                success: function(ret) {
                    b.on('click', data.onClick);
                    b.html(title);
                    b.removeAttr('disabled');
                    data.success1(ret);
                },
                errorScript: function(ret) {
                    b.on('click', data.onClick);
                    b.html(title);
                    b.removeAttr('disabled');
                    if (ret.id == 102) {
                        for (var key in ret.data.errors) {
                            if (ret.data.errors.hasOwnProperty(key)) {
                                var name = key;
                                var value = ret.data.errors[key];
                                var id;
                                for (var key2 in ret.data.fields) {
                                    if (ret.data.fields.hasOwnProperty(key2)) {
                                        var name2 = key2;
                                        var value2 = ret.data.fields[key2];
                                        if (name == name2) {
                                            id = 'field-' + value2;
                                        }
                                    }
                                }
                                var g = $('.' + id);
                                g.addClass('has-error');
                                g.find('p.help-block-error').html(value.join('<br>')).show();
                            }
                        }
                    }
                }
            });
        };

        console.log([formSelector + ' .buttonAction', $(formSelector + ' .buttonAction'), data]);
        $(formSelector + ' .buttonAction').click(data.onClick);

        $(formSelector).submit(function(ret) {
            data.isStart = true;
            data.thisStart = (new Date()).getTime();

            if (data.lastStart == -1) {
                data.lastStart = data.thisStart;
            } else {
                if (data.lastStart + data.delta > data.thisStart) {
                    data.isStart = false;
                }
            }

            if (data.isStart) {
                var b = $(this).find('.buttonAction');
                b.off('click');
                var title = b.html();
                b.html($('<i>', {class: 'fa fa-spinner fa-spin fa-fw'}));
                b.attr('disabled', 'disabled');

                ajaxJson({
                    url: data.url,
                    data: $(formSelector).serializeArray(),
                    success: function(ret) {
                        b.on('click', data.onClick);
                        b.html(title);
                        b.removeAttr('disabled');
                        data.success1(ret);
                    },
                    errorScript: function(ret) {
                        b.on('click', data.onClick);
                        b.html(title);
                        b.removeAttr('disabled');
                        if (ret.id == 102) {
                            for (var key in ret.data.errors) {
                                if (ret.data.errors.hasOwnProperty(key)) {
                                    var name = key;
                                    var value = ret.data.errors[key];
                                    var id;
                                    for (var key2 in ret.data.fields) {
                                        if (ret.data.fields.hasOwnProperty(key2)) {
                                            var name2 = key2;
                                            var value2 = ret.data.fields[key2];
                                            if (name == name2) {
                                                id = 'field-' + value2;
                                            }
                                        }
                                    }
                                    var g = $('.' + id);
                                    g.addClass('has-error');
                                    g.find('p.help-block-error').html(value.join('<br>')).show();
                                }
                            }
                        }
                    }
                });
            }

            return false;
        });

        // снимает ошибочные признаки поля при фокусе
        $(formSelector + ' .form-control').on('focus', function() {
            var o = $(this);
            var p = o.parent();
            if (p.hasClass('input-group')) {
                p = p.parent();
            }
            p.removeClass('has-error');
            p.find('p.help-block-error').hide();
        });

        iAvatar777_ActiveForm.formArray.push({
            id: formId,
            data: data
        });
    },

    getFields: function (formSelector, fields) {
        var rows = [];
        for(var i=0; i < fields.length; i++) {
            var item = fields[i];
            var value;

            if (item.type == 'function') {
                value = item.function();
            } else {
                value = $('#' + item.id).val();
            }
            rows.push({
                name: item.name,
                value: value
            });
        }


        return rows;
    }
};