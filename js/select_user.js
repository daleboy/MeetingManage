// var thost = 'http://147.1.7.22:81';
var thost = 'http://147.1.4.52:5200';
// var thost = 'http://147.1.7.26:5200';
var iiv;
$(function () {
    if (!window.layer) {
        alert("请引入layer");
    }
    /**
     *对外暴露的方法
     * @param button
     * @param config
     */
    $.openSelect = function (button, config) {
        if (typeof button == "object") {
            if (button.selector) config.button = button.selector;
            else throw "异常";
        } else config.button = button;
        $(button).data('ddd', config);
        $(button).click(function () {
            function js(now) {
                if (now) {
                    now.value_hide = $(now.hideSelect).val();
                    now.value_show = $(now.showSelect).val();
                    now.button = config.button;
                }
            }

            if (config.user) js(config.user);
            if (config.dept) js(config.dept);
            if (config.fy) js(config.fy);
            if (config.cs) js(config.cs);
            $.ajax({
                url: thost + '/getUuid?call_function=jsonp_callback_1',
                data: {uuid: $(this).data('uuid'), config: JSON.stringify(config)},
                dataType: "jsonp"
            });
        });
    }
});
function jsonp_callback_1(uuid, config) {
    $(config.button).data('uuid', uuid);
    var lop = {
        area: ['666px', '523px']
    };
    $.extend(lop, config);
    config.uuid = uuid;
    $(config.button).data("layerIndex", layer.open({
        title: lop.title,
        content: [thost + '/sel?uuid=' + uuid],
        area: lop.area,
        success: function (layero, index) {
            iiv = setInterval(function () {
                $.ajax({
                    url: thost + '/isDone',
                    dataType: "jsonp",
                    data: {
                        call_function: 'call_sss',
                        uuid: uuid, r: Math.random
                    }
                });
            }, 500);
        },
        end: function () {
            clearInterval(iiv);
            $.ajax({
                url: thost + '/over',
                data: {uuid: uuid},
                dataType: "jsonp"
            });
        },
        type: 2
    }));
}
function no_call() {

}
function call_sss(dat, uuid) {
    if (dat == "true") {
        $.ajax({
            url: thost + '/getUuid?call_function=xiangy',
            data: {uuid: uuid},
            dataType: "jsonp"
        });
    }
}

function xiangy(uuid, config) {
    var yc;
    if (config.over) {
        for (o in config.config) {
            var now = config.config[o];
            if (now.button != null) {
                var nv = config.values[o];
                yc = $(now.button).data("ddd");
                if (now.hideType == "json") {
                    if (yc[config.name[o]].onEnd) {
                        yc[config.name[o]].onEnd(nv);
                    }
                } else {
                    if (nv.ShowValue != null) {
                        var hv = nv.hideValue.join(','), v = $(now.showSelect), sv = $(now.hideSelect).val(hv);
                        if (v.is('input')) {
                            sv = nv.ShowValue.join(',');
                            v.val(sv);
                        } else if (v.is('textarea')) {
                            sv = nv.ShowValue.join(',');
                            v.text(sv);
                        }
                        if (yc != null && yc.onEnd) {
                            //结束回调
                            yc.onEnd(hv, sv, yc);
                        }
                    }
                }
            }
        }
        if (yc.button) {
            layer.close($(yc.button).data("layerIndex"));
        }
    }
}