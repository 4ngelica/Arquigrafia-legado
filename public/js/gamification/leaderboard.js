$(document).ready(function() {
  $('.greater-than').click(function(e) {
    e.preventDefault();
    getPage(1);
    console.log('oi');
  });

  $('.less-than').click(function(e) {
    e.preventDefault();
    getPage(-1);
  });

});

function getPage(goto) {
  $('#leaderboard table').css({'opacity' : 50 });
}