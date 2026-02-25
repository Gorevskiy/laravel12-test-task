<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 Демо-панель</title>
    <style>
        :root {
            --bg: #eef3f8;
            --card: #ffffff;
            --line: #d9e2ec;
            --line-dark: #bcccdc;
            --text: #102a43;
            --muted: #627d98;
            --primary: #0052cc;
            --primary-dark: #003e99;
            --danger: #cc2f2f;
            --warning: #b76e00;
            --ok: #0f7b41;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "IBM Plex Sans", "Segoe UI", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top right, #dce8ff, var(--bg));
        }

        .page {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 16px 24px;
            display: grid;
            gap: 14px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 10px 22px rgba(16, 42, 67, 0.08);
            padding: 14px;
        }

        h1 {
            margin: 0;
            font-size: 26px;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 17px;
        }

        p {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .toolbar {
            display: grid;
            gap: 10px;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
            background: var(--primary);
            transition: background 0.2s ease;
        }

        .btn:hover { background: var(--primary-dark); }

        .btn-secondary { background: #486581; }
        .btn-secondary:hover { background: #334e68; }

        .btn-danger { background: var(--danger); }
        .btn-danger:hover { background: #a82727; }

        .btn-warning { background: var(--warning); }
        .btn-warning:hover { background: #9b5e00; }

        .btn-xs {
            font-size: 12px;
            padding: 6px 8px;
            border-radius: 6px;
        }

        .tables-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .table-card {
            min-height: 290px;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 8px;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }

        th,
        td {
            border-bottom: 1px solid var(--line);
            padding: 8px;
            font-size: 13px;
            text-align: left;
            vertical-align: top;
            background: #fff;
        }

        thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f7faff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #486581;
        }

        tbody tr:hover td {
            background: #f8fbff;
        }

        .actions {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            align-items: center;
            white-space: nowrap;
        }

        .actions .btn {
            flex: 0 0 auto;
        }

        .meta {
            font-size: 12px;
            color: var(--muted);
        }

        .bottom {
            display: grid;
            gap: 12px;
            grid-template-columns: 2fr 1fr;
        }

        pre {
            margin: 0;
            background: #0b1f3a;
            color: #d9e2ec;
            border-radius: 10px;
            padding: 12px;
            max-height: 320px;
            overflow: auto;
            font-size: 12px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        #socketStatus.ok { color: var(--ok); }
        #socketStatus.fail { color: var(--danger); }

        #eventLog {
            margin: 10px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 6px;
            max-height: 260px;
            overflow: auto;
        }

        #eventLog li {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 8px;
            font-size: 12px;
            background: #f8fbff;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(16, 42, 67, 0.52);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal {
            width: min(520px, 100%);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 18px 36px rgba(16, 42, 67, 0.25);
            overflow: hidden;
        }

        .modal.modal-order {
            width: min(1240px, 97vw);
        }

        .modal.modal-category-products {
            width: min(1080px, 96vw);
        }

        .modal-head {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(135deg, #f8fbff 0%, #edf3ff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
        }

        .modal-close {
            border: none;
            background: transparent;
            color: #486581;
            cursor: pointer;
            font-size: 22px;
            line-height: 1;
            padding: 0;
        }

        .modal-body {
            padding: 16px;
            display: grid;
            gap: 12px;
        }

        .modal-field {
            display: grid;
            gap: 5px;
            font-size: 13px;
            color: #486581;
        }

        .modal-field input,
        .modal-field select {
            width: 100%;
            border: 1px solid var(--line-dark);
            border-radius: 8px;
            font-size: 14px;
            padding: 9px 10px;
            color: var(--text);
            background: #fff;
        }

        .modal-error {
            min-height: 18px;
            font-size: 12px;
            color: var(--danger);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 0 16px 16px;
        }

        .modal-subsection {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f8fbff;
            padding: 10px;
            display: grid;
            gap: 8px;
        }

        .modal-subsection-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .modal-table-wrap {
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
        }

        .modal-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 0;
        }

        .modal-table th,
        .modal-table td {
            border-bottom: 1px solid var(--line);
            padding: 7px;
            font-size: 12px;
            text-align: left;
            vertical-align: top;
            background: #fff;
        }

        @media (max-width: 1100px) {
            .tables-grid {
                grid-template-columns: 1fr;
            }

            .bottom {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <section class="card">
        <h1>Laravel 12 Демо-панель</h1>
        <p>Табличный интерфейс для публичного REST API: пользователи, категории, товары, заказы и realtime-события.</p>
    </section>

    <section class="card toolbar">
        <h2>Быстрые действия</h2>

        <div class="toolbar-actions">
            <button class="btn" id="reloadBtn">Обновить все таблицы</button>
            <button class="btn" id="createUserBtn">Создать пользователя</button>
            <button class="btn" id="createCategoryBtn">Создать категорию</button>
            <button class="btn" id="createProductBtn">Создать товар</button>
            <button class="btn" id="createOrderBtn">Создать заказ</button>
            <button class="btn btn-warning" id="addItemBtn">Добавить товар в заказ</button>
            <button class="btn btn-danger" id="removeItemBtn">Удалить товар из заказа</button>
        </div>
    </section>

    <section class="tables-grid">
        <article class="card table-card">
            <div>
                <h2>Пользователи</h2>
                <div class="meta" id="usersMeta">Загрузка...</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody id="usersTableBody"></tbody>
                </table>
            </div>
        </article>

        <article class="card table-card">
            <div>
                <h2>Категории</h2>
                <div class="meta" id="categoriesMeta">Загрузка...</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Товаров</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody id="categoriesTableBody"></tbody>
                </table>
            </div>
        </article>

        <article class="card table-card">
            <div>
                <h2>Товары</h2>
                <div class="meta" id="productsMeta">Загрузка...</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody id="productsTableBody"></tbody>
                </table>
            </div>
        </article>

        <article class="card table-card">
            <div>
                <h2>Заказы</h2>
                <div class="meta" id="ordersMeta">Загрузка...</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody id="ordersTableBody"></tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="bottom">
        <article class="card">
            <h2>Результат API / детали</h2>
            <pre id="resultOutput">Нажмите действие в любой таблице или используйте панель быстрых действий.</pre>
        </article>

        <article class="card">
            <h2>WebSocket события</h2>
            <p>Статус: <span id="socketStatus" class="fail">не подключено</span></p>
            <ul id="eventLog"></ul>
        </article>
    </section>
</div>

<div class="modal-backdrop" id="userModalBackdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="userModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="userModalTitle">Пользователь</h3>
            <button class="modal-close" id="userModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <label class="modal-field">Имя пользователя
                <input id="userModalName" type="text" placeholder="Иван Петров">
            </label>
            <label class="modal-field">Email
                <input id="userModalEmail" type="email" placeholder="ivan@example.com">
            </label>
            <label class="modal-field">Телефон
                <input id="userModalPhone" type="text" placeholder="+79991234567">
            </label>
            <div class="modal-error" id="userModalError"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="userModalCancelBtn" type="button">Отмена</button>
            <button class="btn" id="userModalSaveBtn" type="button">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="categoryModalBackdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="categoryModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="categoryModalTitle">Категория</h3>
            <button class="modal-close" id="categoryModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <label class="modal-field">Наименование категории
                <input id="categoryModalName" type="text" placeholder="Например, Электроника">
            </label>
            <div class="modal-error" id="categoryModalError"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="categoryModalCancelBtn" type="button">Отмена</button>
            <button class="btn" id="categoryModalSaveBtn" type="button">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="productModalBackdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="productModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="productModalTitle">Товар</h3>
            <button class="modal-close" id="productModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <label class="modal-field">Название товара
                <input id="productModalName" type="text" placeholder="Например, Беспроводная мышь">
            </label>
            <label class="modal-field">Цена
                <input id="productModalPrice" type="number" min="0" step="0.01" placeholder="99.99">
            </label>
            <label class="modal-field">Категория
                <select id="productModalCategoryId"></select>
            </label>
            <div class="modal-error" id="productModalError"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="productModalCancelBtn" type="button">Отмена</button>
            <button class="btn" id="productModalSaveBtn" type="button">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="orderModalBackdrop">
    <div class="modal modal-order" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="orderModalTitle">Заказ</h3>
            <button class="modal-close" id="orderModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <label class="modal-field">Пользователь
                <select id="orderModalUserId"></select>
            </label>
            <label class="modal-field">Статус
                <input id="orderModalStatus" type="text" placeholder="new">
            </label>
            <div class="modal-subsection">
                <div class="modal-subsection-head">
                    <strong>Товары в заказе</strong>
                    <button class="btn btn-xs" id="orderModalAddItemBtn" type="button">Добавить</button>
                </div>
                <div class="meta" id="orderItemsMeta">Товары не загружены.</div>
                <div class="modal-table-wrap">
                    <table class="modal-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Товар</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody id="orderItemsTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-error" id="orderModalError"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="orderModalCancelBtn" type="button">Отмена</button>
            <button class="btn" id="orderModalSaveBtn" type="button">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="orderItemModalBackdrop">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="orderItemModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="orderItemModalTitle">Добавить товар в заказ</h3>
            <button class="modal-close" id="orderItemModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <label class="modal-field">Товар
                <select id="orderItemModalProductId"></select>
            </label>
            <label class="modal-field">Количество
                <input id="orderItemModalQuantity" type="number" min="1" step="1" value="1">
            </label>
            <div class="modal-error" id="orderItemModalError"></div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="orderItemModalCancelBtn" type="button">Отмена</button>
            <button class="btn" id="orderItemModalSaveBtn" type="button">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="categoryProductsModalBackdrop">
    <div class="modal modal-category-products" role="dialog" aria-modal="true" aria-labelledby="categoryProductsModalTitle">
        <div class="modal-head">
            <h3 class="modal-title" id="categoryProductsModalTitle">Товары категории</h3>
            <button class="modal-close" id="categoryProductsModalCloseBtn" type="button" aria-label="Закрыть">&times;</button>
        </div>
        <div class="modal-body">
            <div class="meta" id="categoryProductsModalMeta">Загрузка...</div>
            <div class="modal-table-wrap">
                <table class="modal-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Категория</th>
                    </tr>
                    </thead>
                    <tbody id="categoryProductsTableBody"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn" id="categoryProductsModalOkBtn" type="button">ОК</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    const byId = (id) => document.getElementById(id);

    const resultOutput = byId('resultOutput');
    const usersBody = byId('usersTableBody');
    const categoriesBody = byId('categoriesTableBody');
    const productsBody = byId('productsTableBody');
    const ordersBody = byId('ordersTableBody');

    const usersMeta = byId('usersMeta');
    const categoriesMeta = byId('categoriesMeta');
    const productsMeta = byId('productsMeta');
    const ordersMeta = byId('ordersMeta');

    const userModalBackdrop = byId('userModalBackdrop');
    const userModalTitle = byId('userModalTitle');
    const userModalName = byId('userModalName');
    const userModalEmail = byId('userModalEmail');
    const userModalPhone = byId('userModalPhone');
    const userModalError = byId('userModalError');
    const userModalSaveBtn = byId('userModalSaveBtn');
    const userModalCancelBtn = byId('userModalCancelBtn');
    const userModalCloseBtn = byId('userModalCloseBtn');
    const categoryModalBackdrop = byId('categoryModalBackdrop');
    const categoryModalTitle = byId('categoryModalTitle');
    const categoryModalName = byId('categoryModalName');
    const categoryModalError = byId('categoryModalError');
    const categoryModalSaveBtn = byId('categoryModalSaveBtn');
    const categoryModalCancelBtn = byId('categoryModalCancelBtn');
    const categoryModalCloseBtn = byId('categoryModalCloseBtn');
    const productModalBackdrop = byId('productModalBackdrop');
    const productModalTitle = byId('productModalTitle');
    const productModalName = byId('productModalName');
    const productModalPrice = byId('productModalPrice');
    const productModalCategoryId = byId('productModalCategoryId');
    const productModalError = byId('productModalError');
    const productModalSaveBtn = byId('productModalSaveBtn');
    const productModalCancelBtn = byId('productModalCancelBtn');
    const productModalCloseBtn = byId('productModalCloseBtn');
    const orderModalBackdrop = byId('orderModalBackdrop');
    const orderModalTitle = byId('orderModalTitle');
    const orderModalUserId = byId('orderModalUserId');
    const orderModalStatus = byId('orderModalStatus');
    const orderModalError = byId('orderModalError');
    const orderModalSaveBtn = byId('orderModalSaveBtn');
    const orderModalCancelBtn = byId('orderModalCancelBtn');
    const orderModalCloseBtn = byId('orderModalCloseBtn');
    const orderModalAddItemBtn = byId('orderModalAddItemBtn');
    const orderItemsMeta = byId('orderItemsMeta');
    const orderItemsTableBody = byId('orderItemsTableBody');
    const orderItemModalBackdrop = byId('orderItemModalBackdrop');
    const orderItemModalProductId = byId('orderItemModalProductId');
    const orderItemModalQuantity = byId('orderItemModalQuantity');
    const orderItemModalError = byId('orderItemModalError');
    const orderItemModalSaveBtn = byId('orderItemModalSaveBtn');
    const orderItemModalCancelBtn = byId('orderItemModalCancelBtn');
    const orderItemModalCloseBtn = byId('orderItemModalCloseBtn');
    const categoryProductsModalBackdrop = byId('categoryProductsModalBackdrop');
    const categoryProductsModalTitle = byId('categoryProductsModalTitle');
    const categoryProductsModalMeta = byId('categoryProductsModalMeta');
    const categoryProductsTableBody = byId('categoryProductsTableBody');
    const categoryProductsModalOkBtn = byId('categoryProductsModalOkBtn');
    const categoryProductsModalCloseBtn = byId('categoryProductsModalCloseBtn');

    const state = {
        users: [],
        categories: [],
        products: [],
        orders: [],
        selected: {
            userId: 1,
            categoryId: 1,
            productId: 1,
            orderId: 1,
            quantity: 1,
        },
        userModal: {
            open: false,
            mode: 'create',
            userId: null,
        },
        categoryModal: {
            open: false,
            mode: 'create',
            categoryId: null,
        },
        productModal: {
            open: false,
            mode: 'create',
            productId: null,
        },
        orderModal: {
            open: false,
            mode: 'create',
            orderId: null,
            order: null,
        },
        orderItemModal: {
            open: false,
        },
        categoryProductsModal: {
            open: false,
            categoryId: null,
            categoryName: '',
            products: [],
        },
        productCatalog: [],
    };

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const print = (data) => {
        resultOutput.textContent = JSON.stringify(data, null, 2);
    };

    const api = async (url, options = {}) => {
        const response = await fetch(url, {
            headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
            ...options,
        });

        const contentType = response.headers.get('content-type') || '';
        const body = contentType.includes('application/json') ? await response.json() : await response.text();

        if (!response.ok) {
            throw { status: response.status, body };
        }

        return body;
    };

    const toList = (payload) => {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        return [];
    };

    const randomInt = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

    const currentIds = () => ({ ...state.selected });

    const setDefaultIds = () => {
        if (state.users[0]?.id) state.selected.userId = state.users[0].id;
        if (state.categories[0]?.id) state.selected.categoryId = state.categories[0].id;
        if (state.products[0]?.id) {
            state.selected.productId = state.products[0].id;
            if (state.products[0].category_id) {
                state.selected.categoryId = state.products[0].category_id;
            }
        }
        if (state.orders[0]?.id) state.selected.orderId = state.orders[0].id;
    };

    const resetUserModalError = () => {
        userModalError.textContent = '';
    };

    const openUserModal = ({ mode = 'create', user = null } = {}) => {
        state.userModal.open = true;
        state.userModal.mode = mode;
        state.userModal.userId = mode === 'edit' ? user?.id ?? null : null;

        userModalTitle.textContent = mode === 'edit' ? `Пользователь #${user?.id ?? ''}` : 'Создать пользователя';
        userModalSaveBtn.textContent = mode === 'edit' ? 'Сохранить' : 'Сохранить';

        userModalName.value = user?.name ?? '';
        userModalEmail.value = user?.email ?? '';
        userModalPhone.value = user?.profile?.phone ?? '';
        resetUserModalError();

        userModalBackdrop.classList.add('show');
        setTimeout(() => userModalName.focus(), 0);
    };

    const closeUserModal = () => {
        state.userModal.open = false;
        state.userModal.userId = null;
        userModalBackdrop.classList.remove('show');
        resetUserModalError();
    };

    const openUserEditor = async (id) => {
        try {
            const user = await api(`/api/users/${id}`);
            openUserModal({ mode: 'edit', user: user.data });
        } catch (error) {
            print(error);
        }
    };

    const resetCategoryModalError = () => {
        categoryModalError.textContent = '';
    };

    const openCategoryModal = ({ mode = 'create', category = null } = {}) => {
        state.categoryModal.open = true;
        state.categoryModal.mode = mode;
        state.categoryModal.categoryId = mode === 'edit' ? category?.id ?? null : null;

        categoryModalTitle.textContent = mode === 'edit'
            ? `Категория #${category?.id ?? ''}`
            : 'Создать категорию';
        categoryModalSaveBtn.textContent = 'Сохранить';
        categoryModalName.value = category?.name ?? '';
        resetCategoryModalError();

        categoryModalBackdrop.classList.add('show');
        setTimeout(() => categoryModalName.focus(), 0);
    };

    const closeCategoryModal = () => {
        state.categoryModal.open = false;
        state.categoryModal.categoryId = null;
        categoryModalBackdrop.classList.remove('show');
        resetCategoryModalError();
    };

    const openCategoryEditor = async (id) => {
        try {
            const category = await api(`/api/categories/${id}`);
            openCategoryModal({ mode: 'edit', category: category.data });
        } catch (error) {
            print(error);
        }
    };

    const resetProductModalError = () => {
        productModalError.textContent = '';
    };

    const renderProductCategoryOptions = (selectedCategoryId = null) => {
        if (!state.categories.length) {
            productModalCategoryId.innerHTML = '<option value="">Нет доступных категорий</option>';
            productModalCategoryId.value = '';
            return;
        }

        productModalCategoryId.innerHTML = state.categories
            .map((category) => `<option value="${category.id}">${escapeHtml(category.name)} (ID ${category.id})</option>`)
            .join('');

        const preferredId = Number(selectedCategoryId ?? state.selected.categoryId ?? state.categories[0].id);
        const hasPreferred = state.categories.some((category) => Number(category.id) === preferredId);
        productModalCategoryId.value = String(hasPreferred ? preferredId : state.categories[0].id);
    };

    const openProductModal = ({ mode = 'create', product = null } = {}) => {
        state.productModal.open = true;
        state.productModal.mode = mode;
        state.productModal.productId = mode === 'edit' ? product?.id ?? null : null;

        productModalTitle.textContent = mode === 'edit'
            ? `Товар #${product?.id ?? ''}`
            : 'Создать товар';
        productModalSaveBtn.textContent = 'Сохранить';
        productModalName.value = product?.name ?? '';
        productModalPrice.value = product?.price ?? '';
        renderProductCategoryOptions(product?.category_id);
        resetProductModalError();

        productModalBackdrop.classList.add('show');
        setTimeout(() => productModalName.focus(), 0);
    };

    const closeProductModal = () => {
        state.productModal.open = false;
        state.productModal.productId = null;
        productModalBackdrop.classList.remove('show');
        resetProductModalError();
    };

    const openProductEditor = async (id) => {
        try {
            const product = await api(`/api/products/${id}`);
            openProductModal({ mode: 'edit', product: product.data });
        } catch (error) {
            print(error);
        }
    };

    const resetOrderModalError = () => {
        orderModalError.textContent = '';
    };

    const resetOrderItemModalError = () => {
        orderItemModalError.textContent = '';
    };

    const renderOrderUserOptions = (selectedUserId = null) => {
        if (!state.users.length) {
            orderModalUserId.innerHTML = '<option value="">Нет доступных пользователей</option>';
            orderModalUserId.value = '';
            return;
        }

        orderModalUserId.innerHTML = state.users
            .map((user) => `<option value="${user.id}">${escapeHtml(user.name)} (ID ${user.id})</option>`)
            .join('');

        const preferredId = Number(selectedUserId ?? state.selected.userId ?? state.users[0].id);
        const hasPreferred = state.users.some((user) => Number(user.id) === preferredId);
        orderModalUserId.value = String(hasPreferred ? preferredId : state.users[0].id);
    };

    const getOrderItems = (order) => Array.isArray(order?.items) ? order.items : [];

    const getCategoryNameByProductId = (productId) => {
        const product = state.products.find((entry) => Number(entry.id) === Number(productId))
            || state.productCatalog.find((entry) => Number(entry.id) === Number(productId));
        if (!product) return '—';

        const category = state.categories.find((entry) => Number(entry.id) === Number(product.category_id));
        return category?.name ?? `ID ${product.category_id ?? '—'}`;
    };

    const renderOrderItemsTable = () => {
        const order = state.orderModal.order;
        const orderId = state.orderModal.orderId;

        if (!orderId || !order) {
            orderItemsMeta.textContent = 'Сначала сохраните заказ, затем можно управлять товарами.';
            orderItemsTableBody.innerHTML = '<tr><td colspan="7">Заказ еще не создан.</td></tr>';
            orderModalAddItemBtn.disabled = true;
            return;
        }

        const items = getOrderItems(order);
        orderModalAddItemBtn.disabled = false;

        if (!items.length) {
            orderItemsMeta.textContent = 'Товаров в заказе нет.';
            orderItemsTableBody.innerHTML = '<tr><td colspan="7">Позиции отсутствуют.</td></tr>';
            return;
        }

        const total = items.reduce((sum, item) => sum + Number(item.line_total ?? (Number(item.quantity) * Number(item.price_at_purchase))), 0);
        orderItemsMeta.textContent = `Позиции: ${items.length}, сумма по позициям: ${total.toFixed(2)}`;

        orderItemsTableBody.innerHTML = items.map((item) => `
            <tr>
                <td>${item.id}</td>
                <td>${escapeHtml(item.name)}</td>
                <td>${escapeHtml(getCategoryNameByProductId(item.id))}</td>
                <td>${escapeHtml(item.price_at_purchase ?? item.price ?? '0')}</td>
                <td>${escapeHtml(item.quantity ?? 0)}</td>
                <td>${escapeHtml(item.line_total ?? '0')}</td>
                <td>
                    <button class="btn btn-xs btn-danger" data-action="remove-item" data-product-id="${item.id}" type="button">Удалить</button>
                </td>
            </tr>
        `).join('');
    };

    const refreshOrderInModal = async () => {
        if (!state.orderModal.orderId) return;

        const orderResponse = await api(`/api/orders/${state.orderModal.orderId}`);
        state.orderModal.order = orderResponse.data;
        orderModalStatus.value = orderResponse.data?.status ?? 'new';
        renderOrderItemsTable();
    };

    const ensureProductCatalog = async () => {
        const perPage = 100;
        const firstPage = await api(`/api/products?per_page=${perPage}&page=1`);
        const combined = [...toList(firstPage)];
        const lastPage = Number(firstPage?.meta?.last_page ?? 1);

        for (let page = 2; page <= lastPage; page++) {
            const nextPage = await api(`/api/products?per_page=${perPage}&page=${page}`);
            combined.push(...toList(nextPage));
        }

        state.productCatalog = combined;
    };

    const renderOrderItemProductOptions = (selectedProductId = null) => {
        if (!state.productCatalog.length) {
            orderItemModalProductId.innerHTML = '<option value="">Нет доступных товаров</option>';
            orderItemModalProductId.value = '';
            return;
        }

        const groups = state.categories.map((category) => {
            const products = state.productCatalog.filter((product) => Number(product.category_id) === Number(category.id));
            return { category, products };
        });

        const optionsHtml = groups
            .filter((group) => group.products.length > 0)
            .map((group) => {
                const options = group.products
                    .map((product) => `<option value="${product.id}">${escapeHtml(product.name)} (ID ${product.id}, ${escapeHtml(product.price)})</option>`)
                    .join('');

                return `<optgroup label="${escapeHtml(group.category.name)}">${options}</optgroup>`;
            })
            .join('');

        orderItemModalProductId.innerHTML = optionsHtml || '<option value="">Нет доступных товаров</option>';
        const fallbackId = state.productCatalog[0]?.id;
        const preferred = Number(selectedProductId ?? fallbackId);
        const exists = state.productCatalog.some((product) => Number(product.id) === preferred);
        orderItemModalProductId.value = String(exists ? preferred : fallbackId ?? '');
    };

    const openOrderItemModal = async () => {
        if (!state.orderModal.orderId) {
            orderModalError.textContent = 'Сначала сохраните заказ.';
            return;
        }

        await ensureProductCatalog();
        renderOrderItemProductOptions(state.selected.productId);
        orderItemModalQuantity.value = String(Math.max(1, Number(state.selected.quantity || 1)));
        resetOrderItemModalError();

        state.orderItemModal.open = true;
        orderItemModalBackdrop.classList.add('show');
        setTimeout(() => orderItemModalProductId.focus(), 0);
    };

    const closeOrderItemModal = () => {
        state.orderItemModal.open = false;
        orderItemModalBackdrop.classList.remove('show');
        resetOrderItemModalError();
    };

    const renderCategoryProductsTable = () => {
        const products = state.categoryProductsModal.products;
        const categoryName = state.categoryProductsModal.categoryName || '—';

        if (!products.length) {
            categoryProductsModalMeta.textContent = 'Товары в категории не найдены.';
            categoryProductsTableBody.innerHTML = '<tr><td colspan="4">Нет товаров.</td></tr>';
            return;
        }

        categoryProductsModalMeta.textContent = `Найдено товаров: ${products.length}`;
        categoryProductsTableBody.innerHTML = products.map((product) => `
            <tr>
                <td>${product.id}</td>
                <td>${escapeHtml(product.name)}</td>
                <td>${escapeHtml(product.price)}</td>
                <td>${escapeHtml(categoryName)}</td>
            </tr>
        `).join('');
    };

    const loadCategoryProducts = async (categoryId) => {
        const perPage = 100;
        const firstPage = await api(`/api/products?category_id=${categoryId}&per_page=${perPage}&page=1`);
        const list = [...toList(firstPage)];
        const lastPage = Number(firstPage?.meta?.last_page ?? 1);

        for (let page = 2; page <= lastPage; page++) {
            const nextPage = await api(`/api/products?category_id=${categoryId}&per_page=${perPage}&page=${page}`);
            list.push(...toList(nextPage));
        }

        return list;
    };

    const closeCategoryProductsModal = () => {
        state.categoryProductsModal.open = false;
        state.categoryProductsModal.categoryId = null;
        state.categoryProductsModal.categoryName = '';
        state.categoryProductsModal.products = [];
        categoryProductsModalBackdrop.classList.remove('show');
    };

    const openCategoryProductsModal = async (categoryId) => {
        const category = state.categories.find((entry) => Number(entry.id) === Number(categoryId));
        const categoryName = category?.name ?? `ID ${categoryId}`;

        state.categoryProductsModal.open = true;
        state.categoryProductsModal.categoryId = Number(categoryId);
        state.categoryProductsModal.categoryName = categoryName;
        state.categoryProductsModal.products = [];

        categoryProductsModalTitle.textContent = `Товары категории: ${categoryName}`;
        categoryProductsModalMeta.textContent = 'Загрузка...';
        categoryProductsTableBody.innerHTML = '<tr><td colspan="4">Загрузка...</td></tr>';
        categoryProductsModalBackdrop.classList.add('show');

        try {
            const products = await loadCategoryProducts(categoryId);
            state.categoryProductsModal.products = products;
            renderCategoryProductsTable();
        } catch (error) {
            categoryProductsModalMeta.textContent = 'Не удалось загрузить товары категории.';
            categoryProductsTableBody.innerHTML = '<tr><td colspan="4">Ошибка загрузки.</td></tr>';
            print(error);
        }
    };

    const openOrderModal = ({ mode = 'create', order = null } = {}) => {
        state.orderModal.open = true;
        state.orderModal.mode = mode;
        state.orderModal.orderId = mode === 'edit' ? order?.id ?? null : null;
        state.orderModal.order = order ?? null;

        orderModalTitle.textContent = mode === 'edit'
            ? `Заказ #${order?.id ?? ''}`
            : 'Создать заказ';
        orderModalSaveBtn.textContent = 'Сохранить';
        renderOrderUserOptions(order?.user_id);
        orderModalUserId.disabled = mode === 'edit';
        orderModalStatus.value = order?.status ?? 'new';
        resetOrderModalError();
        renderOrderItemsTable();
        ensureProductCatalog()
            .then(() => {
                if (state.orderModal.open) {
                    renderOrderItemsTable();
                }
            })
            .catch(() => {});

        orderModalBackdrop.classList.add('show');
        setTimeout(() => orderModalStatus.focus(), 0);
    };

    const closeOrderModal = () => {
        state.orderModal.open = false;
        state.orderModal.orderId = null;
        state.orderModal.order = null;
        orderModalBackdrop.classList.remove('show');
        resetOrderModalError();
        closeOrderItemModal();
    };

    const openOrderEditor = async (id) => {
        try {
            const order = await api(`/api/orders/${id}`);
            openOrderModal({ mode: 'edit', order: order.data });
        } catch (error) {
            print(error);
        }
    };

    const renderUsersTable = () => {
        usersMeta.textContent = `Всего: ${state.users.length}`;

        if (!state.users.length) {
            usersBody.innerHTML = '<tr><td colspan="5">Нет данных</td></tr>';
            return;
        }

        usersBody.innerHTML = state.users.map((user) => `
            <tr>
                <td>${user.id}</td>
                <td>${escapeHtml(user.name)}</td>
                <td>${escapeHtml(user.email)}</td>
                <td>${escapeHtml(user.profile?.phone ?? '—')}</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-xs btn-warning" data-entity="user" data-action="edit" data-id="${user.id}">Изменить</button>
                        <button class="btn btn-xs" data-entity="user" data-action="order" data-id="${user.id}">Создать заказ</button>
                        <button class="btn btn-xs btn-danger" data-entity="user" data-action="delete" data-id="${user.id}">Удалить</button>
                    </div>
                </td>
            </tr>
        `).join('');
    };

    const renderCategoriesTable = () => {
        categoriesMeta.textContent = `Всего: ${state.categories.length}`;

        if (!state.categories.length) {
            categoriesBody.innerHTML = '<tr><td colspan="4">Нет данных</td></tr>';
            return;
        }

        categoriesBody.innerHTML = state.categories.map((category) => `
            <tr>
                <td>${category.id}</td>
                <td>${escapeHtml(category.name)}</td>
                <td>${category.products?.length ?? 0}</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-xs" data-entity="category" data-action="products" data-id="${category.id}">Товары</button>
                        <button class="btn btn-xs btn-warning" data-entity="category" data-action="edit" data-id="${category.id}">Изменить</button>
                        <button class="btn btn-xs btn-danger" data-entity="category" data-action="delete" data-id="${category.id}">Удалить</button>
                    </div>
                </td>
            </tr>
        `).join('');
    };

    const renderProductsTable = () => {
        productsMeta.textContent = `Показано: ${state.products.length}`;

        if (!state.products.length) {
            productsBody.innerHTML = '<tr><td colspan="5">Нет данных</td></tr>';
            return;
        }

        productsBody.innerHTML = state.products.map((product) => `
            <tr>
                <td>${product.id}</td>
                <td>${escapeHtml(product.name)}</td>
                <td>${product.category_id}</td>
                <td>${escapeHtml(product.price)}</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-xs btn-warning" data-entity="product" data-action="edit" data-id="${product.id}">Изменить</button>
                        <button class="btn btn-xs" data-entity="product" data-action="add-item" data-id="${product.id}">В заказ</button>
                        <button class="btn btn-xs btn-danger" data-entity="product" data-action="delete" data-id="${product.id}">Удалить</button>
                    </div>
                </td>
            </tr>
        `).join('');
    };

    const renderOrdersTable = () => {
        ordersMeta.textContent = `Показано: ${state.orders.length}`;

        if (!state.orders.length) {
            ordersBody.innerHTML = '<tr><td colspan="5">Нет данных</td></tr>';
            return;
        }

        ordersBody.innerHTML = state.orders.map((order) => `
            <tr>
                <td>${order.id}</td>
                <td>${order.user_id}</td>
                <td>${escapeHtml(order.status)}</td>
                <td>${escapeHtml(order.total)}</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-xs btn-secondary" data-entity="order" data-action="show" data-id="${order.id}">Открыть</button>
                        <button class="btn btn-xs btn-warning" data-entity="order" data-action="status" data-id="${order.id}">Статус</button>
                        <button class="btn btn-xs" data-entity="order" data-action="add-item" data-id="${order.id}">+ Товар</button>
                        <button class="btn btn-xs" data-entity="order" data-action="remove-item" data-id="${order.id}">- Товар</button>
                        <button class="btn btn-xs btn-danger" data-entity="order" data-action="delete" data-id="${order.id}">Удалить</button>
                    </div>
                </td>
            </tr>
        `).join('');
    };

    const renderTables = () => {
        renderUsersTable();
        renderCategoriesTable();
        renderProductsTable();
        renderOrdersTable();
    };

    const loadTables = async () => {
        const [users, categories, products, orders] = await Promise.all([
            api('/api/users?per_page=30'),
            api('/api/categories'),
            api('/api/products?per_page=30'),
            api('/api/orders?per_page=30'),
        ]);

        state.users = toList(users);
        state.categories = toList(categories);
        state.products = toList(products);
        state.orders = toList(orders);

        renderTables();
        setDefaultIds();

        return { users, categories, products, orders };
    };

    const runAction = async (handler, { reload = true } = {}) => {
        try {
            const result = await handler();
            if (result !== undefined) {
                print(result);
            }
            if (reload) {
                await loadTables();
            }
        } catch (error) {
            print(error);
        }
    };

    byId('reloadBtn').addEventListener('click', () => runAction(loadTables, { reload: false }));

    byId('createUserBtn').addEventListener('click', () => {
        openUserModal({
            mode: 'create',
            user: {
                name: `Демо пользователь ${Date.now()}`,
                email: `demo_${Date.now()}@example.com`,
                profile: { phone: `+7${randomInt(9000000000, 9999999999)}` },
            },
        });
    });

    byId('createCategoryBtn').addEventListener('click', () => {
        openCategoryModal({
            mode: 'create',
            category: { name: `Категория ${Date.now()}` },
        });
    });

    byId('createProductBtn').addEventListener('click', () => {
        openProductModal({
            mode: 'create',
            product: {
                name: `Товар ${Date.now()}`,
                price: randomInt(10, 200),
                category_id: state.selected.categoryId ?? 1,
            },
        });
    });

    byId('createOrderBtn').addEventListener('click', () => {
        openOrderModal({
            mode: 'create',
            order: {
                user_id: state.selected.userId ?? 1,
                status: 'new',
            },
        });
    });

    byId('addItemBtn').addEventListener('click', () => runAction(async () => {
        const { orderId, productId, quantity } = currentIds();
        if (!orderId || !productId) {
            return { message: 'Недостаточно данных: нужен заказ и товар.' };
        }

        return await api(`/api/orders/${orderId}/items`, {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, quantity }),
        });
    }));

    byId('removeItemBtn').addEventListener('click', () => runAction(async () => {
        const { orderId, productId } = currentIds();
        if (!orderId || !productId) {
            return { message: 'Недостаточно данных: нужен заказ и товар.' };
        }

        return await api(`/api/orders/${orderId}/items/${productId}`, {
            method: 'DELETE',
        });
    }));

    orderModalAddItemBtn.addEventListener('click', async () => {
        try {
            await openOrderItemModal();
        } catch (error) {
            print(error);
        }
    });

    orderItemsTableBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action="remove-item"][data-product-id]');
        if (!button) return;

        const orderId = state.orderModal.orderId;
        const productId = Number(button.dataset.productId);
        if (!orderId || !productId) return;

        if (!confirm(`Удалить товар #${productId} из заказа #${orderId}?`)) {
            return;
        }

        try {
            const result = await api(`/api/orders/${orderId}/items/${productId}`, {
                method: 'DELETE',
            });

            await refreshOrderInModal();
            await loadTables();
            print(result);
        } catch (error) {
            print(error);
        }
    });

    document.body.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-entity][data-action][data-id]');
        if (!button) return;

        const entity = button.dataset.entity;
        const action = button.dataset.action;
        const id = Number(button.dataset.id);

        const handlers = {
            user: {
                show: () => openUserEditor(id),
                profile: () => runAction(() => api(`/api/users/${id}/profile`), { reload: false }),
                edit: () => openUserEditor(id),
                order: () => runAction(async () => {
                    const created = await api('/api/orders', {
                        method: 'POST',
                        body: JSON.stringify({ user_id: id, status: 'new' }),
                    });
                    if (created?.data?.id) {
                        state.selected.orderId = created.data.id;
                    }
                    state.selected.userId = id;
                    return created;
                }),
                delete: () => runAction(async () => {
                    if (!confirm(`Удалить пользователя #${id}?`)) {
                        return { message: 'Удаление пользователя отменено.' };
                    }
                    await api(`/api/users/${id}`, { method: 'DELETE' });
                    return { message: `Пользователь #${id} удален.` };
                }),
            },
            category: {
                products: () => openCategoryProductsModal(id),
                edit: () => openCategoryEditor(id),
                delete: () => runAction(async () => {
                    if (!confirm(`Удалить категорию #${id}?`)) {
                        return { message: 'Удаление категории отменено.' };
                    }
                    await api(`/api/categories/${id}`, { method: 'DELETE' });
                    return { message: `Категория #${id} удалена.` };
                }),
            },
            product: {
                show: () => runAction(() => api(`/api/products/${id}`), { reload: false }),
                edit: () => openProductEditor(id),
                'add-item': () => runAction(async () => {
                    const fallbackOrderId = currentIds().orderId;
                    const selectedOrderId = Number(prompt('ID заказа', String(fallbackOrderId)) || fallbackOrderId);
                    const selectedQty = Number(prompt('Количество', String(currentIds().quantity)) || 1);

                    state.selected.orderId = selectedOrderId;
                    state.selected.productId = id;
                    state.selected.quantity = selectedQty;

                    return await api(`/api/orders/${selectedOrderId}/items`, {
                        method: 'POST',
                        body: JSON.stringify({ product_id: id, quantity: selectedQty }),
                    });
                }),
                delete: () => runAction(async () => {
                    if (!confirm(`Удалить товар #${id}?`)) {
                        return { message: 'Удаление товара отменено.' };
                    }
                    await api(`/api/products/${id}`, { method: 'DELETE' });
                    return { message: `Товар #${id} удален.` };
                }),
            },
            order: {
                show: () => openOrderEditor(id),
                status: () => runAction(async () => {
                    const order = await api(`/api/orders/${id}`);
                    const newStatus = prompt('Новый статус заказа', order?.data?.status || 'new');
                    if (!newStatus) return { message: 'Изменение статуса отменено.' };

                    return await api(`/api/orders/${id}`, {
                        method: 'PUT',
                        body: JSON.stringify({ status: newStatus }),
                    });
                }),
                'add-item': () => runAction(async () => {
                    const selectedProductId = Number(prompt('ID товара', String(currentIds().productId)) || currentIds().productId);
                    const selectedQty = Number(prompt('Количество', String(currentIds().quantity)) || 1);

                    state.selected.orderId = id;
                    state.selected.productId = selectedProductId;
                    state.selected.quantity = selectedQty;

                    return await api(`/api/orders/${id}/items`, {
                        method: 'POST',
                        body: JSON.stringify({ product_id: selectedProductId, quantity: selectedQty }),
                    });
                }),
                'remove-item': () => runAction(async () => {
                    const selectedProductId = Number(prompt('ID товара для удаления', String(currentIds().productId)) || currentIds().productId);

                    state.selected.orderId = id;
                    state.selected.productId = selectedProductId;

                    return await api(`/api/orders/${id}/items/${selectedProductId}`, {
                        method: 'DELETE',
                    });
                }),
                delete: () => runAction(async () => {
                    if (!confirm(`Удалить заказ #${id}?`)) {
                        return { message: 'Удаление заказа отменено.' };
                    }
                    await api(`/api/orders/${id}`, { method: 'DELETE' });
                    return { message: `Заказ #${id} удален.` };
                }),
            },
        };

        const entityHandler = handlers[entity]?.[action];
        if (entityHandler) {
            entityHandler();
        }
    });

    const submitUserModal = async () => {
        const name = userModalName.value.trim();
        const email = userModalEmail.value.trim();
        const phone = userModalPhone.value.trim();

        if (!name) {
            userModalError.textContent = 'Укажите имя пользователя.';
            return;
        }

        if (!email) {
            userModalError.textContent = 'Укажите email.';
            return;
        }

        await runAction(async () => {
            const payload = { name, email, phone };

            if (state.userModal.mode === 'create') {
                const created = await api('/api/users', {
                    method: 'POST',
                    body: JSON.stringify({
                        ...payload,
                        password: 'password123',
                    }),
                });
                if (created?.data?.id) {
                    state.selected.userId = created.data.id;
                }
                closeUserModal();
                return created;
            }

            const updated = await api(`/api/users/${state.userModal.userId}`, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });

            if (updated?.data?.id) {
                state.selected.userId = updated.data.id;
            }

            closeUserModal();
            return updated;
        });
    };

    const submitCategoryModal = async () => {
        const name = categoryModalName.value.trim();

        if (!name) {
            categoryModalError.textContent = 'Укажите наименование категории.';
            return;
        }

        await runAction(async () => {
            if (state.categoryModal.mode === 'create') {
                const created = await api('/api/categories', {
                    method: 'POST',
                    body: JSON.stringify({ name }),
                });

                if (created?.data?.id) {
                    state.selected.categoryId = created.data.id;
                }

                closeCategoryModal();
                return created;
            }

            const updated = await api(`/api/categories/${state.categoryModal.categoryId}`, {
                method: 'PUT',
                body: JSON.stringify({ name }),
            });

            if (updated?.data?.id) {
                state.selected.categoryId = updated.data.id;
            }

            closeCategoryModal();
            return updated;
        });
    };

    const submitProductModal = async () => {
        const name = productModalName.value.trim();
        const price = Number(String(productModalPrice.value).replace(',', '.'));
        const categoryId = Number(productModalCategoryId.value);

        if (!name) {
            productModalError.textContent = 'Укажите название товара.';
            return;
        }

        if (!Number.isFinite(price) || price <= 0) {
            productModalError.textContent = 'Укажите корректную цену больше 0.';
            return;
        }

        if (!Number.isInteger(categoryId) || categoryId <= 0) {
            productModalError.textContent = 'Укажите корректный ID категории.';
            return;
        }

        await runAction(async () => {
            const payload = {
                name,
                price,
                category_id: categoryId,
            };

            if (state.productModal.mode === 'create') {
                const created = await api('/api/products', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });

                if (created?.data?.id) {
                    state.selected.productId = created.data.id;
                    state.selected.categoryId = created.data.category_id ?? categoryId;
                }

                closeProductModal();
                return created;
            }

            const updated = await api(`/api/products/${state.productModal.productId}`, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });

            if (updated?.data?.id) {
                state.selected.productId = updated.data.id;
                state.selected.categoryId = updated.data.category_id ?? categoryId;
            }

            closeProductModal();
            return updated;
        });
    };

    const submitOrderModal = async () => {
        const userId = Number(orderModalUserId.value);
        const status = orderModalStatus.value.trim();

        if (state.orderModal.mode === 'create' && (!Number.isInteger(userId) || userId <= 0)) {
            orderModalError.textContent = 'Выберите пользователя.';
            return;
        }

        if (!status) {
            orderModalError.textContent = 'Укажите статус заказа.';
            return;
        }

        try {
            let result;

            if (state.orderModal.mode === 'create') {
                const created = await api('/api/orders', {
                    method: 'POST',
                    body: JSON.stringify({ user_id: userId, status }),
                });

                if (created?.data?.id) {
                    state.selected.orderId = created.data.id;
                    state.selected.userId = created.data.user_id ?? userId;
                }

                result = created;
            } else {
                const updated = await api(`/api/orders/${state.orderModal.orderId}`, {
                    method: 'PUT',
                    body: JSON.stringify({ status }),
                });

                if (updated?.data?.id) {
                    state.selected.orderId = updated.data.id;
                    state.selected.userId = updated.data.user_id ?? state.selected.userId;
                }

                result = updated;
            }

            closeOrderModal();
            await loadTables();
            print(result);
        } catch (error) {
            print(error);
        }
    };

    const submitOrderItemModal = async () => {
        const orderId = state.orderModal.orderId;
        const productId = Number(orderItemModalProductId.value);
        const quantity = Number(orderItemModalQuantity.value);

        if (!orderId) {
            orderItemModalError.textContent = 'Сначала сохраните заказ.';
            return;
        }

        if (!Number.isInteger(productId) || productId <= 0) {
            orderItemModalError.textContent = 'Выберите товар.';
            return;
        }

        if (!Number.isInteger(quantity) || quantity < 1) {
            orderItemModalError.textContent = 'Количество должно быть не меньше 1.';
            return;
        }

        try {
            const result = await api(`/api/orders/${orderId}/items`, {
                method: 'POST',
                body: JSON.stringify({ product_id: productId, quantity }),
            });

            state.selected.productId = productId;
            state.selected.quantity = quantity;
            closeOrderItemModal();
            await refreshOrderInModal();
            await loadTables();
            print(result);
        } catch (error) {
            print(error);
        }
    };

    userModalSaveBtn.addEventListener('click', submitUserModal);
    userModalCancelBtn.addEventListener('click', closeUserModal);
    userModalCloseBtn.addEventListener('click', closeUserModal);
    userModalBackdrop.addEventListener('click', (event) => {
        if (event.target === userModalBackdrop) {
            closeUserModal();
        }
    });
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && state.userModal.open) {
            closeUserModal();
        }
        if (event.key === 'Escape' && state.categoryModal.open) {
            closeCategoryModal();
        }
        if (event.key === 'Escape' && state.productModal.open) {
            closeProductModal();
        }
        if (event.key === 'Escape' && state.orderModal.open) {
            closeOrderModal();
        }
        if (event.key === 'Escape' && state.orderItemModal.open) {
            closeOrderItemModal();
        }
        if (event.key === 'Escape' && state.categoryProductsModal.open) {
            closeCategoryProductsModal();
        }
    });

    categoryModalSaveBtn.addEventListener('click', submitCategoryModal);
    categoryModalCancelBtn.addEventListener('click', closeCategoryModal);
    categoryModalCloseBtn.addEventListener('click', closeCategoryModal);
    categoryModalBackdrop.addEventListener('click', (event) => {
        if (event.target === categoryModalBackdrop) {
            closeCategoryModal();
        }
    });

    productModalSaveBtn.addEventListener('click', submitProductModal);
    productModalCancelBtn.addEventListener('click', closeProductModal);
    productModalCloseBtn.addEventListener('click', closeProductModal);
    productModalBackdrop.addEventListener('click', (event) => {
        if (event.target === productModalBackdrop) {
            closeProductModal();
        }
    });

    orderModalSaveBtn.addEventListener('click', submitOrderModal);
    orderModalCancelBtn.addEventListener('click', closeOrderModal);
    orderModalCloseBtn.addEventListener('click', closeOrderModal);
    orderModalBackdrop.addEventListener('click', (event) => {
        if (event.target === orderModalBackdrop) {
            closeOrderModal();
        }
    });

    orderItemModalSaveBtn.addEventListener('click', submitOrderItemModal);
    orderItemModalCancelBtn.addEventListener('click', closeOrderItemModal);
    orderItemModalCloseBtn.addEventListener('click', closeOrderItemModal);
    orderItemModalBackdrop.addEventListener('click', (event) => {
        if (event.target === orderItemModalBackdrop) {
            closeOrderItemModal();
        }
    });

    categoryProductsModalOkBtn.addEventListener('click', closeCategoryProductsModal);
    categoryProductsModalCloseBtn.addEventListener('click', closeCategoryProductsModal);
    categoryProductsModalBackdrop.addEventListener('click', (event) => {
        if (event.target === categoryProductsModalBackdrop) {
            closeCategoryProductsModal();
        }
    });

    (() => {
        const status = byId('socketStatus');
        const log = byId('eventLog');

        const appendEvent = (label, payload) => {
            const item = document.createElement('li');
            item.textContent = `[${new Date().toLocaleTimeString()}] ${label}: ${JSON.stringify(payload)}`;
            log.prepend(item);

            while (log.children.length > 50) {
                log.removeChild(log.lastChild);
            }
        };

        const key = @json($pusherKey);
        const cluster = @json($pusherCluster);
        const host = @json($pusherHost);
        const port = @json($pusherPort);
        const scheme = @json($pusherScheme);

        if (!key || typeof window.Echo !== 'function') {
            status.textContent = 'не подключено';
            status.className = 'fail';
            appendEvent('инфо', { причина: 'Pusher/Echo не настроен. Проверьте PUSHER_* в .env.' });
            return;
        }

        window.Pusher = Pusher;

        const options = {
            broadcaster: 'pusher',
            key,
            cluster,
            forceTLS: scheme === 'https',
        };

        if (host) {
            options.wsHost = host;
            options.wsPort = Number(port || 80);
            options.wssPort = Number(port || 443);
            options.enabledTransports = ['ws', 'wss'];
        }

        const echo = new window.Echo(options);

        echo.connector.pusher.connection.bind('connected', () => {
            status.textContent = 'подключено';
            status.className = 'ok';
        });

        echo.connector.pusher.connection.bind('error', (error) => {
            status.textContent = 'не подключено';
            status.className = 'fail';
            appendEvent('ошибка сокета', error);
        });

        const channel = echo.channel('public-dashboard');
        ['user.created', 'user.updated', 'product.created', 'product.updated', 'order.created', 'order.updated']
            .forEach((eventName) => {
                channel.listen(`.${eventName}`, (payload) => appendEvent(eventName, payload));
            });
    })();

    (async () => {
        await runAction(loadTables, { reload: false });
    })();
</script>
</body>
</html>
