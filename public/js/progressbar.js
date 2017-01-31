(function ($) {
    $.fn.progressbar = function (options) 
    {
        var settings = $.extend({
            width:'300px',
            height:'5px',
            padding:'3px',
            margin_top: '5px'
        }, options);
 
        //Set css to container
        $(this).css({
            'width':settings.width,
            'border':settings.border,
            'border-radius':'2px',
            'overflow':'hidden',
            'display':'inline-block',
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
            var width = getProgressWidth( $(this).width(), value );
            var progress = getProgressLevel(value);
            console.log(value);
            console.log(progress);
            progressbar.width(width);
            this.css({ 'color' : progress });
            progressbar.css({ 'background-color' : progress });
        };

        this.levelUp = function(value) {
            var newWidth = getProgressWidth( $(this).width(), value );
            var progress = getProgressLevel(value);
            console.log(value);
            console.log(progress);
            this.css({ 
                'color' : progress, 
            }, 'slow');
            progressbar.css({ 'background-color': progress, }).animate({
                'width': newWidth
            }, 'slow');
        }
        return this;
    };
 
}(jQuery));

function getProgressWidth(container_width, value) {
    return container_width * value/100 - 5;
}

function getProgressLevel(value) {
    if (value < 60) {
        return '#cd7f32';
    } else if (value < 80) {
        return '#eee';
    }
    return '#fdd017';
}