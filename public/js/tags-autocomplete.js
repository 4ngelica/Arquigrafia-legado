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

    //author
     $('#workAuthor_area').textext({ plugins: 'tags' });

    $('#add_author').click(function(e) {
        e.preventDefault();
        authorsList();
    });

        $('#workAuthor').keypress(function(e) {
            var key = e.which || e.keyCode; 
            if(key ==13)
               authorsList();
            if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
                e.preventDefault();
        });


      function authorsList(){
           var author = $('#workAuthor').val();
           if (author == '') return;
        
            var sizeTags = $('#workAuthor_area').textext()[0].tags()._formData.length;
            if (sizeTags < 3) { 
                $('#workAuthor_area').textext()[0].tags().addTags([ author ]);
                $('#workAuthor').val('');
            }     
            else {
                $('#workAuthor').val('');
            }
      }

 
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

