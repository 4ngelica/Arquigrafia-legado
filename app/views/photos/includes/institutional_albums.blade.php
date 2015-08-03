<!doctype html>
<html>
<head>
  <meta charset="utf8">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/checkbox-edition.css" />






</head>
<body>

  <style>
    div.add_images {
      margin-top: 20px;
    }
  </style>





    
    <div class="twelve columns">
          <div id="add_images" >
          <div class="eleven columns select_options add">
              
                <div class="seven columns alpha omega">
                  <div class="four columns alpha omega block">
                    <p class="filter"></p>
                    <br>
                  </div>
                  <!--<div class="three columns alpha omega block">
                    <p class="selectedItems"></p>
                  </div>-->
                </div>
                
             
            </div>
            <div id="add" class="eleven columns add">
              
              <?php 
                //$photos = $other_photos; //if-$photos->count() > 0
                //$type = 'add';
              ?>
              @if ($albumsInstitutional->count() > 0) 

              <?php $count = 0; ?>
                  <table>
                    @foreach($albumsInstitutional as $albumInstitutional)
                      @if ($count % 2 == 0)
                       <tr>
                      @endif 
                      @if ($count == 0)
                        <td width="143" class="add" >    
                          <span> Adicionar Album</span>                  
                          <img src="{{ URL::to('/img/create_album.png') }}"> 
                        </td>
                      <?php $count++; ?>
                      @endif
                      <td width="143" class="add" >
                        <div class="photo add">
                         <!-- <input type="checkbox" class="ch_photo" id="{{'albums_'.$albumInstitutional->id }}" 
                          name="albums_add[]" value="{{ $albumInstitutional->id }}">-->

                          <input type="radio" class="ch_photo"  id="albums_institution" 
                          name="albums_institution"  value="{{ $albumInstitutional->id }}"
                           >

                          @if ($count % 2 < 1)
                            <?php $position = 'right'; ?>
                          @else
                            <?php $position = 'left'; ?>
                          @endif

                          @if(isset($albumInstitutional->cover_id))
                            <img src="{{ URL::to('/arquigrafia-images/' . $albumInstitutional->cover_id . '_home.jpg') }}" 
                            class="img_photo {{ $position }}" title="{{ $albumInstitutional->title }}"> 
                          @else
                            <div  style = "height:85px; width: 100%; background-color:#aaa; padding-top:4px">
                              <span>Álbum sem capa</span>
                            </div>
                          @endif
                        </div>  
                      </td>
                      @if (($count+1) % 2 == 0 && $count > 0)
                       </tr>
                      @endif
                    <?php $count++ ?>
                    @endforeach
                     @if($count % 2 != 0)
                     @while($count % 2 != 0)
                       <td></td>
                        <?php $count++; ?>
                      @endwhile
                      </tr>
                     @endif  
                  </table>
                 
              @else
              <table>
                <tr>
                  <td width="143" class="add" >    
                          <span> Adicionar Album</span>                  
                          <img src="{{ URL::to('/img/create_album.png') }}"> 
                  </td>
                </tr>
              </table>
              @endif
            </div>
            
        </div>
      
    
  </div>
  <div id="mask"></div>
  <div id="form_window" class="form window">
    <a class="close" href="#" title="FECHAR">Fechar</a>
    <div id="covers_registration"></div>
  </div>
  <div class="message_box"></div> 
  <script type="text/javascript">
   /* $(document).ready(function() {
      $('.tabs .tab-links a').on('click', function(e) {
        var currentAttrValue = $(this).attr('href');
        $('.tabs ' + currentAttrValue).fadeIn('slow').siblings().hide();
        $(this).parent('li').addClass('active').siblings().removeClass('active');
        e.preventDefault();
        if (update && $('.tabs ' + currentAttrValue).hasClass(update)) {
          updatePages(update);
        }
      });
    });*/
  </script>    
</body>
</html>