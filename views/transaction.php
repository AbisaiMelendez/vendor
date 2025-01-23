<?php

// Incluir el archivo de datos de transacciones
include '../vendor/models/transaccion-vendor.php';
include '../vendor/models/cuotas-vendor.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Transacciones</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgb(255, 255, 255);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            background-color: rgb(255, 255, 255);
        }

        th {
            background-color: rgb(255, 255, 255);
        }

        #transaccionesTable_wrapper {
            margin: 50px auto;
            max-width: 100%;


        }


        .date-filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .date-filter-container input {
            padding: 8px;
            border: 0.8px solid #ddd;
            border-radius: 2px;
        }


        .table-bordered {
            width: 100%;

            border-collapse: unset;
            border-spacing: 0;
            /* Elimina espacio entre bordes */
        }

        table.dataTable {
            width: 100%;
            margin: 0 auto;
            clear: both;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .table-bordered th,
        .table-bordered td {
            border: 0.5px solid #ddd;
            /* Bordes sutiles y delgados */
            padding: 8px;
            text-align: left;
        }

        .table-bordered th {
            background-color: rgb(255, 255, 255);
            /* Fondo ligeramente gris para encabezados */
        }

        .table-bordered tr:nth-child(even) {
            background-color: #fefefe;
            /* Fondo sutil en filas pares */
        }

        .table-bordered tr:hover {
            background-color: rgb(255, 255, 255);
            /* Efecto hover */
        }
    </style>
</head>

<body>
    <div id="transaccionesTable_wrapper">
        <div class="mb-4">
            <h1 class="text-2xl mt-8 mb-8 font-semibold">Tabla de Transacciones</h1>
        </div>

        <!-- Filtros por fecha -->
        <div class="date-filter-container">
            <div>
                <label for="minDate">Fecha Inicial:</label>
                <input type="text" id="minDate" placeholder="Selecciona la fecha inicial">
            </div>
            <div>
                <label for="maxDate">Fecha Final:</label>
                <input type="text" id="maxDate" placeholder="Selecciona la fecha final">
            </div>
        </div>

        <table id="transaccionesTable" class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Batch ID</th>
                    <th>Vendor ID</th>
                    <th>Nombre del Vendedor</th>
                    <th>Badge</th>
                    <th>Nombre</th>

                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Opción de Pago</th>
                    <th>Trabajo</th>
                    <th>Fecha de Creación</th>
                    <th>Actions</th>
                    <th>Cuotas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Incluir el archivo que contiene la lógica para obtener los resultados
                include '../vendor/models/transaccion-vendor.php';


                // print_r($_SESSION);
                $idUSER = $_SESSION['user']['userId'];
                $levelUSER = $_SESSION['user']['level'];

                // echo 'userr id----------->' . $idUSER;
                // echo 'level user id----------->' . $levelUSER;

                //print_r($results);
                if ($levelUSER == '2') {
                    $results = array_filter($results, function ($row) use ($idUSER) {
                        return $row['idVendor'] == $idUSER;
                    });
                }

                // Validar si $results tiene datos antes de recorrerlos
                if (!empty($results)) {
                    foreach ($results as $row) {
                        echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['idbatch']}</td>
                        <td>{$row['idVendor']}</td>
                        <td>{$row['nameVendor']}</td>
                        <td>{$row['badge']}</td>
                        <td>{$row['name']}</td>
                      
                        <td>{$row['product']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['total']}</td>
                        <td>{$row['payment_option']}</td>
                        <td>{$row['job']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <button class='bg-blue-500 text-white rounded hover:bg-blue-600' onclick='getId({$row['id']})'>
                                <i class='fa fa-binoculars' aria-hidden='true'></i>
                            </button>
                        </td>
                        <td>
                            <button onclick='showForm(\"{$row['idbatch']}\")' class='bg-green-500 text-white rounded hover:bg-green-600'>
                                <i class='text-white fa fa-shopping-bag' aria-hidden='true'></i>
                            </button>
                        </td>
                    </tr>";
                    }
                } else {
                    // Mostrar un mensaje si no hay resultados
                    echo "<tr><td colspan='16' class='text-center text-gray-500'>No hay datos disponibles</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>




    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <!-- JSZip (para exportar a Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- Botones de exportación a Excel -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DatePicker
            $("#minDate, #maxDate").datepicker({
                dateFormat: 'yy-mm-dd'
            });

            // Configurar DataTable
            var table = $('#transaccionesTable').DataTable({
                paging: true,
                searching: true,
                order: [
                    [13, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1], // Opciones: 10, 25, 50, 100, Todos
                    [10, 25, 50, 100, "Todos"]
                ],
                pageLength: 10, // Cantidad predeterminada
                columnDefs: [{
                    targets: [13, 14],
                    type: 'date'
                }],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/Spanish.json"
                },
                dom: 'Bfrtip<"bottom"l>', // Mover el lengthMenu abajo (dentro de "bottom")
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Descargar Excel',
                    title: 'Reporte de Transacciones',
                    className: 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'
                }]
            });

            // Filtro personalizado de DataTables por rango de fechas
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var dateTime = data[13]; // Columna de 'Fecha de Creación'

                if (!dateTime) return false;

                var date = dateTime.split(' ')[0];
                var minDate = min ? new Date(min + "T00:00:00").getTime() : NaN;
                var maxDate = max ? new Date(max + "T23:59:59").getTime() : NaN;
                var targetDate = new Date(date + "T00:00:00").getTime();

                return (
                    (isNaN(minDate) && isNaN(maxDate)) ||
                    (isNaN(minDate) && targetDate <= maxDate) ||
                    (minDate <= targetDate && isNaN(maxDate)) ||
                    (minDate <= targetDate && targetDate <= maxDate)
                );
            });

            // Redibujar la tabla cuando las fechas cambien
            $('#minDate, #maxDate').change(function() {
                table.draw();
            });
        });



        function getId(id) {


            // Obtener la fila completa basada en el botón presionado
            const row = document.querySelector(`button[onclick='getId(${id})']`).closest('tr');
            const cells = row.querySelectorAll('td');

            // Extraer la información de la fila
            const data = Array.from(cells).map(cell => cell.textContent.trim());



            // Crear el contenido del formulario


            const formContent = `
            <form class="bg-white p-4 rounded-lg shadow-lg w-full max-w-8xl">
                <h2 class="text-2xl font-bold mb-6">Editar Información</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID:</label>
                        <input type="text" value="${data[0]}" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Batch ID:</label>
                        <input type="text" value="${data[1]}" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vendor ID:</label>
                        <input type="text" value="${data[2]}" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vendor:</label>
                        <input type="text" value="${data[3]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Badge:</label>
                        <input type="text" value="${data[4]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del empleado:</label>
                        <input type="text" value="${data[5]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trabajo:</label>
                        <input type="text" value="${data[6]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Compañía:</label>
                        <input type="text" value="${data[7]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad:</label>
                        <input type="text" value="${data[8]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio:</label>
                        <input type="text" value="${data[9]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm fontF-medium text-gray-700">Total:</label>
                        <input type="text" value="${data[10]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Producto:</label>
                        <input type="text" value="${data[11]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Opción de Pago:</label>
                        <input type="text" value="${data[12]}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Creación:</label>
                        <input type="text" value="${data[13]}" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" readonly>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closePopup()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cerrar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Guardar</button>
                </div>
            </form>
        `;




            // Crear el popup
            const popup = document.createElement('div');
            popup.id = 'popupForm';
            popup.style.position = 'fixed';
            popup.style.top = '50%';
            popup.style.left = '50%';
            popup.style.transform = 'translate(-50%, -50%)';
            popup.style.backgroundColor = '#fff';
            popup.style.border = '1px solid #ccc';
            popup.style.padding = '20px';
            popup.style.zIndex = '1000';
            popup.innerHTML = formContent;

            // Crear el fondo oscuro
            const overlay = document.createElement('div');
            overlay.id = 'popupOverlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            overlay.style.zIndex = '999';

            // Agregar el popup y el fondo al DOM
            document.body.appendChild(overlay);
            document.body.appendChild(popup);
        }

        function closePopup() {
            document.getElementById('popupForm').remove();
            document.getElementById('popupOverlay').remove();
        }


        function showForm(x) {
            const idCuota = x;
            const cuotaData = <?php echo json_encode($resultCuotas); ?>;
            console.log(cuotaData);
            console.log('----------------->' + idCuota);

            // Filtrar los datos según el idbatch
            const filteredData = cuotaData.filter(item => item.idbatch === idCuota);

            if (filteredData.length === 0) {
                alert('No data found for the selected ID.');
                return;
            }

            // Generar el contenido dinámico con los datos encontrados
            const dataContent = filteredData.map(item => `
        <div class="bg-gray-100 rounded-lg shadow-md p-4 mb-4">
            <h3 class="text-lg font-bold text-gray-700 mb-2">Installment #${item.installment_number}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Batch ID</p>
                    <p class="text-gray-700">${item.idbatch}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Amount</p>
                    <p class="text-gray-700">$${item.amount}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Status</p>
                <p class='${
                        item.status == 'pending' 
                            ? 'bg-red-400 text-white px-2 py-1 rounded-full inline-block' 
                            : 'bg-green-500 text-white px-2 py-1 rounded-full inline-block'
                    }'>
                        ${item.status}
                    </p>                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Due Date</p>
                    <p class="text-gray-700">${item.due_date}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Paid</p>
                    <p class="text-gray-700">${item.paid ? 'Yes' : 'No'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Payment Date</p>
                    <p class="text-gray-700">${item.payment_date ? item.payment_date : 'N/A'}</p>
                </div>
            </div>
        </div>
    `).join('');

            // Generar el layout del formulario con los datos filtrados
            const formLayout = `
            <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75 z-50">
                <div class="bg-gray-200 p-6 rounded-lg shadow-lg w-full ${filteredData.length > 2 ? 'max-w-4xl' : 'max-w-md'} relative">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Installment Details</h2>
                    <div class="grid ${filteredData.length > 2 ? 'grid-cols-2' : 'grid-cols-1'} gap-6 overflow-y-auto max-h-120">
                        ${dataContent}
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeForm()" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-900">
                            Close
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        `;



            // Crear el contenedor y agregarlo al DOM
            const container = document.createElement('div');
            container.id = 'formContainer';
            container.innerHTML = formLayout;
            document.body.appendChild(container);
        }

        function closeForm() {
            const container = document.getElementById('formContainer');
            if (container) {
                container.remove();
            }
        }
    </script>



</body>

</html>