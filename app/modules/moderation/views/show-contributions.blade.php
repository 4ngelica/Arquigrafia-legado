@extends ('layouts.default')

@section ('head')
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/tabs.css') }}">
  <!-- LOADING VUE.JS BUNDLE -->
  <script src="/js/dist/contributions.bundle.js"></script>
@stop

@section ('content')
  <div class="container">
    <div id="contributions-content">
      <!-- HERE, VUE.JS WILL RENDER CONTENT - CONTRIBUTIONS -->
    </div>
  </div>
@stop
