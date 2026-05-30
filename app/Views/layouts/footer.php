        </div>
        <div class="flex-grow-1"></div>
        <?php if (!empty($_GET['page']) && $_GET['page'] !== 'login'): ?>
            <footer class="footer-area bg-white text-center rounded-10 rounded-bottom-0">
                <p class="fs-16 text-body">
                    © <?= date('Y'); ?>
                    <span class="text-secondary">
                        SIM Rumah Sakit
                    </span>
                </p>
            </footer>
        <?php endif; ?>
    </div>
</div>

<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/sidebar-menu.js') ?>"></script>
<script src="<?= asset('js/quill.min.js') ?>"></script>
<script src="<?= asset('js/data-table.js') ?>"></script>
<script src="<?= asset('js/prism.js') ?>"></script>
<script src="<?= asset('js/clipboard.min.js') ?>"></script>
<script src="<?= asset('js/simplebar.min.js') ?>"></script>
<script src="<?= asset('js/apexcharts.min.js') ?>"></script>
<script src="<?= asset('js/echarts.min.js') ?>"></script>
<script src="<?= asset('js/swiper-bundle.min.js') ?>"></script>
<script src="<?= asset('js/fullcalendar.main.js') ?>"></script>
<script src="<?= asset('js/jsvectormap.min.js') ?>"></script>
<script src="<?= asset('js/world-merc.js') ?>"></script>
<script src="<?= asset('js/custom/custom.js') ?>"></script>
<script src="<?= asset('js/tom-select.complete.min.js') ?>"></script>
<script>

function getPageFromUrl(url)
{
    const parsedUrl = new URL(url, window.location.origin);

    let page = parsedUrl.searchParams.get('page');

    if (page) {
        return page;
    }

    const appPath = new URL('<?= addslashes(baseUrl()) ?>').pathname.replace(/\/+$/, '');
    let path = parsedUrl.pathname;

    if (appPath && path.startsWith(appPath)) {
        path = path.slice(appPath.length);
    }

    page = path.replace(/^\/+|\/+$/g, '') || 'dashboard';

    // Bersihkan kalau parameter nyasar pakai &
    page = page.split('&')[0];

    return page || 'dashboard';
}

const postOnlyRoutes = <?= json_encode([
    'users-delete',
    'roles-permissions-update',
    'simrs-medical-items-delete',
    'parking-checkin-store',
    'parking-checkout-process',
    'parking-payments-store',
    'parking-validations-store'
]) ?>;

document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');

    if (!link || e.defaultPrevented || !postOnlyRoutes.includes(getPageFromUrl(link.href))) {
        return;
    }

    e.preventDefault();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = link.href;
    form.innerHTML = '<?= addslashes(csrfField()) ?>';
    document.body.appendChild(form);
    form.submit();
});

function reinitializePageScripts()
{
    applyListFilterUi();

    if (window.jQuery && $.fn.select2) {
        $('.select2').select2();
    }

    if (window.TomSelect) {
        document.querySelectorAll('.tom-select').forEach(el => {
            if (!el.tomselect) {
                new TomSelect(el);
            }
        });
    }

    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }
}

