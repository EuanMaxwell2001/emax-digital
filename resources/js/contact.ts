// Contact form: sends in the background and swaps in a thank-you, without leaving the page.
// Without JS the form posts normally and the page comes back with the same states.
// Listens on document, so it keeps working after Swup swaps the footer.

type Errors = Record<string, string[]>;

export function initContactForm() {
  document.addEventListener('submit', async (e) => {
    const form = (e.target as HTMLElement).closest<HTMLFormElement>('[data-contact-form]');
    if (!form || form.classList.contains('is-sending')) return;
    e.preventDefault();

    const status = form.querySelector<HTMLElement>('[data-status]')!;
    showErrors(form, {});
    status.textContent = 'Sending…';
    form.classList.add('is-sending');

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (res.ok) {
        const done = form.parentElement!.querySelector<HTMLElement>('[data-contact-done]');
        form.reset();
        form.hidden = true;
        if (done) {
          done.hidden = false;
          done.focus({ preventScroll: true });
        }
        status.textContent = '';
        return;
      }
      const data = await res.json().catch(() => ({}));
      if (res.status === 422 && data.errors) {
        showErrors(form, data.errors as Errors);
        status.textContent = data.errors.form?.[0] ?? 'Please check the highlighted fields.';
      } else if (res.status === 429) {
        status.textContent = 'That is a lot of messages. Please try again later, or email me below.';
      } else if (res.status === 419) {
        status.textContent = 'This page was open a while. Refresh it and try again, or email me below.';
      } else {
        status.textContent = 'Something went wrong sending that. Please email me below instead.';
      }
    } catch {
      status.textContent = 'Could not connect. Please check your connection, or email me below.';
    } finally {
      form.classList.remove('is-sending');
    }
  });
}

function showErrors(form: HTMLFormElement, errors: Errors) {
  form.querySelectorAll<HTMLElement>('[data-error]').forEach((p) => {
    const name = p.dataset.error!;
    const field = form.elements.namedItem(name) as HTMLInputElement | null;
    const msg = errors[name]?.[0] ?? '';
    p.textContent = msg;
    if (!field) return;
    if (msg) {
      field.setAttribute('aria-invalid', 'true');
      field.setAttribute('aria-describedby', p.id);
    } else {
      field.removeAttribute('aria-invalid');
      field.removeAttribute('aria-describedby');
    }
  });
  const first = form.querySelector<HTMLElement>('[aria-invalid="true"]');
  first?.focus();
}
