<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - My App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-center space-x-6">
        <a href="/profil" class="hover:text-blue-600">Profil</a>
        <a href="/katalog" class="text-blue-600 font-bold border-b-2 border-blue-600">Katalog</a>
        <a href="/bantuan" class="hover:text-blue-600">Bantuan</a>
    </nav>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Katalog</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Item 1 -->
            <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                <div class="h-40 bg-gray-200 rounded mb-3">
                    <img src="" alt="">
                </div>
                <h2 class="font-bold">Foto A</h2>
                <p class="text-sm text-gray-600">Foto Keren</p>
                <button class="mt-3 bg-blue-600 text-white px-4 py-1 rounded text-sm">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Velit quo assumenda accusamus dolor nulla, quod ullam impedit ratione sapiente repudiandae id facere animi quos architecto iusto recusandae voluptatibus a provident!</button>
            </div>
            <!-- Item 2 -->
            <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                <div class="h-40 bg-gray-200 rounded mb-3"></div>
                <h2 class="font-bold">Foto B</h2>
                <p class="text-sm text-gray-600">Foto Sigma</p>
                <button class="mt-3 bg-blue-600 text-white px-4 py-1 rounded text-sm">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nisi consequuntur praesentium eum! Dolorem reprehenderit ipsa cumque architecto nobis dolor quibusdam doloremque voluptates, iusto sed aliquam eligendi quo iste, sit culpa!</button>
            </div>
        </div>
    </div>
</body>
</html>
