<?php
// Incluir el archivo con los datos
include '../vendor/models/list-vendors.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendors</title>
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
            background-color:rgb(255, 255, 255);
            /* Fondo ligeramente gris para encabezados */
        }

        .table-bordered tr:nth-child(even) {
            background-color: #fefefe;
            /* Fondo sutil en filas pares */
        }

        .table-bordered tr:hover {
            background-color: #f1f1f1;
            /* Efecto hover */
        }
    </style>
</head>

<body>
    <div id="transaccionesTable_wrapper">
        <div class="mb-4 ">
            <h1 class="text-2xl mt-8 mb-8 font-semibold">List vendors</h1>
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

        <!-- Tabla -->
        <table id="transaccionesTable" class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre del Vendedor</th>
                    <th>Nombre Completo</th>
                    <th>Tipo de Cuenta</th>
                    <th>Nivel de Usuario</th>
                    <th>Número de Cuenta</th>
                    <th>Status</th>
                    <th>Fecha de Creación</th>

                    <th>Comentarios</th>
                    <th>Acciones</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($results as $row) {
                    echo "<tr>
                            <td>{$row['userId']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['name_vendor']}</td>
                            <td>{$row['fullname']}</td>
                            <td>{$row['typeAccount']}</td>
                            <td>{$row['userLevel']}</td>
                            <td>{$row['number_account']}</td>";
                
                    // Aquí implementamos el status dinámicamente
                    if ($row['status'] == 1) {
                        echo "<td>
                                <span style='background-color: green; color: white; padding: 5px; border-radius: 5px;'>Activo</span>
                              </td>";
                    } elseif ($row['status'] == 0) {
                        echo "<td>
                                <span style='background-color: red; color: white; padding: 5px; border-radius: 5px;'>Inactivo</span>
                              </td>";
                    }
                
                    echo "<td>{$row['createdAt']}</td>
                          <td>{$row['comments']}</td>
                          <td>
                              <button class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600' onclick='getId({$row['userId']})'>Edit</button>
                          </td>
                          <td>
                              <button class='bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600'>View</button>
                          </td>
                        </tr>";
                }
          
                
                ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
                    [8, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"]
                ],
                pageLength: 10,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/Spanish.json"
                },
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Descargar Excel',
                    title: 'Reporte de Vendors',
                    className: 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'
                }]
            });

            // Filtro personalizado por rango de fechas
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var dateTime = data[8]; // Columna 'Fecha de Creación'

                if (!dateTime) return false;

                var date = dateTime.split(' ')[0];
                var minDate = min ? new Date(min).getTime() : NaN;
                var maxDate = max ? new Date(max).getTime() : NaN;
                var targetDate = new Date(date).getTime();

                return (
                    (isNaN(minDate) && isNaN(maxDate)) ||
                    (isNaN(minDate) && targetDate <= maxDate) ||
                    (minDate <= targetDate && isNaN(maxDate)) ||
                    (minDate <= targetDate && targetDate <= maxDate)
                );
            });

            // Redibujar la tabla al cambiar fechas
            $('#minDate, #maxDate').change(function() {
                table.draw();
            });
        });

        function getId(id) {
            alert("ID seleccionado: " + id);
        }
    </script>
</body>

</html>