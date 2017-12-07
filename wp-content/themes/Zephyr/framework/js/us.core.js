/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 *
 * Open source under the BSD License.
 *
 * Copyright � 2008 George McGinley Smith
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
*/

jQuery.easing.jswing=jQuery.easing.swing;jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,f,a,h,g){return jQuery.easing[jQuery.easing.def](e,f,a,h,g)},easeInQuad:function(e,f,a,h,g){return h*(f/=g)*f+a},easeOutQuad:function(e,f,a,h,g){return -h*(f/=g)*(f-2)+a},easeInOutQuad:function(e,f,a,h,g){if((f/=g/2)<1){return h/2*f*f+a}return -h/2*((--f)*(f-2)-1)+a},easeInCubic:function(e,f,a,h,g){return h*(f/=g)*f*f+a},easeOutCubic:function(e,f,a,h,g){return h*((f=f/g-1)*f*f+1)+a},easeInOutCubic:function(e,f,a,h,g){if((f/=g/2)<1){return h/2*f*f*f+a}return h/2*((f-=2)*f*f+2)+a},easeInQuart:function(e,f,a,h,g){return h*(f/=g)*f*f*f+a},easeOutQuart:function(e,f,a,h,g){return -h*((f=f/g-1)*f*f*f-1)+a},easeInOutQuart:function(e,f,a,h,g){if((f/=g/2)<1){return h/2*f*f*f*f+a}return -h/2*((f-=2)*f*f*f-2)+a},easeInQuint:function(e,f,a,h,g){return h*(f/=g)*f*f*f*f+a},easeOutQuint:function(e,f,a,h,g){return h*((f=f/g-1)*f*f*f*f+1)+a},easeInOutQuint:function(e,f,a,h,g){if((f/=g/2)<1){return h/2*f*f*f*f*f+a}return h/2*((f-=2)*f*f*f*f+2)+a},easeInSine:function(e,f,a,h,g){return -h*Math.cos(f/g*(Math.PI/2))+h+a},easeOutSine:function(e,f,a,h,g){return h*Math.sin(f/g*(Math.PI/2))+a},easeInOutSine:function(e,f,a,h,g){return -h/2*(Math.cos(Math.PI*f/g)-1)+a},easeInExpo:function(e,f,a,h,g){return(f==0)?a:h*Math.pow(2,10*(f/g-1))+a},easeOutExpo:function(e,f,a,h,g){return(f==g)?a+h:h*(-Math.pow(2,-10*f/g)+1)+a},easeInOutExpo:function(e,f,a,h,g){if(f==0){return a}if(f==g){return a+h}if((f/=g/2)<1){return h/2*Math.pow(2,10*(f-1))+a}return h/2*(-Math.pow(2,-10*--f)+2)+a},easeInCirc:function(e,f,a,h,g){return -h*(Math.sqrt(1-(f/=g)*f)-1)+a},easeOutCirc:function(e,f,a,h,g){return h*Math.sqrt(1-(f=f/g-1)*f)+a},easeInOutCirc:function(e,f,a,h,g){if((f/=g/2)<1){return -h/2*(Math.sqrt(1-f*f)-1)+a}return h/2*(Math.sqrt(1-(f-=2)*f)+1)+a},easeInElastic:function(f,h,e,l,k){var i=1.70158;var j=0;var g=l;if(h==0){return e}if((h/=k)==1){return e+l}if(!j){j=k*0.3}if(g<Math.abs(l)){g=l;var i=j/4}else{var i=j/(2*Math.PI)*Math.asin(l/g)}return -(g*Math.pow(2,10*(h-=1))*Math.sin((h*k-i)*(2*Math.PI)/j))+e},easeOutElastic:function(f,h,e,l,k){var i=1.70158;var j=0;var g=l;if(h==0){return e}if((h/=k)==1){return e+l}if(!j){j=k*0.3}if(g<Math.abs(l)){g=l;var i=j/4}else{var i=j/(2*Math.PI)*Math.asin(l/g)}return g*Math.pow(2,-10*h)*Math.sin((h*k-i)*(2*Math.PI)/j)+l+e},easeInOutElastic:function(f,h,e,l,k){var i=1.70158;var j=0;var g=l;if(h==0){return e}if((h/=k/2)==2){return e+l}if(!j){j=k*(0.3*1.5)}if(g<Math.abs(l)){g=l;var i=j/4}else{var i=j/(2*Math.PI)*Math.asin(l/g)}if(h<1){return -0.5*(g*Math.pow(2,10*(h-=1))*Math.sin((h*k-i)*(2*Math.PI)/j))+e}return g*Math.pow(2,-10*(h-=1))*Math.sin((h*k-i)*(2*Math.PI)/j)*0.5+l+e},easeInBack:function(e,f,a,i,h,g){if(g==undefined){g=1.70158}return i*(f/=h)*f*((g+1)*f-g)+a},easeOutBack:function(e,f,a,i,h,g){if(g==undefined){g=1.70158}return i*((f=f/h-1)*f*((g+1)*f+g)+1)+a},easeInOutBack:function(e,f,a,i,h,g){if(g==undefined){g=1.70158}if((f/=h/2)<1){return i/2*(f*f*(((g*=(1.525))+1)*f-g))+a}return i/2*((f-=2)*f*(((g*=(1.525))+1)*f+g)+2)+a},easeInBounce:function(e,f,a,h,g){return h-jQuery.easing.easeOutBounce(e,g-f,0,h,g)+a},easeOutBounce:function(e,f,a,h,g){if((f/=g)<(1/2.75)){return h*(7.5625*f*f)+a}else{if(f<(2/2.75)){return h*(7.5625*(f-=(1.5/2.75))*f+0.75)+a}else{if(f<(2.5/2.75)){return h*(7.5625*(f-=(2.25/2.75))*f+0.9375)+a}else{return h*(7.5625*(f-=(2.625/2.75))*f+0.984375)+a}}}},easeInOutBounce:function(e,f,a,h,g){if(f<g/2){return jQuery.easing.easeInBounce(e,f*2,0,h,g)*0.5+a}return jQuery.easing.easeOutBounce(e,f*2-g,0,h,g)*0.5+h*0.5+a}});


/*!
 * imagesLoaded PACKAGED v3.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("eventEmitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(this,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function c(e){this.img=e}function f(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);for(var i=n.querySelectorAll("img"),r=0,o=i.length;o>r;r++){var s=i[r];this.addImage(s)}}},s.prototype.addImage=function(e){var t=new c(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),c.prototype=new t,c.prototype.check=function(){var e=v[this.img.src]||new f(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},c.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return f.prototype=new t,f.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},f.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},f.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},f.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},f.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},f.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

/*
 Plugin: jQuery Parallax
 Version 1.1.3
 Author: Ian Lunn
 Twitter: @IanLunn
 Author URL: http://www.ianlunn.co.uk/
 Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

 (Contains lots of UpSolution's modifications)

 Dual licensed under the MIT and GPL licenses:
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html
 */

(function( $ ){
	var $window = $(window),
		windowHeight = $window.height();

	$.fn.parallax = function(xposParam){
		this.each(function(){
			var $container = $(this),
				$this = $container.children('.l-section-img, .l-titlebar-img'),
				speedFactor,
				offsetFactor = 0,
				getHeight,
				topOffset = 0,
				containerHeight = 0,
				containerWidth = 0,
			// Disable parallax on certain screen/img ratios
				disableParallax = false,
				parallaxIsDisabled = false,
			// Base image width and height (if can be achieved)
				baseImgHeight = 0,
				baseImgWidth = 0,
			// Backgroud-size cover? and current image size (counted)
				isBgCover = ($this.css('background-size') == 'cover'),
				originalBgPos = $this.css('background-position'),
				curImgHeight = 0,
				reversed = $container.hasClass('parallaxdir_reversed'),
				baseSpeedFactor = reversed ? -0.1 : 0.61,
				xpos,
				outerHeight = true;
			if ($this.length == 0) return;

			// setup defaults if arguments aren't specified
			if (xposParam === undefined) {
				xpos = "50%";
			} else {
				xpos = xposParam;
			}

			if ($container.hasClass('parallax_xpos_right')) {
				xpos = "100%";
			} else if ($container.hasClass('parallax_xpos_left')) {
				xpos = "0%";
			}

			if (outerHeight){
				getHeight = function(jqo){
					return jqo.outerHeight(true);
				};
			} else {
				getHeight = function(jqo){
					return jqo.height();
				};
			}

			// Count background image size
			function getBackgroundSize(callback){
				var img = new Image(),
				// here we will place image's width and height
					width, height,
				// here we get the size of the background and split it to array
					backgroundSize = ($this.css('background-size') || ' ').split(' '),
					backgroundWidthAttr = $this.attr('data-img-width'),
					backgroundHeightAttr = $this.attr('data-img-height');

				if (backgroundWidthAttr != '') width = parseInt(backgroundWidthAttr);
				if (backgroundHeightAttr != '') height = parseInt(backgroundHeightAttr);

				if (width !== undefined && height !== undefined){
					// Image is not needed
					return callback({ width: width, height: height });
				}

				// checking if width was set to pixel value
				if (/px/.test(backgroundSize[0])) width = parseInt(backgroundSize[0]);
				// checking if width was set to percent value
				if (/%/.test(backgroundSize[0])) width = $this.parent().width() * (parseInt(backgroundSize[0]) / 100);
				// checking if height was set to pixel value
				if (/px/.test(backgroundSize[1])) height = parseInt(backgroundSize[1]);
				// checking if height was set to percent value
				if (/%/.test(backgroundSize[1])) height = $this.parent().height() * (parseInt(backgroundSize[0]) / 100);

				if (width !== undefined && height !== undefined){
					// Image is not needed
					return callback({ width: width, height: height });
				}

				// Image is needed
				img.onload = function () {
					// check if width was set earlier, if not then set it now
					if (typeof width == 'undefined') width = this.width;
					// do the same with height
					if (typeof height == 'undefined') height = this.height;
					// call the callback
					callback({ width: width, height: height });
				};
				// extract image source from css using one, simple regex
				// src should be set AFTER onload handler
				img.src = ($this.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
			}
			function update(){
				if (disableParallax){
					if ( ! parallaxIsDisabled){
						$this.css('backgroundPosition', originalBgPos);
						$container.usMod('parallax', 'fixed');
						parallaxIsDisabled = true;
					}
					return;
				}else{
					if (parallaxIsDisabled){
						$container.usMod('parallax', 'ver');
						parallaxIsDisabled = false;
					}
				}
				if (isNaN(speedFactor))
					return;

				var pos = $window.scrollTop();
				// Check if totally above or totally below viewport
				if ((topOffset + containerHeight < pos) || (pos < topOffset - windowHeight)) return;
				$this.css('backgroundPosition', xpos + " " + (offsetFactor + speedFactor * (topOffset - pos)) + "px");

			}
			function resize(){
				setTimeout(function(){
					windowHeight = $window.height();
					containerHeight = getHeight($this);
					containerWidth = $this.width();


					if ($window.width() < $us.canvasOptions.disableEffectsWidth) {
						disableParallax = true;
					} else {
						disableParallax = false;
						if (isBgCover){
							if (baseImgWidth / baseImgHeight <= containerWidth / containerHeight){
								// Resizing by width
								curImgHeight = baseImgHeight * ($this.width() / baseImgWidth);
								disableParallax = false;
							}
							else {
								disableParallax = true;
							}
						}
					}

					// Improving speed factor to prevent showing image limits
					if (curImgHeight !== 0){
						if (baseSpeedFactor >= 0) {
							speedFactor = Math.min(baseSpeedFactor, curImgHeight / windowHeight);
							offsetFactor = Math.min(0, .5 * (windowHeight - curImgHeight - speedFactor * (windowHeight - containerHeight)));
						} else {
							speedFactor = Math.min(baseSpeedFactor, (windowHeight - containerHeight) / (windowHeight + containerHeight));
							offsetFactor = Math.max(0, speedFactor * containerHeight);
						}
					}else{
						speedFactor = baseSpeedFactor;
						offsetFactor = 0;
					}
					topOffset = $this.offset().top;
					update();
				}, 10);
			}

			getBackgroundSize(function(sz){
				curImgHeight = baseImgHeight = sz.height;
				baseImgWidth = sz.width;
				resize();
			});

			$window.bind({scroll: update, load: resize, resize: resize});
			resize();
		});
	};

	//$(function(){
	jQuery('.parallax_ver').parallax('50%');
	//});

})(jQuery);

/*
 * Horparallax
 *
 * @version 1.0
 *
 * Copyright 2017 UpSolution
 */

!function($){

	/*
	 ********* Horparallax class definition ***********/
	var Horparallax = function(container, options){
		// Context
		var that = this;
		this.$window = $(window);
		this.container = $(container);
		// Apply options
		if (container.onclick != undefined){
			options = $.extend({}, container.onclick() || {}, typeof options == 'object' && options);
			this.container.removeProp('onclick');
		}
		options = $.extend({}, $.fn.horparallax.defaults, typeof options == 'object' && options);
		this.options = options;
		this.bg = this.container.find(options.bgSelector);
		// Count sizes
		this.containerWidth = this.container.outerWidth();
		this.containerHeight = this.container.outerHeight();
		this.bgWidth = this.bg.outerWidth();
		this.windowHeight = this.$window.height();
		// Count frame rate
		this._frameRate = Math.round(1000 / this.options.fps);
		// To fix IE bug that handles mousemove before mouseenter
		this.mouseInside = false;
		// Mouse events for desktop browsers
		if ( ! ('ontouchstart' in window) || ! ('DeviceOrientationEvent' in window)){
			this.container
				.mouseenter(function(e){
					// To fix IE bug that handles mousemove before mouseenter
					that.mouseInside = true;
					var offset = that.container.offset(),
						coord = (e.pageX - offset.left) / that.containerWidth;
					that.cancel();
					that._hoverAnimation = true;
					that._hoverFrom = that.now;
					that._hoverTo = coord;
					that.start(that._hoverTo);
				})
				.mousemove(function(e){
					// To fix IE bug that handles mousemove before mouseenter
					if ( ! that.mouseInside) return;
					// Reducing processor load for too frequent event calls
					if (that._lastFrame + that._frameRate > Date.now()) return;
					var offset = that.container.offset(),
						coord = (e.pageX - offset.left) / that.containerWidth;
					// Handle hover animation
					if (that._hoverAnimation){
						that._hoverTo = coord;
						return;
					}
					that.set(coord);
					that._lastFrame = Date.now();
				})
				.mouseleave(function(e){
					that.mouseInside = false;
					that.cancel();
					that.start(that.options.basePoint);
				});
		}
		// Handle resize
		this.$window.resize(function(){ that.handleResize(); });
		// Device orientation events for touch devices
		this._orientationDriven = ('ontouchstart' in window && 'DeviceOrientationEvent' in window);
		if (this._orientationDriven){
			// Check if container is visible
			this._checkIfVisible();
			window.addEventListener("deviceorientation", function(e){
				// Reducing processor load for too frequent event calls
				if ( ! that.visible || that._lastFrame + that._frameRate > Date.now()) return;
				that._deviceOrientationChange(e);
				that._lastFrame = Date.now();
			});
			this.$window.resize(function(){ that._checkIfVisible(); });
			this.$window.scroll(function(){ that._checkIfVisible(); });
		}
		// Set to basepoint
		this.set(this.options.basePoint);
		this._lastFrame = Date.now();
	};

	Horparallax.prototype = {

		/**
		 * Event to fire on deviceorientation change
		 * @private
		 */
		_deviceOrientationChange: function(e){
			var gamma = e.gamma,
				beta = e.beta,
				x, y;
			switch (window.orientation){
				case -90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (beta + 45) / 90;
					break;
				case 90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (45 - beta) / 90;
					break;
				case 180:
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (gamma + 45) / 90;
					break;
				case 0:
				default:
					// Upside down
					if (gamma < -90 || gamma > 90) gamma = Math.abs(e.gamma)/e.gamma * (180 - Math.abs(e.gamma));
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (45 - gamma) / 90;
					break;
			}
			this.set(x);
		},

		/**
		 * Handle container resize
		 */
		handleResize: function()
		{
			this.containerWidth = this.container.outerWidth();
			this.containerHeight = this.container.outerHeight();
			this.bgWidth = this.bg.outerWidth();
			this.windowHeight = this.$window.height();
			this.set(this.now);
		},

		/**
		 * Update container visibility status (to prevent unnessesary rendering)
		 * @private
		 */
		_checkIfVisible: function()
		{
			var scrollTop = this.$window.scrollTop(),
				containerTop = this.container.offset().top;
			this.visible = (containerTop + this.containerHeight > scrollTop && containerTop < scrollTop + this.windowHeight);
		},

		/**
		 * Render horparallax frame.
		 * @param {Array} x is ranged in [0, 1]
		 */
		set: function(x)
		{
			this.bg.css('left', (this.containerWidth - this.bgWidth) * x);
			this.now = x;
			return this;
		},

		/**
		 * Step value computing function, read more at http://mootools.net/docs/core/Fx/Fx
		 * @param {Number} from
		 * @param {Number} to
		 * @param {Number} delta
		 * @return {Number}
		 */
		compute: function(from, to, delta)
		{
			if (this._hoverAnimation) return (this._hoverTo - this._hoverFrom) * delta + this._hoverFrom;
			return (to - from) * delta + from;
		},

		/**
		 * Start animation to certain point
		 * @param {Array} to
		 * @return {Horparallax}
		 */
		start: function(to)
		{
			var from = this.now,
				that = this;
			this.container
				.css('delta', 0)
				.animate({
					delta: 1
				}, {
					duration: this.options.duration,
					easing: this.options.easing,
					complete: function(){
						that._hoverAnimation = false;
					},
					step: function(delta){
						that.set(that.compute(from, to, delta));
					},
					queue: false
				});
			return this;
		},

		/**
		 * Cancel animation
		 * @return {Horparallax}
		 */
		cancel: function()
		{
			this._hoverAnimation = false;
			this.container.stop(true, false);
			return this;
		}


	};

	// EaseOutElastic easing
	if ($.easing.easeOutElastic == undefined){
		/**
		 * Original function by George McGinley Smith
		 * @link http://gsgd.co.uk/sandbox/jquery/easing/
		 */
		$.easing.easeOutElastic = function (x, t, b, c, d) {
			var s = 1.70158, p = 0, a = c;
			if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
			if (a < Math.abs(c)) { a=c; var s=p/4; }
			else var s = p/(2*Math.PI) * Math.asin (c/a);
			return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
		};
	}

	$.fn.horparallax = function(options){
		return this.each(function(){
			var $this = $(this),
				data = $this.data('horparallax');
			if ( ! data) $this.data('horparallax', (data = new Horparallax(this, options)))
		});
	};

	$.fn.horparallax.defaults = {
		/**
		 * @var {Number} Frame per second limit for rendering
		 */
		fps: 60,

		/**
		 * @var {Number} Point for basic position (after the cursor moves out of the container)
		 */
		basePoint: .5,

		/**
		 * @var {Number} Return to base point duration
		 */
		duration: 500,

		/**
		 * @var {String} Background layer selector
		 */
		bgSelector: '.l-section-img, .l-titlebar-img',

		/**
		 * @var {Function} Returning-to-basepoint easing
		 */
		easing: 'swing'// 'easeOutElastic'
	};

	$.fn.horparallax.Constructor = Horparallax;

	$(function(){
		jQuery('.parallax_hor').horparallax();
	});

}(jQuery);

/**
 * UpSolution Theme Core JavaScript Code
 *
 * @requires jQuery
 */
if (window.$us === undefined) window.$us = {};

/**
 * Retrieve/set/erase dom modificator class <mod>_<value> for UpSolution CSS Framework
 * @param {String} mod Modificator namespace
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
jQuery.fn.usMod = function(mod, value){
	if (this.length == 0) return this;
	// Remove class modificator
	if (value === false) {
		this.get(0).className = this.get(0).className.replace(new RegExp('(^| )' + mod + '\_[a-z0-9]+( |$)'), '$2');
		return this;
	}
	var pcre = new RegExp('^.*?' + mod + '\_([a-z0-9]+).*?$'),
		arr;
	// Retrieve modificator
	if (value === undefined) {
		return (arr = pcre.exec(this.get(0).className)) ? arr[1] : false;
	}
	// Set modificator
	else {
		this.usMod(mod, false).get(0).className += ' ' + mod + '_' + value;
		return this;
	}
};

/**
 * Convert data from PHP to boolean the right way
 * @param {mixed} value
 * @returns {Boolean}
 */
$us.toBool = function(value){
	if (typeof value == 'string') return (value == 'true' || value == 'True' || value == 'TRUE' || value == '1');
	if (typeof value == 'boolean') return value;
	return !!parseInt(value);
};

$us.getScript = function(url, callback){
	if ( ! $us.ajaxLoadJs ) {
		callback();
		return false;
	}

	if ($us.loadedScripts === undefined) {
		$us.loadedScripts = {};
		$us.loadedScriptsFunct = {};
	}

	if ($us.loadedScripts[url] === 'loaded') {
		callback();
		return;
	} else if ($us.loadedScripts[url] === 'loading') {
		$us.loadedScriptsFunct[url].push(callback);
		return;
	}

	$us.loadedScripts[url] = 'loading';
	$us.loadedScriptsFunct[url] = [];
	$us.loadedScriptsFunct[url].push(callback)

	var complete = function(){
		for (var i = 0; i < $us.loadedScriptsFunct[url].length; i++){
			$us.loadedScriptsFunct[url][i]();
		}
		$us.loadedScripts[url] = 'loaded';
	};

	var options = {
		dataType: "script",
		cache: true,
		url: url,
		complete: complete
	};

	return jQuery.ajax(options);
};

// Detecting IE browser
$us.detectIE = function() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
		// IE 10 or older => return version number
		return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	}

	var trident = ua.indexOf('Trident/');
	if (trident > 0) {
		// IE 11 => return version number
		var rv = ua.indexOf('rv:');
		return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	}

	var edge = ua.indexOf('Edge/');
	if (edge > 0) {
		// Edge (IE 12+) => return version number
		return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	}

	// other browser
	return false;
};

