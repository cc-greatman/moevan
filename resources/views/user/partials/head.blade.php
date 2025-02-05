<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="/public">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if(!Route::is('user.transactions.data.show'))
        <link href="{{ asset('../backend/css3/output.css') }}?v={{ time() }}" rel="stylesheet" />
    @endif

    @if(Route::is('user.transactions.data.show') || Route::is('user.receipt.data.show') || Route::is('user.data.search.transactions'))
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("../frontend/fav/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("../frontend/fav/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("../frontend/fav/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("../frontend/fav/site.webmanifest") }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=content_copy" />
    
    <title>{{ $pageTitle }}</title>
    <!-- CSS Styling for Modal -->
    <style>

    /* CSS */
    .button-22 {
      align-items: center;
      appearance: button;
      background-color: #0276FF;
      border-radius: 8px;
      border-style: none;
      box-shadow: rgba(255, 255, 255, 0.26) 0 1px 2px inset;
      box-sizing: border-box;
      color: #fff;
      cursor: pointer;
      font-family: "RM Neue",sans-serif;
      font-size: 100%;
      line-height: 1.15;
      margin: 0;
      padding: 10px 21px;
      text-align: center;
      text-transform: none;
      transition: color .13s ease-in-out,background .13s ease-in-out,opacity .13s ease-in-out,box-shadow .13s ease-in-out;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
    }

    .button-22:active {
      background-color: #006AE8;
    }

    .button-22:hover {
      background-color: #1C84FF;
    }

    .button-54 {
      font-family: "Open Sans", sans-serif;
      font-size: 16px;
      letter-spacing: 2px;
      text-decoration: none;
      text-transform: uppercase;
      color: #fff;
      cursor: pointer;
      border: 3px solid #fff;
      padding: 0.5em 0.5em;
      margin-top: 10px;
      box-shadow: 1px 1px 0px 0px, 2px 2px 0px 0px, 3px 3px 0px 0px, 4px 4px 0px 0px, 5px 5px 0px 0px;
      position: relative;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
    }

    .button-54:active {
      box-shadow: 0px 0px 0px 0px;
      top: 5px;
      left: 5px;
    }

    @media (min-width: 768px) {
      .button-54 {
        padding: 0.25em 0.75em;
      }
    }

    /* Hide the reCAPTCHA v3 badge */
        .grecaptcha-badge {
            display: none !important;
        }

        .container {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
          z-index: 100; /* Ensure it's above other content */
        }

        .cookiesContent {
          width: 420px;
          border-radius: 20px;
          position: fixed;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          display: flex;
          flex-direction: column;
          align-items: center;
          background-color: #fff;
          color: #000;
          text-align: center;
          border-radius: 20px;
          padding: 30px 30px 50px;
        }

        @media screen and (max-width: 767px) {
            .cookiesContent {
                width: 370px;
            }
        }

        .cookiesContents {
          width: 370px;
          border-radius: 20px;
          position: fixed;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          display: flex;
          flex-direction: column;
          align-items: center;
          background-color: #fff;
          color: #000;
          text-align: center;
          border-radius: 20px;
          padding: 30px 30px 70px;
        }

        .cookiesContent button.close {
          width: 30px;
          font-size: 20px;
          color: #c0c5cb;
          align-self: flex-end;
          background-color: transparent;
          border: none;
          margin-bottom: 10px;
        }

        .cookiesContent img,
        .cookiesContents img {
          width: 82px;
          margin-bottom: 15px;
        }

        .cookiesContent p {
          margin-bottom: 30px;
          font-size: 18px;
        }
        .cookiesContents p {
            font-size: 15px;
            margin-bottom: 40px;
        }

        .cookiesContent button.accept,
        .cookiesContent a.accept,
        .cookiesContent button.decline,
        .cookiesContents button.accept {
          background-color: #001D82;
          border: none;
          border-radius: 5px;
          width: 200px;
          padding: 14px;
          font-size: 16px;
          color: white;
        }

        .cookiesContent button.decline {
            background-color: red;
        }

        .cookiesContents button.accept{
            font-size: 14px;
        }
    </style>
    <style>
        .alert {
          position: relative;
          top: 10;
          left: 0;
          width: auto;
          height: auto;
          padding: 10px;
          margin: 10px;
          line-height: 1.8;
          border-radius: 5px;
          cursor: hand;
          cursor: pointer;
          font-family: sans-serif;
          font-weight: 400;
        }

        .alertCheckbox {
          display: none;
        }

        :checked + .alert {
          display: none;
        }

        .alertText {
          display: table;
          margin: 0 auto;
          text-align: center;
          font-size: 16px;
        }

        .alertClose {
          float: right;
          padding-top: 5px;
          font-size: 10px;
        }

        .clear {
          clear: both;
        }

        .error {
          background-color: #FEE;
          border: 1px solid #EDD;
          color: #A66;
        }
    </style>
    <style>


        .flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
      }

      .broad {
          flex-shrink: 0;
        border-radius: 35px;
        box-shadow: 0px -2px 6px 7px rgba(0, 0, 0, 0.25);
        margin-top: 100px;
      }

      .flex-item {
        flex: 0 1 calc(33.3333% - 10px);
        background-color: white;
        padding: 25px;
        text-align: center;
        box-sizing: border-box;
      }



      @media (max-width: 768px) {
        .flex-item {
          flex: 0 1 calc(50% - 10px);
          padding: 11px;
        }
      }

      .place-style::placeholder {
        padding-left: 3rem;
        font-size: 1.4rem;
        opacity: 0.7;
      }

      .place-style {
        padding-left: 1rem;
      }

      .bars {
        display: block;
          color: white;
          font-size: 30px;
      }
      .menus {
        display: flex;
      }
      @media (max-width: 1000px) {
        .bars {
          display: block;
          color: white;
          font-size: 30px;
        }

        .menus {
          position: absolute;
          display: none;
          flex-direction: column;
          flex-wrap: wrap;
          left: 0;
          top: 6rem;
          background-color: rgba(0, 29, 130, 1);
          width: 100%;
          height: 24%;
        }

        .menus li {
          margin-bottom: 1rem;
        }
      }
    </style>
    <style>
      .tab li:first-child:hover {
        background-color: #0357EE !important;
      }

      .tab li:nth-child(2):hover {
        background-color: #AE328E !important;
      }

      .tab li:nth-child(3):hover {
        background-color: rgba(69, 0, 156, 1) !important;
      }

      .tab li:nth-child(4):hover {
        background-color: rgba(187, 8, 8, 1) !important;
      }

      .tab li.active {
        background-color: blue !important;
        color: white !important;
      }

      /* General Styles */
        .tabcontent {
            display: none; /* Hide all tab contents by default */
        }

        /* Active tab content display */
        .tabcontent.active {
            display: block; /* Show active tab content */
        }

        /* Mobile View */
        @media (max-width: 640px) {
            .tab {
                flex-direction: column; /* Stack tabs vertically */
            }

            .tabcontent {
                display: none; /* Hide all tab contents by default */
                margin-top: 0; /* Remove margin */
            }

            .tablinks.active + .tabcontent {
                display: block; /* Show the content of the active tab */
                margin-top: 0; /* Ensure content is directly under the tab */
            }

            /* Adjust tab links if needed */
            .tablinks {
                width: 100%; /* Make tabs full width */
                text-align: center; /* Center the tab text */
            }
        }

      .board {
        flex-shrink: 0;
        border-radius: 35px;
        box-shadow: 0px -2px 6px 7px rgba(0, 0, 0, 0.25);
        margin-top: -50px;
      }
      .board-two {
        flex-shrink: 0;
        border-radius: 35px;
        box-shadow: 0px -2px 6px 7px rgba(0, 0, 0, 0.25);
        padding-bottom: 4rem;
        margin-bottom: 4rem;
      }

      .text-box {
        color: #fff;
        text-align: center;
        font-family: Lexend;
        font-size: 24px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        background: #000e3e;
        box-shadow: 0px 0px 5px 5px rgba(0, 0, 0, 0.25);
      }
      .icon-width {
        width: 3.4rem;
      }

      .menus {
        display: flex;
      }
      @media (max-width: 1000px) {
        .bars {
          display: block;
          color: white;
          font-size: 30px;
        }

        .menus {
          position: absolute;
          display: none;
          flex-direction: column;
          flex-wrap: wrap;
          left: 0;
          top: 7rem;
          background-color: rgba(0, 29, 130, 1);

          width: 100%;
          height: 24%;
        }

        .menus li {
          margin-bottom: 1rem;
        }
      }

    </style>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha_v3.siteKey') }}"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("bitForm").submit();
        }
    </script>


    <script
      type="text/javascript"
      src="https://d3mkw6s8thqya7.cloudfront.net/integration-plugin.js"
      id="aisensy-wa-widget"
      widget-id="MYoz83"
    >
    </script>
    <style>
      @media print {
        body * {
          visibility: hidden;
        }
        #invoice-to-print, #invoice-to-print * {
          visibility: visible;
        }
        #invoice-to-print {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
        }
      }
    </style>

  </head>
