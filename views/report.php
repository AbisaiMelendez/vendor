<?php

// Incluir el archivo de datos de transacciones
include '../vendor/models/reports.php';
include '../vendor/models/cuotas-vendor.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
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
            background-color: #fff;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
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
            border: 1px solid #ddd;
            border-radius: 2px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }

        .table-bordered tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-bordered tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div id="transaccionesTable_wrapper">
        <div class="mb-4">
            <h1 class="text-2xl mt-8 mb-8 font-semibold">Reports</h1>
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
                    <th>ID de Cuota</th>
                    <th>Nombre Completo</th>
                    <th>Badge</th>
                    <th>Número de Cuota</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha de Vencimiento</th>
                    <!-- <th>Pagado</th>
                    <th>Fecha de Pago</th> -->
                    <th>Trabajo</th>
                    <th>Producto</th>
                    <th>idVendor</th>
                    <th>vendorName</th>
                    <th>fullnameVendorName</th>
                    <th>NumberAccountVendor</th>
                    <th>Comments</th>
                    <th>Fecha de Creación del Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php

                //print_r($_SESSION);
                $idUSER = $_SESSION['user']['userId'];
                $levelUSER = $_SESSION['user']['level'];

                // echo 'userr id----------->' . $idUSER;
                // echo 'level user id----------->' . $levelUSER;

                //print_r($results);
                if ($levelUSER == '2') {
                    $results7 = array_filter($results7, function ($row) use ($idUSER) {
                        return $row['idVendor'] == $idUSER;
                    });
                }

                if (!empty($results7)) {
                    foreach ($results7 as $row) {
                        echo "<tr>
            <td>{$row['installment_id']}</td>
            <td>{$row['fullname']}</td>
            <td>{$row['badge']}</td>
            <td>{$row['installment_number']}</td>
            <td>{$row['amount']}</td>
            <td>";
                        // Agregar lógica condicional para `status`
                        if (strtolower($row['status']) === 'pending') {
                            echo "<span style='background-color: gray; color: white; border-radius: 5px; padding: 2px 6px;'>
                    {$row['status']}
                  </span>";
                        } elseif (strtolower($row['status']) === 'approved') {
                            echo "<span style='background-color: green; color: white; border-radius: 5px; padding: 2px 6px;'>
                    {$row['status']}
                  </span>";
                        } else {
                            echo "<span style='background-color: green; color: white; border-radius: 5px; padding: 2px 6px;'>
                    {$row['status']}
                  </span>";
                        }
                        echo "</td>
            <td>{$row['due_date']}</td>
        
            <td>{$row['job']}</td>
            <td>{$row['product']}</td>
            <td>{$row['idVendor']}</td>
            <td>{$row['nameVendor']}</td>
            <td>{$row['fullnameVendor']}</td>
            <td>{$row['number_account']}</td>
            <td>{$row['comments']}</td>
            <td>{$row['payment_created_at']}</td>
        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='13' class='text-center text-gray-500'>No hay datos disponibles</td></tr>";
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
            $("#minDate, #maxDate").datepicker({
                dateFormat: 'yy-mm-dd'
            });

            var table = $('#transaccionesTable').DataTable({
                paging: true,
                searching: true,
                order: [
                    [4, 'asc']
                ], // Ordenar por "Fecha de Vencimiento"
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/Spanish.json"
                },
                dom: 'Bfrtip',
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
                var dueDate = data[6]; // Columna "Fecha de Vencimiento"

                if (!dueDate) return false;

                var dueDateValue = new Date(dueDate).getTime();
                var minDate = min ? new Date(min).getTime() : NaN;
                var maxDate = max ? new Date(max).getTime() : NaN;

                return (
                    (isNaN(minDate) && isNaN(maxDate)) ||
                    (isNaN(minDate) && dueDateValue <= maxDate) ||
                    (minDate <= dueDateValue && isNaN(maxDate)) ||
                    (minDate <= dueDateValue && dueDateValue <= maxDate)
                );
            });

            $('#minDate, #maxDate').change(function() {
                table.draw();
            });
        });
    </script>
</body>

</html>