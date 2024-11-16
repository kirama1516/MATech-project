<?php

include "./db/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['login'])) {

        $usermail = $_POST['usermail'];

        $password = $_POST['password'];

        if (empty($usermail) || empty($password)) {
            
            header("Location: index.php?error=emptyfields");
            exit();

        } else {

            $sql =  "SELECT * FROM `sign_database` WHERE Username = ? OR Email = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {

                header("Location: index.php?error=sqlerror");
                exit();

            } else {

                mysqli_stmt_bind_param($stmt, "ss", $usermail, $usermail);
                mysqli_stmt_execute($stmt);
                // mysqli_stmt_get_result($stmt);

                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_assoc($result)) {

                    $pwdCheck = password_verify($password, $row['Password']);

                    if ($pwdCheck == false) {

                        header("Location: index.php?error=wrongPassword");
                        exit();

                    } else if ($pwdCheck == true) {

                        $_SESSION['userId'] = $row['Id'];
                        $_SESSION['userName'] = $row['Username'];

                        header("Location: home.php?login=success&username=" . $usermail);
                        exit();

                    } else {

                        header("Location: index.php?error=wrongPassword");
                        exit();

                    }
                } else {

                    header("Location: index.php?error=noUser");
                    exit();

                }
            }
        }

    } else if (isset($_POST['signin'])) {

        $email = $_POST['email'];

        $username = $_POST['username'];

        $image = 'upload/' . $_FILES['image']['name'];

        $password = $_POST['password'];

        $confirmpassword = $_POST['confirmpassword'];

        if (empty($username) || empty($email) || empty($password) || empty($confirmpassword) || empty($image)) {

            header("Location: index.php?error=emptyfields&username=" . $username . "&email=" . $email);
            exit();

        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)) {

            header("Location: index.php?error=invalidEmailUsername");
            exit();

        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            header("Location: index.php?error=invalidEmail&username=" . $username);
            exit();

        } else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {

            header("Location: index.php?error=invalidUsername&email=" . $email);
            exit();

        } else if ($password !== $confirmpassword) {

            header("Location: index.php?error=passwordCheck&username=" . $username . "&email=" . $email);
            exit();

        } else {

            $sql = "SELECT Username FROM `sign_database` WHERE Username = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {

                header("Location: index.php?error=sqlerror");
                exit();

            } else {
                                
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                $resultCheck = mysqli_stmt_num_rows($stmt);

                if ($resultCheck > 0) {

                    header("Location: index.php?error=usertaken&email=" . $email);
                    exit();

                } else {

                    $sql = "INSERT INTO `sign_database` (Email, Username, Password, Image) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $sql)) {

                        $_SESSION['Username'] = $username;
                        header("Location: index.php?error=sqlerror");
                        exit();

                    } else {

                        $hashPwd = password_hash($password, PASSWORD_DEFAULT);

                        mysqli_stmt_bind_param($stmt, "ssss", $email, $username, $hashPwd, $image);
                        mysqli_stmt_execute($stmt);
                        header("Location: index.php?signin=success");
                        exit();

                    }
                }
            }
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

    } else {

        header("Location: index.php?error=InvalidRequest");
        exit();

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>MATech Website</title>
    <link rel="shortcut icon" href="assets/images/logo/MATech-fav.png">
</head>

<body>
    <div class="countainer">
        <h1>
            <b>MATech</b><sup>integrated services</sup>
        </h1>
        <h2>Motto:Safety First</h2>
        <address>
            Opp. BUK New-Campus, Along Gwarzo Road, Kano State.<br>
            Phone No.:080-9990-3452, 080-6892-1997, 070-6426-9353. Email:matech@gmail.com. Google:MATech.online.
        </address>
    </div>

    <div class="glass">
        <div class="form-box login">
            <!-- <a href="#">
                <span class="icon-close"><ion-icon name="close">✖</ion-icon></span>
            </a><br> -->
            <b>LOGIN</b>
            <form action="index.php" method="post">
                <div class="input-box">
                    <span class="icon">
                        <!-- <ion-icon name="username">🧑🏻</ion-icon> -->
                    </span>
                    <input type="text" name="usermail" id="usermail">
                    <label>Username/Email</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password">
                    <label>Password</label>
                </div>
                <br>
                <div class="remember">
                    <label><input type="checkbox">Remember me</label>
                    <a href="forgot-password.php">
                        Forgot Password?
                    </a>
                </div>
                <br>
                <div>
                    <input type="submit" name="login" class="rcorners" value="Login">
                </div>
                <div class="login-register">
                    <p>If you don't have an account?<a href="#" class="register-link">Sign up</a></p>
                </div>
            </form>
        </div>


        <div class="form-box register">
            <b>Register</b>
            <form action="index.php" method="post" enctype="multipart/form-data">
                <div class="input-box">
                    <span class="icon">
                        <!-- <ion-icon name="email">🧑🏻</ion-icon> -->
                    </span>
                    <input type="text" name="email" id="email">
                    <label>email</label>
                </div>
                <div class="input-box">
                    <span class="icon">
                        <!-- <ion-icon name="username" id="username">🧑🏻</ion-icon> -->
                    </span>
                    <input type="text" name="username">
                    <label>username</label>
                </div>
                <div class="input-box">
                    <span class="icon">
                        <!-- <ion-icon name="lock-closed">🔒</ion-icon> -->
                    </span>
                    <input type="password" name="password" id="password">
                    <label>Password</label>
                </div>
                <div class="input-box">
                    <span class="icon">
                        <!-- <ion-icon name="lock-closed">🔒</ion-icon> -->
                    </span>
                    <input type="password" name="confirmpassword" id="confirmpassword">
                    <label>Confirm password</label>
                </div>
                <div class="input-image">
                    <span class="icon">
                        <!-- <ion-icon name="image">🧑🏻</ion-icon> -->
                    </span>
                    <input type="file" name="image" id="imageInput" accept="image/*">
                    <label for="file">Upload image</label>
                </div>
                <br>
                <div class="remember">
                    <label><input type="checkbox">I agree to the terms & conditions</label>
                </div>
                <div>
                    <input type="submit" name="signin" class="rcorners" value="Sign in">
                </div>
                <div class="login-register">
                    <p>Already have an account?<a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/index.js"></script>
</body>

</html>