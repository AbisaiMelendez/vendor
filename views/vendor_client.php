<?php

// Verificar si las variables de sesión están definidas dentro de $_SESSION['user']
$idVendor = isset($_SESSION['user']['userId']) ? $_SESSION['user']['userId'] : '';
$nameVendor = isset($_SESSION['user']['name_vendor']) ? $_SESSION['user']['name_vendor'] : '';

// Mostrar las variables
//echo $idVendor;
//echo $nameVendor;

// Imprimir toda la sesión para depuración (opcional)
// print_r($_SESSION);

//echo $idVendor;
//echo $nameVendor;

// print_r($_SESSION);
?>


<h2 class="text-xl font-bold mb-4 mt-12">Vendor Client</h2>

<!DOCTYPE html>
<html>

<head>
    <title>Surgepays</title>
</head>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        header {
            background-color: #292954;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        header .user-info {
            display: flex;
            align-items: center;
        }

        header .user-info span {
            margin-left: 10px;
        }

        main {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;

        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .inline-group {
            display: flex;
            gap: 10px;
        }

        .inline-group .form-group {
            flex: 1;
        }

        .payment-options {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }

        .payment-options label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button {

            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #003a9d;
        }

        @media (max-width: 768px) {
            main {
                padding: 10px;
            }
        }
    </style>
</head>

<?php include '../vendor/models/employees.php';
include '../vendor/models/idCompany.php';
include '../vendor/models/idJobs.php';
include '../vendor/models/creditTotal.php';
?>

<?php
// echo $dataEmployee;
$employeeJson = json_encode($dataEmployee);
//echo $employeeJson;

//idBill
//echo $dataBill;
//Position
//echo $dataPositions;

?>

<body class="bg-red-800 ">
    <header class="bg-indigo-900 text-white px-5 py-4 flex justify-between items-center rounded-lg shadow-lg">
        <div>
            <h1 class="text-xl font-bold">Surgepays</h1>
        </div>
        <div class="user-info">
            <a href="#">
                <i class="fas fa-user text-green-300"></i>
            </a>
            <span class="text-sm"><?php echo 'Bienvenido/a ' . $nameVendor; ?></span>
        </div>
    </header>

    <main class="mt-8">
        <form action="../vendor/models/save_client_agent.php" method="POST">
            <div class="form-group bg-gray-100 shadow-xs rounded-xl p-5 flex items-center justify-between w-auto  bg-cover bg-center">
                <!-- Imagen al lado izquierdo -->
                <img
                    src="https://static.vecteezy.com/system/resources/previews/008/442/086/non_2x/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg"
                    alt="Badge"
                    id="foto"
                    name="foto"
                    class="w-32 h-32 object-cover rounded-full border-4 border-green-300" />
                <!-- Texto al lado derecho -->
                <div class="flex">
                    <a href="#" class="text-gray-700 mt-2 pr-4 text-lg font-semibold">Total</a>
                    <label class="text-5xl font-semibold text-gray-700" id="total_credito" name="total_credito">$0.00</label>
                </div>
            </div>
            <h2>Buscar Badge</h2>
            <div class="form-group">
                <label for="badge">Badge</label>
                <input type="text" id="badge" name="badge" placeholder="Badge" required oninput="">
            </div>
            <div class="form-group">
                <label for="name">Nombre completo</label>
                <input type="text" id="name" name="name" placeholder="Nombre completo" required readonly>
            </div>
            <div class="form-group">
                <label for="job">Puesto Laboral</label>
                <input type="text" id="job" name="job" placeholder="Puesto laboral" readonly>
            </div>
            <div class="form-group">
                <label for="company">Compañía</label>
                <input type="text" id="company" name="company" placeholder="Glass Mountain" value="Surgepays" readonly>
            </div>


            <h2>Registrar la Venta</h2>

            <div class="inline-group">
                <div class="form-group">
                    <label for="quantity">Cantidad (Max. 5)</label>
                    <input type="number" id="quantity" name="quantity" max="5" placeholder="Ej: 3" required>
                </div>
                <div class="form-group">
                    <label for="price">Precio producto ($)</label>
                    <input type="number" id="price" name="price" step="0.01" placeholder="Ej: 50.00" required>
                </div>
                <div class="form-group">
                    <label for="total">Monto a cancelar ($)</label>
                    <input type="number" id="total" name="total" step="0.01" placeholder="Ej: 150.00" required readonly>
                </div>
                <input type="text" id="vendorCondicion" name="vendorCondicion" required readonly value='<?php echo $_SESSION['user']['level']; ?>' hidden>
            </div>

            <div class="form-group">
                <label for="product">Detalle del producto</label>
                <input type="text" id="product" name="product" placeholder="Detalle del producto" required>
            </div>
            <div class="payment-options">
                <?php $levelVendor = $_SESSION['user']['level'];

                if ($levelVendor == 3) {; ?>
                    <label>
                        <input type="radio" name="payment_option" value="2 cuotas $25" required>
                        2 cuotas a partir de $25
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="3 cuotas $45">
                        3 cuotas a partir de $45
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="4 cuotas $75">
                        4 cuotas a partir de $75
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="6 cuotas $100">
                        6 cuotas a partir de $100
                    </label>
                <?php
                } else {; ?>
                    <label>
                        <input type="radio" name="payment_option" value="1 cuota" required>
                        1 cuota
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="2 cuotas $25" required>
                        2 cuotas a partir de $25
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="3 cuotas $45">
                        3 cuotas a partir de $45
                    </label>
                    <label>
                        <input type="radio" name="payment_option" value="4 cuotas $75">
                        4 cuotas a partir de $75
                    </label>
                <?php }; ?>

            </div>


            <input type="text" name="idVendor" value="<?php echo $idVendor; ?>" hidden>
            <input type="text" name="nameVendor" value="<?php echo $nameVendor; ?>" hidden>
            <button class="bg-green-500 hover:bg-green-700 mt-8" type="submit">Registrar Venta</button>
        </form>
    </main>
</body>

</html>



<script>
    document.getElementById('badge').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            try {
                // Obteniendo los datos de empleados desde PHP
                const rawDataEmployee = <?php echo json_encode($dataEmployee, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                const rawDataPosition = <?php echo json_encode($dataPositions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                const rawDataCompany = <?php echo json_encode($dataBill, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                const rawDataCredit = <?php echo json_encode($dataCredit, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

                const url = 'https://hr-surgepays.com/app/public/documents/photo/';



                // Convertir el string JSON a un array de objetos
                const dataPositions = JSON.parse(rawDataPosition)

                // Convertir el string JSON a un array de objetos
                const dataEmployee = JSON.parse(rawDataEmployee);

                // Convertir el string JSON a un array de objetos
                const dataCompany = JSON.parse(rawDataCompany);

                // Convertir el string JSON a un array de objetos
                const dataCredit = JSON.parse(rawDataCredit);

                const badge = e.target.value.trim();
                const employee = dataEmployee.find(emp => emp.badge === badge && emp.status === 1);

                const quantityInput = document.getElementById('quantity');
                const priceInput = document.getElementById('price');
                const totalInput = document.getElementById('total');
                const paymentOptions = document.querySelectorAll('input[name="payment_option"]');



                if (employee) {
                    // Construyendo el nombre completo
                    const fullName = `${employee.firstName || ''} ${employee.secondName || ''} ${employee.firstLastName || ''} ${employee.secondLastName || ''}`.trim();

                    //reset los inputs
                    quantityInput.value = '';
                    priceInput.value = '';
                    totalInput.value = '';
                    paymentOptions.value = '';

                    // Validar y actualizar los campos del formulario
                    const nameField = document.getElementById('name');
                    if (nameField) nameField.value = fullName || 'No disponible';

                    // const jobField = document.getElementById('job');
                    // if (jobField) jobField.value = employee.positionId || 'No disponible';

                    const jobField = document.getElementById('job');
                    if (jobField) {
                        // Buscar el positionId en dataPositions y obtener el positionName
                        const position = dataPositions.find(pos => pos.positionId === employee.positionId);
                        jobField.value = position ? position.positionName : 'No disponible';
                    }

                    const companyField = document.getElementById('company');

                    if (companyField) {
                        // Busca el billToId en la lista correspondiente
                        const company = dataCompany.find(bill => bill.billToId === employee.billTo);
                        companyField.value = company ? company.billName : 'No disponible';
                    }

                    const photoField = document.getElementById('foto');
                    if (photoField) {
                        const photoUrl = `http://hr-surgepays.com/app/public/documents/photo/${employee.photo}`;
                        photoField.src = employee.photo ? photoUrl : 'https://static.vecteezy.com/system/resources/previews/008/442/086/non_2x/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg';
                    }


                    const emailField = document.getElementById('email');
                    if (emailField) emailField.value = employee.personalEmail || 'No disponible';


                    const totalCreditoField = document.getElementById('total_credito');

                    if (totalCreditoField && employee && employee.badge) {
                        // Buscar el badge en los datos de la tabla credits
                        const creditData = dataCredit.find(credit => credit.badge === employee.badge);

                        // Si se encuentra el badge, usar el current_credit; si no, asignar $100
                        const totalCredito = creditData ? creditData.current_credit : 100;

                        // Mostrar el crédito en el campo
                        totalCreditoField.innerHTML = `$${totalCredito}`;
                    }

                } else {

                    const photoField = document.getElementById('foto');
                    photoField.src = 'http://static.vecteezy.com/system/resources/previews/008/442/086/non_2x/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg';

                    const totalCreditoField = document.getElementById('total_credito');
                    if (totalCreditoField) {
                        totalCreditoField.innerHTML = "$0.00";
                    }

                    const form = e.target.closest('form'); // Encuentra el formulario que contiene el campo de entrada
                    if (form) form.reset();

                    // Mostrar alerta con SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'Usuario no encontrado',
                        text: 'El usuario no está activo o no existe.',
                        confirmButtonText: 'Entendido',
                        timer: 3000 // Opcional: La alerta se cierra automáticamente después de 3 segundos
                    });

                }
            } catch (error) {
                console.error('Error procesando dataEmployee:', error);
                if (form) form.reset();

                // Mostrar alerta con SweetAlert
                Swal.fire({
                    icon: 'warning',
                    title: 'Usuario no encontrado',
                    text: 'El usuario no está activo o no existe.',
                    confirmButtonText: 'Entendido',
                    timer: 3000 // Opcional: La alerta se cierra automáticamente después de 3 segundos
                });
            }
        }

    });



    //calculando el precio
    document.addEventListener('DOMContentLoaded', () => {
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');
        const totalInput = document.getElementById('total');
        const paymentOptions = document.querySelectorAll('input[name="payment_option"]');
        const totalCreditoField = document.getElementById('total_credito');
        const vendorIf = document.getElementById('vendorCondicion');




        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            const vendorCondicional = parseInt(vendorIf.value);
         
            // Obtener el crédito actual desde el campo total_credito
            const currentCredit = parseFloat(totalCreditoField ? totalCreditoField.textContent.replace('$', '').trim() : '0');

            if (vendorCondicional == 3) {
                if (total > 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Monto excedido',
                        text: 'El monto no puede ser mayor a $500.',
                    });
                    totalInput.value = '';
                    priceInput.value = '';
                    quantityInput.value = '';
                     
                } else if (total > currentCredit) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Crédito insuficiente',
                        text: `El monto excede su crédito disponible de $${currentCredit}.`,
                    });
                    totalInput.value = '';
                    priceInput.value = '';
                    quantityInput.value = '';
                } else {
                    totalInput.value = total.toFixed(2);
                    updatePaymentOptions(price);
                }

            } else {

                if (total > 100) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Monto excedido',
                        text: 'El monto no puede ser mayor a $100.',
                    });
                    totalInput.value = '';
                    priceInput.value = '';
                    quantityInput.value = '';
                } else if (total > currentCredit) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Crédito insuficiente',
                        text: `El monto excede su crédito disponible de $${currentCredit}.`,
                    });
                    totalInput.value = '';
                    priceInput.value = '';
                    quantityInput.value = '';
                } else {
                    totalInput.value = total.toFixed(2);
                    updatePaymentOptions(price);
                }
            }
        }

        // Validando las opciones de pago según el precio
        function updatePaymentOptions(price) {
            let highestEnabledOption = null;

            paymentOptions.forEach(option => {
                const value = option.value;
                if (value.includes('1 cuota')) {
                    option.disabled = price < 5;
                    if (!option.disabled) highestEnabledOption = option;
                } else if (value.includes('2 cuotas')) {
                    option.disabled = price < 25;
                    if (!option.disabled) highestEnabledOption = option;
                } else if (value.includes('3 cuotas')) {
                    option.disabled = price < 45;
                    if (!option.disabled) highestEnabledOption = option;
                } else if (value.includes('4 cuotas')) {
                    option.disabled = price < 75;
                    if (!option.disabled) highestEnabledOption = option;
                }
            });

            if (highestEnabledOption) {
                highestEnabledOption.checked = true;
            } else {
                paymentOptions.forEach(option => option.checked = false);
            }
        }
        //Alertas de guardado

        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('success');


        if (status === 'ok') {
            Swal.fire({
                icon: 'success',
                title: '¡Compra Realizada!',
                text: 'La transaccion ha sido creada exitosamente.',
            }).then(() => {
                window.location.href = '/vendor/index.php?page=vendor_client';
                // window.location.href = '/hr-surge.com/vendor/index.php?page=vendor_client';
            });
        }

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    });
</script>