// Fixing hovers for devices with both mouse and touch screen
jQuery.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
jQuery('html').toggleClass('no-touch', !jQuery.isMobile);

/**
 * Commonly used jQuery objects
 */
!function($){
	$us.$window = $(window);
	$us.$document = $(document);
	$us.$html = $('html');
	$us.$body = $('.l-body:first');
	$us.$htmlBody = $us.$html.add($us.$body);
	$us.$canvas = $('.l-canvas:first');
}(jQuery);

/**
 * $us.canvas
 *
 * All the needed data and functions to work with overall canvas.
 */
!function($){
	"use strict";

	function USCanvas(options){

		// Setting options
		var defaults = {
			disableEffectsWidth: 900,
			responsive: true,
			backToTopDisplay: 100
		};
		this.options = $.extend({}, defaults, options || {});

		// Commonly used dom elements
		this.$header = $us.$canvas.find('.l-header');
		this.$main = $us.$canvas.find('.l-main');
		this.$titlebar = $us.$canvas.find('.l-titlebar');
		this.$sections = $us.$canvas.find('.l-section');
		this.$firstSection = this.$sections.first();
		this.$secondSection = this.$sections.eq(1);
		this.$fullscreenSections = this.$sections.filter('.height_full');
		this.$topLink = $('.w-toplink');

		// Canvas modificators
		this.sidebar = $us.$canvas.usMod('sidebar');
		this.type = $us.$canvas.usMod('type');
		// Initial header position
		this._headerPos = this.$header.usMod('pos');
		// Current header position
		this.headerPos = this._headerPos;
		this.headerInitialPos = $us.$body.usMod('header_inpos');
		this.headerBg = this.$header.usMod('bg');
		this.rtl = $us.$body.hasClass('rtl');

		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;

		// Used to prevent resize events on scroll for Android browsers
		this.isScrolling = false;
		this.scrollTimeout = false;
		this.isAndroid = /Android/i.test(navigator.userAgent);

		// If in iframe...
		if ($us.$body.hasClass('us_iframe')) {
			// change links so they lead to main window
			$('a:not([target])').each(function(){ $(this).attr('target','_parent')});
			// hide preloader
			jQuery(function($){
				var $framePreloader = $('.l-popup-box-content .g-preloader', window.parent.document);
				$framePreloader.hide();
			});
		}

		// Boundable events
		this._events = {
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this)
		};

		$us.$window.on('scroll', this._events.scroll);
		$us.$window.on('resize load', this._events.resize);
		// Complex logics requires two initial renders: before inner elements render and after
		setTimeout(this._events.resize, 25);
		setTimeout(this._events.resize, 75);
	}

	USCanvas.prototype = {

		/**
		 * Scroll-driven logics
		 */
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());

			// Show/hide go to top link
			this.$topLink.toggleClass('active', (scrollTop >= this.winHeight*this.options.backToTopDisplay/100));

			if (this.isAndroid) {
				this.isScrolling = true;
				if (this.scrollTimeout) clearTimeout(this.scrollTimeout);
				this.scrollTimeout = setTimeout(function () {
					this.isScrolling = false;
				}.bind(this), 100);
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function(){
			// Window dimensions
			this.winHeight = parseInt($us.$window.height());
			this.winWidth = parseInt($us.$window.width());

			// Disabling animation on mobile devices
			$us.$body.toggleClass('disable_effects', (this.winWidth < this.options.disableEffectsWidth));

			// Vertical centering of fullscreen sections in IE 11
			var ieVersion = $us.detectIE();
			if ((ieVersion !== false && ieVersion == 11) && (this.$fullscreenSections.length > 0 && ! this.isScrolling)) {
				var adminBar = $('#wpadminbar'),
					adminBarHeight = (adminBar.length)?adminBar.height():0;
				this.$fullscreenSections.each(function(index, section){
					var $section = $(section),
						sectionHeight = this.winHeight,
						isFirstSection = (index == 0 && this.$titlebar.length == 0 && $section.is(this.$firstSection));
					// First section
					if (isFirstSection) {
						sectionHeight -= $section.offset().top;
					}
					// 2+ sections
					else {
						sectionHeight -= $us.header.scrolledOccupiedHeight + adminBarHeight;
					}
					if ($section.hasClass('valign_center')) {
						var $sectionH = $section.find('.l-section-h'),
							sectionTopPadding = parseInt($section.css('padding-top')),
							contentHeight = $sectionH.outerHeight(),
							topMargin;
						$sectionH.css('margin-top', '');
						// Section was extended by extra top padding that is overlapped by fixed solid header and not visible
						var sectionOverlapped = isFirstSection && $us.header.pos == 'fixed' && $us.header.bg != 'transparent' && $us.header.orientation != 'ver';
						if (sectionOverlapped) {
							// Part of first section is overlapped by header
							topMargin = Math.max(0, (sectionHeight - sectionTopPadding - contentHeight) / 2);
						} else {
							topMargin = Math.max(0, (sectionHeight - contentHeight) / 2 - sectionTopPadding);
						}
						$sectionH.css('margin-top', topMargin || '');
					}
				}.bind(this));
				$us.$canvas.trigger('contentChange');
			}

			// If the page is loaded in iframe
			if ($us.$body.hasClass('us_iframe')) {
				var $frameContent = $('.l-popup-box-content', window.parent.document),
					outerHeight = $us.$body.outerHeight(true);
				// var $frame = $('.l-popup-box-content-frame', window.parent.document);
				// $frame.css('height', $us.$body.outerHeight(true) - -10);
				if ( outerHeight > 0 && $(window.parent).height() > outerHeight){
					$frameContent.css('height', outerHeight);
				} else {
					$frameContent.css('height', '');
				}
			}

			// Fix scroll glitches that could occur after the resize
			this.scroll();
		}
	};

	$us.canvas = new USCanvas($us.canvasOptions || {});

}(jQuery);

/**
 * $us.header
 * Dev note: should be initialized after $us.canvas
 */
!function($){
	"use strict";
	function USHeader(settings){
		this.settings = settings || {};
		this.state = 'default'; // 'tablets' / 'mobiles'
		this.$container = $us.$canvas.find('.l-header');
		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;
		if (this.$container.length == 0) {
			return;
		}
		this.$topCell = this.$container.find('.l-subheader.at_top .l-subheader-cell:first');
		this.$middleCell = this.$container.find('.l-subheader.at_middle .l-subheader-cell:first');
		this.$bottomCell = this.$container.find('.l-subheader.at_bottom .l-subheader-cell:first');
		this.$showBtn = $('.w-header-show:first');
		this.orientation = $us.$body.usMod('header');
		this.pos = this.$container.usMod('pos'); // 'fixed' / 'static'
		this.bg = this.$container.usMod('bg'); // 'solid' / 'transparent'
		this.shadow = this.$container.usMod('shadow'); // 'none' / 'thin' / 'wide'

		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;

		// Breakpoints
		this.tabletsBreakpoint = parseInt(settings.tablets && settings.tablets.options && settings.tablets.options.breakpoint) || 900;
		this.mobilesBreakpoint = parseInt(settings.mobiles && settings.mobiles.options && settings.mobiles.options.breakpoint) || 600;

		this._events = {
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this),
			contentChange: function(){
				this._countScrollable();
			}.bind(this),
			hideMobileVerticalHeader: function(e){
				if ($.contains(this.$container[0], e.target)) return;
				$us.$body
					.off($.isMobile ? 'touchstart' : 'click', this._events.hideMobileVerticalHeader)
					.removeClass('header-show');
			}.bind(this)
		};
		this.$elms = {};
		this.$places = {
			hidden: this.$container.find('.l-subheader.for_hidden')
		};
		this.$container.find('.l-subheader-cell').each(function(index, cell){
			var $cell = $(cell);
			this.$places[$cell.parent().parent().usMod('at') + '_' + $cell.usMod('at')] = $cell;
		}.bind(this));
		var regexp = /(^| )ush_([a-z_]+)_([0-9]+)( |$)/;
		this.$container.find('[class*=ush_]').each(function(index, elm){
			var $elm = $(elm),
				matches = regexp.exec($elm.attr('class'));
			if (!matches) return;
			var id = matches[2] + ':' + matches[3];
			this.$elms[id] = $elm;
			if ($elm.is('.w-vwrapper, .w-hwrapper')) {
				this.$places[id] = $elm;
			}
		}.bind(this));
		// TODO Objects with the header elements
		$us.$window.on('scroll', this._events.scroll);
		$us.$window.on('resize load', this._events.resize);
		this.resize();

		$us.$canvas.on('contentChange', function(){
			if (this.orientation == 'ver') this.docHeight = $us.$document.height();
		}.bind(this));

		this.$container.on('contentChange', this._events.contentChange);

		this.$showBtn.on('click', function(e){
			if ($us.$body.hasClass('header-show')) return;
			e.stopPropagation();
			$us.$body
				.addClass('header-show')
				.on($.isMobile ? 'touchstart' : 'click', this._events.hideMobileVerticalHeader);
		}.bind(this));
	}

	$.extend(USHeader.prototype, {
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());
			if (this.pos == 'fixed') {
				if (this.orientation == 'hor') {
					if (($us.canvas.headerInitialPos == 'bottom' || $us.canvas.headerInitialPos == 'below') && ($us.$body.usMod('state') == 'default')) {
						if (this.adminBarHeight) {
							scrollTop += this.adminBarHeight;
						}
						if (scrollTop >= this.headerTop) {
							if ( ! this.$container.hasClass('sticky')) {
								this.$container.addClass('sticky');
							}
							if (this.applyHeaderTop && this.$container.css('top') != '') {
								this.$container.css('top', '');
							}
						} else if (scrollTop < this.headerTop) {
							if (this.$container.hasClass('sticky')) {
								this.$container.removeClass('sticky');
							}
							if (this.applyHeaderTop && this.$container.css('top') != this.headerTop) {
								this.$container.css('top', this.headerTop);
							}
						}

					} else {
						this.$container.toggleClass('sticky', scrollTop >= (this.settings[this.state].options.scroll_breakpoint || 100));
					}

				} else if ( ! jQuery.isMobile && this.$container.hasClass('scrollable') && this.docHeight > this.headerHeight + this.htmlTopMargin) {
					var scrollRangeDiff = this.headerHeight - $us.canvas.winHeight + this.htmlTopMargin;
					if (this._sidedHeaderScrollRange === undefined) {
						this._sidedHeaderScrollRange = [0, scrollRangeDiff];
					}
					if (scrollTop <= this._sidedHeaderScrollRange[0]) {
						this._sidedHeaderScrollRange[0] = Math.max(0, scrollTop);
						this._sidedHeaderScrollRange[1] = this._sidedHeaderScrollRange[0] + scrollRangeDiff;
						this.$container.css({
							position: 'fixed',
							top: this.htmlTopMargin
						});
					}
					else if (this._sidedHeaderScrollRange[0] < scrollTop && scrollTop < this._sidedHeaderScrollRange[1]) {
						this.$container.css({
							position: 'absolute',
							top: this._sidedHeaderScrollRange[0]
						});
					}
					else if (this._sidedHeaderScrollRange[1] <= scrollTop) {
						this._sidedHeaderScrollRange[1] = Math.min(this.docHeight - $us.canvas.winHeight, scrollTop);
						this._sidedHeaderScrollRange[0] = this._sidedHeaderScrollRange[1] - scrollRangeDiff;
						this.$container.css({
							position: 'fixed',
							top: $us.canvas.winHeight - this.headerHeight
						});
					}
				}
			}
		},
		resize: function(){
			var newState = 'default';
			if (window.innerWidth <= this.tabletsBreakpoint) newState = (window.innerWidth <= this.mobilesBreakpoint) ? 'mobiles' : 'tablets';
			this.setState(newState);
			if (this.pos == 'fixed' && this.orientation == 'hor') {
				var isSticky = this.$container.hasClass('sticky');
				this.$container.addClass('notransition');
				if (!isSticky) this.$container.addClass('sticky');
				this.scrolledOccupiedHeight = this.$container.height();
				if (!isSticky) this.$container.removeClass('sticky');
				// Removing with a small delay to prevent css glitch
				setTimeout(function(){
					this.$container.removeClass('notransition');
				}.bind(this), 50);
			} else /*if (this.orientation == 'ver' || this.pos == 'static')*/ {
				this.scrolledOccupiedHeight = 0;
			}

			if (this.orientation == 'hor') {
				if (this.pos == 'fixed' && ($us.canvas.headerInitialPos == 'bottom' || $us.canvas.headerInitialPos == 'below') && ($us.$body.usMod('state') == 'default')) {
					var adminBar = $('#wpadminbar');
					this.adminBarHeight = (adminBar.length)?adminBar.height():0;

					this.headerTop = $us.canvas.$firstSection.outerHeight() + this.adminBarHeight;
					if ($us.canvas.headerInitialPos == 'bottom' ) {
						this.headerTop = this.headerTop - this.$container.outerHeight();
					}
					if ( ! $us.canvas.$firstSection.hasClass('height_full')) {
						this.$container.css('bottom', 'auto');
						this.applyHeaderTop = true;
						this.$container.css('top', this.headerTop);
					}

				} else {
					this.applyHeaderTop = false;
					this.$container.css('top', '');
				}
			} else {
				this.applyHeaderTop = false;
				this.$container.css('top', '');
			}

			this._countScrollable();
			this.scroll();
		},
		setState: function(newState){
			if (newState == this.state) return;
			var newOrientation = this.settings[newState].options.orientation || 'hor',
				newPos = $us.toBool(this.settings[newState].options.sticky) ? 'fixed' : 'static',
				newBg = $us.toBool(this.settings[newState].options.transparent) ? 'transparent' : 'solid',
				newShadow = this.settings[newState].options.shadow || 'thin';
			if (newOrientation == 'ver') {
				newPos = 'fixed';
				newBg = 'solid';
			}
			this.state = newState;
			// Don't change the order: orientation -> pos -> bg -> layout
			this._setOrientation(newOrientation);
			this._setPos(newPos);
			this._setBg(newBg);
			this._setShadow(newShadow);
			this._setLayout(this.settings[newState].layout || {});
			$us.$body.usMod('state', newState);
			if (newState == 'default') $us.$body.removeClass('header-show');
			// Updating the menu because of dependencies
			if ($us.nav !== undefined) $us.nav.resize();
		},
		_setOrientation: function(newOrientation){
			if (newOrientation == this.orientation) return;
			$us.$body.usMod('header', newOrientation);
			this.orientation = newOrientation;
		},
		_countScrollable: function(){
			if (this.orientation == 'ver' && this.pos == 'fixed' && this.state == 'default') {
				this.docHeight = $us.$document.height();
				this.htmlTopMargin = parseInt($us.$html.css('margin-top'));
				this.headerHeight = this.$topCell.height() + this.$middleCell.height() + this.$bottomCell.height();
				if (this.headerHeight > $us.canvas.winHeight - this.htmlTopMargin) {
					this.$container.addClass('scrollable');
				} else if (this.$container.hasClass('scrollable')) {
					this.$container.removeClass('scrollable').resetInlineCSS('position', 'top', 'bottom');
					delete this._sidedHeaderScrollRange;
				}
				if (this.headerHeight + this.htmlTopMargin >= this.docHeight) {
					this.$container.css({
						position: 'absolute',
						top: 0
					});
				}
			} else if (this.$container.hasClass('scrollable')) {
				this.$container.removeClass('scrollable').resetInlineCSS('position', 'top', 'bottom');
				delete this._sidedHeaderScrollRange;
			}
		},
		_setPos: function(newPos){
			if (newPos == this.pos) return;
			this.$container.usMod('pos', newPos);
			if (newPos == 'static') {
				this.$container.removeClass('sticky');
			}
			this.pos = newPos;
			this._countScrollable();
		},
		_setBg: function(newBg){
			if (newBg == this.bg) return;
			this.$container.usMod('bg', newBg);
			this.bg = newBg;
		},
		_setShadow: function(newShadow){
			if (newShadow == this.shadow) return;
			this.$container.usMod('shadow', newShadow);
			this.shadow = newShadow;
		},
		/**
		 * Recursive function to place elements based on their ids
		 * @param {Array} elms
		 * @param {jQuery} $place
		 * @private
		 */
		_placeElements: function(elms, $place){
			for (var i = 0; i < elms.length; i++) {
				var elmId;
				if (typeof elms[i] == 'object') {
					// Wrapper
					elmId = elms[i][0];
					if (this.$places[elmId] === undefined || this.$elms[elmId] === undefined) continue;
					this.$elms[elmId].appendTo($place);
					this._placeElements(elms[i].shift(), this.$places[elmId]);
				} else {
					// Element
					elmId = elms[i];
					if (this.$elms[elmId] === undefined) continue;
					this.$elms[elmId].appendTo($place);
				}
			}
		},
		_setLayout: function(newLayout){
			// Retrieving the currently shown layout structure
			var curLayout = {};
			$.each(this.$places, function(place, $place){
			}.bind(this));
			for (var place in newLayout) {
				if (!newLayout.hasOwnProperty(place) || this.$places[place] === undefined) continue;
				this._placeElements(newLayout[place], this.$places[place]);
			}
		}
	});
	$us.header = new USHeader($us.headerSettings || {});
}(jQuery);

