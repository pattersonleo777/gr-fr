<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - Fantasy Rally</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white">
    <header class="p-4 border-b border-gray-800 flex justify-between items-center">
        <h1 class="text-xl font-bold">Fantasy Rally</h1>
        <nav>
            <a href="index.php" class="px-4 py-2 hover:text-cyan-400">Dashboard</a>
            <a href="logout.php" class="px-4 py-2 bg-red-600 rounded">Logout</a>
        </nav>
    </header>
    <main class="p-8">
        <h2 class="text-3xl mb-6">Profile: <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <h3 class="text-xl mb-4">Your Uploaded Assets</h3>
        <div id="userAssets" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            </div>
    </main>
    <script>
        async function loadUserAssets() {
            const res = await fetch('api/list_assets.php');
            const assets = await res.json();
            const container = document.getElementById('userAssets');
            assets.forEach(asset => {
                const div = document.createElement('div');
                div.className = "bg-gray-900 p-2 rounded border border-gray-700";
                div.innerHTML = `<img src="${asset.path}" class="w-full h-32 object-cover rounded mb-2">
                                <p class="text-xs truncate">${asset.path.split('/').pop()}</p><button onclick="deleteAsset('${'asset.path'}')" class="mt-2 w-full py-1 bg-red-900 hover:bg-red-600 text-[10px] rounded">Delete</button>`;
                container.appendChild(div);
            });
        }
        async function deleteAsset(path) { 
            if(!confirm("Delete this asset?")) return; 
            const res = await fetch("api/delete_asset.php", { 
                method: "POST", 
                body: JSON.stringify({ path }) 
            }); 
            const result = await res.json(); 
            if(result.success) location.reload(); 
            else alert("Error: " + result.error); 
        }
        loadUserAssets();
    </script>
</body>
</html>
