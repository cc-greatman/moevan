<!DOCTYPE html>
<html lang="en">
  <head>
      <base href="/public">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./backend/css3/output.css" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("../frontend/fav/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("../frontend/fav/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("../frontend/fav/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("../frontend/fav/site.webmanifest") }}">
    <!-- Add this to the <head> of your HTML file -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap"
    />

    <style>
      body,
      html {
        margin: 0;
        padding: 0;
      }

      p {
          width: 350px;
      }

    </style>

    <title>Transaction Processing - Moevan</title>
  </head>
  <body class="md:m-6 m-2 overflow-x-hidden">
    <div class="flex min-h-screen items-center justify-center bg-gray-100">
      <div class="rounded-lg bg-gray
      -50 px-16 py-14">
        <div class="flex justify-center">
          <div class="rounded-full bg-yellow-200 p-6">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-yellow-500 p-4">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-white animate-spin">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>
        <h3 class="my-4 text-center text-3xl font-bold text-gray-700">Transaction Processing</h3>
        <p class="text-center font-normal text-gray-600">{{ session()->get('warning') }}</p>
        <a href="{{ route('user.dashboard') }}" class="mx-auto mt-10 block rounded-xl border-4 border-transparent bg-blue-990 px-6 py-3 text-center text-base font-medium text-white outline-8 hover:outline hover:duration-300">Thank You</a>
      </div>
    </div>
  </body>
</html>
