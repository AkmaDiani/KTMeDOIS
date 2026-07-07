        </main>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/SDW/KTMeDOIS/Presentation/Public/assets/js/main.js"></script>
        <script>
            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const toggleBtn = document.querySelector('.sidebar-toggle');
                if (!sidebar) return;
                if (window.innerWidth < 769) {
                    sidebar.classList.toggle('active');
                    if (overlay) overlay.classList.toggle('active');
                    if (toggleBtn) toggleBtn.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    if (toggleBtn) toggleBtn.classList.toggle('active');
                }
            }

            function toggleDropdown(event) {
                event.stopPropagation();
                const profile = event.currentTarget;
                const menu = profile.querySelector('.dropdown-menu');
                document.querySelectorAll('.user-profile .dropdown-menu.show').forEach(function(el) {
                    if (el !== menu) el.classList.remove('show');
                });
                menu.classList.toggle('show');
            }

            document.addEventListener('click', function(event) {
                const profile = document.querySelector('.user-profile');
                if (profile && !profile.contains(event.target)) {
                    const menu = profile.querySelector('.dropdown-menu');
                    if (menu) menu.classList.remove('show');
                }
            });

            function showNotifications() {
                alert('📬 You have new notifications');
            }
        </script>
        </body>

        </html>