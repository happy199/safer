/**
 * Template Name: EstateAgency - v4.9.1
 * Template URL: https://bootstrapmade.com/real-estate-agency-bootstrap-template/
 * Author: BootstrapMade.com
 * License: https://bootstrapmade.com/license/
 */
;(function () {
  'use strict'

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach((e) => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Toggle .navbar-reduce
   */
  let selectHNavbar = select('.navbar-default')
  if (selectHNavbar) {
    onscroll(document, () => {
      if (window.scrollY > 100) {
        selectHNavbar.classList.add('navbar-reduce')
        selectHNavbar.classList.remove('navbar-trans')
      } else {
        selectHNavbar.classList.remove('navbar-reduce')
        selectHNavbar.classList.add('navbar-trans')
      }
    })
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Preloader
   */
  let preloader = select('#preloader')
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    })
  }

  /**
   * Search window open/close
   */
  let body = select('body')
  on('click', '.navbar-toggle-box', function (e) {
    e.preventDefault()
    body.classList.add('box-collapse-open')
    body.classList.remove('box-collapse-closed')
  })

  on('click', '.close-box-collapse', function (e) {
    e.preventDefault()
    body.classList.remove('box-collapse-open')
    body.classList.add('box-collapse-closed')
  })

  /**
   * Intro Carousel
   */
  new Swiper('.intro-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 2000,
      disableOnInteraction: false,
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true,
    },
  })

  /**
   * Property carousel
   */
  new Swiper('#property-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.propery-carousel-pagination',
      type: 'bullets',
      clickable: true,
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 20,
      },

      1200: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },
  })

  /**
   * News carousel
   */
  new Swiper('#news-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.news-carousel-pagination',
      type: 'bullets',
      clickable: true,
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 20,
      },

      1200: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },
  })

  /**
   * Testimonial carousel
   */
  new Swiper('#testimonial-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.testimonial-carousel-pagination',
      type: 'bullets',
      clickable: true,
    },
  })

  /**
   * Property Single carousel
   */
  new Swiper('#property-single-carousel', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.property-single-carousel-pagination',
      type: 'bullets',
      clickable: true,
    },
  })

  $('.button-checkbox').each(function () {
    var $widget = $(this),
      $button = $widget.find('button'),
      $checkbox = $widget.find('input:checkbox'),
      color = $button.data('color'),
      settings = {
        on: {
          icon: 'glyphicon glyphicon-check',
        },
        off: {
          icon: 'glyphicon glyphicon-unchecked',
        },
      }

    $button.on('click', function () {
      $checkbox.prop('checked', !$checkbox.is(':checked'))
      $checkbox.triggerHandler('change')
      updateDisplay()
    })

    $checkbox.on('change', function () {
      updateDisplay()
    })

    function updateDisplay() {
      var isChecked = $checkbox.is(':checked')
      // Set the button's state
      $button.data('state', isChecked ? 'on' : 'off')

      // Set the button's icon
      $button
        .find('.state-icon')
        .removeClass()
        .addClass('state-icon ' + settings[$button.data('state')].icon)

      // Update the button's color
      if (isChecked) {
        $button.removeClass('btn-default').addClass('btn-' + color + ' active')
      } else {
        $button.removeClass('btn-' + color + ' active').addClass('btn-default')
      }
    }
    function init() {
      updateDisplay()
      // Inject the icon if applicable
      if ($button.find('.state-icon').length == 0) {
        $button.prepend(
          '<i class="state-icon ' +
            settings[$button.data('state')].icon +
            '"></i> ',
        )
      }
    }
    init()
  })

  const slider = document.getElementById('price-slider')

  if (slider) {
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10
    const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10
    const range = noUiSlider.create(slider, {
      start: [min.value || minValue, max.value || maxValue],
      connect: true,
      step: 10,
      range: {
        min: minValue,
        max: maxValue,
      },
    })
    range.on('slide', function (values, handle) {
      if (handle === 0) {
        min.value = Math.round(values[0])
      }
      if (handle === 1) {
        max.value = Math.round(values[1])
      }
    })
    range.on('end', function (values, handle) {
      if (handle === 0) {
        min.dispatchEvent(new Event('change'))
      } else {
        max.dispatchEvent(new Event('change'))
      }
    })
  }
})()
