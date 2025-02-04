<?php

include '../vendor/models/notificacions.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' fill='%23008000'/><path d='M20 50 L40 70 L80 30' stroke='%23FFFFFF' stroke-width='10' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>">

    <style>
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
    </style>
</head>

<body class="bg-gray-200 flex w-full">

    <!-- Sidebar -->
    <div id="sidebar" class="relative w-52 bg-gradient-to-b from-gray-900 to-black text-indigo-100 font-semibold text-sm hidden md:block">
        <div class="flex p-6 font-bold text-sm">
            <img class="w-full mb-14" src="https://surgesetup.com/img/SurgePays_Logo_.png" />
        </div>

        <div class="flex flex-col mt-24 relative z-10 ml-6">
            <nav class="space-y-7">
                <?php

                // print_r($_SESSION);
                $idUSER = $_SESSION['user']['userId'];
                $levelUSER = $_SESSION['user']['level'];


                if ($levelUSER == '2' || $levelUSER =='3' ) {
                ?>
                    <a href="?page=transaction" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>
                        <span>Transaction</span>
                    </a>
                    <a href="?page=report" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Report</span>
                    </a>
                    <a href="?page=vendor_client" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                        </svg>
                        <span>Vendor Client</span>
                    </a>
                <?php
                } else {
                ?>
                    <a href="?page=dashboard" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Dashboard</span>
                    </a>
                    <a href="?page=vendor" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Vendor</span>
                    </a>
                    <a href="?page=transaction" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Transaction</span>
                    </a>
                    <a href="?page=report" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Report</span>
                    </a>
                    <a href="?page=vendor_client" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Vendor Client</span>
                    </a>
                    <a href="?page=user" class="flex items-center py-2 px-4 hover:bg-indigo-900 hover:text-white">
                        <span>Users</span>
                    </a>
                <?php
                }
                ?>
            </nav>
        </div>
    </div>



    <!-- Content Wrapper -->
    <div class="flex-1">
        <?php

        include '../vendor/models/notificacions.php';


        ?>
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-4 flex justify-between items-center">
            <h1 class="text-sm text-gray-500"><a href="#">
                    <i class="fas fa-clock"></i>
                </a><?php echo date('Y-m-d'); ?></h1>
            <!-- Iconos de acciones -->
            <canvas id="nodeCanvas" class="absolute "></canvas>
            <div class="flex items-center space-x-4 ">
                <!-- Botón de menú para dispositivos móviles -->


                <!-- Notificaciones -->
                <div class="relative">
                    <button id="notificationsBtn" class="text-yellow-500 focus:outline-none  rounded-full hover:bg-gray-600 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div
                        id="notificationsDropdown"
                        class="hidden absolute top-36 right-0 mt-4 w-48 bg-gray-700 text-white rounded-lg shadow-lg p-4 z-50"
                        style="transform: translateY(-100%);">
                        <?php
                        // Inicializar $notifications como un array vacío si no está definido
                        $level = $_SESSION['user']['level'];
                        if ($level == 1 ) {
                            $notifications = $notifications ?? [];
                            if (!empty($notificationData)) {
                                foreach ($notificationData as $item) {
                                    echo '<p class="text-sm text-gray-400 p-4 hover:text-white">Estado ' . htmlspecialchars($item['status']) . ' (' . htmlspecialchars($item['total']) . ')</p>';
                                }
                            } else {
                                echo '<p class="text-sm text-gray-400 p-4 hover:text-white">No tienes nuevas notificaciones</p>';
                            }
                        } else {
                            echo '<p class="text-sm text-gray-400 p-4 hover:text-white">No tienes nuevas notificaciones</p>';
                        }
                        ?>




                    </div>

                </div>

                <!-- Configuración -->
                <button class="text-white focus:outline-none rounded-full hover:bg-gray-600">
                    <i class="fas fa-cog"></i>
                </button>

                <!-- Menú de Usuario -->
                <div class="relative">
                    <button id="userMenuBtn" class="text-white focus:outline-none rounded-full hover:bg-gray-600">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-gray-700 text-white rounded-lg shadow-lg p-4">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-600 hover:text-white">Reset Password</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-600 hover:text-white">Notifications</a>
                        <a href="?page=logout" class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-600 hover:text-white">Cerrar sesión</a>


                    </div>
                </div>
                <button id="toggler" class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <script>
            const canvas = document.getElementById("nodeCanvas");
            const ctx = canvas.getContext("2d");

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            const nodes = Array.from({
                length: 5
            }, () => ({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                dx: (Math.random() - 0.5) * 2,
                dy: (Math.random() - 0.5) * 2,
                radius: 2 + Math.random() * 3
            }));

            function drawNodes() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                nodes.forEach((node, i) => {
                    ctx.beginPath();
                    ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                    ctx.fillStyle = "rgba(255, 255, 255, 0.7)";
                    ctx.fill();
                    ctx.closePath();

                    for (let j = i + 1; j < nodes.length; j++) {
                        const dx = nodes[j].x - node.x;
                        const dy = nodes[j].y - node.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);

                        if (distance < 100) {
                            ctx.beginPath();
                            ctx.moveTo(node.x, node.y);
                            ctx.lineTo(nodes[j].x, nodes[j].y);
                            ctx.strokeStyle = `rgba(255, 255, 255, ${1 - distance / 100})`;
                            ctx.stroke();
                            ctx.closePath();
                        }
                    }
                });
            }

            function updateNodes() {
                nodes.forEach(node => {
                    node.x += node.dx;
                    node.y += node.dy;

                    if (node.x < 0 || node.x > canvas.width) node.dx *= -1;
                    if (node.y < 0 || node.y > canvas.height) node.dy *= -1;
                });
            }

            function animate() {
                drawNodes();
                updateNodes();
                requestAnimationFrame(animate);
            }

            canvas.addEventListener("mousemove", event => {
                const rect = canvas.getBoundingClientRect();
                const mouseX = event.clientX - rect.left;
                const mouseY = event.clientY - rect.top;

                nodes.forEach(node => {
                    const dx = node.x - mouseX;
                    const dy = node.y - mouseY;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 500) {
                        node.x += dx / distance * 10;
                        node.y += dy / distance * 10;
                    }
                });
            });

            animate();
        </script>



        <!-- Page Content -->
        <div class="p-4 bg-white w-full min-h-screen">



            <?php include $pageContent; ?>



            <footer class="bg-white mt-auto">
                <div class="w-full max-w-screen-xl mx-auto md:py-8">

                    <hr class="my-6 sm:mx-auto dark:border-gray-700 lg:my-8" />
                    <span class="block text-sm text-gray-500 sm:text-center xm:text-center dark:text-gray-400 p-8">
                        © 2024 <a href="https://flowbite.com/" class="hover:underline">Surgepays</a>. All Rights Reserved.
                    </span>
                </div>
            </footer>

        </div>

    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggler = document.getElementById('toggler');
            const sidebar = document.getElementById('sidebar');
            const notificationsBtn = document.getElementById('notificationsBtn');
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');

            // Toggle sidebar
            toggler.onclick = function() {
                sidebar.classList.toggle('hidden');
            };

            // Toggle notifications dropdown
            notificationsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationsDropdown.classList.toggle('hidden');
                userDropdown.classList.add('hidden'); // Cierra el dropdown de usuario si está abierto
            });

            // Toggle user menu dropdown
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
                notificationsDropdown.classList.add('hidden'); // Cierra el dropdown de notificaciones si está abierto
            });

            // Cerrar ambos dropdowns al hacer clic fuera
            document.addEventListener('click', () => {
                notificationsDropdown.classList.add('hidden');
                userDropdown.classList.add('hidden');
            });
        });
    </script>
</body>

</html>