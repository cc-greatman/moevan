@include('frontend.partials.head')

@include('frontend.partials.navbar')

    <div class="">
      <div
        class="flex hero bg-white bg-blend-multiply bg-cover bg-center bg-no-repeat pb-16 "
      >
        <div class="lg:text-8xl md:text-6xl text-5xl font-bold leading-snug md:ml-20 ml-4 md:mt-24 mt-8">
          <p class="text-white leading-snug ">
            One <br />
            Platform <br />
            <span class="text-[#001D82]">For All Your <br /> </span> Needs.
          </p>
          <div class="cont">
              <div>
                @if(!Auth::guard('web')->user())
                <a href="{{ route('user.login.show') }}" class="log">Login</a>
                <a href="{{ route('user.register.show') }}" class="reg">Sign up</a>
                @else
                <a href="{{ route('user.dashboard') }}" class="log">Return to Dashboard</a>
                @endif
              </div>
          </div>
        </div>
      </div>

      <div id="about" class="se-hero bg-cover bg-center bg-no-repeat" style="background-image: url(../frontend/img/cute-african-couple-sitting-park-watching-funny-video-together\ 1.png);">
        <div class="flex flex-col tracking-tight md:w-38 ml-auto md:pb-32 pb-12 pt-11" style="background-image: linear-gradient(to top, rgba(0, 29, 130, 0.75), rgba(0, 29, 130, 0.04));">
          <p class="text-[#001D82] text-5xl font-extrabold pl-6 mb-5">
            Why Choose Us?
          </p>
          <div class="text-white text-xl font-bold md:pl-6 md:pr-4 pl-4 pr-3">
            <p>
              We provide you with instant airtime recharge, cable tv subscription  (dstv, gotv, and more), electricity bill payment and much more.
            </p>
            <br />

            <p>
              Yes! Navigating certain challenges may be tough, but we make sure
              payments do not have to be a part of those challenges, we simplify
              the process, allowing you make payments for your preferred
              services in a few clicks. With Moevan, you can effortlessly settle
              payments for the services you enjoy, all from the ease of your
              comfort.
            </p>
          </div>
        </div>
      </div>

      <div id="services" class="bg-[#000E3E]">
        <div class="flex justify-center pt-16">
          <p
            class="text-white md:text-5xl text-3xl font-extrabold bg-[#001D82] mb-10 md:px-10 px-6 md:py-3 py-2 rounded-xl"
          >
            Our Services
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 lg:mx-36 md:mx-11 mx-3 pb-10">


          <div class="bg-white text-center md:px-14 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/connection.png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <a href="{{ route('user.data.show') }}"><p class="font-extrabold text-center text-2xl opacity-95">
              Data Plan <br />
              Subscription
            </p></a>
            
          </div>
        
          <div class="bg-white text-center md:px-14 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/Call.png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <a href={{ route('user.airtime.show') }}""><p class="font-extrabold text-center text-2xl opacity-95">
              Airtime <br />
              Top-up
            </p></a>
            
          </div>
        
          <div class="bg-white text-center md:px-11 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/Tv.png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <a href="{{ route('user.utility.cable.show') }}"><p class="font-extrabold text-center text-2xl opacity-95">
              Cable TV <br />
              Subscription
            </p></a>
            
          </div>
        
          <div class="bg-white text-center md:px-11 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/Result.png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <p class="font-extrabold text-center text-2xl opacity-95">
              Check <br />
              Results
            </p>
          </div>
        
          <div class="bg-white text-center md:px-11 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/Frame 1.png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <a href="{{ route('user.utility.electric.show') }}"><p class="font-extrabold text-center text-2xl opacity-95">
              Bills <br />
              Payment
            </p></a>
            
          </div>
        
          <div class="bg-white text-center md:px-11 md:py-3 px-10 py-2 rounded-lg">
            <p class="pb-7">
              <img src="{{ asset("../frontend/img/our-service-img/Tv (1).png") }}" alt="" class="border-4 border-[#001D82] rounded-full mx-auto w-20 p-2" />
            </p>
            <p class="font-extrabold text-center text-2xl opacity-95">
              Bulk SMS <br />
              Services
            </p>
          </div>
        </div>
        
      </div>

      <div class=" bg-cover bg-center bg-no-repeat" style="background-image: url(../frontend/img/handsome-man-spending-time-outdoors-using-modern-smartphone\ 1.png);
    ">
        <div
          class="flex flex-col justify-left items-left tracking-tight lg:w-42 pt-11 pb-12" style="background-image: linear-gradient(to bottom, rgba(16, 42, 136, 0), rgba(255, 255, 255, 0.85));"
        >
          <p class="text-white text-5xl font-extrabold mb-5 md:pl-14 pl-4">
            Become An Agent!
          </p>
          <div class="text-[#001560] text-xl font-bold tracking-normal md:pl-12 pl-4 md:pr-6 pr-3">
            <p>
              <p>Become An Agent</p>
              Become part of our exceptional entrepreneur network by partnering with Moevan. Integrate the Moevan 'easy-payments' experience into your network, earning commissions for each transaction.
              Our referral program provides top incentives, fostering entrepreneurial and managerial skill acquisition, promoting growth and development, empowering interested individuals, students on higher learning campuses and youths in diaspora, and advancing technology through ICT tools in society
            </p>
          </div>
        </div>
      </div>


      
        <div class="bg-[#000E3E] text-white relative">

          <div class="text-center text-5xl font-bold mb-10 pt-10">Testimonials</div>
          
          <div class="flex items-center justify-center lg:mx-28 md:mx-16 mx-0 pb-20 relative">
              <button class="absolute sm:-mt-10 -mt-14 left-0 top-1/2 transform -translate-y-1/2 bg-transparent border-none" onclick="prevTestimonial()">
                  <img src="../frontend/img/Chevron Left.png" alt="" class="lg:h-24 md:h-16 h-8">
              </button>
              <p id="testimonial" class="text-center flex-initial sm:w-[600px] w-298 lg:text-3xl md:text-2xl text-lg">A very reliable and affordable platform for data/airtime and bills payment. Never had a downtime for once. - Justin</p>
              <button class="absolute sm:-mt-10 -mt-14 right-0 top-1/2 transform -translate-y-1/2 bg-transparent border-none" onclick="nextTestimonial()">
                  <img src="../frontend/img/Chevron Left (1).png" alt="" class="lg:h-24 md:h-20 h-8">
              </button>
          </div>
        </div>
      
      <script>
          var testimonials = [
              "A very reliable and affordable platform for data/airtime and bills payment. Never had a downtime for once. - Justin",
              "I must say moevan.com is very reliable and fast i highly recommend 🤌 - Didi",
              "Oh... It's been a thrilling experience ever since I joined hands with your company as a reseller it's been wonderful. Unlike the previous website this one is working fine I haven't had any complaint from my customers and it's seamlessly smooth using the website. Kudos to moevan enterprise - Ebube /@swiftconnect",
              "Glad to say I won your 10GB giveaway and it was awarded through the website. Easy to navigate, swift airtime top-ups and data recharges. Y’all should try it out. - Dior",
              "Customer service is top notch. I love the swift response at every point in time - Confidence.",
              // Add more testimonials as needed
          ];
      
          var currentTestimonialIndex = 0;
          var testimonialElement = document.getElementById('testimonial');
      
          function showTestimonial(index) {
              testimonialElement.innerText = testimonials[index];
          }
      
          function prevTestimonial() {
              if (currentTestimonialIndex > 0) {
                  currentTestimonialIndex--;
              } else {
                  currentTestimonialIndex = testimonials.length - 1;
              }
              showTestimonial(currentTestimonialIndex);
          }
      
          function nextTestimonial() {
              if (currentTestimonialIndex < testimonials.length - 1) {
                  currentTestimonialIndex++;
              } else {
                  currentTestimonialIndex = 0;
              }
              showTestimonial(currentTestimonialIndex);
          }
      
          // Show the initial testimonial
          showTestimonial(currentTestimonialIndex);
      </script>


        <div id="pricing">
          <div class="text-center text-blue-900 text-3xl md:text-4xl font-extrabold my-12">Affordable Data Plans And Prices</div>

   


          <div class="grid sm:grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-2 flex-wrap justify-evenly rounded-lg mb-9">

          <div class=" flex flex-col mx-2 mb-4">
              <img src="{{ asset("../frontend/img/pngwing2.png") }}" alt="" class="bg-gradient-to-tr from-[#10A703C2] to-[#074B01] object-contain mb-4 pr-1 pl-1 pb-8 rounded-xl h-300 ">
              <div class="bg-gradient-to-tr from-[#10A703C2] to-[#074B01] text-xl font-medium text-center rounded-xl">
                {!! $etisalat->rolling_message !!}
                <div class="bg-white text-blue-950 text-center text-2xl font-extrabold rounded-3xl py-3 ml-9 mr-9 mb-12"><a href="{{ route('user.data.show') }}">Order Now</a></div>
              </div>
          </div>

          <div class="flex flex-col mx-2 mb-4">

            <img src="{{ asset("../frontend/img/Airtel.png") }}" alt="" class="bg-[#CF0909] object-contain px-24 lg:px-24 md:px-2 py-4 mb-4 rounded-xl h-300">
            
          
            <div class="bg-[#CF0909] text-xl font-medium pb-1 rounded-xl">
              {!! $airtel->rolling_message !!}
              <div class="bg-white text-blue-950 text-center text-2xl font-extrabold rounded-3xl py-3 ml-9 mr-9 mb-12"><a href="{{ route('user.data.show') }}">Order Now</a></div>
            </div>
          </div>

          <div class=" flex flex-col mx-2 mb-4">
            <img src="{{ asset("../frontend/img/globacom-limited 1.png") }}" alt="" class="bg-[#2DA703] object-contain px-16 py-7 mb-4 rounded-xl h-300 ">
            <div class="bg-[#2DA703] text-xl font-medium pb-1 rounded-xl">
             {!! $glo->rolling_message !!}
              <div class="bg-white text-blue-950 text-center text-2xl font-extrabold rounded-3xl py-3 ml-9 mr-9 mb-12"><a href="{{ route('user.data.show') }}">Order Now</a></div>
            </div>
          </div>

          <div class=" flex flex-col mx-2 mb-4">
            <img src="{{ asset("../frontend/img/MTN-Logo 1.png") }}" alt="" class="bg-[#CFB009] px-16 sm:py-20 py-10 mb-4 rounded-xl h-300 ">
            <div class="bg-[#CFB009] text-xl font-medium pb-1 rounded-xl">
              {!! $mtn->rolling_message !!}
              <div class="bg-white text-blue-950 text-center text-2xl font-extrabold rounded-3xl py-3 ml-9 mr-9 mb-12"><a href="{{ route('user.data.show') }}">Order Now</a></div>
            </div>
          </div>

        </div>

        </div>

     
        <div class="flex flex-wrap lg:flex-nowrap bg-[#000E3E] bg-contain md:pt-48 pt-16 md:pl-24 md:pr-14 pl-10 pb-24">

            <div class="flex flex-col text-white md:mr-28">
                <p class="md:text-5xl text-3xl font-bold mb-5">Integrate Our API</p>
                <p class="md:text-3xl text-xl font-medium mb-12 ">Leverage our developer-friendly API, designed for easy integration with our website or app, allowing you to effortlessly receive payments without any associated fees</p>
                <div>
                    <p class="inline-block text-center text-blue-800 font-medium md:text-xl text-lg  rounded-2xl bg-white md:py-4 md:px-6 py-2 px-4 mb-8 ">API DOCUMENTATION</p>
                </div>
            </div>
           
            <div class="bg-white mr-3">
                <img src="{{ asset("../frontend/img/api.jpg") }}" class="api-img px-5 py-5" >
             </div>
          </div>
    </div>

@include('frontend.partials.footer')