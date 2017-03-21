/*! jquery.fileapi 0.4.11 - MIT | git://github.com/rubaxa/jquery.fileapi.git */
!function(a,b){"use strict";function c(a){var b;for(b in a)if(a.hasOwnProperty(b)&&!(a[b]instanceof Object||"overlay"===b))return!0;return!1}function d(a,b,c,d){if(c&&d){var e=c>0?c:c[b],f=d>0?d:d[b.substr(3).toLowerCase()],g=e-f,h=/max/.test(b);(h&&0>g||!h&&g>0)&&(a.errors||(a.errors={}),a.errors[b]=Math.abs(g))}}function e(a,b,c,e){if(a){var f=c.length-(a-b);f>0&&m(c.splice(0,f),function(a,b){d(a,"maxFiles",-1,b),e.push(a)})}}var f=a.noop,g=!a.fn.prop,h=g?"attr":"prop",i=g?"removeAttr":"removeProp",j="data-fileapi",k="data-fileapi-id",l=[].slice,m=b.each,n=b.extend,o=function(a,b){var c=l.call(arguments,2);return b.bind?b.bind.apply(b,[a].concat(c)):function(){return b.apply(a,c.concat(l.call(arguments)))}},p=function(a){return"["+j+'="'+a+'"]'},q=function(a){return 0===a.indexOf("#")},r=function(b,c){if(this.$el=b=a(b).on("change.fileapi",'input[type="file"]',o(this,this._onSelect)),this.el=b[0],this._options={},this.options={url:0,data:{},accept:0,multiple:!1,paramName:0,dataType:"json",duplicate:!1,uploadRetry:0,networkDownRetryTimeout:5e3,chunkSize:0,chunkUploadRetry:3,chunkNetworkDownRetryTimeout:2e3,maxSize:0,maxFiles:0,imageSize:0,sortFn:0,filterFn:0,autoUpload:!1,clearOnSelect:void 0,clearOnComplete:void 0,lang:{B:"bytes",KB:"KB",MB:"MB",GB:"GB",TB:"TB"},sizeFormat:"0.00",imageOriginal:!0,imageTransform:0,imageAutoOrientation:!!FileAPI.support.exif,elements:{ctrl:{upload:p("ctrl.upload"),reset:p("ctrl.reset"),abort:p("ctrl.abort")},empty:{show:p("empty.show"),hide:p("empty.hide")},emptyQueue:{show:p("emptyQueue.show"),hide:p("emptyQueue.hide")},active:{show:p("active.show"),hide:p("active.hide")},size:p("size"),name:p("name"),progress:p("progress"),list:p("list"),file:{tpl:p("file.tpl"),progress:p("file.progress"),active:{show:p("file.active.show"),hide:p("file.active.hide")},preview:{el:0,get:0,width:0,height:0,processing:0},abort:p("file.abort"),remove:p("file.remove"),rotate:p("file.rotate")},dnd:{el:p("dnd"),hover:"dnd_hover",fallback:p("dnd.fallback")}},onDrop:f,onDropHover:f,onSelect:f,onBeforeUpload:f,onUpload:f,onProgress:f,onComplete:f,onFilePrepare:f,onFileUpload:f,onFileProgress:f,onFileComplete:f,onFileRemove:null,onFileRemoveCompleted:null},a.extend(!0,this.options,c),c=this.options,this.option("elements.file.preview.rotate",c.imageAutoOrientation),!c.url){var d=this.$el.attr("action")||this.$el.find("form").attr("action");d?c.url=d:this._throw("url � is not defined")}this.$files=this.$elem("list"),this.$fileTpl=this.$elem("file.tpl"),this.itemTplFn=a.fn.fileapi.tpl(a("<div/>").append(this.$elem("file.tpl")).html()),m(c,function(a,b){this._setOption(b,a)},this),this.$el.on("reset.fileapi",o(this,this._onReset)).on("submit.fileapi",o(this,this._onSubmit)).on("upload.fileapi progress.fileapi complete.fileapi",o(this,this._onUploadEvent)).on("fileupload.fileapi fileprogress.fileapi filecomplete.fileapi",o(this,this._onFileUploadEvent)).on("click","["+j+"]",o(this,this._onActionClick));var e=c.elements.ctrl;e&&(this._listen("click",e.reset,o(this,this._onReset)),this._listen("click",e.upload,o(this,this._onSubmit)),this._listen("click",e.abort,o(this,this._onAbort)));var g=FileAPI.support.dnd;this.$elem("dnd.el",!0).toggle(g),this.$elem("dnd.fallback").toggle(!g),g&&this.$elem("dnd.el",!0).dnd(o(this,this._onDropHover),o(this,this._onDrop)),this.$progress=this.$elem("progress"),void 0===c.clearOnSelect&&(c.clearOnSelect=!c.multiple),this.clear(),a.isArray(c.files)&&(this.files=a.map(c.files,function(b){return"string"===a.type(b)&&(b={src:b,size:0}),b.name=b.name||b.src.split("/").pop(),b.type=b.type||/\.(jpe?g|png|bmp|gif|tiff?)/i.test(b.src)&&"image/"+b.src.split(".").pop(),b.complete=!0,b}),this._redraw())};r.prototype={constructor:r,_throw:function(a){throw"jquery.fileapi: "+a},_getFiles:function(a,c){var f=this.options,g=f.maxSize,h=f.maxFiles,i=f.filterFn,j=this.files.length,k=b.getFiles(a),l={all:k,files:[],other:[],duplicate:f.duplicate?[]:this._extractDuplicateFiles(k)},n=f.imageSize,o=this;n||i?b.filterFiles(k,function(a,b){return b&&n&&(d(a,"minWidth",n,b),d(a,"minHeight",n,b),d(a,"maxWidth",n,b),d(a,"maxHeight",n,b)),d(a,"maxSize",g,a.size),!a.errors&&(!i||i(a,b))},function(a,b){e(h,j,a,b),l.other=b,l.files=a,c.call(o,l)}):(m(k,function(a){d(a,"maxSize",g,a.size),l[a.errors?"other":"files"].push(a)}),e(h,j,l.files,l.other),c.call(o,l))},_extractDuplicateFiles:function(a){for(var b,c=[],d=a.length,e=this.files;d--;)for(b=e.length;b--;)if(this._fileCompare(a[d],e[b])){c.push(a.splice(d,1));break}return c},_fileCompare:function(a,b){return a.size==b.size&&a.name==b.name},_getFormatedSize:function(a){var c=this.options,d="B";return a>=b.TB?a/=b[d="TB"]:a>=b.GB?a/=b[d="GB"]:a>=b.MB?a/=b[d="MB"]:a>=b.KB&&(a/=b[d="KB"]),c.sizeFormat.replace(/^\d+([^\d]+)(\d*)/,function(b,e,f){return a=(parseFloat(a)||0).toFixed(f.length),(a+"").replace(".",e)+" "+c.lang[d]})},_onSelect:function(a){this.options.clearOnSelect&&(this.queue=[],this.files=[]),this._getFiles(a,o(this,function(b){b.all.length&&this.emit("select",b)!==!1&&this.add(b.files),FileAPI.reset(a.target)}))},_onActionClick:function(b){var c=b.currentTarget,d=a.attr(c,j),e=a(c).closest("["+k+"]",this.$el).attr(k),f=this._getFile(e),g=!0;this.$file(e).attr("disabled")||("file.remove"==d?f&&this.emit("fileRemove"+(f.complete?"Completed":""),f)&&this.remove(e):/^file\.rotate/.test(d)?this.rotate(e,/ccw/.test(d)?"-=90":"+=90"):g=!1),g&&b.preventDefault()},_listen:function(b,c,d){c&&m(a.trim(c).split(","),function(c){c=a.trim(c),q(c)?a(c).on(b+".fileapi",d):this.$el.on(b+".fileapi",c,d)},this)},_onSubmit:function(a){a.preventDefault(),this.upload()},_onReset:function(a){a.preventDefault(),this.clear(!0)},_onAbort:function(a){a.preventDefault(),this.abort()},_onDrop:function(a){this._getFiles(a,function(a){this.emit("drop",a)!==!1&&this.add(a.files)})},_onDropHover:function(b,c){if(this.emit("dropHover",{state:b,event:c})!==!1){var d=this.option("elements.dnd.hover");d&&a(c.currentTarget).toggleClass(d,b)}},_getFile:function(a){return b.filter(this.files,function(c){return b.uid(c)==a})[0]},_getUploadEvent:function(a,b){a=this.xhr||a;var c={xhr:a,file:this.xhr.currentFile,files:this.xhr.files,widget:this};return n(c,b)},_emitUploadEvent:function(a,b,c){var d=this._getUploadEvent(c);this.emit(a+"Upload",d)},_emitProgressEvent:function(a,b,c,d){var e=this._getUploadEvent(d,b);this.emit(a+"Progress",e)},_emitCompleteEvent:function(b,c,d,e){var f=this._getUploadEvent(d,{error:c,status:d.status,statusText:d.statusText,result:d.responseText});if("file"==b&&(e.complete=!0),"json"==this.options.dataType)try{f.result=a.parseJSON(f.result)}catch(c){f.error=c}this.emit(b+"Complete",f)},_onUploadEvent:function(a,b){var c=this,d=c.$progress,e=a.type;if("progress"==e)d.stop().animate({width:b.loaded/b.total*100+"%"},300);else if("upload"==e)d.width(0);else{var f=function(){d.dequeue(),c[c.options.clearOnComplete?"clear":"dequeue"]()};this.xhr=null,this.active=!1,d.length?d.queue(f):f()}},_onFileUploadPrepare:function(d,e){var f=b.uid(d),g=this._rotate[f],h=this._crop[f],i=this._resize[f],j=this._getUploadEvent(this.xhr);if(g||h||i){var k=a.extend(!0,{},e.imageTransform||{});g=null!=g?g:this.options.imageAutoOrientation?"auto":void 0,a.isEmptyObject(k)||c(k)?(n(k,i),k.crop=h,k.rotate=g):m(k,function(a){n(a,i),a.crop=h,a.rotate=g}),e.imageTransform=k}j.file=d,j.options=e,this.emit("filePrepare",j)},_onFileUploadEvent:function(a,c){var d=this,e=a.type.substr(4),f=b.uid(c.file),g=this.$file(f),h=this._$fileprogress,i=this.options;if(this.__fileId!==f&&(this.__fileId=f,this._$fileprogress=h=this.$elem("file.progress",g)),"progress"==e)h.stop().animate({width:c.loaded/c.total*100+"%"},300);else if("upload"==e||"complete"==e){var j=function(){var a="file."+e,b=d.$elem("file.remove",g);"upload"==e?(b.hide(),h.width(0)):i.onFileRemoveCompleted&&b.show(),h.dequeue(),d.$elem(a+".show",g).show(),d.$elem(a+".hide",g).hide()};h.length?h.queue(j):j(),"complete"==e&&(this.uploaded.push(c.file),delete this._rotate[f])}},_redraw:function(c,d){var e=this.files,f=!!this.active,g=!e.length&&!f,i=!this.queue.length&&!f,j=[],l=0,n=this.$files,o=n.children().length,p=this.option("elements.preview"),q=this.option("elements.file.preview");c&&this.$files.empty(),d&&p&&p.el&&!this.queue.length&&this.$(p.el).empty(),m(e,function(c,d){var e=b.uid(c);if(j.push(c.name),l+=c.complete?0:c.size,p&&p.el)this._makeFilePreview(e,c,p,!0);else if(n.length&&!this.$file(e).length){var f=this.itemTplFn({$idx:o+d,uid:e,name:c.name,type:c.type,size:c.size,complete:!!c.complete,sizeText:this._getFormatedSize(c.size)}),g=a(f).attr(k,e);c.$el=g,n.append(g),c.complete&&(this.$elem("file.upload.hide",g).hide(),this.$elem("file.complete.hide",g).hide()),q.el&&this._makeFilePreview(e,c,q)}},this),this.$elem("name").text(j.join(", ")),this.$elem("size").text(i?"":this._getFormatedSize(l)),this.$elem("empty.show").toggle(g),this.$elem("empty.hide").toggle(!g),this.$elem("emptyQueue.show").toggle(i),this.$elem("emptyQueue.hide").toggle(!i),this.$elem("active.show").toggle(f),this.$elem("active.hide").toggle(!f),this.$(".js-fileapi-wrapper,:file")[f?"attr":"removeAttr"]("aria-disabled",f)[h]("disabled",f),this._disableElem("ctrl.upload",i||f),this._disableElem("ctrl.reset",i||f),this._disableElem("ctrl.abort",!f)},_disableElem:function(a,b){this.$elem(a)[b?"attr":"removeAttr"]("aria-disabled","disabled")[h]("disabled",b)},_makeFilePreview:function(a,c,d,e){var f=this,g=e?f.$(d.el):f.$file(a).find(d.el);if(!f._crop[a])if(/^image/.test(c.type)){var h=b.Image(c.src||c),i=function(){h.get(function(b,e){f._crop[a]||(b?(d.get&&d.get(g,c),f.emit("previewError",[b,c])):g.html(e))})};d.width&&h.preview(d.width,d.height),d.rotate&&h.rotate("auto"),d.processing?d.processing(c,h,i):i()}else d.get&&d.get(g,c)},emit:function(b,c){var d,e=this.options,f=a.Event(b.toLowerCase());return f.widget=this,b=a.camelCase("on-"+b),a.isFunction(e[b])&&(d=e[b].call(this.el,f,c)),this.$el.triggerHandler(f,c),d!==!1&&!f.isDefaultPrevented()},add:function(a,b){if(a=[].concat(a),a.length){var c=this.options,d=c.sortFn;d&&a.sort(d),this.xhr&&this.xhr.append(a),this.queue=b?a:this.queue.concat(a),this.files=b?a:this.files.concat(a),this.active?(this.xhr.append(a),this._redraw(b)):(this._redraw(b),this.options.autoUpload&&this.upload())}},$:function(b,c){return"string"==typeof b&&(b=/^#/.test(b)?b:(c?a(c):this.$el).find(b)),a(b)},$elem:function(b,c,d){c&&c.jquery&&(d=c,c=!1);var e=this.option("elements."+b);return void 0===e&&c&&(e=this.option("elements."+b.substr(0,b.lastIndexOf(".")))),this.$("string"!=a.type(e)&&a.isEmptyObject(e)?[]:e,d)},$file:function(a){return this.$("["+k+'="'+a+'"]')},option:function(b,c){if(void 0!==c&&a.isPlainObject(c))return m(c,function(a,c){this.option(b+"."+c,a)},this),this;var d,e,f=this.options,g=f[b],h=0;if(-1!=b.indexOf("."))for(g=f,b=b.split("."),d=b.length;d>h;h++){if(e=b[h],void 0!==c&&d-h===1){g[e]=c;break}g[e]||(g[e]={}),g=g[e]}else void 0!==c&&(f[b]=c);return void 0!==c&&(this._setOption(b,c,this._options[b]),this._options[b]=c),void 0!==c?c:g},_setOption:function(a,b){switch(a){case"accept":case"multiple":this.$(":file")[b?h:i](a,b);break;case"paramName":b&&this.$(":file")[h]("name",b)}},serialize:function(){var b,c={};return this.$el.find(":input").each(function(d,e){(d=e.name)&&!e.disabled&&(e.checked||/select|textarea|input/i.test(e.nodeName)&&!/checkbox|radio|file/i.test(e.type))&&(b=a(e).val(),void 0!==c[d]?(c[d].push||(c[d]=[c[d]]),c[d].push(b)):c[d]=b)}),c},upload:function(){if(!this.active&&this.emit("beforeUpload",{widget:this,files:this.queue})){this.active=!0;var a=this.$el,c=this.options,d={},e={url:c.url,data:n({},this.serialize(),c.data),headers:c.headers,files:d,uploadRetry:c.uploadRetry,networkDownRetryTimeout:c.networkDownRetryTimeout,chunkSize:c.chunkSize,chunkUploadRetry:c.chunkUploadRetry,chunkNetworkDownRetryTimeout:c.chunkNetworkDownRetryTimeout,prepare:o(this,this._onFileUploadPrepare),imageOriginal:c.imageOriginal,imageTransform:c.imageTransform,imageAutoOrientation:c.imageAutoOrientation};d[a.find(":file").attr("name")||"files[]"]=this.queue,m(["Upload","Progress","Complete"],function(a){var b=a.toLowerCase();e[b]=o(this,this["_emit"+a+"Event"],""),e["file"+b]=o(this,this["_emit"+a+"Event"],"file")},this),this.xhr=b.upload(e),this._redraw()}},abort:function(a){this.active&&this.xhr&&this.xhr.abort(a)},crop:function(c,d){var e=b.uid(c),f=this.options,g=f.multiple?this.option("elements.file.preview"):f.elements.preview,h=(f.multiple?this.$file(e):this.$el).find(g&&g.el);h.length&&b.getInfo(c,o(this,function(e,i){if(e)this.emit("previewError",[e,c]);else{h.find("div>div").length||h.html(a("<div><div></div></div>").css(g).css("overflow","hidden")),this.__cropFile!==c&&(this.__cropFile=c,b.Image(c).rotate(f.imageAutoOrientation?"auto":0).get(function(b,c){h.find(">div>div").html(a(c).width("100%").height("100%"))},"exactFit"));var j=g.width,k=g.height,l=j,m=k,n=j/d.rw,o=k/d.rh;g.keepAspectRatio&&(n>1&&o>1?(n=o=1,m=d.h,l=d.w):o>n?(o=n,m=j*d.rh/d.rw):(n=o,l=k*d.rw/d.rh)),h.find(">div>div").css({width:Math.round(n*i[d.flip?"height":"width"]),height:Math.round(o*i[d.flip?"width":"height"]),marginLeft:-Math.round(n*d.rx),marginTop:-Math.round(o*d.ry)}),g.keepAspectRatio&&h.find(">div").css({width:Math.round(l),height:Math.round(m),marginLeft:j>l?Math.round((j-l)/2):0,marginTop:k>m?Math.round((k-m)/2):0})}})),this._crop[e]=d},resize:function(a,c,d,e){this._resize[b.uid(a)]={type:e,width:c,height:d}},rotate:function(c,d){var e="string"==a.type(c)?c:b.uid(c),f=this.options,g=f.multiple?this.option("elements.file.preview"):f.elements.preview,h=(f.multiple?this.$file(e):this.$el).find(g&&g.el),i=this._rotate;c=this._getFile(e),b.getInfo(c,function(a,g){var j=g&&g.exif&&g.exif.Orientation,k=f.imageAutoOrientation&&b.Image.exifOrientation[j]||0;null==i[e]&&(i[e]=k||0),i[e]=/([+-])=/.test(d)?d=i[e]+("+"==RegExp.$1?1:-1)*d.substr(2):d,c.rotate=d,d-=k,h.css({"-webkit-transform":"rotate("+d+"deg)","-moz-transform":"rotate("+d+"deg)",transform:"rotate("+d+"deg)"})})},remove:function(a){var c="object"==typeof a?b.uid(a):a;this.$file(c).remove(),this.queue=b.filter(this.queue,function(a){return b.uid(a)!=c}),this.files=b.filter(this.files,function(a){return b.uid(a)!=c}),this._redraw()},clear:function(a){this._crop={},this._resize={},this._rotate={},this.queue=[],this.files=[],this.uploaded=[],a=void 0===a?!0:a,this._redraw(a,a)},dequeue:function(){this.queue=[],this._redraw()},widget:function(){return this},toString:function(){return"[jQuery.FileAPI object]"},destroy:function(){this.$files.empty().append(this.$fileTpl),this.$el.off(".fileapi").removeData("fileapi"),m(this.options.elements.ctrl,function(b){q(b)&&a(b).off("click.fileapi")})}},a.fn.fileapi=function(b,c){var d=this.data("fileapi");if(d){if("widget"===b)return d;if("string"==typeof b){var e,f=d[b];return a.isFunction(f)?e=f.apply(d,l.call(arguments,1)):void 0===f?e=d.option(b,c):"files"===b&&(e=f),void 0===e?this:e}}else(null==b||"object"==typeof b)&&this.data("fileapi",new r(this,b));return this},a.fn.fileapi.version="0.4.11",a.fn.fileapi.tpl=function(a){var b=0,c="__b+='";return a.replace(/(?:&lt;|<)%([-=])?([\s\S]+?)%(?:&gt;|>)|$/g,function(d,e,f,g){return c+=a.slice(b,g).replace(/[\r\n"']/g,function(a){return"\\"+a}),f&&(c+=e?"'+\n((__x=("+f+"))==null?'':"+("-"==e?"__esc(__x)":"__x")+")\n+'":"';\n"+f+"\n__b+='"),b=g+d.length,d}),new Function("ctx","var __x,__b='',__esc=function(val){return typeof val=='string'?val.replace(/</g,'&lt;').replace(/\"/g,'&quot;'):val;};with(ctx||{}){\n"+c+"';\n}return __b;")},a.fn.webcam=function(c){var d=this,e=d,g=a(d),h="fileapi-camera",i=g.data(h);return i===!0?(b.log("[webcam.warn] not ready."),e=null):"widget"===c?e=i:"destroy"===c?(i.stop(),g.empty()):i?e=i[c]():i===!1?(b.log("[webcam.error] does not work."),e=null):(g.data(h,!0),c=n({success:f,error:f},c),FileAPI.Camera.publish(g,c,function(a,b){g.data(h,a?!1:b),c[a?"error":"success"].call(d,a||b)})),e},a.fn.cropper=function(c){var d=this,e=c.file;if("string"==typeof c)d.first().Jcrop.apply(d,arguments);else{var f=c.minSize||[0,0],g=c.aspectRatio||f[0]/f[1];a.isArray(c.minSize)&&void 0===c.aspectRatio&&g>0&&(c.aspectRatio=g),b.getInfo(e,function(h,i){var j=b.Image(e),k=c.maxSize,l=e.rotate;k&&j.resize(Math.max(k[0],f[0]),Math.max(k[1],f[1]),"max"),j.rotate(void 0===l?"auto":l).get(function(b,e){var f=c.selection,h=Math.min(e.width,e.height),j=h,k=h/g,l=FileAPI.Image.exifOrientation[i.exif&&i.exif.Orientation]||0;if(f){(/%/.test(f)||f>0&&1>f)&&(f=parseFloat(f,10)/(f>0?1:100),j*=f,k*=f);var n=(e.width-j)/2,o=(e.height-k)/2;c.setSelect=[0|n,0|o,n+j|0,o+k|0]}m(["onSelect","onChange"],function(a,b){(b=c[a])&&(c[a]=function(a){var c=l%180,d=i.width,f=i.height,g=a.x/e.width,h=a.y/e.height,j=a.w/e.width,k=a.h/e.height,m=d*(c?h:g),n=f*(c?1-(a.x+a.w)/e.width:h),o=d*(c?k:j),p=f*(c?j:k);b({x:m,y:n,w:o,h:p,rx:g*(c?f:d),ry:h*(c?d:f),rw:j*(c?f:d),rh:k*(c?d:f),lx:a.x,ly:a.y,lw:a.w,lh:a.h,lx2:a.x2,ly2:a.y2,deg:l,flip:c})})});var p=a("<div/>").css("lineHeight",0).append(a(e).css("margin",0));d.html(p),p.Jcrop(c).trigger("resize")})})}return d}}(jQuery,FileAPI);