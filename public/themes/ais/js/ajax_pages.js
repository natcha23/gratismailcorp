$(function(){
  // var History = window.History;
  // if (History.enabled) {
  //     State = History.getState();
  //     History.pushState({urlPath: window.location.pathname}, $("title").text(), State.urlPath);
  // } else {
  //     return false;
  // }
  console.log(History.getState());

  var loadAjaxContent = function(target, urlBase) {
    $(target).load(urlBase);
  };

  var updateContent = function(State) {
    loadAjaxContent('#content article', State.hashedUrl);
  };

  History.Adapter.bind(window, 'statechange', function() {
      updateContent(History.getState());
  });

  $('body').on('click', 'a.ajax', function(e) {
    e.preventDefault();
    var href = $(this).attr('href');
    var title = $(this).text();
    History.pushState({urlPath: href}, title, href);
  });
});