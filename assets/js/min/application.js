jQuery(document).ready(function(a){function t(){var t=a(".next-link"),n=t.data("ppp"),s=t.data("action"),r=t.data("tax"),i=t.data("type"),o=t.parent().siblings(".archive-container");o.fadeTo("fast",0),a.ajax({type:"GET",url:s,data:{tax:r,ppp:n,type:i},complete:function(a){var n=JSON.parse(a.responseText);t.data("next",2),t.data("max",n.max),2>n.max?t.addClass("disabled"):t.removeClass("disabled");var s=e(n,i);o.html(s),o.append('<a class="card empty" href="#"></a><a class="card empty" href="#"></a><a class="card empty" href="#"></a>'),o.fadeTo("fast",1)}})}function e(a,t){for(var e="",s=0;s<=a.data.length-1;s++){switch(t){case"product":var r={image:a.data[s].image,title:a.data[s].title,permalink:a.data[s].permalink,price:a.data[s].price},i=n(r,t);break;case"resources":var r={image:a.data[s].image,title:a.data[s].title,permalink:a.data[s].permalink},i=n(r,t)}e+=i}return e}function n(t,e){switch(e){case"product":var n=a(".card").first().clone();n.attr("href",t.permalink),n.find(".card-image").attr("style","background-image: url("+t.image+"); background-repeat: no-repeat; background-position: center center; background-size: cover;"),n.find(".card-content h4").text(t.title),n.find(".card-content .price").text(t.price);break;case"resources":var n=a(".card").first().clone();n.attr("href",t.permalink),n.find(".card-image").attr("style","background-image: url("+t.image+"); background-repeat: no-repeat; background-position: center center; background-size: cover;"),n.find(".card-content h4").text(t.title);break;default:return!1}var s=a("<div>").append(n.clone()).html();return s}function s(){o.fadeOut(),l.fadeOut(),l.css({height:"auto"});var t=1200,e=500,n=85,s=75;h.each(function(){var r=Math.floor(Math.random()*(t-e+1))+e,i=Math.floor(Math.random()*(n-s+1))+s;a(this).css({"-webkit-transition":"-webkit-transform "+r+"ms ease","-moz-transition":"-moz-transform "+r+"ms ease","-o-transition":"-o-transform "+r+"ms ease",transition:"transform "+r+"ms ease","-webkit-transform":"translate(0, 100vh)","-moz-transform":"translate(0, 100vh)","-ms-transform":"translate(0, 100vh)","-o-transform":"translate(0, 100vh)",transform:"translate(0, 100vh)","background-color":"rgba(35,31,32,"+i/100+")"})})}function r(){h.each(function(){a(this).css({"-webkit-transform":"translate(0, 0vh)","-moz-transform":"translate(0, 0vh)","-ms-transform":"translate(0, 0vh)",transform:"translate(0, 0vh)"})})}function i(t,e){var n=a(window).height();c.html(a("#"+t).clone()).html(),e&&l.addClass(e),setTimeout(function(){l.fadeIn();var a=l.outerHeight();a>n-30?l.css({height:n-30,"margin-top":15}):l.css({"margin-top":n/2-a/2})},600)}a(".news-tax-label").click(function(){a(this).parent().siblings(".news-tax").find(".news-tax-label").each(function(){a(this).next();a(this).hasClass("displayed")&&(a(this).removeClass("displayed"),a(this).next().slideUp(300))});var t=a(this).next();a(this).hasClass("displayed")?(a(this).removeClass("displayed"),t.slideUp(300)):(a(this).addClass("displayed"),t.slideDown(300))}),a(".next-link").click(function(t){t.preventDefault();var n=a(this),s=parseInt(n.data("next")),r=n.data("ppp"),i=n.data("max"),o=n.data("tax"),l=n.data("type"),c=n.data("action");a.ajax({type:"GET",url:c,data:{next:s,ppp:r,tax:o,type:l},complete:function(t){n.data("next",s+1),s+1>i?n.addClass("disabled"):n.removeClass("disabled");var r=JSON.parse(t.responseText),o=e(r,l);a(".empty").remove(),n.parent().siblings(".archive-container").append(o),n.parent().siblings(".archive-container").append('<a class="card empty" href="#"></a><a class="card empty" href="#"></a><a class="card empty" href="#"></a>')}})}),a(".news-tax-list li a").click(function(e){a(this).hasClass("jumpnav")||e.preventDefault();var n=a(".next-link"),s=a(this),r=s.data("tax"),i=s.data("term");a(".news-tax-label").each(function(){a(this).next().slideUp(300),a(this).removeClass("displayed")});var o=s.closest(".news-tax");"all"==i?o.find(".news-tax-label").text(s.data("all")):o.find(".news-tax-label").text(s.text());var l=n.data("tax");l[r]=[i],n.data("tax",l),t()}),a("#filterSearch").submit(function(t){t.preventDefault();var n=a("#filterSearchVal").val();if(n.length>1){var s=a(".next-link"),r=s.data("type"),i=s.data("action");s.addClass("disabled"),a(".news-tax-label").each(function(){a(this).next();a(this).hasClass("displayed")&&(a(this).removeClass("displayed"),a(this).next().slideUp(300)),a(this).text(a(this).data("all"))});var o=s.parent().siblings(".archive-container");o.fadeTo("fast",0),a.ajax({type:"GET",url:i,data:{s:n,type:r},complete:function(a){var t=JSON.parse(a.responseText),n=e(t,r);o.html(n),o.fadeTo("fast",1)}})}});var o=(a(".lightbox"),a(".overlay")),l=a(".lightbox-container"),c=a(".lightbox-content"),d=a(".close"),h=a(".overlay-animation");s(),a(".overlay-animation").click(function(){s()}),d.click(function(){s()}),a("body").on("click",".lightbox",function(t){t.preventDefault();var e=a(this),n=e.data("lb"),s=e.data("class");o.fadeIn(),r(),null==s&&(s=!1),i(n,s)});var p=!1,f=a("#mobileNav"),m=a(".cl-main-nav");f.click(function(t){if(t.stopPropagation(),p)m.css({transform:"translate(0px, 0px)"}),f.removeClass("active"),p=!1;else{var e=0;e=a(window).width()>450?parseFloat(m.outerHeight())+parseFloat(a(".header-top").outerHeight()):parseFloat(m.outerHeight()),m.css({transform:"translate(0px, "+e+"px)"}),f.addClass("active"),p=!0}}),a("html").click(function(){p&&(m.css({transform:"translate(0px, 0px)"}),f.removeClass("active"),p=!1)});var u=!1,v=a("#searchNav"),x=a(".search-input-container"),g=a("#filterSearch"),b=a(window).width();if(v.click(function(a){a.preventDefault(),b<761?u?(g.submit(),x.css({transform:"translate(0px, 0px)"}),v.removeClass("active"),u=!1):(x.css({transform:"translate("+-1*x.outerWidth()+"px, 0px)"}),v.addClass("active"),u=!0):g.submit()}),a("body").on("click",".accordion-toggle",function(){a(this).toggleClass("active"),a(this).next(".accordion-content").slideToggle(300)}),a(window).scroll(function(){a(this).scrollTop()>2?a("header").addClass("header-scrolled"):a("header").removeClass("header-scrolled")}),a(".announcement").length&&setTimeout(function(){a(".announcement").css({height:"45px"})},1500),a(".announcement .close").click(function(t){t.preventDefault(),a(".announcement").css({height:"0"})}),a(".news-filters").length){var k=window.location.hash;if(k.length>2){var y=k.substring(1),w=y.split("-");if("search"==w[0]){var C=decodeURIComponent(w[1]);a("#filterSearchVal").val(C),a("#filterSearch").submit()}else a("."+y).click()}}a(".resource-search").submit(function(t){t.preventDefault();var e=a(this).data("action"),n=a(this).find("input").val(),s=encodeURIComponent(n);window.location.href=e+"#search-"+s})});