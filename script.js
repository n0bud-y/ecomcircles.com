/* Custom scripts for ecomcircles.com — add your own additions below. */

document.addEventListener('DOMContentLoaded', function () {
  var tracks = document.querySelectorAll('.no-scrollbar');

  tracks.forEach(function (track) {
    var controls = track.nextElementSibling;
    if (!controls) return;

    var leftBtn = controls.querySelector('[aria-label="Scroll left"]');
    var rightBtn = controls.querySelector('[aria-label="Scroll right"]');
    if (!leftBtn || !rightBtn) return;

    function step() {
      var card = track.children[0];
      var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return card ? card.getBoundingClientRect().width + gap : track.clientWidth;
    }

    function setButtonState(btn, disabled) {
      btn.disabled = disabled;
      btn.style.cursor = disabled ? 'not-allowed' : 'pointer';
      btn.style.borderColor = disabled ? 'var(--neutral-300)' : 'var(--neutral-800)';
      btn.style.color = disabled ? 'var(--neutral-300)' : 'var(--neutral-800)';
    }

    function updateButtons() {
      var maxScroll = track.scrollWidth - track.clientWidth;
      setButtonState(leftBtn, track.scrollLeft <= 1);
      setButtonState(rightBtn, track.scrollLeft >= maxScroll - 1);
    }

    leftBtn.addEventListener('click', function () {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });

    rightBtn.addEventListener('click', function () {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });

    track.addEventListener('scroll', function () {
      window.requestAnimationFrame(updateButtons);
    });

    window.addEventListener('resize', updateButtons);

    updateButtons();
  });

  function formatCounterValue(value, unit, prefix, suffix, precision) {
    precision = parseInt(precision, 10);
    if (isNaN(precision)) {
      precision = unit ? 1 : 0;
    }

    var formatted;
    if (unit) {
      formatted = value.toFixed(precision) + unit;
    } else if (precision > 0) {
      formatted = value.toLocaleString(undefined, {
        minimumFractionDigits: precision,
        maximumFractionDigits: precision,
      });
    } else {
      formatted = Math.round(value).toLocaleString();
    }

    return (prefix || '') + formatted + (suffix || '');
  }

  function animateCounterElement(el) {
    var target = parseFloat(el.dataset.countTo);
    if (isNaN(target)) return;

    var duration = parseInt(el.dataset.countDuration, 10);
    if (isNaN(duration)) duration = 1200;

    var precision = parseInt(el.dataset.countPrecision, 10);
    if (isNaN(precision)) {
      precision = el.dataset.countUnit ? 1 : 0;
    }

    var prefix = el.dataset.countPrefix || '';
    var suffix = el.dataset.countSuffix || '';
    var unit = el.dataset.countUnit || '';
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var current = target * progress;
      el.textContent = formatCounterValue(current, unit, prefix, suffix, precision);
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    }

    window.requestAnimationFrame(step);
  }

  function initCounters() {
    var counters = document.querySelectorAll('[data-counter]');
    counters.forEach(function (counter) {
      animateCounterElement(counter);
    });
  }

  var counterSection = document.querySelector('#trust-stats');
  if (counterSection && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            initCounters();
            obs.disconnect();
          }
        });
      },
      { threshold: 0.3 }
    );

    observer.observe(counterSection);
  }
});
