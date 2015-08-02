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




$(document).ready(function() {


//@if(isset($tagsArea))
        
       // @foreach ( $tagsArea as $tagArea )
         //   $('#tagsArea').textext()[0].tags().addTags([ {{ '"' . $tagArea . '"' }} ]);
       // @endforeach
//@endif
 //alert(tagsArea);

//tags
    $('#tags_input').textext({ plugins : 'autocomplete'})
    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'geral','getty', 'praça'  ],            
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


    $('#add_tag').click(function(e) {
                e.preventDefault();
                var tag = $('#tags_input').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsArea').textext()[0].tags().addTags([ tag ]);
                $('#tags_input').val('');
    });

//tags material
$('#tagsMaterial').textext({ plugins : 'autocomplete'}) //input
    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'tijolo','concreto', 'vidro'  ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
    });
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
$('#tagsElements').textext({ plugins : 'autocomplete'}) //input
    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'pilar','coluna', 'pilotis'  ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
    });
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
$('#tagsTypology').textext({ plugins : 'autocomplete'}) //input
    .bind('getSuggestions', function(e, data)
         {
            var list = [ 'igreja','catedral', 'praça'  ],            
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }            
            );                            
    });
    $('#tagsTypologyArea').textext({ plugins: 'tags' });
    


    $('#addTagTypology').click(function(e) {
                e.preventDefault();
                var tag = $('#tagsTypology').val();
               // alert(tag);
                if (tag == '') return;
                $('#tagsTypologyArea').textext()[0].tags().addTags([ tag ]);
                $('#tagsTypology').val('');
    });   
   


function retrieveTagsArea(arrayTag){
    alert("ok");
    for(i=0; i<arrayTag.length; i++){
        var tag = arrayTag[i];
        if(tag!= ""){
            $('#tagsArea').textext()[0].tags().addTags([ tag ]);
            
        }       
    }

    $('#tags_input').val('');  
    
}






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

