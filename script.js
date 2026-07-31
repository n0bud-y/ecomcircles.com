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
});
