var MsgHandler = ( function(){
  var addMsg = function(msg) {
    $('#msg_box span').text(msg);
  };
  var showMsg = function(){
    if ( $('#msg_box span').length > 0 ) {
      $('#msg_box').stop().animate({opacity:1},300).animate({opacity:0},5000);
    }
  };

  return {
    addMsg: function(msg) {
      addMsg(msg);
    },
    showMsg: function() {
      showMsg();
    }
  };
})();
