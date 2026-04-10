// Sidebar móvil, filtros y paginación simple para tablas.
document.addEventListener('DOMContentLoaded', () => {
  const root = document.documentElement;
  const themeToggle = document.getElementById('themeToggle');
  const storedTheme = localStorage.getItem('crm_theme') || 'light';
  root.setAttribute('data-theme', storedTheme);
  if (themeToggle) {
    themeToggle.innerHTML = storedTheme === 'dark'
      ? '<i class="fa-solid fa-sun"></i> Claro'
      : '<i class="fa-solid fa-moon"></i> Oscuro';
    themeToggle.addEventListener('click', () => {
      const current = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', current);
      localStorage.setItem('crm_theme', current);
      themeToggle.innerHTML = current === 'dark'
        ? '<i class="fa-solid fa-sun"></i> Claro'
        : '<i class="fa-solid fa-moon"></i> Oscuro';
    });
  }

  const toggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');

  toggle?.addEventListener('click', () => sidebar?.classList.toggle('open'));

  document.querySelectorAll('[data-table]').forEach((tableWrap) => {
    const table = tableWrap.querySelector('table');
    const tbody = table?.querySelector('tbody');
    if (!table || !tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr'));
    const searchInput = tableWrap.querySelector('[data-search]');
    const prevBtn = tableWrap.querySelector('[data-prev]');
    const nextBtn = tableWrap.querySelector('[data-next]');
    const pageLabel = tableWrap.querySelector('[data-page-label]');
    const pageSize = 8;
    let currentPage = 1;
    let filtered = [...rows];

    function render() {
      rows.forEach((r) => (r.style.display = 'none'));
      const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * pageSize;
      const end = start + pageSize;
      filtered.slice(start, end).forEach((row) => (row.style.display = ''));

      if (pageLabel) pageLabel.textContent = `Página ${currentPage} de ${totalPages}`;
      if (prevBtn) prevBtn.disabled = currentPage === 1;
      if (nextBtn) nextBtn.disabled = currentPage === totalPages;
    }

    function applyFilter(term) {
      const lower = term.toLowerCase();
      filtered = rows.filter((row) => row.textContent.toLowerCase().includes(lower));
      currentPage = 1;
      render();
    }

    searchInput?.addEventListener('input', (e) => applyFilter(e.target.value || ''));
    prevBtn?.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage -= 1;
        render();
      }
    });
    nextBtn?.addEventListener('click', () => {
      currentPage += 1;
      render();
    });

    render();
  });
});