/**
 * $us.nav
 *
 * Header navigation will all the possible states
 *
 * @requires $us.canvas
 */
!function($){

	$us.Nav = function(container, options){
		this.init(container, options);
	};

	$us.mobileNavOpened = 0;

	$us.Nav.prototype = {
		init: function(container, options){
			// Commonly used dom elements
			this.$nav = $(container);
			if (this.$nav.length == 0) return;
			this.$control = this.$nav.find('.w-nav-control');
			this.$close = this.$nav.find('.w-nav-close');
			this.$items = this.$nav.find('.menu-item');
			this.$list = this.$nav.find('.w-nav-list.level_1');
			this.$subItems = this.$list.find('.menu-item-has-children');
			this.$subAnchors = this.$list.find('.menu-item-has-children > .w-nav-anchor');
			this.$subLists = this.$list.find('.menu-item-has-children > .w-nav-list');
			this.$anchors = this.$nav.find('.w-nav-anchor');

			// Setting options
			this.options = this.$nav.find('.w-nav-options:first')[0].onclick() || {};

			// In case the nav doesn't exist, do nothing
			if (this.$nav.length == 0) return;

			this.type = this.$nav.usMod('type');
			this.layout = this.$nav.usMod('layout');
			this.mobileOpened = false;

			// Mobile menu toggler
			this.$control.on('click', function(){
				this.mobileOpened = !this.mobileOpened;
				if (this.mobileOpened) {
					// Closing all other menus if present
					$('.l-header .w-nav').not(container).each(function(){
						$(this).trigger('USNavClose');
					});
					// Closing opened sublists
					this.$control.addClass('active');
					this.$items.filter('.opened').removeClass('opened');
					this.$subLists.resetInlineCSS('display', 'height');
					if (this.layout == 'dropdown') {
						this.$list.slideDownCSS(250, this._events.contentChanged);
					}
					$us.mobileNavOpened++;
				} else {
					this.$control.removeClass('active');
					if (this.layout == 'dropdown') {
						this.$list.slideUpCSS(250, this._events.contentChanged);
					}
					$us.mobileNavOpened--;
				}
				$us.$canvas.trigger('contentChange');
			}.bind(this));

			// Close
			this.$close.on('click', function(){
				this.mobileOpened = false;
				this.$control.removeClass('active');
				$us.mobileNavOpened--;
				$us.$canvas.trigger('contentChange');
			}.bind(this));

			// Close on ESC key pressed
			$us.$document.keyup(function(e) {
				if (e.keyCode == 27) {
					if (this.mobileOpened) {
						if (this.layout == 'dropdown') {
							this.$list.slideUpCSS(250, this._events.contentChanged);
						}
						this.mobileOpened = false;
						this.$control.removeClass('active');
						$us.mobileNavOpened--;
						$us.$canvas.trigger('contentChange');
					}
				}
			}.bind(this));

			// Bindable events
			this._events = {
				// Mobile submenu togglers
				toggle: function(e){
					if (this.type != 'mobile') return;
					e.stopPropagation();
					e.preventDefault();
					var $item = $(e.currentTarget).closest('.menu-item'),
						$sublist = $item.children('.w-nav-list');
					if ($item.hasClass('opened')) {
						$item.removeClass('opened');
						$sublist.slideUpCSS(250, this._events.contentChanged);
					} else {
						$item.addClass('opened');
						$sublist.slideDownCSS(250, this._events.contentChanged);
					}
				}.bind(this),
				resize: this.resize.bind(this),
				contentChanged: function(){
					if (this.type == 'mobile' && $us.header.orientation == 'hor' && $us.canvas.headerPos == 'fixed') {
						this.setFixedMobileMaxHeight();
					}
					$us.header.$container.trigger('contentChange');
				}.bind(this),
				close: function(){
					if (this.$list != undefined && jQuery.fn.slideUpCSS != undefined && this.mobileOpened && this.type == 'mobile') {
						this.mobileOpened = false;
						if (this.layout == 'dropdown' && this.headerOrientation == 'hor') {
							this.$list.slideUpCSS(250);
						}
						$us.mobileNavOpened--;
						$us.$canvas.trigger('contentChange');
					}
				}.bind(this)
			};

			// Toggle on item clicks
			if (this.options.mobileBehavior) {
				this.$subAnchors.on('click', this._events.toggle);
			}
			// Toggle on arrows
			else {
				this.$list.find('.menu-item-has-children > .w-nav-anchor > .w-nav-arrow').on('click', this._events.toggle);
			}
			// Mark all the togglable items
			this.$subItems.each(function(){
				var $this = $(this),
					$parentItem = $this.parent().closest('.menu-item');
				if ($parentItem.length == 0 || $parentItem.usMod('columns') === false) $this.addClass('togglable');
			});
			// Touch screen handling for desktop type
			if (!$us.$html.hasClass('no-touch')) {
				this.$list.find('.menu-item-has-children.togglable > .w-nav-anchor').on('click', function(e){
					if (this.type == 'mobile') return;
					e.preventDefault();
					var $this = $(e.currentTarget),
						$item = $this.parent(),
						$list = $item.children('.w-nav-list');
					// Second tap: going to the URL
					if ($item.hasClass('opened')) return location.assign($this.attr('href'));
					$item.addClass('opened');
					var outsideClickEvent = function(e){
						if ($.contains($item[0], e.target)) return;
						$item.removeClass('opened');
						$us.$body.off('touchstart', outsideClickEvent);
					};
					$us.$body.on('touchstart', outsideClickEvent);
				}.bind(this));
			}
			// Close on click outside of level 1 menu list
			// TODO: maybe move this to resize logic
			$($us.$document).on('mouseup touchend', function(e) {
				if (this.mobileOpened && this.type == 'mobile') {
					if ( ! this.$control.is(e.target) && this.$control.has(e.target).length === 0 && ! this.$list.is(e.target) && this.$list.has(e.target).length === 0) {
						this.mobileOpened = false;
						this.$control.removeClass('active');
						this.$items.filter('.opened').removeClass('opened');
						this.$subLists.slideUpCSS(250);
						if (this.layout == 'dropdown' && this.headerOrientation == 'hor') {
							this.$list.slideUpCSS(250);
						}
						$us.mobileNavOpened--;
						$us.$canvas.trigger('contentChange');
					}
				}
			}.bind(this));

			// Close menu on anchor clicks
			this.$anchors.on('click', function(e){
				if (this.type != 'mobile' || $us.header.orientation != 'hor') return;
				// Toggled the item
				if (this.options.mobileBehavior && $(e.currentTarget).closest('.menu-item').hasClass('menu-item-has-children')) return;
				this.mobileOpened = false;
				this.$control.removeClass('active');
				if (this.layout == 'dropdown') {
					this.$list.slideUpCSS(250);
				}
				$us.mobileNavOpened--;
				$us.$canvas.trigger('contentChange');
			}.bind(this));

			$us.$window.on('resize', this._events.resize);
			setTimeout(function(){
				this.resize();
				$us.header.$container.trigger('contentChange');
			}.bind(this), 50);
			this.$nav.on('USNavClose', this._events.close);
		},
		/**
		 * Count proper dimensions
		 */
		setFixedMobileMaxHeight: function(){
			var listTop = Math.min(this.$list.position().top, $us.header.scrolledOccupiedHeight);
			this.$list.css('max-height', $us.canvas.winHeight - listTop + 'px');
		},

		/**
		 * Resize handler
		 */
		resize: function(){
			if (this.$nav.length == 0) return;
			var nextType = (window.innerWidth < this.options.mobileWidth) ? 'mobile' : 'desktop';
			if ($us.header.orientation != this.headerOrientation || nextType != this.type) {
				// Clearing the previous state
				this.$subLists.resetInlineCSS('display', 'height');
				if (this.headerOrientation == 'hor' && this.type == 'mobile') {
					this.$list.resetInlineCSS('display', 'height', 'max-height');
				}
				// Closing opened sublists
				this.$items.removeClass('opened');
				this.headerOrientation = $us.header.orientation;
				this.type = nextType;
				this.$nav.usMod('type', nextType);
			}
			// Max-height limitation for fixed header layouts
			if ($us.header.orientation == 'hor' && this.type == 'mobile' && this.layout == 'dropdown' && $us.canvas.headerPos == 'fixed') this.setFixedMobileMaxHeight();
			this.$list.removeClass('hide_for_mobiles');
		}
	};

	// $us.nav = new USNav();

	$.fn.usNav = function(options){
		return this.each(function(){
			$(this).data('usNav', new $us.Nav(this, options));
		});
	};

	$('.l-header .w-nav').usNav();

}(jQuery);


/**
 * $us.scroll
 *
 * ScrollSpy, Smooth scroll links and hash-based scrolling all-in-one
 *
 * @requires $us.canvas
 */
