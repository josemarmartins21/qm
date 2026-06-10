</div>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</div><!-- /.content-area -->
    </main><!-- /.main-content -->
</div><!-- /.dashboard-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Sidebar Mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    // Dark/Light Mode
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;
    const mainContent = document.querySelector('.main-content');

    // Verificar preferência salva
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        themeToggle.checked = true;
        setDarkMode();
    }

    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            setDarkMode();
            localStorage.setItem('theme', 'dark');
        } else {
            setLightMode();
            localStorage.setItem('theme', 'light');
        }
    });

    function setDarkMode() {
        html.setAttribute('data-bs-theme', 'dark');
        themeIcon.classList.remove('bi-moon-stars-fill');
        themeIcon.classList.add('bi-sun-fill');
        mainContent.style.backgroundColor = '#212529';
    }

    function setLightMode() {
        html.setAttribute('data-bs-theme', 'light');
        themeIcon.classList.remove('bi-sun-fill');
        themeIcon.classList.add('bi-moon-stars-fill');
        mainContent.style.backgroundColor = '#f8f9fa';
    }

    // Fechar sidebar ao clicar em link no mobile
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });
</script>

</body>
</html>
