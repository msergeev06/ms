function checkData (data,parent,err,url)
{
    MS.core.ajax(
        data,
        function(json){
            console.log(json);
            if (json.status == 'success')
            {
                if (json.data.result != 'success')
                {
                    parent.removeClass('has-success');
                    parent.addClass('has-error');
                    err.text(json.data.err);
                }
                else
                {
                    parent.removeClass('has-error');
                    parent.addClass('has-success');
                    err.text('');
                }
            }
            else
            {
                MS.core.showError(json.message);
            }
        },
        url
    );
}

function checkValue (input,parent,err,namespace,func,url)
{
    checkData(
        {
            namespace: namespace,
            func: func,
            value: input.val()
        },
        parent,
        err,
        url
    );
    /*
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(json){
            console.log(json);
            if (json.status == 'error')
            {
                parent.removeClass('has-success');
                parent.addClass('has-error');
                err.text(json.err);
            }
            else if (json.status == 'OK')
            {
                parent.removeClass('has-error');
                parent.addClass('has-success');
                err.text('');
            }
        },
        dataType: "JSON"
    });
    */
}

function checkValueNumber (input,parent,err,step,min,max,namespace,func,url)
{
    checkData(
        {
            namespace: namespace,
            func: func,
            value: input.val(),
            step: step,
            min: min,
            max: max
        },
        parent,
        err,
        url
    );
/*
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(json){
            console.log(json);
            if (json.status == 'error')
            {
                parent.removeClass('has-success');
                parent.addClass('has-error');
                err.text(json.err);
            }
            else if (json.status == 'OK')
            {
                parent.removeClass('has-error');
                parent.addClass('has-success');
                err.text('');
            }
        },
        dataType: "JSON"
    });
*/
}