!function($){
	"use strict";

	function USScroll(options){

		// Setting options
		var defaults = {
			/**
			 * @param {String|jQuery} Selector or object of hash scroll anchors that should be attached on init
			 */
			attachOnInit: '.menu-item a[href*="#"], .menu-item[href*="#"], a.w-btn[href*="#"], .w-iconbox a[href*="#"], .w-image a[href*="#"], .w-img a[href*="#"], .w-text a[href*="#"], ' +
			'.vc_icon_element a[href*="#"], .vc_custom_heading a[href*="#"], a.w-portfolio-item-anchor[href*="#"], .w-toplink, ' +
			'.w-blog-post-meta-comments a[href*="#"], .w-comments-title a[href*="#"], .w-comments-item-date, a.smooth-scroll[href*="#"]',
			/**
			 * @param {String} Classname that will be toggled on relevant buttons
			 */
			buttonActiveClass: 'active',
			/**
			 * @param {String} Classname that will be toggled on relevant menu items
			 */
			menuItemActiveClass: 'current-menu-item',
			/**
			 * @param {String} Classname that will be toggled on relevant menu ancestors
			 */
			menuItemAncestorActiveClass: 'current-menu-ancestor',
			/**
			 * @param {Number} Duration of scroll animation
			 */
			animationDuration: 1000,
			/**
			 * @param {String} Easing for scroll animation
			 */
			animationEasing: 'easeInOutExpo'
		};
		this.options = $.extend({}, defaults, options || {});

		// Hash blocks with targets and activity indicators
		this.blocks = {};

		// Is scrolling to some specific block at the moment?
		this.isScrolling = false;

		// Waypoints that will be called at certain scroll position
		this.waypoints = [];

		// Sticky rows
		this.stickyRows = [];//$('.l-section.type_sticky');

		// Boundable events
		this._events = {
			cancel: this.cancel.bind(this),
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this)
		};

		this._canvasTopOffset = 0;
		$us.$window.on('resize load', this._events.resize);
		setTimeout(this._events.resize, 75);

		$us.$window.on('scroll', this._events.scroll);
		setTimeout(this._events.scroll, 75);

		if (this.options.attachOnInit) {
			this.attach(this.options.attachOnInit);
		}

		$('.l-section.type_sticky').each(function(key, row){
			var $row = $(row),
				$rowGap = $row.next('.l-section-gap'),
				stickyRow = {
					$row: $row,
					$rowGap: $rowGap
				};
			this._countStickyRow(stickyRow);
			this.stickyRows.push(stickyRow);
		}.bind(this));

		// Recount scroll positions on any content changes
		$us.$canvas.on('contentChange', this._countAllPositions.bind(this));

		// Handling initial document hash
		if (document.location.hash && document.location.hash.indexOf('#!') == -1) {
			var hash = document.location.hash,
				scrollPlace = (this.blocks[hash] !== undefined) ? hash : undefined;
			if (scrollPlace === undefined) {
				try {
					var $target = $(hash);
					if ($target.length != 0) {
						scrollPlace = $target;
					}
				} catch (error) {
					//Do not have to do anything here since scrollPlace is already undefined
				}

			}
			if (scrollPlace !== undefined) {
				// While page loads, its content changes, and we'll keep the proper scroll on each sufficient content change
				// until the page finishes loading or user scrolls the page manually
				var keepScrollPositionTimer = setInterval(function(){
					this.scrollTo(scrollPlace);
				}.bind(this), 100);
				var clearHashEvents = function(){
					// Content size still may change via other script right after page load
					setTimeout(function(){
						clearInterval(keepScrollPositionTimer);
						$us.canvas.resize();
						this._countAllPositions();
						this.scrollTo(scrollPlace);
					}.bind(this), 100);
					$us.$window.off('load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents);
				}.bind(this);
				$us.$window.on('load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents);
			}
		}
	}

	USScroll.prototype = {

		/**
		 * Count hash's target position and store it properly
		 *
		 * @param {String} hash
		 * @private
		 */
		_countPosition: function(hash){
			var targetTop = this.blocks[hash].target.offset().top;
			if (this.blocks[hash].target.is('.l-section.sticky')) {
				this.blocks[hash].target.removeClass('sticky');
				targetTop = this.blocks[hash].target.offset().top;
				this.blocks[hash].target.addClass('sticky');
			}
			this.blocks[hash].top = Math.ceil(targetTop - this._canvasTopOffset);
			if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && targetTop > $us.header.headerTop)) {
				this.blocks[hash].top = this.blocks[hash].top - $us.header.scrolledOccupiedHeight;
			}
			if ( this.stickyRows[0] !== undefined && window.innerWidth > this.stickyRows[0].disableWidth && targetTop > this.stickyRows[0].originalTop ) {
				this.blocks[hash].top = this.blocks[hash].top - this.stickyRows[0].height;
			}
			this.blocks[hash].bottom = this.blocks[hash].top + this.blocks[hash].target.outerHeight(false);
		},

		/**
		 * Count all targets' positions for proper scrolling
		 *
		 * @private
		 */
		_countAllPositions: function(){
			// Take into account #wpadminbar (and others possible) offset
			this._canvasTopOffset = $us.$canvas.offset().top;
			// Counting stickyRows
			for (var i = 0; i < this.stickyRows.length; i++) {
				this._countStickyRow(this.stickyRows[i]);
			}
			// Counting blocks
			for (var hash in this.blocks) {
				if (!this.blocks.hasOwnProperty(hash)) continue;
				this._countPosition(hash);
			}
			// Counting waypoints
			for (var i = 0; i < this.waypoints.length; i++) {
				this._countWaypoint(this.waypoints[i]);
			}
		},

		/**
		 * Indicate scroll position by hash
		 *
		 * @param {String} activeHash
		 * @private
		 */
		_indicatePosition: function(activeHash){
			var activeMenuAncestors = [];
			for (var hash in this.blocks) {
				if (!this.blocks.hasOwnProperty(hash)) continue;
				if (this.blocks[hash].buttons !== undefined) {
					this.blocks[hash].buttons.toggleClass(this.options.buttonActiveClass, hash === activeHash);
				}
				if (this.blocks[hash].menuItems !== undefined) {
					this.blocks[hash].menuItems.toggleClass(this.options.menuItemActiveClass, hash === activeHash);
				}
				if (this.blocks[hash].menuAncestors !== undefined) {
					this.blocks[hash].menuAncestors.removeClass(this.options.menuItemAncestorActiveClass);
				}
			}
			if (this.blocks[activeHash] !== undefined && this.blocks[activeHash].menuAncestors !== undefined) {
				this.blocks[activeHash].menuAncestors.addClass(this.options.menuItemAncestorActiveClass);
			}
		},

		/**
		 * Attach anchors so their targets will be listened for possible scrolls
		 *
		 * @param {String|jQuery} anchors Selector or list of anchors to attach
		 */
		attach: function(anchors){
			// Location pattern to check absolute URLs for current location
			var locationPattern = new RegExp('^' + location.pathname.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&") + '#');

			var $anchors = $(anchors);
			if ($anchors.length == 0) return;
			$anchors.each(function(index, anchor){
				var $anchor = $(anchor),
					href = $anchor.attr('href'),
					hash = $anchor.prop('hash');
				// Ignoring ajax links
				if (hash.indexOf('#!') != -1) return;
				// Checking if the hash is connected with the current page
				if (!(
						// Link type: #something
						href.charAt(0) == '#' ||
							// Link type: /#something
						(href.charAt(0) == '/' && locationPattern.test(href)) ||
							// Link type: http://example.com/some/path/#something
						href.indexOf(location.host + location.pathname + '#') > -1
					)) return;
				// Do we have an actual target, for which we'll need to count geometry?
				if (hash != '' && hash != '#') {
					// Attach target
					if (this.blocks[hash] === undefined) {
						var $target = $(hash);
						// Don't attach anchors that actually have no target
						if ($target.length == 0) return;
						// If it's the only row in a section, than use section instead
						if ($target.hasClass('g-cols') && $target.parent().children().length == 1) {
							$target = $target.closest('.l-section');
						}
						// If it's tabs or tour item, then use tabs container
						if ($target.hasClass('w-tabs-section')) {
							var $newTarget = $target.closest('.w-tabs');
							if ( ! $newTarget.hasClass('accordion')) {
								$target = $newTarget;
							}
						}
						this.blocks[hash] = {
							target: $target
						};
						this._countPosition(hash);
					}
					// Attach activity indicator
					if ($anchor.parent().length > 0 && $anchor.parent().hasClass('menu-item')) {
						var $menuIndicator = $anchor.closest('.menu-item');
						this.blocks[hash].menuItems = (this.blocks[hash].menuItems || $()).add($menuIndicator);
						var $menuAncestors = $menuIndicator.parents('.menu-item-has-children');
						if ($menuAncestors.length > 0) {
							this.blocks[hash].menuAncestors = (this.blocks[hash].menuAncestors || $()).add($menuAncestors);
						}
					}
					else {
						this.blocks[hash].buttons = (this.blocks[hash].buttons || $()).add($anchor);
					}
				}
				$anchor.on('click', function(event){
					event.preventDefault();
					this.scrollTo(hash, true);
				}.bind(this));
			}.bind(this));
		},

		/**
		 * Scroll page to a certain position or hash
		 *
		 * @param {Number|String|jQuery} place
		 * @param {Boolean} animate
		 */
		scrollTo: function(place, animate){
			var placeType,
				newY;
			// Scroll to top
			if (place == '' || place == '#') {
				newY = 0;
				placeType = 'top';
			}
			// Scroll by hash
			else if (this.blocks[place] !== undefined) {
				newY = this.blocks[place].top;
				placeType = 'hash';
			}
			else if (place instanceof $) {
				if (place.hasClass('w-tabs-section')) {
					var newPlace = place.closest('.w-tabs');
					if ( ! newPlace.hasClass('accordion')) {
						place = newPlace;
					}
				}
				newY = Math.floor(place.offset().top - this._canvasTopOffset);
				if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && place.offset().top > $us.header.headerTop)) {
					newY = newY - $us.header.scrolledOccupiedHeight;
				}
				placeType = 'element';
			}
			else {
				newY = Math.floor(place - this._canvasTopOffset);
				if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && place > $us.header.headerTop)) {
					newY = newY - $us.header.scrolledOccupiedHeight;
				}
			}
			var indicateActive = function(){
				if (placeType == 'hash') {
					this._indicatePosition(place);
				}
				else {
					this.scroll();
				}
			}.bind(this);
			if (animate) {
				this.isScrolling = true;
				$us.$htmlBody.stop(true, false).animate({
					scrollTop: newY + 'px'
				}, {
					duration: this.options.animationDuration,
					easing: this.options.animationEasing,
					always: function(){
						$us.$window.off('keydown mousewheel DOMMouseScroll touchstart', this._events.cancel);
						this.isScrolling = false;
						indicateActive();
					}.bind(this)
				});
				// Allow user to stop scrolling manually
				$us.$window.on('keydown mousewheel DOMMouseScroll touchstart', this._events.cancel);
			}
			else {
				$us.$htmlBody.stop(true, false).scrollTop(newY);
				indicateActive();
			}
		},

		/**
		 * Cancel scroll
		 */
		cancel: function(){
			$us.$htmlBody.stop(true, false);
		},

		/**
		 * Add new waypoint
		 *
		 * @param {jQuery} $elm object with the element
		 * @param {mixed} offset Offset from bottom of screen in pixels ('100') or percents ('20%')
		 * @param {Function} fn The function that will be called
		 */
		addWaypoint: function($elm, offset, fn){
			$elm = ($elm instanceof $) ? $elm : $($elm);
			if ($elm.length == 0) return;
			if (typeof offset != 'string' || offset.indexOf('%') == -1) {
				// Not percent: using pixels
				offset = parseInt(offset);
			}
			var waypoint = {
				$elm: $elm,
				offset: offset,
				fn: fn
			};
			this._countWaypoint(waypoint);
			this.waypoints.push(waypoint);
		},

		/**
		 *
		 * @param {Object} waypoint
		 * @private
		 */
		_countWaypoint: function(waypoint){
			var elmTop = waypoint.$elm.offset().top,
				winHeight = $us.$window.height();
			if (typeof waypoint.offset == 'number') {
				// Offset is defined in pixels
				waypoint.scrollPos = elmTop - winHeight + waypoint.offset;
			} else {
				// Offset is defined in percents
				waypoint.scrollPos = elmTop - winHeight + winHeight * parseInt(waypoint.offset) / 100;
			}
		},

		/**
		 *
		 * @param {Object} stickyRow
		 * @private
		 */
		_countStickyRow: function(stickyRow){
			var isSticky = false;
			if (stickyRow.$row.hasClass('sticky')) {
				isSticky = true;
				stickyRow.$row.removeClass('sticky');
			}
			stickyRow.disableWidth = (stickyRow.$row.data('sticky-disable-width') !== undefined)?stickyRow.$row.data('sticky-disable-width'):900;
			stickyRow.originalTop = stickyRow.$row.offset().top;
			stickyRow.top = stickyRow.$row.offset().top - this._canvasTopOffset;
			if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && stickyRow.top > $us.header.headerTop)) {
				stickyRow.top = stickyRow.top - $us.header.scrolledOccupiedHeight;
			}
			stickyRow.height = stickyRow.$row.outerHeight();
			if (stickyRow.$row.is('.l-main .l-section:first-child')) {
				stickyRow.height = stickyRow.height - parseInt(stickyRow.$row.css('padding-top'));
			}
			if (isSticky) {
				stickyRow.$row.addClass('sticky');
			}
		},

		/**
		 * Scroll handler
		 */
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());
			if (!this.isScrolling) {
				var activeHash;
				for (var hash in this.blocks) {
					if (!this.blocks.hasOwnProperty(hash)) continue;
					if (scrollTop >= (this.blocks[hash].top - 1) && scrollTop < (this.blocks[hash].bottom - 1)) {
						activeHash = hash;
						break;
					}
				}
				this._indicatePosition(activeHash);
			}
			// Handling sticky rows
			for (var i = 0; i < this.stickyRows.length; i++) {
				if (this.stickyRows[i].top < scrollTop && window.innerWidth > this.stickyRows[i].disableWidth) {
					this.stickyRows[i].$row.addClass('sticky');
					this.stickyRows[i].$rowGap.css('height', this.stickyRows[i].height);
				} else {
					this.stickyRows[i].$row.removeClass('sticky');
					this.stickyRows[i].$rowGap.css('height', null);
				}
			}
			// Handling waypoints
			for (var i = 0; i < this.waypoints.length; i++) {
				if (this.waypoints[i].scrollPos < scrollTop) {
					this.waypoints[i].fn(this.waypoints[i].$elm);
					this.waypoints.splice(i, 1);
					i--;
				}
			}
		},

		/**
		 * Resize handler
		 */
		resize: function(){
			// Delaying the resize event to prevent glitches
			setTimeout(function(){
				this._countAllPositions();
				this.scroll();
			}.bind(this), 150);
			this._countAllPositions();
			this.scroll();
		}
	};

	$(function(){
		$us.scroll = new USScroll($us.scrollOptions || {});
	});

}(jQuery);


jQuery(function($){
	"use strict";

	if ($('a[ref=magnificPopup][class!=direct-link]').length != 0) {
		$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.magnific-popup.js', function(){
			$('a[ref=magnificPopup][class!=direct-link]').magnificPopup({
				type: 'image',
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: false
			});
		});
	}

	$('.animate_fade, .animate_afc, .animate_afl, .animate_afr, .animate_aft, .animate_afb, .animate_wfc, ' +
		'.animate_hfc, .animate_rfc, .animate_rfl, .animate_rfr').each(function(){
		$us.scroll.addWaypoint($(this), '15%', function($elm){
			if (!$elm.hasClass('animate_start')) {
				setTimeout(function(){
					$elm.addClass('animate_start');
				}, 20);
			}
		});
	});
	$('.wpb_animate_when_almost_visible').each(function(){
		$us.scroll.addWaypoint($(this), '15%', function($elm){
			if (!$elm.hasClass('wpb_start_animation')) {
				setTimeout(function(){
					$elm.addClass('wpb_start_animation');
				}, 20);
			}
		});
	});

	jQuery('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"], input[type="search"], input[type="url"], input[type="password"], textarea').each(function(index, input){
		var $input = $(input),
			$row = $input.closest('.w-form-row');
		if ($input.attr('type') == 'hidden') return;
		$row.toggleClass('not-empty', $input.val() != '');
		$input.on('input', function(){
			$row.toggleClass('not-empty', $input.val() != '');
		});
	});

	jQuery('.l-section-img, .l-titlebar-img').each(function(){
		var $this = $(this),
			img = new Image();

		img.onload = function(){
			if (!$this.hasClass('loaded')) {
				$this.addClass('loaded')
			}
		};

		img.src = ($this.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
	});

	/* Ultimate Addons for Visual Composer integration */
	jQuery('.upb_bg_img, .upb_color, .upb_grad, .upb_content_iframe, .upb_content_video, .upb_no_bg').each(function(){
		var $bg = jQuery(this),
			$prev = $bg.prev();

		if ($prev.length == 0) {
			var $parent = $bg.parent(),
				$parentParent = $parent.parent(),
				$prevParentParent = $parentParent.prev();

			if ($prevParentParent.length) {
				$bg.insertAfter($prevParentParent);

				if ($parent.children().length == 0) {
					$parentParent.remove();
				}
			}
		}
	});
	$('.g-cols > .ult-item-wrap').each(function(index, elm){
		var $elm = jQuery(elm);
		$elm.replaceWith($elm.children());
	});
	jQuery('.overlay-show').click(function(){
		window.setTimeout(function(){
			$us.$canvas.trigger('contentChange');
		}, 1000);
	});

});

/**
 * CSS-analog of jQuery slideDown/slideUp/fadeIn/fadeOut functions (for better rendering)
 */
!function(){

	/**
	 * Remove the passed inline CSS attributes.
	 *
	 * Usage: $elm.resetInlineCSS('height', 'width');
	 */
	jQuery.fn.resetInlineCSS = function(){
		for (var index = 0; index < arguments.length; index++) {
			this.css(arguments[index], '');
		}
		return this;
	};

	jQuery.fn.clearPreviousTransitions = function(){
		// Stopping previous events, if there were any
		var prevTimers = (this.data('animation-timers') || '').split(',');
		if (prevTimers.length >= 2) {
			this.resetInlineCSS('transition', '-webkit-transition');
			prevTimers.map(clearTimeout);
			this.removeData('animation-timers');
		}
		return this;
	};
	/**
	 *
	 * @param {Object} css key-value pairs of animated css
	 * @param {Number} duration in milliseconds
	 * @param {Function} onFinish
	 * @param {String} easing CSS easing name
	 * @param {Number} delay in milliseconds
	 */
	jQuery.fn.performCSSTransition = function(css, duration, onFinish, easing, delay){
		duration = duration || 250;
		delay = delay || 25;
		easing = easing || 'ease';
		var $this = this,
			transition = [];

		this.clearPreviousTransitions();

		for (var attr in css) {
			if (!css.hasOwnProperty(attr)) continue;
			transition.push(attr + ' ' + (duration / 1000) + 's ' + easing);
		}
		transition = transition.join(', ');
		$this.css({
			transition: transition,
			'-webkit-transition': transition
		});

		// Starting the transition with a slight delay for the proper application of CSS transition properties
		var timer1 = setTimeout(function(){
			$this.css(css);
		}, delay);

		var timer2 = setTimeout(function(){
			$this.resetInlineCSS('transition', '-webkit-transition');
			if (typeof onFinish == 'function') onFinish();
		}, duration + delay);

		this.data('animation-timers', timer1 + ',' + timer2);
	};
	// Height animations
	jQuery.fn.slideDownCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		var $this = this;
		this.clearPreviousTransitions();
		// Grabbing paddings
		this.resetInlineCSS('padding-top', 'padding-bottom');
		var timer1 = setTimeout(function(){
			var paddingTop = parseInt($this.css('padding-top')),
				paddingBottom = parseInt($this.css('padding-bottom'));
			// Grabbing the "auto" height in px
			$this.css({
				visibility: 'hidden',
				position: 'absolute',
				height: 'auto',
				'padding-top': 0,
				'padding-bottom': 0,
				display: 'block'
			});
			var height = $this.height();
			$this.css({
				overflow: 'hidden',
				height: '0px',
				visibility: '',
				position: ''
			});
			$this.performCSSTransition({
				height: height + paddingTop + paddingBottom,
				'padding-top': paddingTop,
				'padding-bottom': paddingBottom
			}, duration, function(){
				$this.resetInlineCSS('overflow').css('height', 'auto');
				if (typeof onFinish == 'function') onFinish();
			}, easing, delay);
		}, 25);
		this.data('animation-timers', timer1 + ',null');
	};
	jQuery.fn.slideUpCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		this.css({
			height: this.outerHeight(),
			overflow: 'hidden',
			'padding-top': this.css('padding-top'),
			'padding-bottom': this.css('padding-bottom')
		});
		var $this = this;
		this.performCSSTransition({
			height: 0,
			'padding-top': 0,
			'padding-bottom': 0
		}, duration, function(){
			$this.resetInlineCSS('overflow', 'padding-top', 'padding-bottom').css({
				display: 'none'
			});
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
	// Opacity animations
	jQuery.fn.fadeInCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		this.css({
			opacity: 0,
			display: 'block'
		});
		this.performCSSTransition({
			opacity: 1
		}, duration, onFinish, easing, delay);
	};
	jQuery.fn.fadeOutCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		var $this = this;
		this.performCSSTransition({
			opacity: 0
		}, duration, function(){
			$this.css('display', 'none');
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
}();

/**
 * UpSolution Page Scroller
 */
(function($){
	"use strict";

	$us.PageScroller = function(container, options){
		this.init(container, options);
	};

	$us.PageScroller.prototype = {
		init: function(container, options){
			var defaults = {
					coolDown: 100,
					animationDuration: 1000,
					animationEasing: 'easeInOutExpo'
				},
				scrollTop = parseInt($us.$window.scrollTop());

			this.options = $.extend({}, defaults, options);

			this.$container = $(container);
			this._canvasTopOffset = $us.$canvas.offset().top;
			this.activeSection = 0;
			this.sections = [];
			this.dots = [];
			this.scrolls = [];
			this.usingDots = false;
			this.isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0) || (navigator.maxTouchPoints));
			this.disableWidth = (this.$container.data('disablewidth') !== undefined)?this.$container.data('disablewidth'):768;

			if (this.$container.data('speed') !== undefined) {
				this.options.animationDuration = this.$container.data('speed');
			}

			// Adding canvas sections
			$us.$canvas.find('.l-section').each(function(key, elm){
				var $section = $(elm),
					section = {
						$section: $section
					};
				this._countPosition(section);
				this.sections.push(section);
			}.bind(this));

			// Adding dots for canvas sections
			this.$dotsContainer = this.$container.find('.w-scroller-dots');
			if ( this.$dotsContainer.length ) {
				this.usingDots = true;

				this.$firstDot = this.$dotsContainer.find('.w-scroller-dot').first();
				for (var i = 1; i < this.sections.length; i++) {
					this.$firstDot.clone().appendTo(this.$dotsContainer);
				}

				this.$dots = this.$dotsContainer.find('.w-scroller-dot');
				this.$dots.each(function(key, elm){
					var $dot = $(elm);
					this.dots[key] = $dot;
					$dot.click(function(){
						this.scrollTo(key);
						this.$dots.removeClass('active');
						$dot.addClass('active');
					}.bind(this));
				}.bind(this));

				this.dots[this.activeSection].addClass('active');

				this.$dotsContainer.addClass('show');
			}

			// Adding footer sections
			$('.l-footer > .l-section').each(function(key, elm){
				var $section = $(elm),
					section = {
						$section: $section
					};
				this._countPosition(section);
				this.sections.push(section);
			}.bind(this));

			this._attachEvents();

			// Boundable events
			this._events = {
				scroll: this.scroll.bind(this),
				resize: this.resize.bind(this)
			};

			$us.$canvas.on('contentChange', this._events.resize);
			$us.$window.on('resize load', this._events.resize);
			$us.$window.on('resize load scroll', this._events.scroll);
			setTimeout(this._events.resize, 100);
		},
		getScrollSpeed: function(number){
			var sum = 0;
			var lastElements = this.scrolls.slice(Math.max(this.scrolls.length - number, 1));

			for(var i = 0; i < lastElements.length; i++){
				sum = sum + lastElements[i];
			}

			return Math.ceil(sum/number);
		},
		_attachEvents: function(){

			$us.$document.off('mousewheel DOMMouseScroll MozMousePixelScroll');
			$us.$canvas.off('touchstart touchmove');
			
			if ($us.$window.width() > this.disableWidth && $us.mobileNavOpened <= 0 && ( ! $us.$html.hasClass('cloverlay_fixed'))) {
				$us.$document.on('mousewheel DOMMouseScroll MozMousePixelScroll', function(e) {
					e.preventDefault();
					var currentTime = new Date().getTime(),
						target = this.activeSection,
						direction = e.originalEvent.wheelDelta || -e.originalEvent.detail,
						speedEnd, speedMiddle, isAccelerating;


					if(this.scrolls.length > 149){
						this.scrolls.shift();
					}
					this.scrolls.push(Math.abs(direction));

					if ((currentTime - this.previousMouseWheelTime) > this.options.coolDown) {
						this.scrolls = [];
					}
					this.previousMouseWheelTime = currentTime;

					speedEnd = this.getScrollSpeed(10);
					speedMiddle = this.getScrollSpeed(70);
					isAccelerating = speedEnd >= speedMiddle;

					if (isAccelerating) {
						if (direction < 0) {
							target++;
						} else if (direction > 0) {
							target--;
						}
						if (this.sections[target] == undefined) {
							return;
						}
						this.scrollTo(target);
						this.lastScroll = currentTime;
					}

				}.bind(this));

				if ( $.isMobile || this.isTouch ) {
					$us.$canvas.on('touchstart', function(event){
						var e = event.originalEvent;
						if ( typeof e.pointerType === 'undefined' || e.pointerType != 'mouse' ) {
							this.touchStartY = e.touches[0].pageY;
						}
					}.bind(this));

					$us.$canvas.on('touchmove', function(event){
						event.preventDefault();

						var currentTime = new Date().getTime(),
							e = event.originalEvent,
							target = this.activeSection;
						this.touchEndY = e.touches[0].pageY;

						if (Math.abs(this.touchStartY - this.touchEndY) > ($us.$window.height() / 50)) {
							if (this.touchStartY > this.touchEndY) {
								target++;
							} else if (this.touchEndY > this.touchStartY) {
								target--;
							}

							if (this.sections[target] == undefined) {
								return;
							}
							this.scrollTo(target);
							this.lastScroll = currentTime;
						}
					}.bind(this));
				}
			}

		},
		_countPosition: function(section){
			section.top = section.$section.offset().top - this._canvasTopOffset;
			if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && section.top > $us.header.headerTop)) {
				section.top = section.top - $us.header.scrolledOccupiedHeight;
			}
			section.bottom = section.top + section.$section.outerHeight(false);
		},
		_countAllPositions: function(){
			for (var section in this.sections) {
				if (this.sections[section].$section.length) {
					this._countPosition(this.sections[section]);
				}
			}
		},
		scrollTo: function(target){
			var currentTime = new Date().getTime();
			if(this.previousScrollTime !== undefined && (currentTime - this.previousScrollTime < this.options.animationDuration)) {
				return;
			}
			this.previousScrollTime = currentTime;

			if (this.usingDots) {
				this.$dots.removeClass('active');
				if (this.dots[target] !== undefined) {
					this.dots[target].addClass('active');
				}
			}

			$us.$htmlBody.stop(true, false).animate({
				scrollTop: this.sections[target]['top'] + 'px'
			}, {
				duration: this.options.animationDuration,
				easing: this.options.animationEasing,
				always: function(){
					this.activeSection = target;
				}.bind(this)
			});
		},
		resize: function(){
			this._attachEvents();

			// Delaying the resize event to prevent glitches
			setTimeout(function(){
				this._countAllPositions();
				// this.scrollTo(this.activeSection);
			}.bind(this), 150);
			this._countAllPositions();
			// this.scrollTo(this.activeSection);
		},
		scroll: function(){
			var currentTime = new Date().getTime();
			if ((currentTime - this.lastScroll) < (this.options.coolDown+this.options.animationDuration)) {
				return;
			}
			if (this.scrollTimeout) {
				clearTimeout(this.scrollTimeout);
			}
			this.scrollTimeout = setTimeout(function () {
				var scrollTop = parseInt($us.$window.scrollTop());

				for (var section in this.sections) {
					if (scrollTop >= (this.sections[section].top - 1) && scrollTop < (this.sections[section].bottom - 1)) {
						this.activeSection = section;
						break;
					}
				}
				if (this.usingDots) {
					this.$dots.removeClass('active');
					if (this.dots[this.activeSection] !== undefined) {
						this.dots[this.activeSection].addClass('active');
					}
				}
			}.bind(this), 500);
		}
	};

	$.fn.usPageScroller = function(options){
		return this.each(function(){
			$(this).data('usPageScroller', new $us.PageScroller(this, options));
		});
	};

	$(function(){
		$('.w-scroller').usPageScroller();
	});
})(jQuery);

