<?php ob_start(); ?>

<div class="auth-wrapper py-5">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2>KAFINZO</h2>
            <p class="text-muted">Create a new account</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile_number" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Already have an account?</span>
            <a href="/login" class="text-decoration-none fw-bold text-primary">Sign in</a>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . 'views/layouts/main.php'; 
?>
