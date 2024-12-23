@component('mail::message')
    # Resumen de Procesamiento de Comprobantes

    A continuación el resumen del procesamiento realizado:

    ## Comprobantes Procesados Exitosamente
    @if (count($successfullyProcessed) > 0)
        @foreach ($successfullyProcessed as $voucher)
            - **ID:** {{ $voucher->id }} - **Serie:** {{ $voucher->serie }} - **Número:** {{ $voucher->numero }} - **Moneda:** {{ $voucher->moneda }}
        @endforeach
    @else
        _No se procesaron comprobantes exitosamente._
    @endif

    ---

    ## Comprobantes con Errores
    @if (count($failed) > 0)
        @foreach ($failed as $voucher)
            - **ID:** {{ $voucher->id }} - **Error:** {{ $voucher->error_message }}
        @endforeach
    @else
        _No hubo errores en el procesamiento._
    @endif

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent
