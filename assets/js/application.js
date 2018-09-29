jQuery(document).ready(function($) {

	///////////////
	// Filtering //
	///////////////

	// Filter Dropdown
	$('.news-tax-label').click(function() {
		$(this).parent().siblings('.news-tax').find('.news-tax-label').each(function() {
			var listDropDown = $(this).next();
			if ($(this).hasClass('displayed')) {
				$(this).removeClass('displayed');
				$(this).next().slideUp(300);
			}
		});
		
		var listDropDown = $(this).next();
		if ($(this).hasClass('displayed')) {
			$(this).removeClass('displayed');
			listDropDown.slideUp(300);
		} 
		else {
			$(this).addClass('displayed');
			listDropDown.slideDown(300);
		}
	});

	// Archive Filtering
	// Pagination
	$('.next-link').click(function(e) {
		e.preventDefault();
		var el = $(this),
			next = parseInt(el.data('next')),
			ppp = el.data('ppp'),
			max = el.data('max'),
			tax = el.data('tax'),
			type = el.data('type'),
			action = el.data('action');
        
		$.ajax({
			type: 'GET',
			url: action,
			data: {next:next, ppp:ppp, tax:tax, type:type},
			complete: function(response) {
				el.data('next', next + 1);
				
				if (next + 1 > max) {
					el.addClass('disabled');
				}
				else{
					el.removeClass('disabled');
				}
				var newsData = JSON.parse(response.responseText);
				var newsHTML = dataLoop(newsData, type);
				// $('.archive-container').append(newsHTML);
				$('.empty').remove();
				el.parent().siblings('.archive-container').append(newsHTML);
				el.parent().siblings('.archive-container').append('<a class="card empty" href="#"></a><a class="card empty" href="#"></a><a class="card empty" href="#"></a>');
			}
		});
	});

	// Categories
	$('.news-tax-list li a').click(function(e) {
		e.preventDefault();

		var el = $(this),
			tax = el.data('tax'),
			term = el.data('term'),
			type = el.data('type'),
			nextLink = $('.full-archive.' + type).find('.next-link');

		$('.news-tax-label').each(function() {
			$(this).next().slideUp(300);
			$(this).removeClass('displayed');
		});

		var container = el.closest('.news-tax');
		if (term == 'all') {
			container.find('.news-tax-label').text(el.data('all'));
		} 
		else {
			container.find('.news-tax-label').text(el.text());
		}

		var currentFilter = nextLink.data('tax');
		currentFilter[tax] = [term];
		nextLink.data('tax', currentFilter);

    console.log('test');
    console.log(nextLink);

		taxonomyFilter(nextLink);
	});

	function taxonomyFilter(nextLink){
		var ppp = nextLink.data('ppp'),
			action = nextLink.data('action'),
			tax = nextLink.data('tax'),
			type = nextLink.data('type');
		var archive = nextLink.parent().siblings('.archive-container');

		archive.fadeTo('fast', 0);

		$.ajax({
			type: 'GET',
			url: action,
			data: {tax:tax, ppp:ppp, type:type},
			complete: function(response) {
				var newsData = JSON.parse(response.responseText);

				nextLink.data('next', 2);
				nextLink.data('max', newsData['max']);

				if (2 > newsData['max']) {
					nextLink.addClass('disabled');
				}
				else{
					nextLink.removeClass('disabled');
				}

				var newsHTML = dataLoop(newsData, type);
				archive.html(newsHTML);
				archive.append('<a class="card empty" href="#"></a><a class="card empty" href="#"></a><a class="card empty" href="#"></a>');
				archive.fadeTo('fast', 1);
			}
		});
	}

	// Search
	$('.filter-search').submit(function(e) {
		e.preventDefault();

		var el = $(this);
		var type = el.data('type');
		var searchTerm = el.find('.news-search').val();
		if(searchTerm.length > 1){
			
			var nextLink = $('.full-archive.' + type).find('.next-link');
			var action = nextLink.data('action');
			nextLink.addClass('disabled');

			$('.news-tax-label').each(function() {
				var listDropDown = $(this).next();
				if ($(this).hasClass('displayed')) {
					$(this).removeClass('displayed');
					$(this).next().slideUp(300);
				}
				$(this).text($(this).data('all'));
			});
			var archive = nextLink.parent().siblings('.archive-container');
			archive.fadeTo('fast', 0);

			$.ajax({
				type: 'GET',
				url: action,
				data: {s:searchTerm, type:type},
				complete: function(response) {

					var newsData = JSON.parse(response.responseText);
					var newsHTML = dataLoop(newsData, type);

					archive.html(newsHTML);
					archive.fadeTo('fast', 1);
				}
			});
		}
	});

	function dataLoop(newsData, type){
		var newsHTML = '';
		for (var i = 0; i <= newsData['data'].length-1; i++) {
			switch(type) {
			    case 'sermons':
			    case 'events':
					var newsTemplateData = {
						"image": newsData['data'][i].image,
						"title": newsData['data'][i].title,
						"permalink": newsData['data'][i].permalink,
						"date": newsData['data'][i].date
					};
					var htmlData = getTemplateHTML(newsTemplateData, type);
			        break;
			}
			newsHTML += htmlData;
		}
		return newsHTML;
	}

	function getTemplateHTML(data, type) {
		switch(type) {
		    case 'sermons':
		    case 'events':
				var htmlTemplate = $('.card').first().clone();

				htmlTemplate.attr('href', data.permalink);
				htmlTemplate.find('.card-image').attr('style', 'background-image: url(' + data.image + '); background-repeat: no-repeat; background-position: center center; background-size: cover;');
				htmlTemplate.find('.card-content .date').text(data.date);
				htmlTemplate.find('.card-content .post-title').text(data.title);

		        break;

		    default:
		        return false;
		        break;
		}
		var html = $('<div>').append(htmlTemplate.clone()).html();
		return html;
	}

	//////////////////////
	// Lightbox overlay //
	//////////////////////

	var lightboxButtons = $('.lightbox'),
		overlay = $('.overlay'),
		lightboxContainer = $('.lightbox-container'),
		lightboxContent = $('.lightbox-content'),
		closeButton = $('.close'),
		overlayGrid = $('.overlay-animation');

	lightboxReset();

	$('.overlay-animation').click(function() {
		lightboxReset();
	});

	closeButton.click(function() {
		lightboxReset();
	});

	$('body').on('click', '.lightbox', function(e) {
		e.preventDefault();
		var button = $(this);
		var lightboxData = button.data("lb");
		var lightboxClass = button.data("class");
		overlay.fadeIn();
		overlayAnimate();
		if (lightboxClass == null){
			lightboxClass = false;
		}
		lightboxDisplay(lightboxData, lightboxClass);
	});

	function lightboxReset() {
		overlay.fadeOut();
		lightboxContainer.fadeOut();
		lightboxContainer.css({"height": "auto"});
		var maxAnimation = 1200,
			minAnimation = 500,
			maxOpacity = 85,
			minOpacity = 75;

		overlayGrid.each(function() {
			var animTime = Math.floor(Math.random() * (maxAnimation - minAnimation + 1)) + minAnimation;
			var opacityRange = Math.floor(Math.random() * (maxOpacity - minOpacity + 1)) + minOpacity;

			$(this).css({
				"-webkit-transition": "-webkit-transform " + animTime + "ms ease",
				"-moz-transition": "-moz-transform " + animTime + "ms ease",
				"-o-transition": "-o-transform " + animTime + "ms ease",
				"transition": "transform " + animTime + "ms ease",
				"-webkit-transform": "translate(0, 100vh)",
				"-moz-transform": "translate(0, 100vh)",
				"-ms-transform": "translate(0, 100vh)",
				"-o-transform": "translate(0, 100vh)",
				"transform": "translate(0, 100vh)",
				"background-color": "rgba(35,31,32," + opacityRange / 100 + ")"
			});
		});
	};

	function overlayAnimate() {
		overlayGrid.each(function() {
			$(this).css({
				"-webkit-transform": "translate(0, 0vh)",
				"-moz-transform": "translate(0, 0vh)",
				"-ms-transform": "translate(0, 0vh)",
				"transform": "translate(0, 0vh)"
			});
		});
	};

	function lightboxDisplay(content, lightboxClass) {
		var windowHeight = $(window).height();
		lightboxContent.html($('#' + content).clone()).html();
		if (lightboxClass){
			lightboxContainer.addClass(lightboxClass);
		}
		setTimeout(function(){
			lightboxContainer.fadeIn();
			var lightboxContainerHeight = lightboxContainer.outerHeight();
			if (lightboxContainerHeight > windowHeight - 30) {
				lightboxContainer.css({
					"height": windowHeight - 30,
					"margin-top": 15
				});
			}
			else {
				lightboxContainer.css({
					"margin-top": (windowHeight / 2) - (lightboxContainerHeight / 2)
				});
			};
		}, 600);
	};

	/////////////////
	// Mobile Menu //
	/////////////////

	var breakPoint = 1200;

	var mobileNav = $('#mobileNav');
	var navSlide = $('.main-nav');
	var mobileOpenState = false;

	mobileNav.click(function(e) {
		e.stopPropagation();
		if (mobileOpenState) {
			navSlide.css({"transform":"translate(0px, 0px)"});
			mobileNav.removeClass('active');
			mobileOpenState = false;
		} 
		else{
			navSlide.css({"transform":"translate(" + (-1 * navSlide.outerWidth()) + "px, 0px)"});
			mobileNav.addClass('active');
			mobileOpenState = true;
		};
	});

	$('#close').click(function(e) {
		if (mobileOpenState) {
			navSlide.css({"transform":"translate(0px, 0px)"});
			mobileNav.removeClass('active');
			mobileOpenState = false;
		}
	});

	$('html').click(function() {
		if (mobileOpenState) {
			navSlide.css({"transform":"translate(0px, 0px)"});
			mobileNav.removeClass('active');
			mobileOpenState = false;
		}
	});

	$(window).resize(function() {
		if($(window).width() > breakPoint){
			if (mobileOpenState) {
				navSlide.css({"transform":"translate(0px, 0px)"});
				mobileNav.removeClass('active');
				mobileOpenState = false;
			}
		}
	});

	///////////////////
	// Mobile Search //
	///////////////////

	var searchNav = $('.search-nav');
	var searchSlide = $('.search-input-container');
	var searchForm = $('.filter-search');

	var windowWidth = $(window).width();

	searchNav.click(function(e) {
		e.preventDefault();
		var el = $(this);
		var searchForm = el.parent();
		var searchSlide = searchForm.find('.search-input-container');
		if (windowWidth < 761) {
			if (el.hasClass('active')) {
				searchForm.submit();
				searchSlide.css({"transform":"translate(0px, 0px)"});
				searchNav.removeClass('active');
			} 
			else {
				searchSlide.css({"transform":"translate(" + (-1 * searchSlide.outerWidth()) + "px, 0px)"});
				searchNav.addClass('active');
			}
		}
		else{
			searchForm.submit();
		}
	});

    ///////////////
    // Accordion //
    ///////////////

	$('body').on('click', '.accordion-toggle', function() {
		$(this).toggleClass('active');
		$(this).next('.accordion-content').slideToggle(300);
	});

	///////////////////
	// Header Scroll //
	///////////////////

	$(window).scroll(function() {
		if( $(this).scrollTop() > 2 ) {
			$('header').addClass('header-scrolled');
		} 
		else {
			$('header').removeClass('header-scrolled');
		}
	});

    //////////////////////
    // Announcement Bar //
    //////////////////////

    if ($('.announcement').length) {
        setTimeout(function(){
        	$('.announcement').css({"height":"60px"});
        }, 1000);
    }

    $('.announcement .close').click(function(e){
    	e.preventDefault();
        $('.announcement').css({"height":"0"});
    });

    //////////////////
    // Deep Linking // 
    //////////////////

    if ($('.news-filters').length){
    	var hash = window.location.hash;
    	if (hash.length > 2){
    		var query = hash.substring(1);
    		var searchTest = query.split('-');
    		if(searchTest[0] == 'search'){
    			var searchTerm = decodeURIComponent(searchTest[1]);
    			$('.news-search').val(searchTerm);
    			$('.filter-search').submit();
    		}
    		else{
    			$('.' + query).click();
    		}
    	}
    }

    $('.resource-search').submit(function(e){
    	e.preventDefault();
    	var archive = $(this).data('action');
    	var searchTerm = $(this).find('input').val();
    	var encodedTerm = encodeURIComponent(searchTerm);
    	window.location.href = archive + '#search-' + encodedTerm;
    });
});
