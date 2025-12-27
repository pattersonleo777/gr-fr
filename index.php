<?php session_start(); $isLoggedIn = isset($_SESSION["user_id"]); $username = $isLoggedIn ? $_SESSION["username"] : ""; ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantasy Rally App - Gods Rods</title>
    <meta name="description" content="Premium performance racing parts and 3D rally design workspace">
    <script src="https://cdn.tailwindcss.com/3.4.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        
        .font-inter { font-family: 'Inter', sans-serif; }
        
        #rallyHeader {
            min-height: 60px;
            height: 80px;
            user-select: none;
            transition: height 0.3s ease-out;
        }
        
        #resizeHandle { cursor: ns-resize; }
        #rallySidebar { transition: width 0.3s ease, min-width 0.3s ease; }
        
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in { animation: fadeIn 0.5s ease-in; }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            padding: 2rem;
            border-radius: 1rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #374151;
        }
        
        .status-message {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-left: 3px solid;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .status-success { border-color: #10b981; color: #10b981; }
        .status-error { border-color: #ef4444; color: #ef4444; }
        .status-info { border-color: #3b82f6; color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-900 font-inter p-4 sm:p-6 md:p-8 min-h-screen flex justify-center">
<header class="w-full p-4 bg-gray-900 border-b border-gray-800 flex justify-between items-center sticky top-0 z-50"> 
    <div class="flex items-center gap-4"> 
        <span class="text-cyan-400 font-bold text-xl">FANTASY RALLY</span> 
    </div> 
    <div class="flex items-center gap-6"> 
        <a href="index.php" class="text-gray-300 hover:text-white text-sm">Home</a> 
        <?php if($isLoggedIn): ?> 
            <a href="profile.php" class="text-gray-300 hover:text-white text-sm">My Profile</a> 
            <div class="flex items-center gap-2 border-l border-gray-700 pl-6"> 
                <span class="text-xs text-gray-400">Logged in as:</span> 
                <span class="text-sm font-semibold text-fuchsia-400"><?php echo htmlspecialchars($username); ?></span> 
            </div> 
        <?php endif; ?> 
    </div> 
</header>
    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold text-white mb-6">Login to Fantasy Rally</h2>
            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Username or Email</label>
                    <input type="text" id="loginUsername" required
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Password</label>
                    <input type="password" id="loginPassword" required
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div id="loginError" class="text-red-400 text-sm hidden"></div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded transition">
                        Login
                    </button>
                    <button type="button" onclick="closeModal('loginModal')" 
                            class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded transition">
                        Cancel
                    </button>
                </div>
                <p class="text-center text-gray-400 text-sm">
                    Don't have an account? 
                    <a href="#" onclick="switchModal('loginModal', 'registerModal')" class="text-cyan-400 hover:text-cyan-300">Register</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold text-white mb-6">Register for Fantasy Rally</h2>
            <form id="registerForm" class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Username</label>
                    <input type="text" id="regUsername" required
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Email</label>
                    <input type="email" id="regEmail" required
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Display Name</label>
                    <input type="text" id="regDisplayName"
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Password (min 8 characters)</label>
                    <input type="password" id="regPassword" required minlength="8"
                           class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                </div>
                <div id="registerError" class="text-red-400 text-sm hidden"></div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-3 bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-bold rounded transition">
                        Register
                    </button>
                    <button type="button" onclick="closeModal('registerModal')" 
                            class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded transition">
                        Cancel
                    </button>
                </div>
                <p class="text-center text-gray-400 text-sm">
                    Already have an account? 
                    <a href="#" onclick="switchModal('registerModal', 'loginModal')" class="text-cyan-400 hover:text-cyan-300">Login</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-white text-xl">Loading Fantasy Rally...</p>
            <p id="loadingStatus" class="text-gray-400 text-sm mt-2">Initializing...</p>
        </div>
    </div>

    <!-- Application Container -->
    <div id="appContainer" class="w-full max-w-6xl">
        <!-- Home View -->
        <div id="homeView" class="w-full space-y-6">
            <!-- Auth Buttons -->
            <div id="authButtons" class="<?php echo $isLoggedIn ? 'hidden' : ''; ?> flex justify-end gap-3 mb-4">
                <button onclick="openModal('loginModal')" class="px-6 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg transition">
                    Login
                </button>
                <button onclick="openModal('registerModal')" class="px-6 py-2 bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold rounded-lg transition">
                    Register
                </button>
            </div>
            
            <div id="userInfo" class="<?php echo $isLoggedIn ? '' : 'hidden'; ?> flex justify-end gap-4 mb-4 text-white">
                <span>Welcome, <strong id="displayUsername"><?php echo htmlspecialchars($username); ?></strong>!</span>
                <span class="text-green-400">$<span id="headerCashBalance">0</span></span>
                <button onclick="location.href='logout.php'" class="px-4 py-1 bg-red-600 hover:bg-red-700 rounded transition text-sm">Logout</button>
            </div>

            <!-- Banner 1: Gods Rods -->
            <div class="relative overflow-hidden rounded-2xl shadow-2xl h-80 sm:h-96 md:h-[480px] bg-gradient-to-br from-red-900 via-gray-900 to-black">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center text-white p-4 text-center">
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black uppercase tracking-wider mb-2 drop-shadow-lg">
                        Gods Rods
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl font-light mb-8 drop-shadow-md">
                        Premium Performance Racing Parts
                    </p>
                    <a href="#" class="inline-block px-8 py-3 bg-white text-gray-900 font-bold uppercase rounded-full shadow-lg transition duration-300 hover:bg-gray-200 text-sm sm:text-base">
                        Shop Now
                    </a>
                </div>
            </div>
            
            <!-- Banner 2: Fantasy Rally -->
            <div id="fantasyRallyBanner" class="relative overflow-hidden rounded-2xl shadow-2xl h-80 sm:h-96 md:h-[480px] bg-gray-900">
                <div id="canvasContainer" class="absolute inset-0 z-0">
                    <canvas id="rally3DCanvas" class="w-full h-full"></canvas>
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center text-white p-4 text-center z-10">
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black uppercase tracking-wider mb-2 drop-shadow-lg">
                        Fantasy Rally
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl font-light mb-8 drop-shadow-md">
                        Ultimate Racing Experience
                    </p>
                    <button id="enterRallyButton" class="inline-block px-8 py-3 bg-white text-gray-900 font-bold uppercase rounded-full shadow-lg transition duration-300 hover:bg-gray-200 text-sm sm:text-base">
                        Enter Rally Hub
                    </button>
                </div>
            </div>
            
            <!-- 3D Explanation -->
            <div class="bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-xl text-gray-200 border border-gray-700">
                <h2 class="text-2xl sm:text-3xl font-bold mb-4 text-cyan-400">3D Model Dissection Theory</h2>
                <p class="text-base sm:text-lg mb-4 leading-relaxed">
                    The rotating model above demonstrates real-time 3D rendering using Three.js. Achieving a truly dissectible car model requires converting 2D images to 3D geometry and then slicing that geometry into separate components.
                </p>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-white">1. 2D to 3D Conversion 🚗</h3>
                        <p class="text-sm sm:text-base mb-3 text-gray-300">
                            Use AI Image-to-3D generators (Hyper3D, Meshy.ai) to create initial 3D meshes from images. For detailed internal components, professional 3D software like Blender is recommended.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-white">2. 3D Model Dissection 🛠️</h3>
                        <p class="text-sm sm:text-base mb-3 text-gray-300">
                            Use Blender's Bisect Tool or Boolean operations to split the mesh into separate parts. These components can then be animated to reveal internal structure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rally Hub View (Initially hidden) -->
        <div id="rallyView" class="hidden w-full h-[85vh] flex flex-col overflow-hidden rounded-2xl shadow-2xl bg-gray-800">
<div class="p-4 bg-gray-800 rounded-lg mb-4"> 
    <h3 class="text-white mb-2">Upload Asset</h3> 
    <input type="file" id="userFileUpload" class="text-xs text-gray-400 mb-2"> 
    <button onclick="uploadFile()" class="w-full py-1 bg-green-600 text-white rounded text-xs">Upload</button> 
</div>
<div class="p-4 bg-gray-800 rounded-lg mb-4"> 
    <h3 class="text-white mb-2">Upload Asset</h3> 
    <input type="file" id="userFileUpload" class="text-xs text-gray-400 mb-2"> 
    <button onclick="uploadFile()" class="w-full py-1 bg-green-600 text-white rounded text-xs">Upload</button> 
</div>
            <!-- Header (existing code continues...) -->
            <div id="rallyHeader" class="bg-gray-900 text-white flex-shrink-0 relative">
                <div class="p-3 flex justify-between items-center h-full">
                    <div class="flex items-center space-x-4">
                        <div class="text-xl font-mono text-green-400">$<span id="cashBalance">0</span></div>
                        <div class="text-lg text-gray-300 font-semibold hidden sm:block">
                            User: <span id="userNamePlaceholder">Guest</span>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-bold uppercase tracking-wider text-fuchsia-400 flex items-center">
                        <button id="toggleSidebar" class="p-2 mr-2 sm:mr-3 bg-gray-700 hover:bg-gray-600 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <span class="hidden sm:inline">FANTASY RALLY HUB</span>
                    </div>
                    <div class="flex items-center text-lg text-yellow-400">
                        <span id="friendsOnline">0</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 ml-1" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-4.42 3.59-8 8-8 4.41 0 8 3.58 8 8 0 4.41-3.59 8-8 8z"/>
                        </svg>
                    </div>
                </div>
                <div id="resizeHandle" class="absolute bottom-0 left-0 w-full h-2 bg-gray-700 hover:bg-cyan-500 transition"></div>
            </div>
            
            <!-- Main Content (same as before) -->
            <div class="flex flex-1 overflow-hidden">
                <div id="rallySidebar" class="bg-gray-700 min-w-0 w-0 md:w-1/4 max-w-xs flex-shrink-0 text-white overflow-y-auto p-0 md:p-4">
                    <h3 class="text-xl font-bold mb-4 text-cyan-400 border-b border-gray-600 pb-2 hidden md:block">AI Model Integration</h3>
                    <div class="space-y-4">
                        <div class="p-3 bg-gray-800 rounded-lg">
                            <h4 class="font-bold text-sm mb-2 text-gray-300">System Status</h4>
                            <div id="statusConsole" class="max-h-32 overflow-y-auto text-xs">
                                <div class="status-message status-info">System initialized</div>
                            </div>
                        </div>
                        
                        <div class="p-3 bg-gray-600 rounded-lg">
                            <label for="addUrl" class="block text-sm font-medium mb-1">Load 3D Model:</label>
                            <input type="url" id="addUrl" placeholder="https://example.com/model.glb" 
                                   class="w-full p-2 rounded bg-gray-700 border border-gray-500 text-white text-sm">
                            <button id="addAssetBtn" class="w-full mt-2 py-2 bg-cyan-600 hover:bg-cyan-700 rounded font-semibold transition text-sm">
                                Load Model
                            </button>
                        </div>
                        
                        <div class="p-3 bg-gray-600 rounded-lg">
                            <h4 class="font-bold text-sm mb-2">Quick Actions</h4>
                            <div class="space-y-2">
                                <button id="loadSampleCar" class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded text-sm">
                                    🚗 Load Sample Car
                                </button>
                                <button id="clearModel" class="w-full py-2 bg-red-600 hover:bg-red-700 rounded text-sm">
                                    🗑️ Clear Model
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 bg-gray-900 relative p-2 sm:p-4 flex flex-col">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg sm:text-xl text-white font-semibold">3D Rally Design Workspace</h2>
                        <div id="canvasLoading" class="hidden">
                            <div class="loading-spinner w-6 h-6"></div>
                        </div>
                    </div>
                    
                    <canvas id="mainRallyCanvas" class="flex-1 bg-gray-800 border-2 border-gray-600 rounded-lg"></canvas>
                    
                    <div class="mt-3 flex justify-between items-center flex-wrap gap-2">
                        <div class="flex gap-2">
                            <button id="copyCanvasBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                                📋 Copy
                            </button>
                            <button id="resetViewBtn" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg">
                                🔄 Reset
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">Drag • Scroll</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/auth.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
