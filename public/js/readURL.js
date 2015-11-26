function readURL(input) {
    $("#preview_photo").hide();
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#preview_photo')
                .attr('src', e.target.result)
                .width(600);
                $("#preview_photo").show();
        };
        reader.readAsDataURL(input.files[0]);
    }
    document.getElementById("image_rotate").style.display = 'block';

    old_image  = document.getElementById("old_image_rotate");
    new_rotate = document.getElementById("rotate");

    if(old_image != null)
        old_image.style.display = 'none';
    if(new_rotate != null)
        new_rotate.value = 0;

}



