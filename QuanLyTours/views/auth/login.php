<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Travel ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .login-card { 
            width: 100%; max-width: 400px; 
            border: none; border-radius: 10px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;
        }
        .login-header { background: #fff; padding: 30px 20px 10px; text-align: center; }
        .login-body { background: #fff; padding: 20px 30px 40px; }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="login-header">
        <h3 class="fw-bold text-primary">FOURCHICKENS</h3>
        <p class="text-muted small">Hệ thống quản lý du lịch</p>
    </div>
    
    <div class="login-body">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small text-center mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form action="index.php?action=check-login" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">EMAIL</label>
                <input type="email" name="email" class="form-control" placeholder="admin@travel.com" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">MẬT KHẨU</label>
                <input type="password" name="password" class="form-control" placeholder="******" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">ĐĂNG NHẬP</button>
        </form>
    </div>
</div>

</body>
</html>