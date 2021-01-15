var MS = MS || {};

MS.core = {
    user: {
        ID: 0,
        bAdmin: false,
        getID: function (){
            return MS.core.user.ID;
        },
        isAdmin: function () {
            return MS.core.user.bAdmin;
        }
    },
    session: {
        ID: "",
        getID: function () {
            return MS.core.session.ID;
        }
    },
    ajaxHandlerUrl: '/ms/core/tools/ajax.php',
    ajaxType: 'POST',
    ajaxDataType: 'JSON',
    ajax: function (data, successFunction, handlerUrl='', type='', dataType='') {
        if (handlerUrl == '')
        {
            handlerUrl = MS.core.ajaxHandlerUrl;
        }
        if (type == '')
        {
            type = MS.core.ajaxType;
        }
        if (dataType == '')
        {
            dataType = MS.core.ajaxDataType;
        }
        data.session_id = MS.core.session.getID();
        $.ajax({
            type: type,
            url: handlerUrl,
            data: data,
            success: successFunction,
            dataType: dataType
        });
    },
    showError: function (message) {
        //TODO: Доделать вывод ошибок
        alert(message);
    },
    startWait: function () {
        //TODO: Сделать функционал отображения ожидания данных
    },
    stopWait: function () {
        //TODO: Сделать функционал убирающий изображение ожидания данных
    }
};

function ms_sessid ()
{
    return MS.core.session.getID();
}
