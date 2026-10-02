# GoldenSide — wearegoldenside.com

Web oficial y press kit de **GoldenSide**, dúo independiente venezolano radicado en Madrid.

---

## 1. El proyecto como negocio

### Quiénes somos

GoldenSide es un dúo independiente venezolano radicado en Madrid que mezcla rock electrónico, indie alternativo e indietrónica con una narrativa emocional sobre identidad, vulnerabilidad y transformación.

- **Aarón Marfil (ASMA)** — voz, composición, producción
- **Carlos Dalton (Carlos Colmenares)** — guitarra, coros, producción

Equipo: dirección creativa de Felipe Trujillo, management en España con Artes Búho (Madrid) y management internacional con WAMM (Caracas).

### Para qué sirve la web

La web es la presencia digital principal del proyecto y su herramienta de captación profesional:

| Objetivo | Cómo lo cubre la web |
|---|---|
| Presentar la propuesta artística | Hero, "The Underground Is Open", bio del dúo |
| Llevar tráfico a plataformas | Spotify embebido, carrusel de canciones, enlaces a Apple Music, YouTube y redes |
| Conseguir conciertos y colaboraciones | Formulario de contacto segmentado (prensa, booking, colaboración, otro) |
| Dar material a prensa y bookers | Press kit en PDF (`GoldenSide-PressKit-2026.pdf`) |
| Visibilidad en buscadores | SEO, Open Graph y datos estructurados (schema.org) |

**Público:** fans, prensa, bookers, festivales, salas, marcas y colaboradores.

### Etapa actual: Underground Era (2026)

Es una etapa más oscura, honesta y humana. La máscara roja simboliza la perfección que se muestra hacia afuera; debajo está el proceso real.

> "No venimos a mostrar que ya llegamos. Venimos a mostrar el camino."

### Highlights

- "Playing For Keeps" en una campaña de **Samsung**
- Finalistas y 3er lugar en **Stay Alive Fest Madrid 2026**
- **Mercado de Motores Madrid 2026**
- Teloneros de la **gira de Tomates Fritos** (Sala Nazca Barcelona y Sala Nau Madrid)
- Más de **600.000 reproducciones** en Spotify
- "Freedom", premiado en **ONED Art / Experimental Film Festival**
- Nominación en los **Premios Pepsi Music**

---

## 2. El proyecto a nivel técnico

Es un sitio **estático**: HTML, CSS y JavaScript sin frameworks, bundler ni paso de build. Lo único que corre en servidor es un script PHP para el formulario de contacto. Se aloja en **IONOS** (hosting compartido).

### Estructura

```
wearegoldenside/
├── index.html                 Página principal (one-page)
├── contact.php                Procesa el formulario de contacto (PHP mail())
├── css/styles.css             Todos los estilos (BEM + custom properties)
├── js/main.js                 Toda la interactividad (vanilla JS)
├── legal/                     Aviso legal, política de privacidad y de cookies
├── assets/
│   ├── images/                Fotos, portadas, logos, favicons, imagen OG
│   ├── fonts/progress_3/      Fuente de marca "Progress"
│   └── gif/                   Vídeo del manifiesto (mp4, webm); sin uso actualmente
├── docs/                      Brief del press kit y textos de la bio
├── GoldenSide-PressKit-2026.pdf
├── robots.txt · sitemap.xml · site.webmanifest
├── CLAUDE.md                  Contexto del proyecto para Claude Code
└── .claude/                   Configuración compartida de Claude Code
```

### Secciones de `index.html`

Header → Hero (`#hero`) → Barra social (`#redes`) → Underground is Open (`#underground-open`) → Shows (`#shows`) → About (`#about`, `#about-2`) → Música (`#musica`) → Contacto (`#contacto`) → Footer → Banner de cookies.

### Funcionalidad de `main.js`

- Cursor personalizado (solo con puntero fino / desktop)
- Header sólido con blur a partir de 60px de scroll
- Menú móvil (se cierra con ESC y bloquea el scroll del body)
- Parallax en `#about` y `#about-2`
- Animaciones de aparición con `IntersectionObserver` (clase `.reveal`)
- Carrusel de canciones
- Envío del formulario con `fetch` y estados de carga, éxito y error
- Consentimiento de cookies en `localStorage` (`gs_cookie_consent`)

### Formulario de contacto

`index.html` envía por POST a `contact.php`, que:

1. Solo acepta `POST`.
2. Descarta bots con un campo trampa oculto (`website`).
3. Valida nombre, email, asunto (`prensa`, `booking`, `colaboracion`, `otro`), mensaje y aceptación de la privacidad.
4. Envía el correo a `MAIL_TO` desde `MAIL_FROM`. `MAIL_FROM` tiene que ser una dirección del propio dominio creada en IONOS; si no, el correo falla por SPF/DKIM.
5. Devuelve JSON (`{ ok: true }` o el error con los campos que fallan).

