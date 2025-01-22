<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>419 - Page Expired</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("../frontend/fav/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("../frontend/fav/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("../frontend/fav/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("../frontend/fav/site.webmanifest") }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,line-clamp"></script>
</head>
<body>
    <div class="bg-[#001d82] text-white">
        <div class="flex items-center justify-center min-h-screen px-2">
          <div class="text-center">
            <h1 class="text-9xl font-bold">419</h1>
            <p class="text-2xl font-medium mt-4">Oops! This page has expired</p>
            <p class="mt-4 mb-8">Session Expired. Please refresh the page and clear cookes and site data from you device before trying again</p>
            <div class="flex items-center justify-center px-4 gap-2">
                <a href="#" class="px-6 py-3 hover:bg-white font-bold font-semibold rounded-full bg-[#001d82] transition duration-300 ease-in-out hover:text-black text-white border border-solid border-white hover:cursor-pointer" onclick="window.history.back();">
                Go Back
                </a>
                <a href="{{ route('home') }}" class="px-6 py-3 bg-white font-bold font-semibold rounded-full hover:bg-[#001d82] transition duration-300 ease-in-out text-black hover:text-white hover:border hover:border-solid hover:border-white hover:cursor-pointer">
                    Go Home
                </a>
            </div>
          </div>
        </div>
      </div>
</body>
</html>
