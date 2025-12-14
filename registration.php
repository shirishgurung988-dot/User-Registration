<?php
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    } elseif (!preg_match('/[!@#$%^&*]/', $password)) {
        $errors['password'] = 'Password must contain a special character';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // Process registration
    if (empty($errors)) {
        $file = 'users.json';
        
        // Read users
        if (file_exists($file)) {
            $data = file_get_contents($file);
            $users = json_decode($data, true);
        } else {
            $users = [];
        }

        $users[] = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        
        // Save to file
        if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT))) {
            $success = 'Registration successful!';
            $_POST = [];
        } else {
            $errors['system'] = 'Error saving data';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errors['system'])): ?>
            <div class="error-box"><?php echo $errors['system']; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
            
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
            
            <label>Password:</label>
            <input type="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
            
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password">
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="error"><?php echo $errors['confirm_password']; ?></div>
            <?php endif; ?>
            
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>