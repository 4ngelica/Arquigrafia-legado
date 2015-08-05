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
}


/**
 * Method to show the retrieved tags in the form 
 * @param {String} tagsJson 
 * @param {textarea} containerText
 * @param {input type} tagInput
 */
function showTags(tagsJson, containerText, tagInput){

//    console.log(tagsJson);

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



//tags
    $('#tags_input').textext({ plugins : 'autocomplete'})

    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'tijolo','bloque', 'madeira'  ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
    });
    $('#tagsArea').textext({ plugins: 'tags' });

    

    $('#add_tag').click(function(e) {
                e.preventDefault();
                var tag = $('#tags_input').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsArea').textext()[0].tags().addTags([ tag ]);
                $('#tags_input').val('');
    });


  
   //authors
  /* $('#workAuthor').textext({ plugins : 'autocomplete'}) //input
    .bind('getSuggestions', function(e, data)
         {
            var list = ['carlos Armando','ricardo','mary','osvaldo','tomas' ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
    }); */


    $('#workAuthor').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/autor.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;


  /* $('#workAuthorArea').textext({ plugins: 'tags' });    

    $('#addWorkAuthor').click(function(e) {
                e.preventDefault();
                var tag = $('#workAuthor').val();
               // alert(tag);
                if (tag == '') return;
                $('#workAuthorArea').textext()[0].tags().addTags([ tag ]);
                $('#workAuthor').val('');
    });   */



    $(function() {
        $( "#datePickerWorkDate" ).datepicker({
            dateFormat:'dd/mm/yy'
        }
        );
        $( "#datePickerImageDate" ).datepicker({
        dateFormat:'dd/mm/yy'
        }
        );
        });

});

