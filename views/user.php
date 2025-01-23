<h2 class="text-xl font-bold mb-4">Users</h2>



<?php
include '../vendor/models/conex.php';
include '../vendor/models/employees.php';

// Decodifica los datos JSON y convierte a array
$dataDecode = json_decode($data, true);
$dataEmployeess = json_decode($dataEmployee, true);
// print $data;

// Filtrado de datos
$filteredData = $dataDecode;


// Filtrar por estado
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $searchStatus = (int)$_GET['status']; // Convierte a entero para comparación estricta
    $filteredData = array_filter($filteredData, function ($user) use ($searchStatus) {
        return (int)$user['status'] === $searchStatus; // Comparación estricta para evitar inconsistencias
    });
}

// Filtrar por nombre
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $searchName = strtolower($_GET['name']);
    $filteredData = array_filter($filteredData, function ($user) use ($searchName) {
        return strpos(strtolower($user['username']), $searchName) !== false ||
            strpos(strtolower($user['firstName']), $searchName) !== false ||
            strpos(strtolower($user['firstLastName']), $searchName) !== false ||
            strpos(strtolower($user['fullname']), $searchName) !== false ||
            strpos(strtolower($user['badge']), $searchName) !== false;
    });
}

// Convertir los datos filtrados a un arreglo indexado
$filteredData = array_values($filteredData);

// Garantizar que siempre sea un arreglo, incluso si está vacío
if (empty($filteredData)) {
    $filteredData = [];
}

// Convertir a JSON
$jsonData = json_encode($filteredData);

// Imprime el resultado para depuración
// print_r($filteredData);



// Configuración de paginación
$itemsPerPage = 10;
$totalItems = count($filteredData);
$totalPages = ceil($totalItems / $itemsPerPage);

// Determina la página actual usando `items`
$page = isset($_GET['items']) ? (int)$_GET['items'] : 1;
$page = max(1, min($totalPages, $page));

// Calcular el índice de inicio y los datos que se mostrarán en esta página
$startIndex = ($page - 1) * $itemsPerPage;
$pageData = array_slice($filteredData, $startIndex, $itemsPerPage);

// Función para construir la URL con los parámetros actuales
function buildUrlWithParams($params = [])
{
    // Agregar siempre `page=user` como parámetro inicial
    $baseParams = ['page' => 'user'];

    // Extraer los parámetros actuales de la URL si existen
    $queryParams = $_GET;

    // Fusionar los parámetros actuales con los nuevos, incluyendo `page=user`
    $queryParams = array_merge($baseParams, $queryParams, $params);

    // Reconstruir la query string
    $newQueryString = http_build_query($queryParams);

    // Reconstruir la URL completa
    $newUrl = $_SERVER['PHP_SELF'] . '?' . $newQueryString;

    return $newUrl;
}

// Genera la URL con los parámetros actuales
$currentUrl = buildUrlWithParams();
?>

