<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="register-container">

                <div class="card-header">
                    <?= blog::$name ?> Register
                </div>

                <div class="register-form">

                    <div class="alert alert-success hidden"></div>
                    <div class="alert alert-danger hidden"></div>                    

                    <form id="register-form" onsubmit="submitRegister()">
                    
                        <label>Username
                            <input type="text" class="form-control" placeholder="*" onkeyup="checkUsername()">
                            <div class="valid-feedback">
                                Username is available!
                            </div>
                            <div class="invalid-feedback">
                                Please provide a username
                            </div>
                        </label>
                        <label>Email Address
                            <input type="text" class="form-control" placeholder="*" onkeyup="checkEmail()">
                            <div class="invalid-feedback">
                                Please provide a valid email address
                            </div>
                        </label>
                        <label class="mb-0"> Password
                            <input type="password" name="password" id="password" class="form-control" placeholder="*" onkeyup="checkPassword()">
                        </label>
                        <div class="password-requirements mb-2 small font-italic">
                        <span>Numbers</span>, <span>punctuation</span>, <span>uppercase</span> <span>and</span> <span>lowercase</span>
                        </div>
                        <label> Confirm password
                            <input type="password" class="form-control" placeholder="*">
                            <div class="invalid-feedback">
                                Please confirm your password
                            </div>
                        </label>

                        <button type="submit" id="register--submit" class="btn">Register</button>

                    </form>
                    <div class="text-center">
                        <p class="small">By creating an account, you agree to our <a href="/privacy.php" target="_blank">privacy policy</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>