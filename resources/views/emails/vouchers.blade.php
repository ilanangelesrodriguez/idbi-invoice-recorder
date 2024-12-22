<!DOCTYPE html>
<html>
<head>
    <title>Comprobantes Subidos</title>
</head>
<body>
    <h1>Estimado {{ $user->name }},</h1>
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    @foreach ($vouchers as $voucher)
    <ul>
        <li>Nombre del Emisor: {{ $voucher->issuer_name }}</li>
        <li>Tipo de Documento del Emisor: {{ $voucher->issuer_document_type }}</li>
        <li>Número de Documento del Emisor: {{ $voucher->issuer_document_number }}</li>
        <li>Nombre del Receptor: {{ $voucher->receiver_name }}</li>
        <li>Tipo de Documento del Receptor: {{ $voucher->receiver_document_type }}</li>
        <li>Número de Documento del Receptor: {{ $voucher->receiver_document_number }}</li>
        <li>Monto Total: {{ $voucher->total_amount }}</li>
    </ul>
    @endforeach
    <p>¡Gracias por usar nuestro servicio!</p>
</body>
</html>
