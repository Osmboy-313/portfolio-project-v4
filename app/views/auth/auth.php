<div class="app app--auth">

    <div class="wrapper auth">

        <!-- Login Form -->

        <div class="form auth login">

            <form id="login-form">

                <div class="title">Login</div>

                <div class="alert de-active">
                    <span class="alert__msg"></span>
                    <i class='bx bx-x alert__close'></i> 
                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input type="email" id="email" placeholder="Enter your Email">
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input id="password" type="password" placeholder="Enter your Password">
                        <i class='bx bxs-lock-alt'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <select name="user-type" id="user-type-select">
                            <option value="" selected disabled>Select an Option</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="boss">Boss</option>
                        </select>
                        <i class='bx bx-chevron-down select-tag-arrow'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="forgot-password">
                    <a href="" class="forgot-pass">Forgot Password?</a>
                </div>

                <div class="auth-btn">
                    <input type="submit" value="Login" name="login">
                </div>

            </form>

        </div>

        <!-- Registration Form -->

        <div class="form auth register">

            <form id="registration-form">

                <div class="title">Register</div>

                <div class="alert de-active">
                    <span class="alert__msg"></span>
                    <i class='bx bx-x alert__close'></i> 
                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input type="text" id="username" placeholder="Enter your Username" name="username">
                        <i class='bx bxs-user'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input type="email" id="email" placeholder="Enter your Email" name="email">
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <select name="user-type" id="user-type-select" name="user_type">
                            <option value="" selected disabled>Select an Option</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="boss">Boss</option>
                        </select>
                        <i class='bx bx-chevron-down select-tag-arrow'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box" id="code-box" hidden>

                    <div class="input-wrapper">
                        <input type="text" id="code" placeholder="Enter Admin Code" name="code">
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input type="password" id="password" placeholder="Enter your Password" name="password">
                        <i class='bx bxs-lock-alt'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="input-box">

                    <div class="input-wrapper">
                        <input type="password" id="confirm-password" placeholder="Confirm Password" name="confirm-password">
                        <i class='bx bxs-lock-alt'></i>
                    </div>
                    <span class="error-box"></span>

                </div>

                <div class="auth-btn">
                    <input type="submit" value="Register" name="register">
                </div>

            </form>

        </div>

        <!-- Toggle Container For Form Switching Animation -->

        <div class="toggle-container">

            <div class="toggle-panel toggle-left">
                <h1>Welcome back !</h1>
                <p>Don't have an Account?</p>
                <button id="register-btn"><span>Register</span></button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Hello, Welcome !</h1>
                <p>Already have an Account ! </p>
                <button id="login-btn"><span>Login</span></button>
            </div>

        </div>
        

    </div>

</div>