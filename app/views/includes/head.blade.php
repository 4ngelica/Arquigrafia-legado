<!--   FAVICON   -->
<link rel="icon" href="{{ URL::to("/") }}/img/arquigrafia_icon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="{{ URL::to("/") }}/img/arquigrafia_icon.ico" type="image/x-icon" />
<!--   ESTILO GERAL   -->
{{-- <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.css" /> --}}
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
<!-- JQUERRY -->
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
<!-- JQUERY-UI -->
<link rel="stylesheet" href="{{ URL::to("/") }}/css/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
<!-- FANCYBOX -->
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<!--NOTIFICAÇÕES-->
<script type="text/javascript" src="{{ URL::to("/") }}/js/notifications.js"></script>

<!-- AUTOCOMPLETE -->
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.core.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.tags.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/styletags.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.core.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.tags.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.suggestions.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.filter.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.ajax.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/search-autocomplete.js" charset="utf-8"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

<link rel="stylesheet" type="text/css" href="{{ URL::to('/css/tabs.css') }}">
{{-- tabs para albums --}}

<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/ie7.css" />
<![endif]-->
<link rel="stylesheet" type="text/css" media="print" href="{{ URL::to("/") }}/css/print.css" />

<!--[if lt IE 9]>
<script src="{{ URL::to("/") }}/js/html5shiv.js"></script>
<![endif]-->

<!-- FACEBOOK -->
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '344371539091709',
      xfbml      : true,
      version    : 'v2.2'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
