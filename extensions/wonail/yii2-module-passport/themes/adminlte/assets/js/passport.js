jQuery(document).ready(function () {
    $.ajaxSetup({
        beforeSend: function () {
            $("button:submit").attr("disabled", true);
        },
        complete: function () {
            $("button:submit").attr("disabled", false);
        }
    });
    $(document).on("beforeSubmit.yii.activeForm", 'form', function () {
        var self = $(this);
        $.ajax({
            type: "POST",
            url: self.attr("action"),
            data: self.serialize(),
            success: success,
            error: error,
            dataType: "json"
        });
        return false;

        function success(data) {
            var time = 1000;
            if (data.waitSecond) {
                time = data.waitSecond * 1000;
            }

            if (data.jumpUrl) {
                setTimeout(function () {
                    window.location.href = data.jumpUrl;
                }, time);
            }

            if (data.message) {
                if (data.status === 1) {
                    successDialog.alert(data.message);
                } else {
                    errorDialog.alert(data.message);
                }
            }
            
            if (data.status === 0) {
                refreshVerifyCode();
            }
        }
        function error(XMLHttpRequest, textStatus, errorThrown) {
            var message = XMLHttpRequest.responseText ? XMLHttpRequest.responseText : "操作超时，请重新执行";
            errorDialog.alert(message);
        }
        function refreshVerifyCode() {
            if ($("#verifycode-image").length) {
                $("#verifycode-image").yiiCaptcha("refresh");
                $("#verifycode-image").parents('.input-group').find('input').val('');
            }
        }
    });
});