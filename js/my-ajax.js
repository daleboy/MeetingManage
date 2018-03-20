var projectUrl = '/MeetingManage/index.php/';
var perPageNum = 20;

var MyAjax = {
    defaults: {
        type: "POST",
        url: "",
        data: {},
        dataType: "json",
        async: true,
        layerIndex: "",
        beforeSend: function() {
            layerIndex = layer.load(3);
        },
        complete: function() {
            layer.close(layerIndex);
        }
    },
    request: function(options, callback, failCallback) {
        var settings = $.extend(this.defaults, options);
        $.ajax({
            url: settings.url,
            type: settings.type,
            dataType: settings.dataType,
            data: settings.data,
            async: settings.async,
            beforeSend: settings.beforeSend,
            success: function(result) {
                if (callback)
                    callback(result);
            },
            complete: settings.complete,
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (failCallback) {
                    var failMessage = {};
                    failMessage.XMLHttpRequest = XMLHttpRequest;
                    failMessage.textStatus = textStatus;
                    failMessage.errorThrown = errorThrown;
                    failCallback(failMessage);
                }
                else {
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            }
        });
    }
}

function CutStr(str, len) {
    var str_length = 0;
    var str_len = 0;
    str_cut = new String();
    str_len = str.length;
    for (var i = 0; i < str_len; i++) {
        a = str.charAt(i);
        str_length++;
        if (escape(a).length > 4) {
            //中文字符的长度经编码之后大于4  
            str_length++;
        }
        str_cut = str_cut.concat(a);
        if (str_length >= len) {
            str_cut = str_cut.concat("...");
            return str_cut;
        }
    }
    //如果给定字符串小于指定长度，则返回源字符串；  
    if (str_length < len) {
        return str;
    }
}