### SEO

- Idioma `es_ES`, Open Graph y Twitter Cards
- JSON-LD con `MusicGroup`, `Person`, `MusicRecording` (8 canciones) y `WebSite`
- `sitemap.xml` (home y 3 páginas legales) y `robots.txt` abierto a todos los crawlers
- Web App Manifest y favicons en varias resoluciones

### Desarrollo local

No hace falta instalar nada. Desde la raíz del proyecto:

```bash
python3 -m http.server 5173
```

Abre http://localhost:5173. El servidor de Python no ejecuta PHP, así que el formulario de contacto solo funciona en IONOS. En local puedes probarlo con `php -S localhost:5173`.

### Despliegue

Sube los archivos a la raíz del dominio en IONOS (mismo nivel para `index.html` y `contact.php`) y comprueba que PHP está activo en el panel de IONOS.

---

## 3. Convenciones

### Git

| Rama | Uso |
|---|---|
| `main` | Producción |
| `develop` | Integración |
| `feature/<nombre>` | Una rama por funcionalidad, por ejemplo `feature/presskit` |

- Los mensajes de commit empiezan por el nombre de la rama: `feature/presskit: descripción`.
- **Nunca subas secretos.** `.mcp.json`, `.env*` y `.claude/settings.local.json` están en `.gitignore`. Si un servicio necesita una API key, va en tu entorno local, nunca en el repo.

### CSS

- Metodología **BEM**: `.bloque__elemento--modificador`.
- Los colores salen **siempre** de las custom properties de `:root`; nunca se escriben a mano.
- `--blood` (rojo) es el único acento fuerte; `--gold` (dorado) se usa con moderación. No añadir colores fuera de la paleta.
- Sin frameworks CSS (Bootstrap, Tailwind…).
- Las animaciones se hacen en CSS siempre que sea posible.
- Breakpoint principal `768px` (secundarios `480px` y `900px`). Respetar `prefers-reduced-motion`.
- El archivo está dividido en bloques con comentarios `/* ─── SECCIÓN ─── */`; añade los estilos nuevos en el bloque que les corresponde.

### Paleta

| Token | Valor | Uso |
|---|---|---|
| `--noir` | `#0A0A0A` | Fondo principal |
| `--carbon` | `#1A1A1A` | Gris oscuro |
| `--asphalt` | `#2D2D2D` | Gris medio |
| `--concrete` | `#4A4A4A` | Gris claro |
| `--smoke` | `#8A8A8A` | Texto atenuado |
| `--bone` | `#E8E4DF` | Beige claro |
| `--chalk` | `#F5F2ED` | Blanco roto |
| `--blood` | `#C41E1E` | Acento principal |
| `--ember` | `#8B1A1A` | Rojo oscuro |
| `--gold` | `#D4A843` | Acento secundario |

### JavaScript

- Vanilla JS (ES6+) sin dependencias.
- Listeners de scroll y ratón con `{ passive: true }`.
- Los formularios muestran siempre los estados de carga, éxito y error.

### HTML

- HTML5 semántico (`header`, `main`, `nav`, `section`, `article`, `footer`).
- `aria-label` en todos los elementos interactivos.
- `loading="lazy"` en las imágenes que no se ven al cargar.
- Scripts con `defer`.

### Contenido y marca

- Idioma principal: **español**.
- El nombre es siempre **GoldenSide**: junto, con la S mayúscula y sin "we are" en el texto. El dominio sí es `wearegoldenside.com`.
- Tono editorial, oscuro, sofisticado y humano. Evitar frases de coaching, exageraciones, cifras sin fuente y el tono corporativo.
- Al hablar de Stay Alive Fest, decir siempre "finalistas y 3er lugar", nunca solo "3er lugar".
- No comprimir ni redimensionar imágenes sin confirmarlo antes.

---

## 4. Herramientas y lenguajes

| Área | Herramienta |
|---|---|
| Marcado | HTML5 |
| Estilos | CSS3 (custom properties, BEM) |
| Interactividad | JavaScript vanilla (ES6+) |
| Backend | PHP 7.4+ / 8.x (solo `contact.php`) |
| Hosting | IONOS (hosting compartido, PHP `mail()`) |
| Tipografía | Google Fonts (Playfair Display, EB Garamond, IBM Plex Mono, Oswald, Special Elite) y la fuente local "Progress" |
| Música y vídeo | Spotify Embed, YouTube Embed, enlaces a Apple Music |
| Datos estructurados | schema.org (JSON-LD) |
| Control de versiones | Git + GitHub |
| Asistente de desarrollo | Claude Code: contexto en `CLAUDE.md`, configuración compartida en `.claude/` |
| Servidor local | `python3 -m http.server` (configurado en `.claude/launch.json`) |

> **Licencia de la fuente:** la fuente "Progress" incluida es la versión *Personal Use*. Antes de usarla con fines comerciales hay que comprar una licencia comercial.
