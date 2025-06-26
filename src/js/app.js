$ = jQuery;

jQuery(document).ready(function() {

  // Function to calculate and set the position of dropdown menus
  function adjustDropdownMenuPosition() {
    const headerContainer = document.querySelector('.c-header__container');
    const headerMenuItems = document.querySelectorAll('.c-main-nav__list .c-main-nav__item.--has-children.--is-top-level');

    if (!headerContainer) {
      return;
    }

    if (headerMenuItems.length === 0) {
      return;
    }

    const containerBottom = headerContainer.getBoundingClientRect().bottom;

    headerMenuItems.forEach((li) => {
      const subMenu = li.querySelector('.c-main-nav__sub-menu');

      if (!subMenu) {
        return;
      }

      const { bottom: liBottom, height: liHeight } = li.getBoundingClientRect();
      const distanceFromContainer = containerBottom - liBottom;
      const subMenuPosition = liHeight + distanceFromContainer;

      subMenu.style.top = `${subMenuPosition}px`;
    });
  } 

  if (window.innerWidth >= 1400) {
    adjustDropdownMenuPosition();
  }

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1400) {
      adjustDropdownMenuPosition();
    }
  });
 
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

  $(document).on("keydown", function (event) {
    if ( $('.o-body.--main-nav-open').length ) {
      if (event.key === "Escape") {
        menuOpenClose();
      } 
    }
  });

  $(document).on("keydown", function (event) {
    if ( $('.o-body.--main-nav-open').length ) {
      if (event.key === "Escape") {
        menuOpenClose();
      } 
    }
  });

  /**
   * Tomselect Plugin: "oo_remove_button" (Corrected)
   */
  TomSelect.define('oo_remove_button', function(userOptions) {
      const self = this;

      function esc_html(str) {
          if (!str) return '';
          return (str + '')
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;');
      }

      function getDom(html) {
          const tpl = document.createElement('template');
          tpl.innerHTML = html.trim();
          return tpl.content.firstChild;
      }

      const options = Object.assign({
          label: '&times;',
          title: 'Remove',
          className: 'remove',
          append: true,
          position: 'before',
      }, userOptions);

      if (!options.append) {
          return;
      }

      // Encapsulated logic to add a button to any item element
      const addButtonToItem = (itemElement) => {
          // Prevent adding a button twice
          if (itemElement.querySelector(`.${options.className}`)) return;

          const buttonHTML = `<button class="${options.className}" tabindex="-1" title="${esc_html(options.title)}">${options.label}</button>`;
          const closeButton = getDom(buttonHTML);

          if (options.position === 'before') {
              itemElement.prepend(closeButton);
          } else {
              itemElement.appendChild(closeButton);
          }

          closeButton.addEventListener('mousedown', (evt) => {
              evt.preventDefault();
              evt.stopPropagation();
          });

          closeButton.addEventListener('click', (evt) => {
              if (self.isLocked) return;
              evt.preventDefault();
              evt.stopPropagation();

              const value = itemElement.dataset.value;
              if (value) {
                  self.removeItem(value);
                  self.refreshOptions(false);
              }
          });
      };

      self.on('initialize', () => {
          // Wrap the render function for items added dynamically
          const originalRenderItem = self.settings.render.item;
          self.settings.render.item = (data, escape) => {
              const itemElement = getDom(originalRenderItem.call(self, data, escape));
              addButtonToItem(itemElement);
              return itemElement;
          };

          // Apply the button to items that were already rendered on init
          self.control.querySelectorAll('.ts-item').forEach(itemElement => {
              addButtonToItem(itemElement);
          });
      });
  });

  /**
   * Tom Select Plugin: "oo_checkbox_options"
   */
  TomSelect.define('oo_checkbox_options', function(userOptions) {
      const self = this;
      const orig_onOptionSelect = self.onOptionSelect;

      self.settings.hideSelected = false;

      const options = Object.assign({
          className: "tomselect-checkbox",
          checkedClassNames: undefined,
          uncheckedClassNames: undefined,
      }, userOptions);

      /**
       * Hashes a value for use as a key.
       * @param {string|number|boolean} value
       * @returns {string|number|null}
       */
      function hash_key(value) {
          if (typeof value === 'undefined' || value === null) return null;
          if (typeof value === 'boolean') return value ? '1' : '0';
          return value + '';
      }

      /**
       * Prevents default event behavior and optionally stops propagation.
       * @param {Event} evt
       * @param {boolean} [stop=false]
       */
      function preventDefault(evt, stop = false) {
          if (evt) {
              evt.preventDefault();
              if (stop) {
                  evt.stopPropagation();
              }
          }
      }

      /**
       * Converts an HTML string into a DOM node.
       * @param {string} html
       * @returns {HTMLElement}
       */
      function getDom(html) {
          const tpl = document.createElement('template');
          tpl.innerHTML = html.trim();
          return tpl.content.firstChild;
      }

      /**
       * Toggles checkbox state and associated classes.
       * @param {HTMLInputElement} checkbox
       * @param {boolean} toCheck
       */
      function updateChecked(checkbox, toCheck) {
          checkbox.checked = toCheck;
          if (toCheck) {
              if (options.uncheckedClassNames) checkbox.classList.remove(...options.uncheckedClassNames);
              if (options.checkedClassNames) checkbox.classList.add(...options.checkedClassNames);
          } else {
              if (options.checkedClassNames) checkbox.classList.remove(...options.checkedClassNames);
              if (options.uncheckedClassNames) checkbox.classList.add(...options.uncheckedClassNames);
          }
      }

      /**
       * Updates the checkbox for a given option element.
       * @param {HTMLElement} option
       */
      function updateCheckbox(option) {
          setTimeout(() => {
              const checkbox = option.querySelector(`input.${options.className}`);
              if (checkbox instanceof HTMLInputElement) {
                  updateChecked(checkbox, option.classList.contains('selected'));
              }
          }, 1);
      }

      self.on('initialize', () => {
          const originalRenderOption = self.settings.render.option;

          self.settings.render.option = (data, escape) => {
              const rendered = getDom(originalRenderOption.call(self, data, escape));
              const checkbox = document.createElement('input');

              checkbox.type = 'checkbox';
              checkbox.tabIndex = '-1';
              checkbox.ariaHidden = true;
              checkbox.classList.add(options.className);
              checkbox.addEventListener('click', (evt) => preventDefault(evt));

              // Use the local hash_key function
              const hashedValue = hash_key(data[self.settings.valueField]);
              updateChecked(checkbox, !!(hashedValue && self.items.includes(hashedValue)));

              rendered.prepend(checkbox);
              return rendered;
          };
      });

      self.on('item_remove', (value) => {
          const option = self.getOption(value);
          if (option) {
              option.classList.remove('selected');
              updateCheckbox(option);
          }
      });

      self.on('item_add', (value) => {
          const option = self.getOption(value);
          if (option) {
              updateCheckbox(option);
          }
      });

      self.hook('instead', 'onOptionSelect', (evt, option) => {
          if (option.classList.contains('selected')) {
              option.classList.remove('selected');
              self.removeItem(option.dataset.value);
              self.refreshOptions();
              preventDefault(evt, true); // prevent default and stop propagation
              return;
          }

          orig_onOptionSelect.call(self, evt, option);
          updateCheckbox(option);
      });
  });

  // Tomselect
  if ($('select').length) {
    $('select').each(function() {
      var select = $(this);
      var options = select.find('option');
      var isMultiselect = select.hasClass('--multiple');

      if (options.length === 1) {
          var firstOption = options[0];
          if (firstOption) {
              $(firstOption).prop('selected', true);
          }
      }

      var plugins = {
          'oo_remove_button': {
              'className': 'ts-item-remove',
              'title': 'Remove this item',
              'label': '',
              'position': 'before'
          },
      };

      if (isMultiselect) {
          plugins['oo_checkbox_options'] = {
              'className': 'o-control__input',
              'checkedClassNames': ['ts-checked'],
              'uncheckedClassNames': ['ts-unchecked'],
          };
      }

      new TomSelect(select, {
        itemClass: 'ts-item',
        createOnBlur: false,
        create: false,
        diacritics: true,
        sortField: {
          field: "text",
          direction: "asc"
        },
        plugins: plugins,
        onInitialize: function() {
          let labelText = '';

          // 1. Primary Method: Try to get the label from the original <select>
          if (this.input.labels && this.input.labels.length > 0) {
            labelText = this.input.labels[0].innerText;
          }
          // 2. Fallback Method: If the first fails, try the control input
          else if (this.control_input.labels && this.control_input.labels.length > 0) {
            labelText = this.control_input.labels[0].innerText;
          }

          // 3. Use the found label text if it's not empty
          if (labelText) {
            this.dropdown_content.setAttribute('aria-label', labelText.trim() + ' dropdown');
          }

          // Ensure accessibility for required fields
          if (this.input.required) {
            this.control_input.setAttribute('aria-required', 'true');
          }

          this.control_input.removeAttribute('tabindex');
          // --- Create custom wrappers ---
          const controlInner = document.createElement('div');
          controlInner.classList.add('ts-control-inner');

          const itemsWrapper = document.createElement('div');
          itemsWrapper.classList.add('ts-items');
          this.items_wrapper = itemsWrapper; // Save reference for onItemAdd

          // --- Re-parent existing elements ---

          // Move all initial .ts-item elements into the new wrapper
          this.control.querySelectorAll('.ts-item').forEach(item => {
              itemsWrapper.appendChild(item);
          });

          // Build the new structure within controlInner
          controlInner.appendChild(itemsWrapper);
          controlInner.appendChild(this.control_input); // Move the input as well

          // Replace the control's content with the new structured content
          this.control.appendChild(controlInner);
        },
        onItemAdd: function(value, item) {
          this.items_wrapper.appendChild(item);
        },
        render: {
          option: function(data, escape) {

            if (isMultiselect) {
              return '<div class="ts-dropdown__item o-control" tabindex="0">' +
                        '<span class="ts-dropdown__label o-control__label">' +
                          '<span class="ts-dropdown__text o-control__text">' + escape(data.text) + '</span>' +
                        '</span>' +
                      '</div>';
            } else {
              return '<div class="ts-dropdown__item" tabindex="0">' +
                        '<span class="ts-dropdown__label">' +
                          '<span class="ts-dropdown__text">' + escape(data.text) + '</span>' +
                        '</span>' +
                      '</div>';
            }

          },
          no_results:function(data,escape){
            return '<div class="no-results">Keine Ergebnisse</div>';
          },
        }
      });
    });
  }

  // Textarea auto expand
  const textareas = document.querySelectorAll('.o-textarea');

  if( textareas.length > 0 ) {
    textareas.forEach(textarea => {
      textarea.addEventListener('input', () => {
        textarea.style.height = 'auto';
        const borderOffset = textarea.offsetHeight - textarea.clientHeight;
        textarea.style.height = `${textarea.scrollHeight + borderOffset}px`;
      });
    });
  }

  // Read more
  const buttonMore = document.querySelectorAll('.c-read-more');

  if( buttonMore.length > 0 ) {
    buttonMore.forEach(button => {
      let openClass = '--is-open';
      const moreContainer = button.previousElementSibling;
      const parentContainer = button.parentNode;

      button.addEventListener('click', (e) => {
        e.preventDefault();

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

  // Function to apply text shortening, read-more button and visibility adjustments based on word count and screen size
  applyResponsiveTextShortening();


  // Accordion
   if ( $('.c-accordion').length > 0) {
    const details = document.querySelectorAll('details');
    details.forEach(detail => {
      detail.style.height = (detail.querySelector('summary').offsetHeight + 4) + 'px';
      detail.style.transition = 'height 0.3s ease';

      detail.addEventListener('toggle', () => {
        const srOpen = detail.querySelector('.u-screen-reader-only.--open');
        const srClose = detail.querySelector('.u-screen-reader-only.--close');
        if (detail.open) {
          detail.style.height = (detail.scrollHeight + 2) + 'px';
          srOpen.setAttribute('aria-hidden', 'true');
          srClose.setAttribute('aria-hidden', 'false');

        } else {
          detail.style.height = (detail.querySelector('summary').offsetHeight + 2) + 'px';
          srOpen.setAttribute('aria-hidden', 'false');
          srClose.setAttribute('aria-hidden', 'true');
        }
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
  const openPopup = document.querySelectorAll('.--open-popup');
  const popUpHeadline = document.querySelectorAll('.c-popup__headline');

  // Trim Popup Headine
  if (popUpHeadline.length > 0) {
    popUpHeadline.forEach(function(headline) {
      const originalText = headline.textContent;
      const lineHeight = parseFloat(window.getComputedStyle(headline).lineHeight);
      const maxHeight = lineHeight * 5;

      let truncatedText = originalText;
      headline.textContent = truncatedText;

      while (headline.scrollHeight > maxHeight) {
        truncatedText = truncatedText.slice(0, -1);
        headline.textContent = truncatedText + '…';
      }
    });
  }

  if (openPopup.length > 0) {
    openPopup.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        // Get the ID of the popup from the data-popup attribute
        const popupId = button.getAttribute('data-popup');
        const popupElement = document.getElementById(popupId);

        if (!popupElement) {
          return;
        }

        if (popupElement) {
          popupElement.classList.add('--is-open');
          document.body.classList.add('--modal-open');

          function closePopup() {
            popupElement.classList.remove('--is-open');
            document.body.classList.remove('--modal-open');
          }

          // Close popup with click on icon or overlay
          const overlay = popupElement.querySelector('.c-popup__overlay');
          const closeButton = popupElement.querySelector('.c-popup__close');

          if (overlay) {
            overlay.addEventListener('click', closePopup);
          }
          if (closeButton) {
            closeButton.addEventListener('click', closePopup);
          }

          // Close popup on press ESC
          document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
              closePopup();
            }
          });
        }
      });
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
          <svg class="c-lightbox__icon --arrow" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <button class="gnext gbtn c-lightbox__icon-wrapper --arrow --next" tabindex="1" aria-label="Previous">
          <svg class="c-lightbox__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
    </div>`;
    
    let customSlideHTML = 
    `<div class="gslide c-lightbox__slide">
      <div class="gslide-inner-content c-lightbox__wrapper">
        <div class="ginner-container c-lightbox__content">
          <div class="gslide-media c-lightbox__media">
            <button class="gclose gbtn c-lightbox__icon-wrapper --close" tabindex="2" aria-label="Close">
              <svg class="c-lightbox__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
      loop: true,
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

  function updateTabIndex() {
    document.querySelectorAll('.splide__slide').forEach(slide => {
      slide.tabIndex = slide.classList.contains('is-active') ? 0 : -1
    });
    document.querySelectorAll('.splide__slide--clone').forEach(slide => {
      slide.tabIndex = -1
    });
  }


  if( sliders.length > 0 ) {
      sliders.forEach(function(slider) {

      const splide = new Splide(slider);
      let outerslider = false; 
      
      if (slider.classList.contains("--is-properties-slider") || slider.classList.contains("--is-properties-similar-slider"))
      {
        outerslider = true;
      }
      if( outerslider) {

        const btnNextOuter = slider.querySelector(".c-slider__navigation:not(.--is-properties-images-slider) .c-slider__arrow.--next");
        const btnPrevOuter = slider.querySelector(".c-slider__navigation:not(.--is-properties-images-slider) .c-slider__arrow.--prev");

        btnNextOuter.addEventListener('click', e => {
          splide.go('+1')
        })
      
        btnPrevOuter.addEventListener('click', e => {
          splide.go('-1')
        })
      }
   
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
          focusableNodes: 'a, button, input, textarea, select:not([aria-hidden])'
        };
      });
      splide.on('mounted moved', () => requestAnimationFrame(updateTabIndex));
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

  if (scrollBackToTop) {
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
  }
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
  // Function to apply text shortening, read-more button and visibility adjustments based on word count and screen size
  applyResponsiveTextShortening();
});

// Global variables
const doc = document.documentElement;

// Menu open & close
function menuOpenClose() {
  $("body").toggleClass("--main-nav-open");
  $(".c-header").toggleClass("--main-nav-open");
  $(".c-main-nav").toggleClass("--is-open");
  $(".c-main-nav__item").toggleClass("--is-open");
  $(".c-main-nav__button .--open").toggle();
  $(".c-main-nav__button .--close").toggle();
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

// Function to apply text shortening, read-more button and visibility adjustments based on word count and screen size
function applyResponsiveTextShortening() {
  const isMobile = window.innerWidth <= 768;

  function shortenElements(elementsEach, textElement, elementToShorten) {
    $(elementsEach).each(function() {
      const text = $(this).find(textElement).text();
      const wordCount = text.trim().split(/\s+/).length;

      let shouldShorten;
      if (isMobile) {
        shouldShorten = wordCount > 50;
      } else {
        shouldShorten = wordCount > 100;
      }

      if (shouldShorten) {
        $(this).find(elementToShorten).addClass('--shorten');
        $(this).find('.c-read-more').show();
      } else {
        $(this).find(elementToShorten).removeClass('--shorten');
        $(this).find('.c-read-more').hide();
      }
    });
  }

  // google review slider
  shortenElements('.c-google-review-card', '.c-google-review-card__text p', '.c-google-review-card__contents');
  // review slider
  shortenElements('.c-review-card', '.c-review-card__text', '.c-review-card__text');
  // property list
  shortenElements('.c-property-details__text-wrapper', '.c-property-details__text-content', '.c-property-details__text-content');
  // team
  shortenElements('.c-team-card', '.c-team-card__description', '.c-team-card__description');
}

// Select2 copy class
function select2CopyClasses(data, container) {
  if (data.element) {
      $(container).addClass($(data.element).attr("data-level"));
  }
  return data.text;
}