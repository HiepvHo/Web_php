<?php
use App\Helpers\SessionHelper;
require_once 'app/views/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['register'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $errors['register']; ?>
                        </div>
                    <?php endif; ?>

                    <form action="/project1/Auth/register" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                   id="username" name="username" value="<?php echo $_POST['username'] ?? ''; ?>">
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['username']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Tên đăng nhập phải có ít nhất 3 ký tự</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['password']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                   id="confirm_password" name="confirm_password">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['confirm_password']; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>"
                                   id="phone" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['phone']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Nhập số điện thoại của bạn (10-11 số)</div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>"
                                      id="address" name="address" rows="3"><?php echo $_POST['address'] ?? ''; ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['address']; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p>Đã có tài khoản? <a href="/project1/Auth/login">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/footer.php'; ?>