/**
 * UpSolution Shortcode: us_message
 */
(function($){
	"use strict";

	$.fn.usMessage = function(){
		return this.each(function(){
			var $this = $(this),
				$closer = $this.find('.w-message-close');
			$closer.click(function(){
				$this.wrap('<div></div>');
				var $wrapper = $this.parent();
				$wrapper.css({overflow: 'hidden', height: $this.outerHeight(true)});
				$wrapper.performCSSTransition({
					height: 0
				}, 300, function(){
					$wrapper.remove();
					$us.$canvas.trigger('contentChange');
				}, 'cubic-bezier(.4,0,.2,1)');
			});
		});
	};

	$(function(){
		$('.w-message').usMessage();
	});
})(jQuery);


/**
 * Focus for different kind of forms
 */
jQuery(function($){
	$(document).on('focus', '.w-form-row-field input, .w-form-row-field textarea', function(){
		$(this).closest('.w-form-row').addClass('focused');
	});
	$(document).on('blur', '.w-form-row-field input, .w-form-row-field textarea', function(){
		$(this).closest('.w-form-row').removeClass('focused');
	});
});


/**
 * UpSolution Widget: w-dropdown
 */
(function($){
	"use strict";
	$.fn.wDropdown = function(){
		return this.each(function(){
			var $this = $(this),
				$list = $this.find('.w-dropdown-list'),
				$current = $this.find('.w-dropdown-current');
			var closeList = function(){
				$list.slideUpCSS(250, function(){
					$this.removeClass('active');
				});
				$us.$window.off('mouseup touchstart mousewheel DOMMouseScroll touchstart', closeListEvent);
			};
			var closeListEvent = function(e){
				if ($this.has(e.target).length !== 0) return;
				e.stopPropagation();
				e.preventDefault();
				closeList();
			};
			$list.hide();
			$current.click(function(){
				if ($this.hasClass('active')) {
					closeList();
					return;
				}
				$this.addClass('active');
				$list.slideDownCSS();
				$us.$window.on('mouseup touchstart mousewheel DOMMouseScroll touchstart', closeListEvent);
			});
		});
	};
	$(function(){
		$('.w-dropdown').wDropdown();
	});
})(jQuery);


/**
 * UpSolution Widget: w-blog
 */
(function($){
	"use strict";

	$us.WBlog = function(container, options){
		this.init(container, options);
	};

	$us.WBlog.prototype = {

		init: function(container, options){
			// Commonly used dom elements
			this.$container = $(container);
			this.$filters = this.$container.find('.g-filters-item');
			this.$list = this.$container.find('.w-blog-list');
			this.$items = this.$container.find('.w-blog-post');
			this.$pagination = this.$container.find('.g-pagination');
			this.$loadmore = this.$container.find('.g-loadmore');
			this.$preloader = this.$container.find('.w-blog-preloader');
			this.curCategory = '';
			this.paginationType = this.$pagination.length ? 'regular' : (this.$loadmore.length ? 'ajax' : 'none');
			this.items = [];
			this.loading = false;

			if (this.$list.hasClass('owl-carousel')) {
				$us.getScript($us.templateDirectoryUri+'/framework/js/owl.carousel.min.js', function() {
					var items = parseInt(this.$list.data('items')),
						responsive = {};
					responsive[0] = {items: Math.min(items, this.$list.data('breakpoint_3_cols')), autoHeight: true, autoplay: false};
					responsive[this.$list.data('breakpoint_3_width')] = {items: Math.min(items, this.$list.data('breakpoint_2_cols'))};
					responsive[this.$list.data('breakpoint_2_width')] = {items: Math.min(items, this.$list.data('breakpoint_1_cols'))};
					responsive[this.$list.data('breakpoint_1_width')] = {items: items};
					this.$list.owlCarousel({
						mouseDrag: ! jQuery.isMobile,
						items: items,
						loop: true,
						rtl: $('.l-body').hasClass('rtl'),
						nav: this.$list.data('nav'),
						dots: this.$list.data('dots'),
						center: this.$list.data('center'),
						autoplay: this.$list.data('autoplay'),
						autoplayTimeout: this.$list.data('timeout'),
						autoHeight: this.$list.data('autoheight'),
						slideBy: this.$list.data('slideby'),
						autoplayHoverPause: true,
						responsive: responsive
					});
				}.bind(this));

				return;
			}

			if (this.paginationType != 'none') {
				var $jsonContainer = this.$container.find('.w-blog-json');
				if ($jsonContainer.length == 0) return;
				this.ajaxData = $jsonContainer[0].onclick() || {};
				this.ajaxUrl = this.ajaxData.ajax_url || '';
				this.permalinkUrl = this.ajaxData.permalink_url || '';
				this.templateVars = this.ajaxData.template_vars || {};
				this.category = this.templateVars.query_args.category_name || '';
				this.curCategory = this.category;
				this.curPage = this.ajaxData.current_page || 1;
				this.perpage = this.ajaxData.perpage || this.$items.length;
				this.infiniteScroll = this.ajaxData.infinite_scroll || 0;
				$jsonContainer.remove();

			}


			else if (this.paginationType == 'regular' && this.$filters.length) {
				this.paginationPcre = new RegExp('/page/([0-9]+)/$');
				this.location = location.href.replace(this.paginationPcre, '/');
				this.$navLinks = this.$container.find('.nav-links');
				var self = this;
				this.$navLinks.on('click', 'a', function(e){
					e.preventDefault();
					var arr,
						pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
					self.setState(pageNum);
				});
			}

			if (this.$container.hasClass('with_isotope')) {
				$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.isotope.js', function(){
					this.$list.imagesLoaded(function(){
						this.$list.isotope({
							itemSelector: '.w-blog-post',
							layoutMode: (this.$container.hasClass('isotope_fit_rows')) ? 'fitRows' : 'masonry',
							isOriginLeft: !$('.l-body').hasClass('rtl')
						});
						this.$list.isotope();

						if (this.paginationType == 'ajax') {
							this.initAjaxPagination();
						}
					}.bind(this));

					$us.$canvas.on('contentChange', function(){
						this.$list.imagesLoaded(function(){
							this.$list.isotope('layout');
						}.bind(this));
					}.bind(this));
				}.bind(this));
			} else if (this.paginationType == 'ajax') {
				this.initAjaxPagination();
			}

			this.$filters.each(function(index, filter){
				var $filter = $(filter),
					category = $filter.data('category');
				$filter.on('click', function(){
					if (category != this.curCategory) {
						this.setState(1, category);
						this.$filters.removeClass('active');
						$filter.addClass('active');
					}
				}.bind(this))
			}.bind(this));
		},

		initAjaxPagination: function(){
			if (this.templateVars.query_args.orderby == 'rand') {
				this.$items.each(function(index, item){
					this.items.push(parseInt(item.getAttribute('data-id')));
				}.bind(this));
			}
			this.$loadmore.on('click', function(){
				if (this.curPage < this.ajaxData.max_num_pages) {
					this.setState(this.curPage + 1);
				}
			}.bind(this));

			if (this.infiniteScroll) {
				$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
					this.$loadmore.click();
				}.bind(this));
			}
		},

		setState: function(page, category){
			if (this.paginationType == 'none') {
				// Simple state changer
				this.$list.isotope({filter: (category == '*') ? '*' : ('.' + category)});
				this.curCategory = category;
				return;
			}

			if (this.loading) return;

			this.loading = true;

			category = category || this.curCategory;
			if (category == '*') {
				category = this.category;
			}

			this.templateVars.query_args.paged = page;
			this.templateVars.query_args.category_name = category;

			if (this.paginationType == 'ajax') {
				if (page == 1) {
					this.items = [];
					this.templateVars.query_args.post__not_in = this.items;
					this.$loadmore.addClass('done');
				} else {
					if (this.templateVars.query_args.orderby == 'rand') {
						this.templateVars.query_args.paged = 1;
						this.templateVars.query_args.post__not_in = this.items;
					}
					this.$loadmore.addClass('loading');
				}
			}

			if (this.paginationType != 'ajax' || page == 1) {
				this.$preloader.addClass('active');
				if (this.$list.data('isotope')) {
					this.$list.isotope('remove', this.$container.find('.w-blog-post'));
					this.$list.isotope('layout');
				} else {
					this.$container.find('.w-blog-post').remove();
				}
			}

			this.ajaxData.template_vars = JSON.stringify(this.templateVars);

			// In case we set paged to 1 for rand order - setting it back
			this.templateVars.query_args.paged = page;

			$.ajax({
				type: 'post',
				url: this.ajaxData.ajax_url,
				data: this.ajaxData,
				success: function(html){
					var $result = $(html),
						$container = $result.find('.w-blog-list'),
						$items = $container.children(),
						isotope = this.$list.data('isotope');
					$container.imagesLoaded(function(){
						this.beforeAppendItems($items);
						$items.appendTo(this.$list);
						$container.remove();
						var $sliders = $items.find('.w-slider');
						this.afterAppendItems($items);
						if (isotope) {
							isotope.appended($items);
						}
						if ($sliders.length) {
							$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.royalslider.min.js', function(){
								$sliders.each(function(index, slider){
									$(slider).wSlider().find('.royalSlider').data('royalSlider').ev.on('rsAfterInit', function(){
										if (isotope) {
											this.$list.isotope('layout');
										}
									});
								}.bind(this));

							}.bind(this));
						}

						if (isotope) {
							this.$list.isotope('layout');
						}
						if (this.paginationType == 'regular') {
							this.$pagination.remove();

							var $pagination = $result.find('.g-pagination');

							this.$container.append($pagination);
							this.$pagination = this.$container.find('.g-pagination');

							var self = this;
							this.$pagination.find('.nav-links a').each(function(){
								var $link = $(this),
									linkURL = $link.attr('href');
								linkURL = linkURL.replace(self.ajaxUrl, self.permalinkUrl);
								$link.attr('href', linkURL);
							});

							this.paginationPcre = new RegExp('/page/([0-9]+)/$');
							this.location = location.href.replace(this.paginationPcre, '/');
							this.$navLinks = this.$container.find('.nav-links');

							this.$navLinks.on('click', 'a', function(e){
								e.preventDefault();
								var arr,
									pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
								self.setState(pageNum);
							});

						}
						if (this.paginationType == 'ajax') {
							if (page == 1) {
								var $jsonContainer = $result.find('.w-blog-json');
								if ($jsonContainer.length) {
									var ajaxData = $jsonContainer[0].onclick() || {};
									this.ajaxData.max_num_pages = ajaxData.max_num_pages || this.ajaxData.max_num_pages;
								} else {
									this.ajaxData.max_num_pages = 1;
								}
							}

							if (this.templateVars.query_args.orderby == 'rand') {
								$items.each(function(index, item){
									this.items.push(parseInt(item.getAttribute('data-id')));
								}.bind(this));
							}

							if (this.templateVars.query_args.paged >= this.ajaxData.max_num_pages) {
								this.$loadmore.addClass('done');
							} else {
								this.$loadmore.removeClass('done');
								this.$loadmore.removeClass('loading');
							}

							if (this.infiniteScroll) {
								$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
									this.$loadmore.click();
								}.bind(this));
							}
						}
						this.$preloader.removeClass('active');
					}.bind(this));

					this.loading = false;

				}.bind(this),
				error: function(){
					this.$loadmore.removeClass('loading');
				}.bind(this)
			});


			this.curPage = page;
			this.curCategory = category;

		},
		/**
		 * Overloadable function for themes
		 * @param $items
		 */
		beforeAppendItems: function($items){
		},

		afterAppendItems: function($items){
		}

	};

	$.fn.wBlog = function(options){
		return this.each(function(){
			$(this).data('wBlog', new $us.WBlog(this, options));
		});
	};
})(jQuery);


/**
 * UpSolution Widget: w-tabs
 *
 * @requires $us.canvas
 */
