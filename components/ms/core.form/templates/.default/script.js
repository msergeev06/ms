function checkValue (input,parent,err,namespace,func,url)
{
    var data = {
        namespace: namespace,
        func: func,
        value: input.val()
    };
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
}

function checkValueNumber (input,parent,err,step,min,max,namespace,func,url)
{
    var data = {
        namespace: namespace,
        func: func,
        value: input.val(),
        step: step,
        min: min,
        max: max
    };
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
}