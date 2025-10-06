<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Loader with Functions</title>
  <style>
    /* Loader Styles */
    #loader-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .loader {
      width: 60px;
      height: 60px;
      border: 6px solid #02489d;
      border-top: 6px solid #932431;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .fade-out {
      opacity: 0;
      visibility: hidden;
      transition: all 0.5s ease-out;
    }
  </style>
</head>
<body>

  <div style="padding: 30px; text-align: center;">
    <h1 style="color: #02489d;">Welcome to Tej Smart ERP</h1>
    <p style="color: #932431;">Streamlining your contractor workflow with smart solutions.</p>
    <button onclick="simulateLoading()">Simulate Load</button>
  </div>

  <script>
    function addLoader() {
      if (!document.getElementById("loader-wrapper")) {
        const loaderWrapper = document.createElement("div");
        loaderWrapper.id = "loader-wrapper";
        loaderWrapper.innerHTML = `<div class="loader"></div>`;
        document.body.appendChild(loaderWrapper);
      }
    }

    function removeLoader() {
      const loader = document.getElementById("loader-wrapper");
      if (loader) {
        loader.classList.add("fade-out");
        setTimeout(() => loader.remove(), 500); // Match transition duration
      }
    }

    // Demo function
    function simulateLoading() {
      addLoader();
      setTimeout(removeLoader, 2000); // simulate 2-second loading
    }
  </script>

</body>
</html>
