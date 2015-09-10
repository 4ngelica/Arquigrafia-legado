<!--   FAVICON   -->
<link rel="icon" href="{{ URL::to("/") }}/img/arquigrafia_icon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="{{ URL::to("/") }}/img/arquigrafia_icon.ico" type="image/x-icon" />
<!--   ESTILO GERAL   -->
{{-- <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.css" /> --}}
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
<!-- JQUERRY -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
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

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
{{-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> --}}
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

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