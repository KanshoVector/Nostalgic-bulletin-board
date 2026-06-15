  </main>
</div>
<div id="imageModal" class="close-modal hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
  <button type="button" class="close absolute right-6 top-6 text-4xl leading-none text-white/90 transition hover:text-white" aria-label="閉じる">&times;</button>
  <img id="modalImage" class="max-h-[90vh] max-w-full rounded-xl shadow-2xl" src="" alt="拡大画像">
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const closeBtn = document.querySelector('#imageModal .close');

  function showModal() {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function hideModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  document.body.addEventListener('click', function(e) {
    if (e.target && e.target.matches('.post-image[data-src]')) {
      if (modalImage) {
        modalImage.src = e.target.getAttribute('data-src');
      }
      showModal();
    }
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', hideModal);
  }

  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        hideModal();
      }
    });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal && modal.classList.contains('flex')) {
      hideModal();
    }
  });

  const urlParams = new URLSearchParams(window.location.search);
  const messageParam = urlParams.get('message');
  const errorParam = urlParams.get('error');
  const messageBoxSuccess = document.querySelector('.message-box.success-message');
  const messageBoxError = document.querySelector('.message-box.error-message');

  if (messageParam && messageBoxSuccess) {
    messageBoxSuccess.textContent = decodeURIComponent(messageParam);
    messageBoxSuccess.classList.remove('hidden');
    urlParams.delete('message');
    history.replaceState(null, '', '?' + urlParams.toString());
  }

  if (errorParam && messageBoxError) {
    messageBoxError.textContent = decodeURIComponent(errorParam);
    messageBoxError.classList.remove('hidden');
    urlParams.delete('error');
    history.replaceState(null, '', '?' + urlParams.toString());
  }

  document.body.addEventListener('click', function(e) {
    if (e.target && e.target.matches('.edit-comment-button')) {
      const button = e.target;
      const commentId = button.dataset.commentId;
      const currentTextElement = document.querySelector('.comment-text-' + commentId);
      const deleteForm = button.nextElementSibling;
      const editForm = document.querySelector('.comment-edit-form-' + commentId);

      if (currentTextElement) currentTextElement.classList.add('hidden');
      button.classList.add('hidden');
      if (deleteForm) deleteForm.classList.add('hidden');
      if (editForm) editForm.classList.remove('hidden');
    } else if (e.target && e.target.matches('.cancel-button')) {
      const button = e.target;
      const commentId = button.dataset.commentId;
      const currentTextElement = document.querySelector('.comment-text-' + commentId);
      const editButton = document.querySelector('.edit-comment-button[data-comment-id="' + commentId + '"]');
      const deleteForm = (editButton && editButton.nextElementSibling) ? editButton.nextElementSibling : null;
      const editForm = document.querySelector('.comment-edit-form-' + commentId);

      if (currentTextElement) currentTextElement.classList.remove('hidden');
      if (editButton) editButton.classList.remove('hidden');
      if (deleteForm) deleteForm.classList.remove('hidden');
      if (editForm) editForm.classList.add('hidden');
    }
  });
});
</script>
</body>
</html>