!function($){
	"use strict";

	$us.WTabs = function(container, options){
		this.init(container, options);
	};

	$us.WTabs.prototype = {

		init: function(container, options){
			// Setting options
			var defaults = {
				duration: 300,
				easing: 'cubic-bezier(.78,.13,.15,.86)'
			};
			this.options = $.extend({}, defaults, options);
			this.isRtl = $('.l-body').hasClass('rtl');

			// Commonly used dom elements
			this.$container = $(container);
			this.$tabsList = this.$container.find('.w-tabs-list:first');
			this.$tabs = this.$tabsList.find('.w-tabs-item');
			this.$tabsH = this.$tabsList.find('.w-tabs-item-h');
			this.$sectionsWrapper = this.$container.find('.w-tabs-sections:first');
			this.$sectionsHelper = this.$sectionsWrapper.children();
			this.$sections = this.$sectionsHelper.find('.w-tabs-section');
			this.$headers = this.$sections.children('.w-tabs-section-header');
			this.$contents = this.$sections.children('.w-tabs-section-content');
			this.$line_charts = this.$container.find(".vc_line-chart");
			this.$round_charts = this.$container.find(".vc_round-chart");

			// Class variables
			this.width = 0;
			this.tabWidths = [];
			this.isTogglable = (this.$container.usMod('type') == 'togglable');
			// Basic layout
			this.basicLayout = this.$container.hasClass('accordion') ? 'accordion' : (this.$container.usMod('layout') || 'default');
			// Current active layout (may be switched to 'accordion')
			this.curLayout = this.basicLayout;
			this.responsive = $us.canvas.options.responsive;
			// Array of active tabs indexes
			this.active = [];
			this.activeOnInit = [];
			this.definedActive = [];
			this.count = this.$tabs.length;
			// Container width at which we should switch to accordion layout
			this.minWidth = 0;

			if (this.count == 0) return;

			// Preparing arrays of jQuery objects for easier manipulating in future
			this.tabs = $.map(this.$tabs.toArray(), $);
			this.sections = $.map(this.$sections.toArray(), $);
			this.headers = $.map(this.$headers.toArray(), $);
			this.contents = $.map(this.$contents.toArray(), $);

			$.each(this.tabs, function(index){
				if (this.tabs[index].hasClass('active')) {
					this.active.push(index);
					this.activeOnInit.push(index);
				}
				if (this.tabs[index].hasClass('defined-active')) {
					this.definedActive.push(index);
				}
				this.tabs[index].add(this.headers[index]).on('click', function(e){
					e.preventDefault();
					// Toggling accordion sections
					if (this.curLayout == 'accordion' && this.isTogglable) {
						// Cannot toggle the only active item
						this.toggleSection(index);
					}
					// Setting tabs active item
					else {
						if (index != this.active[0]) {
							this.headerClicked = true;
							this.openSection(index);
						} else if ( this.curLayout == 'accordion' ) {
							this.contents[index].css('display', 'block').slideUp(this.options.duration, this._events.contentChanged);
							this.tabs[index].removeClass('active');
							this.sections[index].removeClass('active');
							this.active[0] = undefined;
						}
					}
				}.bind(this));
			}.bind(this));

			this.$tabsH.on('click', function(e){
				e.preventDefault();
			});

			// Boundable events
			this._events = {
				resize: this.resize.bind(this),
				contentChanged: function(){
					$us.$canvas.trigger('contentChange');
					this.$line_charts.length&&jQuery.fn.vcLineChart&&this.$line_charts.vcLineChart({reload:!1});// TODO: check if we can do this without hardcoding line charts init here;
					this.$round_charts.length&&jQuery.fn.vcRoundChart&&this.$round_charts.vcRoundChart({reload:!1});// TODO: check if we can do this without hardcoding line charts init here;
				}.bind(this)
			};

			// Starting everything
			this.switchLayout(this.curLayout);


			$us.$window.on('resize', this._events.resize);

			$us.$document.on('ready', this._events.resize);

			$us.$document.on('ready', function(){
				setTimeout(this._events.resize, 50);

				setTimeout(function(){
				// Open tab on page load by hash
					if (window.location.hash) {
						var hash = window.location.hash.substr(1),
							$linkedSection = this.$container.find('.w-tabs-section[id="' + hash + '"]');
						if ($linkedSection.length && ( !$linkedSection.hasClass('active'))) {
							var $header = $linkedSection.find('.w-tabs-section-header');
							$header.click();
						}
					}
				}.bind(this), 150);
			}.bind(this));

			// Support for external links to tabs
			$.each(this.tabs, function(index){
				if (this.headers[index].attr('href') != undefined) {
					var tabHref = this.headers[index].attr('href'),
						tabHeader = this.headers[index];
					$('a[href="' + tabHref + '"]').on('click', function(e){
						e.preventDefault();
						if ($(this).hasClass('w-tabs-section-header', 'w-tabs-item-h')) {
							return;
						}
						tabHeader.click();
					});
				}
			}.bind(this));

			this.$container.addClass('initialized');
		},

		switchLayout: function(to){
			this.cleanUpLayout(this.curLayout);
			this.prepareLayout(to);
			this.curLayout = to;
		},

		/**
		 * Clean up layout's special inline styles and/or dom elements
		 * @param from
		 */
		cleanUpLayout: function(from){
			if (from == 'default' || from == 'timeline' || from == 'modern' || from == 'trendy') {
				this.$sectionsWrapper.clearPreviousTransitions().resetInlineCSS('width', 'height');
				this.$sectionsHelper.clearPreviousTransitions().resetInlineCSS('position', 'width', 'left');
				this.$sections.resetInlineCSS('width');
				this.$container.removeClass('autoresize');
			}
			else if (from == 'accordion') {
				this.$container.removeClass('accordion');
				this.$contents.resetInlineCSS('height', 'padding-top', 'padding-bottom', 'display', 'opacity');
			}
			else if (from == 'ver') {
				this.$contents.resetInlineCSS('height', 'padding-top', 'padding-bottom', 'display', 'opacity');
			}
		},

		/**
		 * Apply layout's special inline styles and/or dom elements
		 * @param to
		 */
		prepareLayout: function(to){
			if (to != 'accordion' && this.active[0] == undefined) {
				this.active[0] = this.activeOnInit[0];
				if (this.active[0] != undefined) {
					this.tabs[this.active[0]].addClass('active');
					this.sections[this.active[0]].addClass('active');
				}
			}

			if (to == 'default' || to == 'timeline' || to == 'modern' || to == 'trendy') {
				this.$container.addClass('autoresize');
				this.$sectionsHelper.css('position', 'absolute');
			}
			else if (to == 'accordion') {
				this.$container.addClass('accordion');
				this.$contents.hide();
				if (this.curLayout != 'accordion' && this.active[0] != undefined && this.active[0] != this.definedActive[0]) {
					this.tabs[this.active[0]].removeClass('active');
					this.sections[this.active[0]].removeClass('active');
					this.active[0] = this.definedActive[0];

				}
				for (var i = 0; i < this.active.length; i++) {
					if (this.contents[this.active[i]] !== undefined) {
						this.tabs[this.active[i]].addClass('active');
						this.sections[this.active[i]].addClass('active');
						this.contents[this.active[i]].show();
					}
				}

			}
			else if (to == 'ver') {
				this.$contents.hide();
				this.contents[this.active[0]].show();
			}


		},

		/**
		 * Measure needed sizes and store them to this.tabWidths variable
		 *
		 * TODO Count minWidth here as well
		 */
		measure: function(){
			if (this.basicLayout == 'ver') {
				// Measuring minimum tabs width
				this.$tabsList.css('width', 0);
				var minTabWidth = this.$tabsList.outerWidth(true);
				this.$tabsList.css('width', '');
				// Measuring the mininum content width
				this.$container.addClass('measure');
				var minContentWidth = this.$sectionsWrapper.outerWidth(true);
				this.$container.removeClass('measure');
				// Measuring minimum tabs width for percent-based sizes
				var navWidth = this.$container.usMod('navwidth');
				if (navWidth != 'auto') {
					// Percent-based measure
					minTabWidth = Math.max(minTabWidth, minContentWidth * parseInt(navWidth) / (100 - parseInt(navWidth)));
				}
				this.minWidth = Math.max(480, minContentWidth + minTabWidth + 1);
			} else {
				this.tabWidths = [];
				// We hide active line temporarily to count tab sizes properly
				this.$container.addClass('measure');
				for (var index = 0; index < this.tabs.length; index++) {
					this.tabWidths.push(this.tabs[index].outerWidth(true));
				}
				this.$container.removeClass('measure');
				if (this.basicLayout == 'default' || this.basicLayout == 'timeline' || this.basicLayout == 'modern' || this.basicLayout == 'trendy') {
					// Array sum
					this.minWidth = this.tabWidths.reduce(function(pv, cv){
						return pv + cv;
					}, 0);
				}
			}
		},

		/**
		 * Open tab section
		 *
		 * @param index int
		 */
		openSection: function(index){
			if (this.sections[index] === undefined) return;
			if (this.curLayout == 'default' || this.curLayout == 'timeline' || this.curLayout == 'modern' || this.curLayout == 'trendy') {
				this.$container.removeClass('autoresize');
				var height = this.sections[index].height();
				this.$sectionsHelper.performCSSTransition({
					left: -this.width * (this.isRtl ? (this.count - index - 1 ) : index)
				}, this.options.duration, this._events.contentChanged, this.options.easing);
				this.$sectionsWrapper.performCSSTransition({
					height: height
				}, this.options.duration, function(){
					this.$container.addClass('autoresize');
				}.bind(this), this.options.easing);
			} else if (this.curLayout == 'accordion' || this.curLayout == 'ver') {
				if (this.contents[this.active[0]] !== undefined) {
					this.contents[this.active[0]].css('display', 'block').slideUp(this.options.duration);
				}
				this.contents[index].css('display', 'none').slideDown(this.options.duration, this._events.contentChanged);
				// Scrolling to the opened section at small window dimensions
				if (this.curLayout == 'accordion' && $us.canvas.winWidth < 768 && this.headerClicked == true) {
					var newTop = this.headers[0].offset().top;
					for (var i = 0; i < index; i++) {
						newTop += this.headers[i].outerHeight();
					}
					$us.scroll.scrollTo(newTop, true);
					this.headerClicked = false;
				}
			}
			this._events.contentChanged();
			this.$tabs.removeClass('active');
			this.tabs[index].addClass('active');
			this.$sections.removeClass('active');
			this.sections[index].addClass('active');
			this.active[0] = index;
		},

		/**
		 * Toggle some togglable accordion section
		 *
		 * @param index
		 */
		toggleSection: function(index){
			// (!) Can only be used within accordion state
			var indexPos = $.inArray(index, this.active);
			if (indexPos != -1) {
				this.contents[index].css('display', 'block').slideUp(this.options.duration, this._events.contentChanged);
				this.tabs[index].removeClass('active');
				this.sections[index].removeClass('active');
				this.active.splice(indexPos, 1);
			}
			else {
				this.contents[index].css('display', 'none').slideDown(this.options.duration, this._events.contentChanged);
				this.tabs[index].addClass('active');
				this.sections[index].addClass('active');
				this.active.push(index);
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function(){
			this.width = this.$container.width();
			this.$tabsList.removeClass('hidden');

			// Basic layout may be overriden
			if (this.responsive) {
				// if (this.basicLayout == 'ver' && this.curLayout != 'ver') this.switchLayout('ver');
				if (this.curLayout != 'accordion') this.measure();
				var nextLayout = (this.width < this.minWidth) ? 'accordion' : this.basicLayout;
				if (nextLayout !== this.curLayout) this.switchLayout(nextLayout);
			}

			// Fixing tabs display
			if (this.curLayout == 'default' || this.curLayout == 'timeline' || this.curLayout == 'modern' || this.curLayout == 'trendy') {
				this.$container.addClass('autoresize');
				this.$sectionsWrapper.css('width', this.width);
				this.$sectionsHelper.css('width', this.count * this.width);
				this.$sections.css('width', this.width);
				if (this.contents[this.active[0]] !== undefined) {
					this.$sectionsHelper.css('left', -this.width * (this.isRtl ? (this.count - this.active[0] - 1) : this.active[0]));
					var height = this.sections[this.active[0]].height();
					this.$sectionsWrapper.css('height', height);
				}
			}
			this._events.contentChanged();
		}

	};

	$.fn.wTabs = function(options){
		return this.each(function(){
			$(this).data('wTabs', new $us.WTabs(this, options));
		});
	};
}(jQuery);


/**
 * UpSolution Shortcode: us_logos
 */
jQuery(function($){
	$(".w-logos.type_carousel").each(function(){
		$us.getScript($us.templateDirectoryUri+'/framework/js/owl.carousel.min.js', function() {
			var $list = $(this).find('.w-logos-list'),
				items = parseInt($list.data('items')),
				breakpoint1_width = 1024,
				breakpoint1_cols = 3,
				breakpoint2_width = 768,
				breakpoint2_cols = 2,
				breakpoint3_width = 480,
				breakpoint3_cols = 1,
				responsive_breakpoints = {};

			if (parseInt($list.data('breakpoint_1_width')) > 0 && parseInt($list.data('breakpoint_1_cols'))) {
				breakpoint1_width = parseInt($list.data('breakpoint_1_width'));
				breakpoint1_cols = parseInt($list.data('breakpoint_1_cols'));
			}

			if (parseInt($list.data('breakpoint_2_width')) > 0 && parseInt($list.data('breakpoint_2_cols'))) {
				breakpoint2_width = parseInt($list.data('breakpoint_2_width'));
				breakpoint2_cols = parseInt($list.data('breakpoint_2_cols'));
			}

			if (parseInt($list.data('breakpoint_3_width')) > 0 && parseInt($list.data('breakpoint_3_cols'))) {
				breakpoint3_width = parseInt($list.data('breakpoint_3_width'));
				breakpoint3_cols = parseInt($list.data('breakpoint_3_cols'));
			}

			responsive_breakpoints[0] = {items: Math.min(items, breakpoint3_cols), autoplay: $list.data('breakpoint_3_autoplay')};
			responsive_breakpoints[breakpoint3_width] = {items: Math.min(items, breakpoint2_cols), autoplay: $list.data('breakpoint_2_autoplay')};
			responsive_breakpoints[breakpoint2_width] = {items: Math.min(items, breakpoint1_cols), autoplay: $list.data('breakpoint_1_autoplay')};
			responsive_breakpoints[breakpoint1_width] = {items: items};

			$list.owlCarousel({
				mouseDrag: ! jQuery.isMobile,
				items: items,
				loop: true,
				rtl: $('.l-body').hasClass('rtl'),
				nav: $list.data('nav'),
				dots: $list.data('dots'),
				center: $list.data('center'),
				autoplay: $list.data('autoplay'),
				autoplayTimeout: $list.data('timeout'),
				autoplayHoverPause: true,
				slideBy: $list.data('slideby'),
				responsive: responsive_breakpoints
			});
		}.bind(this));

	});
});


/**
 * UpSolution Shortcode: us_testimonials
 */
jQuery(function($){
	$(".w-testimonials.type_carousel").each(function(){
		$us.getScript($us.templateDirectoryUri+'/framework/js/owl.carousel.min.js', function() {
			var $list = $(this).find('.w-testimonials-list'),
				items = parseInt($list.data('items'));
			$list.owlCarousel({
				mouseDrag: ! jQuery.isMobile,
				items: items,
				loop: true,
				rtl: $('.l-body').hasClass('rtl'),
				nav: $list.data('nav'),
				dots: $list.data('dots'),
				center: $list.data('center'),
				autoplay: $list.data('autoplay'),
				autoplayTimeout: $list.data('timeout'),
				autoHeight: $list.data('autoheight'),
				slideBy: $list.data('slideby'),
				autoplayHoverPause: true,
				margin: 30,
				responsive: {
					0: {items: 1, autoHeight: true, autoplay: false},
					768: {items: Math.min(items, 2)},
					1025: {items: Math.min(items, 3)},
					1279: {items: items}
				}
			});

			$us.$window.load(function() { $list.trigger('refresh.owl.carousel'); });
			
		}.bind(this));


	});

	$(".w-testimonials.type_masonry").each(function(){

		$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.isotope.js', function() {
			var $container = $(this).find('.w-testimonials-list'),
				isotopeOptions = {
					layoutMode: 'masonry',
					isOriginLeft: !$('body').hasClass('rtl')
				};
			if ($container.parents('.w-tabs-section-content-h').length) {
				isotopeOptions.transitionDuration = 0;
			}
			$container.imagesLoaded(function(){
				$container.isotope(isotopeOptions);
				$container.isotope();
			});
			$us.$canvas.on('contentChange', function(){
				$container.imagesLoaded(function(){
					$container.isotope();
				});
			});
		}.bind(this));
	});
});


/**
 * UpSolution Shortcode: us_cform
 */
jQuery(function($){

	$('.w-form.for_cform').each(function(){
		var $container = $(this),
			$form = $container.find('form:first'),
			$submitBtn = $form.find('.w-btn'),
			$resultField = $form.find('.w-form-message'),
			options = $container.find('.w-form-json')[0].onclick();

		$form.submit(function(event){
			event.preventDefault();

			// Prevent double-sending
			if ($submitBtn.hasClass('loading')) return;

			$resultField.usMod('type', false).html('');
			// Validation
			var errors = 0;
			$form.find('[data-required="true"]').each(function(){
				var $input = $(this),
					isEmpty = ($input.val() == ''),
					$row = $input.closest('.w-form-row'),
					errorText = options.errors[$input.attr('name')] || '';
				$row.toggleClass('check_wrong', isEmpty);
				$row.find('.w-form-row-state').html(isEmpty ? errorText : '');
				if (isEmpty) {
					errors++;
				}
			});

			if (errors != 0) return;

			$submitBtn.addClass('loading');
			$.ajax({
				type: 'POST',
				url: options.ajaxurl,
				dataType: 'json',
				data: $form.serialize(),
				success: function(result){
					if (result.success) {
						$resultField.usMod('type', 'success').html(result.data);
						$form.find('.w-form-row.check_wrong').removeClass('check_wrong');
						$form.find('.w-form-row.not-empty').removeClass('not-empty');
						$form.find('.w-form-state').html('');
						$form.find('input[type="text"], input[type="email"], textarea').val('');
					} else {
						$form.find('.w-form-row.check_wrong').removeClass('check_wrong');
						$form.find('.w-form-state').html('');
						if (result.data && typeof result.data == 'object') {
							for (var fieldName in result.data) {
								if (fieldName == 'empty_message') {
									var errorText = result.data[fieldName];
									$resultField.usMod('type', 'error').html(errorText);
									continue;
								}
								if (!result.data.hasOwnProperty(fieldName)) continue;
								var $input = $form.find('[name="' + fieldName + '"]'),
									errorText = result.data[fieldName];
								$input.closest('.w-form-row').addClass('check_wrong')
									.find('.w-form-row-state').html(errorText);
							}
						} else {
							$resultField.usMod('type', 'error').html(result.data);
						}
					}
				},
				complete: function(){
					$submitBtn.removeClass('loading');
				}
			});
		});

	});
});


/**
 * UpSolution Shortcode: us_counter
 */
jQuery(function($){
	$('.w-counter').each(function(index, elm){
		var $container = $(this),
			$number = $container.find('.w-counter-number'),
			initial = ($container.data('initial') || '0') + '',
			target = ($container.data('target') || '10') + '',
			prefix = $container.data('prefix') || '',
			suffix = $container.data('suffix') || '',
		// 0 for integers, 1+ for floats (number of digits after the decimal)
			precision = 0,
			usingComma = false;
		if (target.indexOf('.') != -1) {
			precision = target.length - 1 - target.indexOf('.');
		} else if (target.indexOf(',') != -1) {
			precision = target.length - 1 - target.indexOf(',');
			usingComma = true;
			target = target.replace(',', '.');
		}
		initial = window[precision ? 'parseFloat' : 'parseInt'](initial, 10);
		target = window[precision ? 'parseFloat' : 'parseInt'](target, 10);

		if ( /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent) ) {
			if (usingComma) {
				$number.html(prefix + target.toFixed(precision).replace('\.', ',') + suffix);
			} else {
				$number.html(prefix + target.toFixed(precision) + suffix);
			}

			return;
		}

		if (usingComma) {
			$number.html(prefix + initial.toFixed(precision).replace('\.', ',') + suffix);
		} else {
			$number.html(prefix + initial.toFixed(precision) + suffix);
		}
		$us.scroll.addWaypoint(this, '15%', function(){
			var current = initial,
				step = 25,
				stepValue = (target - initial) / 25,
				interval = setInterval(function(){
					current += stepValue;
					step--;
					if (usingComma) {
						$number.html(prefix + current.toFixed(precision).replace('\.', ',') + suffix);
					} else {
						$number.html(prefix + current.toFixed(precision) + suffix);
					}
					if (step <= 0) {
						if (usingComma) {
							$number.html(prefix + target.toFixed(precision).replace('\.', ',') + suffix);
						} else {
							$number.html(prefix + target.toFixed(precision) + suffix);
						}
						window.clearInterval(interval);
					}
				}, 40);
		});
	});
});


