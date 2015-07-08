(function ($) {
    $.fn.progressbar = function (options) 
    {
        var settings = $.extend({
            width:'300px',
            height:'10px',
            padding:'3px',
            border:'1px solid',
            margin_top: '5px'
        }, options);
 
        //Set css to container
        $(this).css({
            'width':settings.width,
            'border':settings.border,
            'border-radius':'2px',
            'overflow':'hidden',
            'display':'inline-block',
            // 'padding': settings.padding,
            'margin':'0',
            'margin-top' : settings.margin_top
            });
 
        // add progress bar to container
        var progressbar =$("<div></div>");
        progressbar.css({
        'height':settings.height,
        'text-align': 'center',
        'vertical-align':'middle',
        'color': '#fff',
        'font-size' : '14px',
        'width': '0px',
        // 'border-radius': '2px',
        });
 
        $(this).append(progressbar);
 
        this.progress = function(value)
        {
            var width = $(this).width() * value/100 - 5;
            var progress = getProgressLevel(value);
            progressbar.width(width);
            this.css({ 'color' : progress[1], 'border-color' : progress[1] });
            progressbar.css({ 'background-color' : progress[1] });
        }
        return this;
    };
 
}(jQuery));

function getProgressLevel(value) {
    if (value < 60) {
        return ['Razoável', '#cd7f32'];
    } else if (value < 80) {
        return ['bom', '#c0c0c0'];
    }
    return ['ótimo', '#fdd017'];
}