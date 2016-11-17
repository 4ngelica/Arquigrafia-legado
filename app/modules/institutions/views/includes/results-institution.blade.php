<div class="container">
<div id="user_header" class="twelve columns">
  <div class="div_avatar_size_inst" >
            <img class ="class_img_avatar" class="avatar" src="{{ asset($institution->photo) }}"
              class="user_photo_thumbnail"/>
  </div> 
  <div class="info">
    <h1>Imagens do {{$institution->name}}</h1>
    <p><a href="{{ URL::to('/institutions/'.$institution->id) }}">Perfil</a></p>
  </div>  
</div>  

      <div class="twelveMid columns">    
       <div id="add_images" class="" style="display: block;"> 
        <div id="add" class="twelveMid columns add" >
          <!--<img class="loader" src="{{ URL::to('/img/ajax-loader.gif') }}" /> style="height: 1500px;"-->
          @if ( $photos!= null)
              @if ($photos->count() > 0)
                
                 @include('includes.all_images')
                 
              @else
                <p>Não foi encontrada nenhuma imagem sua para sua busca.</p>
              @endif
           @else
               <div class="wrap">
               </div>
           @endif   
        </div>
        @if ( $photos!= null)
        <div class="eleven columns block add">
          <!-- {{-- $photos->appends(array('sort' => 'created_at'))->links()  --}} --> </p>
           <div class="eight columns alpha buttons">
            {{ $photos->links()}}
              <!-- <input type="button" class="btn less less-than" value="&lt;&lt;">
              <input type="button" class="btn less-than" value="&lt;">
              <p>{{--$page --}} / {{--$maxPage --}}</p>
              <input type="button" class="btn greater-than" value="&gt;">
              <input type="button" class="btn greater greater-than" value="&gt;&gt;">-->
            </div>          
        </div>
        @endif
        </div>
        
      </div>
</div>