/**
 * UpSolution Shortcode: us_progbar
 */
jQuery(function($){
	$('.w-progbar').each(function(index, elm){
		var $container = $(this),
			$bar = $container.find('.w-progbar-bar-h'),
			count = $container.data('count') + '',
			$titleCount = $container.find('.w-progbar-title-count'),
			$barCount = $container.find('.w-progbar-bar-count');

		if (count === null) {
			count = 50;
		}

		if ( /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent) ) {
			$container.removeClass('initial');
			$titleCount.html(count + '%');
			$barCount.html(count + '%');
			return;
		}

		$titleCount.html('0%');
		$barCount.html('0%');

		$us.scroll.addWaypoint(this, '15%', function(){
			var current = 0,
				step = 40,
				stepValue = count / 40,
				interval = setInterval(function(){
					current += stepValue;
					step--;
					$titleCount.html(current.toFixed(0) + '%');
					$barCount.html(current.toFixed(0) + '%');
					if (step <= 0) {
						$titleCount.html(count + '%');
						$barCount.html(count + '%');
						window.clearInterval(interval);
					}
				}, 20);

			$container.removeClass('initial');
		});
	});
});


/**
 * UpSolution Shortcode: us_gallery
 */
jQuery(function($){
	$('.w-gallery.link_file .w-gallery-list').each(function(){

		$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.magnific-popup.js', function(){
			$(this).magnificPopup({
				type: 'image',
				delegate: 'a.w-gallery-item',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0, 1],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: false
			});
		}.bind(this));
	});


	// Applying isotope to gallery
	$('.w-gallery.type_masonry').each(function(index, gallery){
		$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.isotope.js', function(){

			var $container = $($(gallery).find('.w-gallery-list')),
				isotopeOptions = {
					layoutMode: 'masonry',
					isOriginLeft: !$('body').hasClass('rtl')
				};
			if ($container.parents('.w-tabs-section-content-h').length) {
				isotopeOptions.transitionDuration = 0;
			}
			$container.imagesLoaded(function(){
				$container.isotope(isotopeOptions);
				$container.isotope();
			});
			$us.$canvas.on('contentChange', function(){
				$container.imagesLoaded(function(){
					$container.isotope();
				});
			});
		});

	});

});


/**
 * UpSolution Shortcode: us_slider
 */
(function($){
	$.fn.wSlider = function(){
		return this.each(function(){
			$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.royalslider.min.js', function(){
				var $this = $(this),
					$frame = $this.find('.w-slider-h'),
					$slider = $this.find('.royalSlider'),
					$options = $this.find('.w-slider-json'),
					options = $options[0].onclick() || {};
				$options.remove();
				if (!$.fn.royalSlider) {
					return;
				}
				// Always apply certain fit options for blog listing slider
				if ($this.parent().hasClass('w-blog-post-preview')) {
					options['imageScaleMode'] = 'fill';
				}
				$slider.royalSlider(options);
				var slider = $slider.data('royalSlider');
				if (options.fullscreen && options.fullscreen.enabled) {
					// Moving royal slider to the very end of body element to allow a proper fullscreen
					var rsEnterFullscreen = function(){
						$slider.appendTo($('body'));
						slider.ev.off('rsEnterFullscreen', rsEnterFullscreen);
						slider.exitFullscreen();
						slider.enterFullscreen();
						slider.ev.on('rsEnterFullscreen', rsEnterFullscreen);
						slider.ev.on('rsExitFullscreen', rsExitFullscreen);
					};
					slider.ev.on('rsEnterFullscreen', rsEnterFullscreen);
					var rsExitFullscreen = function(){
						$slider.prependTo($frame);
						slider.ev.off('rsExitFullscreen', rsExitFullscreen);
						slider.exitFullscreen();
					};
				}
				$us.$canvas.on('contentChange', function(){
					$slider.parent().imagesLoaded(function(){
						slider.updateSliderSize();
					});
				});
			}.bind(this));


		});
	};
	$(function(){
		jQuery('.w-slider').wSlider();
	});
})(jQuery);


/**
 * UpSolution Widget: w-portfolio
 */
!function($){
	"use strict";

	$us.WPortfolio = function(container, options){
		this.init(container, options);
	};

	$us.WPortfolio.prototype = {

		init: function(container, options){
			// Commonly used dom elements
			this.$container = $(container);
			var $jsonContainer = this.$container.find('.w-portfolio-json');
			if ($jsonContainer.length > 0) {
				this.jsonData = $jsonContainer[0].onclick() || {};
				$jsonContainer.remove();
			}

			this.$filters = this.$container.find('.g-filters-item');
			this.$list = this.$container.find('.w-portfolio-list');
			this.$items = this.$container.find('.w-portfolio-item');
			this.$pagination = this.$container.find('.g-pagination');
			this.$loadmore = this.$container.find('.g-loadmore');
			this.paginationType = this.$pagination.length ? 'regular' : (this.$loadmore.length ? 'ajax' : 'none');
			this.preloaderType = ( this.$list.data('preloader_type') ) ? this.$list.data('preloader_type') : 1;
			this.items = {};
			this.curCategory = '*';
			this.loading = false;

			this.$items.each(function(index, item){
				this.items[parseInt(item.getAttribute('data-id'))] = $(item);
			}.bind(this));

			if (this.$container.hasClass('lightbox_page')) {
				if (this.jsonData == undefined) return;
				this.ajaxUrl = this.jsonData.ajax_url || '';

				this.lightboxTimer = null;
				this.$lightboxOverlay = this.$container.find('.l-popup-overlay');
				this.$lightboxWrap = this.$container.find('.l-popup-wrap');
				this.$lightboxBox = this.$container.find('.l-popup-box');
				this.$lightboxContent = this.$container.find('.l-popup-box-content');
				this.$lightboxContentPreloader = this.$lightboxContent.find('.g-preloader');
				this.$lightboxContentFrame = this.$container.find('.l-popup-box-content-frame');
				this.$lightboxNextArrow = this.$container.find('.l-popup-arrow.to_next');
				this.$lightboxPrevArrow = this.$container.find('.l-popup-arrow.to_prev');
				this.$container.find('.l-popup-closer').click(function(){ this.hideLightbox(); }.bind(this));
				this.$container.find('.l-popup-box').click(function(){ this.hideLightbox(); }.bind(this));
				this.$container.find('.l-popup-box-content').click(function(e){ e.stopPropagation(); }.bind(this));
				this.originalURL = window.location.href;
				this.lightboxOpened = false;

				this.$items.each(function(key, item){
					var $item = $(item),
						$anchor = $item.find('.w-portfolio-item-anchor'),
						itemUrl = $anchor.attr('href');

					if ( ! $item.hasClass('custom-link')) {
						$anchor.click(function(e){
							if ($us.$window.width() >= $us.canvasOptions.disableEffectsWidth ) {
								e.stopPropagation();
								e.preventDefault();

								this.openLightboxItem(itemUrl, $item);
							}
						}.bind(this));
					}
				}.bind(this));

				$(window).on('resize', function(){
					if (this.lightboxOpened && $us.$window.width() < $us.canvasOptions.disableEffectsWidth ) {
						this.hideLightbox();
					}
				}.bind(this));
			}

			if (this.$list.hasClass('owl-carousel')) {
				$us.getScript($us.templateDirectoryUri+'/framework/js/owl.carousel.min.js', function() {
					var items = parseInt(this.$list.data('items')),
						responsive = {};
					responsive[0] = {items: Math.min(items, this.$list.data('breakpoint_3_cols'))};
					responsive[this.$list.data('breakpoint_3_width')] = {items: Math.min(items, this.$list.data('breakpoint_2_cols'))};
					responsive[this.$list.data('breakpoint_2_width')] = {items: Math.min(items, this.$list.data('breakpoint_1_cols'))};
					responsive[this.$list.data('breakpoint_1_width')] = {items: items};

					this.$list.owlCarousel({
						mouseDrag: ! jQuery.isMobile,
						items: items,
						loop: true,
						rtl: $('.l-body').hasClass('rtl'),
						nav: this.$list.data('nav'),
						dots: this.$list.data('dots'),
						center: this.$list.data('center'),
						autoplay: this.$list.data('autoplay'),
						autoplayTimeout: this.$list.data('timeout'),
						slideBy: this.$list.data('slideby'),
						autoplayHoverPause: true,
						responsive: responsive
					});
				}.bind(this));

				return;
			}

			if (this.$container.hasClass('with_isotope')) {
				this.isotopeOptions = {
					itemSelector: '.w-portfolio-item',
					layoutMode: 'masonry',
					masonry: {},
					isOriginLeft: !$('.l-body').hasClass('rtl')
				};

				if (this.$container.find('.w-portfolio-item.size_1x1').length) {
					this.itemWidth = 1;
					this.isotopeOptions.masonry.columnWidth = '.size_1x1';
				} else if (this.$container.find('.w-portfolio-item.size_1x2').length) {
					this.itemWidth = 1;
					this.isotopeOptions.masonry.columnWidth = '.size_1x2';
				} else {
					this.itemWidth = 2;
					this.isotopeOptions.masonry.columnWidth = '.w-portfolio-item';
				}

				if (this.paginationType != 'none') {
					if (this.jsonData == undefined) return;
					this.jsonData = $jsonContainer[0].onclick() || {};
					this.ajaxUrl = this.jsonData.ajax_url || '';
					this.templateVars = JSON.stringify(this.jsonData.template_vars || {});
					this.perpage = this.jsonData.perpage || this.$items.length;
					this.order = this.jsonData.order || {};
					this.sizes = this.jsonData.sizes || {};
					this.curPage = this.jsonData.page || 1;
					this.infiniteScroll = this.jsonData.infinite_scroll || 0;

					this.isotopeOptions.sortBy = 'number';
					this.isotopeOptions.getSortData = {
						number: function(elm){
							return this.order['*'].indexOf(parseInt(elm.getAttribute('data-id')));
						}.bind(this)
					};
				}

				$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.isotope.js', function(){
					if (this.paginationType == 'ajax') {
						this.$loadmore.on('click', function(){
							var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage);
							if (this.curPage < maxPage) {
								this.setState(this.curPage + 1);
							}
						}.bind(this));
					}
					else if (this.paginationType == 'regular') {
						this.paginationPcre = new RegExp('/page/([0-9]+)/$');
						this.location = location.href.replace(this.paginationPcre, '/');
						this.$navLinks = this.$container.find('.nav-links');
						var self = this;
						this.$navLinks.on('click', 'a', function(e){
							e.preventDefault();
							var arr,
								pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
							self.setState(pageNum);
						});
						this.renderPagination(this.curPage);
					}

					this.$filters.each(function(index, filter){
						var $filter = $(filter),
							category = $filter.data('category');
						$filter.on('click', function(){
							if (category != this.curCategory) {
								this.setState((this.paginationType == 'regular') ? 1 : this.curPage, category);
								this.$filters.removeClass('active');
								$filter.addClass('active');
							}
						}.bind(this))
					}.bind(this));

					// Applying isotope
					this.loading = true;
					this.$list.imagesLoaded(function(){
						this.$list.isotope(this.isotopeOptions);
						this.$list.isotope();
						this.loading = false;
						$us.$canvas.on('contentChange', function(){
							this.$list.isotope('layout');
						}.bind(this));
						$(window).on('resize', function(){
							this.$list.isotope('layout');
						}.bind(this));


						if (this.paginationType == 'ajax' && this.infiniteScroll) {
							$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
								this.$loadmore.click();
							}.bind(this));
						}
					}.bind(this));
				}.bind(this));
			}

		},

		_hasScrollbar: function(){
			return document.documentElement.scrollHeight > document.documentElement.clientHeight;
		},
		_getScrollbarSize: function(){
			if ($us.scrollbarSize === undefined) {
				var scrollDiv = document.createElement('div');
				scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
				document.body.appendChild(scrollDiv);
				$us.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
				document.body.removeChild(scrollDiv);
			}
			return $us.scrollbarSize;
		},
		openLightboxItem: function(itemUrl, $item){
			this.showLightbox();

			var $nextItem = $item.nextAll('div:visible:not(.custom-link)').first(),
				$prevItem = $item.prevAll('div:visible:not(.custom-link)').first();

			if ($nextItem.length != 0) {
				this.$lightboxNextArrow.show();
				this.$lightboxNextArrow.attr('title', $nextItem.find('.w-portfolio-item-title').text());
				this.$lightboxNextArrow.off('click').click(function(e){
					var $nextItemAnchor = $nextItem.find('.w-portfolio-item-anchor'),
						nextItemUrl = $nextItemAnchor.attr('href');
					e.stopPropagation();
					e.preventDefault();

					this.openLightboxItem(nextItemUrl, $nextItem);
				}.bind(this));
			} else {
				this.$lightboxNextArrow.attr('title', '');
				this.$lightboxNextArrow.hide();
			}

			if ($prevItem.length != 0) {
				this.$lightboxPrevArrow.show();
				this.$lightboxPrevArrow.attr('title', $prevItem.find('.w-portfolio-item-title').text());
				this.$lightboxPrevArrow.off('click').click(function(e){
					var $prevItemAnchor = $prevItem.find('.w-portfolio-item-anchor'),
						prevItemUrl = $prevItemAnchor.attr('href');
					e.stopPropagation();
					e.preventDefault();

					this.openLightboxItem(prevItemUrl, $prevItem);
				}.bind(this));
			} else {
				this.$lightboxPrevArrow.attr('title', '');
				this.$lightboxPrevArrow.hide();
			}

			if (itemUrl.indexOf('?') !== -1) {
				this.$lightboxContentFrame.attr('src', itemUrl + '&us_iframe=1');
			} else {
				this.$lightboxContentFrame.attr('src', itemUrl + '?us_iframe=1');
			}

			// Replace window location with portfolio's URL
			if (history.replaceState) {
				history.replaceState(null, null, itemUrl);
			}
			this.$lightboxContentFrame.load(function() {
				this.lightboxContentLoaded();
			}.bind(this));

		},
		lightboxContentLoaded: function(){
			this.$lightboxContentPreloader.css('display', 'none');
		},
		showLightbox: function(){
			clearTimeout(this.lightboxTimer);
			this.$lightboxOverlay.appendTo($us.$body).show();
			this.$lightboxWrap.appendTo($us.$body).show();
			this.lightboxOpened = true;

			this.$lightboxContentPreloader.css('display', 'block');
			// this.$lightboxContentFrame.css('display', 'none');
			// this.$lightboxContentFrame.css('width', this.$lightboxContent.width());
			$us.$html.addClass('usoverlay_fixed');

			if ( ! $.isMobile ) {
				// Storing the value for the whole popup visibility session
				this.windowHasScrollbar = this._hasScrollbar();
				if (this.windowHasScrollbar && this._getScrollbarSize()) {
					$us.$html.css('margin-right', this._getScrollbarSize());
				}
			}
			this.lightboxTimer = setTimeout(function(){ this.afterShowLightbox(); }.bind(this), 25);
		},
		afterShowLightbox: function(){
			clearTimeout(this.lightboxTimer);
			this.$lightboxOverlay.addClass('active');
			this.$lightboxBox.addClass('active');

			$us.$canvas.trigger('contentChange');
			$us.$window.trigger('resize');
		},
		hideLightbox: function(){
			clearTimeout(this.lightboxTimer);
			this.lightboxOpened = false;
			this.$lightboxOverlay.removeClass('active');
			this.$lightboxBox.removeClass('active');
			// Replace window location back to original URL
			if (history.replaceState) {
				history.replaceState(null, null, this.originalURL);
			}
			
			this.lightboxTimer = setTimeout(function(){ this.afterHideLightbox(); }.bind(this), 500);
		},
		afterHideLightbox: function(){
			clearTimeout(this.lightboxTimer);
			this.$lightboxOverlay.appendTo(this.$container).hide();
			this.$lightboxWrap.appendTo(this.$container).hide();
			this.$lightboxContentFrame.attr('src', 'about:blank');
			$us.$html.removeClass('usoverlay_fixed');
			if ( ! $.isMobile ) {
				if (this.windowHasScrollbar) $us.$html.css('margin-right', '');
			}
		},

		setState: function(page, category){
			if (this.paginationType == 'none') {
				// Simple state changer
				this.$list.isotope({filter: (category == '*') ? '*' : ('.' + category)});
				this.curCategory = category;
				return;
			}

			if (this.loading) return;
			category = category || this.curCategory;
			var start = (this.paginationType == 'ajax') ? 0 : ((page - 1) * this.perpage),
				length = page * this.perpage,
				showIds = (this.order[category] || []).slice(start, length),
				loadIds = [],
				$newItems = [];
			$.each(showIds, function(i, id){
				// Determining which items we need to load via ajax and creating temporary stubs for them
				if (this.items[id] !== undefined) return;
				var itemSize = (this.sizes[id] || '1x1'),
					itemHtml = '<div class="w-portfolio-item size_' + itemSize + ' loading" data-id="' + id + '">' +
						'<div class="w-portfolio-item-anchor"><div class="g-preloader type_'+this.preloaderType+'"><div>' +
						'</div></div></div></div>';
				this.items[id] = $(itemHtml).appendTo(this.$list);
				$newItems.push(this.items[id][0]);
				loadIds.push(showIds[i]);
			}.bind(this));
			if (loadIds.length > 0) {
				// Loading new items
				var $insertedItems = $();
				$.ajax({
					type: 'post',
					url: this.ajaxUrl,
					data: {
						action: 'us_ajax_portfolio',
						ids: loadIds.join(','),
						template_vars: this.templateVars
					},
					success: function(html){
						var $container = $('<div>', {html: html}),
							$items = $container.children(),
							isotope = this.$list.data('isotope');
						$items.each(function(index, item){
							var $item = $(item),
								itemID = parseInt($item.data('id'));

							$item.imagesLoaded(function(){
								this.items[itemID].attr('class', $item.attr('class')).attr('style', $item.attr('style'));
								this.itemLoaded(itemID, $item);
								this.items[itemID].html($item.html());
								$insertedItems = $insertedItems.add(this.items[itemID]);

								if (this.$container.hasClass('lightbox_page')) {
									var $loadedItem = this.$container.find('.w-portfolio-item[data-id="'+itemID+'"]'),
										$anchor = $loadedItem.find('.w-portfolio-item-anchor'),
										itemUrl = $anchor.attr('href');

									if ( ! $loadedItem.hasClass('custom-link')) {
										$anchor.click(function(e){
											if ($us.$window.width() >= $us.canvasOptions.disableEffectsWidth ) {
												e.stopPropagation();
												e.preventDefault();
												this.openLightboxItem(itemUrl, $loadedItem);
											}
										}.bind(this));
									}
								}

								if ($insertedItems.length >= loadIds.length) {
									$container.remove();
									this.itemsLoaded($insertedItems);
								}
								if (isotope) {
									if (this.itemWidth != 1) {
										if (this.$container.find('.w-portfolio-item.size_1x1').length) {
											this.itemWidth = 1;
											this.isotopeOptions.masonry.columnWidth = '.size_1x1';
										} else if (this.$container.find('.w-portfolio-item.size_1x2').length) {
											this.itemWidth = 1;
											this.isotopeOptions.masonry.columnWidth = '.size_1x2';
										} else {
											this.itemWidth = 2;
											this.isotopeOptions.masonry.columnWidth = '.w-portfolio-item';
										}
										if (this.itemWidth == 1) {
											this.$list.isotope(this.isotopeOptions);
										}
									}

									this.$list.isotope('layout');
								}

								if (this.items[itemID].find('a[ref=magnificPopup][class!=direct-link]').length != 0) {

									$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.magnific-popup.js', function(){
										var $loadedItem = this.$container.find('.w-portfolio-item[data-id="'+itemID+'"]');
										$loadedItem.find('a[ref=magnificPopup][class!=direct-link]').magnificPopup({
											type: 'image',
											removalDelay: 300,
											mainClass: 'mfp-fade',
											fixedContentPos: false
										});
									}.bind(this));
								}
							}.bind(this));

						}.bind(this));

					}.bind(this)
				});
			}
			this.$list.isotope({
				filter: function(){
					return (showIds.indexOf(parseInt(this.getAttribute('data-id'))) != -1);
				}
			});
			if (loadIds.length > 0) {
				this.$list.isotope('insert', $newItems);
			}
			if (this.infiniteScroll) {
				$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
					this.$loadmore.click();
				}.bind(this));
			}
			this.curPage = page;
			this.curCategory = category;
			this.renderPagination();
		},

		renderPagination: function(){
			if (this.paginationType == 'ajax') {
				var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage);
				this.$loadmore[(this.curPage < maxPage) ? 'removeClass' : 'addClass']('done');
			}
			else if (this.paginationType == 'regular') {
				var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage),
					html = '';
				if (maxPage > 1) {
					if (this.curPage > 1) {
						html += '<a href="' + this.pageUrl(this.curPage - 1) + '" class="prev page-numbers"><span>&lt;</span></a>';
					} else {
						html += '<span class="prev page-numbers">&lt;</span>';
					}
					for (var i = 1; i <= maxPage; i++) {
						if (i != this.curPage) {
							html += '<a href="' + this.pageUrl(i) + '" class="page-numbers"><span>' + i + '</span></a>';
						} else {
							html += '<span class="page-numbers current"><span>' + i + '</span></span>';
						}
					}
					if (this.curPage < maxPage) {
						html += '<a href="' + this.pageUrl(this.curPage + 1) + '" class="next page-numbers"><span>&gt;</span></a>';
					} else {
						html += '<span class="next page-numbers">&gt;</span>';
					}
				}
				this.$navLinks.html(html);
			}
		},

		pageUrl: function(page){
			return (page == 1) ? this.location : (this.location + 'page/' + page + '/');
		},

		/**
		 * Overloadable function for themes
		 * @param $item
		 */
		itemLoaded: function($item){
		},

		/**
		 * Overloadable function for themes
		 * @param $item
		 */
		itemsLoaded: function($items){
		}

	};

	$.fn.wPortfolio = function(options){
		return this.each(function(){
			$(this).data('wPortfolio', new $us.WPortfolio(this, options));
		});
	};

	$('.w-portfolio-list').each(function(){
		var $list = $(this);
		$us.getScript($us.templateDirectoryUri+'/framework/js/jquery.magnific-popup.js', function(){
			var delegateStr = 'a[ref=magnificPopupPortfolio]:visible';
			if ($list.hasClass('owl-carousel')) {
				delegateStr = '.owl-item:not(.cloned) a[ref=magnificPopupPortfolio]';
			}
			$(this).magnificPopup({
				type: 'image',
				delegate: delegateStr,
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0, 1],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: false
			});
		}.bind(this));

	});

}(jQuery);

