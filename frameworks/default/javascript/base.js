function jCONFIRM(message) {
  if (message) {
    var agree=confirm(message);
    if (agree)
      return true ;
    else
      return false ;
  } else {
    return true ;
  } // if
} // jCONFIRM

function jACTIONSUBMIT (action) {

  document.masslist.gAUTOSUBMITACTION.value = action;

  document.masslist.submit();
  
  return (true);

} // jACTIONSUBMIT

function jPOSTLINK(href, datalist, confirm) {

  if (!jCONFIRM (confirm)) return (false);

  formElement = document.createElement ('form');
  formElement.setAttribute ("method", "post");
  formElement.setAttribute ("action", href);
  formElement.setAttribute ("id", "hideurl");
  queryarray = datalist.split ("&");

  var count = 0;

  while (count<queryarray.length)
  {
    attrib = queryarray[count].split ("=");
    inputElement = document.createElement ('input');
    inputElement.setAttribute ("name", attrib[0]);
    inputElement.setAttribute ("value", attrib[1]);
    inputElement.setAttribute ("type", "hidden");
    formElement.appendChild (inputElement);
    count++;
  }

  document.body.appendChild (formElement);
  document.getElementById ("hideurl").submit();

} // jPOSTLINK

function jTOGGLEFORM (source) {

  var classlist = getElementsByClass (document, 'on', '*');

  for (c = 0; c < classlist.length; c++) {
    classlist[c].setAttribute('class', 'off');
  }

  tElement = document.getElementById (source);
  oElement = document.getElementById (source + "_top");

  tElement.setAttribute('class', 'on');
  oElement.setAttribute('class', 'on');

  document.getElementById('gSECTION').value = source.toUpperCase();

  return (true);
  
} // jTOGGLEFORM

function jPOPUP(pURL, pWIDTH, pHEIGHT) {

  day = new Date();
  id = day.getTime();

  var leftside = (screen.availWidth/2) - (pWIDTH/2);
  var topside = (screen.availHeight/2) - (pHEIGHT/2);

  var top = 'top = ' + topside + ", ";
  var left = 'left = ' + leftside + ", ";

  var width = 'width =' + pWIDTH + ", ";
  var height = 'height =' + pHEIGHT + ", ";

  var arguments = "titlebar = 0, status = 0, " + width + height + top + left + " resizable = 1, scrollbars = 1, directories = 0, menubar = 0, location = 0, toolbar = 0"

  window.open( pURL, "appleseed", arguments)

} // jPOPUP

function getElementsByClass(node,searchClass,tag) {
  var classElements = new Array();
  if ( node == null )
    node = document;
  if ( tag == null )
    tag = '*';
  var els = node.getElementsByTagName(tag);
  var elsLen = els.length;
  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
  for (i = 0, j = 0; i < elsLen; i++) {
    if ( pattern.test(els[i].className) ) {
        classElements[j] = els[i];
        j++;
    } 
  } 
  return classElements;
}