<script>
    // Almacena la URL en el localStorage
    localStorage.setItem('path', '<?php echo $currentUrl; ?>');
    const dataEmployeesss = <?php echo json_encode($dataEmployee); ?>
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<body class="bg-gray-100  justify-center min-h-screen ">
    <div class=" max-w-7xl p-4 bg-white ">

        <!-- Formulario -->
        <form action="<?php echo buildUrlWithParams(); ?>" method="GET" class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-4 md:space-y-2 mb-4 pl-4">


            <div class="md:w-1/3 w-full">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ingrese el nombre"
                    value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>"
                    oninput="searchWithStoredPath()"
                    onkeypress="if(event.key === 'Enter') { event.preventDefault(); searchWithStoredPath(); }">
            </div>


            <!-- Campo Estado -->
            <div class="md:w-1/4 w-full">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="searchWithStoredPath()">
                    <option value="">Select status</option>
                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>


            <!-- Botones en línea -->
            <div class="md:w-1/6 w-full flex space-x-4">
                <!-- <button type="submit" class="w-full md:w-auto py-2 px-4 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Search
                </button> -->

                <button type="button" onclick="searchWithStoredPath()" class="w-full md:w-auto py-2 px-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 ml-4">
                    search
                </button>
                <button type="button" onclick="openPopupForm()" class="w-full md:w-auto py-2 px-4 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 ml-4">
                    +
                </button>
                <button
                    type="reset"
                    onclick="ReloadPage()"
                    class="w-full md:w-auto py-2 px-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 ml-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 inline-block">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>

                </button>

                <button type="button" onclick="downloadExcel()" class="w-2 md:w-auto py-2 px-2 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>

                </button>
            </div>
        </form>



        <!-- Modal -->
        <div id="popupModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <!-- Header del Modal -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Create New User</h2>
                    <button onclick="closePopupForm()" class="text-white bg-red-500 hover:bg-red-600 rounded-full w-8 h-8 flex items-center justify-center">
                        &times;
                    </button>
                </div>

                <!-- Formulario -->
                <form action="../vendor/models/submit-form-endpoint.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block font-semibold mt-4">Select User</label>
                        <div name="" id="" class="mt-4">
                            <label class="mr-8 ">
                                <input type="radio" name="userLevel" id="userLevel" value="1" onchange="toggleBadgeInput()" required> Admin
                            </label>
                            <label>
                                <input type="radio" name="userLevel" id="userLevel" value="2" onchange="toggleBadgeInput()" required> Vendor
                            </label>
                            <label>
                                <input type="radio" class="ml-8" name="userLevel" id="userLevel" value="3" onchange="toggleBadgeInput()" required> Optica
                            </label>
                        </div>
                    </div>

                    <div id="badgeContainer" style="display: none;">
                        <label for="badge" class="font-semibold">Badge</label>
                        <input type="number" id="badge" name="badge" class="w-full p-2 border rounded "
                            oninput="searchBadge(this.value)"
                            onkeypress="if(event.key === 'Enter') { event.searchBadge(); searchBadge(); }">

                    </div>


                    <div>
                        <label for="username" class="block font-semibold">Email</label>
                        <input type="email" id="username" name="username" class="w-full p-2 border rounded" required>
                    </div>


                    <div>
                        <label for="fullname" class="block font-semibold">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="w-full p-2 border rounded" required>
                    </div>

                    <div id="vendorContainer" style="display: none;">
                        <label for="vendor_name" class="font-semibold">Vendor Name</label>
                        <input type="text" id="vendor_name" name="vendor_name" class="w-full p-2 border rounded">
                    </div>
                    <div id="vendorDui" style="display: none;">
                        <label for="vendor_dui" class="font-semibold">DUI:</label>
                        <input type="text" id="vendor_dui" name="vendor_dui" class="w-full p-2 border rounded" oninput="formatDUI()">
                    </div>

                    <div id="vendorNit" style="display: none;">
                        <label for="vendor_nit" class="font-semibold">NIT:</label>
                        <input type="text" id="vendor_nit" name="vendor_nit" class="w-full p-2 border rounded" oninput="formatNIT()">
                    </div>
                    <div id="tipoBanco" style="display: none;">
                        <label for="vendor_name" class="font-semibold">Type Bank</label>
                        <select type="text" id="typeBank" name="typeBank" class="w-full p-2 border rounded">

                            <option value="">Selected</option>

                            <option value="Banco Agricola">Banco Agricola</option>
                            <option value="Banco Promerica">Banco Promerica</option>
                            <option value="Banco Cuscatlan">Banco Cuscatlan</option>
                            <option value="Banco de América Central">Banco de América Central</option>
                            <option value="Scotiabank El Salvador">Scotiabank El Salvador</option>
                            <option value="Banco Davivienda El Salvador">Banco Davivienda El Salvador</option>
                            <option value="Banco Azul">Banco Azul</option>
                            <option value="Banco G&T Continental">Banco G&T Continental</option>
                            <option value="Banco Hipotecario">Banco Hipotecario</option>
                            <option value="Banco Industrial de El Salvador">Banco Industrial de El Salvador</option>
                            <option value="Banco Ficohsa">Banco Ficohsa</option>
                            <option value="Banco de Desarrollo de El Salvador">Banco de Desarrollo de El Salvador (BANDESAL)</option>
                            <option value="Banco Caja de Crédito">Banco Caja de Crédito</option>
                            <option value="Banco Interbank">Banco Interbank El Salvador</option>
                            <option value="Banco Sabadell">Banco Sabadell</option>
                            <option value="Banco La Hipotecaria">Banco La Hipotecaria</option>
                            <option value="Banco Inmobiliario">Banco Inmobiliario</option>
                            <option value="Banco Agrícola de El Salvador">Banco Agrícola de El Salvador</option>
                            <option value="Banco de San Salvador">Banco de San Salvador</option>
                            <option value="Banco Santa Tecla">Banco Santa Tecla</option>

                            <option value="FEDECACES">FEDECACES (Federación de Cajas de Crédito de El Salvador)</option>
                            <option value="Caja de Crédito de La Unión">Caja de Crédito de La Unión</option>
                            <option value="Caja de Crédito de San Vicente">Caja de Crédito de San Vicente</option>
                            <option value="Caja de Crédito de Sonsonate">Caja de Crédito de Sonsonate</option>
                            <option value="Caja de Crédito de Ahuachapan">Caja de Crédito de Ahuachapán</option>
                            <option value="Caja de Crédito de Chalatenango">Caja de Crédito de Chalatenango</option>
                            <option value="Caja de Crédito de Santa Ana">Caja de Crédito de Santa Ana</option>
                            <option value="Caja de Crédito de San Miguel">Caja de Crédito de San Miguel</option>
                            <option value="Caja de Crédito de La Paz">Caja de Crédito de La Paz</option>
                            <option value="Caja de Crédito de Usulután">Caja de Crédito de Usulután</option>
                            <option value="Caja de Crédito de Morazan">Caja de Crédito de Morazán</option>
                            <option value="Caja de Crédito de Cuscatlán">Caja de Crédito de Cuscatlán</option>
                            <option value="Caja de Crédito de La Libertad">Caja de Crédito de La Libertad</option>
                            <option value="Caja de Crédito de Comasagua">Caja de Crédito de Comasagua</option>
                            <option value="Caja de Crédito El Congo">Caja de Crédito El Congo</option>
                            <option value="Caja de Crédito El Paraíso">Caja de Crédito El Paraíso</option>
                            <option value="Caja de Crédito de Ciudad Arce">Caja de Crédito de Ciudad Arce</option>
                            <option value="Caja de Crédito de Zacatecoluca">Caja de Crédito de Zacatecoluca</option>
                            <option value="Caja de Crédito El Rosario">Caja de Crédito El Rosario</option>
                            <option value="Caja de Crédito de San Juan Opico">Caja de Crédito de San Juan Opico</option>

                            <option value="Cooperativa COCAFE">Cooperativa COCAFE</option>
                            <option value="Cooperativa de Ahorro y Crédito San Antonio">Cooperativa de Ahorro y Crédito San Antonio</option>
                            <option value="Cooperativa de Ahorro y Crédito La Paz">Cooperativa de Ahorro y Crédito La Paz</option>
                            <option value="Cooperativa de Ahorro y Crédito San Isidro">Cooperativa de Ahorro y Crédito San Isidro</option>
                            <option value="Cooperativa de Ahorro y Crédito El Roble">Cooperativa de Ahorro y Crédito El Roble</option>
                            <option value="Cooperativa de Ahorro y Crédito CrediFamilia">Cooperativa de Ahorro y Crédito CrediFamilia</option>
                            <option value="Cooperativa de Ahorro y Crédito de La Libertad">Cooperativa de Ahorro y Crédito de La Libertad</option>
                            <option value="Cooperativa de Ahorro y Crédito de Ahuachapan">Cooperativa de Ahorro y Crédito de Ahuachapán</option>
                            <option value="Cooperativa de Ahorro y Crédito Santa Teresa">Cooperativa de Ahorro y Crédito Santa Teresa</option>
                            <option value="Cooperativa de Ahorro y Crédito El Triunfo">Cooperativa de Ahorro y Crédito El Triunfo</option>
                            <option value="Cooperativa de Ahorro y Crédito Coopeuch">Cooperativa de Ahorro y Crédito Coopeuch</option>
                            <option value="Cooperativa de Ahorro y Crédito Accaciba">Cooperativa de Ahorro y Crédito Acacciba</option>
                            <option value="Cooperativa de Ahorro y Crédito La Providencia">Cooperativa de Ahorro y Crédito La Providencia</option>
                            <option value="Cooperativa de Ahorro y Crédito Sagrada Familia">Cooperativa de Ahorro y Crédito Sagrada Familia</option>
                            <option value="Cooperativa de Ahorro y Crédito Solidaria">Cooperativa de Ahorro y Crédito Solidaria</option>
                            <option value="Cooperativa de Ahorro y Crédito La Ceiba">Cooperativa de Ahorro y Crédito La Ceiba</option>
                            <option value="Cooperativa de Ahorro y Crédito El Buen Samaritano">Cooperativa de Ahorro y Crédito El Buen Samaritano</option>
                            <option value="Cooperativa de Ahorro y Crédito San José">Cooperativa de Ahorro y Crédito San José</option>
                            <option value="Cooperativa de Ahorro y Crédito El Salvador">Cooperativa de Ahorro y Crédito El Salvador</option>


                        </select>
                    </div>
                    <div id="tipoCuenta" style="display: none;">
                        <label for="vendor_name" class="font-semibold">Type Account</label>
                        <select type="text" id="typeCuenta" name="typeCuenta" class="w-full p-2 border rounded">

                            <option value="">Selected</option>
                            <option value="Cuenta de ahorro">Cuenta de ahorro</option>
                            <option value="Cuenta corriente">Cuenta corriente</option>
                        </select>
                    </div>
                    <div id="numberContainer" style="display: none;">
                        <label for="number_account" class="font-semibold">Number Account</label>
                        <input type="text" id="number_account" name="number_account" class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label for="password" class="block font-semibold">Password</label>
                        <input type="password" id="password" name="password" class="w-full p-2 border rounded" required>
                    </div>

                    <div id="commentsContainer" style="display: none;">
                        <label for="comments" class="font-semibold">Comments</label>
                        <textarea id="comments" name="comments" rows="4" class="w-full p-2 border rounded"></textarea>

                    </div>
                    <!-- <div>
                        <label for="firstName" class="block font-semibold">Last Name</label>
                    </div> -->

                    <input type="text" id="firstname" name="firstname" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="firstlastname" name="firstlastname" class="w-full p-2 border rounded" hidden>

                    <div>
                        <label for="status" class="block font-semibold">Status</label>
                        <select id="status" name="status" class="w-full p-2 border rounded" required>
                            <option value="">Selected</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- <div>
                        <label for="createdAt" class="block font-semibold">Created At</label>
                        <input type="datetime-local" id="createdAt" name="createdAt" class="w-full p-2 border rounded">
                    </div> -->

                    <!-- Botón de envío -->
                    <div class="text-right">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form edit data -->
        <div id="popupModalEdit" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <!-- Header del Modal -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Edit User</h2>
                    <button onclick="closePopupFormEdit()" class="text-white bg-red-500 hover:bg-red-600 rounded-full w-8 h-8 flex items-center justify-center">
                        &times;
                    </button>
                </div>

                <!-- Formulario -->
                <form action="../vendor/models/submit-form-editendpoint.php" method="POST" class="space-y-4">

                    <div class="mb-4">
                        <label for="userIdField" class="block font-semibold mb-2">User details</label>
                        <div class="flex space-x-2">
                            <div class="flex-1">
                                <label for="userIdField" class="block font-semibold mb-2">ID</label>
                                <input
                                    type="number"
                                    id="userIdField"
                                    name="userIdField"
                                    class="w-full p-2 border rounded"
                                    readonly>
                            </div>
                            <div class="flex-1">
                                <label for="userIdField" class="block font-semibold mb-2">Badge</label>
                                <input
                                    type="number"
                                    id="badgeField"
                                    name="badgeField"
                                    class="w-full p-2 border rounded"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block font-semibold">Type User</label>
                        <input type="text" id="userLevelField" name="userLevelField" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div>
                        <label for="fullname" class="block font-semibold">Email</label>
                        <input type="text" id="usernameField" name="usernameField" class="w-full p-2 border rounded" readonly>
                    </div>

                    <div>
                        <label for="fullname" class="block font-semibold">Full Name</label>
                        <input type="text" id="nameField" name="nameField" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div>
                        <label for="fullname" class="block font-semibold">DUI</label>
                        <input type="text" id="vendorDUI" name="vendorDUI" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div>
                        <label for="fullname" class="block font-semibold">NIT</label>
                        <input type="text" id="vendorNIT" name="vendorNIT" class="w-full p-2 border rounded" readonly>
                    </div>


                    <div id="vendorContainer">
                        <label for="" class="font-semibold">Vendor Name</label>
                        <input type="text" id="vendorNAME" name="vendorNAME" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div>
                        <label for="password" class="block font-semibold">Password</label>
                        <input type="password" id="password" name="password" class="w-full p-2 border rounded">
                    </div>
                    <div id="numberContainer" style="">
                        <label for="numberAccountField" class="font-semibold">Bank</label>
                        <input type="text" id="vendorBANK" name="vendorBANK" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div id="numberContainer" style="">
                        <label for="numberAccountField" class="font-semibold">Type Account</label>
                        <input type="text" id="vendorAccountBank" name="vendorAccountBank" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div id="numberContainer" style="">
                        <label for="numberAccountField" class="font-semibold">Number Account</label>
                        <input type="text" id="numberAccountField" name="numberAccountField" class="w-full p-2 border rounded" readonly>
                    </div>
                    <div id="commentsContainer" style="">
                        <label for="comments" class="font-semibold">Comments</label>
                        <textarea id="commentsField" name="commentsField" rows="4" class="w-full p-2 border rounded"></textarea>

                    </div>
                    <!-- <div>
                        <label for="firstName" class="block font-semibold">Last Name</label>
                    </div> -->

                    <div>
                        <label for="status" class="block font-semibold">Status</label>
                        <select id="statusField" name="statusField" class="w-full p-2 border rounded">
                            <option value="">Selected</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <input type="text" id="firstname" name="firstname" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="vendorNameOld" name="vendorNameOld" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="passwordOld" name="passwordOld" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="statusOld" name="statusOld" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="commentsOld" name="commentsOld" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="badgeField" name="badgeField" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="dataUser" name="dataUser" class="w-full p-2 border rounded" hidden>
                    <input type="text" id="numberAccountFieldOLD" name="numberAccountFieldOLD" class="w-full p-2 border rounded" hidden>

                    <!-- <div>
                        <label for="createdAt" class="block font-semibold">Created At</label>
                        <input type="datetime-local" id="createdAt" name="createdAt" class="w-full p-2 border rounded">
                    </div> -->

                    <!-- Botón de envío -->
                    <div class="text-right">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!--Formulario de editar el status del user -->

        <div id="popupModalStatus" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <!-- Header del Modal -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Edit Status</h2>
                    <button onclick="closePopupFormStatus()" class="text-white bg-red-500 hover:bg-red-600 rounded-full w-8 h-8 flex items-center justify-center">
                        &times;
                    </button>
                </div>

                <!-- Formulario -->
                <form action="../vendor/models/submit-form-endpoint-status.php" method="POST" class="space-y-4">

                    <div class="mb-4">
                        <!-- <label for="status-id" class="block font-semibold mb-2">User details</label> -->
                        <div class="flex space-x-2">
                            <div class="flex-1">
                                <!-- <label for="status-id" class="block font-semibold mb-2">ID</label> -->
                                <input
                                    type="number"
                                    id="status-id"
                                    name="status-id"
                                    class="w-full p-2 border rounded"
                                    readonly
                                    hidden>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="status" class="block font-semibold">Status</label>
                        <select id="statusField" name="statusField" class="w-full p-2 border rounded">
                            <option value="">Selected</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <input type="text" id="statusOld" name="statusOld" class="w-full p-2 border rounded" hidden>

                    <div class="text-right">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>



    </div>

    <table class="min-w-full divide-y divide-gray-200 overflow-x-auto bg-white mb-4">
        <thead class="bg-gray-50">
            <tr class="hover:bg-gray-100">
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Dui</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Nit</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type Account</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number Account</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Badge</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type User</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($pageData as $user): ?>
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-medium text-gray-900"><?php echo ($user['userId']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['fullname']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['name_vendor']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-purple-100 text-green-800"><?php echo htmlspecialchars($user['dui']); ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-blue-100 text-green-800"><?php echo htmlspecialchars($user['nit']); ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-green-100 text-green-800"><?php echo htmlspecialchars($user['typeBank']); ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['typeAccount']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-purple-100 text-green-800"><?php echo htmlspecialchars($user['number_account']); ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-purple-100 text-green-800"> <?php echo $user['badge'] ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-medium leading-5 font-semibold rounded-full bg-purple-100 text-green-800">
                            <?php
                            if ($user['userLevel'] == 1) {
                                echo 'Admin';
                            } elseif ($user['userLevel'] == 2) {
                                echo 'Vendor';
                            } elseif ($user['userLevel'] == 3) {
                                echo 'Vendor - Optica';
                            }
                            ?>
                        </span>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($user['status'] == 1): ?>
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-medium text-gray-500"><?php echo htmlspecialchars($user['createdAt']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-medium text-gray-500">
                        <div class="flex justify-center space-x-4">
                            <!-- Botón para editar -->
                            <button
                                class="flex items-center justify-center w-12 h-12 bg-blue-500 text-white rounded hover:bg-blue-600"
                                onclick="openPopupFormEdit(this)"
                                data-user-id="<?php echo $user['userId']; ?>"
                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                data-name="<?php echo htmlspecialchars($user['fullname']); ?>"
                                data-badge="<?php echo htmlspecialchars($user['badge']); ?>"
                                data-type-user="<?php echo ($user['userLevel']); ?>"
                                data-vendor-name="<?php echo htmlspecialchars($user['name_vendor']); ?>"
                                data-password="<?php echo ($user['password']); ?>"
                                data-comments="<?php echo ($user['comments']); ?>"
                                data-status="<?php echo $user['status']; ?>"
                                data-numberAccount="<?php echo $user['number_account']; ?>"
                                data-dui="<?php echo $user['dui']; ?>"
                                data-nit="<?php echo $user['nit']; ?>"
                                data-typeBank="<?php echo $user['typeBank']; ?>"
                                data-typeAccount="<?php echo $user['typeAccount']; ?>"
                                data-user="<?php echo htmlspecialchars(json_encode($user, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>

                            <!-- Botón para estado -->
                            <button
                                class="flex items-center justify-center w-12 h-12 bg-red-400 text-white rounded hover:bg-red-600"
                                onclick="openPopupFormStatus(this)"
                                data-id-status="<?php echo $user['userId']; ?>"
                                data-active-status="<?php echo $user['status']; ?>"
                                data-numberAccount="<?php echo $user['number_account']; ?>"
                                data-user-status="<?php echo htmlspecialchars(json_encode($user, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if ($user['status'] == "1") : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                        <path d="M17 24a1 1 0 0 1 -1-1 7 7 0 0 0 -14 0 1 1 0 0 1 -2 0 9 9 0 0 1 18 0 1 1 0 0 1 -1 1zm6-11h-6a1 1 0 0 1 0-2h6a1 1 0 0 1 0 2zm-14-1a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6zm0-10a4 4 0 1 0 4 4 4 4 0 0 0 -4-4z" />
                                    </svg>
                                <?php else : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path d="M23,11H21V9a1,1,0,0,0-2,0v2H17a1,1,0,0,0,0,2h2v2a1,1,0,0,0,2,0V13h2a1,1,0,0,0,0-2Z" />
                                        <path d="M9,12A6,6,0,1,0,3,6,6.006,6.006,0,0,0,9,12ZM9,2A4,4,0,1,1,5,6,4,4,0,0,1,9,2Z" />
                                        <path d="M9,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,9,14Z" />
                                    </svg>
                                <?php endif; ?>
                            </button>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Paginador -->
    <div class="flex justify-center space-x-1 mt-4 bg-white  mb-4 p-4">
        <?php if ($page > 1): ?>
            <a href="?page=user&items=<?php echo $page - 1; ?>" class="px-3 py-1 rounded bg-gray-200">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="px-3 py-1 rounded-full bg-blue-500 text-white"><?php echo $i; ?></span>
            <?php elseif ($i <= 3 || $i >= $totalPages - 2 || ($i >= $page - 1 && $i <= $page + 1)): ?>
                <a href="?page=user&items=<?php echo $i; ?>" class="px-3 py-1 rounded-full bg-gray-200"><?php echo $i; ?></a>
            <?php elseif ($i == 4 || $i == $totalPages - 3): ?>
                <span class="px-3 py-1">...</span>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=user&items=<?php echo $page + 1; ?>" class="px-3 py-1 rounded bg-gray-200">Next</a>
        <?php endif; ?>
    </div>
    <!-- 
    <script>
        function searchWithStoredPath() {
            // Obtiene la URL base del localStorage
            let baseUrl = localStorage.getItem('path') || '<?php echo $currentUrl; ?>';

            // Nuevos parámetros a concatenar (puedes obtener estos valores dinámicamente según el formulario)
            const newParams = new URLSearchParams({
                name: document.getElementById('name').value,
                status: document.getElementById('status').value
            });

            // Concatena la URL base con los nuevos parámetros
            const finalUrl = baseUrl + '&' + newParams.toString();

            // Redirige a la URL final
            window.location.href = finalUrl;
        }
    </script> -->

    <script>
        // Enfoca el campo 'name' y coloca el cursor al final del texto al cargar la página
        window.onload = function() {
            const nameField = document.getElementById('name');
            nameField.focus();
            const length = nameField.value.length;
            nameField.setSelectionRange(length, length); // Coloca el cursor al final del texto
        };


        function ReloadPage() {
            window.location.reload(true); // Forzar recarga desde el servidor
            window.location.href = '?page=user';

        }

        // formato para Dui
        function formatDUI() {
            var input = document.getElementById('vendor_dui');
            let value = input.value.replace(/\D/g, ''); // Eliminar cualquier carácter no numérico
            if (value.length > 8) {
                value = value.substring(0, 8) + '-' + value.substring(8, 9); // Formato 000000-0
            }
            input.value = value;
        }

        //formato para NIT

        function formatNIT() {
            var input = document.getElementById('vendor_nit');
            let value = input.value.replace(/\D/g, ''); // Eliminar cualquier carácter no numérico


            if (value.length > 12) {
                value = value.substring(0, 4) + '-' + value.substring(4, 10) + '-' + value.substring(10, 13) + '-' + value.substring(13, 14);
            }

            input.value = value;
        }


        function searchWithStoredPath() {
            // Obtiene la URL base del localStorage
            let baseUrl = localStorage.getItem('path') || '<?php echo $currentUrl; ?>';

            // Nuevos parámetros a concatenar (valores actuales del formulario)
            const newParams = new URLSearchParams({
                name: document.getElementById('name').value,
                status: document.getElementById('status').value
            });

            // Concatena la URL base con los nuevos parámetros
            const finalUrl = baseUrl + '&' + newParams.toString();

            // Actualiza la URL sin recargar la página
            history.replaceState(null, '', finalUrl);

            // Redirige a la URL final
            window.location.href = finalUrl;

            // Mantiene el foco en el campo 'name' y coloca el cursor al final del texto
            const nameField = document.getElementById('name');
            nameField.focus();
            const length = nameField.value.length;
            nameField.setSelectionRange(length, length); // Coloca el cursor al final del texto
        }


        const data = <?php echo $jsonData; ?>;

        function downloadExcel() {
            // Filtrar las columnas no deseadas
            const filteredData = data.map(({
                password,
                token,

                firstLogin,
                ...rest
            }) => rest);

            console.log("ESTE es EL RESULTADO DE LA DATA: ", filteredData);

            // Crea una hoja de cálculo con los datos filtrados
            const worksheet = XLSX.utils.json_to_sheet(filteredData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

            // Genera el archivo Excel y descarga
            XLSX.writeFile(workbook, "data.xlsx");
        }


        function openPopupForm() {
            const popupModal = document.getElementById('popupModal');
            popupModal.classList.remove('hidden');
        }

        function closePopupForm() {
            const popupModal = document.getElementById('popupModal');
            popupModal.classList.add('hidden');
        }


        //Editar Form
        // function openPopupFormEdit() {
        //     const popupModal = document.getElementById('popupModalEdit');
        //     popupModal.classList.remove('hidden');
        // }


        function openPopupFormEdit(button) {
            // Obtén los datos del botón
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            const name = button.getAttribute('data-name');
            const badge = button.getAttribute('data-badge');
            const userLevel = button.getAttribute('data-type-user');
            const status = button.getAttribute('data-status');
            const vendorName = button.getAttribute('data-vendor-name');
            const passwordOld = button.getAttribute('data-password');
            const commentsOld = button.getAttribute('data-comments');
            const dataUser = button.getAttribute('data-user');
            const numberAccount = button.getAttribute('data-numberAccount');
            const dui = button.getAttribute('data-dui');
            const nit = button.getAttribute('data-nit');
            const typeBank = button.getAttribute('data-typeBank');
            const bank = button.getAttribute('data-bank');
            const bankTypeAccount = button.getAttribute('data-typeAccount');

            console.log('-------------------->' + vendorName);

            // Crea el objeto JSON con los datos
            const userData = {
                userId: userId,
                username: username,
                name: name,
                badge: badge,
                status: status,
                userLevel: userLevel,
                vendorName: vendorName,
                passwordOld: passwordOld,
                commentsOld: commentsOld,
                dataUser: dataUser,
                numberAccount: numberAccount,
                dui: dui,
                nit: nit,
                bank: bank,
                typeBank: typeBank,
                bankAccountType: bankTypeAccount,
            };

            // Imprime el JSON en la consola para depuración
            console.log(JSON.stringify(userData, null, 2));
            console.log(JSON.stringify(dui, null, 2));
            console.log(JSON.stringify(userData, null, 2));

            // Opcional: pasa los datos al popup si necesitas mostrarlos
            const popupModal = document.getElementById('popupModalEdit');
            popupModal.classList.remove('hidden');

            // Ejemplo: llena los campos del popup (si tienes un formulario dentro)
            document.getElementById('userIdField').value = userData.userId;
            document.getElementById('userLevelField').value = userData.userLevel == 1 ? 'Administrator' : 'Vendor';
            document.getElementById('usernameField').value = userData.username;
            document.getElementById('nameField').value = userData.name;
            document.getElementById('badgeField').value = userData.badge;
            document.getElementById('commentsField').value = userData.commentsOld;
            document.getElementById('numberAccountField').value = userData.numberAccount;
            document.getElementById('statusField').value = "";
            document.getElementById('vendorNAME').value = userData.vendorName;
            document.getElementById('vendorNIT').value = userData.nit;
            document.getElementById('vendorDUI').value = userData.dui;
            document.getElementById('vendorBANK').value = userData.typeBank;
            document.getElementById('vendorAccountBank').value = userData.bankAccountType;

            //Hidden inputs
            document.getElementById('statusOld').value = userData.status;
            document.getElementById('vendorNameOld').value = userData.vendorName;
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('vendorName').value = userData.vendorName;
            });
            document.getElementById('passwordOld').value = userData.passwordOld;
            document.getElementById('commentsOld').value = userData.commentsOld;
            document.getElementById('dataUser').value = userData.dataUser;
            document.getElementById('numberAccountFieldOLD').value = userData.numberAccount;


        }

        //Edit status de user
        function openPopupFormStatus(button) {
            // Obtén los datos del botón
            const userId = button.getAttribute('data-id-status');

            const status = button.getAttribute('data-active-status');
            const dataUser = button.getAttribute('data-user-status');


            // Crea el objeto JSON con los datos
            const userData = {
                userId: userId,
                status: status,
                data: dataUser,

            };

            // Imprime el JSON en la consola para depuración
            console.log(JSON.stringify(userData, null, 2));

            // Opcional: pasa los datos al popup si necesitas mostrarlos
            const popupModal = document.getElementById('popupModalStatus');
            popupModal.classList.remove('hidden');

            // Ejemplo: llena los campos del popup (si tienes un formulario dentro)
            document.getElementById('status-id').value = userData.userId;
            //Hidden inputs

        }


        function closePopupFormStatus() {
            const popupModal = document.getElementById('popupModalStatus');
            popupModal.classList.add('hidden');
        }

        function closePopupFormEdit() {
            const popupModal = document.getElementById('popupModalEdit');
            popupModal.classList.add('hidden');
        }



        function toggleBadgeInput() {
            const userType = document.querySelector('input[name="userLevel"]:checked')?.value;
            const badgeContainer = document.getElementById('badgeContainer');
            const vendorContainer = document.getElementById('vendorContainer');
            const accountContainer = document.getElementById('numberContainer');
            const commentsContainer = document.getElementById('commentsContainer');
            const tipoCuenta = document.getElementById('tipoCuenta');
            const tipoBank = document.getElementById('tipoBanco');
            const vendorNit = document.getElementById('vendorDui');
            const vendorDui = document.getElementById('vendorNit');


            document.getElementById("firstname").value = "";
            document.getElementById("firstlastname").value = "";
            document.getElementById("fullname").value = "";
            document.getElementById("username").value = "";
            document.getElementById("badge").value = "";
            document.getElementById("vendor_name").value = "";
            document.getElementById("number_account").value = "";
            document.getElementById("comments").value = "";
            document.getElementById("tipoCuenta").value = "";

            if (userType === '1') {
                badgeContainer.style.display = 'block';
                vendorContainer.style.display = 'none';
                accountContainer.style.display = 'none';
                commentsContainer.style.display = 'none';
                tipoCuenta.style.display = 'none';
                tipoBank.style.display = 'none';
                vendorDui.style.display = 'none';
                vendorNit.style.display = 'none';
            } else {
                vendorContainer.style.display = 'block';
                accountContainer.style.display = 'block';
                commentsContainer.style.display = 'block';
                badgeContainer.style.display = 'none';
                tipoCuenta.style.display = 'block';
                tipoBank.style.display = 'block';
                vendorDui.style.display = 'block';
                vendorNit.style.display = 'block';


                document.getElementById("firstname").value = "";
                document.getElementById("firstlastname").value = "";
                document.getElementById("fullname").value = "";
                document.getElementById("username").value = "";
                document.getElementById("typeCuenta").value = "";
                document.getElementById("vendor_dui").value = "";
                document.getElementById("vendor_nit").value = "";
                document.getElementById("typeCuenta").value = "";
                document.getElementById("typeBank").value = "";
                document.getElementById("password").value = "";
            }
        }



        function searchBadge(badgeValue) {
            // console.log("Badge value to search:", badgeValue);
            // console.log("Tipo de dataEmployeesss:", typeof dataEmployeesss);
            // console.log("Contenido de dataEmployeesss antes de parsear:", dataEmployeesss);

            // Si dataEmployeesss es un string, parsearlo a un array temporal
            let parsedData = dataEmployeesss;
            if (typeof dataEmployeesss === "string") {
                try {
                    parsedData = JSON.parse(dataEmployeesss); // No reasignamos la constante original
                    // console.log("parsedData después de parsear:", parsedData);
                } catch (error) {
                    // console.error("Error al parsear dataEmployeesss:", error);
                    return; // Termina la función si hay un error
                }
            }

            // Confirmar que ahora es un array
            if (!Array.isArray(parsedData)) {
                // console.error("parsedData no es un array después de parsear.");
                return;
            }

            // Busca el empleado basado en el badge
            const employee = parsedData.find(emp => String(emp.badge) === String(badgeValue));

            // Actualiza los campos según el resultado de la búsqueda
            if (employee) {
                console.log("Employee found:", employee);

                document.getElementById("fullname").value = [
                        employee.firstName,
                        employee.secondName,
                        employee.thirdName,
                        employee.firstLastName,
                        employee.secondLastName,
                        employee.thirdLastName
                    ]
                    .filter(name => name && name.trim() !== "") // Filtrar valores vacíos o nulos
                    .join(" "); // Unirlos con un espacio


                document.getElementById("firstname").value = employee.firstName || "";
                document.getElementById("firstlastname").value = employee.firstLastName || "";


                document.getElementById("username").value = employee.corporateEmail || "";
            } else {
                console.warn("No employee found with the given badge value.");

                document.getElementById("fullname").value = "";
                document.getElementById("username").value = "";
            }
        }



        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('alert');
        const message = urlParams.get('message');


        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Usuario creado!',
                text: 'El usuario ha sido creado exitosamente.',
            }).then(() => {
                window.location.href = '/vendor/index.php?page=user';
            });
        } else if (status === 'update') {
            Swal.fire({
                icon: 'success',
                title: '¡Usuario actualizado!',
                text: 'El usuario ha sido actualizado exitosamente.',
            }).then(() => {
                window.location.href = '/vendor/index.php?page=user';
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message ? decodeURIComponent(message) : 'Hubo un problema al crear el usuario.',
            }).then(() => {
                window.location.href = '/vendor/index.php?page=user';
            });
        }
    </script>


</body>