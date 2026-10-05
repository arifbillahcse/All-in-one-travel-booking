/* Latest three articles on the home page. */
(function () {
  "use strict";
  var box = document.querySelector("#home-blog");
  if (!box || !window.TO || !window.TravelBlog) return;
  function render() {
    box.innerHTML = window.TravelBlog.list().slice(0, 3).map(function (p) { return window.TravelBlog.card(p); }).join("");
  }
  render();
  document.addEventListener("travelorio:langchange", render);
})();
