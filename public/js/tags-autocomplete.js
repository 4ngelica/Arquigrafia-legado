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
}


/**
 * Method to show the retrieved tags in the form 
 * @param {String} tagsJson 
 * @param {textarea} containerText
 * @param {input type} tagInput
 */
function showTags(tagsJson, containerText, tagInput){

    if(tagsJson != null && tagsJson != ''){
        containerText.textext({ plugins: 'tags' });
        var array = eval(tagsJson);  // convert string to array  
        for(i=0; i< array.length; i++){
            containerText.textext()[0].tags().addTags([ array[i] ]);
        }
    }
    tagInput.val('');
}

$(document).ready(function() {


    /*
    $('#tags_input').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/tagList.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ; */
    //ok
    $('#tagsArea').textext({ plugins: 'tags' });
    $('#add_tag').click(function(e) {
        e.preventDefault();
        var tag = $('#tags_input').val();
        if (tag == '') return;
        if ($('#tagsArea').textext()[0] == null) {
            var sizeTags = $('#tags').textext()[0].tags()._formData.length;
            if (sizeTags < 5) {
                $('#tags').textext()[0].tags().addTags([ tag ]);
                $('#tags_input').val('');
            }
        }
        else {
            $('#tagsArea').textext()[0].tags().addTags([ tag ]);
            $('#tags_input').val('');
        }
    });
    $('#tags_input').keypress(function(e) {
            var key = e.which || e.keyCode; //alert("B"+key);
            if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
                e.preventDefault();
        });

    /*$('#workAuthor').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/autor.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;*/
 
    $(function() {
        
        $( "#datePickerImageDate" ).datepicker({
            dateFormat:'dd/mm/yy'
        });

        $( "#datePickerHygieneDate" ).datepicker({
            dateFormat:'dd/mm/yy'
        });
        $( "#datePickerBackupDate" ).datepicker({
            dateFormat:'dd/mm/yy'
        });
    });

});