function applyListFilterUi()
{
    const statusFilterOptions = {
        'simrs-registration': [['registered', 'Registered'], ['waiting', 'Waiting'], ['in_consultation', 'In Consultation'], ['pharmacy', 'Pharmacy'], ['billing', 'Billing'], ['paid', 'Paid'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-queue': [['waiting', 'Waiting'], ['called', 'Called'], ['serving', 'Serving'], ['done', 'Done'], ['cancelled', 'Cancelled']],
        'simrs-outpatient': [['registered', 'Registered'], ['waiting', 'Waiting'], ['in_consultation', 'In Consultation'], ['pharmacy', 'Pharmacy'], ['billing', 'Billing'], ['paid', 'Paid'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-inpatient': [['registered', 'Registered'], ['waiting', 'Waiting'], ['in_consultation', 'In Consultation'], ['billing', 'Billing'], ['paid', 'Paid'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-laboratory': [['pending', 'Pending'], ['sample_taken', 'Sample Taken'], ['result_ready', 'Result Ready'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-radiology': [['pending', 'Pending'], ['scheduled', 'Scheduled'], ['result_ready', 'Result Ready'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-pharmacy': [['pending', 'Pending'], ['verified', 'Verified'], ['prepared', 'Prepared'], ['dispensed', 'Dispensed'], ['cancelled', 'Cancelled']],
        'simrs-billing': [['draft', 'Draft'], ['unpaid', 'Unpaid'], ['partial', 'Partial'], ['paid', 'Paid'], ['cancelled', 'Cancelled']],
        'simrs-cashier': [['draft', 'Draft'], ['unpaid', 'Unpaid'], ['partial', 'Partial'], ['paid', 'Paid'], ['cancelled', 'Cancelled']],
        'simrs-reports-visits': [['registered', 'Registered'], ['waiting', 'Waiting'], ['in_consultation', 'In Consultation'], ['pharmacy', 'Pharmacy'], ['billing', 'Billing'], ['paid', 'Paid'], ['completed', 'Completed'], ['cancelled', 'Cancelled']],
        'simrs-patients': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'simrs-doctors': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'simrs-polyclinics': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'simrs-doctor-schedules': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'simrs-medical-items': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-tickets': [['active', 'Active'], ['unpaid', 'Unpaid'], ['paid', 'Paid'], ['lost_ticket', 'Lost Ticket'], ['cancelled', 'Cancelled']],
        'parking-checkout': [['active', 'Active'], ['unpaid', 'Unpaid'], ['paid', 'Paid'], ['lost_ticket', 'Lost Ticket'], ['cancelled', 'Cancelled']],
        'parking-gate-queue': [['active', 'Active'], ['unpaid', 'Unpaid'], ['paid', 'Paid'], ['lost_ticket', 'Lost Ticket'], ['cancelled', 'Cancelled']],
        'parking-reports-transactions': [['active', 'Active'], ['unpaid', 'Unpaid'], ['paid', 'Paid'], ['lost_ticket', 'Lost Ticket'], ['cancelled', 'Cancelled']],
        'parking-reports-lost-ticket': [['lost_ticket', 'Lost Ticket'], ['unpaid', 'Unpaid'], ['paid', 'Paid']],
        'parking-areas': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-gates': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-vehicle-types': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-rates': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-members': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
        'parking-rate-settings': [['active', 'Aktif'], ['inactive', 'Nonaktif']],
    };
    const currentParams = new URLSearchParams(window.location.search);

    document.querySelectorAll('form').forEach(form => {
        const pageInput = form.querySelector('input[name="page"]');
        const searchInput = form.querySelector('input[name="search"]');
        const dateInputs = form.querySelectorAll('input[type="date"]');
        const submitButton = Array.from(form.querySelectorAll('button[type="submit"], button:not([type])'))
            .find(button => /^(Cari|Filter)$/i.test(button.textContent.trim()));

        if (!pageInput || !submitButton || (!searchInput && dateInputs.length === 0)) {
            return;
        }

        form.classList.add('simrs-list-filter');

        const statusOptions = statusFilterOptions[pageInput.value] || null;
        let hasStatusFilter = Boolean(form.querySelector('select[name="status"]'));
        if (statusOptions && !form.querySelector('select[name="status"]')) {
            const statusColumn = document.createElement('div');
            statusColumn.className = 'col-md-2';

            const statusSelect = document.createElement('select');
            statusSelect.name = 'status';
            statusSelect.className = 'form-select';
            statusSelect.innerHTML = '<option value="">Semua Status</option>' + statusOptions
                .map(option => `<option value="${option[0]}">${option[1]}</option>`)
                .join('');
            statusSelect.value = currentParams.get('status') || '';
            statusColumn.appendChild(statusSelect);

            const actionWrapper = submitButton.closest('[class*="col-"]') || submitButton.parentElement;
            if (actionWrapper && actionWrapper.parentElement === form) {
                form.insertBefore(statusColumn, actionWrapper);
                hasStatusFilter = true;
            }
        }

        submitButton.classList.remove('btn-outline-primary');
        submitButton.classList.remove('w-100');
        submitButton.classList.add('btn-primary', 'text-white', 'erp-btn');

        const actionColumn = submitButton.closest('[class*="col-"]') || submitButton.parentElement;
        if (!actionColumn) {
            return;
        }

        actionColumn.classList.add('simrs-filter-actions');

        form.querySelectorAll('select').forEach(select => {
            const selectColumn = select.closest('[class*="col-"]');
            if (selectColumn) {
                selectColumn.classList.add('simrs-filter-select');
            }
        });

        if (searchInput) {
            const searchColumn = searchInput.closest('[class*="col-"]');
            if (searchColumn) {
                searchColumn.classList.remove('col-md-10', 'col-md-8', 'col-md-5');
                searchColumn.classList.add(hasStatusFilter ? 'col-md-5' : 'col-md-7', 'simrs-filter-search');
            }

            actionColumn.classList.remove('col-md-4', 'col-md-3', 'col-md-2', 'col-md-auto');
            actionColumn.classList.add('col-md-2');
        } else {
            actionColumn.classList.remove('col-md-4', 'col-md-3', 'col-md-2', 'col-md-auto');
            actionColumn.classList.add('col-md-2');
        }

        const existingReset = Array.from(form.querySelectorAll('a.btn, a'))
            .find(link => /^Reset$/i.test(link.textContent.trim()));

        if (existingReset) {
            existingReset.classList.remove('w-100');
            existingReset.classList.add('simrs-filter-reset', 'erp-btn');
            const resetColumn = existingReset.closest('[class*="col-"]');
            if (resetColumn && resetColumn !== actionColumn) {
                actionColumn.appendChild(existingReset);
                if (resetColumn.children.length === 0) {
                    resetColumn.remove();
                }
            }
        }

        if (!existingReset && !actionColumn.querySelector('.simrs-filter-reset')) {
            const resetLink = document.createElement('a');
            resetLink.href = '<?= addslashes(rtrim(baseUrl(), '/')) ?>/' + pageInput.value;
            resetLink.className = 'btn btn-light erp-btn simrs-filter-reset';
            resetLink.textContent = 'Reset';
            actionColumn.appendChild(resetLink);
        }
    });
}

async function loadSidebarPage(url, pushState = true)
{
    const target = document.getElementById('app-content');

    if (!target) {
        window.location.href = url;
        return;
    }

    const requestedUrl = new URL(url, window.location.origin);
    requestedUrl.searchParams.set('partial', '1');
    target.setAttribute('aria-busy', 'true');
    target.classList.add('content-loading');

    try {
        const response = await fetch(requestedUrl.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        const html = await response.text();

        if (!response.ok || /<!doctype|<html/i.test(html) || response.url.includes('/login')) {
            window.location.href = url;
            return;
        }

        target.innerHTML = html;
        const partialTitle = target.querySelector('template[data-partial-title]');

        if (partialTitle) {
            document.title = partialTitle.dataset.partialTitle;
            partialTitle.remove();
        }

        executePartialPageScripts(target);
        reinitializePageScripts();
        updateActiveMenu(url);

        if (pushState) {
            window.history.pushState({ partialPage: true }, '', url);
        }
    } catch (error) {
        window.location.href = url;
    } finally {
        target.removeAttribute('aria-busy');
        target.classList.remove('content-loading');
    }
}

function executePartialPageScripts(container)
{
    const scripts = Array.from(container.querySelectorAll('script'));

    if (!scripts.length) {
        return;
    }

    const originalAddEventListener = document.addEventListener;

    document.addEventListener = function(type, listener, options) {
        if (type === 'DOMContentLoaded' && typeof listener === 'function') {
            listener.call(document, new Event('DOMContentLoaded'));
            return;
        }

        return originalAddEventListener.call(document, type, listener, options);
    };

    try {
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');

            Array.from(oldScript.attributes).forEach(attribute => {
                newScript.setAttribute(attribute.name, attribute.value);
            });

            newScript.textContent = oldScript.textContent;
            oldScript.replaceWith(newScript);
        });
    } finally {
        document.addEventListener = originalAddEventListener;
    }
}

function normalizeSidebarPage(page)
{
    if (!page) return 'simrs-dashboard';

    page = page
        .split('?')[0]
        .split('&')[0]
        .replace(/^\/+|\/+$/g, '');

    const pageMap = {
        'dashboard': 'simrs-dashboard',
        'process-login': 'login',
        'logout': 'login',

        'simrs-patients-create': 'simrs-patients',
        'simrs-patients-store': 'simrs-patients',
        'simrs-patients-edit': 'simrs-patients',
        'simrs-patients-update': 'simrs-patients',
        'simrs-patients-show': 'simrs-patients',

        'simrs-doctors-create': 'simrs-doctors',
        'simrs-doctors-store': 'simrs-doctors',
        'simrs-doctors-edit': 'simrs-doctors',
        'simrs-doctors-update': 'simrs-doctors',

        'simrs-polyclinics-create': 'simrs-polyclinics',
        'simrs-polyclinics-store': 'simrs-polyclinics',
        'simrs-polyclinics-edit': 'simrs-polyclinics',
        'simrs-polyclinics-update': 'simrs-polyclinics',

        'simrs-doctor-schedules-create': 'simrs-doctor-schedules',
        'simrs-doctor-schedules-store': 'simrs-doctor-schedules',
        'simrs-doctor-schedules-edit': 'simrs-doctor-schedules',
        'simrs-doctor-schedules-update': 'simrs-doctor-schedules',

        'simrs-registration-create': 'simrs-registration',
        'simrs-registration-store': 'simrs-registration',
        'simrs-queue-status': 'simrs-queue',
        'simrs-outpatient-examine': 'simrs-outpatient',
        'simrs-medical-records-store': 'simrs-outpatient',
        'simrs-diagnoses-store': 'simrs-outpatient',
        'simrs-treatments-store': 'simrs-outpatient',
        'simrs-prescriptions-store': 'simrs-outpatient',

        'simrs-pharmacy-show': 'simrs-pharmacy',
        'simrs-pharmacy-status': 'simrs-pharmacy',
        'simrs-pharmacy-dispense': 'simrs-pharmacy',

        'simrs-medical-items-create': 'simrs-medical-items',
        'simrs-medical-items-store': 'simrs-medical-items',
        'simrs-medical-items-edit': 'simrs-medical-items',
        'simrs-medical-items-update': 'simrs-medical-items',
        'simrs-medical-items-delete': 'simrs-medical-items',

        'simrs-billing-show': 'simrs-billing',
        'simrs-billing-add-item': 'simrs-billing',
        'simrs-cashier-pay': 'simrs-cashier',
        'simrs-reports-income': 'simrs-reports-visits',
        'simrs-reports-pharmacy': 'simrs-reports-visits',

        'parking-areas-create': 'parking-areas',
        'parking-areas-store': 'parking-areas',
        'parking-areas-edit': 'parking-areas',
        'parking-areas-update': 'parking-areas',
        'parking-gates-create': 'parking-gates',
        'parking-gates-store': 'parking-gates',
        'parking-gates-edit': 'parking-gates',
        'parking-gates-update': 'parking-gates',
        'parking-vehicle-types-create': 'parking-vehicle-types',
        'parking-vehicle-types-store': 'parking-vehicle-types',
        'parking-vehicle-types-edit': 'parking-vehicle-types',
        'parking-vehicle-types-update': 'parking-vehicle-types',
        'parking-rates-create': 'parking-rates',
        'parking-rates-store': 'parking-rates',
        'parking-rates-edit': 'parking-rates',
        'parking-rates-update': 'parking-rates',
        'parking-members-create': 'parking-members',
        'parking-members-store': 'parking-members',
        'parking-members-edit': 'parking-members',
        'parking-members-update': 'parking-members',
        'parking-tickets-show': 'parking-tickets',
        'parking-checkin-store': 'parking-checkin',
        'parking-checkout-process': 'parking-checkout',
        'parking-payments-store': 'parking-tickets',
        'parking-validations-store': 'parking-validations',
        'parking-gate-open': 'parking-tickets',
        'parking-reports-transactions': 'parking-reports-transactions',
        'parking-reports-income': 'parking-reports-income',
        'parking-reports-occupancy': 'parking-reports-occupancy',
        'parking-reports-duration': 'parking-reports-duration',
        'parking-reports-lost-ticket': 'parking-reports-lost-ticket',

        'users-create': 'users',
        'users-store': 'users',
        'users-edit': 'users',
        'users-update': 'users',
        'users-delete': 'users',
        'roles-create': 'roles',
        'roles-store': 'roles',
        'roles-edit': 'roles',
        'roles-update': 'roles',
        'roles-permissions': 'roles',
        'roles-permissions-update': 'roles'
    };

    return pageMap[page] || page;
}
function updateActiveMenu(url)
{
    const currentPage = normalizeSidebarPage(getPageFromUrl(url));

    document.querySelectorAll('#layout-menu .menu-item').forEach(item => {
        item.classList.remove('active', 'open');
    });

    document.querySelectorAll('#layout-menu .menu-link').forEach(link => {
        link.classList.remove('active');
    });

    let activeLink = null;

    document.querySelectorAll('#layout-menu a[href]').forEach(link => {
        const href = link.getAttribute('href');

        if (!href || href.includes('javascript:')) return;

        const linkPage = normalizeSidebarPage(getPageFromUrl(href));

        if (linkPage === currentPage) {
            activeLink = link;
        }
    });

    if (!activeLink) return;

    activeLink.classList.add('active');

    const activeItem = activeLink.closest('.menu-item');

    if (activeItem) {
        activeItem.classList.add('active');
    }

    let parentSub = activeLink.closest('.menu-sub');

    while (parentSub) {
        const parentItem = parentSub.closest('.menu-item');

        if (parentItem) {
            parentItem.classList.add('active', 'open');

            const parentToggle = parentItem.querySelector(':scope > .menu-link');
            if (parentToggle) {
                parentToggle.classList.add('active');
            }
        }

        parentSub = parentItem
            ? parentItem.parentElement.closest('.menu-sub')
            : null;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updateActiveMenu(window.location.href);
    reinitializePageScripts();
});

document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');

    if (!link
        || e.defaultPrevented
        || (!link.closest('#sidebar-area') && !link.closest('#app-content') && !link.closest('.custom-pagination'))
        || e.button !== 0
        || e.metaKey
        || e.ctrlKey
        || e.shiftKey
        || e.altKey
        || link.target === '_blank'
        || link.hasAttribute('download')
        || link.getAttribute('href').startsWith('javascript:')
        || link.getAttribute('href').startsWith('#')
        || getPageFromUrl(link.href) === 'logout'
        || postOnlyRoutes.includes(getPageFromUrl(link.href))) {
        return;
    }

    const destination = new URL(link.href, window.location.origin);

    if (destination.origin !== window.location.origin) {
        return;
    }

    destination.searchParams.delete('partial');

    e.preventDefault();
    loadSidebarPage(destination.toString());
});

window.addEventListener('popstate', function() {
    loadSidebarPage(window.location.href, false);
});

</script>
</body>
</html>
