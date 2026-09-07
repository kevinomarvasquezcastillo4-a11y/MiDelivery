<?php
/*
===========================================================
 APP DE PEDIDOS - TODO EN UNO
 Archivo: index.php
 Requisitos: PHP 8+ / MySQL / XAMPP
 Base de datos: pedidos_app
===========================================================
*/

session_start();

/* =========================
   CONFIGURACIÓN MYSQL
========================= */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "comida";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {

}

$conn->set_charset("utf8mb4");

/* Crear base de datos */
$conn->query("CREATE DATABASE IF NOT EXISTS `pedido`
              CHARACTER SET utf8mb4
              COLLATE utf8mb4_unicode_ci");

$conn->select_db($db);

/* =========================
   CREAR TABLAS
========================= */

$conn->query("
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    imagen VARCHAR(500),
    disponible TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

$conn->query("
CREATE TABLE IF NOT EXISTS pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(150) NOT NULL,
    telefono VARCHAR(50),
    direccion TEXT NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    envio DECIMAL(10,2) NOT NULL,
    impuesto DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

$conn->query("
CREATE TABLE IF NOT EXISTS pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    producto VARCHAR(150) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB
");

/* =========================
   INSERTAR PRODUCTOS
========================= */

$contador = $conn->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc();

if ($contador["total"] == 0) {

    $productos = [

        /* COMIDA */
        ["Hamburguesa Clásica","Carne de res, queso, lechuga, tomate y salsa",95,"Comida","https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600"],
        ["Hamburguesa BBQ","Carne, queso cheddar, tocino y salsa BBQ",120,"Comida","https://images.unsplash.com/photo-1553979459-d2229ba7433a?w=600"],
        ["Pizza Pepperoni","Pizza artesanal con pepperoni y queso mozzarella",150,"Comida","https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600"],
        ["Pizza Hawaiana","Jamón, piña y mozzarella",145,"Comida","https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600"],
        ["Pollo Frito","Piezas de pollo crujiente con papas",130,"Comida","https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600"],
        ["Tacos de Carne","Tres tacos con carne, cebolla y cilantro",100,"Comida","https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=600"],
        ["Burrito Mexicano","Carne, frijoles, arroz, queso y salsa",115,"Comida","https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=600"],
        ["Papas Fritas","Papas crujientes con salsa especial",55,"Comida","https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600"],
        ["Hot Dog Especial","Salchicha, queso, cebolla y salsas",75,"Comida","https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=600"],
        ["Ensalada César","Lechuga, pollo, parmesano y aderezo César",90,"Comida","https://images.unsplash.com/photo-1546793665-c74683f339c1?w=600"],

        /* BEBIDAS */
        ["Coca-Cola","Refresco frío 500 ml",35,"Bebidas","https://images.unsplash.com/photo-1554866585-cd94860890b7?w=600"],
        ["Pepsi","Refresco frío 500 ml",35,"Bebidas","https://images.unsplash.com/photo-1629203851122-3726ecdf080e?w=600"],
        ["Limonada","Limonada natural con hielo",40,"Bebidas","https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9e?w=600"],
        ["Jugo de Naranja","Jugo natural de naranja",45,"Bebidas","https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=600"],
        ["Café Americano","Café caliente recién preparado",40,"Bebidas","https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600"],
        ["Frappé de Chocolate","Bebida fría de chocolate",65,"Bebidas","https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=600"],
        ["Batido de Fresa","Fresa, leche y hielo",60,"Bebidas","https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600"],

        /* POSTRES */
        ["Pastel de Chocolate","Pastel de chocolate con cobertura",75,"Postres","https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600"],
        ["Cheesecake","Cheesecake cremoso con frutos rojos",80,"Postres","https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=600"],
        ["Brownie","Brownie de chocolate con nueces",55,"Postres","https://images.unsplash.com/photo-1606313564200-e75d5e30476b?w=600"],
        ["Helado de Vainilla","Dos bolas de helado de vainilla",50,"Postres","https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=600"],
        ["Donas","Dos donas glaseadas",45,"Postres","https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600"],
        ["Flan","Flan casero con caramelo",50,"Postres","https://images.unsplash.com/photo-1614707267537-2b6f5a0c0f1b?w=600"]
    ];

    $stmt = $conn->prepare("
        INSERT INTO productos
        (nombre, descripcion, precio, categoria, imagen)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($productos as $p) {
        $stmt->bind_param(
            "ssdss",
            $p[0],
            $p[1],
            $p[2],
            $p[3],
            $p[4]
        );
        $stmt->execute();
    }

    $stmt->close();
}

/* =========================
   OBTENER PRODUCTOS
========================= */

$resultado = $conn->query("
    SELECT * FROM productos
    WHERE disponible = 1
    ORDER BY id DESC
");

$productosDB = [];

while ($row = $resultado->fetch_assoc()) {
    $productosDB[] = $row;
}

/* =========================
   GUARDAR PEDIDO
========================= */

$factura = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["accion"]) &&
    $_POST["accion"] === "guardar_pedido") {

    $cliente   = trim($_POST["cliente"] ?? "");
    $telefono  = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $pago      = trim($_POST["metodo_pago"] ?? "");
    $carrito   = json_decode($_POST["carrito"] ?? "[]", true);

    if ($cliente === "" || $direccion === "" || $pago === "" || empty($carrito)) {
        $error = "Completa los datos y agrega productos al carrito.";
    } else {

        $subtotal = 0;

        foreach ($carrito as $item) {
            $precio = floatval($item["precio"]);
            $cantidad = intval($item["cantidad"]);

            if ($cantidad > 0) {
                $subtotal += $precio * $cantidad;
            }
        }

        $envio = $subtotal >= 300 ? 0 : 35;
        $impuesto = $subtotal * 0.15;
        $total = $subtotal + $envio + $impuesto;

        $conn->begin_transaction();

        try {

            $stmt = $conn->prepare("
                INSERT INTO pedido
                
                (cliente, telefono, direccion, metodo_pago,
                 subtotal, envio, impuesto, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssssdddd",
                $cliente,
                $telefono,
                $direccion,
                $pago,
                $subtotal,
                $envio,
                $impuesto,
                $total
            );

            $stmt->execute();

            $pedidoID = $conn->insert_id;

            $stmtDetalle = $conn->prepare("
                INSERT INTO pedido_detalles
                (pedido_id, producto_id, producto, precio, cantidad, total)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($carrito as $item) {

                $productoID = intval($item["id"]);
                $nombre = $item["nombre"];
                $precio = floatval($item["precio"]);
                $cantidad = intval($item["cantidad"]);
                $totalProducto = $precio * $cantidad;

                if ($cantidad <= 0) {
                    continue;
                }

                $stmtDetalle->bind_param(
                    "iisdid",
                    $pedidoID,
                    $productoID,
                    $nombre,
                    $precio,
                    $cantidad,
                    $totalProducto
                );

                $stmtDetalle->execute();
            }

            $stmtDetalle->close();
            $stmt->close();

            $conn->commit();

            $factura = [
                "id" => $pedidoID,
                "cliente" => $cliente,
                "telefono" => $telefono,
                "direccion" => $direccion,
                "pago" => $pago,
                "subtotal" => $subtotal,
                "envio" => $envio,
                "impuesto" => $impuesto,
                "total" => $total
            ];

        } catch (Exception $e) {

            $conn->rollback();

            
        }
    }
}

/* =========================
   CATEGORÍAS
========================= */

$categorias = ["Todos", "Comida", "Bebidas", "Postres"];
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Mi Delivery</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f5f5;
    color: #222;
}

/* HEADER */

header {
    background: #ff1744;
    color: white;
    padding: 18px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 3px 12px rgba(0,0,0,.2);
}

.logo {
    font-size: 26px;
    font-weight: bold;
}

.logo span {
    color: #ffe082;
}

.cart-button {
    border: none;
    background: white;
    color: #ff1744;
    padding: 12px 18px;
    border-radius: 30px;
    font-weight: bold;
    cursor: pointer;
}

/* HERO */

.hero {
    background:
    linear-gradient(
        rgba(0,0,0,.55),
        rgba(0,0,0,.55)
    ),
    url("https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1600")
    center/cover;

    min-height: 280px;

    display: flex;
    align-items: center;
    justify-content: center;

    text-align: center;
    color: white;

    padding: 40px 20px;
}

.hero h1 {
    font-size: 42px;
    margin-bottom: 12px;
}

.hero p {
    font-size: 18px;
}

/* CONTENEDOR */

.container {
    width: 92%;
    max-width: 1250px;
    margin: 30px auto;
}

/* BUSCADOR */

.search {
    width: 100%;
    padding: 16px 20px;
    border: none;
    border-radius: 12px;
    background: white;
    box-shadow: 0 3px 12px rgba(0,0,0,.08);
    margin-bottom: 25px;
    font-size: 16px;
}

/* CATEGORIAS */

.categories {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 15px;
}

.category {
    border: none;
    background: white;
    padding: 12px 22px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: bold;
    white-space: nowrap;
}

.category.active {
    background: #ff1744;
    color: white;
}

/* PRODUCTOS */

.products {
    display: grid;
    grid-template-columns:
    repeat(auto-fill, minmax(230px, 1fr));

    gap: 22px;
}

.card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,.08);
    transition: .2s;
}

.card:hover {
    transform: translateY(-4px);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content {
    padding: 17px;
}

.card h3 {
    margin-bottom: 7px;
}

.description {
    color: #777;
    font-size: 14px;
    min-height: 40px;
}

.price {
    color: #ff1744;
    font-weight: bold;
    font-size: 20px;
    margin: 12px 0;
}

.add {
    width: 100%;
    padding: 12px;
    background: #ff1744;
    border: none;
    color: white;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
}

.add:hover {
    background: #d50032;
}

/* CARRITO */

.cart {
    position: fixed;
    right: -450px;
    top: 0;
    width: 420px;
    max-width: 95%;
    height: 100%;
    background: white;
    z-index: 1000;
    box-shadow: -5px 0 20px rgba(0,0,0,.2);
    padding: 25px;
    transition: .3s;
    overflow-y: auto;
}

.cart.open {
    right: 0;
}

.cart-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.close {
    background: #eee;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px solid #eee;
    padding: 13px 0;
}

.cart-item-info {
    flex: 1;
}

.qty {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}

.qty button {
    border: none;
    width: 27px;
    height: 27px;
    border-radius: 50%;
    background: #ff1744;
    color: white;
    cursor: pointer;
}

.remove {
    color: #ff1744;
    background: none;
    border: none;
    cursor: pointer;
}

/* TOTALES */

.totals {
    margin-top: 20px;
    border-top: 2px solid #eee;
    padding-top: 15px;
}

.total-line {
    display: flex;
    justify-content: space-between;
    margin: 8px 0;
}

.grand-total {
    font-size: 23px;
    font-weight: bold;
    color: #ff1744;
}

/* FORMULARIO */

.checkout {
    margin-top: 25px;
}

.checkout input,
.checkout textarea,
.checkout select {
    width: 100%;
    padding: 13px;
    margin: 7px 0;
    border: 1px solid #ddd;
    border-radius: 9px;
}

.checkout textarea {
    resize: vertical;
    min-height: 80px;
}

.checkout button {
    width: 100%;
    padding: 15px;
    margin-top: 10px;
    background: #19a974;
    border: none;
    color: white;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

/* FACTURA */

.invoice {
    width: 92%;
    max-width: 650px;
    margin: 30px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
}

.invoice h2 {
    color: #ff1744;
    margin-bottom: 20px;
}

.invoice-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.invoice-total {
    border-top: 2px solid #ddd;
    padding-top: 15px;
    font-size: 24px;
    font-weight: bold;
}

.print {
    background: #222;
    color: white;
    border: none;
    padding: 13px 20px;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 15px;
}

.error {
    background: #ffdddd;
    color: #a00000;
    padding: 15px;
    width: 92%;
    max-width: 650px;
    margin: 20px auto;
    border-radius: 10px;
}

/* FOOTER */

footer {
    margin-top: 60px;
    background: #222;
    color: white;
    text-align: center;
    padding: 30px;
}

/* RESPONSIVE */

@media(max-width:600px) {

    .hero h1 {
        font-size: 30px;
    }

    .products {
        grid-template-columns:
        repeat(2, minmax(0,1fr));

        gap: 12px;
    }

    .card img {
        height: 130px;
    }

    .card-content {
        padding: 12px;
    }

    .description {
        font-size: 12px;
    }

    .price {
        font-size: 17px;
    }

    .cart {
        width: 100%;
    }
}

/* IMPRESIÓN */

@media print {

    body * {
        visibility: hidden;
    }

    .invoice,
    .invoice * {
        visibility: visible;
    }

    .invoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
    }

    .print {
        display: none;
    }
}

</style>

</head>

<body>

<header>

    <div class="logo">
        🍔 Mi<span>Delivery</span>
    </div>

    <button class="cart-button"
            onclick="abrirCarrito()">
        🛒 Carrito
        (<span id="cartCount">0</span>)
    </button>

</header>

<section class="hero">

    <div>

        <h1>¿Qué quieres comer hoy?</h1>

        <p>
            Comida, bebidas y postres directamente hasta tu puerta.
        </p>

    </div>

</section>

<?php if ($error): ?>

<div class="error">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>


<?php if ($factura): ?>

<div class="invoice" id="factura">

    <h2>🧾 Factura #<?= $factura["id"] ?></h2>

    <p>
        <strong>Cliente:</strong>
        <?= htmlspecialchars($factura["cliente"]) ?>
    </p>

    <p>
        <strong>Teléfono:</strong>
        <?= htmlspecialchars($factura["telefono"]) ?>
    </p>

    <p>
        <strong>Dirección:</strong>
        <?= htmlspecialchars($factura["direccion"]) ?>
    </p>

    <p>
        <strong>Pago:</strong>
        <?= htmlspecialchars($factura["pago"]) ?>
    </p>

    <hr style="margin:20px 0">

    <div class="invoice-row">
        <span>Subtotal</span>
        <strong>L <?= number_format($factura["subtotal"],2) ?></strong>
    </div>

    <div class="invoice-row">
        <span>Envío</span>
        <strong>L <?= number_format($factura["envio"],2) ?></strong>
    </div>

    <div class="invoice-row">
        <span>Impuesto 15%</span>
        <strong>L <?= number_format($factura["impuesto"],2) ?></strong>
    </div>

    <div class="invoice-row invoice-total">
        <span>Total</span>
        <span>L <?= number_format($factura["total"],2) ?></span>
    </div>

    <button class="print" onclick="window.print()">
        🖨️ Imprimir factura
    </button>

</div>

<?php endif; ?>


<main class="container">

    <input
        type="text"
        class="search"
        id="search"
        placeholder="🔎 Buscar hamburguesa, pizza, bebida, postre..."
        onkeyup="buscarProductos()"
    >

    <div class="categories">

        <?php foreach ($categorias as $categoria): ?>

            <button
                class="category <?= $categoria === "Todos" ? "active" : "" ?>"
                onclick="filtrarCategoria('<?= $categoria ?>', this)"
            >
                <?= $categoria ?>
            </button>

        <?php endforeach; ?>

    </div>

    <div class="products" id="products">

        <?php foreach ($productosDB as $producto): ?>

            <article
                class="card"
                data-category="<?= htmlspecialchars($producto["categoria"]) ?>"
                data-name="<?= strtolower(htmlspecialchars($producto["nombre"])) ?>"
            >

                <img
                    src="<?= htmlspecialchars($producto["imagen"]) ?>"
                    alt="<?= htmlspecialchars($producto["nombre"]) ?>"
                    loading="lazy"
                >

                <div class="card-content">

                    <h3>
                        <?= htmlspecialchars($producto["nombre"]) ?>
                    </h3>

                    <p class="description">
                        <?= htmlspecialchars($producto["descripcion"]) ?>
                    </p>

                    <div class="price">
                        L <?= number_format($producto["precio"],2) ?>
                    </div>

                    <button
                        class="add"
                        onclick='agregar(
                            <?= json_encode($producto["id"]) ?>,
                            <?= json_encode($producto["nombre"]) ?>,
                            <?= json_encode($producto["precio"]) ?>
                        )'
                    >
                        + Agregar al carrito
                    </button>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

</main>


<!-- CARRITO -->

<aside class="cart" id="cart">

    <div class="cart-header">

        <h2>🛒 Tu pedido</h2>

        <button
            class="close"
            onclick="cerrarCarrito()">
            ✕
        </button>

    </div>

    <div id="cartItems"></div>

    <div class="totals">

        <div class="total-line">
            <span>Subtotal</span>
            <strong id="subtotal">L 0.00</strong>
        </div>

        <div class="total-line">
            <span>Envío</span>
            <strong id="envio">L 35.00</strong>
        </div>

        <div class="total-line">
            <span>Impuesto 15%</span>
            <strong id="impuesto">L 0.00</strong>
        </div>

        <div class="total-line grand-total">
            <span>Total</span>
            <strong id="total">L 0.00</strong>
        </div>

    </div>


    <form
        class="checkout"
        method="POST"
        onsubmit="return prepararPedido()"
    >

        <input
            type="hidden"
            name="accion"
            value="guardar_pedido"
        >

        <input
            type="hidden"
            name="carrito"
            id="carritoInput"
        >

        <h3>Datos de entrega</h3>

        <input
            type="text"
            name="cliente"
            placeholder="Nombre completo"
            required
        >

        <input
            type="tel"
            name="telefono"
            placeholder="Teléfono"
            required
        >

        <textarea
            name="direccion"
            placeholder="Dirección de entrega"
            required
        ></textarea>

        <select name="metodo_pago" required>

            <option value="">
                Selecciona método de pago
            </option>

            <option value="Efectivo">
                💵 Efectivo
            </option>

            <option value="Tarjeta">
                💳 Tarjeta
            </option>

            <option value="Transferencia">
                🏦 Transferencia
            </option>

        </select>

        <button type="submit">
            Confirmar pedido
        </button>

    </form>

</aside>


<footer>

    <p>
        © <?= date("Y") ?> MiDelivery — Tu comida favorita.
    </p>

</footer>


<script>

/* =========================
   CARRITO
========================= */

let carrito = [];

function agregar(id, nombre, precio) {

    let producto = carrito.find(
        item => item.id === id
    );

    if (producto) {

        producto.cantidad++;

    } else {

        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            cantidad: 1
        });

    }

    actualizarCarrito();

    abrirCarrito();
}


function aumentar(id) {

    let producto = carrito.find(
        item => item.id === id
    );

    if (producto) {
        producto.cantidad++;
    }

    actualizarCarrito();
}


function disminuir(id) {

    let producto = carrito.find(
        item => item.id === id
    );

    if (!producto) return;

    producto.cantidad--;

    if (producto.cantidad <= 0) {

        carrito = carrito.filter(
            item => item.id !== id
        );

    }

    actualizarCarrito();
}


function eliminar(id) {

    carrito = carrito.filter(
        item => item.id !== id
    );

    actualizarCarrito();
}


/* =========================
   ACTUALIZAR CARRITO
========================= */

function actualizarCarrito() {

    const contenedor =
        document.getElementById("cartItems");

    contenedor.innerHTML = "";

    let subtotal = 0;
    let cantidadTotal = 0;

    carrito.forEach(item => {

        subtotal +=
            item.precio * item.cantidad;

        cantidadTotal +=
            item.cantidad;

        const div =
            document.createElement("div");

        div.className = "cart-item";

        div.innerHTML = `

            <div class="cart-item-info">

                <strong>
                    ${escapeHTML(item.nombre)}
                </strong>

                <div>
                    L ${item.precio.toFixed(2)}
                </div>

                <div class="qty">

                    <button
                        onclick="disminuir(${item.id})">
                        -
                    </button>

                    <span>
                        ${item.cantidad}
                    </span>

                    <button
                        onclick="aumentar(${item.id})">
                        +
                    </button>

                </div>

            </div>

            <div>

                <strong>
                    L ${(item.precio * item.cantidad).toFixed(2)}
                </strong>

                <br>

                <button
                    class="remove"
                    onclick="eliminar(${item.id})">
                    Eliminar
                </button>

            </div>

        `;

        contenedor.appendChild(div);

    });


    let envio = subtotal >= 300 ? 0 : 35;

    if (subtotal === 0) {
        envio = 35;
    }

    let impuesto =
        subtotal * 0.15;

    let total =
        subtotal + envio + impuesto;


    document.getElementById("subtotal")
        .textContent =
        "L " + subtotal.toFixed(2);

    document.getElementById("envio")
        .textContent =
        "L " + envio.toFixed(2);

    document.getElementById("impuesto")
        .textContent =
        "L " + impuesto.toFixed(2);

    document.getElementById("total")
        .textContent =
        "L " + total.toFixed(2);

    document.getElementById("cartCount")
        .textContent =
        cantidadTotal;

}


function prepararPedido() {

    if (carrito.length === 0) {

        alert(
            "Agrega al menos un producto."
        );

        return false;
    }

    document.getElementById(
        "carritoInput"
    ).value =
        JSON.stringify(carrito);

    return true;
}


/* =========================
   CARRITO ABRIR/CERRAR
========================= */

function abrirCarrito() {

    document
        .getElementById("cart")
        .classList.add("open");

}

function cerrarCarrito() {

    document
        .getElementById("cart")
        .classList.remove("open");

}


/* =========================
   FILTROS
========================= */

function filtrarCategoria(
    categoria,
    boton
) {

    document
        .querySelectorAll(".category")
        .forEach(btn => {
            btn.classList.remove("active");
        });

    boton.classList.add("active");

    document
        .querySelectorAll(".card")
        .forEach(card => {

            if (
                categoria === "Todos" ||
                card.dataset.category === categoria
            ) {

                card.style.display = "";

            } else {

                card.style.display = "none";

            }

        });

}


function buscarProductos() {

    const texto =
        document
        .getElementById("search")
        .value
        .toLowerCase();

    document
        .querySelectorAll(".card")
        .forEach(card => {

            const nombre =
                card.dataset.name;

            const descripcion =
                card
                .querySelector(".description")
                .textContent
                .toLowerCase();

            if (
                nombre.includes(texto) ||
                descripcion.includes(texto)
            ) {

                card.style.display = "";

            } else {

                card.style.display = "none";

            }

        });

}


/* =========================
   SEGURIDAD HTML
========================= */

function escapeHTML(text) {

    const div =
        document.createElement("div");

    div.textContent = text;

    return div.innerHTML;
}


/* =========================
   INICIO
========================= */

actualizarCarrito();

</script>

</body>
</html>
