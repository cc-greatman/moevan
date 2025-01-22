<div class="mt-20" style="background: linear-gradient(180deg, #000E3E 50%, #001D82 100%);">

       <div class="grid md:grid-cols-3 text-white md:mx-4 pt-20  pb-14">

        <ul class="flex flex-1 flex-col md:pl-12 pl-4 pb-6">
          <li class="pb-14"><img src="{{ asset("../frontend/img/Logo2.png") }}" style="width: 12rem;"></li>
          <li class="md:text-2xl text-xl font-medium">We offer instant recharge of Airtime, Databundle, CableTV (DStv, GOtv, & more), Electricity Bill Payment and more...</li>
        </ul>

        <ul class="flex flex-col justify-center md:items-center flex-wrap space-y-6  pl-4 pb-10">
           <li class="text-4xl font-bold">Useful Links</li>
           <li class="text-xl font-medium"><a href="{{ route('home') }}">Home</a></li>
           <li class="text-xl font-medium"><a href="#" class="scroll-link" data-id="about">About Us</a></li>
           <li class="text-xl font-medium"><a href="#" class="scroll-link" data-id="services">Services</a></li>
           <li class="text-xl font-medium"><a href="#" class="scroll-link" data-id="pricing">Pricing</a></li>
           @if (Auth::guard('web')->user())
                <li class="text-xl font-medium"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="text-xl font-medium"><a href="{{ route('user.logout') }}">Logout</a></li>
            @else
                <li class="text-xl font-medium"><a href="{{ route('user.login.show') }}">Login</a></li>
                <li class="text-xl font-medium"><a href="{{ route('user.register.show') }}">Register</a></li>
            @endif
        </ul>

        <ul class="flex flex-1 flex-col pl-4 space-y-6 ">
          <li class="text-3xl font-bold">Contact Information</li>
          <li class="text-xl font-medium"><a href="mailto:support@moevan.com">Email: support@moevan.com</a></li>
          <li class="text-xl font-medium"><a href="tel:+2349027205635">Customer Care: +234 902 720 5635</a></li>
        </ul>


      </div> 


    </div> 

    <!-- JavaScript for smooth scrolling -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Smooth scroll functionality for in-page links
            document.querySelectorAll('.scroll-link').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-id');
                    const targetElement = document.getElementById(targetId);
    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });
    
            // Check if URL contains a hash for scrolling after page load
            if (window.location.hash) {
                const targetId = window.location.hash.substring(1);
                const targetElement = document.getElementById(targetId);
    
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
    <script>
      "use strict"

      // for show password 
      let createpassword = (type, ele) => {
          document.getElementById(type).type = document.getElementById(type).type == "password" ? "text" : "password"
          let icon = ele.childNodes[0].classList
          let stringIcon = icon.toString()
          if (stringIcon.includes("ri-eye-line")) {
              ele.childNodes[0].classList.remove("ri-eye-line")
              ele.childNodes[0].classList.add("ri-eye-off-line")
          }
          else {
              ele.childNodes[0].classList.add("ri-eye-line")
              ele.childNodes[0].classList.remove("ri-eye-off-line")
          }
      }
    </script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const menuBar = document.querySelector(".bars");

        if (menuBar) {
          menuBar.addEventListener("click", () => {
            const menusElement = document.querySelector(".menus");

            if (menusElement) {
              // Toggle the display property
              menusElement.style.display =
                menusElement.style.display === "none" ||
                menusElement.style.display === ""
                  ? "flex"
                  : "none";
            }
          });
        }
      });
    </script>


  </body>
  <footer>
      <script  type="text/javascript">
          var config = {
            phone :" 2349027205635",
            call :"Chat With Us",
            position :"ww-right",
            size : "ww-normal",
            text : "Hi there,I have an issue with",
            type: "ww-extended",
            brand: "Moevan Enterprises",
            subtitle: "",
            welcome: "Hi thereHow can I be of help to you"
          };
          var proto = document.location.protocol, host = "cloudfront.net", url = proto + "//d3kzab8jj16n2f." + host;
            var s = document.createElement("script"); s.type = "text/javascript"; s.async = true; s.src = url + "/v2/main.js";
        
            s.onload = function () { tmWidgetInit(config) };
            var x = document.getElementsByTagName("script")[0]; x.parentNode.insertBefore(s, x);
        </script>
  </footer>
</html>
