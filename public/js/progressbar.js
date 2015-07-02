(function ($) {
    $.fn.progressbar = function (options) 
    {
        var settings = $.extend({
            width:'300px',
            height:'20px',
            padding:'3px',
            border:'1px solid'
        }, options);
 
        //Set css to container
        $(this).css({
            'width':settings.width,
            'border':settings.border,
            'border-radius':'5px',
            'overflow':'hidden',
            'display':'inline-block',
            'padding': settings.padding,
            'margin':'0'
            });
 
        // add progress bar to container
        var progressbar =$("<div></div>");
        progressbar.css({
        'height':settings.height,
        'text-align': 'center',
        'vertical-align':'middle',
        'color': '#fff',
        'font-size' : '17px',
        'width': '0px',
        'border-radius': '3px',
        });
 
        $(this).append(progressbar);
 
        this.progress = function(value)
        {
            var width = $(this).width() * value/100;
            var progress = getProgressLevel(value);
            progressbar.width(width).html(value + '% (' + progress[0] + ')');
            this.css({ 'color' : progress[1], 'border-color' : progress[1] });
            progressbar.css({ 'background-color' : progress[1] });
        }
        return this;
    };
 
}(jQuery));

function getProgressLevel(value) {
    if (value < 50) {
        return ['baixo', '#ff8533'];
    } else if (value < 71) {
        return ['razoável', '#ecae33'];
    } else if (value < 81) {
        return ['bom', '#66CCFF'];
    } else if (value < 91) {
        return ['muito bom', '#0ba1b5'];
    }
    return ['ótimo', '#339933'];
}