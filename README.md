# IDBI Invoice Recorder Challenge

Esta API es responsable de gestionar comprobantes incluyendo funcionalidades para almacenar información adicional, procesamiento asíncrono, consultas avanzadas, eliminación de comprobantes y soporte para filtros avanzados. A continuación, se detallan los requerimientos desarrollados y los cambios realizados.
La API utiliza JSON Web Token (JWT) para la autenticación.

## **Características Implementadas**

### **1. Almacenamiento de Información Adicional en Comprobantes**
- **Objetivo:** Permitir guardar datos adicionales al registrar comprobantes, tales como:
    - Serie
    - Número
    - Tipo de comprobante
    - Moneda

Además, regularizar los comprobantes existentes extrayendo estos datos del campo `xml_content` para integrarlos en el sistema.
- **Solución Implementada:**
    1. Se añadió lógica en el controlador del registro de comprobantes para aceptar los nuevos campos: `serie`, `numero`, `tipo_comprobante` y `moneda`.
    2. Se creó un **Script de Regularización** para extraer la información del campo `xml_content` de los comprobantes ya existentes en la tabla `vouchers` y actualizar los nuevos campos:
        - Este script utiliza regular expressions para analizar el contenido XML y extrae las etiquetas necesarias correspondientes a la serie, número, tipo de comprobante y moneda, actualizando los registros uno por uno.

    3. La base de datos fue modificada:
        - Se añadieron nuevas columnas a la tabla `vouchers`:
            - `serie`: `string`, nullable
            - `numero`: `string`, nullable
            - `tipo_comprobante`: `string`, nullable
            - `moneda`: `string`, nullable

#### **Código Relacionado:**    
- **Migración:**

```php
  Schema::table('vouchers', function (Blueprint $table) {
      $table->string('serie')->nullable();
      $table->string('numero')->nullable();
      $table->string('tipo_comprobante')->nullable();
      $table->string('moneda')->nullable();
  });
```
- **Script de Regularización:**
```php
  public function regularizeVouchers()
  {
      $vouchers = \App\Models\Voucher::all();

      foreach ($vouchers as $voucher) {
          $xml = $voucher->xml_content;

          $voucher->serie = extractFromXML($xml, 'serie'); // Método para extraer 'serie'
          $voucher->numero = extractFromXML($xml, 'numero'); // Método para extraer 'numero'
          $voucher->tipo_comprobante = extractFromXML($xml, 'tipo'); // Método para extraer 'tipo de comprobante'
          $voucher->moneda = extractFromXML($xml, 'moneda'); // Método para extraer 'moneda'
          $voucher->save();
      }
  }
```
### **2. Procesamiento Asíncrono de Comprobantes**
- **Objetivo:** Modificar el registro de comprobantes para ejecutarlo en un proceso **en segundo plano** mediante colas y envíos de correos electrónicos con un resumen al finalizar el proceso.
- **Solución Implementada:**
    1. Se configuró el procesamiento de comprobantes mediante **Jobs** utilizando `dispatch()`.
    2. En el Job `ProcessVouchersJob`, se realiza:
        - El registro de los comprobantes.
        - El manejo de errores (en caso de fallos).
        - Se genera un listado de registros exitosos y fallidos.

    3. **Actualización del correo electrónico:**
        - Una vez completado el Job, se envía un correo al usuario con un resumen que incluye:
            - Comprobantes procesados exitosamente.
            - Comprobantes que fallaron junto con las razones del fallo.

    4. Configuración de la conexión de colas en el archivo `.env`:
```shell
QUEUE_CONNECTION=database
```
- **Código Relacionado:**
```php
  dispatch(new \App\Jobs\ProcessVouchersJob($user, $comprobantesData));
```
### **3. Consulta de Montos Totales Acumulados por Moneda**
- **Objetivo:** Implementar un endpoint que permita consultar montos totales acumulados (agrupados por moneda) de los comprobantes registrados por el usuario autenticado.
- **Endpoint Implementado:**
    - **Ruta:** `GET /api/vouchers/totals`
    - **Descripción:** Devuelve los totales acumulados por moneda (ej. PEN, USD).

- **Lógica del Controlador:** El controlador calcula la suma de los montos de los comprobantes del usuario autenticado agrupando por la columna `moneda`.
- **Código Relacionado:**
```php
public function getTotalAmountsByCurrency()
  {
      $user = Auth::user();

      $totals = Voucher::selectRaw('moneda, SUM(amount) as total_amount')
          ->where('user_id', $user->id)
          ->groupBy('moneda')
          ->get();

      return response()->json([
          'totals' => $totals->pluck('total_amount', 'moneda')
      ], 200);
  }
```
### **4. Eliminación de Comprobantes por Identificador**
- **Objetivo:** Permitir a los usuarios autenticados eliminar comprobantes específicos utilizando su identificador (`ID`), siempre y cuando les pertenezcan.
- **Endpoint Implementado:**
    - **Ruta:** `DELETE /api/vouchers/{id}`
    - **Descripción:** Elimina un comprobante específico.

- **Restricción:**
  Solo el usuario al que pertenece el comprobante puede eliminarlo.
- **Código Relacionado:**
```php
  public function deleteVoucher($id)
  {
      $user = Auth::user();

      $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();

      if (!$voucher) {
          return response()->json(['message' => 'Comprobante no encontrado o no autorizado para eliminarlo.'], 404);
      }

      $voucher->delete();

      return response()->json(['message' => 'Comprobante eliminado exitosamente.'], 200);
  }
```
### **5. Filtros Avanzados en la Consulta de Comprobantes**
- **Objetivo:** Ampliar el endpoint de listado de comprobantes con filtros avanzados opcionales:
    - Serie
    - Número
    - Tipo de comprobante
    - Moneda
    - **Rango de fechas (obligatorio)**

- **Endpoint Implementado:**
    - **Ruta:** `GET /api/vouchers`
    - **Descripción:** Permite consultar los comprobantes aplicando filtros.

- **Lógica del Controlador:**
```php
  public function listVouchers(Request $request)
  {
      $user = Auth::user();

      $validated = $request->validate([
          'serie' => 'nullable|string',
          'numero' => 'nullable|string',
          'tipo_comprobante' => 'nullable|string',
          'moneda' => 'nullable|string|in:PEN,USD',
          'fecha_inicio' => 'required|date',
          'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
      ]);

      $query = Voucher::where('user_id', $user->id)
          ->whereBetween('created_at', [$validated['fecha_inicio'], $validated['fecha_fin']]);

      if ($validated['serie']) $query->where('serie', $validated['serie']);
      if ($validated['numero']) $query->where('numero', $validated['numero']);
      if ($validated['tipo_comprobante']) $query->where('tipo_comprobante', $validated['tipo_comprobante']);
      if ($validated['moneda']) $query->where('moneda', $validated['moneda']);

      return response()->json(['data' => $query->get()], 200);
  }
```
## **Requerimientos Previos**
### **1. Colas y Procesos Asíncronos**
Asegúrate de configurar correctamente el sistema de colas en el archivo `.env`:
```shell
QUEUE_CONNECTION=database
```
