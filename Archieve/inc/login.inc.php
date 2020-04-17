<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="login-container">

                <div class="card-header">
                    <?= blog::$name ?> Admin Login
                </div>
                
                <div class="login-form">

                <div class="alert alert-danger" id="login-error" style="display:none;">
                    
                </div>
                <div class="alert alert-success" id="login-success" style="display:none;">
                    
                </div>

                    <form id="login-form" onsubmit="submitLogin()">
                    <label>Email Address
                        <input type="text" class="form-control" placeholder="*">
                    </label>
                    <label> Password
                        <input type="password" class="form-control" placeholder="*">
                    </label>
                   

                    <button type="submit" id="login--submit" class="btn">Login</button>
                    <!-- <a href="/forgotPassword.php" class="forgot-password">Forgot password</a> -->

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>