# Puesta en marcha de /Comunidad (MySQL del hosting + email en IONOS)

Pasos para dejar funcionando el registro en producción, usando el MySQL que
ya viene incluido en el hosting compartido de IONOS (sin contratar nada
aparte). El sitio sigue siendo estático; solo `register.php` y `config.php`
se ejecutan en servidor (igual que `contact.php`).

## 1. Crear la base de datos MySQL en el panel de IONOS

1. Entra a `my.ionos.com` (o `www.ionos.es/mi-ionos`) con la cuenta que ya
   administra `wearegoldenside.com`.
2. En la pantalla principal, entra al tile **"Hosting"** → busca la sección
   **"Bases de datos"** (a veces aparece como "Bases de datos MySQL").
3. Crea una nueva base de datos MySQL:
   - IONOS genera automáticamente el **host**, **nombre de la base de
     datos** y un **usuario**; tú defines la **contraseña**.
   - Anota los cuatro datos en cuanto se generen.
4. Desde esa misma sección normalmente hay un acceso directo a
   **phpMyAdmin** (o una consola SQL) para administrar la base de datos sin
   necesidad de conectarte por línea de comandos.

## 2. Crear la tabla

Opción A — **phpMyAdmin** (más simple):
1. Entra a phpMyAdmin desde el panel de IONOS, selecciona tu base de datos.
2. Pestaña **SQL** → pega el contenido de `sql/usuarios_registrados.sql` →
   Ejecutar.

Opción B — **línea de comandos**, si tienes el host accesible desde fuera:
```bash
mysql -h <DB_HOST> -P 3306 -u <DB_USER> -p <DB_NAME> < sql/usuarios_registrados.sql
```
(En muchos planes de hosting compartido el MySQL solo acepta conexiones
desde dentro del propio hosting, por eso phpMyAdmin suele ser la vía real.)

## 3. Variables de entorno

Copia `.env.example` a `.env` en el servidor, en el mismo directorio que
`register.php` (raíz del sitio), y rellena con los datos del paso 1:

```
DB_HOST=<host que te dio IONOS, ej. db12345678.hosting-data.io>
DB_PORT=3306
DB_NAME=<nombre de la base de datos>
DB_USER=<usuario>
DB_PASS=<contraseña>
MAIL_FROM=no-reply@wearegoldenside.com
MAIL_FROM_NAME=GoldenSide Web
```

**Nunca subas `.env` al repositorio** (ya está en `.gitignore`). Súbelo solo
por FTP/SFTP directamente al hosting.

`MAIL_FROM` debe ser una dirección creada en IONOS sobre `wearegoldenside.com`
— igual restricción que ya aplica a `contact.php` (si no, los correos de
agradecimiento se rechazan por SPF/DKIM).

No hace falta activar ninguna extensión de PHP: `pdo_mysql` ya viene
habilitada por defecto en el hosting compartido de IONOS.

## 4. Subir archivos

Sube a la raíz del sitio (mismo nivel que `index.html`):

- `comunidad.html`
- `register.php`
- `config.php`
- `templates/email/bienvenida.html` (opcional: plantilla HTML del email; sin
  ella se envía en texto plano)
- `.htaccess` (si ya tienes uno en el servidor, añade solo el bloque de la
  reglas de `/Comunidad`, no lo sobrescribas)
- `.env` (creado en el paso 3, nunca el `.env.example`)

## 5. Verificación

1. Abre `https://wearegoldenside.com/Comunidad` — debe cargar sin `.html` en
   la URL (confirma que `.htaccess` se aplicó).
2. Envía el formulario con un correo de prueba:
   - Alta exitosa → mensaje en pantalla + email de agradecimiento recibido.
   - Mismo correo otra vez → error "ese correo ya está registrado" (422).
   - Verifica la fila nueva en `usuarios_registrados` desde phpMyAdmin.

## 6. Indexación (opcional)

Si quieres que la página aparezca en buscadores, confirma que
`https://wearegoldenside.com/Comunidad` está incluida en `sitemap.xml` (ya
añadida) y que `robots.txt` no la bloquea (no lo hace, permite todo).

## 7. Migración desde /registrate

Si en el servidor ya estaba desplegada la versión anterior (`/registrate`):

1. Sube `comunidad.html` y el `.htaccess` nuevo (si el del servidor tiene
   otras reglas, reemplaza solo el bloque de GoldenSide).
2. Comprueba que `/Comunidad` carga y que `/registrate` redirige (301) a
   `/Comunidad`.
3. Borra `registrate.html` del servidor.
4. Sube el `sitemap.xml` actualizado y, si usas Google Search Console,
   reenvíalo.

`register.php`, `config.php`, `.env` y la tabla `usuarios_registrados` no
cambian: los registros existentes se conservan.

## 8. Plantilla HTML del email de bienvenida

`register.php` usa `templates/email/bienvenida.html` si existe en el servidor.
Si no está, sigue enviando el email en texto plano, así que la plantilla se
puede añadir en cualquier momento sin tocar código.

- Marcadores que se reemplazan: `{{nombre}}` y `{{correo}}`.
- Usa estilos inline (`style="..."`) y tablas para el layout: muchos clientes
  de correo (Gmail, Outlook) ignoran `<style>` y CSS moderno.
- Imágenes con URL absoluta (`https://wearegoldenside.es/assets/...`), nunca
  rutas relativas.
- El texto plano de `register.php` se sigue enviando como respaldo para los
  clientes que no muestran HTML.
