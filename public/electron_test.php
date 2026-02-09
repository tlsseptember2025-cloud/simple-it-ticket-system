<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Electron Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,.1);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>✅ Electron ↔ PHP Connected</h2>
        <p>This page is served by PHP</p>
        <p><strong><?php echo date('Y-m-d H:i:s'); ?></strong></p>
    </div>
</body>
</html>
