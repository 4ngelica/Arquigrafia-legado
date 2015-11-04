var Rotate = (function(){

    var HAS_CANVAS = (function(){

        var canvas = document.createElement('canvas');
        return !!(canvas && canvas.getContext);  
    })();

    return function(img, angle) {

        var sin = Math.sin(angle),
            cos = Math.cos(angle);

        if(HAS_CANVAS) return (function(){

            var loader = new Image();

            loader.onload = function(){

                var sin = Math.sin(angle),
                cos = Math.cos(angle);

                var canvas = document.createElement('canvas');

                var imgWidth = this.width;
                var imgHeight = this.height;


                var fullWidth = Math.abs(sin) * imgHeight +  Math.abs(cos) * imgWidth;
                var fullHeight = Math.abs(cos) * imgHeight +  Math.abs(sin) * imgWidth;

                canvas.setAttribute('width',fullWidth);
                canvas.setAttribute('height',fullHeight);

                var g = canvas.getContext('2d');

                g.translate(fullWidth / 2, fullHeight / 2);

                g.rotate(angle);

                g.drawImage(loader,-imgWidth/2, -imgHeight/2, imgWidth, imgHeight);

                img.src = canvas.toDataURL();

                input_file   = document.getElementById('imageUpload');
                input_rotate = document.getElementById('rotate');

                var input  = document.createElement('input');
                input.type = "hidden";
                input.name = "rotate";
                input.id   = "rotate";
                // Popula o value com -angle pois o package intervention
                // interpreta a rotacao de modo contrario ao context 2d.
                if(input_rotate == null) {
                    input.value = -angle * 180/Math.PI;
                    input_file.parentNode.appendChild(input);
                }
                else {
                    input.value = ((-angle * 180/Math.PI) +  parseInt(input_rotate.value)) % 360;
                    input_file.parentNode.replaceChild(input, input_rotate);
                }
            };

            loader.src = img.src;
        })();
    } 
})();
