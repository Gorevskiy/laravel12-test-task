<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API тестового задания Laravel 12",
 *     description="Публичный REST API с CRUD, связями, Swagger-документацией и событиями вещания"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8383",
 *     description="Локальный сервер"
 * )
 *
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+14155552671"),
 *     @OA\Property(property="address", type="string", nullable=true, example="ул. Ленина, 10"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Алиса Иванова"),
 *     @OA\Property(property="email", type="string", format="email", example="alice@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="profile", ref="#/components/schemas/Profile", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Электроника"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Беспроводная мышь"),
 *     @OA\Property(property="price", type="string", example="49.99"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="OrderItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Беспроводная мышь"),
 *     @OA\Property(property="price", type="string", example="49.99"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price_at_purchase", type="string", example="49.99"),
 *     @OA\Property(property="line_total", type="string", example="99.98")
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="new"),
 *     @OA\Property(property="total", type="string", example="129.98"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem"))
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Переданные данные некорректны."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="total", type="integer", example=3)
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     @OA\Property(property="first", type="string", nullable=true),
 *     @OA\Property(property="last", type="string", nullable=true),
 *     @OA\Property(property="prev", type="string", nullable=true),
 *     @OA\Property(property="next", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="UserStoreRequest",
 *     type="object",
 *     required={"name","email","password"},
 *     @OA\Property(property="name", type="string", example="Иван Петров"),
 *     @OA\Property(property="email", type="string", format="email", example="ivan@example.com"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+14155552671"),
 *     @OA\Property(property="address", type="string", nullable=true, example="Невский проспект, 15")
 * )
 *
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     type="object",
 *     required={"name","email"},
 *     @OA\Property(property="name", type="string", example="Иван Петров Обновлен"),
 *     @OA\Property(property="email", type="string", format="email", example="ivan-updated@example.com"),
 *     @OA\Property(property="password", type="string", nullable=true, example="new-password-123"),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="ProfileUpdateRequest",
 *     type="object",
 *     @OA\Property(property="phone", type="string", nullable=true, example="+14155550101"),
 *     @OA\Property(property="address", type="string", nullable=true, example="ул. Тверская, 7")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryStoreRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Сад")
 * )
 *
 * @OA\Schema(
 *     schema="ProductStoreRequest",
 *     type="object",
 *     required={"category_id","name","price"},
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Умная лампа"),
 *     @OA\Property(property="price", type="number", format="float", example=79.99)
 * )
 *
 * @OA\Schema(
 *     schema="OrderStoreRequest",
 *     type="object",
 *     required={"user_id"},
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="new")
 * )
 *
 * @OA\Schema(
 *     schema="OrderUpdateRequest",
 *     type="object",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", example="processing")
 * )
 *
 * @OA\Schema(
 *     schema="OrderItemStoreRequest",
 *     type="object",
 *     required={"product_id","quantity"},
 *     @OA\Property(property="product_id", type="integer", example=2),
 *     @OA\Property(property="quantity", type="integer", example=3)
 * )
 *
 * @OA\Get(
 *     path="/api/users",
 *     operationId="usersIndex",
 *     tags={"Пользователи"},
 *     summary="Список пользователей",
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), example=10),
 *     @OA\Response(
 *         response=200,
 *         description="Пользователи с пагинацией",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/users",
 *     operationId="usersStore",
 *     tags={"Пользователи"},
 *     summary="Создать пользователя с необязательным профилем",
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")),
 *     @OA\Response(response=201, description="Создано", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/api/users/{id}",
 *     operationId="usersShow",
 *     tags={"Пользователи"},
 *     summary="Получить пользователя",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Пользователь", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Put(
 *     path="/api/users/{id}",
 *     operationId="usersUpdate",
 *     tags={"Пользователи"},
 *     summary="Обновить пользователя",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")),
 *     @OA\Response(response=200, description="Пользователь", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     operationId="usersDestroy",
 *     tags={"Пользователи"},
 *     summary="Удалить пользователя",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Удалено"),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Get(
 *     path="/api/users/{id}/profile",
 *     operationId="profilesShow",
 *     tags={"Профили"},
 *     summary="Получить профиль пользователя",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Профиль", @OA\JsonContent(ref="#/components/schemas/Profile")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Put(
 *     path="/api/users/{id}/profile",
 *     operationId="profilesUpdate",
 *     tags={"Профили"},
 *     summary="Создать или обновить профиль",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProfileUpdateRequest")),
 *     @OA\Response(response=200, description="Профиль", @OA\JsonContent(ref="#/components/schemas/Profile")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/api/categories",
 *     operationId="categoriesIndex",
 *     tags={"Категории"},
 *     summary="Список категорий",
 *     @OA\Response(response=200, description="Категории", @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))))
 * )
 *
 * @OA\Post(
 *     path="/api/categories",
 *     operationId="categoriesStore",
 *     tags={"Категории"},
 *     summary="Создать категорию",
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CategoryStoreRequest")),
 *     @OA\Response(response=201, description="Создано", @OA\JsonContent(ref="#/components/schemas/Category")),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     operationId="categoriesShow",
 *     tags={"Категории"},
 *     summary="Получить категорию",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Категория", @OA\JsonContent(ref="#/components/schemas/Category")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Put(
 *     path="/api/categories/{id}",
 *     operationId="categoriesUpdate",
 *     tags={"Категории"},
 *     summary="Обновить категорию",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CategoryStoreRequest")),
 *     @OA\Response(response=200, description="Обновлено", @OA\JsonContent(ref="#/components/schemas/Category")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/api/categories/{id}",
 *     operationId="categoriesDestroy",
 *     tags={"Категории"},
 *     summary="Удалить категорию",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Удалено"),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Get(
 *     path="/api/products",
 *     operationId="productsIndex",
 *     tags={"Товары"},
 *     summary="Список товаров с фильтрацией",
 *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer"), example=1),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), example=10),
 *     @OA\Response(
 *         response=200,
 *         description="Товары с пагинацией",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/products",
 *     operationId="productsStore",
 *     tags={"Товары"},
 *     summary="Создать товар",
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductStoreRequest")),
 *     @OA\Response(response=201, description="Создано", @OA\JsonContent(ref="#/components/schemas/Product")),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/api/products/{id}",
 *     operationId="productsShow",
 *     tags={"Товары"},
 *     summary="Получить товар",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Товар", @OA\JsonContent(ref="#/components/schemas/Product")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Put(
 *     path="/api/products/{id}",
 *     operationId="productsUpdate",
 *     tags={"Товары"},
 *     summary="Обновить товар",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductStoreRequest")),
 *     @OA\Response(response=200, description="Обновлено", @OA\JsonContent(ref="#/components/schemas/Product")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     operationId="productsDestroy",
 *     tags={"Товары"},
 *     summary="Удалить товар",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Удалено"),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Get(
 *     path="/api/orders",
 *     operationId="ordersIndex",
 *     tags={"Заказы"},
 *     summary="Список заказов с фильтрацией",
 *     @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer"), example=1),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), example=10),
 *     @OA\Response(
 *         response=200,
 *         description="Заказы с пагинацией",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
 *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/orders",
 *     operationId="ordersStore",
 *     tags={"Заказы"},
 *     summary="Создать заказ",
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderStoreRequest")),
 *     @OA\Response(response=201, description="Создано", @OA\JsonContent(ref="#/components/schemas/Order")),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Get(
 *     path="/api/orders/{id}",
 *     operationId="ordersShow",
 *     tags={"Заказы"},
 *     summary="Получить заказ с позициями",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Заказ", @OA\JsonContent(ref="#/components/schemas/Order")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Put(
 *     path="/api/orders/{id}",
 *     operationId="ordersUpdate",
 *     tags={"Заказы"},
 *     summary="Обновить статус заказа",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderUpdateRequest")),
 *     @OA\Response(response=200, description="Обновлено", @OA\JsonContent(ref="#/components/schemas/Order")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/api/orders/{id}",
 *     operationId="ordersDestroy",
 *     tags={"Заказы"},
 *     summary="Удалить заказ",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Удалено"),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 *
 * @OA\Post(
 *     path="/api/orders/{id}/items",
 *     operationId="orderItemsStore",
 *     tags={"Позиции заказа"},
 *     summary="Добавить или обновить позицию в заказе",
 *     @OA\Parameter(name="id", in="path", required=true, description="ID заказа", @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderItemStoreRequest")),
 *     @OA\Response(response=200, description="Обновленный заказ", @OA\JsonContent(ref="#/components/schemas/Order")),
 *     @OA\Response(response=404, description="Не найдено"),
 *     @OA\Response(response=422, description="Ошибка валидации", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
 * )
 *
 * @OA\Delete(
 *     path="/api/orders/{id}/items/{product_id}",
 *     operationId="orderItemsDestroy",
 *     tags={"Позиции заказа"},
 *     summary="Удалить позицию из заказа",
 *     @OA\Parameter(name="id", in="path", required=true, description="ID заказа", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="product_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Обновленный заказ", @OA\JsonContent(ref="#/components/schemas/Order")),
 *     @OA\Response(response=404, description="Не найдено")
 * )
 */
class ApiDocumentation
{
}
