@if( $user->equal(Auth::user()) && $gamified )
<div class="container row">
  <div class="twelve columns">
    <hgroup class="profile_block_title">
      <h3><i class="points"></i>
        Minha Pontuação
      </h3>
    </hgroup>
    <div class="profile_box">
      @if ( !$userPoints && !$waitingPoints && !sizeof($refusedSuggestions))
        <p>Você ainda não possui nenhum ponto.</p>
      @else
        @if($userPoints)
          <p><strong>Pontuação atual:</strong> {{ $userPoints }}</p>
        @endif
        @if($userWaitingPoints)
          <p><strong>Pontuação a ser aprovada:</strong>  {{ $userWaitingPoints }}</p>
        @endif
        @if(sizeof($acceptedSuggestions))
          <p><strong>Número de sugestões aceitas:</strong>  {{ sizeof($acceptedSuggestions) }}
        @endif
        @if(sizeof($waitingSuggestions))
          <p><strong>Número de sugestões aguardando aprovação:</strong> {{ sizeof($waitingSuggestions) }}
          @if(sizeof($refusedSuggestions))
          @endif
          <p><strong>Número de sugestões recusadas:</strong> {{ sizeof($refusedSuggestions) }}
        @endif
      @endif
    </div>
  </div>
</div>
@endif