/**
 * UpSolution Widget: w-cart
 *
 * @requires $us.canvas
 * @requires $us.nav
 */
jQuery(function($){
	var $cart = $('.w-cart');
	if ($cart.length == 0) return;
	var $quantity = $cart.find('.w-cart-quantity');

	var updateCart = function(){
		var $mini_cart_amount = $cart.find('.us_mini_cart_amount'),
			mini_cart_amount = $mini_cart_amount.text();

		if (mini_cart_amount !== undefined) {
			mini_cart_amount = mini_cart_amount + '';
			mini_cart_amount = mini_cart_amount.match(/\d+/g);

			if (mini_cart_amount > 0) {
				$quantity.html(mini_cart_amount);
				$cart.removeClass('empty');
			} else {
				$quantity.html('0');
				$cart.addClass('empty');
			}

		} else {
			// fallback in case our action wasn't fired somehow
			var $quantities = $cart.find('.quantity'),
				total = 0;
			$quantities.each(function(){
				var quantity,
					text = $(this).text() + '',
					matches = text.match(/\d+/g);

				if (matches) {
					quantity = parseInt(matches[0], 10);
					total += quantity;
				}

			});

			if (total > 0) {
				$quantity.html(total);
				$cart.removeClass('empty');
			} else {
				$quantity.html('0');
				$cart.addClass('empty');
			}

		}

	};

	updateCart();

	$(document.body).bind('wc_fragments_loaded', function(){
		updateCart();
	});

	$(document.body).bind('wc_fragments_refreshed', function(){
		updateCart();
	});

	var $notification = $cart.find('.w-cart-notification'),
		$productName = $notification.find('.product-name'),
		$cartLink = $cart.find('.w-cart-link'),
		$dropdown = $cart.find('.w-cart-dropdown'),
		$quantity = $cart.find('.w-cart-quantity'),
		productName = $productName.text(),
		showFn = 'fadeInCSS',
		hideFn = 'fadeOutCSS',
		opened = false;

	$notification.on('click', function(){
		$notification[hideFn]();
	});

	jQuery('body').bind('added_to_cart', function(event, fragments, cart_hash, $button){
		if (event === undefined) return;

		updateCart();

		productName = $button.closest('.product').find('.woocommerce-loop-product__title').text();
		$productName.html(productName);

		$notification.addClass('shown');
		$notification.on('mouseenter', function(){
			$notification.removeClass('shown');
		});

		var newTimerId = setTimeout(function(){
			$notification.removeClass('shown');
			$notification.off('mouseenter');
		}, 3000);

	});

	if ($.isMobile) {
		var outsideClickEvent = function(e){
			if (jQuery.contains($cart[0], e.target)) return;
			$cart.removeClass('opened');
			$us.$body.off('touchstart', outsideClickEvent);
			opened = false;
		};
		$cartLink.on('click', function(e){
			if (!opened) {
				e.preventDefault();
				$cart.addClass('opened');
				$us.$body.on('touchstart', outsideClickEvent);
			} else {
				$cart.removeClass('opened');
				$us.$body.off('touchstart', outsideClickEvent);
			}
			opened = !opened;
		});
	}
});


/**
 * UpSolution Login Widget: widget_us_login
 *
 */
!function($){
	"use strict";

	$us.wUsLogin = function(container, options){
		this.$container = $(container);
		this.$form = this.$container.find('.w-form');
		this.$profile = this.$container.find('.w-profile');

		var $jsonContainer = this.$container.find('.w-profile-json');

		this.jsonData = $jsonContainer[0].onclick() || {};
		$jsonContainer.remove();

		this.ajaxUrl = this.jsonData.ajax_url || '';
		this.logoutRedirect = this.jsonData.logout_redirect || '';

		$.ajax({
			type: 'post',
			url: this.ajaxUrl,
			data: {
				action: 'us_ajax_user_info',
				logout_redirect: this.logoutRedirect
			},
			success: function(result){
				if (result.success) {
					var $avatar = this.$profile.find('.w-profile-avatar'),
						$name = this.$profile.find('.w-profile-name'),
						$logoutLink = this.$profile.find('.w-profile-link.for_logout');

					$avatar.html(result.data.avatar);
					$name.html(result.data.name);
					$logoutLink.attr('href', result.data.logout_url);

					this.$profile.removeClass('hidden');
				} else {
					this.$form.removeClass('hidden');
				}
			}.bind(this)
		});
	};

	$.fn.wUsLogin = function(options){
		return this.each(function(){
			$(this).data('wUsLogin', new $us.wUsLogin(this, options));
		});
	};

	$(function(){
		$('.widget_us_login').wUsLogin();
	});
}(jQuery);


/**
 * UpSolution Widget: w-maps
 *
 * Used for [us_gmaps] shortcode
 */
!function($){
	"use strict";

	$us.WMapsGeocodesCounter = 0; // counter of total geocode requests number
	$us.WMapsGeocodesRunning = false;
	$us.WMapsCurrentGeocode = 0; // current processing geocode
	$us.WMapsGeocodesMax = 5; // max number of simultaneous geocode requests allowed
	$us.WMapsGeocodesStack = {};

	$us.WMapsRunGeoCode = function(){
		if ($us.WMapsCurrentGeocode <= $us.WMapsGeocodesCounter) {
			$us.WMapsGeocodesRunning = true;
			if ($us.WMapsGeocodesStack[$us.WMapsCurrentGeocode] != null)
				$us.WMapsGeocodesStack[$us.WMapsCurrentGeocode]();
		} else {
			$us.WMapsGeocodesRunning = false;
		}
	};

	$us.WMaps = function(container, options){

		this.$container = $(container);

		var $jsonContainer = this.$container.find('.w-map-json'),
			jsonOptions = $jsonContainer[0].onclick() || {},
			$jsonStyleContainer = this.$container.find('.w-map-style-json'),
			jsonStyleOptions,
			markerOptions,
			shouldRunGeoCode = false;
		$jsonContainer.remove();
		if ($jsonStyleContainer.length) {
			jsonStyleOptions = $jsonStyleContainer[0].onclick() || {};
			$jsonStyleContainer.remove();
		}


		// Setting options
		var defaults = {};
		this.options = $.extend({}, defaults, jsonOptions, options);

		this._events = {
			redraw: this.redraw.bind(this)
		};

		var gmapsOptions = {
			el: '#' + this.$container.attr('id'),
			lat: 0,
			lng: 0,
			zoom: this.options.zoom,
			type: this.options.type,
			height: this.options.height + 'px',
			width: '100%',
			mapTypeId: google.maps.MapTypeId[this.options.maptype]
		};

		if (this.options.hideControls) {
			gmapsOptions.disableDefaultUI = true;
		}
		if (this.options.disableZoom) {
			gmapsOptions.scrollwheel = false;
		}
		if (this.options.disableDragging && ( !$us.$html.hasClass('no-touch'))) {
			gmapsOptions.draggable = false;
		}
		if (this.options.mapBgColor) {
			gmapsOptions.backgroundColor = this.options.mapBgColor;
		}

		this.GMapsObj = new GMaps(gmapsOptions);
		if (jsonStyleOptions != null && jsonStyleOptions != {}) {
			this.GMapsObj.map.setOptions({styles: jsonStyleOptions});
		}

		var that = this;

		if (this.options.latitude != null && this.options.longitude != null) {
			this.GMapsObj.setCenter(this.options.latitude, this.options.longitude);
		} else {
			var mapGeoCode = function(geocodeNum){
				GMaps.geocode({
					address: that.options.address,
					callback: function(results, status){
						if (status == 'OK') {
							var latlng = results[0].geometry.location;
							that.options.latitude = latlng.lat();
							that.options.longitude = latlng.lng();
							that.GMapsObj.setCenter(that.options.latitude, that.options.longitude);
							$us.WMapsCurrentGeocode++;
							$us.WMapsRunGeoCode();
						} else if (status == "OVER_QUERY_LIMIT") {
							setTimeout(function(){
								$us.WMapsRunGeoCode()
							}, 2000);
						}
					}
				});
			};
			shouldRunGeoCode = true;
			$us.WMapsGeocodesStack[$us.WMapsGeocodesCounter] = mapGeoCode;
			$us.WMapsGeocodesCounter++;
		}

		$.each(this.options.markers, function(i, val){
			markerOptions = {};
			if (that.options.icon != null) {
				markerOptions.icon = {
					url: that.options.icon.url,
					size: new google.maps.Size(that.options.icon.size[0], that.options.icon.size[1]),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(that.options.icon.anchor[0], that.options.icon.anchor[1])
				};
			}

			if (that.options.markers[i] != null) {

				if (that.options.markers[i].latitude != null && that.options.markers[i].longitude != null) {
					markerOptions.lat = that.options.markers[i].latitude;
					markerOptions.lng = that.options.markers[i].longitude;
					markerOptions.infoWindow = {content: that.options.markers[i].html};
					var marker = that.GMapsObj.addMarker(markerOptions);
					if (that.options.markers[i].infowindow) {
						marker.infoWindow.open(that.GMapsObj.map, marker);
					}
				} else {
					var markerGeoCode = function(geocodeNum){
						GMaps.geocode({
							address: that.options.markers[i].address,
							callback: function(results, status){
								if (status == 'OK') {
									var latlng = results[0].geometry.location;
									markerOptions.lat = latlng.lat();
									markerOptions.lng = latlng.lng();
									markerOptions.infoWindow = {content: that.options.markers[i].html};
									var marker = that.GMapsObj.addMarker(markerOptions);
									if (that.options.markers[i].infowindow) {
										marker.infoWindow.open(that.GMapsObj.map, marker);
									}
									$us.WMapsCurrentGeocode++;
									$us.WMapsRunGeoCode();
								} else if (status == "OVER_QUERY_LIMIT") {
									setTimeout(function(){
										$us.WMapsRunGeoCode()
									}, 2000);
								}
							}
						});
					};
					shouldRunGeoCode = true;
					$us.WMapsGeocodesStack[$us.WMapsGeocodesCounter] = markerGeoCode;
					$us.WMapsGeocodesCounter++;
				}
			}
		});

		if (shouldRunGeoCode && ( !$us.WMapsGeocodesRunning)) {
			$us.WMapsRunGeoCode();
		}

		$us.$canvas.on('contentChange', this._events.redraw);

		// In case some toggler was opened before the actual page load
		$us.$window.load(this._events.redraw);
	};

	$us.WMaps.prototype = {
		/**
		 * Fixing hidden and other breaking-cases maps
		 */
		redraw: function(){
			if (this.$container.is(':hidden')) return;
			this.GMapsObj.refresh();
			if (this.options.latitude != null && this.options.longitude != null) {
				this.GMapsObj.setCenter(this.options.latitude, this.options.longitude);
			}

		}
	};

	$.fn.wMaps = function(options){
		return this.each(function(){
			$(this).data('wMaps', new $us.WMaps(this, options));
		});
	};

	$(function(){
		var $wMap = $('.w-map');
		if ($wMap.length){
			$us.getScript($us.templateDirectoryUri+'/framework/js/gmaps.min.js', function(){
				$wMap.wMaps();
			});
		}
	});
}(jQuery);


/**
 * UpSolution Widget: w-sharing
 */
!function($){
	"use strict";

	$('.w-sharing-item').on('click', function(){
		var $this = $(this);
		var opt = {
			url: window.location,
			text: document.title,
			lang: document.documentElement.lang,
			image: $('meta[name="og:image"]').attr('content') || ''
		};
		if ($this.attr('data-sharing-url') !== undefined && $this.attr('data-sharing-url') != '') {
			opt.url = $this.attr('data-sharing-url');
		}
		if ($this.attr('data-sharing-image') !== undefined && $this.attr('data-sharing-image') != '') {
			opt.image = $this.attr('data-sharing-image');
		}
		if (opt.image == '' || opt.image === undefined) {
			var first_image_src = $('img').first().attr('src');
			if (first_image_src != undefined && first_image_src != '') {
				opt.image = first_image_src;
			}
		}
		if ($this.hasClass('facebook')) {
			window.open("http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(opt.url) + "&t=" + encodeURIComponent(opt.text) + "", "", "toolbar=0, status=0, width=900, height=500");
		} else if ($this.hasClass('twitter')) {
			window.open("https://twitter.com/intent/tweet?text=" + encodeURIComponent(opt.text) + "&url=" + encodeURIComponent(opt.url), "", "toolbar=0, status=0, width=650, height=360");
		} else if ($this.hasClass('linkedin')) {
			window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(opt.url), 'linkedin', 'toolbar=no,width=550,height=550');
		} else if ($this.hasClass('gplus')) {
			window.open("https://plus.google.com/share?hl=" + encodeURIComponent(opt.lang) + "&url=" + encodeURIComponent(opt.url), "", "toolbar=0, status=0, width=900, height=500");
		} else if ($this.hasClass('pinterest')) {
			window.open('http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(opt.url) + '&media=' + encodeURIComponent(opt.image) + '&description=' + encodeURIComponent(opt.text), 'pinterest', 'toolbar=no,width=700,height=300');
		} else if ($this.hasClass('vk')) {
			window.open('http://vk.com/share.php?url=' + encodeURIComponent(opt.url) + '&title=' + encodeURIComponent(opt.text), '&description=&image=' + encodeURIComponent(opt.image), 'toolbar=no,width=700,height=300');
		} else if ($this.hasClass('email')) {
			window.location = 'mailto:?subject=' + opt.text + '&body=' + opt.url;
		}
	});
}(jQuery);


/**
 * UpSolution Widget: l-preloader
 */
!function($){
	"use strict";

	if ($('.l-preloader').length) {
		$('document').ready(function(){
			setTimeout(function(){
				$('.l-preloader').addClass('done');
			}, 500);
			setTimeout(function(){
				$('.l-preloader').addClass('hidden');
			}, 1000); // 500 ms after 'done' class is added
		});
	}
}(jQuery);

/**
 * RevSlider support for our tabs
 */
jQuery(function($){
	$('.w-tabs .rev_slider').each(function(){
		var $slider = $(this);
		$slider.bind("revolution.slide.onloaded",function (e) {
			$us.$canvas.on('contentChange', function(){
				$slider.revredraw();
			});
		});
	});
});
