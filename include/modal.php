<link rel="stylesheet" href="Css/responsive.css" />
<!-- ======================= LOGIN MODAL ======================= -->
<div id="loginModal" class="modal-overlay">
    <div class="modal-content">

        <!-- CLOSE BUTTON -->
        <button class="close-btn" data-close="login">&times;</button>

        <h2>Login <br> Journey<span>Merapi</span></h2>

        <!-- FORM LOGIN -->
        <form method="POST" action="auth/login.php">
            <div class="form-group">
                <label for="loginUsername">Username</label>
                <input type="text" name="username" id="loginUsername" placeholder="Masukkan Username" required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" name="password" id="loginPassword" placeholder="Masukkan Password" required>
                <span class="toggle-password" data-target="loginPassword"></span>
            </div>

            <button type="submit" class="submit-btn">Login</button>

            <div class="form-footer">
                Belum punya akun?
                <a href="#" data-switch="register">Daftar sekarang</a>
            </div>
        </form>

    </div>
</div>


<!-- ======================= REGISTER MODAL ======================= -->
<div id="registerModal" class="modal-overlay">
    <div class="modal-content">

        <!-- CLOSE BUTTON -->
        <button class="close-btn" data-close="register">&times;</button>

        <h2>Registrasi <br> Journey<span>Merapi</span></h2>

        <!-- FORM REGISTER -->
        <form method="POST" action="auth/register.php">
            <div class="form-group">
                <label for="regUsername">Username</label>
                <input type="text" name="username" id="regUsername" placeholder="Buat Username" required>
            </div>

            <div class="form-group">
                <label for="regEmail">Email</label>
                <input type="email" name="email" id="regEmail" placeholder="Masukkan Email" required>
            </div>

            <div class="form-group">
                <label for="regPassword">Password</label>
                <input type="password" name="password" id="regPassword" placeholder="Buat Password" required>
                <span class="toggle-password" data-target="regPassword"></span>
            </div>

            <button type="submit" class="submit-btn">Registrasi</button>

            <div class="form-footer">
                Sudah punya akun?
                <a href="#" data-switch="login">Login sekarang</a>
            </div>
        </form>

    </div>
</div>