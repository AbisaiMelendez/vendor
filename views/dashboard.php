<h3 class="text-xl font-bold mb-2">Dashboard</h3>


<div class="bg-gray-200 rounded-lg w-full">
    <!-- <h4 class="text-blue p-2 bg-gray7600 text-sm md:text-2xl font-semibold rounded-lg text-center">
        Welcome to Vendor!
    </h4> -->

</div>

<?php include '../vendor/models/total-credit.php' ?>
<?php include '../vendor/models/total-paid.php' ?>
<?php include '../vendor/models/total-pending.php' ?>
<?php include '../vendor/models/vendorTabs.php' ?>

<!-- <?php echo $dataTabs; ?> -->

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full p-4 md:px-8 mt-24">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-700 font-semibold">Total Credit</h3>
        <p class="text-gray-400 text-sm uppercase">Sales</p>
        <div class="flex items-center space-x-2 mt-2">
            <p class="text-2xl font-bold text-gray-800"><?php echo '$' . $result['total_sum'];  ?></p>
            <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded">+100%</span>
        </div>
        <div class="mt-4">
            <div class="h-2 bg-gradient-to-r from-green-200 to-blue-500 rounded"></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-700 font-semibold">Paid balance</h3>
        <p class="text-gray-400 text-sm uppercase">Credit</p>
        <div class="flex items-center space-x-2 mt-2">
            <p class="text-2xl font-bold text-gray-800">$<?php echo $resultPaid['amount']; ?></p>
            <?php
            // Asegúrate de que total_sum y amount no sean cero o nulos para evitar errores de división
            if ($result['total_sum'] > 0) {
                $percentagePaid = ($resultPaid['amount'] / $result['total_sum']) * 100;
            } else {
                $percentagePaid = 0; // En caso de que no haya ventas
            }
            ?>
            <span class="text-sm text-red-500 bg-red-100 px-2 py-1 rounded">
                <?php echo number_format($percentagePaid, 2); ?>%
            </span>
        </div>
        <div class="mt-4">
            <div class="h-2 bg-gradient-to-r from-blue-500 to-green-200 rounded"></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-700 font-semibold">Balance Pending</h3>
        <p class="text-gray-400 text-sm uppercase">Sell</p>
        <div class="flex items-center space-x-2 mt-2">
            <p class="text-2xl font-bold text-gray-800"><?php echo '$' . $resultPending['amount']; ?></p>
            <?php
            // Asegúrate de que total_sum y amount no sean cero o nulos para evitar errores de división
            if ($result['total_sum'] > 0) {
                $percentagePending = ($resultPending['amount'] / $result['total_sum']) * 100;
            } else {
                $percentagePending = 0; // En caso de que no haya ventas
            }
            ?>

            <span class="text-sm text-green-500 bg-green-100 px-2 py-1 rounded">
                <?php echo number_format($percentagePending, 2); ?>%
            </span>
        </div>
        <div class="mt-4">
            <div class="h-2 bg-gradient-to-r from-green-200 to-blue-500 rounded"></div>
        </div>
    </div>
</div>


<!-- Contenedor de la cuadrícula de gráficos con límite de altura -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full p-4 md:px-8 mt-24">

    <!-- Gráfico 2 - Two Linked Pie Charts with a Legend -->
    <div class="bg-white shadow-md rounded-lg p-4 h-80 overflow-hidden flex flex-col items-center justify-center">
        <h3 class="text-md font-semibold mb-2">(%) Vendor Credit</h3>
        <div id="chart2" style="width: 100%; height: 100%;"></div>
    </div>
    <!-- Gráfico 1 - Stacked and Clustered Column Chart -->
    <div class="bg-white shadow-md rounded-lg p-4 h-80 overflow-hidden flex flex-col items-center justify-center">
        <h3 class="text-md font-semibold mb-2">Vendor Credit Balance Chart</h3>
        <div id="chart1" style="width: 100%; height: 100%;"></div>
    </div>

