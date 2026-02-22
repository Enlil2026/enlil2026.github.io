document.addEventListener('DOMContentLoaded', () => {
  const list = document.querySelector('#channel-list');

  if (list) {
    list.addEventListener('click', (event) => {
      const item = event.target.closest('.channel');

      // If we didn't click a channel, or it's already active, do nothing
      if (!item) return;

      // 1. Find the currently active item (if there is one)
      const currentActive = list.querySelector('.channel.active');

      // 2. If an active item exists and it's NOT the one we just clicked:
      if (currentActive && currentActive !== item) {
        currentActive.classList.remove('active');
      }

      // 3. Toggle the clicked item
      item.classList.toggle('active');
    });
  }
});
