<script>
  document.addEventListener('DOMContentLoaded', () => {
      const menuBar = document.querySelector(".bars");

      if (menuBar) {
        menuBar.addEventListener('click', () => {
          const menusElement = document.querySelector(".menus");

          if (menusElement) {
            // Toggle the display property
            menusElement.style.display = (menusElement.style.display === 'none' || menusElement.style.display === '') ? 'flex' : 'none';
          }
        });
      }
    });

    setInterval(() => {
    fetch('/refresh-csrf')
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="_token"]').value = data.csrf_token;
        });
    }, 600000);
</script>

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

  </body>
</html>
