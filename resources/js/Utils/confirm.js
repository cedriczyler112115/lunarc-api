/**
 * Utility helper to display modern jquery-confirm UI dialogs across Inertia/React components.
 * Falls back to native confirm() if jquery-confirm is not available.
 *
 * @param {Object} options
 * @param {string} options.title - Dialog title
 * @param {string} options.content - Dialog message / description
 * @param {'red'|'green'|'blue'|'amber'|'purple'|'dark'} [options.type='red'] - Type/color theme of the dialog
 * @param {string} [options.confirmButtonText='Confirm'] - Label for confirm button
 * @param {string} [options.cancelButtonText='Cancel'] - Label for cancel button
 * @param {string} [options.confirmButtonClass='btn-red'] - CSS class for confirm button ('btn-red', 'btn-green', 'btn-blue', 'btn-amber')
 * @param {() => void} [options.onConfirm] - Callback executed on confirm
 * @param {() => void} [options.onCancel] - Callback executed on cancel
 */
export function confirmDialog({
  title = 'Please Confirm',
  content = 'Are you sure you want to proceed?',
  type = 'red',
  confirmButtonText = 'Confirm',
  cancelButtonText = 'Cancel',
  confirmButtonClass = 'btn-red',
  onConfirm,
  onCancel,
}) {
  if (typeof window !== 'undefined' && window.jQuery && typeof window.jQuery.confirm === 'function') {
    window.jQuery.confirm({
      title,
      content,
      type,
      typeAnimated: true,
      theme: 'modern',
      animation: 'scale',
      closeAnimation: 'scale',
      backgroundDismiss: true,
      buttons: {
        confirm: {
          text: confirmButtonText,
          btnClass: confirmButtonClass,
          action: function () {
            if (typeof onConfirm === 'function') {
              onConfirm();
            }
          },
        },
        cancel: {
          text: cancelButtonText,
          btnClass: 'btn-default',
          action: function () {
            if (typeof onCancel === 'function') {
              onCancel();
            }
          },
        },
      },
    });
  } else {
    // Graceful fallback if jQuery or script is loading
    const formattedMsg = `${title}\n\n${content.replace(/<[^>]*>?/gm, '')}`;
    if (window.confirm(formattedMsg)) {
      if (typeof onConfirm === 'function') {
        onConfirm();
      }
    } else {
      if (typeof onCancel === 'function') {
        onCancel();
      }
    }
  }
}

// Attach globally for convenience
if (typeof window !== 'undefined') {
  window.confirmDialog = confirmDialog;
}

export default confirmDialog;
