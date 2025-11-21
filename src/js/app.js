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
          title: window.ooTimelessTheme.translations.removeThisItem || 'Remove this item',
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
              'title': window.ooTimelessTheme.translations.removeThisItem || 'Remove this item',
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

      const is_regionaler_zusatz = select.length && select[0].id === 'regionaler_zusatz';

      const tom = new TomSelect(select, {
        itemClass: 'ts-item',
        createOnBlur: false,
        create: false,
        diacritics: true,
        maxOptions: false,
        sortField: is_regionaler_zusatz ? null : {
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
              return '<div class="ts-dropdown__item o-control '+data.level+'" tabindex="0">' +
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
            return `<div class="no-results">${window.ooTimelessTheme.translations.noResults || 'No results'}</div>`;
          },
        }
      });

      if (select.hasClass('--is-styled')) {
        // 1) Grab the static, initial array of <option> elements
        const allOptions = select.find('option').toArray();

        if (!allOptions.length) {
          console.warn('No options found for styled select');
          return;
        }

        let isSubRegionAutoChanging = false;

        const getOptionLevel = (optEl) => {
          if (!optEl) return 0;
          const lvlStr = $(optEl).data('level') || '';
          return parseInt((lvlStr+'').replace('level-', ''), 10) || 0;
        };

        // 2) Walk the fixed allOptions array, not the live DOM order
        const getFollowingOptions = ($opt) => {
          const idx = allOptions.findIndex(el => el.value === $opt.val());
          if (idx === -1) return $();
          return $(allOptions.slice(idx + 1));
        };

        function getTomValues(ts) {
          const v = ts.getValue();
          return Array.isArray(v) ? v : [v];
        }

        function collectDescendants($opt) {
          const baseLevel = getOptionLevel($opt.get(0));
          const descendants = [];
          getFollowingOptions($opt).each((_, el) => {
            const lvl = getOptionLevel(el);
            if (lvl > baseLevel) {
              descendants.push(el.value);
            } else {
              return false;
            }
          });
          return descendants;
        }

        tom.on('item_add', value => {
          if (isSubRegionAutoChanging) return;

          isSubRegionAutoChanging = true;

          const $opt = select.find(`option[value="${value}"]`);
          const toAdd = new Set([value, ...collectDescendants($opt)]);
          const current = getTomValues(tom);
          const newSet = new Set([...current, ...toAdd]);

          let idx = allOptions.findIndex(o => o.value === value);
          let lastLevelSeen = getOptionLevel($opt.get(0));

          // also select parents if all children are selected
          for (let i = idx - 1; i >= 0; i--) {
            const el = allOptions[i];
            const lvl = getOptionLevel(el);

            if (lvl < lastLevelSeen) {
              const parentIdx = i;
              const parentLevel = lvl;
              const children = [];

              for (let j = parentIdx + 1; j < allOptions.length; j++) {
                const cEl = allOptions[j];
                const cLvl = getOptionLevel(cEl);

                if (cLvl === parentLevel + 1) {
                  children.push(cEl.value);
                }

                if (cLvl <= parentLevel) break;
              }

              if (children.length > 0 && children.every(v => newSet.has(v))) {
                newSet.add(el.value);
              }

              lastLevelSeen = parentLevel;
              if (lastLevelSeen === 0) break;
            }
          }

          tom.setValue(Array.from(newSet), true);

          isSubRegionAutoChanging = false;
        });

        tom.on('item_remove', value => {
          if (isSubRegionAutoChanging) return;
          isSubRegionAutoChanging = true;

          const $opt      = select.find(`option[value="${value}"]`);
          const baseLevel = getOptionLevel($opt.get(0));
          const toRemove  = new Set([ value, ...collectDescendants($opt) ]);

          // remove all parents, grandparents, ...
          if (baseLevel > 0) {
            let currentLevel = baseLevel;
            const startIdx = allOptions.findIndex(opt => opt.value === value);

            for (let i = startIdx - 1; i >= 0; i--) {
              const optEl = allOptions[i];
              const lvl   = getOptionLevel(optEl);

              if (lvl < currentLevel) {
                toRemove.add(optEl.value);
                currentLevel = lvl;
                if (currentLevel === 0) break;
              }
            }
          }

          const current      = getTomValues(tom);
          const newSelection = current.filter(v => !toRemove.has(v));

          tom.setValue(newSelection);

          isSubRegionAutoChanging = false;
        });
      }
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
  const readMoreButton = document.querySelectorAll('.c-read-more, .c-property-details__more, .c-address-details__more');
  if( readMoreButton.length > 0 ) {
    readMoreButton.forEach(button => {
      const openClass = '--is-open';
    
      const targetId = button.getAttribute('aria-controls');
      const content = document.getElementById(targetId);
      const parentContainer = button.parentNode;
    
      if (!content) return;
    
      const openText = button.getAttribute('data-open-text');
      const closeText = button.getAttribute('data-close-text');
    
      button.addEventListener('click', event => {
        event.preventDefault();
      
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        const shouldExpand = !isExpanded;
      
        button.setAttribute('aria-expanded', String(shouldExpand));
        content.classList.toggle(openClass, shouldExpand);
        button.classList.toggle(openClass, shouldExpand);
        button.textContent = shouldExpand ? closeText : openText;
      
        if (isExpanded && parentContainer) {
          parentContainer.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  }

  // Function to apply text shortening, read-more button and visibility adjustments based on word count and screen size
  applyResponsiveTextShortening();


  // Accordion
  if (document.querySelectorAll('.c-accordion').length > 0) {
    const details = document.querySelectorAll('details');

    details.forEach(detail => {
      detail.addEventListener('toggle', () => {
        const srOpen = detail.querySelector('.u-screen-reader-only.--open');
        const srClose = detail.querySelector('.u-screen-reader-only.--close');
  
        if (detail.open) {
          detail.classList.add('--is-open');
          srOpen?.setAttribute('aria-hidden', 'true');
          srClose?.setAttribute('aria-hidden', 'false');
        } else {
          detail.classList.remove('--is-open');
          srOpen?.setAttribute('aria-hidden', 'false');
          srClose?.setAttribute('aria-hidden', 'true');
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
          const closeButton = popupElement.querySelector('.c-popup__close, .--close-popup');
          if (popupElement instanceof HTMLDialogElement && typeof popupElement.showModal === 'function') {
            popupElement.showModal();
          }
          popupElement.classList.add('--is-open');
          document.body.classList.add('--modal-open');
          if(closeButton){
              setTimeout(() => {
                  closeButton.focus();
              }, 100);
          }

          function closePopup() {
            if (popupElement.dataset.unclosable === 'true') {
                const propertyListUrl = window.ooTimelessTheme?.urls?.propertyList || '/';
                const referrer = document.referrer;
                
                if (referrer && referrer !== window.location.href) {
                    window.location.href = referrer;
                } else {
                    window.location.href = propertyListUrl;
                }
                return; // Stop further execution
            }

            popupElement.classList.remove('--is-open');
            if (popupElement instanceof HTMLDialogElement && typeof popupElement.close === 'function') {
              popupElement.close();
            }
            document.body.classList.remove('--modal-open');
          }

          if (closeButton) {
            closeButton.addEventListener('click', closePopup);
          }

          popupElement.addEventListener('click', (event) => {
            if (event.target === popupElement) {
              closePopup();
            }
          });

          // Close popup on press ESC
          document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
              e.preventDefault();
              closePopup();
            }
          });
        }
      });
    });
  }

  // Lightbox
  const lightboxClass = document.querySelectorAll('.glightbox');

  if(lightboxClass.length > 0 ) {
    const customLightboxHTML = 
    `<div id="glightbox-body" class="glightbox-container c-lightbox">
      <div class="gloader visible c-lightbox__loader"></div>
      <div class="goverlay c-lightbox__overlay"></div>
      <div class="gcontainer c-lightbox__container">
        <div id="glightbox-slider" class="gslider c-lightbox__slider"></div>
        <button class="gprev gbtn c-lightbox__icon-wrapper --arrow --prev" tabindex="0" aria-label="${window.ooTimelessTheme.translations.previous || 'Previous'}">
          <svg class="c-lightbox__icon --arrow" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <button class="gnext gbtn c-lightbox__icon-wrapper --arrow --next" tabindex="1" aria-label="${window.ooTimelessTheme.translations.next || 'Next'}">
          <svg class="c-lightbox__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
    </div>`;
    
    let customSlideHTML = 
    `<div class="gslide c-lightbox__slide">
      <div class="gslide-inner-content c-lightbox__wrapper">
        <div class="ginner-container c-lightbox__content">
          <div class="gslide-media c-lightbox__media">
            <button class="gclose gbtn c-lightbox__icon-wrapper --close" tabindex="2" aria-label="${window.ooTimelessTheme.translations.close || 'Close'}">
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
  const $sliders = $('.c-slider');

  function innerSliderUpdateTabIndex($slider) {
    $slider.find('.c-property-card__picture-wrapper').each(function() {
      this.tabIndex = $(this).hasClass('is-visible') ? 0 : -1;
    });
  }

  function outerSliderUpdateTabIndex($slider) {
    $slider.find('.c-property-card').each(function() {
      const $slide = $(this);
      if ($slide.hasClass('is-visible')) {
        const $innerSlider = $slide.find('.c-slider').first();
        if ($innerSlider.length) {
          innerSliderUpdateTabIndex($innerSlider);
        }
        $slide.find('button').each(function() { this.tabIndex = 0; });
        $slide.find('.c-property-card__button').each(function() { this.tabIndex = 0; });
      } else {
        $slide.find('button, a').each(function() { this.tabIndex = -1; });
      }
    });
  }

  if ($sliders.length > 0) {
    let innerSliderTotalCount = 0;
    let innerSliderMountedCount = 0;

    $sliders.each(function() {
      const slider = this;
      const $slider = $(slider);
      const splide = new Splide(slider);

      const hasVideoIframe = $slider.find('iframe[src*="youtube"], iframe[src*="vimeo"]').length > 0;
      if (hasVideoIframe) {
        const sanitize = () => sanitizeClonedYouTubeIframes(slider);
        splide.on('mounted', sanitize);
        splide.on('move', sanitize);
        splide.on('refresh', sanitize);
      }

      const innerslider = $slider.hasClass("--is-properties-images-slider");
      const outerslider = $slider.hasClass("--is-properties-slider") || $slider.hasClass("--is-properties-similar-slider");

      if (outerslider) {
        const $btnNextOuter = $slider.find(".c-slider__navigation:not(.--is-properties-images-slider) .c-slider__arrow.--next");
        const $btnPrevOuter = $slider.find(".c-slider__navigation:not(.--is-properties-images-slider) .c-slider__arrow.--prev");

        $btnNextOuter.on('click', e => {
          splide.go('+1');
        });

        $btnPrevOuter.on('click', e => {
          splide.go('-1');
        });

        splide.on('moved', () => requestAnimationFrame(() => requestAnimationFrame(() => outerSliderUpdateTabIndex($slider))));
      } else if (innerslider) {
        innerSliderTotalCount++;
        splide.on('mounted', () => requestAnimationFrame(() => requestAnimationFrame(() => {
          innerSliderUpdateTabIndex($slider);
          innerSliderMountedCount++;
          if (innerSliderMountedCount === innerSliderTotalCount) {
            $('.c-property-list__slider').each(function() {
              outerSliderUpdateTabIndex($(this));
            });
          }
        })));
        splide.on('moved', () => requestAnimationFrame(() => requestAnimationFrame(() => innerSliderUpdateTabIndex($slider))));
      }

      // progress bar animation
      const bar = $slider.find('.c-slider__progress-bar')[0];
      if (bar) {
        splide.on('mounted move', function() {
          const end = splide.Components.Controller.getEnd() + 1;
          bar.style.width = String(100 * (splide.index + 1) / end) + '%';
        });
      }

      const manuallyHandleFocus = !!innerslider || !!outerslider;

      splide.on('overflow', function(isOverflow) {
        splide.options = {
          drag: isOverflow,
          focusableNodes: manuallyHandleFocus ? '' : 'a, button, input, textarea, select:not([aria-hidden])'
        };
      });

      splide.on('mounted', function() {
        const $pagination = $slider.find('.splide__pagination').first();
        const $autoslideItem = $pagination.find('.c-slider__autoslide-item').first();
        if ($pagination.length && $autoslideItem.length) {
          $pagination.append($autoslideItem);
        }
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

  if (scrollBackToTop) {
    scrollBackToTop.onclick = () => {
      window.scroll({
        top: 0,
        behavior: 'smooth'
      });
    }
  }

  const header = document.querySelector('.c-header');
  function handleScroll() {
    if (window.scrollY > 0) {
      header.classList.add('--scrolled');
    } else {
      header.classList.remove('--scrolled');
    }
    if (scrollY > 200) {
      scrollBackToTop.classList.add('--visible');
    } else {
      scrollBackToTop.classList.remove('--visible');
    }
  }

  // needed to not make the scroll callback fire 50 times on each scroll.
  const debouncedScroll = debounce(handleScroll, 30);
  window.addEventListener('scroll', debouncedScroll);


 // Progressbar dynamic label width calculation
  function calculateProgressbarLabelWidths() {
    const progressbars = document.querySelectorAll('.c-progressbar');
    
    progressbars.forEach(progressbar => {
      const steps = progressbar.querySelectorAll('.c-progressbar__step');
      const labels = progressbar.querySelectorAll('.c-progressbar__label');
      
      if (steps.length === 0) return;
      
      const progressbarWidth = progressbar.offsetWidth;
      const stepCount = steps.length;
      const viewportWidth = window.innerWidth;
      const isInModal = progressbar.classList.contains('--in-modal'); // Check if in modal
      
      // Mobile: Set max-width and ensure labels don't overflow
      if (viewportWidth < 768) {
        // Find the form or parent container to get max width
        const form = progressbar.closest('.c-form') || progressbar.parentElement;
        const formWidth = form ? form.offsetWidth : progressbarWidth;
        
        labels.forEach((label, index) => {
          const step = steps[index];
          
          // Remove desktop positioning styles
          label.style.removeProperty('width');
          label.style.removeProperty('min-width');
          label.style.removeProperty('left');
          label.style.removeProperty('right');
          label.style.removeProperty('transform');
          
          // Only calculate for active step on mobile
          if (step.classList.contains('--is-active')) {
            // Get the position of the active step
            const stepRect = step.getBoundingClientRect();
            const formRect = form ? form.getBoundingClientRect() : progressbar.getBoundingClientRect();
            
            if (isInModal) {
            label.style.width = 'auto';
            label.style.maxWidth = `${formWidth - 20}px`;
            label.style.left = '50%';
            label.style.transform = 'translateX(-50%)';
          } else {
            // First, let the label size naturally to its content
            label.style.width = 'auto';
            label.style.maxWidth = 'none';
            label.style.left = '50%';
            label.style.transform = 'translateX(-50%)';
            
            // Give browser time to calculate natural size
            setTimeout(() => {
              const labelRect = label.getBoundingClientRect();
              const naturalWidth = labelRect.width;
              
              const stepCenterX = stepRect.left + (stepRect.width / 2);
              const padding = 10; // Safety padding
              
              // Calculate if label would overflow with natural width
              const halfLabel = naturalWidth / 2;
              const wouldOverflowLeft = (stepCenterX - halfLabel) < (formRect.left + padding);
              const wouldOverflowRight = (stepCenterX + halfLabel) > (formRect.right - padding);
              
              if (!wouldOverflowLeft && !wouldOverflowRight) {
                // Label fits perfectly centered - use natural width
                label.style.width = 'auto';
                label.style.maxWidth = `${formWidth - (padding * 2)}px`;
                label.style.left = '50%';
                label.style.transform = 'translateX(-50%)';
              } else {
                // Label would overflow - need to adjust
                const maxWidth = formWidth - (padding * 2);
                
                if (index === 0 && !isInModal) {
                  // First step: left-aligned
                  label.style.width = 'auto';
                  label.style.maxWidth = `${maxWidth}px`;
                  label.style.left = '0';
                  label.style.transform = 'none';
                } else if (index === steps.length - 1 && !isInModal) {
                  // Last step: right-aligned
                  label.style.width = 'auto';
                  label.style.maxWidth = `${maxWidth}px`;
                  label.style.left = 'auto';
                  label.style.right = '0';
                  label.style.transform = 'none';
                } else {
                  // Middle steps: adjust positioning to keep within bounds
                  if (wouldOverflowLeft) {
                    // Shift to right to avoid left overflow
                    const leftOffset = (formRect.left + padding) - stepRect.left;
                    label.style.width = 'auto';
                    label.style.maxWidth = `${maxWidth}px`;
                    label.style.left = `${leftOffset}px`;
                    label.style.transform = 'none';
                  } else if (wouldOverflowRight) {
                    // Shift to left to avoid right overflow
                    const rightOffset = (formRect.right - padding) - stepRect.right;
                    label.style.width = 'auto';
                    label.style.maxWidth = `${maxWidth}px`;
                    label.style.left = 'auto';
                    label.style.right = `${-rightOffset}px`;
                    label.style.transform = 'none';
                  }
                }
              }
            }, 10);
          }
          } else {
            // Hide non-active labels on mobile (handled by CSS)
            label.style.removeProperty('max-width');
            label.style.removeProperty('width');
          }
        });
        return; // Exit early for mobile
      }
      
      // Desktop calculations for 3+ steps
      // Calculate available width per label
      // Account for spacing between steps
      const spacing = 20; // Minimum space between labels
      const availableWidth = progressbarWidth - (spacing * (stepCount - 1));
      let labelWidth = Math.floor(availableWidth / stepCount);

      // Reduce by 10px for safety margin
      labelWidth = labelWidth - 10;
      
      // Set min/max based on viewport
      let minWidth, maxWidth;
      
      if (viewportWidth < 1200) { // Tablet/MD
        minWidth = 80;
        // Formula: fewer steps = wider labels
        maxWidth = Math.min(200, 280 - (stepCount * 20));
      } else if (viewportWidth < 1400) { // LG
        minWidth = 100;
        maxWidth = Math.min(250, 370 - (stepCount * 30));
      } else { // XL
        minWidth = 120;
        maxWidth = Math.min(220, 340 - (stepCount * 30));
      }
      
      // Clamp the calculated width
      labelWidth = Math.max(minWidth, Math.min(labelWidth, maxWidth));
      
      // Apply width to all labels
      labels.forEach((label, index) => {
        const step = steps[index];
        
        // Adjust width for first and last labels (15px narrower)
        let adjustedLabelWidth = labelWidth;
        if (step.dataset.step === '1' || index === steps.length - 1) {
          adjustedLabelWidth = labelWidth - 15;
        }
        
        // Apply calculated width
        label.style.width = `${adjustedLabelWidth}px`;
        label.style.maxWidth = `${adjustedLabelWidth}px`;
        
        // Adjust positioning for first and last labels
        if (step.dataset.step === '1') {
          // First label stays left-aligned
        } else if (index === steps.length - 1) {
          // Last label stays right-aligned
        } else {
          // Center labels might need adjustment if they overlap
          const prevStep = steps[index - 1];
          const nextStep = steps[index + 1];
          
          if (prevStep && nextStep) {
            const currentPos = step.getBoundingClientRect();
            const prevPos = prevStep.getBoundingClientRect();
            const nextPos = nextStep.getBoundingClientRect();
            
            // Check for potential overlap and adjust if needed
            const halfWidth = adjustedLabelWidth / 2;
            const leftEdge = currentPos.left - halfWidth;
            const rightEdge = currentPos.left + halfWidth;
            
            if (leftEdge < prevPos.right || rightEdge > nextPos.left) {
              // Reduce width to prevent overlap
              const reducedWidth = Math.min(
                adjustedLabelWidth,
                (nextPos.left - prevPos.right) - spacing
              );
              label.style.width = `${reducedWidth}px`;
              label.style.maxWidth = `${reducedWidth}px`;
            }
          }
        }
      });
    });
  }
  
  // Run on load
  if ($('.c-progressbar').length > 0) {
    calculateProgressbarLabelWidths();
    
    // Recalculate on resize with debouncing
    const debouncedCalculate = debounce(calculateProgressbarLabelWidths, 20);
    window.addEventListener('resize', debouncedCalculate);

    // Recalculate when page changes in multi-step form
    $(document).on('oo-page-changed', function(event, currentPage) {
      // Small delay to ensure DOM updates are complete
      setTimeout(() => {
        calculateProgressbarLabelWidths();
      }, 20);
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

// Initialize last known viewport dimensions
let ooLastHeight = window.innerHeight;
let ooLastWidth = window.innerWidth;
let ooResizeTimeout;

jQuery(window).on('resize', function() {
  clearTimeout(ooResizeTimeout);

  ooResizeTimeout = setTimeout(function() {
    const heightChange = Math.abs(window.innerHeight - ooLastHeight);
    const widthChange = Math.abs(window.innerWidth - ooLastWidth);
    ooLastHeight = window.innerHeight;
    ooLastWidth = window.innerWidth;
    // Only proceed if a significant change occurred
    if (heightChange > 100 || widthChange > 50) {
      if ($('.o-body.--main-nav-open').length) {
        menuOpenClose();
      }

      // Correct padding for first element in main if header
      correctFirstElementPadding();
      // Function to apply text shortening, read-more button and visibility adjustments based on word count and screen size
      applyResponsiveTextShortening();
    }

  }, 200); // 200ms debounce
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

  function shortenElements(elementsEach, textElement, elementToShorten) {
    $(elementsEach).each(function() {
      const $root = $(this);
      const $textEl = $root.find(elementToShorten);
      const $readMore = $root.find('.c-read-more');

      if ($textEl.length === 0) {
        $readMore.hide();
        return;
      }

      $textEl.addClass('--shorten');
      const el = $textEl.get(0);

      if (!el) {
        $textEl.removeClass('--shorten');
        $readMore.hide();
        return;
      }

      const shouldShorten = el.scrollHeight > el.clientHeight;

      if (shouldShorten) {
        $textEl.addClass('--shorten');
        $readMore
          .show()
          .attr('aria-expanded', 'false')
          .text($readMore.data('open-text') || 'weiterlesen...');
      } else {
        $textEl.removeClass('--shorten');
        $readMore.hide();
      }
    });
  }

  // google review slider
  shortenElements('.c-google-review-card', '.c-google-review-card__text p', '.c-google-review-card__text');

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

function debounce(func, delay) {
  let timeoutId;

  return function(...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      func.apply(this, args);
    }, delay);
  };
}

function getSlide(element) {
  return element.closest('.c-banner__slide');
}

function initVideoToggle() {
    $('.c-banner__video-playback-toggle').off('click').on('click', function (e) {
      e.preventDefault();
  
      const btn = $(this);
      const slide = getSlide(this);
      const iframe = $(slide).find('iframe');
      const isPlaying = btn.attr('aria-pressed') === 'false';
      const labelPlay = btn.data('label-play');
      const labelPause = btn.data('label-pause');
  
      // Toggle video playback
      iframe.each(function () {
        const isYouTube = this.src.includes('youtube');
        const isVimeo = this.src.includes('vimeo');
        const player = $(this).data('yt-player') || $(this).data('vimeo-player');
        if (!player) return;
  
        try {
          if (isYouTube) {
            isPlaying ? player.pauseVideo() : player.playVideo();
          } else if (isVimeo) {
            isPlaying ? player.pause() : player.play();
          }
        } catch (e) {
          console.warn('Video control failed', e);
        }
      });
  
      // Update ARIA and screen reader label
      const newState = isPlaying ? 'true' : 'false';
      const newLabel = isPlaying ? labelPlay : labelPause;
  
      btn.attr('aria-pressed', newState);
      btn.attr('aria-label', newLabel);
    });
}

function onYouTubeIframeAPIReady() {
  $('iframe[src*="youtube"]').each(function () {
    const iframe = this;

    if ($(iframe).closest('.splide__slide--clone').length > 0) return;
    if ($(iframe).data('yt-player')) return;

    try {
      const player = new YT.Player(iframe, {
        events: {
          onReady: onPlayerReady,
        },
      });

      $(iframe).data('yt-player', player);
    } catch (e) {
      console.error('YouTube Player Error:', e);
    }
  });
}

function onPlayerReady() {
  // No-op for now; autoplay is handled by iframe URL params
}

function initVimeoPlayers() {
  $('iframe[src*="vimeo"]').each(function () {
    const iframe = this;

    if ($(iframe).closest('.splide__slide--clone').length > 0) return;
    if ($(iframe).data('vimeo-player')) return;

    const player = new Vimeo.Player(iframe);
    $(iframe).data('vimeo-player', player);
  });
}

function sanitizeClonedYouTubeIframes(root) {
    $(root).find('.splide__slide--clone iframe[src*="youtube"]').each(function () {
      const $iframe = $(this);
      const iframeEl = this;

      const currentId = $iframe.attr('id');
      if (currentId?.startsWith('widget')) {
        $iframe.removeAttr('id');
      }
      // Remove all YouTube-injected data attributes
      const attrs = iframeEl.attributes;
      for (let i = attrs.length - 1; i >= 0; i--) {
        const attr = attrs[i];
        if (attr.name.startsWith('data-ytplayer')) {
          $iframe.removeAttr(attr.name);
        }
      }
    });
}
document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('.c-form.--custom-validation');

  forms.forEach(function(form) {
    form.setAttribute('novalidate', '');
    const inputs = form.querySelectorAll('input, textarea');
    const selects = form.querySelectorAll('select');

    const showError = (input, message) => {
      const errorDiv = input.closest('label, div')?.querySelector('.c-form__error-message');
      if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.add('is-visible');
        errorDiv.setAttribute('aria-live', 'polite');
      }
    };

    const hideError = (input) => {
      const errorDiv = input.closest('label, div')?.querySelector('.c-form__error-message');
      if (errorDiv) {
        errorDiv.textContent = '';
        errorDiv.classList.remove('is-visible');
        errorDiv.removeAttribute('aria-live');
      }
    };

    const inputHandleBlur = (input) => {
      if (!input.checkValidity()) {
        let errorMessage = 'This field is invalid.';
        if (input.type === 'email' && input.validity.typeMismatch) {
          errorMessage = window.ooTimelessTheme.translations.invalidEmail || 'Please enter a valid email address.';
        } else if (input.validity.valueMissing) {
          errorMessage = window.ooTimelessTheme.translations.requiredField || 'Please fill in the required field.';
          if(input.type === 'checkbox'){
            errorMessage = window.ooTimelessTheme.translations.requiredCheckbox || 'Please accept.';
          }
        }

        showError(input, errorMessage);
      } else {
        hideError(input);
      }
    }

    inputs.forEach(function(input) {
      input.addEventListener('blur', function() {
        inputHandleBlur(input)
      });

      input.addEventListener('input', function() {
        if (input.checkValidity()) {
          hideError(input);
        }
      });
    });

    const selectHandleChange = (select) => {
      const tomSelectControl = select.nextElementSibling;
      const errorMessage = window.ooTimelessTheme.translations.requiredField || 'Please fill in the required field.';
      if (!select.checkValidity()) {
        tomSelectControl.classList.add('is-invalid');
        showError(select, errorMessage);
      } else {
        tomSelectControl.classList.remove('is-invalid');
        hideError(select);
      }
    }

    selects.forEach(select => {
      select.addEventListener('change', function() {
        selectHandleChange(select)
      });
    });

    const jumpToFirstInvalidInput = (form) => {
      const firstInvalid = form.querySelector(':invalid');
      if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus({ preventScroll: true });
      }
    }

    // use a capture phase click listener on submit button instead of simple submit event.
    // It is needed to prevent recaptcha code to call form.reportValidity and display the browsers error messages.
    // If the form is valid the recaptcha code still gets executed.
    form.querySelectorAll('.c-form__button').forEach(button => {
      button.addEventListener('click', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopImmediatePropagation();
          inputs.forEach(input => {
            inputHandleBlur(input)
          });
          selects.forEach(select => {
            selectHandleChange(select)
          })
          jumpToFirstInvalidInput(form);
        }
        form.classList.add('--validated');
      }, true);
    });
  });
});
jQuery(window).on('load', function () {
  initVideoToggle();
  initVimeoPlayers();
  sanitizeClonedYouTubeIframes(document);
});