</div>

<!-- CDNs de amCharts -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Script para generar los gráficos -->
<script>
    am5.ready(function() {
        // Tema de animación
        am5.addLicense("AM5C329334656");

        // Configuración del gráfico 1 - Stacked and Clustered Column Chart
        let root1 = am5.Root.new("chart1");
        root1.setThemes([am5themes_Animated.new(root1)]);

        let chart1 = root1.container.children.push(
            am5xy.XYChart.new(root1, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX: true
            })
        );

        let xAxis1 = chart1.xAxes.push(
            am5xy.CategoryAxis.new(root1, {
                categoryField: "category",
                renderer: am5xy.AxisRendererX.new(root1, {})
            })
        );

        let yAxis1 = chart1.yAxes.push(
            am5xy.ValueAxis.new(root1, {
                renderer: am5xy.AxisRendererY.new(root1, {})
            })
        );


        let series1 = chart1.series.push(
            am5xy.ColumnSeries.new(root1, {
                name: "Series 1",
                stacked: true,
                xAxis: xAxis1,
                yAxis: yAxis1,
                valueYField: "value1",
                categoryXField: "category"
            })
        );

        let series2 = chart1.series.push(
            am5xy.ColumnSeries.new(root1, {
                name: "Series 2",
                stacked: true,
                xAxis: xAxis1,
                yAxis: yAxis1,
                valueYField: "value2",
                categoryXField: "category"
            })
        );


        // Agregar labels para mostrar el valor de cada barra
        series1.bullets.push(function() {
            return am5.Bullet.new(root1, {
                sprite: am5.Label.new(root1, {
                    text: "{value1}",
                    centerY: am5.p50,
                    centerX: am5.p50,
                    populateText: true
                })
            });
        });

        series2.bullets.push(function() {
            return am5.Bullet.new(root1, {
                sprite: am5.Label.new(root1, {
                    text: "{value2}",
                    centerY: am5.p50,
                    centerX: am5.p50,
                    populateText: true
                })
            });
        });

        // Asignar datos correctamente y convertir a números
        let chartData = <?php echo $dataTabs; ?>;

        // Convertir a números antes de asignar los datos
        chartData = chartData.map(data => ({
            ...data,
            value1: Number(data.value1), // Convertir value1 a número
            value2: Number(data.value2) // Convertir value2 a número
        }));

        series1.data.setAll(chartData);
        series2.data.setAll(chartData);


        xAxis1.data.setAll(chartData);




        // Configuración del gráfico 2 - Two Linked Pie Charts with a Legend
        let root2 = am5.Root.new("chart2");
        root2.setThemes([am5themes_Animated.new(root2)]);

        let container2 = root2.container.children.push(
            am5.Container.new(root2, {
                layout: root2.horizontalLayout,
                width: am5.percent(100),
                height: am5.percent(100)
            })
        );

        let chart2 = container2.children.push(
            am5percent.PieChart.new(root2, {
                radius: am5.percent(70),
                innerRadius: am5.percent(30),
                legend: am5.Legend.new(root2, {})
            })
        );

        // Obtener y convertir los datos a números con validación
        let chartData2 = <?php echo $dataTabs; ?>;

        // Validar si chartData2 está definido y tiene contenido
        if (Array.isArray(chartData2) && chartData2.length > 0) {
            chartData2 = chartData2.map(data => ({
                ...data,
                value: !isNaN(Number(data.value1)) ? Number(data.value1) : 2 // Asegurar valor numérico válido
            }));

            // Asignar datos al gráfico después de convertirlos
            chart2.series.push(
                am5percent.PieSeries.new(root2, {
                    valueField: "value",
                    categoryField: "category"
                })
            ).data.setAll(chartData2);
        } else {
            console.error("Los datos del gráfico están vacíos o no definidos.");
        }

    });
</script>