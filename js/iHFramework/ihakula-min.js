(function(a){var b=function(c,e){for(var d in e){c[d]=e[d]}return c};Function.prototype.method=function(d,c){this.prototype[d]=c;return this};a.$Object={clone:function(){var d={};b(d,this);return d}};a.$O=function(c){return b(a.$Object,c||{})};a.$Array={push:function(c){this[this.length]=c},pop:function(){--this.length},each:function(c){for(var d=0;d<this.length;d++){c(this[d])}},indexOf:function(d){for(var c=0;c<this.length;c++){if(this[c]===d){return c}}return -1},contains:function(c){return this.indexOf(c)>=0},remove:function(d){var c=this.indexOf(d);if(c>=0){return this.splic(c,1)[0]}},find:function(c){for(var d=0;d<this.length;d++){if(c(this[d])){return this[d]}}}};a.$A=function(c){if(c==null){c=[]}else{if(c instanceof Array){}else{c=[c]}}return b(c,a.$Array)};a.$Function={};a.$F=function(c){return b(c,a.$Function)};a.$Y=function(c){return function(){var d=new a.$AsyncCB(this,arguments);var e=new a.$MethodRV(d);e.whenDone(arguments[1]);c.apply(this,[d]);return e}};a.$AsyncCB=function(d,c){this._thisobj=d;this.args=c};a.$AsyncCB.method("done",function(c){if(this._ondone&&typeof(this._ondone)=="function"){this._ondone(c)}});a.$MethodRV=function(c){this._cb=c;c._ondone=this._ondone.bind(this)};a.$MethodRV.method("_trigger",function(c){this._rv=c;this._done=true}).method("whenDone",function(c){this._callbackFct=c}).method("_ondone",a.$F(function(c){this._trigger(c);this._onCallback()})).method("_onCallback",function(){if(this._done&&this._callbackFct&&typeof(this._callbackFct)=="function"){this._callbackFct(this._rv)}})})(window.ih=window.ih||{});(function(a){a.addEvent=function(d,c,b){if(d.addEventListener){d.addEventListener(c,b,false)}else{if(d.attachEvent){d["e"+c+b]=b;d[c+b]=function(){d["e"+c+b](window.event)};d.attachEvent("on"+c,d[c+b])}}};a.browserDetect=function(){if("\v"=="v"){return"IE"}else{if(/Mozilla/.test(navigator.userAgent)&&/Firefox/.test(navigator.userAgent)){return"MZ"}else{if(/Chrome/.test(navigator.userAgent)&&/Mozilla/.test(navigator.userAgent)){return"CH"}else{if(/KHTML/.test(navigator.userAgent)){return"SF"}}}}};a.loadScript=a.$Y(function(b){var f=b.args[0];var d=document.createElement("script");d.setAttribute("src",f);d.setAttribute("type","text/javascript");var c=function(g){d.removeEventListener("load",c,false);b.done({statu:"ok"})};var e=function(){var g=this.readyState;if(g=="loaded"||g=="interactive"||g=="complete"){d.onreadystatechange=null;b.done({statu:"ok"})}};a.addEvent(d,"load",c);a.addEvent(d,"onreadystatechange",e);document.getElementsByTagName("head")[0].appendChild(d)});a.removeChildren=function(d){if(d){var c=d.childNodes;for(var b=c.length-1;b>=0;b--){d.removeChild(c[b])}}};a.setcookie=function(d,f,b,j,e,h){var c=new Date();c.setTime(c.getTime());if(b){b=b*1000*60*60*24}var g=new Date(c.getTime()+(b));document.cookie=d+"="+escape(f)+((b)?";expires="+g.toGMTString():"")+((j)?";path="+j:"")+((e)?";domain="+e:"")+((h)?";secure":"")};a.getcookie=function(d){var e=document.cookie.indexOf(d+"=");var b=e+d.length+1;if((!e)&&(d!=document.cookie.substring(0,d.length))){return null}if(e==-1){return null}var c=document.cookie.indexOf(";",b);if(c==-1){c=document.cookie.length}return unescape(document.cookie.substring(b,c))};a.getDaysInMonth=function(c,b){var d;switch(c){case 1:case 3:case 5:case 7:case 8:case 10:case 12:d=31;break;case 4:case 6:case 9:case 11:d=30;break;case 2:if(((b%4)==0)&&((b%100)!=0)||((b%400)==0)){d=29}else{d=28}break}return(d)}})(window.ih=window.ih||{});(function(ns){var extend=function(targetObj,sourceObj){for(var f in sourceObj){targetObj[f]=sourceObj[f]}return targetObj};ns.IHInterface=function(name){try{if(arguments.length<2){throw"Interface exception : The arguments length is actually more than two, not "+arguments.length}this.name=name;this.methods=[];var len=arguments.length;for(var i=1;i<len;i++){var methodArray=arguments[i];if(!methodArray instanceof Array){throw"Interface methods : The methods group should be an intanceof Array, not "+typeof(methodArray)}else{var mlen=methodArray.length;for(var j=0;j<mlen;j++){var mname=methodArray[j];if(typeof(mname)!="string"){throw"Method name : The typeof method in KInterface should be string"}else{this.methods.push(mname)}}}}}catch(e){console.log(e)}};ns.ensureImplements=function(objClass,interfaces){try{if(arguments.length<2){throw"IHInterface implements : The arguments expected is actually more than two"}var len=interfaces.length,mlen=0;for(var i=0;i<len;i++){var interfaceObj=interfaces[i];if(interfaceObj.constructor!=ns.IHInterface){throw"Implements interface : "+interfaceObj+" is not an IHInterface"}mlen=interfaceObj.methods.length;for(var j=0;j<mlen;j++){var method=interfaceObj.methods[j];if(!objClass[method]||typeof(objClass[method])!="function"){throw"Interface method : "+method+" in "+objClass.__classname+" not implemented yet"}}}}catch(e){console.log(e)}};ns.cextends=function(subClass,superClass){var F=function(){};F.prototype=superClass.prototype;subClass.prototype=new F();subClass.prototype.constructor=subClass;subClass.prototype.superclass=superClass.prototype;if(superClass.prototype.constructor==Object.prototype.constructor){superClass.prototype.constructor=superClass}};ns.defineClass=function(name,superclass,interfaces,fct){try{if(typeof(name)!="string"){throw"Define class : Class name"+name+"should be typeof stirng, not "+typeof(name)}if(!(fct&&fct instanceof Function)){throw"Define class : the class"+name+"function is invalid"}else{var staticObj={};var proObj={};proObj.prototype={};fct(staticObj,proObj)}var nameObjArray=name.split(".");for(var n=0;n<nameObjArray.length-1;n++){var m=0;var nameString="";while(m<=n){m==n?nameString+=nameObjArray[m]:nameString+=nameObjArray[m]+".";m++}eval("window."+nameString+"=window."+nameString+"|| {};")}var c;if(superclass==null||superclass==""){window.__kinit=function(){if(this.init&&this.init instanceof Function){this.init.apply(this,arguments)}};c=eval(name+"="+window.__kinit);extend(c,staticObj);extend(c.prototype,proObj.prototype)}else{if(!(superclass&&superclass instanceof Function)){throw"Define class : Superclass "+superclass+" is invalid"}else{window.__kinit=function(){var t=this.init;this.init=this.superclass.init;this.superclass.constructor.apply(this,arguments);if(t&&t instanceof Function){this.init=t;this.init.apply(this,arguments)}};c=eval(name+"="+window.__kinit);ns.cextends(c,superclass);extend(c,staticObj);extend(c.prototype,proObj.prototype)}}delete window.__kinit;c.prototype.__classname=name;if(!(interfaces==null||interfaces=="")){var _tinstance=new c("__ti");ns.ensureImplements(_tinstance,interfaces);delete _tinstance}delete c}catch(e){console.log(e)}}})(window.ih=window.ih||{});ih.defineClass("ih.XML",null,null,function(a,b){a.arrMSXMLProgIDs=["MSXML4.DOMDocument","MSXML3.DOMDocument","MSXML2.DOMDocument","MSXML.DOMDocument","Microsoft.XmlDom"];a.strMSXMLProgID="";a.blnFailed=false;b.prototype.init=function(){this.initData()};b.prototype.initData=function(){var d=ih.browserDetect();if(d=="IE"){var g=false;for(var f=0;f<ih.XML.arrMSXMLProgIDs.length&&!g;f++){try{var c=ih.XML.arrMSXMLProgIDs[f];var e=new ActiveXObject(c);ih.XML.strMSXMLProgID=c;g=true}catch(h){}}if(!g){ih.blnFailed=true}}else{if(d=="MZ"){Document.prototype.__load__=Document.prototype.load;Document.prototype.load=ih.XML._Moz_Document_load;Document.prototype.loadXML=ih.XML._Moz_Document_loadXML;Document.prototype.parseError=0;Document.prototype.readystate=0;Document.prototype.onreadystatechange=null;Node.prototype.transformNode=ih.XML._Moz_node_transformNode;Node.prototype.transformNodeToObject=ih.XML._Moz_node_transformNodeToObject;Node.prototype.__defineGetter__("xml",ih.XML._Moz_Node_getXML)}else{if(d=="CH"||d=="SF"){this.load=ih.XML._Chrome_Document_load;Document.prototype.load=ih.XML._Chrome_Document_load;this.loadXML=ih.XML._Chrome_Document_loadXML;Document.prototype.loadXML=ih.XML._Chrome_Document_loadXML;Document.prototype.parseError=0;Document.prototype.readystate=0;Document.prototype.onreadystatechange=null}else{ih.blnFailed=true}}}};a._Chrome_Document_load=function(g){try{var d=new window.XMLHttpRequest();d.open("GET",g,false);d.send(null);var c=d.responseXML;if(this.onreadystatechange&&typeof this.onreadystatechange=="function"){c.onreadystatechange=this.onreadystatechange}c.parseError=0}catch(f){this.parseError=-1}a.updateReadyState(c,4)};a._Chrome_Document_loadXML=function(g){try{var d=new DOMParser();var c=d.parseFromString(g,"text/xml");if(this.onreadystatechange&&typeof this.onreadystatechange=="function"){c.onreadystatechange=this.onreadystatechange}c.parseError=0}catch(f){this.parseError=-1}a.fireOnLoad(c)};a._Moz_Node_getXML=function(){return(new XMLSerializer()).serializeToString(this)};a._Moz_Document_load=function(d){a.updateReadyState(this,1);try{this.__load__(d)}catch(c){this.parseError=-1}a.updateReadyState(this,4)};a._Moz_Document_loadXML=function(g){a.updateReadyState(this,1);var f=new DOMParser();var c=f.parseFromString(g,"text/xml");while(this.hasChildNodes()){this.removeChild(this.lastChild)}for(var e=0;e<c.childNodes.length;e++){var d=this.importNode(c.childNodes[e],true);this.appendChild(d)}a.fireOnLoad(this)};a.document_onload=function(){ih.XML.fireOnLoad(this)};a.fireOnLoad=function(c){if(!c.documentElement||c.documentElement.tagName=="parsererror"){c.parseError=-1}a.updateReadyState(c,4)};a.updateReadyState=function(c,d){c.readystate=d;if(c.onreadystatechange&&typeof(c.onreadystatechange)=="function"){c.onreadystatechange()}};a._Moz_node_transformNode=function(d){var e=new XSLTProcessor();var c=document.implementation.createDocument("","",null);e.transformDocument(this,d,c,null);return(new XMLSerializer()).serializeToString(c)};a._Moz_node_transformNodeToObject=function(c,e){var d=new XSLTProcessor();d.transformDocument(this,c,e,null);a.updateReadyState(e,4);return 1};b.prototype.createDOMDocument=function(){var d=null;var c=ih.browserDetect();if(c=="IE"){d=new ActiveXObject(ih.XML.strMSXMLProgID);d.preservWhiteSpace=true}else{if(c=="MZ"){d=document.implementation.createDocument("","",null);ih.addEvent(d,"load",ih.XML.document_onload)}else{if(c=="CH"||c=="SF"){d=this}}}return d}});ih.defineClass("ih.Scroll",null,null,function(a,b){b.prototype.steepLength=100;b.prototype.init=function(c){this.element=document.getElementById(c);this.scrollLeft=this.element.scrollLeft;this.scrollTop=this.element.scrollTop};b.prototype.toElement=function(c,d){this._time=d/this.steepLength;this.target=document.getElementById(c);this.offsetObj=ih.browserDetect()=="IE"?this.getOffset(this.target):{left:this.target.offsetLeft,top:this.target.offsetTop};this.distance=Math.sqrt(Math.pow(this.offsetObj.left-this.scrollLeft,2)+Math.pow(this.offsetObj.top-this.scrollTop,2));if(this.distance==0){return}else{this.xDistance=this.offsetObj.left-this.scrollLeft;this.yDistance=this.offsetObj.top-this.scrollTop;this.xAccelerate=(2*this.xDistance)/Math.pow(d/1000,2);this.yAccelerate=(2*this.yDistance)/Math.pow(d/1000,2);this._keepMoving()}};b.prototype.scrollTo=function(d,c){this.element.scrollLeft=d;this.element.scrollTop=c};b.prototype._keepMoving=function(){this.startTime=0;this._invokeFct=ih.$F(function c(){if(this instanceof ih.Scroll){this.startTime+=this._time;if(this.startTime==this._time*this.steepLength){this.scrollTo(this.offsetObj.left,this.offsetObj.top);this.scrollLeft=this.offsetObj.left;this.scrollTop=this.offsetObj.top;clearInterval(_interval)}else{var d=this.scrollLeft+0.5*this.xAccelerate*Math.pow(this.startTime/1000,2);var e=this.scrollTop+0.5*this.yAccelerate*Math.pow(this.startTime/1000,2);this.scrollTo(d,e)}}}).bind(this);_interval=setInterval(this._invokeFct,this._time)};b.prototype.getOffset=function(c){var e=c.offsetLeft;var d=c.offsetTop;while(c.offsetParent!=document.body){c=c.offsetParent;e+=c.offsetLeft;d+=c.offsetTop}e+=document.body.offsetLeft;d+=document.body.offsetTop;return{left:e,top:d}}});ih.defineClass("ih.PubSub",null,null,function(a,b){b.prototype.init=function(){this.msgBox=ih.$A()};b.prototype.subscribe=function(f,e,c){var d=this.msgBox[f];if(!d){this.msgBox[f]=[[e,c]]}else{d[d.length]=[e,c]}};b.prototype.unsubscribe=function(f,e){var d=this.msgBox[f];if(!d){return}else{for(var c=0;c<d.length;c++){if(d[c][0]==e){d.splice(c,1);return}}}};b.prototype.publish=function(f,c){var e=this.msgBox[f];if(!e){return}else{for(var d=0;d<e.length;d++){e[d][1].apply(e[d][0],[c])}}}});(function(a){a.HException=function(c,b){this.isHException=true;this.comment=c;this.message=b};a.HLog=function(c,b){this.isHLog=true;this.comment=c;this.message=b};a.defineClass("ih.Message",null,null,function(b,c){c.prototype.init=function(d){this.name=d;this.on=false;this.msgArray=new a.$A();this.windowInstance=null};c.prototype.push=function(d){this.msgArray.push(d)};c.prototype.pop=function(){this.msgArray.pop()};c.prototype.close=function(){if(!this.on){return}if(!this.windowInstance.closed){this.windowInstance.close()}this.windowInstance=null};c.prototype.open=function(){if(this.windowInstance&&!this.windowInstance.closed){this.close()}this.windowInstance=window.open("about:blank","default","height=400, width=600, resizable=yes, scrollbars=yes, top=100, left=200");this.windowInstance.document.write("<html><head><title>"+this.name+'</title><style type="text/css">'+this.style+'</style></head><body><div id="content"></body></html>');this.windowInstance.document.close();this.windowInstance.focus()};c.prototype.setMsgStyle=function(d,g){var e=d||"#8A2BE2";var f=g||"#000000";this.style="p {color:"+e+";}span {color:"+f+";}"};c.prototype.showMessage=function(){if(this.windowInstance&&!this.windowInstance.closed){this.close()}this.open();var d=this.windowInstance;this.msgArray.each(a.$F(function(f){var e=d.document.getElementById("content");var g=d.document.createElement("p");g.appendChild(d.document.createTextNode(f.comment+" : "));var h=d.document.createElement("span");h.appendChild(d.document.createTextNode(f.message));g.appendChild(h);e.appendChild(g)}).bind(d))}})})(window.ih=window.ih||{});ih.defineClass("ih.KeyMap",null,null,function(c,d){var a={8:"backspace",9:"tab",13:"return",19:"pause",27:"escape",32:"space",33:"pageup",34:"pagedown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",44:"printscreen",45:"insert",46:"delete",112:"f1",113:"f2",114:"f3",115:"f4",116:"f5",117:"f6",118:"f7",119:"f8",120:"f9",121:"f10",122:"f11",123:"f12",144:"numlock",145:"scrolllock"};var b={48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",59:";",61:"=",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z",107:"+",109:"-",110:".",188:",",190:".",191:"/",192:"'",219:"[",220:"\\",221:"]",222:'"'};d.prototype.init=function(e){this.map={"default":function(){}};if(e){for(name in e){this.map[name.toLowerCase()]=e[name]}}};d.prototype.install=function(f){var e=this;function g(h){return e.dispatch(h)}ih.addEvent(f,"keydown",g);ih.addEvent(f,"keypress",g)};d.prototype.dispatch=function(k){var n=k||window.event;var g="";var m=null;if(n.type=="keydown"){var j=n.keyCode;if(j==16||j==17||j==18){return}m=a[j];if(!m&&(n.altKey||n.ctrlKey)){m=b[j]}if(m){if(n.ctrlKey){g+="ctrl_"}if(n.altKey){g+="alt_"}if(n.shiftKey){g+="shift_"}}else{return}}else{if(n.type=="keypress"){if(n.altKey||n.ctrlKey){return}if(n.charCode!=undefined&&n.charCode==0){return}var j=n.charCode||n.keyCode;m=String.fromCharCode(j);var h=m.toLowerCase();if(m!=h){m=h;modifies="shift_"}}}var f=this.map[g+m];if(!f){f=this.map["default"]}else{var l=n.target;if(!l){l=n.srcElement}f(l,g+m,n);return false}}});ih.defineClass("ih.User",null,null,function(b,a){a.prototype.init=function(){this.id=ih.getcookie("ihengine-utilities-session-sid");this.name=ih.getcookie("ihengine-utilities-session-uname")};a.prototype.isLogin=function(){if(this.id){return true}return false};a.prototype.setUserInfo=function(c){this.id=c.id;this.name=c.email;ih.setcookie("ihengine-utilities-session-sid",this.id);ih.setcookie("ihengine-utilities-session-uname",this.name)};a.prototype.logout=function(){this.id=null;this.name=null;ih.setcookie("ihengine-utilities-session-sid","",-1);ih.setcookie("ihengine-utilities-session-uname","",-1)}});ih.defineClass("ih.Service",null,null,function(SERVICE,service){service.prototype.init=function(){this._jsxescapemap={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"}};service.prototype.getRequest=function(){var IE="\v"=="v";return IE?new ActiveXObject("Msxml2.XMLHTTP"):new XMLHttpRequest()};service.prototype.getSessionCookie=function(){if(document.cookie.search(/ihengine-utilities-session-sid=([^;]*)/)!=-1){var c=RegExp.$1;return(c!=null&&c!="")?c:null}};service.prototype.strEscapeJSON=function(str,em){if(!em){em=this._jsxescapemap}if(/["\\\x00-\x1f]/.test(str)){return'"'+str.replace(/[\x00-\x1f\\"]/g,function(a){var c=em[a];if(c){return c}c=a.charCodeAt();return"\\u00"+Math.floor(c/16).toString(16)+(c%16).toString(16)})+'"'}return'"'+str+'"'};service.prototype._evalWithCatch=function(s,r){try{var o=window.eval("("+s+")");if(typeof(o.status)=="undefined"){o.status="ok"}}catch(e){var o={status:"-1",error:{detailed_message:"Client-side error\n\n"+e+"\n\n"+this.strEscapeJSON(s)+"\n\n"+r.status+" "+r.statusText,code:"-1"}}}return o};service.prototype._sendReceive=ih.$F(function(r,fnCallback,strContent,bNoEval){var self=this;if(fnCallback){r.onreadystatechange=function(){if(r.readyState==4){if(r.status==403||r.status==404||r.status==500){var objR=self._evalWithCatch(r.responseText,r);objR.httpstatus=r.status;fnCallback(objR)}else{fnCallback(bNoEval?r.responseText:self._evalWithCatch(r.responseText,r))}}};r.send(strContent||"")}else{r.send(strContent||"");if(r.status==403||r.status==404||r.status==500){var objR=self._evalWithCatch(r.responseText,r);objR.httpstatus=r.status;return objR}else{return bNoEval?r.responseText:self._evalWithCatch(r.responseText,r)}}});service.prototype.callService=function(objParam,fnCallback,strURI,strMethod,bNoEval){var bPost=strMethod.toUpperCase()=="POST";var arrParam=new Array();if(bPost){for(var p in objParam){arrParam.push(p+"="+window.encodeURIComponent(objParam[p]))}var c=this.getSessionCookie();if(c){arrParam.push("cookie="+window.encodeURIComponent(c))}var strData=arrParam.join("&")}else{var strDelim=strURI.indexOf("?")>-1?"&":"?";for(var p in objParam){arrParam.push(p+"="+window.encodeURIComponent(objParam[p]))}strURI+=strDelim+arrParam.join("&");var strData}var objRequest=this.getRequest();objRequest.open(strMethod,strURI,fnCallback!=null);if(bPost){objRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded")}this._sendReceive.bind(this);return this._sendReceive(objRequest,fnCallback,strData,bNoEval)}});ih.defineClass("ih.Gantt",null,null,function(b,a){a.prototype.init=function(c){this._taskList=[];this._ganttDiv=c;this._firstRowStr="<table border=1 style='border-collapse:collapse'><tr><td rowspan='2' width='300px' style='width:300px;'><div class='GTaskTitle' style='width:300px;'>Task</div></td>";this._thirdRow="";this._gStr="";this._maxDate=new Date();this._minDate=new Date();this._dTemp=new Date();this._colSpan=0;this.counter=0};a.prototype.addTaskDetail=function(c){this._taskList.push(c)};a.prototype.task=function(l,m,f,g,d){var k=new Date();var j=new Date();var n="<b>"+f+"</b>";var h="<b>"+g+"</b>";var e=d;var c=l.split("-");k.setFullYear(parseInt(c[0],10),parseInt(c[1],10)-1,parseInt(c[2],10));c=m.split("-");j.setFullYear(parseInt(c[0],10),parseInt(c[1],10)-1,parseInt(c[2],10));this.getFrom=function(){return k};this.getTo=function(){return j};this.getTask=function(){return n};this.getResource=function(){return h};this.getProgress=function(){return e}};a.prototype.getProgressDiv=function(c){return"<div class='GProgress' style='width:"+c+"%; overflow:hidden'></div>"};a.prototype.setMinMaxDate=function(){this._maxDate.setFullYear(this._taskList[0].getTo().getFullYear(),this._taskList[0].getTo().getMonth(),this._taskList[0].getTo().getDate());this._minDate.setFullYear(this._taskList[0].getFrom().getFullYear(),this._taskList[0].getFrom().getMonth(),this._taskList[0].getFrom().getDate());for(i=0;i<this._taskList.length;i++){if(Date.parse(this._taskList[i].getFrom())<Date.parse(this._minDate)){this._minDate.setFullYear(this._taskList[i].getFrom().getFullYear(),this._taskList[i].getFrom().getMonth(),this._taskList[i].getFrom().getDate())}if(Date.parse(this._taskList[i].getTo())>Date.parse(this._maxDate)){this._maxDate.setFullYear(this._taskList[i].getTo().getFullYear(),this._taskList[i].getTo().getMonth(),this._taskList[i].getTo().getDate())}}if(this._maxDate.getMonth()==11){if(this._maxDate.getDay()+5>ih.getDaysInMonth(this._maxDate.getMonth()+1,this._maxDate.getFullYear())){this._maxDate.setFullYear(this._maxDate.getFullYear()+1,1,5)}else{this._maxDate.setFullYear(this._maxDate.getFullYear(),this._maxDate.getMonth(),this._maxDate.getDate()+5)}}else{if(this._maxDate.getDay()+5>ih.getDaysInMonth(this._maxDate.getMonth()+1,this._maxDate.getFullYear())){this._maxDate.setFullYear(this._maxDate.getFullYear(),this._maxDate.getMonth()+1,5)}else{this._maxDate.setFullYear(this._maxDate.getFullYear(),this._maxDate.getMonth(),this._maxDate.getDate()+5)}}};a.prototype.drawThirdRow=function(){var d=new Date();d.setFullYear(d.getFullYear(),d.getMonth(),d.getDate());d.setHours(0);d.setMinutes(0);d.setSeconds(0);var c=new Date();c.setFullYear(this._dTemp.getFullYear(),this._dTemp.getMonth(),this._dTemp.getDate());c.setHours(0);c.setMinutes(0);c.setSeconds(0);if(c.getDay()%6==0){this._gStr+="<td class='GWeekend'><div style='width:24px;'>"+c.getDate()+"</div></td>";if(Date.parse(c)==Date.parse(d)){this._thirdRow+="<td id='GC_"+(this.counter++)+"' class='GToday' style='height:"+(this._taskList.length*21)+"px'>&nbsp;</td>"}else{this._thirdRow+="<td id='GC_"+(this.counter++)+"' class='GWeekend' style='height:"+(this._taskList.length*21)+"px'>&nbsp;</td>"}}else{this._gStr+="<td class='GDay'><div style='width:24px;'>"+c.getDate()+"</div></td>";if(Date.parse(c)==Date.parse(d)){this._thirdRow+="<td id='GC_"+(this.counter++)+"' class='GToday' style='height:"+(this._taskList.length*21)+"px'>&nbsp;</td>"}else{this._thirdRow+="<td id='GC_"+(this.counter++)+"' class='GDay'>&nbsp;</td>"}}};a.prototype.drawFirstRow=function(){if(this._dTemp.getDate()<ih.getDaysInMonth(this._dTemp.getMonth()+1,this._dTemp.getFullYear())){if(Date.parse(this._dTemp)==Date.parse(this._maxDate)){this._firstRowStr+="<td class='GMonth' colspan='"+(this._colSpan+1)+"'>T"+(this._dTemp.getMonth()+1)+"/"+this._dTemp.getFullYear()+"</td>"}this._dTemp.setDate(this._dTemp.getDate()+1);this._colSpan++}else{this._firstRowStr+="<td class='GMonth' colspan='"+(this._colSpan+1)+"'>T"+(this._dTemp.getMonth()+1)+"/"+this._dTemp.getFullYear()+"</td>";this._colSpan=0;if(this._dTemp.getMonth()==11){this._dTemp.setFullYear(this._dTemp.getFullYear()+1,0,1)}else{this._dTemp.setFullYear(this._dTemp.getFullYear(),this._dTemp.getMonth()+1,1)}}};a.prototype.drawTasks=function(){var d=0;var c=0;for(i=0;i<this._taskList.length;i++){d=(Date.parse(this._taskList[i].getFrom())-Date.parse(this._minDate))/(24*60*60*1000);c=(Date.parse(this._taskList[i].getTo())-Date.parse(this._taskList[i].getFrom()))/(24*60*60*1000)+1;this._gStr+="<div style='position:absolute; top:"+(20*(i+2)+8)+"px; left:"+(d*25+302)+"px; width:"+(25*c+100)+"px'><div title='"+this._taskList[i].getTask()+"' class='GTask' style='float:left; width:"+(25*c-1)+"px;'>"+this.getProgressDiv(this._taskList[i].getProgress())+"</div><div style='float:left; padding-left:3px'>"+this._taskList[i].getResource()+"</div></div>";this._gStr+="<div style='position:absolute; top:"+(20*(i+2)+1)+"px; left:5px'>"+this._taskList[i].getTask()+"</div>"}};a.prototype.draw=function(){if(this._taskList.length>0){this.setMinMaxDate();this._gStr="";this._gStr+="</tr><tr>";this._thirdRow="<tr><td>&nbsp;</td>";this._dTemp.setFullYear(this._minDate.getFullYear(),this._minDate.getMonth(),this._minDate.getDate());while(Date.parse(this._dTemp)<=Date.parse(this._maxDate)){this.drawThirdRow();this.drawFirstRow()}this._thirdRow+="</tr>";this._gStr+="</tr>"+this._thirdRow;this._gStr+="</table>";this._gStr=this._firstRowStr+this._gStr;this.drawTasks();this._ganttDiv.innerHTML=this._gStr}}});ih.defineClass("ih.AMPPlugin",null,null,function(PLUGIN,plugin){plugin.prototype.id=null;plugin.prototype.confXmlPath="";plugin.prototype.parentId=null;plugin.prototype.lazyLoad=true;plugin.prototype.scriptsLoaded=false;plugin.prototype.init=function(pluginPath){this.childs=new ih.$A();this.scripts=new ih.$A();this.confXmlPath=pluginPath+"plugin.xml";this.initFromConfXMLFile()};plugin.prototype.initFromConfXMLFile=function(){var me=this;var xml=new ih.XML();var objXML=xml.createDOMDocument();objXML.onreadystatechange=function(){if(this.readystate==4){var scriptNodes=this.documentElement.getElementsByTagName("script");for(var i=0;i<scriptNodes.length;i++){me.scripts.push(scriptNodes[i].getAttribute("path"))}var infoNode=this.documentElement.getElementsByTagName("info")[0];me.id=infoNode.getAttribute("id");me.parentId=infoNode.getAttribute("parentId");if(infoNode.getAttribute("lazyLoad")=="false"){me.lazyLoad=false;me.loadScripts()}var childNodes=this.documentElement.getElementsByTagName("child");for(var i=0;i<childNodes.length;i++){var aampPlugin=new ih.AMPPlugin(childNodes[i].firstChild.nodeValue);me.childs.push(aampPlugin)}}};objXML.load(this.confXmlPath)};plugin.prototype.loadScripts=function(){if(this.scriptsLoaded){return}var me=this;var j=0;var tempF=function(scriptPath){ih.loadScript(scriptPath,ih.$F(function(rv){if(rv.statu="ok"){j++;if(j==this.scripts.length){this.scriptsLoaded=true;var pluginClassName=this.id+"Plugin";this.pluginAnchor=eval(pluginClassName+"= new "+pluginClassName+"();");this.pluginAnchor.plugin=this;if(typeof(this.pluginAnchor.scriptsLoaded)=="function"){this.pluginAnchor.scriptsLoaded()}}}}).bind(me))};this.scripts.each(tempF)};plugin.prototype.findChildPluginById=function(id){var me=this;for(var i=0;i<this.childs.length;i++){var plg=this.childs[i];if(plg.id==id){return plg}if(plg.childs.length){return plg.findChildPluginById(id)}}}});ih.defineClass("ih.AMPEngine",null,null,function(a,b){b.prototype.name="ihakula.ampEngine";b.prototype.version="1.0";b.prototype.root=null;b.prototype.init=function(c){ih.plugins={};this.initFromConfXMLFile(c);this.pubsub=new ih.PubSub()};b.prototype.initFromConfXMLFile=function(d){var e=this;var c=new ih.XML();var f=c.createDOMDocument();f.onreadystatechange=function(){if(this.readystate==4){var h=this.documentElement.getElementsByTagName("plugin");var g=h[0];e.root=new ih.AMPPlugin(g.getAttribute("path"))}};f.load(d)}});