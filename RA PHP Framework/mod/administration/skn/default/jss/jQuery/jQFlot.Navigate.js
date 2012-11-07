(function(i){i.fn.drag=function(j,k,l){if(k){this.bind("dragstart",j)}if(l){this.bind("dragend",l)}return !j?this.trigger("drag"):this.bind("drag",k?k:j)};var d=i.event,c=d.special,h=c.drag={not:":input",distance:0,which:1,dragging:false,setup:function(j){j=i.extend({distance:h.distance,which:h.which,not:h.not},j||{});j.distance=e(j.distance);d.add(this,"mousedown",f,j);if(this.attachEvent){this.attachEvent("ondragstart",a)}},teardown:function(){d.remove(this,"mousedown",f);if(this===h.dragging){h.dragging=h.proxy=false}g(this,true);if(this.detachEvent){this.detachEvent("ondragstart",a)}}};c.dragstart=c.dragend={setup:function(){},teardown:function(){}};function f(j){var k=this,l,m=j.data||{};if(m.elem){k=j.dragTarget=m.elem;j.dragProxy=h.proxy||k;j.cursorOffsetX=m.pageX-m.left;j.cursorOffsetY=m.pageY-m.top;j.offsetX=j.pageX-j.cursorOffsetX;j.offsetY=j.pageY-j.cursorOffsetY}else{if(h.dragging||(m.which>0&&j.which!=m.which)||i(j.target).is(m.not)){return}}switch(j.type){case"mousedown":i.extend(m,i(k).offset(),{elem:k,target:j.target,pageX:j.pageX,pageY:j.pageY});d.add(document,"mousemove mouseup",f,m);g(k,false);h.dragging=null;return false;case !h.dragging&&"mousemove":if(e(j.pageX-m.pageX)+e(j.pageY-m.pageY)<m.distance){break}j.target=m.target;l=b(j,"dragstart",k);if(l!==false){h.dragging=k;h.proxy=j.dragProxy=i(l||k)[0]}case"mousemove":if(h.dragging){l=b(j,"drag",k);if(c.drop){c.drop.allowed=(l!==false);c.drop.handler(j)}if(l!==false){break}j.type="mouseup"}case"mouseup":d.remove(document,"mousemove mouseup",f);if(h.dragging){if(c.drop){c.drop.handler(j)}b(j,"dragend",k)}g(k,true);h.dragging=h.proxy=m.elem=false;break}return true}function b(m,k,j){m.type=k;var l=i.event.handle.call(j,m);return l===false?false:l||m.result}function e(j){return Math.pow(j,2)}function a(){return(h.dragging===false)}function g(j,k){if(!j){return}j.unselectable=k?"off":"on";j.onselectstart=function(){return k};if(j.style){j.style.MozUserSelect=k?"":"none"}}})(jQuery);(function(f){var e=["DOMMouseScroll","mousewheel"];f.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var a=e.length;a;){this.addEventListener(e[--a],d,false)}}else{this.onmousewheel=d}},teardown:function(){if(this.removeEventListener){for(var a=e.length;a;){this.removeEventListener(e[--a],d,false)}}else{this.onmousewheel=null}}};f.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}});function d(b){var h=[].slice.call(arguments,1),a=0,c=true;b=f.event.fix(b||window.event);b.type="mousewheel";if(b.wheelDelta){a=b.wheelDelta/120}if(b.detail){a=-b.detail/3}h.unshift(b,a);return f.event.handle.apply(this,h)}})(jQuery);(function(b){var a={xaxis:{zoomRange:null,panRange:null},zoom:{interactive:false,trigger:"dblclick",amount:1.5},pan:{interactive:false,cursor:"move",frameRate:20}};function c(o){function m(q,p){var r=o.offset();r.left=q.pageX-r.left;r.top=q.pageY-r.top;if(p){o.zoomOut({center:r})}else{o.zoom({center:r})}}function d(p,q){m(p,q<0);return false}var i="default",g=0,e=0,n=null;function f(p){if(p.which!=1){return false}var q=o.getPlaceholder().css("cursor");if(q){i=q}o.getPlaceholder().css("cursor",o.getOptions().pan.cursor);g=p.pageX;e=p.pageY}function j(q){var p=o.getOptions().pan.frameRate;if(n||!p){return}n=setTimeout(function(){o.pan({left:g-q.pageX,top:e-q.pageY});g=q.pageX;e=q.pageY;n=null},1/p*1000)}function h(p){if(n){clearTimeout(n);n=null}o.getPlaceholder().css("cursor",i);o.pan({left:g-p.pageX,top:e-p.pageY})}function l(q,p){var r=q.getOptions();if(r.zoom.interactive){p[r.zoom.trigger](m);p.mousewheel(d)}if(r.pan.interactive){p.bind("dragstart",{distance:10},f);p.bind("drag",j);p.bind("dragend",h)}}o.zoomOut=function(p){if(!p){p={}}if(!p.amount){p.amount=o.getOptions().zoom.amount}p.amount=1/p.amount;o.zoom(p)};o.zoom=function(q){if(!q){q={}}var x=q.center,r=q.amount||o.getOptions().zoom.amount,p=o.width(),t=o.height();if(!x){x={left:p/2,top:t/2}}var s=x.left/p,v=x.top/t,u={x:{min:x.left-s*p/r,max:x.left+(1-s)*p/r},y:{min:x.top-v*t/r,max:x.top+(1-v)*t/r}};b.each(o.getAxes(),function(z,C){var D=C.options,B=u[C.direction].min,w=u[C.direction].max,E=D.zoomRange;if(E===false){return}B=C.c2p(B);w=C.c2p(w);if(B>w){var A=B;B=w;w=A}var y=w-B;if(E&&((E[0]!=null&&y<E[0])||(E[1]!=null&&y>E[1]))){return}D.min=B;D.max=w});o.setupGrid();o.draw();if(!q.preventEvent){o.getPlaceholder().trigger("plotzoom",[o])}};o.pan=function(p){var q={x:+p.left,y:+p.top};if(isNaN(q.x)){q.x=0}if(isNaN(q.y)){q.y=0}b.each(o.getAxes(),function(s,u){var v=u.options,t,r,w=q[u.direction];t=u.c2p(u.p2c(u.min)+w),r=u.c2p(u.p2c(u.max)+w);var x=v.panRange;if(x===false){return}if(x){if(x[0]!=null&&x[0]>t){w=x[0]-t;t+=w;r+=w}if(x[1]!=null&&x[1]<r){w=x[1]-r;t+=w;r+=w}}v.min=t;v.max=r});o.setupGrid();o.draw();if(!p.preventEvent){o.getPlaceholder().trigger("plotpan",[o])}};function k(q,p){p.unbind(q.getOptions().zoom.trigger,m);p.unbind("mousewheel",d);p.unbind("dragstart",f);p.unbind("drag",j);p.unbind("dragend",h);if(n){clearTimeout(n)}}o.hooks.bindEvents.push(l);o.hooks.shutdown.push(k)}b.plot.plugins.push({init:c,options:a,name:"navigate",version:"1.3"})})(jQuery);
