<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <style>
    body {
      background-color: #ff6680;
      /* Similar to the pink background */
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      text-align: center;
      padding: 20px;
      max-width: 400px;
      /* For responsiveness */
    }

    h1 {
      margin-bottom: 20px;
    }

    .button-container {
      display: flex;
      justify-content: space-between;
    }

    button {
      width: 45%;
      padding: 10px;
      font-size: 16px;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .login-btn {
      background-color: #4CAF50;
      /* Green */
    }

    .exit-btn {
      background-color: #f44336;
      /* Red */
    }

    .input-fields {
      margin: 15px 0;
      width: 100%;

    }

    .input-fields input {
      width: 100%;
      padding: 10px;
      outline: none;
      border: none;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <h1>Please Login</h1>
    <form>
      <div>
        <div class="input-fields">
          <input type="text" id="username" placeholder="Username" required>
        </div>
        <div class="input-fields">
          <input type="password" id="password" placeholder="Password" required>
        </div>
        <div class="button-container">
          <button type="submit" id="login_btn" class="login-btn">Login</button>
          <button type="button" class="exit-btn">Exit</button>
        </div>
      </div>
    </form>
  </div>
  <script type="module" src="./urls.js"></script>
  <script type="module">
    import { LOGIN_URL } from './urls.js';
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const login_btn = document.getElementById('login_btn');
    // const URL = 'http://localhost:8080/workspace/techspheresoft/rajaranimohini_gaming/be/login.php';
    login_btn.addEventListener('click', async (e) => {
      e.preventDefault();
      if (username.value === '' || password.value === '') {
        alert("Username or Password is missing!")
      } else {
        let LoginObj = {}
        LoginObj.email = username.value
        LoginObj.password = password.value
        // window.location = './'
        try {
          const res = await fetch(LOGIN_URL, {
            mode: 'no-cors',
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(LoginObj)
          });
          const response = await res.json();
          if (res.ok) {
            //Api called Successfull
            console.log('>>>Login', response);
            
            localStorage.setItem("loginInfo",JSON.stringify(response.data))
            window.location = './'
          } else {
            alert(response.msg)
            console.log("Error", response)
          }
        } catch (error) {
          alert(error.msg)
          console.log(error);
        }

        console.log(LoginObj);
      }
    })
  </script>
</body>

</html>