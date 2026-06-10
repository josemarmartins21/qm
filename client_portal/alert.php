<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    
</head>
<body>
    
<script>
    const notyf = new Notyf({
        duration: 3000,
        position: { x: 'center', y: 'top' },
        dismissible: true
    });

    function alerta(mensagem, tipo = 'success') {
        if (typeof notyf[tipo] === 'function') {
            notyf[tipo](mensagem);
        } else {
            notyf.success(mensagem);
        }
    }
</script>
</body>
</html>