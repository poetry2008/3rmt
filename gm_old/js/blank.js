window.onload = function() { //Open blank window
  var links = document.getElementsByTagName('a');
  for (var i=0;i < links.length;i++) {
    if (links[i].className == 'blank') {
      links[i].onclick = function() {
        window.open(this.href,'','');
        return false;
      };
    }
  }
};
