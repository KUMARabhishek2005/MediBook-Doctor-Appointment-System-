document.addEventListener('DOMContentLoaded', () => {
  const $$ = (s) => document.querySelectorAll(s);

  // 1. Confirm before risky actions (links or forms with data-confirm)
  $$('[data-confirm]').forEach(el => {
    const evt = el.tagName === 'FORM' ? 'submit' : 'click';
    el.addEventListener(evt, ev => { if (!confirm(el.dataset.confirm)) ev.preventDefault(); });
  });

  // 2. Animated count-up numbers on the dashboard
  $$('[data-count]').forEach(el => {
    const target = +el.dataset.count; let n = 0;
    const step = Math.max(1, Math.ceil(target / 30));
    const t = setInterval(() => { n = Math.min(target, n + step); el.textContent = n; if (n >= target) clearInterval(t); }, 25);
  });

  // 3. Flash messages fade away after 4 seconds
  $$('.flash').forEach(el => setTimeout(() => { el.style.opacity = 0; setTimeout(() => el.remove(), 500); }, 4000));

  // 4. Live table search: <input data-search="#tableId">
  $$('[data-search]').forEach(box => box.addEventListener('input', () => {
    const q = box.value.toLowerCase();
    document.querySelectorAll(box.dataset.search + ' tr').forEach((row, i) => {
      if (i > 0) row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }));

  // 5. Show/hide password
  $$('[data-toggle-pw]').forEach(b => b.addEventListener('click', () => {
    const i = b.parentElement.querySelector('input'); i.type = i.type === 'password' ? 'text' : 'password';
  }));

  // 6. One-click demo login: fills email + password
  $$('[data-fill]').forEach(c => c.addEventListener('click', () => {
    document.querySelector('[name=email]').value = c.dataset.fill;
    document.querySelector('[name=password]').value = 'password';
  }));

  // 7. Mobile menu
  const burger = document.querySelector('.burger');
  if (burger) burger.addEventListener('click', () => document.querySelector('.topbar nav').classList.toggle('open'));

  // 8. Booking page: reload with chosen date so booked slots are shown
  const d = document.querySelector('[data-reload-date]');
  if (d) d.addEventListener('change', () => {
    const p = new URLSearchParams(location.search); p.set('date', d.value); location.search = p.toString();
  });
});
