<?php ob_start(); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2>KAFINZO</h2>
            <p class="text-muted">Sign in to your account</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label">Password</label>
                    <a href="#" class="text-decoration-none small text-primary">Forgot Password?</a>
                </div>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Don't have an account?</span>
            <a href="/register" class="text-decoration-none fw-bold text-primary">Register here</a>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . 'views/layouts/main.php'; 
?>
