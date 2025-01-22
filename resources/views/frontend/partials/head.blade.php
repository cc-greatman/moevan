<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="/public">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="description" content="Moevan provides Instant airtime recharge, Instant Data Subscription, cable TV subscription (DStv, GOtv, and more), electricity bill payment, data plan subscription, and bulk SMS services. Simplify your payments in a few clicks.">
    <meta name="keywords" content="Moevan, airtime recharge, cable TV subscription, electricity bill payment, data plan subscription, bulk SMS services, bills payment">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("../frontend/fav/apple-touch-icon.png") }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("../frontend/fav/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("../frontend/fav/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("../frontend/fav/site.webmanifest") }}">
    <link href="{{ asset("../backend/css3/output.css") }}?v={{ time() }}" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css"
    />
    <title>{{ $pageTitle }}</title>

    <style>
        /* Hide the reCAPTCHA v3 badge */
        .grecaptcha-badge {
            display: none !important;
        }

      .custom-input::placeholder {
        padding-left: 20px;
        color: #6b7280;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 2px;
        opacity: 0.7;
      }
      .hero {
        background-image: url(../frontend/img/stable-diffu1.png);
        background-color: #001c827b;
      }

     @media screen and (min-width: 1024px) {
      .api-img {
        width: 1600px;
      height: 300px;
      }

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
          left: 0;
          top: 6rem;
          background-color: rgba(0, 29, 130, 1);
          width: 100%;
      }

      .menus li {
          margin-bottom: 1rem;
      }
    }
    
    .cont {
      justify-content: left;
      align-items: center;
      display: flex;
      text-align: center;
    }
    
    .reg,
    .log {
      cursor: pointer;
      border: 0;
      border-radius: 4px;
      font-weight: 600;
      height: 30px;
      font-size: 28px;
      width: 200px;
      padding: 10px 50px;
      box-shadow: 0 0 20px rgba(104, 85, 224, 0.2);
      transition: 0.4s;
    }
    
    .reg {
      color: white;
      background-color: rgba(0, 29, 130, 1);
    }
    
    .log {
      color: rgba(0, 29, 130, 1);
      background-color: rgba(255, 255, 255, 1);
      border: 1px solid rgba(104, 85, 224, 1);
    }
    
    .reg:hover,
    .log:hover {
      color: white;
      width:;
      box-shadow: 0 0 20px rgba(104, 85, 224, 0.6);
      background-color: rgba(0, 29, 130, 1);
    }
    
    @media (max-width: 720px) {
        .reg,
        .log {
            font-size: 18px;
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
	
  </head>
