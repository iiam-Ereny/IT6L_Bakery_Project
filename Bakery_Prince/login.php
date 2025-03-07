<!DOCTYPE html>
<html>
    <head>
        <title>Prince Dough Bakery</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <div class="container">
            <div class="LoginHeader">
                <h1>Prince Dough Bakery</h1>
                <p>Admin Login</p>
            </div>
            <div class="LoginBody">
                <form onsubmit="return redirectToHomepage(event)">
                    <div class="loginInputsContainer"> 
                        <label for="username">Username</label>
                        <input id="username" placeholder="username" type="text" required />
                    </div>
                    <div class="loginInputsContainer"> 
                        <label for="password">Password</label>
                        <input id="password" placeholder="password" type="password" required />
                    </div>
                    <div class="loginButtonContainer">
                        <button type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function redirectToHomepage(event) {
                event.preventDefault();
                window.location.href = "main.php";
            }
        </script>
    </body>
</html>
