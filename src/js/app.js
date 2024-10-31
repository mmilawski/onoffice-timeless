$ = jQuery;

jQuery(document).ready(function() {

  if( $('.c-table').length > 0  ) {
    $('.c-table').each(function( index, element) {

      let table = $(this);
      let tableWidth = table.find('.c-table__wrapper').outerWidth();
      let tableContainer = table.outerWidth();

      if( tableWidth > tableContainer ) {
        table.addClass('--is-scrollable');
      } else {
        table.removeClass('--is-scrollable');
      }
    });
  }


  // Menu open & close
  $(".c-main-nav__button").on("click tap", function () {
    menuOpenClose();
  });

  // Select2
  if ($('select').length) {
    $('select').each(function() {
      var select = $(this);
      var parent = select.closest('.o-main');
      var options = select.find('option');
      if (options.length === 1) {
          var firstOption = options[0];
          if (firstOption) {
              $(firstOption).prop('selected', true);
          }
      }

      if (select.hasClass('--multiple')) {
        var clear = false;
      } else {
        var clear = true;
      }

      select.select2({
        minimumResultsForSearch: Infinity,
        language: { noResults: () => "Keine Ergebnisse"},
        dropdownParent: parent,
        allowClear: clear,
        dropdownAutoWidth: true,
        templateResult: select2CopyClasses,
        templateSelection: select2CopyClasses,
      });

      if ($('select.--is-styled').length) {
        $('select.--is-styled').on("select2:select", function(e) {
            var selectedID = e.params.data.id;
            var selectedOptions = $(this).find('[value=' + selectedID + ']');
            $(selectedOptions).each(function() {
                var selectedLevel = $(this).attr('data-level');
                var currentNode = $(this).next();
                while ( (currentNode.attr('data-level')) > selectedLevel) {
                    currentNode.prop('selected', true);
                    currentNode = currentNode.next();
                }
            });
            $(this).trigger('change');
        });

        $('select.--is-styled').on('select2:unselect', function(e) {
            var selectedID = e.params.data.id;
            var selectedOptions = $(this).find('[value=' + selectedID + ']');
            $(selectedOptions).each(function() {
                var selectedLevel = $(this).attr('data-level');
                var selectSiblings = $(this).nextUntil("[data-level='" + selectedLevel + "']");
                $(selectSiblings).prop('selected', false);
            });
            $(this).trigger('change');
        });
      }
    });
  }


  // Select2
  $('input[name="geo_search"]').select2({
    minimumInputLength: 4,
    ajax: {
      url: `https://nominatim.openstreetmap.org/search`,
      type: "GET",
      allowClear: true,
      data: function (params) {
        let addCountryFilter = (!!window.geoFilter && !!window.geoFilter.country && window.geoFilter.country.length > 0)? " ,"+window.geoFilter.country : "";
        let query = {
          q: params.term+addCountryFilter,
          format: 'geojson'
        }
        return query;
      },
      processResults: function (data) {
        let results = data.features.map(x => {
          return {
            text: x.properties.display_name,
            id: encodeURI(x.geometry.coordinates)
          };
        });
        return {results};
      },
      language: document.getElementsByTagName('html')[0].getAttribute('lang')
    },
  });
  if ($('input[name="geo_search"]').length) {
    $('input[name="geo_search"]').on("select2:select", function(e) {
      if($('input[name="geo_search"]').parent().find('.select2-selection__rendered').length) {
        $('input[name="geo_search"]').parent().find('.select2-selection__rendered')[0].innerHTML = e.params.data.text;
        $('input[name="geo_search_text"]')[0].value = e.params.data.text;
      }
    });
    $('input[name="geo_search"]').on("select2:open", function(e) {
      $('input[name="geo_search"]')[0].value = "";
      $('input[name="geo_search"]').parent().find('.select2-selection__rendered')[0].innerHTML = "";
      $('input[name="geo_search_text"]')[0].value = "";
    });
  }
  //preselect
  if ($('input[name="geo_search_text"]').length) {
    if($('input[name="geo_search"]').parent().find('.select2-selection__rendered').length) {
      $('input[name="geo_search"]').parent().find('.select2-selection__rendered')[0].innerHTML = $('input[name="geo_search_text"]')[0].value;
    }
  }

  // Read more
  const buttonMore = document.querySelectorAll('.--read-more');

  if( buttonMore.length > 0 ) {
    buttonMore.forEach(button => {
      let openClass = '--is-open';
      const moreContainer = button.previousElementSibling;
      const parentContainer = button.parentNode;

      button.addEventListener('click', () => {
        if (!moreContainer.classList.contains(openClass)) {
          moreContainer.classList.add(openClass);
          button.classList.add(openClass);
        } else {
          moreContainer.classList.remove(openClass);
          button.classList.remove(openClass);
          parentContainer.scrollIntoView({ behavior: 'smooth'});
        }
      });
    });
  }

  // Accordion
   if ( $('.c-accordion').length > 0) {
    $('.c-accordion-card').each(function() {
      const accordion = $(this);
      const accordionTitle = accordion.find('.c-accordion-card__title');
      const accordionContent = accordion.find('.c-accordion-card__content');
        $(accordionTitle).click(function() {
            $(accordionContent).slideToggle('1500', function() {
              $(accordion).toggleClass('--is-open --is-closed').trigger('classChanged');
            });
        });
    });
    // after map accordion is opened, resize osm map
    $('.c-accordion-card.--is-map').on(
      "classChanged", (e) => {
        let osm = $(e.target).find("div.--is-open-street-map");
        if(!osm || !osm[0]) return;
        let lId = osm[0].getAttribute("lid");
        let map = window.maps.find(x => x._leaflet_id == lId);
        if(!!map)
          map.invalidateSize(true);
      }
    );
  }

    // Open popup
  if ($('.--open-popup').length > 0) {
      $('.--open-popup').on('click', function(e) {
          e.preventDefault();
          const popup = $(this).parent().find('.c-popup');
          popup.toggleClass('--is-open');
      });
  
      // Close popup with click on icon or overlay
      $('.c-popup__overlay, .c-popup__close').on('click', function() {
          const popup = $(this).closest('.c-popup');
          if (!popup.hasClass('--is-widget')) {
              popup.removeClass('--is-open');
          }
      });
  
      // Close popup on press ESC
      $(document).keyup(function(e) {
          if (e.key === "Escape") {
              $('.c-popup.--is-open').each(function() {
                  if (!$(this).hasClass('--is-widget')) {
                      $(this).removeClass('--is-open');
                  }
              });
          }
      });
  }

  // Lightbox
  const lightboxClass = document.querySelectorAll('.glightbox');

  if( lightboxClass.length > 0 ) {
    const customLightboxHTML = 
    `<div id="glightbox-body" class="glightbox-container c-lightbox">
      <div class="gloader visible c-lightbox__loader"></div>
      <div class="goverlay c-lightbox__overlay"></div>
      <div class="gcontainer c-lightbox__container">
        <div id="glightbox-slider" class="gslider c-lightbox__slider"></div>
        <button class="gprev gbtn c-lightbox__icon-wrapper --arrow --prev" tabindex="0" aria-label="Next">
          <svg class="c-lightbox__icon --arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </button>
        <button class="gnext gbtn c-lightbox__icon-wrapper --arrow --next" tabindex="1" aria-label="Previous">
          <svg class="c-lightbox__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
        </button>
      </div>
    </div>`;
    
    let customSlideHTML = 
    `<div class="gslide c-lightbox__slide">
      <div class="gslide-inner-content c-lightbox__wrapper">
        <div class="ginner-container c-lightbox__content">
          <div class="gslide-media c-lightbox__media">
            <button class="gclose gbtn c-lightbox__icon-wrapper --close" tabindex="2" aria-label="Close">
              <svg class="c-lightbox__icon" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14"><path d="M1,13L13,1 M1,1l12,12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"></path></svg>
            </button>
          </div>
          <div class="gslide-description c-lightbox__description-wrapper">
            <div class="gdesc-inner c-lightbox__description-inner">
              <p class="gslide-title c-lightbox__title"></p>
              <div class="gslide-desc c-lightbox__description"></div>
            </div>
          </div>
        </div>
      </div>
    </div>`;
    
    const lightbox = GLightbox({
      moreLength: 0,
      zoomable: false,
      skin: 'custom',
      lightboxHTML: customLightboxHTML,
      slideHTML: customSlideHTML
    });

    lightbox.on('open', () => {
      const closeElements = document.querySelectorAll('.gclose');
      closeElements.forEach(closeButton => {
        closeButton.addEventListener("click", function(e){ 
          lightbox.close();
        });
      });
    });
  }

  // Media
  if ($('.c-media__iframe, .c-media-text__iframe').length > 0 ) {
    const iframeVideos = $('[class*="__video"]');

    iframeVideos.each(function() {
        const video = $(this); 
        const iframe = video.find('[class*="__iframe"]');
        const thumbnail = video.find('[class*="__thumbnail-wrapper"]');

        if( thumbnail.length > 0 ) {
          thumbnail.on('click', function(e) {
            thumbnail.hide();
            iframe.attr('src', iframe.attr('data-src') ).show();
          });
        }
    });
  }  

  // Correct banner video ratio
  const bannerVideos = document.querySelectorAll('.c-banner__video-wrapper');
  if( bannerVideos.length > 0 ) {
    bannerVideos.forEach(video => {
      const iframe = video.querySelector('[class*="__video"]');
      const videoHeight = video.offsetHeight;
      const videoWidth = video.offsetWidth;
      const ratioHeight = Math.ceil(videoWidth * 9 / 16);
      const ratioWidth = Math.ceil(videoWidth * 16 / 9);
      if (ratioHeight > videoHeight) {
        iframe.style.setProperty('--video-ratio-width', videoWidth+'px');
        iframe.style.setProperty('--video-ratio-height', ratioHeight+'px');
      } else {
        iframe.style.setProperty('--video-ratio-width', ratioWidth+'px');
        iframe.style.setProperty('--video-ratio-height', videoHeight+'px');
      }
    });
  }

  // Masonry
  if( $('.--is-masonry').length > 0 ) {
    // Modules: Seals
    $('.c-seals.--is-masonry').each(function(){
      var $gallery = $(this);
      var updateMasonry = function(){
        $gallery.masonry('layout');
      };
    
      this.addEventListener('load', updateMasonry, true);
    
      $gallery.masonry({
        gutter: 8
      });
    });
    // Components: Gallery
    $('.c-gallery .--is-masonry').each(function() {
      var $gallery = $(this);

      var initializeMasonry = function() {
        if (!$gallery.data('masonry')) {
          $gallery.masonry({
            gutter: 8
          });
        }
      };

      var updateMasonry = function() {
        if (window.matchMedia("(min-width: 768px)").matches) {
          // Destroy Masonry on desktop
          if ($gallery.data('masonry')) {
            $gallery.masonry('destroy');
            $gallery.removeData('masonry');
          }
        } else {
          // Initialize Masonry on non-desktop
          initializeMasonry();
        }
      };

      this.addEventListener('load', updateMasonry, true);
      window.addEventListener('resize', updateMasonry);

      // Initial check
      updateMasonry();
    });
  }

  // Slider
  const sliders = document.querySelectorAll('.c-slider');

  if( sliders.length > 0 ) {
    sliders.forEach(function(slider) {
      const splide = new Splide(slider);

      // progress bar animation
      const bar = slider.querySelector('.c-slider__progress-bar');
      if (bar) {
        splide.on('mounted move', function() {
          const end = splide.Components.Controller.getEnd() + 1;
          bar.style.width = String(100 * (splide.index + 1) / end) + '%';
        });
      }
      // remove drag when not enough slides
      splide.on( 'overflow', function ( isOverflow ) {
        splide.options = {
          drag : isOverflow,
        };
      });
      splide.mount();
    });
  }

  // Toogle Property Card
  const propertyFeatureOpenerButton = $('.c-property-details__more');

  if( propertyFeatureOpenerButton.length > 0 ) {
    propertyFeatureOpenerButton.on('click', function(e) {
      // Button 
      let button = propertyFeatureOpenerButton;
      let activeClass = '--is-active';
      
      // Button Text 
      let buttonOpenText = button.attr('data-open-text');
      let buttonCloseText = button.attr('data-close-text');

      // Features Opener 
      let featureListItem = $('.c-property-details__features-items.--is-toggle');
 
      if( button.hasClass(activeClass) ) {
        // Close Items
        button.removeClass(activeClass);
        featureListItem.slideUp({
          'duration': 200, 
          'start': function() {
            $('html,body').animate({
              scrollTop: $(featureListItem).offset().top - 150
            }, 200); 
          }
        });
        button.text(buttonOpenText);

      } else {
        // Open Items
        $(button).addClass(activeClass);
        featureListItem.slideDown(200);
        button.text(buttonCloseText);
      }

    });
  }
  // Toogle Property Card
  const addressFeatureOpenerButton = $('.c-address-details__more');

  if( addressFeatureOpenerButton.length > 0 ) {
    addressFeatureOpenerButton.on('click', function(e) {
      // Button 
      let button = addressFeatureOpenerButton;
      let activeClass = '--is-active';
      
      // Button Text 
      let buttonOpenText = button.attr('data-open-text');
      let buttonCloseText = button.attr('data-close-text');

      // Features Opener 
      let featureListItem = $('.c-address-details__info-table.--is-toggle');
 
      if( button.hasClass(activeClass) ) {
        // Close Items
        button.removeClass(activeClass);
        featureListItem.slideUp({
          'duration': 200,
        });
        button.text(buttonOpenText);
        featureListItem.removeClass('--is-open');

      } else {
        // Open Items
        $(this).addClass(activeClass);
        featureListItem.slideDown({
          'duration': 200, 
          'start': function() {
            featureListItem.css('display', 'grid');
          },
        });
        button.text(buttonCloseText);
        featureListItem.addClass('--is-open');
      }

    });
  }

  // Correct padding for first element in main
  correctFirstElementPadding();

  // Scroll Back To Top Button
  const scrollBackToTop = document.querySelector('.c-back-to-top');
  scrollBackToTop.onclick = () => {
    window.scroll({
      top: 0,
      behavior: 'smooth'
    });
  }

  window.addEventListener('scroll', () => {
    let scrollPosition = window.scrollY;
    if (scrollPosition > 200) {
      scrollBackToTop.classList.add('--visible');
    } else {
      scrollBackToTop.classList.remove('--visible');
    }
 });
});

