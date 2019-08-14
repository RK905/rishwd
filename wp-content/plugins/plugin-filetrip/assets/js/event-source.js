!function(s){"use strict";var o=s.setTimeout,u=s.clearTimeout,t=function(){};function h(t,e,i,s,h){this._internal=new n(t,e,i,s,h)}function n(t,e,i,s,h){this.onStartCallback=e,this.onProgressCallback=i,this.onFinishCallback=s,this.thisArg=h,this.xhr=t,this.state=0,this.charOffset=0,this.offset=0,this.url="",this.withCredentials=!1,this.timeout=0}function e(){this._data={}}function i(){this._listeners=new e}function r(t){o(function(){throw t},0)}function a(t){this.type=t,this.target=void 0}function l(t,e){a.call(this,t),this.data=e.data,this.lastEventId=e.lastEventId}h.prototype.open=function(t,e){this._internal.open(t,e)},h.prototype.cancel=function(){this._internal.cancel()},n.prototype.onStart=function(){if(1===this.state){this.state=2;var e=0,i="",s=void 0;if("contentType"in this.xhr)e=200,i="OK",s=this.xhr.contentType;else try{e=this.xhr.status,i=this.xhr.statusText,s=this.xhr.getResponseHeader("Content-Type")}catch(t){i="",s=void(e=0)}null==s&&(s=""),this.onStartCallback.call(this.thisArg,e,i,s)}},n.prototype.onProgress=function(){if(this.onStart(),2===this.state||3===this.state){this.state=3;var t="";try{t=this.xhr.responseText}catch(t){}for(var e=this.charOffset,i=t.length,s=this.offset;s<i;s+=1){var h=t.charCodeAt(s);h!=="\n".charCodeAt(0)&&h!=="\r".charCodeAt(0)||(this.charOffset=s+1)}this.offset=i;var n=t.slice(e,this.charOffset);this.onProgressCallback.call(this.thisArg,n)}},n.prototype.onFinish=function(){this.onProgress(),3===this.state&&(this.state=4,0!==this.timeout&&(u(this.timeout),this.timeout=0),this.onFinishCallback.call(this.thisArg))},n.prototype.onReadyStateChange=function(){null!=this.xhr&&(4===this.xhr.readyState?(this.xhr.status,this.onFinish()):3===this.xhr.readyState?this.onProgress():this.xhr.readyState)},n.prototype.onTimeout2=function(){this.timeout=0;var t=/^data\:([^,]*?)(base64)?,([\S]*)$/.exec(this.url),e=t[1],i="base64"===t[2]?s.atob(t[3]):decodeURIComponent(t[3]);1===this.state&&(this.state=2,this.onStartCallback.call(this.thisArg,200,"OK",e)),2!==this.state&&3!==this.state||(this.state=3,this.onProgressCallback.call(this.thisArg,i)),3===this.state&&(this.state=4,this.onFinishCallback.call(this.thisArg))},n.prototype.onTimeout1=function(){this.timeout=0,this.open(this.url,this.withCredentials)},n.prototype.onTimeout0=function(){var t=this;this.timeout=o(function(){t.onTimeout0()},500),3===this.xhr.readyState&&this.onProgress()},n.prototype.handleEvent=function(t){"load"===t.type?this.onFinish():"error"===t.type?this.onFinish():"abort"===t.type?this.onFinish():"progress"===t.type?this.onProgress():"readystatechange"===t.type&&this.onReadyStateChange()},n.prototype.open=function(t,e){0!==this.timeout&&(u(this.timeout),this.timeout=0),this.url=t,this.withCredentials=e,this.state=1,this.charOffset=0,this.offset=0;var i=this;if(null==/^data\:([^,]*?)(?:;base64)?,[\S]*$/.exec(t))if((!("ontimeout"in this.xhr)||"sendAsBinary"in this.xhr||"mozAnon"in this.xhr)&&null!=s.document&&null!=s.document.readyState&&"complete"!==s.document.readyState)this.timeout=o(function(){i.onTimeout1()},4);else{this.xhr.onload=function(t){i.handleEvent({type:"load"})},this.xhr.onerror=function(){i.handleEvent({type:"error"})},this.xhr.onabort=function(){i.handleEvent({type:"abort"})},this.xhr.onprogress=function(){i.handleEvent({type:"progress"})},this.xhr.onreadystatechange=function(){i.handleEvent({type:"readystatechange"})},this.xhr.open("GET",t,!0),this.xhr.withCredentials=e,this.xhr.responseType="text","setRequestHeader"in this.xhr&&this.xhr.setRequestHeader("Accept","text/event-stream");try{this.xhr.send(void 0)}catch(t){throw t}"readyState"in this.xhr&&null!=s.opera&&(this.timeout=o(function(){i.onTimeout0()},0))}else this.timeout=o(function(){i.onTimeout2()},0)},n.prototype.cancel=function(){0!==this.state&&4!==this.state&&(this.state=4,this.xhr.onload=t,this.xhr.onerror=t,this.xhr.onabort=t,this.xhr.onprogress=t,this.xhr.onreadystatechange=t,this.xhr.abort(),0!==this.timeout&&(u(this.timeout),this.timeout=0),this.onFinishCallback.call(this.thisArg)),this.state=0},e.prototype.get=function(t){return this._data[t+"~"]},e.prototype.set=function(t,e){this._data[t+"~"]=e},e.prototype.delete=function(t){delete this._data[t+"~"]},i.prototype.dispatchEvent=function(t){t.target=this;var e=t.type.toString(),i=this._listeners.get(e);if(null!=i)for(var s=i.length,h=void 0,n=0;n<s;n+=1){h=i[n];try{"function"==typeof h.handleEvent?h.handleEvent(t):h.call(this,t)}catch(t){r(t)}}},i.prototype.addEventListener=function(t,e){t=t.toString();var i=this._listeners,s=i.get(t);null==s&&(s=[],i.set(t,s));for(var h=s.length;0<=h;h-=1)if(s[h]===e)return;s.push(e)},i.prototype.removeEventListener=function(t,e){t=t.toString();var i=this._listeners,s=i.get(t);if(null!=s){for(var h=s.length,n=[],r=0;r<h;r+=1)s[r]!==e&&n.push(s[r]);0===n.length?i.delete(t):i.set(t,n)}},l.prototype=a.prototype;var c=s.XMLHttpRequest,f=s.XDomainRequest,d=null!=c&&null!=(new c).withCredentials,p=d||null!=c&&null==f?c:f,y=-1,v=0,m=4,S=/^text\/event\-stream;?(\s*charset\=utf\-8)?$/i,g=18e6,C=function(t,e){var i=t;return i!=i&&(i=e),i<1e3?1e3:g<i?g:i},x=function(t,e,i){try{"function"==typeof e&&e.call(t,i)}catch(t){r(t)}};function E(t,e){i.call(this),this.onopen=void 0,this.onmessage=void 0,this.onerror=void 0,this.url="",this.readyState=v,this.withCredentials=!1,this._internal=new T(this,t,e)}function T(t,e,i){this.url=e.toString(),this.readyState=v,this.withCredentials=d&&null!=i&&Boolean(i.withCredentials),this.es=t,this.initialRetry=C(1e3,0),this.heartbeatTimeout=C(45e3,0),this.lastEventId="",this.retry=this.initialRetry,this.wasActivity=!1;var s=new(null!=i&&null!=i.Transport?i.Transport:p);this.transport=new h(s,this.onStart,this.onProgress,this.onFinish,this),this.timeout=0,this.currentState=y,this.dataBuffer=[],this.lastEventIdBuffer="",this.eventTypeBuffer="",this.state=m,this.fieldStart=0,this.valueStart=0,this.es.url=this.url,this.es.readyState=this.readyState,this.es.withCredentials=this.withCredentials,this.onTimeout()}function w(){this.CONNECTING=v,this.OPEN=1,this.CLOSED=2}T.prototype.onStart=function(t,e,i){if(this.currentState===v)if(null==i&&(i=""),200===t&&S.test(i)){this.currentState=1,this.wasActivity=!0,this.retry=this.initialRetry,this.readyState=1,this.es.readyState=1;var s=new a("open");this.es.dispatchEvent(s),x(this.es,this.es.onopen,s)}else if(0!==t){var h="";h=200!==t?"EventSource's response has a status "+t+" "+e.replace(/\s+/g," ")+" that is not 200. Aborting the connection.":"EventSource's response has a Content-Type specifying an unsupported type: "+i.replace(/\s+/g," ")+". Aborting the connection.",r(new Error(h)),this.close();s=new a("error");this.es.dispatchEvent(s),x(this.es,this.es.onerror,s)}},T.prototype.onProgress=function(t){if(1===this.currentState){var e=t.length;0!==e&&(this.wasActivity=!0);for(var i=0;i<e;i+=1){var s=t.charCodeAt(i);if(3===this.state&&s==="\n".charCodeAt(0))this.state=m;else if(3===this.state&&(this.state=m),s==="\r".charCodeAt(0)||s==="\n".charCodeAt(0)){if(this.state!==m){5===this.state&&(this.valueStart=i+1);var h=t.slice(this.fieldStart,this.valueStart-1),n=t.slice(this.valueStart+(this.valueStart<i&&t.charCodeAt(this.valueStart)===" ".charCodeAt(0)?1:0),i);if("data"===h)this.dataBuffer.push(n);else if("id"===h)this.lastEventIdBuffer=n;else if("event"===h)this.eventTypeBuffer=n;else if("retry"===h)this.initialRetry=C(Number(n),this.initialRetry),this.retry=this.initialRetry;else if("heartbeatTimeout"===h&&(this.heartbeatTimeout=C(Number(n),this.heartbeatTimeout),0!==this.timeout)){u(this.timeout);var r=this;this.timeout=o(function(){r.onTimeout()},this.heartbeatTimeout)}}if(this.state===m){if(0!==this.dataBuffer.length){this.lastEventId=this.lastEventIdBuffer,""===this.eventTypeBuffer&&(this.eventTypeBuffer="message");var a=new l(this.eventTypeBuffer,{data:this.dataBuffer.join("\n"),lastEventId:this.lastEventIdBuffer});if(this.es.dispatchEvent(a),"message"===this.eventTypeBuffer&&x(this.es,this.es.onmessage,a),2===this.currentState)return}this.dataBuffer.length=0,this.eventTypeBuffer=""}this.state=s==="\r".charCodeAt(0)?3:m}else this.state===m&&(this.fieldStart=i,this.state=5),5===this.state?s===":".charCodeAt(0)&&(this.valueStart=i+1,this.state=6):6===this.state&&(this.state=7)}}},T.prototype.onFinish=function(){if(1===this.currentState||this.currentState===v){this.currentState=y,0!==this.timeout&&(u(this.timeout),this.timeout=0),this.retry>16*this.initialRetry&&(this.retry=16*this.initialRetry),this.retry>g&&(this.retry=g);var t=this;this.timeout=o(function(){t.onTimeout()},this.retry),this.retry=2*this.retry+1,this.readyState=v,this.es.readyState=v;var e=new a("error");this.es.dispatchEvent(e),x(this.es,this.es.onerror,e)}},T.prototype.onTimeout=function(){if(this.timeout=0,this.currentState===y){this.wasActivity=!1;e=this;this.timeout=o(function(){e.onTimeout()},this.heartbeatTimeout),this.currentState=v,this.dataBuffer.length=0,this.eventTypeBuffer="",this.lastEventIdBuffer=this.lastEventId,this.fieldStart=0,this.valueStart=0,this.state=m;var t=this.url.slice(0,5);t="data:"!==t&&"blob:"!==t?this.url+(-1===this.url.indexOf("?",0)?"?":"&")+"lastEventId="+encodeURIComponent(this.lastEventId)+"&r="+(Math.random()+1).toString().slice(2):this.url;try{this.transport.open(t,this.withCredentials)}catch(t){throw this.close(),t}}else if(this.wasActivity){this.wasActivity=!1;var e=this;this.timeout=o(function(){e.onTimeout()},this.heartbeatTimeout)}else r(new Error("No activity within "+this.heartbeatTimeout+" milliseconds. Reconnecting.")),this.transport.cancel()},T.prototype.close=function(){this.currentState=2,this.transport.cancel(),0!==this.timeout&&(u(this.timeout),this.timeout=0),this.readyState=2,this.es.readyState=2},w.prototype=i.prototype,(E.prototype=new w).close=function(){this._internal.close()},w.call(E),d&&(E.prototype.withCredentials=void 0);null==p||null!=s.EventSource&&(!d||null!=s.EventSource&&"withCredentials"in s.EventSource.prototype)||(s.NativeEventSource=s.EventSource,s.EventSource=E)}("undefined"!=typeof window?window:this);