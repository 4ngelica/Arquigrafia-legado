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
    /*$('#tags_input').textext({ plugins : 'autocomplete'})

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
    }); */

    $('#tags_input').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/tagList.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;




    $('#tagsArea').textext({ plugins: 'tags' });

    

    $('#add_tag').click(function(e) {
                e.preventDefault();
                var tag = $('#tags_input').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsArea').textext()[0].tags().addTags([ tag ]);
                $('#tags_input').val('');
    });


    $('#workAuthor').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/autor.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;


 /////
//tags material
/*
$('#tagsMaterial').textext({ plugins : 'autocomplete'}) //input
    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'tijolo','concreto', 'concreto armado', 'vidro'  ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
 }); */

    $('#tagsMaterial').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/tagListMaterial.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;

    $('#tagsMaterialArea').textext({ plugins: 'tags' });


    $('#addTagMaterial').click(function(e) {
                e.preventDefault();
                var tagMaterial = $('#tagsMaterial').val();
               // alert(tag);
                if (tagMaterial == '') return;
                $('#tagsMaterialArea').textext()[0].tags().addTags([ tagMaterial ]);
                $('#tagsMaterial').val('');
    });

 //tags elements

    $('#tagsElements').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/tagListElements.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;

    $('#tagsElementsArea').textext({ plugins: 'tags' });


    $('#addTagElements').click(function(e) {
                e.preventDefault();
                var tag = $('#tagsElements').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsElementsArea').textext()[0].tags().addTags([ tag ]);
                $('#tagsElements').val('');
    });    
    
//tags tipology

    $('#tagsTypology').textext({ plugins : 'autocomplete ajax',
            ajax : {
                url : '/js/tagListTypology.json',
                dataType : 'json',
                cacheResults : true
            }
        })
    ;

    $('#tagsTypologyArea').textext({ plugins: 'tags' });
    


    $('#addTagTypology').click(function(e) {
                e.preventDefault();
                var tag = $('#tagsTypology').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsTypologyArea').textext()[0].tags().addTags([ tag ]);
                $('#tagsTypology').val('');
    }); 

////

    $(function() {
        $( "#datePickerWorkDate" ).datepicker({
            dateFormat:'dd/mm/yy'
        });
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

