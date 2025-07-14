<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .zoom-in {
      animation: zoomIn 0.4s ease-out;
    }
    @keyframes zoomIn {
      from { transform: scale(0.9); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg zoom-in">
    <h2 class="text-2xl font-bold text-center text-blue-700 mb-4">Lupa Password</h2>
    <p class="text-center text-sm text-gray-600 mb-6">Hubungi admin atau guru untuk reset password.</p>

    <div class="text-center">
      <a href="login.php" class="text-sm text-blue-500 hover:underline">← Kembali ke Login</a>
    </div>
  </div>
</body>
</html>
