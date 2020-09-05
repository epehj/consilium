$(function(){
  // confirm action
  $('a.confirm').click(function(e){
    var r = confirm($(this).data('confirm-message'));
    if(r !== true){
      e.preventDefault();
    }
  });
});
