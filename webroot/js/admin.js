/**
 * FatturaCake Admin JavaScript
 * Theme management, sidebar, and utilities
 */

(function() {
    'use strict';

    // ========================================
    // Theme Management
    // ========================================

    const ThemeManager = {
        init() {
            this.applyTheme();
            this.applyColor();
            this.bindEvents();
            this.updateActiveStates();
        },

        getSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },

        applyTheme() {
            const theme = localStorage.getItem('theme') || 'system';
            const effectiveTheme = theme === 'system' ? this.getSystemTheme() : theme;
            document.documentElement.setAttribute('data-bs-theme', effectiveTheme);
        },

        applyColor() {
            const color = localStorage.getItem('color') || 'sky';
            document.documentElement.setAttribute('data-color', color);
        },

        setTheme(theme) {
            localStorage.setItem('theme', theme);
            this.applyTheme();
            this.updateActiveStates();
        },

        setColor(color) {
            localStorage.setItem('color', color);
            this.applyColor();
            this.updateActiveStates();
        },

        updateActiveStates() {
            const currentTheme = localStorage.getItem('theme') || 'system';
            const currentColor = localStorage.getItem('color') || 'sky';

            // Theme buttons
            document.querySelectorAll('#theme-selector [data-theme]').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.theme === currentTheme);
            });

            // Color swatches
            document.querySelectorAll('#color-selector [data-color]').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.color === currentColor);
            });
        },

        bindEvents() {
            // Theme buttons
            document.querySelectorAll('#theme-selector [data-theme]').forEach(btn => {
                btn.addEventListener('click', () => this.setTheme(btn.dataset.theme));
            });

            // Color swatches
            document.querySelectorAll('#color-selector [data-color]').forEach(btn => {
                btn.addEventListener('click', () => this.setColor(btn.dataset.color));
            });

            // System theme change listener
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (localStorage.getItem('theme') === 'system') {
                    this.applyTheme();
                }
            });
        }
    };

    // ========================================
    // Sidebar Management
    // ========================================

    const SidebarManager = {
        init() {
            this.sidebar = document.getElementById('sidebarOffcanvas');
            this.toggleBtn = document.getElementById('sidebar-toggle-btn');
            this.searchInput = document.getElementById('sidebar-search');
            this.noResultsMsg = document.getElementById('no-results-message');

            this.restoreState();
            this.bindEvents();
        },

        restoreState() {
            const collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (collapsed) {
                document.body.classList.add('sidebar-collapsed');
            }
        },

        toggle() {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        },

        filterMenu(query) {
            const menuLinks = document.querySelectorAll('#sidebar-nav .menu-link');
            const menuHeaders = document.querySelectorAll('#sidebar-nav .menu-header');
            const normalizedQuery = query.toLowerCase().trim();

            let visibleCount = 0;

            // Reset all
            menuLinks.forEach(link => {
                link.classList.remove('hidden', 'highlight');
            });
            menuHeaders.forEach(header => {
                header.classList.remove('hidden');
            });

            if (!normalizedQuery) {
                this.noResultsMsg?.classList.add('d-none');
                return;
            }

            // Filter links
            menuLinks.forEach(link => {
                const text = link.textContent.toLowerCase();
                const matches = text.includes(normalizedQuery);
                link.classList.toggle('hidden', !matches);
                if (matches) {
                    link.classList.add('highlight');
                    visibleCount++;
                }
            });

            // Hide headers with no visible children
            menuHeaders.forEach(header => {
                let nextEl = header.nextElementSibling;
                let hasVisibleChild = false;

                while (nextEl && !nextEl.classList.contains('menu-header')) {
                    if (nextEl.classList.contains('menu-link') && !nextEl.classList.contains('hidden')) {
                        hasVisibleChild = true;
                        break;
                    }
                    nextEl = nextEl.nextElementSibling;
                }

                header.classList.toggle('hidden', !hasVisibleChild);
            });

            // Show/hide no results message
            this.noResultsMsg?.classList.toggle('d-none', visibleCount > 0);
        },

        bindEvents() {
            // Toggle button
            this.toggleBtn?.addEventListener('click', () => this.toggle());

            // Search
            this.searchInput?.addEventListener('input', (e) => {
                this.filterMenu(e.target.value);
            });

            // Clear search on Escape
            this.searchInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    e.target.value = '';
                    this.filterMenu('');
                }
            });
        }
    };

    // ========================================
    // Date/Time Display
    // ========================================

    const DateTimeManager = {
        init() {
            this.dateEl = document.getElementById('header-date');
            this.timeEl = document.getElementById('header-time');

            if (this.dateEl || this.timeEl) {
                this.update();
                setInterval(() => this.update(), 1000);
            }
        },

        update() {
            const now = new Date();

            if (this.dateEl) {
                this.dateEl.textContent = now.toLocaleDateString('it-IT', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short'
                });
            }

            if (this.timeEl) {
                this.timeEl.textContent = now.toLocaleTimeString('it-IT', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    };

    // ========================================
    // Column Toggle Management
    // ========================================

    const ColumnToggleManager = {
        init() {
            this.bindEvents();
            this.restoreState();
        },

        getStorageKey(tableId) {
            return `columns-expanded-${tableId || 'default'}`;
        },

        restoreState() {
            document.querySelectorAll('.content table[data-table-id]').forEach(table => {
                const tableId = table.dataset.tableId;
                const isExpanded = localStorage.getItem(this.getStorageKey(tableId)) === 'true';
                if (isExpanded) {
                    table.classList.add('show-all-columns');
                    const btn = document.querySelector(`[data-toggle-table="${tableId}"]`);
                    if (btn) {
                        btn.classList.add('active');
                        this.updateButtonLabel(btn, true);
                    }
                }
            });
        },

        toggle(tableId) {
            const table = document.querySelector(`table[data-table-id="${tableId}"]`);
            const btn = document.querySelector(`[data-toggle-table="${tableId}"]`);

            if (!table) return;

            const isExpanded = table.classList.toggle('show-all-columns');
            localStorage.setItem(this.getStorageKey(tableId), isExpanded);

            if (btn) {
                btn.classList.toggle('active', isExpanded);
                this.updateButtonLabel(btn, isExpanded);
            }

            // Re-init Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },

        updateButtonLabel(btn, isExpanded) {
            const labelEl = btn.querySelector('.btn-label');
            if (labelEl) {
                labelEl.textContent = isExpanded ? 'Nascondi colonne' : 'Mostra tutte';
            }
            btn.setAttribute('title', isExpanded ? 'Nascondi colonne aggiuntive' : 'Mostra tutte le colonne');
        },

        bindEvents() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-toggle-table]');
                if (btn) {
                    e.preventDefault();
                    this.toggle(btn.dataset.toggleTable);
                }
            });
        }
    };

    // ========================================
    // Utilities
    // ========================================

    const Utils = {
        init() {
            this.initTooltips();
            this.initDropdownFix();
        },

        initTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
        },

        initDropdownFix() {
            // Keep dropdown open when clicking inside
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-group') || e.target.closest('.color-swatch')) {
                        e.stopPropagation();
                    }
                });
            });
        }
    };

    // ========================================
    // Initialize on DOM Ready
    // ========================================

    document.addEventListener('DOMContentLoaded', () => {
        ThemeManager.init();
        SidebarManager.init();
        DateTimeManager.init();
        ColumnToggleManager.init();
        Utils.init();

        // Re-init Lucide icons after dynamic content
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

})();