// Fixed Header on scroll
jQuery(window).on('scroll', function(e) {
  const header  = $(".c-header"); 
  const mainContainer = $('.o-main');
  const navheight = header.outerHeight();
  const scroll = $(window).scrollTop();

  if ( scroll > navheight) {
    header.addClass("--fixed");
    mainContainer.addClass("--header-fixed");
    
  } else {
    header.removeClass("--fixed");
    mainContainer.removeClass("--header-fixed");
  }
  
});

jQuery(window).on('load', function(){
});

jQuery(window).on('resize', function(){
  // Menu close on resize
  if ( $('.o-body.--main-nav-open').length ) {
      menuOpenClose();
  };

  // Correct padding for first element in main if header
  correctFirstElementPadding();
});

// Global variables
const doc = document.documentElement;

// Menu open & close
function menuOpenClose() {
  $("body").toggleClass("--main-nav-open");
  $(".c-header").toggleClass("--main-nav-open");
  $(".c-main-nav").toggleClass("--is-open");
  $(".c-main-nav__item").toggleClass("--is-open");
  $(".c-main-nav__button-icon.--open").toggle();
  $(".c-main-nav__button-icon.--close").toggle();
}

// Correct padding for first element in main
function correctFirstElementPadding() {
  const body = document.body;
  const header = document.getElementsByClassName('c-header'); 

  if (header.length === 0) {
    body.style.removeProperty('--header-height');
    return;
  }

  let headerHeight = header[0].offsetHeight;
  let headerHeightRounded = Math.round(headerHeight);

  let firstElement = document.querySelector('.o-main > :first-child');
  if (!firstElement) {
    body.style.removeProperty('--header-height');
    return;
  }

  let firstElementPaddingTop = 0;

  if(firstElement.classList.contains('c-banner')) {
    firstElement = firstElement.querySelector('.c-banner__slide');
    firstElementPaddingTop = parseInt(window.getComputedStyle(firstElement, null).getPropertyValue('padding-top'));
  } else if(firstElement.classList.contains('c-property-details')) {
    firstElement = firstElement.querySelector('.c-property-details__banner-wrapper');
    firstElementPaddingTop = parseInt(window.getComputedStyle(firstElement, null).getPropertyValue('padding-top'));
  } else if(firstElement.classList.contains('c-news-details')) {
    let firstChildElement = firstElement.querySelector(':first-child');
    if(firstChildElement.classList.contains('c-news-details__info')) {
      firstElementPaddingTop = parseInt(window.getComputedStyle(firstChildElement, null).getPropertyValue('padding-top'));
    }
  } else if(firstElement.classList.contains('o-section')) {
    firstElementPaddingTop = parseInt(window.getComputedStyle(firstElement, null).getPropertyValue('padding-top'));
  }

  if (firstElementPaddingTop === 0 && headerHeightRounded <= firstElementPaddingTop) {
    body.style.removeProperty('--header-height');
    return;
  }

  body.style.setProperty('--header-height', `${headerHeightRounded}px`);
  
}

// Select2 copy class
function select2CopyClasses(data, container) {
  if (data.element) {
      $(container).addClass($(data.element).attr("data-level"));
  }
  return data.text;
}
